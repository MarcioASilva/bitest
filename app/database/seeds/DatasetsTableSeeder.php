<?php

class DatasetsTableSeeder extends Seeder {

	public function run()
	{
    Dataset::create([
      'dataset' => 'AXA - Imperial WNS'
    ]);

    Dataset::create([
      'dataset' => 'AXA - GAB WNS'
    ]);
	}

}