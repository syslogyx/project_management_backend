<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class ProjectResourceTechnology extends Model
{

    //project_resource_technology_mapping
    protected $table = 'project_resource_technology_mapping';
    protected $guarded = ['id', 'created_at', 'updated_at'];
    private $errors;

    public function errors()
    {
        return $this->errors;
    }

    public function technology()
    {
        return $this->belongsTo('App\Technology');
    }

    public function project_resource()
    {
        return $this->belongsTo('App\projectResource');
    }

}
