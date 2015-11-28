<?php

require "inc/settings.php";
require "inc/utils.php";
require "inc/db.php";
require "inc/tpl.php";
require "inc/datatypes.php";

if (!empty($_GET['action'])) {
	$action = 'action_' . $_GET['action'];
	if (function_exists($action)) {
		$action();
	}
	else {
		display_error('Ugyldig handling');
	}
}
else {
	action_default();
}

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


function action_save_session() {

	if (empty($_POST['module_id']) || !ctype_digit($_POST['module_id']) || !get_module_by_id($_POST['module_id'])) {
		ajax_response(true, ['Ugyldig module id']);
	}

	if (empty($_POST['date']) || !strtotime($_POST['date'])) {
		ajax_response(true, ['Ugyldig date']);
	}

	if (!empty($_POST['session_id']) && (!ctype_digit($_POST['session_id']) || !get_session_by_id($_POST['session_id']))) {
		ajax_response(true, ['Ugyldig session id']);
	}
	
	if (!empty($_POST['repeatable']) && (empty($_POST['repeat_interval_weeks']) || !ctype_digit($_POST['repeat_interval_weeks']))) {
		ajax_response(true, ['Ugyldig ukesintervall.']);
	}

	if (empty($_POST['duration_hours']) || !ctype_digit($_POST['duration_hours']) || $_POST['duration_hours'] == 0 || $_POST['duration_hours'] > 24) {
		ajax_response(true, ['Du må oppgi mellom 1 og 24 timers lengde på økten.']);
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

function action_complete_module() {
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

function action_reopen_module() {
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

function action_edit_module_settings() {
	$tpl = new tpl('edit_module_settings');
	$tpl->set('modules', get_modules_for_user(LOADED_COURSE_ID));
	$tpl->set('standard_module_hours', get_user_standard_module_hours(LOADED_COURSE_ID));
	echo $tpl->render();
}

function action_save_module_settings() {

}
/*


GET index.php?action=new_session&date=2015-11-22
GET index.php?action=edit_session&session_id=123

GET index.php?showmonth=2015-11

POST index.php?action=save_session (session_id)
POST index.php?action=save_session (date)
POST index.php?action=complete_module (module_id=1234)

POST index.php?action=toggle_view_completed (ja/nei)

index.php?action=module_settings
POST index.php?action=save_module_settings (kun endrede felter)

*/
