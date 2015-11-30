<?php

/**
 * This is the main entry point for the application, inclusion of all code and routing occurs
 */

require "inc/settings.php";
require "inc/utils.php";
require "inc/db.php";
require "inc/tpl.php";
require "inc/datatypes.php";
require "inc/actions.php";

// Router implementation. Requets to ?action=some_handler is handled by the function "action_some_handler"
// If no action is supplied, call action_default(). See actions.php.
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

