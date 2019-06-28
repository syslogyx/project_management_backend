<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Validator;

class Mom extends Model
{

    protected $table = 'mom';
    protected $guarded = ['id', 'created_at', 'updated_at'];
    private $rules = array(
        'title' => 'required | max:190',
        'description' => 'required',
        // 'status' => 'required',
        'meeting_venue' => 'required',
        'date' => 'required',
        'start_time' => 'required',
        // 'user_id' => 'required',
        'end_time' => 'required',
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

    public function project()
    {
        return $this->belongsToMany('App\Project', 'mom_project', 'mom_id', 'project_id');
    }

    public function user()
    {
        return $this->belongsTo('App\User');
    }

    public function momAttendees()
    {
        return $this->belongsToMany('App\User', 'mom_attendees', 'mom_id', 'user_id');
    }

    public function momTask()
    {
        return $this->hasMany('App\MomTasks')->with('user')->orderBy('name', 'asc');
    }

    public function momClients()
    {
        return $this->hasMany('App\MomClient');
    }
}
