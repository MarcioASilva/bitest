<?php

class DatabaseSeeder extends Seeder {

	/**
	 * Run the database seeds.
	 *
	 * @return void
	 */
	public function run()
	{
		Eloquent::unguard();

    $this->call('DatasetsTableSeeder');
    $this->call('FileStatusesTableSeeder');
    $this->call('PerilsTableSeeder');
    $this->call('ReasonsTableSeeder');
    $this->call('ReportsTableSeeder');
    $this->call('XstatusesTableSeeder');
    $this->call('RecordsTableSeeder');
    $this->call('ExportedDatesTableSeeder');
	}

}
