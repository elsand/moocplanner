<?php

/**
 * Wrapper for PDO constructor. Sets up a UTF-8 enabled connection, enables exceptions on error,
 * and forcefully disables emulated prepares
 *
 * @return PDO
 */
function get_database_connection() {
	static $pdo;
	if ($pdo) return $pdo;
	try {
		$pdo = new PDO(sprintf('mysql:host=%s;dbname=%s;charset=UTF8', DB_HOST, DB_DATABASE), DB_USER, DB_PASSWORD, [
			PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES 'UTF8'"
		]);
		$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
		$pdo->setAttribute(PDO::ATTR_EMULATE_PREPARES, false);
		$pdo->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);

		return $pdo;
	}
	catch (PDOException $e) {
		die("Unable to connect to database: " . $e->getMessage());
	}
}

/**
 * Returns a CourseEnrollment object for the supplied course. Throws exception if current user is not enrolled in supplied course,
 * if user does not exist, or course does not exist
 *
 * @param $course_id
 *
 * @throws RuntimeException
 * @return CourseEnrollment
 */
function get_course_enrollment($course_id) {
	static $cache = [];
	if (!isset($cache[$course_id])) {
		$db = get_database_connection();
		$sth = $db->prepare('SELECT c.id, c.name, uc.start_date FROM course c, user_course uc WHERE c.id = uc.course_id AND c.id = ? AND uc.user_id = ?');
		if (!$sth->execute([$course_id, CURRENT_USER_ID])) {
			throw new RuntimeException('Invalid user-id, course-id or missing enrollment');
		}
		$result = $sth->fetch();
		$c = new CourseEnrollment();
		$c->id = $result['id'];
		$c->name = $result['name'];
		$c->enrolled_date = new DateTime($result['start_date']);
		// Get module count, and how many are completed
		$c->modules_count = $db->query('SELECT COUNT(*) FROM module WHERE course_id = ' . $c->id . ' AND is_exam = 0')->fetchColumn();
		$c->exam_count = $db->query('SELECT COUNT(*) FROM module WHERE course_id = ' . $c->id . ' AND is_exam = 1')->fetchColumn();
		$c->completed_modules_count = $db->query('SELECT COUNT(*) FROM user_module WHERE user_id = ' . CURRENT_USER_ID . ' AND module_id IN (SELECT id FROM module WHERE course_id = ' . $c->id . ') AND completed = 1')->fetchColumn();

		// Populate modules
		$c->modules = get_modules_for_user($c->id);
		$cache[$course_id] = $c;
	}

	return $cache[$course_id];
}

/**
 * Gets a list of modules that are active on the present date, that is uncompleted modules that are scheduled to be worked on
 * this week or any preceeding dates
 *
 * @param $course_id
 *
 * @return Module[]
 */
function get_active_modules($course_id) {
	$course_enrollment = get_course_enrollment($course_id);
	$modules = [];
	foreach ($course_enrollment->modules as $m) {
		if ($m->is_active) {
			$modules[] = $m;
		}
	}
	return $modules;
}

/**
 * Gets an array of Session objects for the supplied course, year and month.
 * The array has the form:
 *
 * [
 *    '2015-11-20' => [ $session1, $session2, ...],
 *    '2015-11-24' => [ $session3 ],
 *    '2016-01-05' => [ $session5 ]
 * ]
 *
 * @param $course_id
 * @param $year
 * @param $month
 *
 * @return array
 */
function get_sessions_for_month($course_id, $year, $month) {
	return [];
}

/**
 * Gets list of all modules that are not fully booked for the supplied course_id
 *
 * @param $course_id
 *
 * @throws RuntimeException
 * @return Module[]
 */
function get_not_fully_booked_modules($course_id) {

	$course_enrollment = get_course_enrollment($course_id);
	$modules = [];
	foreach ($course_enrollment->modules as $m) {
		if ($m->spent_hours + $m->booked_hours < $m->estimated_hours) {
			$modules[] = $m;
		}
	}
	return $modules;
}

function get_modules_for_user($course_id) {
	$db = get_database_connection();
	$sth = $db->prepare('
		SELECT
		m.course_id, m.is_exam, m.module_hours, m.module_order, m.name,
		c.standard_module_hours as course_standard_module_hours,
		uc.standard_module_hours as user_standard_module_hours,
		um.user_id, um.module_id, um.module_hours as user_module_hours, um.completed
		FROM course c, user_course uc, module m
		LEFT JOIN user_module um ON (m.id = um.module_id AND um.user_id = ?)
		WHERE c.id = uc.course_id
		AND c.id = m.course_id
		AND c.id = ?
	');
	if (!$sth->execute([CURRENT_USER_ID, $course_id])) {
		throw new RuntimeException('Invalid course id');
	}

	$modules = [];
	while ($r = $sth->fetch()) {
		$m = new Module();
		$m->id = $r['module_id'];
		$m->name = $r['name'];
		$m->completed = (bool) $r['completed'];
		// 1. Check if user has overridden this particular module
		if ($r['user_module_hours']) {
			$m->is_estimate_overridden = true;
			$m->estimated_hours = $r['user_module_hours'];
		}
		// 2. Check if the user has an override for all modules this course
		else if ($r['user_standard_module_hours']) {
			$m->is_estimate_overridden = true;
			$m->estimated_hours = $r['user_standard_module_hours'];
		}
		// 3. Check if the module itself has a specfied amount of hours
		else if ($r['module_hours']) {
			$m->is_estimate_overridden = false;
			$m->estimated_hours = $r['module_hours'];
		}
		// 4. Lastly, check the standard module hours for the course
		else {
			$m->is_estimate_overridden = false;
			$m->estimated_hours = $r['course_standard_module_hours'];
		}

		$m = get_module_populated_with_sessions($m);

		$modules[] = $m;
	}

	return $modules;
}

/**
 * @param Module $m
 *
 * @throws RuntimeException
 * @return Module;
 */
function get_module_populated_with_sessions(Module $m) {
	$db = get_database_connection();
	$sth = $db->prepare('SELECT * FROM session WHERE user_id = ? AND module_id = ?');
	if (!$sth->execute([CURRENT_USER_ID, $m->id])) {
		throw new RuntimeException('Invalid course id');
	}
	$sessions = [];

	// Used to map repeat_days into something DateInterval can understand. Monday = 1, Tuesday = 2 etc
	$day_names = ['', 'monday', 'tuesday', 'wednesday', 'thursday', 'friday', 'saturday', 'sunday'];

	while ($real_session = $sth->fetch()) {
		$dt = new DateTime($real_session['start_date']);

		$s = new Session();
		$s->id = $real_session['id'];
		$s->date = $dt;
		$s->duration_hours = $real_session['hours'];
		$s->is_repeating = (bool) $real_session['repeating'];
		$s->is_repeated = false;
		// TODO! Resolve conflict/invalid status
		$s->is_conflicted = false;
		$s->is_invalid = false;

		if ($dt < new DateTime()) {
			$m->spent_hours += $s->duration_hours;
		}
		else {
			$m->booked_hours += $s->duration_hours;
		}

		if ($s->is_repeating) {
			$s->repeat_interval_weeks = $real_session['repeating'];
			$s->repeat_days = explode(',', $real_session['repeat_days']);

			$sessions[] = $s;
			$day_of_start_date = date('N', $dt->getTimestamp());
			$week_multiplier = 0;
			$adding_intervals = true;
			do {
				$new_date = clone $s->date;
				// Add a number of weeks. Note that this is 0, so nothing gets added the first week
				$new_date->add(new DateInterval('P' . $week_multiplier * $s->repeat_interval_weeks . 'W'));
				foreach ($s->repeat_days as $day) {
					if ($week_multiplier == 0 && $day <= $day_of_start_date) {
						// only repeat for days past start_date the first week
						continue;
					}
					$repeated_s = clone $s;
					$repeated_s->is_repeating = true;
					$repeated_s->date = $new_date;
					$repeated_s->date->add(DateInterval::createFromDateString('last ' . $day_names[$day]));


					if ($m->estimated_hours > $m->spent_hours + $m->booked_hours + $repeated_s->duration_hours) {
						// We got room for this session in full
						$sessions[] = $repeated_s;
 					}
					else {
						// Not room for a full session. Check how many hours we got left.
						$repeated_s->duration_hours = $m->estimated_hours - $m->spent_hours - $m->booked_hours;
						if ($repeated_s->duration_hours) {
							// Last session with some left over hours
							$sessions[] = $repeated_s;
						}
						// Stop adding intervals, the module is fully booked / spent
						$adding_intervals = false;
					}
					if ($repeated_s->date < new DateTime()) {
						$m->spent_hours += $repeated_s->duration_hours;
					}
					else {
						$m->booked_hours += $repeated_s->duration_hours;
					}
				}
				$week_multiplier++;
			} while ($adding_intervals);

		}
		else {
			$sessions[] = $s;
		}
	}

	$m->sessions = $sessions;
	return $m;
}

function get_user_standard_module_hours($course_id) {
	$db = get_database_connection();
	$sth = $db->prepare('SELECT standard_module_hours FROM user_course WHERE user_id = ? AND course_id = ?');
	if (!$sth->execute([CURRENT_USER_ID, $course_id])) {
		throw new RuntimeException('Invalid course id');
	}
	return $sth->fetchColumn();
}