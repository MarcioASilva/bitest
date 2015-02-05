<?php

class Record extends \Eloquent {

	use SoftDeletingTrait;

	// Add your validation rules here
	public static $rules = [
		'date_delivered'                     => 'date_format:"Y-m-d H:i:s"',
		'date_received'                      => 'date_format:"Y-m-d H:i:s"',
		'date_returned'                      => 'date_format:"Y-m-d H:i:s"',
		'file_closed_date'                   => 'date_format:"Y-m-d H:i:s"',
		'total'                              => 'regex:/[\d]*,[\d]{2}/',
		'original_estimate_value'            => 'regex:/[\d]*,[\d]{2}/',
		'received_to_delivered_working_days' => 'regex:/[\d]*,[\d]{2}/',
		'received_to_returned_working_days'  => 'regex:/[\d]*,[\d]{2}/',
		'received_to_closed_working_days'    => 'regex:/[\d]*,[\d]{2}/',
		'dataset_id'                         => 'required|integer',
		'xstatus_id'   		                 	 => 'required|integer',
		'file_status_id'                     => 'required|integer',
		'reason_id'                          => 'required|integer',
		'peril_id'                           => 'required|integer',
		'report_id'                          => 'required|integer'
	];

	// Don't forget to fill this array
	protected $fillable = [
		'date_delivered',
		'date_received',
		'date_returned',
		'file_closed_date',
		'total',
		'original_estimate_value',
		'received_to_delivered_working_days',
		'received_to_returned_working_days',
		'received_to_closed_working_days',
		'dataset_id',
		'xstatus_id',
		'file_status_id',
		'reason_id',
		'peril_id',
		'report_id'
	];

}