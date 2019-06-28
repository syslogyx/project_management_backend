<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateClientsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('clients', function (Blueprint $table) {
            $table->increments('id');
            // $table->integer('project_id')->unsigned();
            // $table->foreign('project_id')->references('id')->on('projects');
            $table->string('name');
            $table->text('address')->nullable();
            $table->string('city')->nullable();
            $table->integer('pincode')->nullable();
            $table->string('state')->nullable();
            $table->string('country')->nullable();
            $table->bigInteger('mobile')->nullable();
            $table->string('tel_number')->nullable();
            $table->string('email')->nullable();
            $table->string('business')->nullable();
            $table->string('type')->nullable();
            $table->integer('created_by');
            $table->integer('updated_by');
            $table->timestampsTz();});
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('clients');
    }
}
