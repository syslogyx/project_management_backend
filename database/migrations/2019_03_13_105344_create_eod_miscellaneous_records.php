<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateEodMiscellaneousRecords extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('eod_miscellaneous_records', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('eod_id')->unsigned();
            $table->foreign('eod_id')->references('id')->on('eod_report');
            $table->string('miscellaneous_time')->nullable();
            $table->longText('miscellaneous_reason')->nullable();
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
        Schema::dropIfExists('eod_miscellaneous_records');
    }
}
