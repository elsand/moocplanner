<?php

// Check that we have defined a database connection
if (!file_exists(__DIR__ . '/dbconfig.php')) {
	die("dbconfig.php does not exist! Copy dbconfig.php.default to dbconfig.php");
}
require 'dbconfig.php';

// For formatting of dates
setlocale(LC_TIME, 'nb_NO');
date_default_timezone_set('Europe/Oslo');

// We use UTF-8 on all the things
ini_set('default_charset', 'utf-8');

// This project does not implement user nor course handling, but this can be trivally added
// For now, just hard code the user id and selected course id.
define('CURRENT_USER_ID', 1);
define('LOADED_COURSE_ID', 1);

// Convenience constants for formatting dates
define('DATE_FORMAT_LONG_DATE', '%e. %B %Y');
define('DATE_FORMAT_SHORT_DATE', '%d.%m.%Y');
define('DATE_FORMAT_MONTH', '%B');