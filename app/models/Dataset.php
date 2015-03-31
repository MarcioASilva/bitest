<?php

class Dataset extends \Eloquent {

	use SoftDeletingTrait;

	// Add your validation rules here
	public static $rules = [
		'dataset'         => 'required|unique:datasets',
    'slide2Friendly'  => 'required',
    'slide2Sequence'  => 'required|integer',
	];

	// Don't forget to fill this array
	protected $fillable = [
		'dataset',
    'slide2Friendly',
    'slide2Sequence',
	];

  public function records()
  {
    return $this->hasMany('Record');
  }
}