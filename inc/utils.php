<?php

function display_error($error_message) {
	$tpl = new tpl('error');
	$tpl->set('error_message', $error_message);
	echo $tpl->render();
}

function is_ajax() {
	return !empty($_SERVER['HTTP_X_REQUESTED_WITH']);
}

function fdate($format, DateTime $datetime = null) {
	if (!$datetime) $datetime = new DateTime();
	return strftime($format, $datetime->getTimestamp());
}

function month_num_to_name($num) {
	return ucfirst(strftime(DATE_FORMAT_MONTH, mktime(0, 0, 0, $num, 10)));
}

function h($str) {
	return htmlspecialchars($str, ENT_QUOTES, 'UTF-8');
}