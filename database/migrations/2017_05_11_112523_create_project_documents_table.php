<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateProjectDocumentsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('project_documents', function (Blueprint $table) {
            $table->increments('id');
            $table->string('project_id')->nullable();
            $table->string('file_name');
            $table->string('path')->nullable();
            $table->string('title')->nullable();
            $table->text('description')->nullable();
            $table->integer('type')->nullable();
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
        Schema::table('project_documents');
    }
}
