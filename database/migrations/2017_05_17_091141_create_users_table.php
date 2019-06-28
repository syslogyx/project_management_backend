<?php

use App\User;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateUsersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('users', function (Blueprint $table) {
            $table->increments('id');
            $table->string('name');
            $table->string('email')->nullable();
            $table->string('password')->nullable();
            $table->string('gender')->nullable();
            $table->integer('status')->nullable();
            $table->string('email_internal')->nullable();
            $table->string('email_external')->nullable();
            $table->string('department')->nullable();
            $table->string('designation')->nullable();
            $table->string('avatar');
            $table->string('token')->nullable();
            $table->time('expiry')->nullable();
            $table->integer('user_id');
            $table->date('doj')->nullable();
            $table->string('total_experience')->nullable();
            $table->rememberToken();
            $table->timestamps();
        });

        $data = array(
            array(
                "name" => "Super Admin",
                "email" => "super_admin@syslogyx.com",
                "password" => Hash::make('test123'),
                "gender" => "male",
                "status" => 1,
                "user_id" => 0,
                "total_experience" => 1.0,
                "email_internal" => "super_admin@syslogyx.in",
                "email_external" => "super_admin@syslogyx.com",
                "department" => "Admin",
                "designation" => "Administrator",
                "avatar" => "default_profile.png",
            ),
            array(
                "name" => "Admin",
                "email" => "admin@syslogyx.com",
                "password" => Hash::make('test123'),
                "gender" => "male",
                "status" => 1,
                "user_id" => 0,
                "total_experience" => 1.0,
                "email_internal" => "admin@syslogyx.in",
                "email_external" => "admin@syslogyx.com",
                "department" => "Admin",
                "designation" => "Administrator",
                "avatar" => "default_profile.png",
            ),
            array(
                "name" => "Manager",
                "email" => "manager@syslogyx.com",
                "password" => Hash::make('test123'),
                "gender" => "male",
                "status" => 1,
                "user_id" => 0,
                "total_experience" => 1.0,
                "email_internal" => "manager@syslogyx.in",
                "email_external" => "manager@syslogyx.com",
                "department" => "Manager",
                "designation" => "Administrator",
                "avatar" => "default_profile.png",
            ),
            array(
                "name" => "Lead",
                "email" => "lead@syslogyx.com",
                "password" => Hash::make('test123'),
                "gender" => "male",
                "status" => 1,
                "user_id" => 0,
                "total_experience" => 1.0,
                "email_internal" => "lead@syslogyx.in",
                "email_external" => "lead@syslogyx.com",
                "department" => "Lead",
                "designation" => "Administrator",
                "avatar" => "default_profile.png",
            ),
            array(
                "name" => "Employee",
                "email" => "employee@syslogyx.com",
                "password" => Hash::make('test123'),
                "gender" => "male",
                "status" => 1,
                "user_id" => 0,
                "total_experience" => 1.0,
                "email_internal" => "employee@syslogyx.in",
                "email_external" => "employee@syslogyx.com",
                "department" => "Employee",
                "designation" => "Employee",
                "avatar" => "default_profile.png",
            ),

            array(
                "name" => "Intern",
                "email" => "intern@syslogyx.com",
                "password" => Hash::make('test123'),
                "gender" => "male",
                "status" => 1,
                "user_id" => 0,
                "total_experience" => 1.0,
                "email_internal" => "intern@syslogyx.in",
                "email_external" => "intern@syslogyx.com",
                "department" => "Intern",
                "designation" => "Intern",
                "avatar" => "default_profile.png",
            ),
        );

        User::insert($data);

//        Schema::create('users', function (Blueprint $table) {
        //            $table->increments('id');
        //            $table->string('name');
        //            $table->string('email');
        //            $table->string('password');
        //            $table->rememberToken();
        //            $table->timestamps();
        //        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('users');
    }
}
