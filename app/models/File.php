<?php

class File extends \Eloquent {

	use SoftDeletingTrait;

  // Add your validation rules here
	public static $rules = [
		// 'title' => 'required'
	];

	// Don't forget to fill this array
	protected $fillable = [];

}