<?php

class XstatusesTableSeeder extends Seeder {

	public function run()
	{
		
        Xstatus::create([
          'xact_analysis' => 'In Assign Queue'
        ]);

        Xstatus::create([
          'xact_analysis' => 'Delivered'
        ]);
	}

}