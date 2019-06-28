<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Menu extends Model
{
    protected $table = 'menus';
    protected $guarded = ['id', 'created_at', 'updated_at'];
    public function roles()
    {
        return $this->belongsToMany('App\Role', 'role_menu_permissions', 'role_id', 'menu_id');
    }

}
