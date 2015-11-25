<?php

/**
 * Wrapper for PDO constructor. Sets up a UTF-8 enabled connection, enables exceptions on error,
 * and forcefully disables emulated prepares
 *
 * @return PDO
 */
function get_database_connection() {
	try {
		$pdo = new PDO(sprintf('mysql:host=%s;dbname=%s;charset=UTF8', DB_HOST, DB_DATABASE), DB_USER, DB_PASSWORD, [
			PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES 'UTF8'"
		]);
		$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
		$pdo->setAttribute(PDO::ATTR_EMULATE_PREPARES, false);

		return $pdo;
	}
	catch (PDOException $e) {
		die("Unable to connect to database: " . $e->getMessage());
	}
}

/**
 * Returns a CourseEnrollment object for the supplied course. Throws exception if current user is not enrolled in supplied course.
 *
 * @param $course_id
 *
 * @return CourseEnrollment
 */
function get_course_data($course_id) {
	$c = new CourseEnrollment();
	return $c;
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
	return [];
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
 * @return Module[]
 */
function get_not_fully_booked_modules($course_id) {
	return [];
}