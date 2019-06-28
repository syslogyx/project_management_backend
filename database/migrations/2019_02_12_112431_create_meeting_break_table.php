<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateMeetingBreakTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('meeting_break_logs', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('hrms_meeting_break_id')->nullable();

            $table->integer('eod_id')->unsigned();
            $table->foreign('eod_id')->references('id')->on('eod_report');

            $table->string('activity_type')->nullable();
            $table->string('start_time')->nullable();
            $table->string('end_time')->nullable();
            $table->string('total_duration')->nullable();
            $table->longText('reason')->nullable();
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
        Schema::dropIfExists('meeting_break_logs');
    }
}
