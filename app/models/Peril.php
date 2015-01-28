<?php

class Peril extends \Eloquent {

	// Add your validation rules here
	public static $rules = [
		'peril' => 'required'
	];

	// Don't forget to fill this array
	protected $fillable = [
		'peril'
	];

}