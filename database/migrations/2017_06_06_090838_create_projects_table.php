<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateProjectsTable extends Migration
{

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('projects', function (Blueprint $table) {
            $table->increments('id');
            // $table->integer('domain_id')->unsigned();
            // $table->foreign('domain_id')->references('id')->on('domains');
            $table->integer('user_id')->unsigned();
            $table->foreign('user_id')->references('id')->on('users');

            $table->integer('lead_id')->unsigned();
            // $table->foreign('lead_id')->references('id')->on('users');
            $table->string('status_id')->nullable();
            // $table->integer('status_id')->unsigned();
            // $table->foreign('status_id')->references('id')->on('status');
            $table->integer('client_id')->unsigned()->nullable();
            $table->foreign('client_id')->references('id')->on('clients');
            $table->string('name');
            $table->dateTime('start_date')->nullable();
            $table->dateTime('due_date')->nullable();
            $table->dateTime('revised_date')->nullable();
            $table->float('duration_in_days', 5, 2)->nullable();
            $table->text('description')->nullable();
            $table->integer('current_milestone_index')->nullable();
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
        Schema::dropIfExists('projects');
    }

}
