<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class EODTaskAssoc extends Model
{
    protected $table = 'eod_task_assoc';
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

    public function eod()
    {
        return $this->belongsTo('App\EODReport', 'eod_id');
    }

    public function task()
    {
        return $this->belongsTo('App\Task', 'task_id');
    }

}
