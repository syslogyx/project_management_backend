<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Validator;

class MenuType extends Model
{
    protected $table = 'menu_type';
    protected $guarded = ['id', 'created_at', 'updated_at'];
    private $rules = array(
        'type' => 'required',
    );
    private $errors;

    public function validate($data)
    {
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

    public function menu()
    {
        return $this->hasMany('App\Menu', 'type_id');
    }
}
