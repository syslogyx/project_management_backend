<?php

namespace App;

use App\BaseModel;
use Config;
use Validator;

class Task extends BaseModel
{

    protected $table = 'tasks';
    protected $guarded = ['id', 'created_at', 'updated_at'];
    private $rules = array(
        // 'project_resource_id' => 'required|numeric',
        // 'milestone_id' => 'required|numeric',
        // 'status_id' => 'required',
        // 'technical_support_id' => 'required|numeric',
        // 'completion_date' => 'nullable|date',
        'title' => 'max:190',
        // 'description' => 'required',
        // 'assigned_to' => 'nullable|numeric',
        // 'estimated_hours' => 'required',
        // 'comment' => 'required',
        // 'original_task_id' => 'required|numeric',
        // 'start_date' => 'nullable|date',
        // 'priority_id' => 'required'
    );
    private $messages = array(
        'title.max' => 'The title should not be greater than 190 characters.',
    );
    private $errors;
    public $create_status_log = false;

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

    public function projectResource()
    {
        return $this->belongsTo('App\ProjectResource', 'assigned_to');
    }

    public function status()
    {
        return $this->belongsTo('App\Status');
    }

    public function technicalSupport()
    {
        return $this->hasMany('App\TechnicalSupport');
    }

    public function milestones()
    {
        return $this->belongsTo('App\Milestone', 'milestone_id');
    }

    public function taskCommentLog()
    {
        return $this->hasMany('App\TaskCommentLog', 'task_id');
    }

    public function taskComment()
    {
        return $this->hasMany('App\EODTaskComment', 'task_id');
    }

    // Suvrat Issue#3179
    public function eod_assoc()
    {
        return $this->hasMany('App\EODTaskAssoc', 'task_id');
    }
    /////////////////////
    protected static function boot()
    {

        Task::created(function ($model) {

            //Kalyani : Add activity log
            $data = Task::create_activity_log($model, Config::get('constants.FEED_CONSTANTS.TASK'), Config::get('constants.FEED_CONSTANTS_MSGS.TASK_CREATE'));

            app('App\Http\Controllers\FeedController')->create($data);

            return;
        });
        Task::saved(function ($model) {
            if ($model->create_status_log) {
                //Kalyani : Add activity log
                $data = Task::create_activity_log($model, Config::get('constants.FEED_CONSTANTS.TASK'), Config::get('constants.FEED_CONSTANTS_MSGS.TASK_UPDATE') . $model["status_id"]);

                app('App\Http\Controllers\FeedController')->create($data);
            }
        });

    }

}
