<?php


class WivesTableSeeder extends Seeder {

	public function run()
	{
		Wife::create([
			'name'       => 'Jane',
			'husband_id' => 1
		]);

		Wife::create([
			'name'       => 'Tania',
			'husband_id' => 2
		]);
	}

}