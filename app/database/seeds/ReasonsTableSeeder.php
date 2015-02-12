<?php

class ReasonsTableSeeder extends Seeder {

  public function run()
    {
      Reason::create([
        'work_not_proceeding_reason' => 'Cash/Other Settlement'
      ]);

      Reason::create([
        'work_not_proceeding_reason' => 'Abandoned'
      ]);
  }
}