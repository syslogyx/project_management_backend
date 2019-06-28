<?php

use App\MenuType;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateMenuType extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('menu_type', function (Blueprint $table) {
            $table->increments('id');
            $table->string('type');
            $table->timestamps();
        });
        $data = array(
            array(
                'type' => 'Project',
            ),
            array(
                'type' => 'Dashboard',
            ),
            array(
                'type' => 'Resource',
            ),
            array(
                'type' => 'Dashboard Content',
            ),
        );

        MenuType::insert($data);
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('menu_type');
    }
}
