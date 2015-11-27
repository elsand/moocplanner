<?php

if (!file_exists(__DIR__ . '/dbconfig.php')) {
	die("dbconfig.php does not exist! Copy dbconfig.php.default to dbconfig.php");
}
require 'dbconfig.php';

setlocale(LC_TIME, 'nb_NO');
date_default_timezone_set('Europe/Oslo');

ini_set('default_charset', 'utf-8');

define('CURRENT_USER_ID', 1);
define('LOADED_COURSE_ID', 1);

define('DATE_FORMAT_LONG_DATE', '%e. %B %Y');
define('DATE_FORMAT_SHORT_DATE', '%d.%m.%Y');
define('DATE_FORMAT_MONTH', '%B');