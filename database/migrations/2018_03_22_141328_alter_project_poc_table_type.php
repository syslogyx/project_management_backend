<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\Schema;

class AlterProjectPocTableType extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('project_poc', function ($table) {
            $table->string('mobile_primary')->change()->unsigned();
            $table->string('mobile_secondary')->change()->unsigned();
        });

        Schema::table('clients', function ($table) {
            $table->string('mobile')->change()->unsigned();
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
