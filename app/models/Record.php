<?php

class Record extends \Eloquent {

	use SoftDeletingTrait;

	// Add your validation rules here
	public static $rules = [
		// 'date_delivered'                     => 'date_format:"Y-m-d H:i:s"',
		// 'date_received'                      => 'date_format:"Y-m-d H:i:s"',
		// 'date_returned'                      => 'date_format:"Y-m-d H:i:s"',
		// 'file_closed_date'                   => 'date_format:"Y-m-d H:i:s"',
		// 'total'                              => 'regex:/[\d]*,[\d]{2}/',
		// 'original_estimate_value'            => 'regex:/[\d]*,[\d]{2}/',
		// 'received_to_delivered_working_days' => 'regex:/[\d]*,[\d]{2}/',
		// 'received_to_returned_working_days'  => 'regex:/[\d]*,[\d]{2}/',
		// 'received_to_closed_working_days'    => 'regex:/[\d]*,[\d]{2}/',
		// 'dataset_id'                         => 'required|integer',
		// 'xstatus_id'   		                 	 => 'required|integer',
		// 'file_id'                            => 'required|integer',
		// 'reason_id'                          => 'required|integer',
		// 'peril_id'                           => 'required|integer',
		// 'report_id'                          => 'required|integer',
		// 'exported_date_id'                   => 'required|integer'
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
		'report_id',
		'exported_date_id'
	];

	public function dataset()
	{
		return $this->belongsTo('Dataset');
	}

	public function file()
	{
		return $this->belongsTo('FileStatus');
	}

	public function peril()
	{
		return $this->belongsTo('Peril');
	}

		public function reason()
	{
		return $this->belongsTo('Reason');
	}

	public function report()
	{
		return $this->belongsTo('Report');
	}

	public function xstatus()
	{
		return $this->belongsTo('Xstatus');
	}

	public function exported_dates()
	{
		return $this->belongsTo('ExportedDate');
	}
}