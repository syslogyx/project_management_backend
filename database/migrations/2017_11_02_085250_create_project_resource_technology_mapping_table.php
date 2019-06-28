<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateProjectResourceTechnologyMappingTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('project_resource_technology_mapping', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('project_resource_id')->unsigned();
            $table->foreign('project_resource_id')->references('id')->on('project_resources');
            $table->integer('technology_id')->unsigned();
            $table->foreign('technology_id')->references('id')->on('technologies');
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
        Schema::dropIfExists('project_resource_technology_mapping');
    }
}
