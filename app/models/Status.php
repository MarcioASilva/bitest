<?php

class Status extends \Eloquent {

	// Add your validation rules here
	public static $rules = [
		'xact_analysis' => 'required'
	];

	// Don't forget to fill this array
	protected $fillable = [
		'xact_analysis'
	];

}