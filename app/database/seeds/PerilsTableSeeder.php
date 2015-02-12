<?php

class PerilsTableSeeder extends Seeder {

  public function run()
  {
    Peril::create([
      'peril' => 'Impact'
    ]);

    Peril::create([
      'peril' => 'Explosion'
    ]);
  }

}