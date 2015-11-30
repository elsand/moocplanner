<?php

/**
 * These are the actions, ie. the URL handlers for the application
 */

/**
 * Default handler. Called on initial load and whenever the view needs to refresh.
 */
function action_default() {
	if (isset($_GET['showmonth']) && preg_match('/^2\d{3}\-[01][0-9]$/', $_GET['showmonth'])) {
		list($year, $month) = explode('-', $_GET['showmonth']);
		if ($month < 1 || $month > 12) {
			$year = date('Y');
			$month = date('m');
		}
	}
	else {
		$year = date('Y');
		$month = date('m');
	}

	$tpl = new tpl('main');
	$tpl->set('year', $year);
	$tpl->set('month', $month);
	$tpl->set('course_data', get_course_enrollment(LOADED_COURSE_ID));
	$tpl->set('active_modules', get_active_modules(LOADED_COURSE_ID));
	$tpl->set('entries', get_sessions_for_month(LOADED_COURSE_ID, $year, $month));
	$tpl->set('not_fully_booked', get_not_fully_booked_modules(LOADED_COURSE_ID));

	echo $tpl->render();

}

/**
 * Called via ajax. Popuplates the popup when the user clicks on a date in the calendar to add a new session.
 *
 * @throws RuntimeException
 */
function action_new_session() {

	if (empty($_GET['date']) || !strtotime($_GET['date'])) {
		throw new RuntimeException('Ugyldig dato');
	}

	$tpl = new tpl('edit_session');
	$tpl->set('session', new Session());
	$tpl->set('modules', get_modules_for_user(LOADED_COURSE_ID));
	$tpl->set('date', new DateTime($_GET['date']));
	echo $tpl->render();
}

/**
 * Called via ajax. Popuplates the popup when the user clicks on a session in the calendar to edit it.
 *
 * @throws RuntimeException
 */
function action_edit_session() {
	if (empty($_GET['session_id']) || !ctype_digit($_GET['session_id']) || !($session = get_session_by_id($_GET['session_id']))) {
		throw new RuntimeException('Ugyldig session id');
	}

	$tpl = new tpl('edit_session');
	$tpl->set('session', $session);
	$tpl->set('modules', get_modules_for_user(LOADED_COURSE_ID));
	$tpl->set('date', $session->date);

	echo $tpl->render();
}

/**
 * Called via ajax. Handles the submit from the new/edit session form. Sends a JSON response via ajax_response().
 */
function action_save_session() {

	// Validate that the module_id exists and that the user is enrolled to it
	$module = null;
	if (empty($_POST['module_id']) || !ctype_digit($_POST['module_id']) || !$module = get_module_by_id($_POST['module_id'])) {
		ajax_response(true, ['Ugyldig module id']);
	}

	if (empty($_POST['date']) || !strtotime($_POST['date'])) {
		ajax_response(true, ['Ugyldig date']);
	}

	// If editing a session, check that the supplied session_id exists and is owned by the user
	if (!empty($_POST['session_id']) && (!ctype_digit($_POST['session_id']) || !get_session_by_id($_POST['session_id']))) {
		ajax_response(true, ['Ugyldig session id']);
	}

	if (!empty($_POST['repeatable']) && (empty($_POST['repeat_interval_weeks']) || !ctype_digit($_POST['repeat_interval_weeks']))) {
		ajax_response(true, ['Ugyldig ukesintervall.']);
	}

	if (empty($_POST['duration_hours']) || !ctype_digit($_POST['duration_hours']) || $_POST['duration_hours'] == 0 || $_POST['duration_hours'] > 24) {
		ajax_response(true, ['Du må oppgi mellom et tall for som lengde på økten, og maks 24 timer i døgnet']);
	}

	if ($_POST['duration_hours'] > $module->estimated_hours - $module->spent_hours - $module->booked_hours) {
		ajax_response(true, ['Modulen har bare ' . ($module->estimated_hours - $module->spent_hours - $module->booked_hours) . ' timer igjen, du kan ikke oppgi mer enn det.']);
	}

	if (!empty($_POST['repeatable']) && (empty($_POST['repeat_days']) || !is_valid_days($_POST['repeat_days']))) {
		ajax_response(true, ['Du må oppgi minst én ukedag du ønsker å gjenta arbeidsøkten på.']);
	}

	try {
		save_session_to_database($_POST);
	}
	catch (RuntimeException $e) {
		ajax_response(true, [ $e->getMessage() ]);
	}

	ajax_response();
}

/**
 * Called via ajax. Used when the user asks to delete a session.
 */
function action_delete_session() {
	if (!empty($_POST['session_id']) && (!ctype_digit($_POST['session_id']) || !get_session_by_id($_POST['session_id']))) {
		ajax_response(true, ['Ugyldig session id']);
	}

	try {
		delete_session_from_database($_POST['session_id']);
	}
	catch (RuntimeException $e) {
		ajax_response(true, [ $e->getMessage() ]);
	}

	ajax_response();
}

/**
 * Called via ajax. Used when the user checks off a module as completed
 */
function action_complete_module() {
	// Check that the supplied module exists and that the user is enrolled in it
	if (empty($_POST['module_id']) || !ctype_digit($_POST['module_id']) || !get_module_by_id($_POST['module_id'])) {
		ajax_response(true, ['Ugyldig module id']);
	}

	try {
		set_completed_flag_on_module($_POST['module_id'], true);
	}
	catch (RuntimeException $e) {
		ajax_response(true, [ $e->getMessage() ]);
	}

	ajax_response();
}

/**
 * Called via ajax. Used when the user re-opens a module after first marking it completed
 */
function action_reopen_module() {
	// Check that the supplied module exists and that the user is enrolled in it
	if (empty($_POST['module_id']) || !ctype_digit($_POST['module_id']) || !get_module_by_id($_POST['module_id'])) {
		ajax_response(true, ['Ugyldig module id']);
	}

	try {
		set_completed_flag_on_module($_POST['module_id'], false);
	}
	catch (RuntimeException $e) {
		ajax_response(true, [ $e->getMessage() ]);
	}

	ajax_response();
}

/**
 * Called via ajax. Used to populate the popup where the user can set estimated work load on modules.
 */
function action_edit_module_settings() {
	$tpl = new tpl('edit_module_settings');
	$tpl->set('modules', get_modules_for_user(LOADED_COURSE_ID));
	$tpl->set('course_standard_module_hours', get_course_standard_module_hours(LOADED_COURSE_ID));
	$tpl->set('user_standard_module_hours', get_user_standard_module_hours(LOADED_COURSE_ID));
	echo $tpl->render();
}

/**
 * Called via ajax. Handler for the submit in the session settings form
 */
function action_save_module_settings() {

	if (isset($_POST['user_standard_module_hours'])) {
		// Set or reset standard module hours for the user?
		if (!ctype_digit($_POST['user_standard_module_hours']) || $_POST['user_standard_module_hours'] == 0) {
			save_standard_module_hours(LOADED_COURSE_ID, null);
		}
		else {
			save_standard_module_hours(LOADED_COURSE_ID, $_POST['user_standard_module_hours']);
		}
	}

	// Iterate supplied modules. Only those changed client side is submitted
	foreach ($_POST as $key => $value) {
		if (preg_match('/^module-(\d+)$/', $key, $m)) {
			$module_id = $m[1];
			// Check that we got a valid module id
			if (!get_module_by_id($module_id)) {
				ajax_response(true, 'Invalid module id');
			}
			// Reset to standard
			if (!ctype_digit($value) || $value == null) {
				save_module_hours($module_id, null);
			}
			else {
				save_module_hours($module_id, $value);
			}
		}
	}

	ajax_response();

}
