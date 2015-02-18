<?php

class ReasonsTableSeeder extends Seeder {

  public function run()
    {
      Reason::create([
        'work_not_proceeding_reason' => 'Canceled',
      ]);

      Reason::create([
        'work_not_proceeding_reason' => 'Returned'
      ]);
  }
}