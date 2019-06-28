<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Validator;

class MomProject extends Model
{
    protected $table = 'mom_project';
    protected $guarded = ['id', 'created_at', 'updated_at'];
    private $rules = array(
        '*.mom_id' => 'required|numeric',
        '*.project_id' => 'required|numeric',
    );
    private $messages = array(
        '*.mom_id.required' => 'A MoM id is required',
        '*.project_id.required' => 'A project id is required',
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
