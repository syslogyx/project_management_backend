<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateProjectResourcesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('project_resources', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('project_id')->unsigned();
            $table->foreign('project_id')->references('id')->on('projects');
            $table->integer('user_id')->unsigned();
            $table->foreign('user_id')->references('id')->on('users');
            $table->integer('domain_id')->unsigned();
            $table->foreign('domain_id')->references('id')->on('categories');
            $table->string('status_id')->nullable();
//            $table->integer('status_id')->unsigned();
            //            $table->foreign('status_id')->references('id')->on('status');
            $table->string('role')->nullable();
            $table->dateTime('start_date')->nullable();
            $table->dateTime('due_date')->nullable();
            $table->integer('created_by');
            $table->integer('updated_by');
            $table->timestamps();
            // $table->unique(['project_id', 'user_id','domain_id']);

        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('project_resources');
    }
}
