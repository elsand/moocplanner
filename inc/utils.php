<?php

function display_error($error_message) {
	$tpl = new tpl('error');
	$tpl->set('error_message', $error_message);
	echo $tpl->render();
}

function is_ajax() {
	return !empty($_SERVER['HTTP_X_REQUESTED_WITH']);
}