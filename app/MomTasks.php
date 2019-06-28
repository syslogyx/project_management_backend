<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Validator;

class MomTasks extends Model
{

    protected $table = 'mom_task_table';
    protected $guarded = ['id', 'created_at', 'updated_at'];
    private $rules = array(
        '*.mom_id' => 'required|numeric',
        '*.name' => 'required | max:190',
        '*.description' => 'required',
        '*.type' => 'required',
        // '*.status' => 'required',
        '*.start_date' => 'required',
        '*.due_date' => 'required',
    );
    private $rules1 = array(
        'mom_id' => 'required|numeric',
        'name' => 'required | max:190',
        'description' => 'required',
        'type' => 'required',
        // 'status' => 'required',
        'start_date' => 'required',
        'due_date' => 'required',
    );
    private $errors;
    private $messages = array(
        '*.mom_id.required' => 'A MoM id is required',
        '*.name.required' => 'A task name is required',
        '*.description.required' => 'A task description is required',
        '*.type.required' => 'A task type is required',
        '*.status.required' => 'A task status is required',
        '*.start_date.required' => 'A task start date is required',
        '*.due_date.required' => 'A task due date is required',
    );

    public function validate($data, $text)
    {
        if ($text == "Tasks") {
            $validator = Validator::make($data, $this->rules1, $this->messages);
        } else if ($text == "MOM") {
            $validator = Validator::make($data, $this->rules, $this->messages);
        }

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

}
