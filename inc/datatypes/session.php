<?php

class Session {
	/** @var int Database ID */
	public $id;

	/** @var bool If this is an repeated entry (eg. not the "original") */
	public $is_repeated;

	/** @var Module What module this session refers to*/
	public $module;

	/** @var DateTime When this session is to take place */
	public $date;

	/** @var int How many hours is this session going to take */
	public $duration_hours;

	/** @var bool Is this a repeatable session? */
	public $is_repeating;

	/** @var int Repeat every X weeks */
	public $repeat_interval_weeks;

	/** @var array Contains day numbers to repeat this session */
	public $repeat_days;

	/** @var bool If this session is in a state of conflict (too many sessions per day) */
	public $is_conflicted;

	/** @var bool If this session is invalid, eg. because the module is already completed */
	public $is_invalid;
}
