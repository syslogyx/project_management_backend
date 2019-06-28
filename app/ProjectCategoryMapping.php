<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class ProjectCategoryMapping extends Model
{
    protected $table = 'project_category_mapping';
    protected $guarded = ['id', 'created_at', 'updated_at'];

    public function project()
    {
        return $this->belongsTo('App\Project');
    }

    public function category()
    {
        return $this->belongsTo('App\Category');
    }

    public function ProjectCategoryTechnology()
    {
        return $this->hasMany('App\ProjectCategoryTechnology', 'project_category_id');
    }
}
