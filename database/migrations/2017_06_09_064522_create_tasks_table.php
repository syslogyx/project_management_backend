<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTasksTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('tasks', function (Blueprint $table) {
            $table->increments('id');
            // $table->integer('project_resource_id')->unsigned();
            // $table->foreign('project_resource_id')->references('id')->on('project_resources');
            $table->string('title')->nullable();
            $table->text('description')->nullable();
            $table->integer('parent_id')->nullable();

            $table->integer('milestone_id')->unsigned()->nullable();
            $table->foreign('milestone_id')->references('id')->on('milestones');
            $table->string('status_id');

            $table->integer('reported_by')->unsigned()->nullable();
            $table->foreign('reported_by')->references('id')->on('project_resources');

            $table->integer('assigned_to')->unsigned();
            $table->foreign('assigned_to')->references('id')->on('project_resources');

            $table->integer('domain_id')->unsigned()->nullable();
            $table->foreign('domain_id')->references('id')->on('domains');

//            $table->integer('status_id')->unsigned();
            //            $table->foreign('status_id')->references('id')->on('status');
            $table->integer('technical_support')->unsigned();
            // $table->foreign('technical_support_id')->references('id')->on('technical_supports');
            $table->dateTime('start_date')->nullable();
            $table->dateTime('stop_date')->nullable();
            $table->dateTime('completion_date')->nullable();

            $table->string('spent_time')->nullable();
            $table->string('break_time')->nullable();
            $table->string('estimated_time')->nullable();

            $table->text('comment')->nullable();
            $table->text('reason')->nullable();
            // $table->integer('original_task_id');
            $table->integer('task_list_id')->nullable();
            $table->integer('priority_id')->nullable();
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
        Schema::dropIfExists('tasks');
    }
}
