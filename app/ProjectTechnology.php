<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class ProjectTechnology extends Model
{
    protected $table = 'project_technologies';
    protected $guarded = ['id', 'created_at', 'updated_at'];
}
