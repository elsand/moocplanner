<?php
if (isset($_GET['debug'])) {
	var_dump($course_data);
	exit;
}
/**/
?>
<?php require "header.php" ?>
<?php require "calendar.php" ?>
<?php require "footer.php" ?>