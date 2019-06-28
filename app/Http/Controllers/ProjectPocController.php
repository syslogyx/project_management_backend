<?php

namespace App\Http\Controllers;

use App\Http\Transformers\ProjectPocTransformer;
use App\ProjectPoc;
use Config;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Input;

class ProjectPocController extends BaseController
{
    public function index()
    {
        $projectPoc = ProjectPoc::orderBy('name', 'asc')->with('project')->paginate(200);
        if ($projectPoc->first()) {
            return $this->dispatchResponse(200, "", $projectPoc);
        } else {
            return $this->dispatchResponse(200, "No Records Found!!", $projectPoc);
        }
    }

    public function create()
    {
        $posted_data = Input::all();
        $posted_data["created_by"] = 1;
        $posted_data["updated_by"] = 1;

        $objectProjectPoc = new ProjectPoc();

        if ($objectProjectPoc->validate($posted_data)) {
            $model = ProjectPoc::create($posted_data);
            return $this->response->item($model, new ProjectPocTransformer())->setStatusCode(200);
        } else {
            throw new \Dingo\Api\Exception\StoreResourceFailedException('Unable to create Project POC.', $objectProjectPoc->errors());
        }
    }

    public function update($id)
    {
        $posted_data = Input::all();

        $model = ProjectPoc::find((int) $id);

        if ($model->validate($posted_data)) {
            if ($model->update($posted_data)) {
                return $this->response->item($model, new ProjectPocTransformer())->setStatusCode(200);
            }

        } else {
            throw new \Dingo\Api\Exception\StoreResourceFailedException('Unable to update Project POC.', $model->errors());
        }
    }

    public function view($id)
    {
        $model = ProjectPoc::with('project')->find((int) $id);

        if ($model) {
            return $this->response->item($model, new ProjectPocTransformer())->setStatusCode(200);
        }

    }

    public function getPocByProjectId($projectId, Request $request)
    {
        $page = $request->page;
        $limit = $request->limit;
        if (($page == null || $limit == null) || ($page == -1 || $limit == -1)) {
            $model = ProjectPoc::where([
                ['project_id', '=', $projectId], ['status', '=', 0],
            ])->with('project')->paginate(100);
        } else {
            $model = ProjectPoc::where([
                ['project_id', '=', $projectId], ['status', '=', 0],
            ])->with('project')->paginate($limit);
        }

        if ($model->first()) {
            return $this->dispatchResponse(200, "Data", $model);
        } else {
            return $this->dispatchResponse(Config::get('constants.SUCESS_CODE'), "No Records Found!!", null);
        }
        // if ($model)
        //     return $this->response->item($model, new ProjectPocTransformer())->setStatusCode(200);
    }

    /*
     * API to Change POC Status
     */
    public function changePOCStatus()
    {
        $posted_data = Input::all();
        $model = ProjectPoc::find((int) $posted_data['poc_id']);

        if ($model->first()) {
            $user = ProjectPoc::where('id', $posted_data['poc_id'])->update(['status' => $posted_data['status']]);

            $model = ProjectPoc::find((int) $model->id);
            return $this->dispatchResponse(200, "Status changed successfully", $model);
        } else {
            return $this->dispatchResponse(400, "Status is not changed successfully!", $model);
        }
    }
}
