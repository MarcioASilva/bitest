<?php

class FileStatus extends \Eloquent {

	use SoftDeletingTrait;

  // Add your validation rules here
	public static $rules = [
    'file_status' => 'required|unique:file_statuses'
	];

	// Don't forget to fill this array
	protected $fillable = [
    'file_status'
  ];

  public function records()
  {
    return $this->hasMany('Record');
  }
}