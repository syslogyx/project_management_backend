<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\Schema;

class AlterUsersTechnologyMappingTable extends Migration
{

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('user_technology_mapping', function ($table) {
            $table->integer('domain_id')->unsigned();
            $table->foreign('domain_id')->references('id')->on('categories');
            $table->string('duration')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        //
    }

}
