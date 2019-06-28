<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class MilestoneLogs extends Model
{
    protected $table = 'milestone_logs';
    protected $fillable = ['milestone_id', 'project_id', 'message', 'created_by', 'updated_by'];
    protected $guarded = ['id', 'created_at'];
}
