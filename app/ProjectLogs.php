<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class ProjectLogs extends Model
{
    protected $table = 'project_logs';
    protected $fillable = ['project_id', 'message', 'created_by', 'updated_by'];
    protected $guarded = ['id', 'created_at'];

    public function project()
    {
        return $this->belongsTo('App\Project', 'project_id');
    }
}
