<?php

class Reason extends \Eloquent {

	use SoftDeletingTrait;

  // Add your validation rules here
	public static $rules = [
		'work_not_proceeding_Reason' => 'required'
	];

	// Don't forget to fill this array
	protected $fillable = [
		'work_not_proceeding_Reason'
	];

}