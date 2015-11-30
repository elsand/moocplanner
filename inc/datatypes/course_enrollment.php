<?php

/** Datatype for the users course enrollment. Contains array of all modules for the user. */

class CourseEnrollment {
	/** @var int Database ID */
	public $id;

	/** @var string Name of course */
	public $name;

	/** @var int Number of modules in course */
	public $modules_count;

	/** @var int Number of exams in course */
	public $exam_count;

	/** @var int How many of the modules are completed by the user */
	public $completed_modules_count;

	/** @var DateTime When did the user enroll on this course */
	public $enrolled_date;

	/** @var Module[] List of modules in this enrollement */
	public $modules;
}