<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Validator;

class MomClient extends Model
{

    protected $table = 'mom_client';
    protected $guarded = ['id', 'created_at', 'updated_at'];
    private $rules = array(
        '*.mom_id' => 'required|numeric',
        '*.name' => 'required | max:190',
    );
    private $messages = array(
        '*.mom_id.required' => 'A MoM id is required',
        '*.name.required' => 'A client name is required',
    );
    private $errors;

    public function validate($data)
    {
        $validator = Validator::make($data, $this->rules, $this->messages);
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

}
