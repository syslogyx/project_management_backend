<?php

namespace App;

use Validator;

class Role extends \Zizaco\Entrust\EntrustRole
{

    protected $table = 'roles';
    protected $guarded = ['id', 'created_at', 'updated_at'];
    private $rules = array(
        'name' => 'required | max:190|unique:roles,name,',
        'display_name' => 'required | max:190',
        // 'description' => 'required'
    );
    private $errors;

    public function validate($data)
    {
        if ($this->id) {
            $this->rules['name'] .= $this->id;
        }

        $validator = Validator::make($data, $this->rules);

        if ($validator->fails()) {
            $this->errors = $validator->errors();
            return false;
        }
        return true;
    }

    public function errors()
    {
        return $this->errors;
    }

    public function users()
    {
        return $this->belongsToMany('App\User', 'role_user', 'role_id', 'user_id');
    }

    public function menus()
    {
        return $this->belongsToMany('App\Menu', 'role_menu_permissions', 'role_id', 'menu_id');
    }

}
