<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateXstatusesTable extends Migration {

  /**
   * Run the migrations.
   *
   * @return void
   */
  public function up()
  {
    Schema::create('xstatuses', function(Blueprint $table)
    {
      $table->engine = 'InnoDB';

      $table->increments('id');
      $table->string('xact_analysis');
      $table->timestamps();
      $table->softDeletes();
    });
  }


  /**
   * Reverse the migrations.
   *
   * @return void
   */
  public function down()
  {
    Schema::drop('xstatuses');
  }
}