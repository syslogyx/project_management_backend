<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\Schema;

class AlterTaskTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('tasks', function ($table) {
            // $table->dropColumn('original_task_id');
            // $table->integer('reported_by')->unsigned();
            // $table->foreign('reported_by')->references('id')->on('project_resources');
            // $table->dateTime('spent_time')->nullable();
            // $table->renameColumn('project_resource_id', 'assigned_to');
            // $table->renameColumn('estimated_hours', 'estimated_time');
            //            $table->dropForeign('tasks_status_id_foreign');
            //
            //            $table->string('status_id')->change()->unsigned();
            //            $table->foreign('status_id')->references('name')->on('status');

        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('tasks');
    }
}
