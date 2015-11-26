<?php

class Module {
	/** @var int Database ID */
	public $id;

	/** @var  string Name of modules */
	public $name;

	/** @var boolean If the module is completed */
	public $completed;

	/** @var int How many hours is this module estimated to take */
	public $estimated_hours;

	/** @var bool If the estimate has been set by the user on this particular module */
	public $is_estimate_overridden;

	/** @var int How many hours are spent working on this module */
	public $spent_hours;

	/** @var int How many hours are booked on this module (cannot exceed $estimated_hours) */
	public $booked_hours;

	/** @var  bool Whether there are previously booked hours on this module and completed is false*/
	public $is_active;

	/** @var Session[] The booked / spent sessions on this module */
	public $sessions;
}