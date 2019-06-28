<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use Validator;

class ProjectResource extends Model
{

    protected $table = 'project_resources';
    protected $guarded = ['id', 'created_at', 'updated_at'];
    /*private $rules = array(
    'project_id' => 'required|unique_multiple:project_resources,user_id,project_id',
    'user_id' => 'required|numeric',
    'domain_id' => 'required|numeric',
    'status_id' => 'required|numeric',
    'role' => 'required',
    'start_date' => 'nullable|date',
    'due_date' => 'nullable|date',
    );*/
    public $rules = array(
        'domain_id' => 'required|numeric',
        'status_id' => 'required',
        'role' => 'required',
        'start_date' => 'nullable|date',
        'due_date' => 'nullable|date',
    );
    private $messages = array(
        'unique_multiple' => 'Project and User combination already exists.',
    );
    private $errors;

    public function validate($data, $type)
    {

        // if($type == "create"){
        $this->rules['project_id'] = 'required|unique_multiple:project_resources,user_id,project_id,domain_id,active_status';
        $this->rules['user_id'] = 'required|numeric';
//          }else{
        //             $this->rules;
        //          }

        Validator::extend('unique_multiple', function ($attribute, $value, $parameters, $validator) {
            // Get the other fields
            $fields = $validator->getData();

            // Get table name from first parameter
            $table = array_shift($parameters);

            // Build the query
            $query = DB::table($table);

            // Add the field conditions
            foreach ($parameters as $i => $field) {
                $query->where($field, $fields[$field]);
            }

            // Validation result will be false if any rows match the combination
            return ($query->count() == 0);
        });

        $validator = Validator::make($data, $this->rules, $this->messages);

        if ($validator->fails()) {
            $this->errors = $validator->errors();
            return false;
        }
        return true;
    }

    public function errors()
    {
        array('unique_multiple' => 'This combination already exists.');
        return $this->errors;
    }

    public function task()
    {
        return $this->hasMany('App\Task', 'assigned_to');
    }

    public function project_resource_technology()
    {
        return $this->hasMany('App\ProjectResourceTechnology', 'project_resource_id');
    }

    public function project()
    {
        return $this->belongsTo('App\Project');
    }

    public function user()
    {
        return $this->belongsTo('App\User');
    }

    public function domain()
    {
        return $this->belongsTo('App\Category');
    }

    public function status()
    {
        return $this->belongsTo('App\Status');
    }

    public function projectActivityStatusLog()
    {
        return $this->hasMany('App\ProjectActivityStatusLog', 'project_resource_id');
    }
    public function technologies()
    {
        $technologies = DB::table("technologies")->select('*')
            ->whereIn('id', function ($query) {
                $query->select('technology_id')->from('project_resource_technology_mapping')->where("project_resource_id", $this->projectActivityStatusLog);
            })
            ->get();
        print_r($this->project_resource_technology);die();
        return $technologies;
    }

}
