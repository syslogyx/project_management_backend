<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Validator;

class TaskCommentLog extends Model
{

    protected $table = 'task_comment_logs';
    protected $guarded = ['id', 'created_at', 'updated_at'];

    private $rules = array(
        'task_id' => 'required|numeric',
        'comment' => 'required|alpha',
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
        return $this->belongsTo('App\Task');
    }
}
