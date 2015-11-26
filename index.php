<?php

require "inc/settings.php";
require "inc/utils.php";
require "inc/db.php";
require "inc/tpl.php";
require "inc/datatypes.php";

$db = get_database_connection();

if (!empty($_GET['action'])) {
	$action = 'action_' . $_GET['action'];
	if (function_exists($action)) {
		$action();
	}
	else {
		display_error('Invalid action specificed');
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
	$tpl->set('course_data', get_course_data(LOADED_COURSE_ID));
	$tpl->set('active_modules', get_active_modules(LOADED_COURSE_ID));
	$tpl->set('entries', get_sessions_for_month(LOADED_COURSE_ID, $year, $month));
	$tpl->set('not_fully_booked', get_not_fully_booked_modules(LOADED_COURSE_ID));

	echo $tpl->render();

}

function action_new_session() {

}

function action_edit_session() {

}

function action_save_session() {

}

function action_complete_module() {

}

function action_toggle_view_completed() {

}

function action_module_settings() {

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
