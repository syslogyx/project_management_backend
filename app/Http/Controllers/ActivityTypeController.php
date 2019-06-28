<?php

namespace App\Http\Controllers;

use App\ActivityType;
use App\Http\Transformers\ActivityTypeTransformer;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Input;

class ActivityTypeController extends BaseController
{

    public function index(Request $request)
    {
        $page = $request->page;
        $limit = $request->limit;

        if (($page == null || $limit == null) || ($page == -1 || $limit == -1)) {
            $activity_types = ActivityType::paginate(25);
        } else {
            $activity_types = ActivityType::paginate($limit);
        }

        if ($activity_types->first()) {
            return $this->dispatchResponse(200, "", $activity_types);
        } else {
            return $this->dispatchResponse(200, "No Records Found!!", $activity_types);
        }
    }

    public function create()
    {
        $posted_data = Input::all();

        $activityType = new ActivityType();

        if ($activityType->validate($posted_data)) {
            $model = ActivityType::create($posted_data);
            return $this->response->item($model, new ActivityTypeTransformer())->setStatusCode(200);
        } else {
            throw new \Dingo\Api\Exception\StoreResourceFailedException('Unable to create  Activity type.', $activityType->errors());
        }
    }

    public function update($id)
    {
        $posted_data = Input::all();
        $model = ActivityType::find((int) $id);

        $activityType = new ActivityType();

        if ($activityType->validate($posted_data)) {
            if ($model->update($posted_data)) {
                return $this->response->item($model, new ActivityTypeTransformer())->setStatusCode(200);
            }

        } else {
            throw new \Dingo\Api\Exception\StoreResourceFailedException('Unable to update  Activity type.', $activityType->errors());
        }
    }

    public function view($id)
    {
        $model = ActivityType::find((int) $id);
        if ($model) {
            return $this->response->item($model, new ActivityTypeTransformer())->setStatusCode(200);
        }

    }

}
