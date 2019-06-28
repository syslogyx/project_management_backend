<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateMomProjectTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('mom_project', function (Blueprint $table) {
//            $table->increments('id');
            $table->integer('mom_id')->unsigned();
            $table->foreign('mom_id')->references('id')->on('mom')
                ->onUpdate('cascade')->onDelete('cascade');
            $table->integer('project_id')->unsigned();
            $table->foreign('project_id')->references('id')->on('projects')
                ->onUpdate('cascade')->onDelete('cascade');
            $table->string('name')->nullable();
//            $table->integer('created_by');
            //            $table->integer('updated_by');
            //            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('mom_project');
    }
}
