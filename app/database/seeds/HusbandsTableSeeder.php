<?php


class HusbandsTableSeeder extends Seeder {

	public function run()
	{
		Husband::create([
			'name' => 'Smith'
		]);

		Husband::create([
			'name' => 'Silva'
		]);
	}

}