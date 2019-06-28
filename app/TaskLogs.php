<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class TaskLogs extends Model
{
    protected $table = 'task_logs';
    protected $fillable = ['task_id', 'project_id', 'milestone_id', 'message', 'created_by', 'updated_by'];
    protected $guarded = ['id', 'created_at'];

}
