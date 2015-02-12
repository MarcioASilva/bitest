<?php

class ReportsTableSeeder extends Seeder {

	public function run()
	{
        Report::create([
        'report_date' => '201502'
      ]);
              Report::create([
        'report_date' => '201503'
      ]);
	}
}