<?php

class StatusesTableSeeder extends Seeder {

	public function run()
	{
		
			Status::create([
        Dataset::create([,
          'xact_analysis' => 'In Assign Queue'
        ]);

        Dataset::create([,
          'xact_analysis' => 'Delivered'
        ]);
			]);
	}

}