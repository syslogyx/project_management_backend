<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Validator;

class Status extends Model
{

    protected $table = 'status';
    protected $guarded = ['id', 'created_at', 'updated_at'];
    private $rules = array(
        'activity_type_id' => 'required|alpha',
        'name' => 'required | max:190|alpha|unique:status,name,',
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

    public function project()
    {
        return $this->hasMany('App\Project', 'status_id');
    }

    public function task()
    {
        return $this->hasMany('App\Task', 'status_id');
    }

    public function milestone()
    {
        return $this->hasMany('App\Milestone', 'status_id');
    }

    public function projectResource()
    {
        return $this->hasMany('App\ProjectResource', 'status_id');
    }

    public function activityType()
    {
        return $this->belongsTo('App\ActivityType');
    }

    public function projectActivityStatusLog()
    {
        return $this->hasMany('App\ProjectActivityStatusLog', 'status_id');
    }
}
