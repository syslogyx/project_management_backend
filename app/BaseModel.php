<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class BaseModel extends Model
{

    public static function create_activity_log($model, $activityType, $msg)
    {
        $data = [];

        $data["activity_id"] = $model->id;
        $data["activity_type"] = $activityType;
        $data["message"] = $msg;
        return $data;
    }

    // public static function create_project_activity_log($model) {
    //     $data = [];

    //     $data["activity_id"] = $model->id;
    //     $data["activity_type"] = $activityType;
    //     $data["message"] = $msg;
    //     return $data;
    // }

}
