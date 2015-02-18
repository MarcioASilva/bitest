<?php

class ExportedDatesTableSeeder extends Seeder {

  public function run(){
    Dataset::create([
      'exported_date' => \Carbon\Carbon::now()->toDateTimeString(),
  ]);

  Dataset::create([
    'exported_date' => \Carbon\Carbon::now()->toDateTimeString(),
  ]);
  }
}