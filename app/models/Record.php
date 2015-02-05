<?php

class Record extends \Eloquent {

	use SoftDeletingTrait;

	// Add your validation rules here...
	public static $rules = [
		'date_delivered'                     => 'required|date_format:"Y-m-d H:i:s"',
		'date_received'                      => 'required|date_format:"Y-m-d H:i:s"',
		'date_returned'                      => 'required|date_format:"Y-m-d H:i:s"',
		'file_closed_date'                   => 'required|date_format:"Y-m-d H:i:s"',
		'total'                              => 'required|regex:/[\d]*,[\d]{2}/',
		'original_estimate_value'            => 'required|regex:/[\d]*,[\d]{2}/',
		'received_to_delivered_working_days' => 'required|regex:/[\d]*,[\d]{2}/',
		'received_to_returned_working_days'  => 'required|regex:/[\d]*,[\d]{2}/',
		'received_to_closed_working_days'    => 'required|regex:/[\d]*,[\d]{2}/',
		'dataset_id'                         => 'required|integer',
		'xactanalysis_id'                    => 'required|integer',
		'file_status_id'                     => 'required|integer',
		'reason_id'                          => 'required|integer',
		'peril_id'                           => 'required|integer',
		'report_id'                          => 'required|integer',
	];

	// Don't forget to fill this array
	protected $fillable = [];

}