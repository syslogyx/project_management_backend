<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateEODTaskAssocTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('eod_task_assoc', function (Blueprint $table) {
            $table->increments('id');

            $table->integer('task_id')->unsigned();
            $table->foreign('task_id')->references('id')->on('tasks');

            $table->integer('eod_id')->unsigned();
            $table->foreign('eod_id')->references('id')->on('eod_report');

            $table->string('status_id');
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
        Schema::dropIfExists('eod_task_assoc');
    }
}
