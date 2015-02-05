<?php

class Report extends \Eloquent {

	use SoftDeletingTrait;

	// Add your validation rules here
	public static $rules = [
		'report_date' => 'required'
	];

	// Don't forget to fill this array
	protected $fillable = [
		'report_date'
	];

  public function records()
  {
    return $this->hasMany('Record');
  }
}