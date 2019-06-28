<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class RoleMenuPermissions extends Model
{
    protected $table = 'role_menu_permission';
    protected $guarded = ['id', 'created_at', 'updated_at'];

}
