<?php

class ExportedDatesTableSeeder extends Seeder {

  public function run() {
    ExportedDate::create([
      'exported_date' => \Carbon\Carbon::now()->toDateTimeString()
    ]);

    ExportedDate::create([
      'exported_date' => \Carbon\Carbon::now()->toDateTimeString()
    ]);
  }
}
