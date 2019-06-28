<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Validator;

class ProjectActivityStatusLog extends Model
{

    protected $table = 'project_activity_status_logs';
    protected $guarded = ['id', 'created_at', 'updated_at'];

    private $rules = array(
        'activity_id' => 'required|numeric',
        'activity_type_id' => 'required|alpha',
        'status_id' => 'required',
        'project_resource_id' => 'required|numeric',
        'spent_hour' => 'required|numeric',
        'start_date' => 'nullable|date',
        'due_date' => 'nullable|date',
        'revised_date' => 'nullable|date',
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

    public function activityType()
    {
        return $this->belongsTo('App\ActivityType');
    }

    public function projectResource()
    {
        return $this->belongsTo('App\ProjectResource');
    }

    public function status()
    {
        return $this->belongsTo('App\Status');
    }

}
