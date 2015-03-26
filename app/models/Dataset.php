<?php

class Dataset extends \Eloquent {

	use SoftDeletingTrait;

	// Add your validation rules here
	public static $rules = [
		'dataset'       => 'required|unique:datasets'
    'group'         => 'required',
    'friendly_name' => 'required',
	];

	// Don't forget to fill this array
	protected $fillable = [
		'dataset',
    'group',
    'friendly_name',
	];

  public function records()
  {
    return $this->hasMany('Record');
  }
}