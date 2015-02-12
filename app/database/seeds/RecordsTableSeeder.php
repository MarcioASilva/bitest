<?php

class RecordsTableSeeder extends Seeder {

	public function run()
	{
      Record::create([
        'date_delivered'                     => \Carbon\Carbon::now()->toDateTimeString(),
        'date_received'                      => \Carbon\Carbon::now()->toDateTimeString(),
        'date_returned'                      => \Carbon\Carbon::now()->toDateTimeString(),
        'file_closed_date'                   => \Carbon\Carbon::now()->toDateTimeString(),
        'total'                              => 0,
        'original_estimate_value'            => 4111.66,
        'received_to_delivered_working_days' => 0.002,
        'received_to_returned_working_days'  => 30.447,
        'received_to_closed_working_days'    => 71.889,
        'dataset_id'                         => 1,
        'xstatus_id'                         => 1,
        'file_status_id'                     => 1,
        'reason_id'                          => 1,
        'peril_id'                           => 1,
        'report_id'                          => 1
      ]);

      Record::create([
        'date_delivered'                     => \Carbon\Carbon::now()->toDateTimeString(),
        'date_received'                      => \Carbon\Carbon::now()->toDateTimeString(),
        'date_returned'                      => \Carbon\Carbon::now()->toDateTimeString(),
        'file_closed_date'                   => \Carbon\Carbon::now()->toDateTimeString(),
        'total'                              => 10,
        'original_estimate_value'            => 4,
        'received_to_delivered_working_days' => 0.003,
        'received_to_returned_working_days'  => 31.447,
        'received_to_closed_working_days'    => 70,
        'dataset_id'                         => 2,
        'xstatus_id'                         => 2,
        'file_status_id'                     => 2,
        'reason_id'                          => 2,
        'peril_id'                           => 2,
        'report_id'                          => 2
      ]);
	}

}