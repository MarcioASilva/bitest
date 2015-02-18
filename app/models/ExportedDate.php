<?php

class ExportedDate extends \Eloquent {

  use SoftDeletingTrait;
	
  // Add your validation rules here
	public static $rules = [
    'exported_date' => 'required'
	];

	// Don't forget to fill this array
  protected $fillable = [
    'exported_date'
  ];

  public function records()
  {
    return $this->hasMany('Record');
  }
}