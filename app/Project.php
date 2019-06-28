<?php

namespace App;

use App\BaseModel;
use Validator;

class Project extends BaseModel
{

    protected $table = 'projects';
    protected $guarded = ['id', 'created_at'];
    private $rules = array(
        'name' => 'required | max:190|unique:projects,name,',
        'user_id' => 'required|numeric',
        'client_id' => 'required|numeric',
        'status_id' => 'required',
        'start_date' => 'required|date',
        'due_date' => 'required|date',
        'revised_date' => 'required|date',
        'duration_in_days' => 'required',
        'current_milestone_index' => 'required|numeric',
    );
    private $rule1 = array(
        'name' => 'required | max:190|unique:projects,name,',
        'user_id' => 'required|numeric',
        'start_date' => 'required|date',
        'due_date' => 'required|date',
        'company_name' => 'required',
        'role' => 'required',
        'type' => 'required|numeric',
        'duration_in_years' => 'required',
    );
    private $errors;
    public $temp_status_id;
    public $comment = "";
    public $create_status_log = false;

    public function validate($data, $type)
    {

//        if ($this->id)
        //            $this->rules['name'] .= $this->id;

        if ($type == "new") {
            if ($this->id) {
                $this->rules['name'] .= $this->id;
            }

            $validator = Validator::make($data, $this->rules);
        } else if ($type == "old") {
            if ($this->id) {
                $this->rule1['name'] .= $this->id;
            }

            $validator = Validator::make($data, $this->rule1);
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

    protected static function boot()
    {

        //on create
        Project::created(function ($model) {
            Project::create_status_log($model);

            //Kalyani : Add activity log
            // $data = Project::create_activity_log($model, Config::get('constants.FEED_CONSTANTS.PROJECT'),Config::get('constants.FEED_CONSTANTS_MSGS.PROJECT_CREATE'));

            // Project::create_project_activity_log($model);

            // app('App\Http\Controllers\FeedController')->create($data);

            return;
        });

        //on update
        Project::saved(function ($model) {
            if ($model->create_status_log) {
                Project::create_status_log($model);

                //Kalyani : Add activity log
                // $data = Project::create_activity_log($model, Config::get('constants.FEED_CONSTANTS.PROJECT_STATUS_UPDATED'),Config::get('constants.FEED_CONSTANTS_MSGS.PROJECT_STATUS_UPDATED').$model["status_id"]);

                // app('App\Http\Controllers\FeedController')->create($data);

            }
            return;
        });
    }

//    protected static function boot() {
    //        Project::saved(function ($model) {
    //            return $model;
    //        });
    //    }
    //    public function domain(){
    //        $this->hasOne('App\Domain', 'id', 'domain_id');
    //    }
    public function domain()
    {
        return $this->belongsToMany('App\Category', 'project_category_mapping', 'project_id', 'category_id');
    }

    public function user()
    {
        return $this->belongsTo('App\User');
    }

    public function status()
    {
        return $this->belongsTo('App\Status');
    }

    public function client()
    {
        return $this->belongsTo('App\Client');
    }

    public function technology()
    {
        return $this->belongsToMany('App\Technology', 'project_technologies', 'project_id', 'technology_id');
    }

    public function milestones()
    {
        return $this->hasMany('App\Milestone', 'project_id');
    }

    public function projectResource()
    {
        return $this->hasMany('App\ProjectResource', 'project_id')->orderBy('user_id', 'asc');
    }

    public function projectPoc()
    {
        return $this->hasMany('App\ProjectPoc', 'project_id');
    }

    public function projectCategoryMapping()
    {
        return $this->hasMany('App\ProjectCategoryMapping', 'project_id');
    }

    public function mom()
    {
        return $this->belongsToMany('App\Mom', 'mom_project', 'project_id', 'mom_id');
    }

    /* public function task() {
    $projectResourceId = $this->hasMany('App\ProjectResource','project_id');
    return $projectResourceId->hasMany('App\Task');
    } */

    public function save_status($posted_data)
    {
        if (isset($posted_data["project_id"])) {
            $projects = Project::find((int) $posted_data["project_id"]);
        } else {
            $projects = $posted_data;
        }

        $data = [];
        $data["activity_id"] = $projects->id;
        $data["activity_type_id"] = "PROJECT";
        $data["status_id"] = $posted_data["status_id"];
        if ($posted_data["comment"] != "") {
            $data["comment"] = $posted_data["comment"];
        } else {
            $data["comment"] = "Project Created";
        }

        $data["created_by"] = 1;
        $data["updated_by"] = 1;

        ProjectActivityStatusLog::create($data);
        if ($projects->status_id != $posted_data["status_id"]) {
            $this->temp_status_id = $posted_data["status_id"];
            $projects->status_id = $this->temp_status_id;
            $projects->save();
            //            $data1["status_id"]=$posted_data["status_id"];
            //            $this->update($data1);
        }
    }

    public static function create_status_log($model)
    {
        $data = [];

        $data["activity_id"] = $model->id;
        $data["activity_type_id"] = "PROJECT";
        $data["status_id"] = $model->status_id;
        $data["comment"] = $model->comment;
        $data["created_by"] = 1;
        $data["updated_by"] = 1;

        ProjectActivityStatusLog::create($data);
    }

    // public static function create_activity_log($model,$activityType,$msg) {
    //     $data = [];

    //     $data["activity_id"] = $model->id;
    //     $data["activity_type"] = $activityType;
    //     $data["message"] = $msg;
    //     return $data;
    // }

}
