<?php

class Wife extends \Eloquent {

	// Add your validation rules here
	public static $rules = [
		'name'       => 'required',
		'husband_id' => 'required|integer'
	];

	// Don't forget to fill this array
	protected $fillable = [
		'name',
		'husband_id'
	];

	public function husband()
	{
		return $this->belongsTo('Husband');
	}

}