<?php

use App\Category;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCategoriesTable extends Migration
{

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('categories', function (Blueprint $table) {
            $table->increments('id');
            $table->string('name');
            $table->string('alias')->nullable();
            $table->integer('created_by');
            $table->integer('updated_by');
            $table->timestampsTz();
        });
        $data = array(
            'name' => 'Manager',
            'alias' => 'Manager',
            'created_by' => 1,
            'updated_by' => 1,
        );
        Category::create($data);
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('categories');
    }

}
