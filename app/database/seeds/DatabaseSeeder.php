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
    $this->call('FilesTableSeeder');
    $this->call('PerilsTableSeeder');
    $this->call('ReasonsTableSeeder');
    $this->call('ReportsTableSeeder');
    $this->call('StatusesTableSeeder');
    $this->call('RecordsTableSeeder');
	}

}
