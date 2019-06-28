<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateMilestonesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('milestones', function (Blueprint $table) {
            $table->increments('id');
            $table->string('title')->nullable();
            $table->integer('project_id')->unsigned();
            $table->foreign('project_id')->references('id')->on('projects');
            $table->string('status_id')->nullable();
            $table->text('comment')->nullable();
//            $table->integer('status_id')->unsigned();
            //            $table->foreign('status_id')->references('id')->on('status');
            $table->integer('milestone_index')->nullable();
            $table->dateTime('due_date')->nullable();
            $table->dateTime('start_date')->nullable();
            $table->dateTime('revised_date')->nullable();
            $table->text('description')->nullable();
            $table->bigInteger('delay')->nullable();
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
        Schema::dropIfExists('milestones');
    }
}
