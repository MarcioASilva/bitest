<?php

class ReasonsTableSeeder extends Seeder {

  public function run()
    {
    Reason::create([
      Dataset::create([,
        'work_not_proceeding_reason' => 'Cash/Other Settlement'
      ]);

      Dataset::create([,
        'work_not_proceeding_reason' => 'Abandoned'
      ]);
    ]);
  }
}