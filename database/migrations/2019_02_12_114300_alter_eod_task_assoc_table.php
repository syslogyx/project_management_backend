<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AlterEodTaskAssocTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('eod_task_assoc', function (Blueprint $table) {
            $table->string('todays_total_spent_time')->nullable();
            $table->integer('lead_id')->unsigned()->nullable();
            $table->foreign('lead_id')->references('id')->on('users');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('eod_task_assoc', function (Blueprint $table) {
            //
        });
    }
}
