<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\Schema;

class AddTotalSpentTime extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('eod_task_assoc', function ($table) {
            $table->string('total_spent_time')->nullable();
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
            $table->dropColumn('total_spent_time');
        });
    }
}
