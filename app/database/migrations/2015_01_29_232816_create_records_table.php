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
			$table->increments('id');
			$table->dateTime('date_delivered');
			$table->dateTime('date_received');
			$table->dateTime('date_returned');
			$table->dateTime('file_closed_date');
			$table->decimal('total');
			$table->decimal('original_estimate_value');
			$table->decimal('received_to_delivered_working_days');
			$table->decimal('received_to_returned_working_days');
			$table->decimal('received_to_closed_working_days');
			$table->integer('dataset_id');
			$table->integer('xactanalysis_id');
			$table->integer('file_status_id');
			$table->integer('reason_id');
			$table->integer('peril_id');
			$table->integer('report_id');
			$table->timestamps();
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
