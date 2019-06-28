<?php

namespace App\Http\Controllers;

use App\Category;
use App\Http\Transformers\ProjectResourceTransformer;
use App\ProjectResource;
use App\User;
use Config;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Input;

class ProjectResourceController extends BaseController
{

    public function index(Request $request)
    {
        $page = $request->page;
        $limit = $request->limit;
        if (($page == null || $limit == null) || ($page == -1 || $limit == -1)) {
            $projectResource = ProjectResource::with('domain', 'user', 'project_resource_technology', 'project')->paginate(25);
        } else {
            $projectResource = ProjectResource::with('domain', 'user', 'project_resource_technology', 'project')->paginate($limit);
        }

        $temp = $projectResource->toArray();

        $len = count($temp["data"]);
        for ($i = 0; $i < $len; $i++) {
            $len1 = count($temp["data"][$i]["project_resource_technology"]);
            for ($j = 0; $j < $len1; $j++) {
                $tech = \App\Technology::where("id", "=", $temp["data"][$i]["project_resource_technology"][$j]["technology_id"])->get();

                $temp["data"][$i]["project_resource_technology"][$j]["technology"] = [
                    "id" => $tech[0]["id"],
                    "name" => $tech[0]["name"],
                ];
            }
        }

        $projectResource = $temp;
        //        $projectResource->project_resource_technology;
        if ($projectResource) {
            return $this->dispatchResponse(200, "", $projectResource);
            //            return $this->response->item($projectResource, new ProjectResourceTransformer())->setStatusCode(200);
        } else {
            return $this->dispatchResponse(200, "No Records Found!!", $projectResource);
        }
    }

    public function create()
    {
        $posted_data = Input::all();
        $posted_data["created_by"] = 1;
        $posted_data["updated_by"] = 1;
        $type = "create";

        $object = new ProjectResource();

        if ($object->validate($posted_data, $type)) {
            $model = ProjectResource::create($posted_data);
            if ($model) {
                //add activity log
                $this->activitylogForAddRemoveProRes($posted_data, true);
                return $this->response->item($model, new ProjectResourceTransformer())->setStatusCode(200);
            } else {
                throw new \Dingo\Api\Exception\StoreResourceFailedException('Unable to create  Project Resources.', $object->errors());
            }
        } else {
            throw new \Dingo\Api\Exception\StoreResourceFailedException('Unable to create  Project Resources.', $object->errors());
        }
    }

    /**
     * Kalyani : make activity log for add or remove project resource
     */
    public function activitylogForAddRemoveProRes($posted_data, $isAdd)
    {
        //get user name
        $user = User::find((int) $posted_data["user_id"]);

        // print_r($user);
        // die();

        //get domain name
        $domain = Category::find((int) $posted_data["domain_id"]);

        if ($isAdd) {
            $username = "";
            $domainname = "";
            if ($user != null) {
                $username = $user->name;
            }

            if ($domain != null) {
                $domainname = $domain->name;
            }

            // print_r($domain);
            // die();
            //Kalyani : create activity log
            $msg = sprintf(Config::get('constants.FEED_CONSTANTS_MSGS.PROJECT_RESOURCE_ADDED'), $username, $domainname);
        } else {
            //Kalyani : create activity log
            $msg = sprintf(Config::get('constants.FEED_CONSTANTS_MSGS.PROJECT_RESOURCE_UPDATED'), $user->name);
        }

        $data = app('App\Http\Controllers\ProjectController')->create_project_activity_log($posted_data["project_id"], $msg, 1, 1);
    }

    public function update($id)
    {
        $posted_data = Input::all();
        $type = "update";

        $model = ProjectResource::find((int) $id);

        if ($model->validate($posted_data, $type)) {
            if ($model->update($posted_data))
            //add activity log
            {
                $this->activitylogForAddRemoveProRes($posted_data, false);
            }

            return $this->response->item($model, new ProjectResourceTransformer())->setStatusCode(200);
        } else {
            throw new \Dingo\Api\Exception\StoreResourceFailedException('Unable to update Project Resource.', $model->errors());
        }
    }

    public function view($id)
    {
        $model = ProjectResource::find((int) $id);
        if ($model) {
            return $this->response->item($model, new ProjectResourceTransformer())->setStatusCode(200);
        }

    }

    public function view_by_project_id($projectId, Request $request)
    {
        $page = $request->page;
        $limit = $request->limit;

        $model = ProjectResource::where([
            ['project_id', '=', $projectId],
            ['status_id', '<>', '"' . Config::get('constants.STATUS_CONSTANT.DELETED') . '"'],
        ])->with('domain', 'user', 'project_resource_technology', 'project', 'task');

        if (($page != null && $page != 0) || ($limit != null && $limit != 0) || ($page == -1 && $limit == -1)) {
            $model = $model->paginate($limit);

            $temp = $model->toArray();
            $temp1 = $this->calculateDataProjectResourceTech($temp['data']);
            $temp['data'] = $temp1;
            $model = $temp;
            if ($model['data'] != null) {
                return $this->dispatchResponse(200, "", $model);
            }

        } else {
            $model = $model->get();
            $temp = $this->calculateDataProjectResourceTech($model->toArray());
            $model = $temp;
            if ($model != null) {
                return $this->dispatchResponse(200, "", $model);
            }

        }

        return $this->dispatchResponse(Config::get('constants.SUCESS_CODE'), "No Records Found!!", null);
    }

    public function calculateDataProjectResourceTech($temp)
    {
        $len = count($temp);
        for ($i = 0; $i < $len; $i++) {
            $len1 = count($temp[$i]["project_resource_technology"]);
            if ($len1 > 0) {
                for ($j = 0; $j < $len1; $j++) {
                    $tech = \App\Technology::where("id", "=", $temp[$i]["project_resource_technology"][$j]["technology_id"])->get();

                    $temp[$i]["project_resource_technology"][$j]["technology"] = [
                        "id" => $tech[0]["id"],
                        "name" => $tech[0]["name"],
                    ];
                }
            }

        }

        return $temp;
    }
    /**
     * Kalyani : Get the list of the project resource accoring to the filter
     */
    public function getProjectResourceList(Request $request)
    {

        $page = $request->page;
        $limit = $request->limit;

        $posted_data = Input::all();

        $query = ProjectResource::query();
        $query = $query->with('project', 'user', 'domain');
        $query = $query->where('status_id', '<>', Config::get('constants.STATUS_CONSTANT.DELETED'));
        $query = $this->searchFilterCondition($posted_data, $query);
        // $query = $query->with('milestones');
        $query = $query->orderBy('created_at', 'asc');

        if ($page == -1 || $limit == -1) {
            $tasks = $query->paginate(200);
        } else {
            $tasks = $query->paginate(200);
        }

        if ($tasks->first()) {
            return $this->dispatchResponse(200, "", $tasks);
        } else {
            return $this->dispatchResponse(200, "No Records Found!!", null);
        }
    }

    public function searchFilterCondition($posted_data, $query)
    {
        if (@$posted_data['project_id'] && $posted_data['project_id'] != 0) {
            $query = $query->where('project_id', '=', $posted_data['project_id']);
        }

        if (@$posted_data['status_id'] && $posted_data['status_id'] != null && $posted_data['status_id'] != "") {
            $query = $query->where('status_id', '=', $posted_data['status_id']);
        }
        return $query;
    }

}
