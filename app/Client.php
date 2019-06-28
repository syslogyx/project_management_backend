<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Validator;

class Client extends Model
{

    protected $table = 'clients';
    protected $guarded = ['id', 'created_at', 'updated_at'];
    private $rules = array(
        'name' => 'required | max:190',
//        'project_id' => 'required|numeric',
        'pincode' => 'regex:/\b\d{6}\b/|integer',
        'mobile' => 'required',
//        'tel_number' => 'required',
        'email' => 'required|email|unique:clients,email,',
    );
    private $errors;

    public function validate($data)
    {
        if ($this->id) {
            $this->rules['email'] .= $this->id;
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

    public function project()
    {
        return $this->hasMany('App\Project', 'client_id');
    }

}
