<?php

namespace App;

use App\BaseModel;
use Config;
use Validator;

class Milestone extends BaseModel
{

    protected $table = 'milestones';
    protected $guarded = ['id', 'created_at', 'updated_at'];
    private $rules = array(
        'title' => 'required | max:190 ',
        'project_id' => 'required|numeric',
        'status_id' => 'required',
        'milestone_index' => 'required|numeric',
        'due_date' => 'nullable|date',
        'start_date' => 'nullable|date',
        'revised_date' => 'nullable|date',
    );
    private $messages = array(
        'title.max' => 'The title should not be greater than 190 characters.',
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

    public function task()
    {
        return $this->hasMany('App\Task', 'milestone_id')->with('projectResource', "projectResource.user");
    }

    public function project()
    {
        return $this->belongsTo('App\Project');
    }

    public function status()
    {
        return $this->belongsTo('App\Status');
    }

    protected static function boot()
    {

        Milestone::created(function ($model) {

            //Kalyani : Add activity log
            $data = Milestone::create_activity_log($model, Config::get('constants.FEED_CONSTANTS.MILESTONE'), Config::get('constants.FEED_CONSTANTS_MSGS.MILESTONE_CREATE'));

            app('App\Http\Controllers\FeedController')->create($data);

            return;
        });
        Milestone::saved(function ($model) {
            //Kalyani : Add activity log
            $data = Project::create_activity_log($model, Config::get('constants.FEED_CONSTANTS.MILESTONE'), Config::get('constants.FEED_CONSTANTS_MSGS.MILESTONE_UPDATE') . $model["status_id"]);

            app('App\Http\Controllers\FeedController')->create($data);

        });
    }
}
