<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Validator;

class TechnicalSupport extends Model
{

    protected $table = 'technical_supports';
    protected $guarded = ['id', 'created_at', 'updated_at'];

    private $rules = array(
        'user_id' => 'required|numeric',
        'task_id' => 'required|numeric',
        'description' => 'required|alpha',
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

    public function user()
    {
        return $this->belongsTo('App\User');
    }

    public function task()
    {
        return $this->hasMany('App\Task', 'technical_support_id');
    }

}
