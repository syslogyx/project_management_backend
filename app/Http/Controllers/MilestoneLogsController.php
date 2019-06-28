<?php

namespace App\Http\Controllers;

use App\MilestoneLogs;

class MilestoneLogsController extends Controller
{
    public function create($requestedData)
    {
        // print_r($requestedData);
        // die();
        $model = MilestoneLogs::create($requestedData);
        // $feed_id = $model->id;

    }
}
