<?php

class Husband extends \Eloquent {

	// Add your validation rules here
	public static $rules = [
		'name' => 'required'
	];

	// Don't forget to fill this array
	protected $fillable = [
		'name'
	];

	public function wife()
	{
		return $this->hasOne('Wife');
	}

}