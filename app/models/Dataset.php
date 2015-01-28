<?php

class Dataset extends \Eloquent {

	use SoftDeletingTrait;

	// Add your validation rules here
	public static $rules = [
		'dataset' => 'required'
	];

	// Don't forget to fill this array
	protected $fillable = [
		'dataset'
	];

}