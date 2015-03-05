<?php

class ExportedDatesTableSeeder extends Seeder {

  public function run() {
    ExportedDate::create([
      'exported_date' => '2015-02-02'//Y-m-d H:i:s
    ]);

    ExportedDate::create([
      'exported_date' => \Carbon\Carbon::now()->toDateTimeString()
    ]);
  }
}
