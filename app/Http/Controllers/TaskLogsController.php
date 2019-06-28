<?php

namespace App\Http\Controllers;

use App\TaskLogs;

class TaskLogsController extends Controller
{
    public function create($requestedData)
    {
        // print_r($requestedData);
        // die();
        $model = TaskLogs::create($requestedData);
        // $feed_id = $model->id;

    }
}
