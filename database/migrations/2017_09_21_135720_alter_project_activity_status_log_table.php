<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\Schema;

class AlterProjectActivityStatusLogTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('project_activity_status_logs', function ($table) {
//            $table->dropForeign('project_activity_status_logs_status_id_foreign');
            //
            //            $table->string('status_id')->change()->unsigned();
            //            $table->foreign('status_id')->references('name')->on('status');

        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('project_activity_status_logs');
    }
}
