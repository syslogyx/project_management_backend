<?php

use Illuminate\Database\Seeder;


use App\Role;

class RolesTableSeeder extends Seeder
{
    public function run()
    {
       
        $owner = new Role();
        $owner->name = "owner";
        $owner->display_name = "project owner";
        $owner->description = "test";
        $owner->save();
        
        $owner = new Role();
        $owner->name = "admin";
        $owner->display_name = "project admin";
        $owner->description = "test";
        $owner->save();
        
    }
}
