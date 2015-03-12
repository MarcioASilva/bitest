<?php

class Dataset extends \Eloquent {

	use SoftDeletingTrait;

	// Add your validation rules here
	public static $rules = [
		'dataset' => 'required|unique:datasets'
	];

	// Don't forget to fill this array
	protected $fillable = [
		'dataset'
	];

  public function records()
  {
    return $this->hasMany('Record');
  }
}