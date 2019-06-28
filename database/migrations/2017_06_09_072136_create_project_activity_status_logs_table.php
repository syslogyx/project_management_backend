<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateProjectActivityStatusLogsTable extends Migration
{

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('project_activity_status_logs', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('activity_id')->nullable();
            $table->integer('activity_type_id')->nullable();
//            $table->foreign('activity_type_id')->references('id')->on('activity_types');
            //            $table->integer('status_id')->unsigned();
            //            $table->foreign('status_id')->references('id')->on('status');
            $table->string('status_id')->nullable();
//            $table->integer('project_resource_id')->nullable();
            //            $table->foreign('project_resource_id')->references('id')->on('project_resources');
            $table->float('spent_hour', 5, 2)->nullable();
            $table->dateTime('start_date')->nullable();
            $table->dateTime('due_date')->nullable();
            $table->dateTime('revised_date')->nullable();
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
        Schema::dropIfExists('project_activity_status_logs');
    }

}
