<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Validator;

class EODTaskComment extends Model
{
    protected $table = 'eod_task_comment';
    protected $guarded = ['id', 'created_at', 'updated_at'];

    private $rules = array(
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

    public function task()
    {
        return $this->belongsTo('App\Task', 'task_id');
    }
}
