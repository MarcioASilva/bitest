<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateRecordsTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('records', function(Blueprint $table)
		{
			$table->engine = 'InnoDB';

			$table->increments('id');
			$table->dateTime('date_delivered')->nullable();
			$table->dateTime('date_received');
			$table->dateTime('date_returned')->nullable();
			$table->dateTime('file_closed_date')->nullable();
			$table->decimal('total');
			$table->decimal('original_estimate_value');
			$table->decimal('received_to_delivered_working_days');
			$table->decimal('received_to_returned_working_days');
			$table->decimal('received_to_closed_working_days');
			$table->integer('dataset_id')->unsigned();
			$table->foreign('dataset_id')->references('id')->on('datasets')->onDelete('cascade');
			$table->integer('xstatus_id')->unsigned();
			$table->foreign('xstatus_id')->references('id')->on('xstatuses')->onDelete('cascade');
			$table->integer('file_status_id')->unsigned();
			$table->foreign('file_status_id')->references('id')->on('file_statuses')->onDelete('cascade');
			$table->integer('reason_id')->unsigned()->nullable();
			// $table->foreign('reason_id')->references('id')->on('reasons')->onDelete('cascade');
			$table->integer('peril_id')->unsigned();
			$table->foreign('peril_id')->references('id')->on('perils')->onDelete('cascade');
			$table->integer('report_id')->unsigned();
			$table->foreign('report_id')->references('id')->on('reports')->onDelete('cascade');
			$table->integer('exported_date_id')->unsigned();
			$table->foreign('exported_date_id')->references('id')->on('exported_dates')->onDelete('cascade');
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
		Schema::drop('records');
	}

}