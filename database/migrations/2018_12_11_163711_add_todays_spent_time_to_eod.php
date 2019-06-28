<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\Schema;

class AddTodaysSpentTimeToEod extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('eod_task_assoc', function ($table) {
            $table->string('todays_spent_time')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('eod_task_assoc', function ($table) {
            $table->dropColumn('todays_spent_time');
        });
    }
}
