<?php

class Record extends \Eloquent {

	// Add your validation rules here
	public static $rules = [
		'date_delivered' => 'required'

		'date_received' => 'required'
		
		'date_returned' => 'required'

		'file_closed_date' => 'required'

		'total' => 'required'

		'original_estimate_value' => 'required'

		'received_to_delivered_working_days' => 'required'

		'received_to_returned_working_days' => 'required'

		'received_to_closed_working_days' => 'required'

		'dataset_id' => 'required' => 'unsigned()' => references('id')->on('dataset')->onDelete('cascade');

		'xactanalysis_id' => 'required' => 'unsigned()'  => references('id')->on('xactanalysis')->onDelete('cascade');

		'file_status_id' => 'required' => 'unsigned()'

		'reason_id' => 'required' => 'unsigned()'

		'peril_id' => 'required' => 'unsigned()'

		'report_id' => 'required' => 'unsigned()'
	];

	// Don't forget to fill this array
	protected $fillable = [];

}