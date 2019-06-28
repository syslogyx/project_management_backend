<?php

namespace App\Http\Controllers;

use App\Category;
use App\Project;
use App\ProjectResource;
use App\ProjectResourceTechnology;
use App\User;
use Config;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Input;

class ProjectResourceTechnologyController extends BaseController
{

    public function index()
    {
        $projectResource = ProjectResourceTechnology::with('technology', 'project_resource')->paginate(200);
        if ($projectResource->first()) {
            return $this->dispatchResponse(200, "", $projectResource);
        } else {
            return $this->dispatchResponse(200, "No Records Found!!", $projectResource);
        }
    }

    public function create()
    {
        $posted_data = Input::all();

        if ($posted_data) {
            DB::beginTransaction();
            $object = new ProjectResourceTechnology();

            $isPresentProjCatTecUser = $this->getPresentProjUserTech($posted_data);
            $project_id = $posted_data['project_id'];
            if ($isPresentProjCatTecUser == null && !$isPresentProjCatTecUser) {
                try {
                    $project_data = Project::where([
                        ['id', '=', $project_id],
                    ])->first();
                    foreach ($posted_data['user_id'] as $key1 => $value1) {
                        $project_data["user_id"] = $value1;
                        $project_data["domain_id"] = $posted_data["domain_id"];
                        $project_data["start_date"] = $posted_data["start_date"];
                        $project_data["due_date"] = $posted_data["due_date"];

                        $project_resource = $this->createProjectResource($project_data, $project_id);
                        $project_resource_id = $project_resource->id;

                        foreach ($posted_data['technologies'] as $key => $value) {
                            $data = [];
                            $data['project_resource_id'] = $project_resource_id;
                            $data['technology_id'] = $value;
                            $data["start_date"] = $posted_data["start_date"];
                            $data["due_date"] = $posted_data["due_date"];
                            $data['created_by'] = 1;
                            $data['updated_by'] = 1;

                            $model = ProjectResourceTechnology::create($data);
                        }

                        //add activity log
                        $this->activitylogForAddRemoveProRes($value1, $posted_data["domain_id"], $project_id, true);
                    }
                    DB::commit();
                    return $this->dispatchResponse(200, "Added Successfully...!!", $model);
                } catch (\Exception $e) {
                    DB::rollback();
                    throw $e;
                }
            } else {
                if ($isPresentProjCatTecUser[0]['status_id'] == Config::get('constants.STATUS_CONSTANT.ACTIVE')) {
                    DB::rollback();
                    return $this->dispatchResponse(300, 'Sorry!, Unable to add resource. Project with this domain and resource already exist.', null);
                } else {
                    DB::table('project_resources')
                        ->where('id', $isPresentProjCatTecUser[0]['id'])
                        ->update(array('start_date' => $posted_data["start_date"],
                            'due_date' => $posted_data["due_date"],
                            'status_id' => Config::get('constants.STATUS_CONSTANT.ACTIVE')));

                    DB::commit();
                    return $this->dispatchResponse(200, "Added Successfully...!!", $isPresentProjCatTecUser[0]);
                }
            }
        }
    }

    /**
     * Kalyani : make activity log for add or remove project resource
     */
    public function activitylogForAddRemoveProRes($userId, $domainId, $projectId, $isAdd)
    {
        //get user name
        $user = User::find((int) $userId);

        //get domain name
        $domain = Category::find((int) $domainId);

        //get project name
        $project = Project::find((int) $projectId);
        $username = "";
        $domainname = "";
        $projectname = "";
        if ($user != null) {
            $username = "<a href='" . Config::get('constants.WEB_URL_CONSTANTS.USER_VIEW') . $user->id . "'>" . $user->name . "</a>";
        }

        if ($domain != null) {
            $domainname = $domain->name;
        }

        if ($project != null) {
            $projectname = $project->name;
            $projectnameLink = "<a href='" . Config::get('constants.WEB_URL_CONSTANTS.PROJECT_VIEW') . $project->id . "'>" . $project->name . "</a>";
        }

        if ($isAdd) {
            //Kalyani : create activity log
            $msg = sprintf(Config::get('constants.FEED_CONSTANTS_MSGS.PROJECT_RESOURCE_ADDED'), $username, $projectnameLink, $domainname);
        } else {
            $username = "<a href='" . Config::get('constants.WEB_URL_CONSTANTS.USER_VIEW') . $user->id . "'>" . $user->name . "</a>";
            //Kalyani : create activity log
            $msg = sprintf(Config::get('constants.FEED_CONSTANTS_MSGS.PROJECT_RESOURCE_UPDATED'), $username, $projectnameLink);
        }

        $data = app('App\Http\Controllers\ProjectController')->create_project_activity_log($projectId, $msg, 1, 1);
    }

    public function update_dates()
    {
        $posted_data = Input::all();
        if ($posted_data) {
            $project_id = $posted_data['project_id'];
            $project_res_data = $posted_data['project_res_data'];

            foreach ($project_res_data as $key => $value) {

                $proj_res = ProjectResource::where([
                    ['id', '=', $value["proj_res_id"]],
                ])->first();
                $proj_res->start_date = $value["start_date"];
                $proj_res->due_date = $value["due_date"];
                $proj_res->save();

                $proj_res_tech = ProjectResourceTechnology::where([
                    ['project_resource_id', '=', $value["proj_res_id"]],
                ])->get();

                foreach ($proj_res_tech as $key1 => $value1) {
                    $value1->start_date = $value["start_date"];
                    $value1->due_date = $value["due_date"];
                    $value1->save();
                }
            }
            if ($proj_res_tech) {
                return $this->dispatchResponse(200, "Records Updated Successfully...!!", $proj_res_tech);
            }

        } else {
            throw new \Dingo\Api\Exception\StoreResourceFailedException('Unable to update.', $model->errors());
        }
    }

    public function update_date_for_resourse()
    {
        $posted_data = Input::all();
        if ($posted_data) {
            $proj_res_id = $posted_data['proj_res_id'];
            $project_id = $posted_data['project_id'];
            $start_date = $posted_data['start_date'];
            $due_date = $posted_data['due_date'];

            $proj_res = ProjectResource::where([
                ['id', '=', $proj_res_id],
            ])->first();

            $proj_res->start_date = $start_date;
            $proj_res->due_date = $due_date;
            $proj_res->save();

            $proj_res_tech = ProjectResourceTechnology::where([
                ['project_resource_id', '=', $proj_res_id],
            ])->get();

            foreach ($proj_res_tech as $key1 => $value1) {
                $value1->start_date = $start_date;
                $value1->due_date = $due_date;
                $value1->save();
            }

            if ($proj_res_tech) {
                return $this->dispatchResponse(200, "Records Updated Successfully...!!", $proj_res_tech);
            }

        } else {
            throw new \Dingo\Api\Exception\StoreResourceFailedException('Unable to update.', $model->errors());
        }
    }

    public function deleteResource($id)
    {
        $model = ProjectResource::find((int) $id);
        $project_resource_id = $model->id;
        try {
            DB::beginTransaction();

            // $updatedData = $model;
            // $updatedData = array();

            // $updatedData['status_id'] =  Config::get('constants.STATUS_CONSTANT.DELETED');

            // $model->update($updatedData);

            $proj_res_tech = ProjectResourceTechnology::where([
                ['project_resource_id', '=', $project_resource_id],
            ])->delete();

            // $deletedRows = $proj_res_tech->delete();
            $deletedRows1 = $model->delete();

            //add activity log
            $this->activitylogForAddRemoveProRes($model->user_id, $model->domain_id, $model->project_id, false);

            DB::commit();
            return $this->dispatchResponse(200, "Record Deleted Successfully...!!", null);
        } catch (\Exception $e) {
            DB::rollback();
            throw $e;
        }
    }

    public function getPresentProjUserTech($posted_data)
    {
        $project_id = $posted_data["project_id"];
        $domain_id = $posted_data["domain_id"];
        $technologies = $posted_data["technologies"];
        $user_id = $posted_data["user_id"];

        $project_resource = ProjectResource::where([
            ['project_id', '=', $project_id],
            ['domain_id', '=', $domain_id],
        ])->whereIN('user_id', $user_id)
            ->get();

        if ($project_resource) {
            foreach ($project_resource as $key => $value) {
                $project_resource_id = $value->id;

                $model = ProjectResourceTechnology::with('project_resource')->where([
                    ['project_resource_id', '=', $project_resource_id],
                ])->whereIN('technology_id', $technologies)
                    ->get()->toArray();

                if ($model) {
                    return $project_resource;
                }
            }
        } else {

        }
    }

    public function createProjectResource($project_data, $project_id)
    {
        $object = new ProjectResource();
        $project_resource_data = [];
        $project_resource_data["project_id"] = $project_id;
        $project_resource_data["user_id"] = $project_data["user_id"];
        $project_resource_data["domain_id"] = $project_data["domain_id"];
        $project_resource_data["status_id"] = Config::get('constants.STATUS_CONSTANT.ACTIVE');
        $project_resource_data["role"] = "Team Member";
        $project_resource_data["start_date"] = $project_data["start_date"];
        $project_resource_data["due_date"] = $project_data["due_date"];
        $project_resource_data["created_by"] = 1;
        $project_resource_data["updated_by"] = 1;
        $type = "create";

        $is_exist = $this->getProjCatTech($project_id, $project_data);
        if (!$is_exist) {
            // if ($object->validate($project_resource_data, $type)) {
            $model = ProjectResource::create($project_resource_data);
            if ($model) {
                return $model;
            }
        } else {
            $existed_id = $is_exist->id;
            return $is_exist;
            // return $this->dispatchResponse(200, "Project with this Domain and User already exist. ", null);
            //throw new \Dingo\Api\Exception\StoreResourceFailedException('Unable to create  Project Resources.', $object->errors());
        }
    }

    public function getProjCatTech($project_id, $project_data)
    {
        $user_id = $project_data["user_id"];
        $domain_id = $project_data["domain_id"];

        $project_resource = ProjectResource::where([
            ['project_id', '=', $project_id],
            ['domain_id', '=', $domain_id],
            ['user_id', '=', $user_id],
        ])->first();

        return $project_resource;
    }
}
