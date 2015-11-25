<?php

if (!file_exists(__DIR__ . '/dbconfig.php')) {
	die("dbconfig.php does not exist! Copy dbconfig.php.default to dbconfig.php");
}

require 'dbconfig.php';


define('CURRENT_USER_ID', 1);
define('LOADED_COURSE_ID', 1);