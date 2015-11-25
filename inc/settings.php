<?php

if (!file_exists(__DIR__ . '/dbconfig.php')) {
	die("dbconfig.php does not exist! Copy dbconfig.php.default to dbconfig.php");
}

require 'dbconfig.php';