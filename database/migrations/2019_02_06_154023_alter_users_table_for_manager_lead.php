<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AlterUsersTableForManagerLead extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('users', function (Blueprint $table) {
            $table->integer('manager_id')->unsigned()->nullable();
            $table->foreign('manager_id')->references('id')->on('users');

            $table->integer('mentor_id')->unsigned()->nullable();
            $table->foreign('mentor_id')->references('id')->on('users');

            $table->integer('hrms_role_id')->nullable();
            $table->integer('is_updated');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('users', function (Blueprint $table) {
            //
        });
    }
}
