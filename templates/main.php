<?php

if (!empty($_GET['debug'])) {
	var_dump($course_data);
}

// Main template, adding the calendar with header and footer

?>
<?php require "header.php" ?>
<?php require "calendar.php" ?>
<?php require "footer.php" ?>