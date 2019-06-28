<?php

namespace App\Http\Controllers;

use App\Http\Transformers\ProjectActivityStatusLogTransformer;
use App\ProjectActivityStatusLog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Input;

class ProjectActivityStatusLogController extends BaseController
{

    public function index(Request $request)
    {
        $page = $request->page;
        $limit = $request->limit;

        if (($page == null || $limit == null) || ($page == -1 || $limit == -1)) {
            $status = ProjectActivityStatusLog::with('activityType', 'projectResource', 'status')->paginate(25);
        } else {
            $status = ProjectActivityStatusLog::with('activityType', 'projectResource', 'status')->paginate($limit);
        }

        if ($status->first()) {
            return $this->dispatchResponse(200, "", $status);

        } else {
            return $this->dispatchResponse(200, "No Records Found!!", $status);
        }
    }

    public function create()
    {
        $posted_data = Input::all();
        $posted_data["created_by"] = 1;
        $posted_data["updated_by"] = 1;

        $object = new ProjectActivityStatusLog();

        if ($object->validate($posted_data)) {
            $model = ProjectActivityStatusLog::create($posted_data);
            return $this->response->item($model, new ProjectActivityStatusLogTransformer())->setStatusCode(200);
        } else {
            throw new \Dingo\Api\Exception\StoreResourceFailedException('Unable to create  Project Activity Status Log.', $object->errors());
        }
    }

    public function update($id)
    {
        $posted_data = Input::all();

        $model = ProjectActivityStatusLog::find((int) $id);

        if ($model->validate($posted_data)) {
            if ($model->update($posted_data)) {
                return $this->response->item($model, new ProjectActivityStatusLogTransformer())->setStatusCode(200);
            }

        } else {
            throw new \Dingo\Api\Exception\StoreResourceFailedException('Unable to update Project Activity Status Logs.', $model->errors());
        }
    }

    public function view($id)
    {
        $model = ProjectActivityStatusLog::find((int) $id);
        if ($model) {
            return $this->response->item($model, new ProjectActivityStatusLogTransformer())->setStatusCode(200);
        }

    }

    public function view_by_id()
    {
        $data = Input::all();
        $model = ProjectActivityStatusLog::where([
            ['activity_id', '=', $data["project_id"]],
            ['activity_type_id', '=', $data["activity_type"]],
        ])->with("status")->orderBy('id', 'desc')->get();
        if ($model) {
            return $this->response->item($model, new ProjectActivityStatusLogTransformer())->setStatusCode(200);
        }

    }

}
