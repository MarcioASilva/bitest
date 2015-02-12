<?php

class FileStatusesTableSeeder extends Seeder {

	public function run()
	{
    FileStatus::create([
      'file_status' => 'Open'
    ]);

    FileStatus::create([
      'file_status' => 'Close'
    ]);
	}
}