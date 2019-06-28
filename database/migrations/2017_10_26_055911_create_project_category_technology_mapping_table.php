<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateProjectCategoryTechnologyMappingTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('project_category_technology_mapping', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('project_category_id')->unsigned();
            $table->foreign('project_category_id')->references('id')->on('project_category_mapping');
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
        Schema::dropIfExists('project_category_technology_mapping');
    }
}
