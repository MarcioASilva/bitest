<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateWivesTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('wives', function(Blueprint $table)
		{
			$table->increments('id');
			$table->string('name');
			$table->integer('husband_id')->unsigned();
			$table->foreign('husband_id')->references('id')->on('husbands')->onDelete('cascade');
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
		Schema::drop('wives');
	}

}
