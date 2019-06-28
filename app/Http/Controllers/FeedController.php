<?php

namespace App\Http\Controllers;

use App\Feed;

class FeedController extends Controller
{
    public function create($requestedData)
    {
        // $feedData = [];
        $requestedData["created_by"] = 1;
        $requestedData["updated_by"] = 1;
        // $feedData["activity_id"] = $requestedData['activity_id'];
        // $feedData["activity_type"] = $requestedData['activity_type'];
        // $feedData["message"] = $requestedData['message'];

        $model = Feed::create($requestedData);
        $feed_id = $model->id;

    }
}
