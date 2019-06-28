<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Validator;

class ProjectPoc extends Model
{

    protected $table = 'project_poc';
    protected $guarded = ['id', 'created_at', 'updated_at'];
    private $rules = array(
        'project_id' => 'required|numeric',
        'name' => 'required | max:190',
        'mobile_primary' => 'required',
        'mobile_secondary' => 'required',
        'email_personal' => 'required|email',
        'email_official' => 'required|email',
    );
    private $errors;

    public function validate($data)
    {
        // if ($this->id)
        //     $this->rules['name'] .= $this->id;

        $validator = Validator::make($data, $this->rules);

        $name = $data['name'];
        $project_id = $data['project_id'];
        if ($this->id) {
            // print_r("inside if");
        } else {
            // print_r("inside else");
            $validator->after(function ($validator) use ($name, $project_id) {
                $checkName = ProjectPoc::where('name', $name)->where('project_id', $project_id)->get();
                if (count($checkName) > 0) {
                    $validator->errors()->add('name', 'POC named ' . $name . ' is already assigned. ');
                }
            });
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

    public function project()
    {
        return $this->belongsTo('App\Project');
    }

}
