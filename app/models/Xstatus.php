<?php

class Xstatus extends \Eloquent {

  use SoftDeletingTrait;

  protected $table = 'xstatuses';

  // Add your validation rules here
  public static $rules = [
    'xact_analysis' => 'required'
  ];

  // Don't forget to fill this array
  protected $fillable = [
    'xact_analysis'
  ];

  public function records()
  {
    return $this->hasMany('Record');
  }
}