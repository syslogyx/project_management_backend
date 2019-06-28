<?php

use App\RoleMenuPermissions;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateRoleMenuPermission extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('role_menu_permission', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('role_id')->unsigned();
            $table->foreign('role_id')->references('id')->on('roles')
                ->onUpdate('cascade')->onDelete('cascade');
            $table->integer('menu_id')->unsigned();
            $table->foreign('menu_id')->references('id')->on('menus')
                ->onUpdate('cascade')->onDelete('cascade');
            $table->timestamps();
        });
        $data = array(
            array(
                'role_id' => 1,
                'menu_id' => 1,
            ),
            array(
                'role_id' => 1,
                'menu_id' => 2,
            ),
            array(
                'role_id' => 1,
                'menu_id' => 3,
            ),
            array(
                'role_id' => 1,
                'menu_id' => 4,
            ),
            array(
                'role_id' => 1,
                'menu_id' => 5,
            ),
            array(
                'role_id' => 1,
                'menu_id' => 6,
            ),
            array(
                'role_id' => 1,
                'menu_id' => 7,
            ),
            array(
                'role_id' => 1,
                'menu_id' => 8,
            ),
            array(
                'role_id' => 1,
                'menu_id' => 9,
            ),
            array(
                'role_id' => 1,
                'menu_id' => 10,
            ),
            array(
                'role_id' => 1,
                'menu_id' => 11,
            ),
            array(
                'role_id' => 1,
                'menu_id' => 12,
            ),
            array(
                'role_id' => 1,
                'menu_id' => 13,
            ),
            array(
                'role_id' => 1,
                'menu_id' => 14,
            ),
            array(
                'role_id' => 1,
                'menu_id' => 15,
            ),
            array(
                'role_id' => 1,
                'menu_id' => 16,
            ),
            array(
                'role_id' => 1,
                'menu_id' => 17,
            ),
            array(
                'role_id' => 1,
                'menu_id' => 18,
            ),
            array(
                'role_id' => 1,
                'menu_id' => 19,
            ),
            array(
                'role_id' => 1,
                'menu_id' => 20,
            ),
            array(
                'role_id' => 1,
                'menu_id' => 21,
            ),
            array(
                'role_id' => 1,
                'menu_id' => 22,
            ),
            array(
                'role_id' => 1,
                'menu_id' => 23,
            ),
            array(
                'role_id' => 1,
                'menu_id' => 24,
            ),
            array(
                'role_id' => 1,
                'menu_id' => 25,
            ),
            array(
                'role_id' => 1,
                'menu_id' => 26,
            ),
            array(
                'role_id' => 1,
                'menu_id' => 27,
            ),
            array(
                'role_id' => 1,
                'menu_id' => 28,
            ),
            array(
                'role_id' => 1,
                'menu_id' => 29,
            ),

            array(
                'role_id' => 2,
                'menu_id' => 1,
            ),
            array(
                'role_id' => 2,
                'menu_id' => 2,
            ),
            array(
                'role_id' => 2,
                'menu_id' => 3,
            ),
            array(
                'role_id' => 2,
                'menu_id' => 4,
            ),
            array(
                'role_id' => 2,
                'menu_id' => 5,
            ),
            array(
                'role_id' => 2,
                'menu_id' => 6,
            ),
            array(
                'role_id' => 2,
                'menu_id' => 7,
            ),
            array(
                'role_id' => 2,
                'menu_id' => 8,
            ),
            array(
                'role_id' => 2,
                'menu_id' => 9,
            ),
            array(
                'role_id' => 2,
                'menu_id' => 10,
            ),
            array(
                'role_id' => 2,
                'menu_id' => 11,
            ),
            array(
                'role_id' => 2,
                'menu_id' => 12,
            ),
            array(
                'role_id' => 2,
                'menu_id' => 13,
            ),
            array(
                'role_id' => 2,
                'menu_id' => 14,
            ),
            array(
                'role_id' => 2,
                'menu_id' => 15,
            ),
            array(
                'role_id' => 2,
                'menu_id' => 16,
            ),
            array(
                'role_id' => 2,
                'menu_id' => 17,
            ),
            array(
                'role_id' => 2,
                'menu_id' => 18,
            ),
            array(
                'role_id' => 2,
                'menu_id' => 19,
            ),
            array(
                'role_id' => 2,
                'menu_id' => 20,
            ),
            array(
                'role_id' => 2,
                'menu_id' => 21,
            ),
            array(
                'role_id' => 2,
                'menu_id' => 22,
            ),
            array(
                'role_id' => 2,
                'menu_id' => 23,
            ),
            array(
                'role_id' => 2,
                'menu_id' => 24,
            ),
            array(
                'role_id' => 2,
                'menu_id' => 25,
            ),
            array(
                'role_id' => 2,
                'menu_id' => 26,
            ),
            array(
                'role_id' => 2,
                'menu_id' => 27,
            ),
            array(
                'role_id' => 2,
                'menu_id' => 28,
            ),
            array(
                'role_id' => 2,
                'menu_id' => 29,
            ),

            array(
                'role_id' => 3,
                'menu_id' => 1,
            ),
            array(
                'role_id' => 3,
                'menu_id' => 2,
            ),
            array(
                'role_id' => 3,
                'menu_id' => 3,
            ),
            array(
                'role_id' => 3,
                'menu_id' => 4,
            ),
            array(
                'role_id' => 3,
                'menu_id' => 5,
            ),
            array(
                'role_id' => 3,
                'menu_id' => 6,
            ),
            array(
                'role_id' => 3,
                'menu_id' => 7,
            ),
            array(
                'role_id' => 3,
                'menu_id' => 8,
            ),
            array(
                'role_id' => 3,
                'menu_id' => 9,
            ),
            array(
                'role_id' => 3,
                'menu_id' => 10,
            ),
            array(
                'role_id' => 3,
                'menu_id' => 11,
            ),
            array(
                'role_id' => 3,
                'menu_id' => 12,
            ),
            array(
                'role_id' => 3,
                'menu_id' => 13,
            ),
            array(
                'role_id' => 3,
                'menu_id' => 14,
            ),
            array(
                'role_id' => 3,
                'menu_id' => 15,
            ),
            array(
                'role_id' => 3,
                'menu_id' => 16,
            ),
            array(
                'role_id' => 3,
                'menu_id' => 17,
            ),
            array(
                'role_id' => 3,
                'menu_id' => 18,
            ),
            array(
                'role_id' => 3,
                'menu_id' => 19,
            ),
            array(
                'role_id' => 3,
                'menu_id' => 20,
            ),
            array(
                'role_id' => 3,
                'menu_id' => 21,
            ),
            array(
                'role_id' => 3,
                'menu_id' => 22,
            ),
            array(
                'role_id' => 3,
                'menu_id' => 23,
            ),
            array(
                'role_id' => 3,
                'menu_id' => 24,
            ),
            array(
                'role_id' => 3,
                'menu_id' => 25,
            ),
            array(
                'role_id' => 3,
                'menu_id' => 26,
            ),
            array(
                'role_id' => 3,
                'menu_id' => 27,
            ),
            array(
                'role_id' => 3,
                'menu_id' => 28,
            ),

            array(
                'role_id' => 3,
                'menu_id' => 29,
            ),

            array(
                'role_id' => 4,
                'menu_id' => 1,
            ),
            array(
                'role_id' => 4,
                'menu_id' => 2,
            ),
            array(
                'role_id' => 4,
                'menu_id' => 3,
            ),
            array(
                'role_id' => 4,
                'menu_id' => 4,
            ),
            array(
                'role_id' => 4,
                'menu_id' => 5,
            ),
            array(
                'role_id' => 4,
                'menu_id' => 6,
            ),
            array(
                'role_id' => 4,
                'menu_id' => 7,
            ),
            array(
                'role_id' => 4,
                'menu_id' => 8,
            ),
            array(
                'role_id' => 4,
                'menu_id' => 9,
            ),
            array(
                'role_id' => 4,
                'menu_id' => 10,
            ),
            array(
                'role_id' => 4,
                'menu_id' => 11,
            ),
            array(
                'role_id' => 4,
                'menu_id' => 12,
            ),
            array(
                'role_id' => 4,
                'menu_id' => 13,
            ),
            array(
                'role_id' => 4,
                'menu_id' => 14,
            ),
            array(
                'role_id' => 4,
                'menu_id' => 15,
            ),
            array(
                'role_id' => 4,
                'menu_id' => 16,
            ),
            array(
                'role_id' => 4,
                'menu_id' => 17,
            ),
            array(
                'role_id' => 4,
                'menu_id' => 18,
            ),
            array(
                'role_id' => 4,
                'menu_id' => 19,
            ),
            array(
                'role_id' => 4,
                'menu_id' => 20,
            ),
            array(
                'role_id' => 4,
                'menu_id' => 21,
            ),
            array(
                'role_id' => 4,
                'menu_id' => 22,
            ),
            array(
                'role_id' => 4,
                'menu_id' => 23,
            ),
            array(
                'role_id' => 4,
                'menu_id' => 24,
            ),
            array(
                'role_id' => 4,
                'menu_id' => 25,
            ),
            array(
                'role_id' => 4,
                'menu_id' => 26,
            ),
            array(
                'role_id' => 4,
                'menu_id' => 27,
            ),
            array(
                'role_id' => 4,
                'menu_id' => 28,
            ),
            array(
                'role_id' => 4,
                'menu_id' => 29,
            ),

            array(
                'role_id' => 5,
                'menu_id' => 1,
            ),
            array(
                'role_id' => 5,
                'menu_id' => 2,
            ),
            array(
                'role_id' => 5,
                'menu_id' => 3,
            ),
            array(
                'role_id' => 5,
                'menu_id' => 4,
            ),
            array(
                'role_id' => 5,
                'menu_id' => 5,
            ),
            array(
                'role_id' => 5,
                'menu_id' => 6,
            ),
            array(
                'role_id' => 5,
                'menu_id' => 7,
            ),
            array(
                'role_id' => 5,
                'menu_id' => 8,
            ),
            array(
                'role_id' => 5,
                'menu_id' => 9,
            ),
            array(
                'role_id' => 5,
                'menu_id' => 10,
            ),
            array(
                'role_id' => 5,
                'menu_id' => 11,
            ),
            array(
                'role_id' => 5,
                'menu_id' => 12,
            ),
            array(
                'role_id' => 5,
                'menu_id' => 13,
            ),
            array(
                'role_id' => 5,
                'menu_id' => 14,
            ),
            array(
                'role_id' => 5,
                'menu_id' => 15,
            ),
            array(
                'role_id' => 5,
                'menu_id' => 16,
            ),
            array(
                'role_id' => 5,
                'menu_id' => 17,
            ),
            array(
                'role_id' => 5,
                'menu_id' => 18,
            ),
            array(
                'role_id' => 5,
                'menu_id' => 19,
            ),
            array(
                'role_id' => 5,
                'menu_id' => 20,
            ),
            array(
                'role_id' => 5,
                'menu_id' => 21,
            ),
            array(
                'role_id' => 5,
                'menu_id' => 22,
            ),
            array(
                'role_id' => 5,
                'menu_id' => 23,
            ),
            array(
                'role_id' => 5,
                'menu_id' => 24,
            ),
            array(
                'role_id' => 5,
                'menu_id' => 25,
            ),
            array(
                'role_id' => 5,
                'menu_id' => 26,
            ),
            array(
                'role_id' => 5,
                'menu_id' => 27,
            ),
            array(
                'role_id' => 5,
                'menu_id' => 28,
            ),
            array(
                'role_id' => 5,
                'menu_id' => 29,
            ),

        );

        RoleMenuPermissions::insert($data);
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('role_menu_permission');
    }
}
