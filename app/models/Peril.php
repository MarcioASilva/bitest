<?php

class Peril extends \Eloquent {

	use SoftDeletingTrait;

  // Add your validation rules here
	public static $rules = [
		'peril' => 'required|unique:perils'
	];

	// Don't forget to fill this array
	protected $fillable = [
		'peril'
	];

  public function records()
  {
    return $this->hasMany('Record');
  }
}