<?php

namespace App\Http\Controllers;

use App\Http\Transformers\ProjectTransformer;
use App\Milestone;
use App\Project;
use App\ProjectCategoryMapping;
use App\ProjectCategoryTechnology;
use App\ProjectResource;
use App\ProjectResourceTechnology;
use App\ProjectTechnology;
use App\Task;
use App\User;
use Config;
use DateTime;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Input;
use PDF;

class ProjectController extends BaseController
{

    public function index(Request $request)
    {
        $requestBody = $request;
        $page = $request->page;
        $limit = $request->limit;

        if (($page == null || $limit == null) || ($page == -1 || $limit == -1)) {

            $projects = Project::where("type", "=", 2)->with('user', "domain", "client")->orderBy('due_date', 'asc')->paginate(200);
        } else {
            $condition = [["type", "=", 2]];

            if (isset($requestBody->user_id) || $requestBody->user_id != null) {
                $projectResourceIdJSON = ProjectResource::where("user_id", "=", $requestBody->user_id)->select('project_id')->get();
                $projectResourceId = [];
                foreach ($projectResourceIdJSON as $key => $value) {
                    array_push($projectResourceId, $value->project_id);
                }

                $projects = Project::where($condition)->whereIn('id', $projectResourceId)->orWhere('lead_id', $requestBody->user_id)->with('user', "domain", "client")->orderBy('due_date', 'asc')->paginate($limit);

            } else {
                $projects = Project::where($condition)->with('user', "domain", "client")->orderBy('due_date', 'asc')->paginate($limit);
            }
        }

        if ($projects->first()) {

            foreach ($projects as $key => $value) {

                //get milestone and task count of the project
                $this->getMilestoneTaskCount($value);

                //get milestone and task count of the project
                $this->getProjectResourceCount($value);

                $query = "SELECT b.technology_id FROM `project_category_mapping` a " .
                    "INNER JOIN `project_category_technology_mapping` b ON (a.id = b.project_category_id) " .
                    "WHERE a.project_id = " . $projects[$key]["id"];

                $tech_list = DB::select($query);

                $tech_ids = [];
                $tech_ids2 = [];
                foreach ($tech_list as $k => $v) {
                    array_push($tech_ids, $v->technology_id);
                }

                $query2 = "SELECT a.*, b.technology_id FROM `project_resources` a " .
                "INNER JOIN `project_resource_technology_mapping` b " .
                "ON (a.id = b.project_resource_id) " .
                "WHERE a.project_id = " . $projects[$key]["id"] . " AND b.technology_id IN (" . implode(",", $tech_ids) . ")";

                $tech_list2 = DB::select($query2);

                foreach ($tech_list2 as $k => $v) {
                    array_push($tech_ids2, $v->technology_id);
                }

                $result = array_intersect($tech_ids, $tech_ids2);

                if (count($result) !== count($tech_ids)) {
                    $projects[$key]["isAllRsourcesAssigned"] = 1;
                }
            }

            return $this->dispatchResponse(200, "", $projects);
            // return $this->response->item($projects, new Project())->setStatusCode(200);
        } else {
            return $this->dispatchResponse(Config::get('constants.SUCESS_CODE'), "No Records Found!!", null);
        }
    }

    /**
     * Kalyani : Calculate total number of milestones and tasks
     */
    public function getMilestoneTaskCount($project)
    {
        $milestoneList = Milestone::where([
            ['project_id', '=', $project->id],
        ])->get();

        $totalMilestoneCount = 0;
        $totalTaskCount = 0;
        if ($milestoneList != null && !$milestoneList->isEmpty()) {

            //get total count of milestones
            $totalMilestoneCount = count($milestoneList);

            $milestones = [];
            foreach ($milestoneList as $key => $value) {
                array_push($milestones, $value->id);
            }

            //get total count of tasks
            if (count($milestones) != 0) {
                $taskList = Task::whereIn('milestone_id', $milestones)
                    ->where('status_id', '<>', Config::get('constants.STATUS_CONSTANT.DELETED'))
                    ->get();
                $totalTaskCount = count($taskList);
            }
        }
        $project['total_milestones'] = $totalMilestoneCount;
        $project['total_tasks'] = $totalTaskCount;
    }

    /**
     * Kalyani : Calculate total number of project resource
     */
    public function getProjectResourceCount($project)
    {
        // $projectResourceList = ProjectResource::where([['project_id', '=', $project->id], ['status_id', '=', Config::get('constants.STATUS_CONSTANT.ACTIVE')]])->get();

        // $projectResourceList = ProjectResource::where([['project_id', '=', $project->id]])->get();

        $projectResourceList = ProjectResource::where([['project_id', '=', $project->id]])->distinct('user_id')->pluck('user_id');

        $totalProjectResourceList = 0;
        if ($projectResourceList != null && !$projectResourceList->isEmpty()) {
            //get total count of project resource
            $totalProjectResourceList = count($projectResourceList);
        }
        $project['total_resources'] = $totalProjectResourceList;
    }

    public function create()
    {

        $posted_data = Input::all();
        // $posted_data["created_by"] = 1;
        // $posted_data["updated_by"] = 1;

        $sdate = $posted_data["start_date"];
        $edate = $posted_data["due_date"];

        $sdate = new DateTime($sdate);
        $edate = new DateTime($edate);

        $diff = date_diff($sdate, $edate);
        $days = $diff->format("%a");
        $posted_data["duration_in_years"] = $this->daysToYearConversion($days);

        $category = $posted_data["domain"];
        unset($posted_data["domain"]);

        $objectProject = new Project();

        $type = $posted_data["project_type"];
        unset($posted_data["project_type"]);

        if ($objectProject->validate($posted_data, $type)) {
            try {
                DB::beginTransaction();
                $model = Project::create($posted_data);
                $project_id = $model->id;

                if ($model) {
                    if (isset($category)) {
                        if ($type == "new") {
                            $projectResource = $this->createSingleProjectResource($posted_data, $project_id);
                        } else if ($type == "old") {
                            $projectResource = $this->createProjectResource($posted_data, $project_id, $category, $type);
                        }
                        $projectCategory = $this->createProjectCategory($category, $project_id, $posted_data["created_by"], $posted_data["updated_by"]);
                    }

                    $user_name = User::find((int) $posted_data["created_by"]);
                    $userURL = "<a href='" . Config::get('constants.WEB_URL_CONSTANTS.USER_VIEW') . $user_name->id . "'>" . $user_name->name . "</a>";
                    $projectURL = "<a href='" . Config::get('constants.WEB_URL_CONSTANTS.PROJECT_VIEW') . $model->id . "'>" . $model->name . "</a>";

                    //Kalyani : create activity log
                    $msg = sprintf(Config::get('constants.FEED_CONSTANTS_MSGS.PROJECT_CREATE'), $projectURL, $userURL);

                    $data = $this->create_project_activity_log($project_id, $msg, 1, 1);

                    DB::commit();
                    return $this->response->item($model, new ProjectTransformer())->setStatusCode(200);
                } else {
                    DB::rollback();
                    return $this->dispatchResponse(400, "Something went wrong.", $objectProject->errors());
                }
            } catch (\Exception $e) {
                DB::rollback();
                throw $e;
            }

        } else {

            throw new \Dingo\Api\Exception\StoreResourceFailedException('Unable to create  project.', $objectProject->errors());
        }
    }

    public function daysToYearConversion($convert)
    {
        // $convert = $convert + 1;

        $years = ($convert / 365.2525); // days / 365 days
        $years = floor($years); // Remove all decimals

        $month = ($convert - ($years * 365.2525)) / 30.5;
        $month = floor($month); // Remove all decimals

        $days = ($convert - ($years * 365.2525) - ($month * 30.5));
        $days = floor($days);
        //        if ($days >= 20) {
        //            $month = $month + 1;
        //        }
        //        if ($month == 12) {
        //            $month = 0;
        //            $years = $years + 1;
        //        }
        //        return (($years > 0) ? $years . ' Year' . ($years > 1 ? 's' : '') . ', ' : '') . (($month > 0) ? $month . ' Month' . ($month > 1 ? 's' : '') . ' ' : '');
        //print_r($days);
        //die();
        return (($years > 0) ? $years . ' Year' . ($years > 1 ? 's' : '') . ' ' : '') . (($month > 0) ? $month . ' Month' . ($month > 1 ? 's' : '') . ' ' : '') . (($days > 0) ? $days . ' Day' . ($days > 1 ? 's' : '') : '');
    }

    public function update($id)
    {
        $posted_data = Input::all();

        $model = Project::find((int) $id);

        $sdate = $posted_data["start_date"];
        $edate = $posted_data["due_date"];

        $sdate = new DateTime($sdate);
        $edate = new DateTime($edate);

        $diff = date_diff($sdate, $edate);
        $days = $diff->format("%a");
        $posted_data["duration_in_years"] = $this->daysToYearConversion($days);

        $type = $posted_data["project_type"];
        unset($posted_data["project_type"]);

        if ($model->validate($posted_data, $type)) {

            try {
                if ($type == "new") {
                    if ($model->status_id !== $posted_data["status_id"]) {
                        $data = [];
                        $data["id"] = $id;
                        $data["comment"] = "";
                        $data["status_id"] = $posted_data["status_id"];
                        $data = (object) $data;
                        $project = new Project();
                        $project->create_status_log($data);
                    }
                    // if ($model->user_id !== $posted_data["user_id"]) {
                    //     $proj_res = ProjectResource::where([
                    //                 ['project_id', '=', $id],
                    //                 ['user_id', '=', $model->user_id],
                    //                 ['domain_id', '=', 1]
                    //             ])->first();

                    //     $proj_res->user_id = $posted_data["user_id"];
                    //     $proj_res->save();
                    // }
                    if ($model->start_date !== $posted_data["start_date"]) {
                        $proj_res = ProjectResource::where([
                            ['project_id', '=', $id],
                            ['start_date', '=', $model->start_date],
                        ])->get();
                        foreach ($proj_res as $key => $value) {
                            $value->start_date = $posted_data["start_date"];
                            $value->save();
                        }
                    }
                    if ($model->due_date !== $posted_data["due_date"]) {
                        $proj_res = ProjectResource::where([
                            ['project_id', '=', $id],
                            ['due_date', '=', $model->due_date],
                        ])->get();
                        foreach ($proj_res as $key => $value) {
                            $value->due_date = $posted_data["due_date"];
                            $value->save();
                        }
                    }
                } else if ($type == "old") {
                    if (@$posted_data["company_start_date"] && $posted_data["company_start_date"] == null) {
                        unset($posted_data["company_start_date"]);
                        unset($posted_data["company_due_date"]);
                    }

                    $proj_res = ProjectResource::where([
                        ['project_id', '=', $id],
                        ['user_id', '=', $model->user_id],
                    ])->get();

                    foreach ($proj_res as $key => $value) {
                        $value->start_date = $posted_data["start_date"];
                        $value->due_date = $posted_data["due_date"];
                        $value->save();
                    }
                }

                //Kalyani : create activity log
                $this->create_project_activity_log_for_update($model, $posted_data, $id);
                if ($model->update($posted_data)) {
                    DB::commit();
                    return $this->response->item($model, new ProjectTransformer())->setStatusCode(200);
                } else {
                    DB::rollback();
                    throw new \Dingo\Api\Exception\StoreResourceFailedException('Unable to update projects.', $model->errors());
                }
            } catch (\Exception $e) {
                DB::rollback();
                throw $e;
            }
        } else {
            throw new \Dingo\Api\Exception\StoreResourceFailedException('Unable to update projects.', $model->errors());
        }
    }

    public function view($id)
    {
        $projects = Project::with('milestones.task')->find((int) $id);
        //        $project_technology = $this->technology_of_project_id($id);
        //        print_r($project_technology);
        //        die();
        if ($projects) {
            return $this->response->item($projects, new ProjectTransformer())->setStatusCode(200);
        }

    }

    //not in use
    public function ptojectTaskList($id)
    {
        $projects = Project::with('task')->find((int) $id);
        if ($projects) {
            return $this->response->item($projects, new ProjectTransformer())->setStatusCode(200);
        }

    }

    //not in use
    public function checkProjectTechnology($projectId, $technologyId)
    {
        $model = ProjectTechnology::where([
            ['project_id', '=', $projectId],
            ['technology_id', '=', $technologyId],
        ])->first();
        if ($model) {
            return $model;
        }

    }

    public function getProjectCategory($projectId, $categoryId)
    {
        $model = ProjectCategoryMapping::where([
            ['project_id', '=', $projectId],
            ['category_id', '=', $categoryId],
        ])->first();

        if ($model) {
            return $model;
        }

    }

    public function getProjectCategoriesTechnology($project_cat_id, $technology_id)
    {
        $model = ProjectCategoryTechnology::where([
            ['project_category_id', '=', $project_cat_id],
            ['technology_id', '=', $technology_id],
        ])->first();

        if ($model) {
            return $model;
        }

    }

//    $project_cat_id = $model->id;
    //        $model_tech = ProjectCategoryTechnology::where([
    //                    ['project_category_id', '=', $project_cat_id],
    //                    ['technology_id', '=', $technology_id]
    //                ])->first();

    public function deleteProjectTechnologies($projectId, $technologyId)
    {
        $model = ProjectTechnology::where([
            ['project_id', '=', $projectId],
            ['technology_id', '=', $technologyId],
        ])->delete();
        if ($model) {
            return $model;
        }

    }

    public function deleteProjectCategories($projectId, $categoryId)
    {
        $model = ProjectCategoryMapping::where([
            ['project_id', '=', $projectId],
            ['category_id', '=', $categoryId],
        ])->delete();
        if ($model) {
            return $model;
        }

    }

    public function technology_of_project_id($projectId)
    {
        $model = ProjectTechnology::where([
            ['project_id', '=', $projectId],
        ])->get();
        if ($model) {
            return $model;
        }

    }

    public function category_of_project_id($projectId)
    {
        $model = ProjectCategoryMapping::where([
            ['project_id', '=', $projectId],
        ])->get();
        if ($model) {
            return $model;
        }

    }

    public function update_status()
    {
        $posted_data = Input::all();

        $model = Project::find((int) $posted_data["project_id"]);

        //Kalyani : create activity log
        $this->create_project_activity_log_for_update($model, $posted_data, $posted_data["project_id"]);
        $model->comment = $posted_data["comment"];
        $model->status_id = $posted_data["status_id"];
        $model->create_status_log = true;

        $model->save();

        return $this->dispatchResponse(200, "Status Updated Successfully...!!", $model);
    }

    public function createProjectResource($posted_data, $project_id, $category, $type)
    {
        $objectProject = new Project();
        //        print_r($category);die();
        if ($category) {
            foreach ($category as $key => $value) {
                $project_resource_data = [];
                $project_resource_data["project_id"] = $project_id;
                $project_resource_data["user_id"] = $posted_data["user_id"];
                $project_resource_data["domain_id"] = $value["id"];
                $project_resource_data["status_id"] = $posted_data["status_id"];
                $project_resource_data["role"] = $posted_data["role"];
                $project_resource_data["start_date"] = $posted_data["start_date"];
                $project_resource_data["due_date"] = $posted_data["due_date"];
                //                $project_resource_data["start_date"] = $value["tech_start_date"];
                //                $project_resource_data["due_date"] = $value["tech_end_date"];
                $project_resource_data["created_by"] = $posted_data["created_by"];
                $project_resource_data["updated_by"] = $posted_data["updated_by"];
                $project_resource_data["active_status"] = 1;

                $model_project_resource = \App\ProjectResource::create($project_resource_data);

                if ($type == "old" && isset($model_project_resource->id)) {
                    $project_resource_id = $model_project_resource->id;
                    foreach ($value["technology"] as $key1 => $value1) {
                        $data = [];
                        $data["project_resource_id"] = $project_resource_id;
                        $data["technology_id"] = $value1["id"];
                        $data["start_date"] = $value1["start_date"];
                        $data["due_date"] = $value1["due_date"];
                        $data["created_by"] = $model_project_resource->created_by;
                        $data["updated_by"] = $model_project_resource->updated_by;
                        $project_resource_technology = ProjectResourceTechnology::create($data);
                    }
                }
            }
            if ($model_project_resource) {
                return $model_project_resource;
            } else {
                $model = Project::where([
                    ['id', '=', $project_id],
                ])->delete();
                throw new \Dingo\Api\Exception\StoreResourceFailedException('Unable to create  project resources.', $objectProject->errors());
            }
        }
    }

    public function createSingleProjectResource($posted_data, $project_id)
    {
        $objectProject = new Project();
        //For Manager
        $project_resource_data = [];
        $project_resource_data["project_id"] = $project_id;
        $project_resource_data["user_id"] = $posted_data["user_id"];
        $project_resource_data["domain_id"] = 1;
        $project_resource_data["status_id"] = $posted_data["status_id"];
        $project_resource_data["role"] = "Manager";
        $project_resource_data["start_date"] = $posted_data["start_date"];
        $project_resource_data["due_date"] = $posted_data["due_date"];
        $project_resource_data["created_by"] = $posted_data["created_by"];
        $project_resource_data["updated_by"] = $posted_data["updated_by"];
        $project_resource_data["active_status"] = 1;

        $model_project_resource = \App\ProjectResource::create($project_resource_data);

        //For Lead
        if (@$posted_data["lead_id"]) {
            $project_resource_data = [];
            $project_resource_data["project_id"] = $project_id;
            $project_resource_data["user_id"] = $posted_data["lead_id"];
            $project_resource_data["domain_id"] = 1;
            $project_resource_data["status_id"] = $posted_data["status_id"];
            $project_resource_data["role"] = "Lead";
            $project_resource_data["start_date"] = $posted_data["start_date"];
            $project_resource_data["due_date"] = $posted_data["due_date"];
            $project_resource_data["created_by"] = $posted_data["created_by"];
            $project_resource_data["updated_by"] = $posted_data["updated_by"];
            $project_resource_data["active_status"] = 1;

            $model_project_resource = \App\ProjectResource::create($project_resource_data);

            if ($model_project_resource) {
                return $model_project_resource;
            } else {
                $model = Project::where([
                    ['id', '=', $project_id],
                ])->delete();
                throw new \Dingo\Api\Exception\StoreResourceFailedException('Unable to create  project resources.', $objectProject->errors());
            }
        }
    }

    //not in use
    public function createProjectTechnology($technology, $project_id)
    {
        $objectProject = new Project();
        foreach ($technology as $key => $value) {
            $proj_tech_exist = $this->checkProjectTechnology($project_id, $value);
            if (!$proj_tech_exist) {
                $data = [];
                $data["project_id"] = $project_id;
                $data["technology_id"] = $value;
                $data["created_by"] = 1;
                $data["updated_by"] = 1;
                $project_technology = ProjectTechnology::create($data);
            } else {
                $project_technology = "";
            }
        }
        if ($project_technology) {
            return $project_technology;
        } else {
            return false;
        }
    }

//    function updateStartAndDueDateOfUser() {
    //        $posted_data = Input::all();
    //
    //        if ($posted_data) {
    //            $proj_res = ProjectResource::where([
    //                        ['project_id', '=', $posted_data["project_id"]],
    //                        ['user_id', '=', $posted_data["user_id"]],
    //                        ['domain_id', '=', 1]
    //                    ])->first();
    //
    //            $proj_res->user_id = $posted_data["user_id"];
    //            $proj_res->save();
    //        }
    //        if ($proj_res) {
    //            return $proj_res;
    //        } else {
    //            return false;
    //        }
    //    }

    public function createProjectCategory($category, $project_id, $created_by, $updated_by)
    {
        $objectProject = new Project();
        //        print_r($category);die();
        if ($category) {
            foreach ($category as $key => $value) {
                $data = [];
                $data["project_id"] = $project_id;
                $data["category_id"] = $value["id"];
                $data["created_by"] = $created_by;
                $data["updated_by"] = $updated_by;
                $project_category = ProjectCategoryMapping::create($data);
                $project_category_id = $project_category->id;
                $technology = $value["technology"];
                // $project_technology = $this->createProjectTechnology($technology, $project_id);
                if ($project_category) {
                    foreach ($technology as $key => $value) {
                        $data = [];
                        $data["project_category_id"] = $project_category_id;
                        $data["technology_id"] = $value["id"];
                        $data["created_by"] = $created_by;
                        $data["updated_by"] = $updated_by;
                        $project_category_technology = ProjectCategoryTechnology::create($data);
                    }
                }
            }
            if ($project_category_technology) {
                return $project_category_technology;
            } else {
                $model = Project::where([
                    ['id', '=', $project_id],
                ])->delete();
                throw new \Dingo\Api\Exception\StoreResourceFailedException('Unable to create  project categories.', $objectProject->errors());
            }
        }
    }

    public function createProjectCategoryTechnologies()
    {
        $posted_data = Input::all();
        $type = $posted_data["type"];
        if ($type == "old") {
            $this->createProjectResourceForOldProject($posted_data);
        }
        $model = $this->createProjectCategorySingle($posted_data);
        if ($model) {
            return $this->dispatchResponse(200, "Added Successfully...!!", $model);
            //            return $this->response->item($model, new ProjectCategoryTransformer())->setStatusCode(200);
        } else {
            return $this->dispatchResponse(200, "Domain with this technology already exist.", null);
        }
    }

    public function createProjectResourceForOldProject($posted_data)
    {
        $technologies = $posted_data["technology"];
        $is_resource_exist = $this->getProjectResource($posted_data["project_id"], $posted_data["user_id"], $posted_data["domain_id"]);

        if (!isset($is_resource_exist)) {
            $objectProject = new Project();
            if ($posted_data) {
                $project_resource_data = [];
                $project_resource_data["project_id"] = $posted_data["project_id"];
                $project_resource_data["user_id"] = $posted_data["user_id"];
                $project_resource_data["domain_id"] = $posted_data["domain_id"];
                $project_resource_data["status_id"] = null;
                $project_resource_data["role"] = "Team Member";
                $project_resource_data["start_date"] = $posted_data["start_date"];
                $project_resource_data["due_date"] = $posted_data["end_date"];
                $project_resource_data["created_by"] = $posted_data["created_by"];
                $project_resource_data["updated_by"] = $posted_data["updated_by"];
                $project_resource_data["active_status"] = 1;

                $model_project_resource = \App\ProjectResource::create($project_resource_data);

                if (isset($model_project_resource->id)) {
                    $project_resource_id = $model_project_resource->id;
                    foreach ($technologies as $key1 => $value1) {
                        $data = [];
                        $data["project_resource_id"] = $project_resource_id;
                        $data["technology_id"] = $value1["id"];
                        $data["start_date"] = $value1["start_date"];
                        $data["due_date"] = $value1["due_date"];
                        $data["created_by"] = $model_project_resource->created_by;
                        $data["updated_by"] = $model_project_resource->updated_by;
                        $project_resource_technology = ProjectResourceTechnology::create($data);
                    }
                }

                if ($model_project_resource) {
                    return $model_project_resource;
                } else {
                    $model = Project::where([
                        ['id', '=', $project_id],
                    ])->delete();
                    throw new \Dingo\Api\Exception\StoreResourceFailedException('Unable to create  project resources.', $objectProject->errors());
                }
            }
        } else {
            if (isset($is_resource_exist->id)) {
                $project_resource_id = $is_resource_exist->id;
                foreach ($technologies as $key1 => $value1) {
                    $data = [];
                    $data["project_resource_id"] = $project_resource_id;
                    $data["technology_id"] = $value1["id"];
                    $data["start_date"] = $value1["start_date"];
                    $data["due_date"] = $value1["due_date"];
                    $data["created_by"] = $is_resource_exist->created_by;
                    $data["updated_by"] = $is_resource_exist->updated_by;
                    $project_resource_technology = ProjectResourceTechnology::create($data);
                }
                return $project_resource_technology;
            }
        }
    }

    public function getProjectResource($project_id, $user_id, $domain_id)
    {
        $model = ProjectResource::where([
            ['project_id', '=', $project_id],
            ['domain_id', '=', $domain_id],
            ['user_id', '=', $user_id],
        ])->first();

        if ($model) {
            return $model;
        }

    }

    //not in use
    public function createProjectCategoryTechnogy($category, $project_id)
    {
        $objectProject = new Project();
        foreach ($category as $key => $value) {
            $data = [];
            $data["project_id"] = $project_id;
            $data["category_id"] = $value;
            $data["created_by"] = 1;
            $data["updated_by"] = 1;
            $project_category = ProjectCategoryMapping::create($data);
        }
        if ($project_category) {
            return $project_category;
        } else {
            $model = Project::where([
                ['id', '=', $project_id],
            ])->delete();
            throw new \Dingo\Api\Exception\StoreResourceFailedException('Unable to create  project categories.', $objectProject->errors());
        }
    }

    public function createProjectCategorySingle($posted_data)
    {
        $project_id = $posted_data["project_id"];
        $category_id = $posted_data["domain_id"];
        $technologies = $posted_data["technology"];
        //        $tech_start_date = $posted_data["start_date"];
        //        $tech_end_date = $posted_data["end_date"];
        //        if (Input::get("user_id")) {
        //
        //        }
        $proj_cat_is_exist = $this->getProjectCategory($project_id, $category_id);
        $project_category = null;
        if (!$proj_cat_is_exist) {
            $data["project_id"] = $project_id;
            $data["category_id"] = $category_id;
            $data["created_by"] = $posted_data["created_by"];
            $data["updated_by"] = $posted_data["updated_by"];
            $project_category = ProjectCategoryMapping::create($data);
            $project_category_id = $project_category->id;
            if ($project_category) {
                //$project_technology = $this->createProjectTechnology($technologies, $project_id);
                //                if ($project_technology) {

                foreach ($technologies as $key => $value) {
                    $data = [];
                    $data["project_category_id"] = $project_category_id;
                    $data["technology_id"] = $value["id"];
                    $data["created_by"] = $posted_data["created_by"];
                    $data["updated_by"] = $posted_data["updated_by"];
                    $project_category_technology = ProjectCategoryTechnology::create($data);
                }
                //                }
            }
            return $project_category;
        } else {
            $project_category_technology = null;
            $project_cat_id = $proj_cat_is_exist->id;

            foreach ($technologies as $key => $value) {
                $is_exist_tech = $this->getProjectCategoriesTechnology($project_cat_id, $value);
                if (!$is_exist_tech) {
                    $data = [];
                    $data["project_category_id"] = $project_cat_id;
                    $data["technology_id"] = $value["id"];
                    $data["created_by"] = $proj_cat_is_exist->created_by;
                    $data["updated_by"] = $proj_cat_is_exist->updated_by;
                    $project_category_technology = ProjectCategoryTechnology::create($data);
                }
            }
            return $project_category_technology;
        }
    }

    public function updateManager()
    {
        $posted_data = Input::all();
        $project_id = $posted_data["project_id"];
        $old_user_id = $posted_data["old_user_id"];

        $start_date = $posted_data['start_date'];
        $due_date = $posted_data['due_date'];
        unset($posted_data['start_date']);
        unset($posted_data['due_date']);

        $projects = Project::find((int) $project_id);
        //Kalyani : create activity log
        $this->create_project_activity_log_for_update($projects, $posted_data, $project_id);

        $posted_data['start_date'] = $start_date;
        $posted_data['due_date'] = $due_date;

        $data = ProjectResource::where([
            ['project_id', '=', $project_id],
            ['domain_id', '=', 1],
            ['active_status', '=', 1],
        ])->first();

        if ($data) {
            $data->active_status = 0;
            $data->due_date = $posted_data["old_due_date"];
            $data->save();
        }

        if ($posted_data["isChecked"] == "false") {
            //change end date of all domain and technologies.
            $res_data = ProjectResource::where([
                ['project_id', '=', $project_id],
                ['user_id', '=', $old_user_id],
            ])->get();

            if ($res_data) {
                foreach ($res_data as $key => $value) {
                    $value->due_date = $posted_data["old_due_date"];
                    $value->save();

                    $res_tech_data = ProjectResourceTechnology::where([
                        ['project_resource_id', '=', $value->id],
                    ])->get();

                    foreach ($res_tech_data as $key1 => $value1) {
                        $value1->due_date = $posted_data["old_due_date"];
                        $value1->save();
                    }
                }
            }
        }

        $model = $this->createSingleProjectResource($posted_data, $project_id);

        if ($model) {

            //here insert data into manager log table.
            DB::table('projects')
                ->where('id', $project_id)
                ->update(['user_id' => $posted_data["user_id"]]);

            return $this->dispatchResponse(200, "Records Updated Successfully...!!", $model);
        } else {
            return $this->dispatchResponse(200, "Something went wrong.", null);
        }
    }

    /*
     *Kalyani : save activity log
     */
    public function create_project_activity_log($project_id, $msg, $created_by, $updated_by)
    {
        $data = [];
        $data["project_id"] = $project_id;
        $data["message"] = $msg;
        $data["created_by"] = $created_by;
        $data["updated_by"] = $updated_by;

        app('App\Http\Controllers\ProjectLogsController')->create($data);
    }

    /*
     *Kalyani : save activity log for update
     */
    public function create_project_activity_log_for_update($actualdata, $requesteddata, $project_id)
    {

        $user_name = User::find((int) $requesteddata["updated_by"]);
        // $user_name = $user->name;

        //Kalyani : create activity log
        $projectURL = "<a href='" . Config::get('constants.WEB_URL_CONSTANTS.PROJECT_VIEW') . $actualdata->id . "'>" . $actualdata->name . "</a>";

        $userURL = "<a href='" . Config::get('constants.WEB_URL_CONSTANTS.USER_VIEW') . $user_name->id . "'>" . $user_name->name . "</a>";

        //check project name
        if (@$requesteddata["name"] && $actualdata->name != $requesteddata["name"]) {
            $msg = sprintf(Config::get('constants.FEED_CONSTANTS_MSGS.PROJECT_NAME_CHANGED'), $projectURL, $userURL);

            $msg = $msg . $actualdata->name . " to <b>" . $requesteddata["name"] . "</b>" . ".";

            $data = $this->create_project_activity_log($project_id, $msg, $requesteddata["updated_by"], $requesteddata["updated_by"]);
        }

        //check start date
        if (@$requesteddata["start_date"]) {
            $date[] = explode(" ", $actualdata->start_date);
            $oldDate = new DateTime($date[0][0]);
            $formetedOldDate = $oldDate->format('d-m-Y');
            $newDate = new DateTime($requesteddata["start_date"]);
            $formetedNewDate = $newDate->format('d-m-Y');
            if ($date[0][0] != $requesteddata["start_date"]) {
                $msg = sprintf(Config::get('constants.FEED_CONSTANTS_MSGS.PROJECT_START_DATE_CHANGED'), $projectURL, $userURL);
                $msg = $msg . $formetedOldDate . " to <b>" . $formetedNewDate . "</b>" . ".";
                $data = $this->create_project_activity_log($project_id, $msg, 1, 1);
            }
        }

        //check due date of project
        if (@$requesteddata["due_date"]) {
            // print_r($actualdata->due_date);
            $date1[] = explode(" ", $actualdata->due_date);
            $oldDate = new DateTime($date1[0][0]);
            $formetedOldDate = $oldDate->format('d-m-Y');
            $newDate = new DateTime($requesteddata["due_date"]);
            $formetedNewDate = $newDate->format('d-m-Y');
            if ($date1[0][0] != $requesteddata["due_date"]) {
                $msg = sprintf(Config::get('constants.FEED_CONSTANTS_MSGS.PROJECT_END_DATE_CHANGED'), $projectURL, $userURL);
                $msg = $msg . $formetedOldDate . " to <b>" . $formetedNewDate . "</b>" . ".";
                $data = $this->create_project_activity_log($project_id, $msg, 1, 1);
            }
        }

        // check description
        // if(@$requesteddata["description"] && $actualdata->description != $requesteddata["description"]){
        //     $msg = sprintf(Config::get('constants.FEED_CONSTANTS_MSGS.PROJECT_DESC_CHANGED'), $projectURL, "<b>".$user_name."</b>");
        //     $msg = $msg.$actualdata->description." to ".$requesteddata["description"];
        //     $data=$this->create_project_activity_log($project_id, $msg, 1, 1);
        // }

        // check manager
        if (@$requesteddata["user_id"] && $actualdata->user_id != $requesteddata["user_id"]) {
            $msg = sprintf(Config::get('constants.FEED_CONSTANTS_MSGS.PROJECT_MANAGER_CHANGED'), $projectURL, $userURL);
            //get new name of manager
            $newmanager = User::find((int) $requesteddata["user_id"]);

            $oldManagerURL = "<a href='" . Config::get('constants.WEB_URL_CONSTANTS.USER_VIEW') . $actualdata->id . "'>" . $actualdata->user->name . "</a>";

            $newManagerURL = "<a href='" . Config::get('constants.WEB_URL_CONSTANTS.USER_VIEW') . $newmanager->id . "'>" . $newmanager->name . "</a>";

            $msg = $msg . $oldManagerURL . " to " . $newManagerURL . ".";
            $data = $this->create_project_activity_log($project_id, $msg, 1, 1);
        }

        // check status
        if (@$requesteddata["status_id"] && ($actualdata->status_id != $requesteddata["status_id"])) {
            $msg = sprintf(Config::get('constants.FEED_CONSTANTS_MSGS.PROJECT_STATUS_UPDATED'), $projectURL, $userURL);

            $msg = $msg . $actualdata->status_id . " to <b>" . $requesteddata["status_id"] . "</b>" . ".";

            $data = $this->create_project_activity_log($project_id, $msg, 1, 1);
        }
    }

    public function topFiveProjectListWithResourcesCount()
    {

        $projects = Project::where('type', 2)->with('projectResource.user', 'projectResource.domain')->get();
        if (count($projects) > 0) {
            foreach ($projects as $key => $value) {
                $projects[$key]["count"] = ProjectResource::where('project_id', $value->id)->distinct('user_id')->count('user_id');

                $userData = [];
                $data = [];
                $tempDataArray = [];
                $tempIdArray = [];
                // $projects[$key]["resource_desc"] = [];

                foreach ($value->projectResource as $key1 => $value1) {
                    if (count($userData) > 0) {
                        foreach ($userData as $key2 => $value2) {
                            if ($value2["user_name"] == $value1->user->name) {
                                $tempData .= ', ' . $value1->domain->name;
                                $tempIdData = $value1->user->id;
                            } else {
                                $tempData .= ')';
                                array_push($tempDataArray, $tempData);
                                array_push($tempIdArray, $tempIdData);
                                $tempData = $value1->user->name . '(' . $value1->domain->name;
                                $tempIdData = $value1->user->id;

                                $userData = [];
                                $data["user_name"] = $value1->user->name;
                                $data["user_id"] = $value1->user->id;
                                $data["user_des"] = $value1->domain->name;
                                array_push($userData, $data);
                            }
                        }
                    } else {
                        $data["user_name"] = $value1->user->name;
                        $data["user_id"] = $value1->user->id;
                        $data["user_des"] = $value1->domain->name;
                        array_push($userData, $data);
                        $tempData = $value1->user->name . '(' . $value1->domain->name;
                        $tempIdData = $value1->user->id;
                    }
                }
                $tempData .= ')';
                array_push($tempDataArray, $tempData);
                array_push($tempIdArray, $tempIdData);
                $projects[$key]["resource_desc"] = $tempDataArray;
                $projects[$key]["resource_ids"] = $tempIdArray;
                unset($value->projectResource);
                // usort($projects[$key]["count"], array($this, "cmp"));
            }
        }
        $projectArr = json_decode(json_encode($projects), true);
        $this->sortArrayByKey($projectArr, "count", false, false); //number sort (descending order)
        if (count($projectArr) > 5) {
            $remainingProjectCount = count($projectArr) - 5;
            $remainingResourceCount = 0;
            $remainingResourceName = [];

            for ($ind = 5; $ind < count($projectArr); $ind++) {
                $remainingResourceCount += $projectArr[$ind]["count"];
                array_push($remainingResourceName, $projectArr[$ind]["resource_desc"]);
                $projectArr[$ind]["other_data"] = [
                    "project_remaining_count" => $remainingProjectCount,
                    "resource_remaining_count" => $remainingResourceCount,
                    "project_remaining_name" => $remainingResourceName,
                ];
            }
        }

        return $this->dispatchResponse(200, "Data.", $projectArr);
    }

    public function sortArrayByKey(&$array, $key, $string = false, $asc = true)
    {
        if ($string) {
            usort($array, function ($a, $b) use (&$key, &$asc) {
                if ($asc) {
                    return strcmp(strtolower($a{ $key}), strtolower($b{ $key}));
                } else {
                    return strcmp(strtolower($b{$key}), strtolower($a{$key}));
                }

            });
        } else {
            usort($array, function ($a, $b) use (&$key, &$asc) {
                if ($a[$key] == $b{ $key}) {return 0;}
                if ($asc) {
                    return ($a{$key} < $b{$key}) ? -1 : 1;
                } else {
                    return ($a{$key} > $b{$key}) ? -1 : 1;
                }

            });
        }
    }

    public function getProjectHighlights(Request $request)
    {
        $requestBody = $request;
        $page = $request->page;
        $limit = $request->limit;

        if (($page == null || $limit == null) || ($page == -1 || $limit == -1)) {
            $projects = Project::where("type", "=", 2)->with('user', "projectPoc", "client")->orderBy('due_date', 'asc')->paginate(200);
        } else {
            $projects = Project::where('type', 2)->with('user', "projectPoc", "client")->orderBy('due_date', 'asc')->paginate($limit);
        }
        if ($projects) {
            foreach ($projects as $key => $value) {
                if ($value->lead_id) {
                    $projects[$key]["leads"] = User::where('id', '=', $value->lead_id)->first();
                }
            }

            return $this->dispatchResponse(200, "Data.", $projects);
        } else {
            return $this->dispatchResponse(200, "No Records Found.", null);
        }
    }

    public function getDeliverySchedules(Request $request)
    {
        $requestBody = $request;
        $page = $request->page;
        $limit = $request->limit;
        $type = $request->type;
        $currentDate = date('Y-m-d');

        // $projects = Project::where('type',2)->with('user', "projectPoc", "client","milestones")->orderBy('due_date', 'asc')->get();

        $str = DB::raw("DATEDIFF('" . $currentDate . "',milestones.due_date) as delay");
        $query = DB::table('projects')
            ->select('projects.*', 'milestones.due_date as m_due_date', 'milestones.title as m_title', 'milestones.status_id as status_id', $str)
            ->join('milestones', 'projects.id', '=', 'milestones.project_id');

        if ($type == "month") {

            $currentDate = date('Y-m');
            $query->where(DB::raw("(DATE_FORMAT(milestones.due_date,'%Y-%m'))"), $currentDate);

        } else if ($type == "today") {

            $currentDate = date('Y-m-d');
            // $currentDate = $currentDate.' 00:00:00';
            $query->where(DB::raw("(DATE_FORMAT(milestones.due_date,'%Y-%m-%d'))"), $currentDate);
            // ->where("milestones.due_date","=","'".$currentDate."%'");

        } else if ($type == "week") {

            $currentDate = date('Y-m-d');
            $query->whereRaw("WEEK(`milestones`.`due_date`) = WEEK( '" . $currentDate . "')");

        }

        // $currentDate = date('Y-m-d');
        $query->where('milestones.status_id', '!=', Config::get('constants.STATUS_CONSTANT.CLOSED'))->orderBy('milestones.due_date', 'asc');
        // $query->whereRaw("DATEDIFF((DATE_FORMAT(milestones.due_date,'%Y-%m-%d')),'".$currentDate."') > 0");

        if (($page == null || $limit == null) || ($page == -1 || $limit == -1)) {
            $data = $query->paginate(200);
        } else {
            $data = $query->paginate($limit);
        }
        return $this->dispatchResponse(200, "Data.", $data);
    }

    public function generatePdfForProjects($id)
    {
        // $data = app('App\Http\Controllers\MilestoneController')->update_status($posted_data);

        $projects = Project::where("type", "=", 2)->where("id", "=", $id)->with('user', "domain", "client", "projectPoc", "projectResource", "projectResource.user", "projectResource.domain", "projectResource.task")->get();

        if ($projects->first()) {

            foreach ($projects as $key => $value) {
                //get milestone and task count of the project
                $this->getMilestoneTaskCount($value);

                //get milestone and task count of the project
                $this->getProjectResourceCount($value);

                $milestoneIdList = Milestone::where([
                    ['project_id', '=', $value->id],
                ])->pluck('id');

                $leadName = User::where("id", $value->lead_id)->pluck("name");
                $value["lead_name"] = $leadName[0];

                $con_in_progresss = [Config::get('constants.STATUS_CONSTANT.START'), Config::get('constants.STATUS_CONSTANT.IN_PROGRESS'), Config::get('constants.STATUS_CONSTANT.PAUSE'), Config::get('constants.STATUS_CONSTANT.HOLD')];

                $con_assigned = [Config::get('constants.STATUS_CONSTANT.ASSIGNED'), Config::get('constants.STATUS_CONSTANT.PENDING')];

                $con_resolved = [Config::get('constants.STATUS_CONSTANT.RESOLVED'), Config::get('constants.STATUS_CONSTANT.CLOSED')];

                $con_pending_app = [Config::get('constants.STATUS_CONSTANT.PENDING_APPROVED')];

                if ($milestoneIdList != null && !$milestoneIdList->isEmpty()) {
                    $assigned = Task::select(DB::raw('COUNT(id) as task_count'))
                        ->whereIn('milestone_id', $milestoneIdList)
                        ->whereIn("status_id", $con_assigned)
                        ->get();

                    $in_progress = Task::select(DB::raw('COUNT(id) as task_count'))
                        ->whereIn('milestone_id', $milestoneIdList)
                        ->whereIn("status_id", $con_in_progresss)
                        ->get();

                    $resolved = Task::select(DB::raw('COUNT(id) as task_count'))
                        ->whereIn('milestone_id', $milestoneIdList)
                        ->whereIn("status_id", $con_resolved)
                        ->get();

                    $pending_app = Task::select(DB::raw('COUNT(id) as task_count'))
                        ->whereIn('milestone_id', $milestoneIdList)
                        ->whereIn("status_id", $con_pending_app)
                        ->get();

                    $value["task_completed"] = $resolved[0]->task_count;
                    $value["task_in_progress"] = $in_progress[0]->task_count;
                    $value["task_pending"] = $assigned[0]->task_count;
                    $value["task_pending_app"] = $pending_app[0]->task_count;
                }

                foreach ($value->projectResource as $key1 => $value1) {
                    $sql = 'SELECT SUM(estimated_time) AS total_estimation, SEC_TO_TIME(SUM(TIME_TO_SEC(spent_time))) AS total_spent FROM `tasks` WHERE assigned_to = ' . $value1->id;

                    $query_data = DB::select($sql);
                    $value1['total_estimation'] = $query_data[0]->total_estimation;
                    $value1['total_spent'] = $query_data[0]->total_spent;
                }

            }
        }
        // return $projects;
        $projects[0]["report_date"] = date("d-m-Y");
        $now = new DateTime();
        // $now = $now->format('Y-m-d H:i:s');
        $now = $now->format('d-m-Y');
        view()->share(compact('projects'));
        $pdf = PDF::loadView('report/project');
        return $pdf->stream($projects[0]["name"] . '_' . $now . '.pdf');
    }
}
