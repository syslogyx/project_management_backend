<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateMomTasksTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('mom_task_table', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('mom_id')->unsigned();;
            $table->foreign('mom_id')->references('id')->on('mom');
            $table->integer('user_id')->unsigned();;
            $table->foreign('user_id')->references('id')->on('users');
            $table->string('name')->nullable();
            $table->text('description')->nullable();
            $table->string('type')->nullable();
            $table->string('status')->nullable();
            $table->dateTime('start_date')->nullable();
            $table->dateTime('due_date')->nullable();
            $table->integer('created_by');
            $table->integer('updated_by');
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
        Schema::dropIfExists('mom_task_table');
    }
}
