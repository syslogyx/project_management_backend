<?php

namespace App\Http\Controllers;

use App\Http\Transformers\UserFilterTransformer;
use App\Mom;
use App\MomClient;
use App\MomProject;
use App\User;
use DateTime;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Input;

class SearchController extends BaseController
{

    public function filter(Request $request)
    {
        // return 'fgdg';
        $page = $request->page;
        $limit = $request->limit;
        $posted_data = Input::all();

        $query = User::query();
        if ($posted_data == "" || $posted_data == null) {
            $users = \App\ProjectResource::all()->pluck("user_id");
            $query->whereIn("id", $users)->get();
        }

        if (@$posted_data["user_id"]) {
            $id = $posted_data["user_id"];
            $query->where("id", $posted_data["user_id"])->get();
        }

        if (@$posted_data["project"]) {
            //            $query->whereHas('project', function($query) use($posted_data) {
            //                $query->whereIn('id', Input::get("project"));
            //            })->get();
            //            $query->whereHas('projectResource', function($query) use($posted_data) {
            //                $query->whereIn('project_id', Input::get("project"))
            //                        ->where("type","=",2);
            //            })->get();
            $query->whereHas('projectResource', function ($query) use ($posted_data) {
                $query->whereIn('project_id', $posted_data["project"]);
            })->get();
        }

        if (@$posted_data["technology"]) {
            $query->whereHas('technology', function ($query) use ($posted_data) {
                $query->whereIn('technologies.id', $posted_data["technology"])
                    ->orWhereIn('technologies.parent_id', $posted_data["technology"]);
            })->get();
        }

        if (@$posted_data["duration"]) {
            $duration = $posted_data["duration"];

            if ($duration["start_year"] > 0 || $duration["start_month"] > 0) {
                // print_r($duration["start_year"]);
                if ($posted_data["technology"]) {
                    $duration = $this->calculateDurationInMonthsAndYears($duration);
                    $query->whereHas('userTechnologyMapping', function ($query) use ($posted_data, $duration) {
                        $query->where('duration_in_month', '>=', $duration)
                            ->whereIn("technology_id", $posted_data["technology"]);
                    })->get();
                } else {
                    $duration = $this->calculateDurationInMonthsAndYears($duration);
                    $query->whereHas('userTechnologyMapping', function ($query) use ($posted_data, $duration) {
                        $query->where('duration_in_month', '>=', $duration);
                    })->get();
                }
                //
            }

        }

        if (@$posted_data["technology_group"]) {
            //            $users  = DB::table('categories_technology_mapping')
            //                    ->whereIn('category_id', Input::get("technology_group"))
            //                    ->pluck('technology_id')
            //                    ->get();

            // $query->whereHas('technology', function($query) use($posted_data) {
            //     $query->whereIn('technologies.id', DB::table('categories_technology_mapping')
            //                     ->whereIn('category_id', Input::get("technology_group"))
            //                     ->pluck('technology_id'));
            // })->get();

            $query->whereHas('userTechnologyMapping', function ($query) use ($posted_data) {
                $query->whereIn('domain_id', $posted_data["technology_group"]);
            })->get();
        }

        if (@$posted_data["status"]) {

            $statusInfo = $posted_data["status"];

            $status = $statusInfo["name"];
            $projectId = $statusInfo["project"];

            $prjctResources = DB::table('project_resources')->whereIn('project_id', $projectId)->get();

            $now = new DateTime();
            $formetedDate = $now->format('Y-m-d');
            $data = [];
            if ($status == "active") {

                foreach ($prjctResources as $key => $value) {
                    $formatedDueDate = date('Y-m-d', strtotime($value->due_date));
                    if (strtotime($formatedDueDate) > strtotime($formetedDate)) {
                        //                        $data = $value->user_id;
                        array_push($data, $value->user_id);
                    }
                }
                //                die();
            } else if ($status == "deactive") {

                foreach ($prjctResources as $key => $value) {
                    $formatedDueDate = date('Y-m-d', strtotime($value->due_date));

                    if (strtotime($formetedDate) > strtotime($formatedDueDate)) {
                        //                        $data = $value->user_id;
                        array_push($data, $value->user_id);
                    }
                }
            }

            $query->whereIn("id", $data)->get();
            /* $query->whereHas('technology', function($query) use($posted_data) {
        $query->whereIn('technologies.id', DB::table('categories_technology_mapping')
        ->whereIn('category_id', Input::get("technology_group"))
        ->pluck('technology_id'));
        })->get(); */
        }
        //        $user = $query->toSql();

        // if(($page != null && $page != 0) && ($limit != null && $limit != 0)){
        //     $user = $query->orderBy('name', 'asc')->with("project", "technology", "userTechnologyMapping","projectResource")->paginate($limit);
        // }
        // else{
        $user = $query->orderBy('name', 'asc')->with("project", "technology", "userTechnologyMapping", "projectResource")->get();
        // }

        //        return $this->response->item($query->with("project", "technology", "userTechnologyMapping")->get(), new UserFilterTransformer())->setStatusCode(200);
        //        return DataTables::eloquent($query)
        //                ->setTransformer(new UserFilterTransformer)
        //                ->toJson();
        //     //        return $query->with("project", "technology", "userTechnologyMapping")->get();
        // if ($user->first()) {
        //     foreach ($user as $key => $value) {
        //         $project_data = $this->projectArray($value, $value['projectResource']);

        //         // print_r($project_data);
        //         // die();

        //         $technology_data = $this->technologyArray($value['technology']);
        //         $user[$key]['project'] = $project_data;
        //     }
        //     return $this->dispatchResponse(200, "",$user);
        // }else{
        //     return $this->dispatchResponse(200, "No Records Found!!",null);
        // }

        return $this->response->collection($user, new UserFilterTransformer());

        // $data = (array)$this->response->collection($user, new UserFilterTransformer());

        // if (($page != null && $page != 0) && ($limit != null && $limit != 0)) {
        //     $perPage = $limit;
        // } else {
        //     $perPage = 10;
        // }

        // $currentPage = LengthAwarePaginator::resolveCurrentPage();

        // $itemCollection = collect($data["original"]);

        // $currentPageItems = $itemCollection->slice(($currentPage * $perPage) - $perPage, $perPage)->values()->all();

        // $paginatedItems= new LengthAwarePaginator($currentPageItems , count($itemCollection), $perPage);

        // $paginatedItems->setPath($request->url());

        // if ($paginatedItems->first()){
        //     return response()->json(['status_code' => 200, 'message' => 'User List', 'data' => $paginatedItems]);
        // }else{
        //     return response()->json(['status_code' => 404, 'message' => 'Record not found']);
        // }
    }

    // Calculate duration in months from year & month
    public function calculateDurationInMonthsAndYears($duration)
    {
        $years = $duration["start_year"];
        $months = $duration["start_month"];
        $durationInMonths = ($years * 12) + $months;
        $durationInDays = $durationInMonths * 30;
        return $durationInDays;
    }

    public function projectArray($user, $project_resource)
    {
        $project_data = [];

        if ($project_resource != null) {
            foreach ($project_resource as $key => $value) {

                $project_data1 = \App\Project::where([
                    ['id', '=', $project_resource[$key]["project_id"]],
                    ['type', '=', 2],
                ])
                    ->first();
                if ($project_data1) {
                    $project_data1["user_id"] = $user["id"];
                    array_push($project_data, $project_data1);
                }

                unset($project_data[$key]["created_by"]);
                unset($project_data[$key]["updated_by"]);
                unset($project_data[$key]["created_at"]);
                unset($project_data[$key]["updated_at"]);

                $domains = \App\Category::where('id', $project_resource[$key]["domain_id"])->get();
                $project_data[$key]["domain"] = $domains;
                unset($project_data[$key]["domain_id"]);

                $work_end_date = \App\ProjectResource::select('due_date')
                    ->where([
                        ['project_id', '=', $project_resource[$key]["project_id"]],
                        ['user_id', '=', $project_resource[$key]["user_id"]],
                        ['domain_id', '=', $project_resource[$key]["domain_id"]],
                        ['active_status', '=', $project_resource[$key]["active_status"]],
                    ])
                    ->first();

                $project_data[$key]["work_end_date"] = $work_end_date["due_date"];
                $project_data[$key]["active_status"] = $project_resource[$key]["active_status"];

                $time_allocation_log = \App\ResourceMatrixLog::where([
                    ['project_id', '=', $project_resource[$key]["project_id"]],
                ])
                    ->get();

                foreach ($time_allocation_log as $key1 => $value1) {
                    $created_by_name = \App\User::select('name')
                        ->where([
                            ['id', '=', $time_allocation_log[$key1]["created_by"]],
                        ])
                        ->first();

                    $time_allocation_log[$key1]["created_by_name"] = $created_by_name["name"];
                }
                $project_data[$key]["time_allocation_log"] = $time_allocation_log;
            }
        }

        return $project_data;
    }

    public function technologyArray($technology)
    {

        $technology_data = $technology;
        foreach ($technology as $key => $value) {

            if ($technology_data[$key]["parent_id"] != null) {
                $parent_name = \App\Technology::select('name')
                    ->where([
                        ['id', '=', $technology[$key]["parent_id"]],
                    ])
                    ->first();
                $technology_data[$key]["parent_name"] = $parent_name["name"];
            }
            $duration = \App\UserTechnologyMapping::select('duration')
                ->where([
                    ['technology_id', '=', $technology[$key]["id"]],
                    ['user_id', '=', $technology[$key]["pivot"]["user_id"]],
                ])->first();
            $technology_data[$key]["duration"] = $duration["duration"];

            unset($technology_data[$key]["created_by"]);
            unset($technology_data[$key]["updated_by"]);
            unset($technology_data[$key]["created_at"]);
            unset($technology_data[$key]["updated_at"]);
            unset($technology_data[$key]["pivot"]);
        }
    }

    public function filterMoM(Request $request)
    {
        $page = $request->page;
        $limit = $request->limit;
        $posted_data = Input::all();

        $query = Mom::query();
        if ($posted_data == "" || $posted_data == null) {
            $query->get();
        }

        if ($posted_data["project_id"]) {

            $mom = MomProject::whereIn("project_id", $posted_data["project_id"])->pluck("mom_id");
            $query->whereIn("id", $mom);
        }
        //  print_r($mom);
        // die();

        if ($posted_data["client_name"]) {
            $mom = MomClient::whereIn("name", $posted_data["client_name"])->pluck("mom_id");
            $query->whereIn("id", $mom);
        }

        if ($posted_data["status"]) {
            $query->where("status", $posted_data["status"]);
        }

        if ($posted_data['from_date'] != '' && $posted_data['to_date'] != '') {
            $start = date("Y-m-d H:i:s", strtotime($posted_data['from_date']));
            $end = date("Y-m-d H:i:s", strtotime($posted_data['to_date']));
            $query->whereBetween('date', [$start, $end]);

        } else if ($posted_data['from_date']) {
            $start = date("Y-m-d H:i:s", strtotime($posted_data['from_date']));

            $query->where('date', '>=', $start);

        } else if ($posted_data['to_date']) {
            $start = date("Y-m-d H:i:s", strtotime($posted_data['from_date']));
            $end = date("Y-m-d H:i:s", strtotime($posted_data['to_date']));
            $query->where('date', '<=', $end);

        }

        // $mom = $query->orderBy('title', 'asc')->where([
        //             ['mom_status', '=', 0]
        //         ])->with('project', 'user', 'momAttendees', 'momTask', 'momClients')->get();

        if (($page != null && $page != 0) && ($limit != null && $limit != 0)) {
            $mom = $query->orderBy('title', 'asc')->where([
                ['mom_status', '=', 0],
            ])->with('project', 'user', 'momAttendees', 'momTask', 'momClients')->paginate($limit);
        } else {
            $mom = $query->orderBy('title', 'asc')->where([
                ['mom_status', '=', 0],
            ])->with('project', 'user', 'momAttendees', 'momTask', 'momClients')->paginate(50);
        }

        return $this->dispatchResponse(200, "", $mom);
    }
}
