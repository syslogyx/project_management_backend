<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Feed extends Model
{
    protected $table = 'feed';
    protected $fillable = ['activity_id', 'activity_type', 'message', 'created_by'];
    protected $guarded = ['id', 'created_at'];
}
