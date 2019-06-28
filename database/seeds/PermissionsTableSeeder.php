<?php

use Illuminate\Database\Seeder;


use App\Permission;

class PermissionsTableSeeder extends Seeder
{
    public function run()
    {
       
        $createInvoice = new Permission();
        $createInvoice->name = "technology.create";
        $createInvoice->display_name = "create technologies";
        $createInvoice->description = "";
        $createInvoice->save();
        
        $owner = new Permission();
        $owner->name = "technology.update";
        $owner->display_name = "update technologies";
        $owner->description = "";
        $owner->save();
        
    }
}
