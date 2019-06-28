<?php

namespace App\Http\Controllers;

use App\Http\Transformers\ResourceMatrixLogTransformer;
use App\ProjectResource;
use App\ResourceMatrixLog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Input;

class ResourceMatrixController extends BaseController
{

    public function index(Request $request)
    {
        $page = $request->page;
        $limit = $request->limit;
        if (($page == null || $limit == null) || ($page == -1 || $limit == -1)) {
            $resource_log = ResourceMatrixLog::with("project", "user")->paginate(25);
        } else {
            $resource_log = ResourceMatrixLog::with("project", "user")->paginate($limit);
        }

        if ($resource_log->first()) {
            return $this->dispatchResponse(200, "", $resource_log);
            //            return $this->response->item($resource_log, new ResourceMatrixLogTransformer())->setStatusCode(200);
        } else {
            return $this->dispatchResponse(200, "No Records Found!!", $resource_log);
        }
    }

    public function create()
    {
        $posted_data = Input::all();
        $posted_data["created_by"] = 1;
        $posted_data["updated_by"] = 1;

        $due_date = ProjectResource::where([
            ['project_id', '=', $posted_data["project_id"]],
            ['user_id', '=', $posted_data["user_id"]],
            ['domain_id', '=', $posted_data["domain_id"]],
        ])->pluck('due_date')->first();

        if ($due_date) {
            $posted_data["start_date"] = $due_date;

            $update = ProjectResource::where([
                ['project_id', '=', $posted_data["project_id"]],
                ['user_id', '=', $posted_data["user_id"]],
                ['domain_id', '=', $posted_data["domain_id"]],
            ])->update(['due_date' => $posted_data["due_date"]]);
        }

        if ($update) {
//            unset($posted_data["domain_id"]);
            $object = new ResourceMatrixLog();

            if ($object->validate($posted_data)) {
                $model = ResourceMatrixLog::create($posted_data);
                return $this->response->item($model, new ResourceMatrixLogTransformer())->setStatusCode(200);
            } else {
                throw new \Dingo\Api\Exception\StoreResourceFailedException('Unable to create Resource Matrix Log.', $object->errors());
            }
        }
    }

    public function getLogs()
    {
        $posted_data = Input::all();
        $resource_log = ResourceMatrixLog::where([
            ['project_id', '=', $posted_data["project_id"]],
            ['user_id', '=', $posted_data["user_id"]],
            ['domain_id', '=', $posted_data["domain_id"]],
        ])->with("project", "user", "domain")->get();
        foreach ($resource_log as $key => $value) {
            $created_by_name = \App\User::select('name')
                ->where([
                    ['id', '=', $value["created_by"]],
                ])
                ->first();
            $resource_log[$key]["created_by_name"] = $created_by_name["name"];
        }

        return $this->dispatchResponse(200, "", $resource_log);
    }

}
