<?php

namespace App\Http\Transformers;

use League\Fractal\TransformerAbstract;

class UserFilterTransformer extends TransformerAbstract {

    public function transform(\App\User $user) {
//        $users_technology = $this->calculateDurationInMonthsAndYears($user->userTechnologyMapping);
        $project_data = $this->projectArray($user, $user->projectResource);
        $technology_data = $this->technologyArray($user->technology);
//        $user_data = $this->userArray($user);
        return [
            'id' => $user->id,
            'name' => $user->name,
            'email' => $user->email,
            'gender' => $user->gender,
            'status' => $user->status,
            'email_internal' => $user->email_internal,
            'email_external' => $user->email_external,
            'department' => $user->department,
            'designation' => $user->designation,
            'avatar' => $user->avatar,
            'project' => $project_data,
            'technology' => $user->technology,
            'user_id' => $user->user_id,
        ];
    }

    // Calculate duration in months from year & month
    function calculateDurationInMonthsAndYears($users_technology) {
        $month = $users_technology["duration_in_month"];
        unset($users_technology["duration_in_month"]);
        $years = floor($month / 12);
        $months = $month % 12;
        $users_technology["duration_months"] = $months;
        $users_technology["duration_years"] = $years;
//            $users_technology[$key]["technology"] = $model = \App\Technology::where([
//                    ['id', '=', $users_technology[$key]["technology_id"]]
//                ])->first();


        return $users_technology;
    }

    function technologyArray($technology) {

        $technology_data = $technology;
        foreach ($technology as $key => $value) {

            if ($technology_data[$key]["parent_id"] != null) {
                $parent_name = \App\Technology::select('name')
                        ->where([
                            ['id', '=', $technology[$key]["parent_id"]]
                        ])
                        ->first();
                $technology_data[$key]["parent_name"] = $parent_name["name"];
            }
            $duration = \App\UserTechnologyMapping::select('duration')
                            ->where([
                                ['technology_id', '=', $technology[$key]["id"]],
                                ['user_id', '=', $technology[$key]["pivot"]["user_id"]]
                            ])->first();
//            $duration = $this->calculateDurationInMonthsAndYears($duration);
//            $technology_data[$key]["duration_months"] = $duration["duration_months"];
//            $technology_data[$key]["duration_years"] = $duration["duration_years"];
            $technology_data[$key]["duration"] = $duration["duration"];

            unset($technology_data[$key]["created_by"]);
            unset($technology_data[$key]["updated_by"]);
            unset($technology_data[$key]["created_at"]);
            unset($technology_data[$key]["updated_at"]);
            unset($technology_data[$key]["pivot"]);
        }
    }

    function projectArray($user, $project_resource) {

        $project_data = [];
//        foreach ($project_resource as $key => $value) {
//            $project_data1 = \App\Project::where([
//                        ['id', '=', $project_resource[$key]["project_id"]],
////                        ['user_id', '=', $user["id"]],
//                        ['type', '=', 2]
//                    ])
//                    ->first();
//
//            if (($project_data1)) {
//                if (count($project_data) > 0) {
//                    $count = 0;
//                    foreach ($project_data as $k => $v) {
//                        if ($project_data[$k]["id"] !== $project_data1["id"]) {
//                            $project_data1["user_id"] = $user["id"];
//                            $count = 1;                            
//                        }
//                    }
//                    if($count >0){
//                        array_push($project_data, $project_data1);
//                    }
//                } else {
//                    $project_data1["user_id"] = $user["id"];
//                    array_push($project_data, $project_data1);
//                }
//            }
//            //
//        }

        foreach ($project_resource as $key => $value) {

            $project_data1 = \App\Project::where([
                        ['id', '=', $project_resource[$key]["project_id"]],
                        ['type', '=', 2]
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

//            $domain = \App\ProjectResource::select('domain_id')
//                    ->where([
//                        ['project_id', '=', $project_data[$key]["id"]],
//                        ['user_id', '=', $project_data[$key]["user_id"]]
//                    ])
//                    ->get();

            $domains = \App\Category::where('id', $project_resource[$key]["domain_id"])->get();
//            $domains = \App\ProjectCategoryMapping::where([
//                    ['project_id', '=', $project_data[$key]["id"]]
//                    ])->whereIn('category_id',$domain)->with("domain")->get();
//            
            $project_data[$key]["domain"] = $domains;
            unset($project_data[$key]["domain_id"]);

//            $status = \App\Status::select('id', 'name')
//                    ->where([
//                        ['id', '=', $project_data[$key]["status_id"]]
//                    ])
//                    ->first();
//            $project_data[$key]["status"] = $status;
//            unset($project_data[$key]["status_id"]);
//            $client = \App\Client::select('id', 'name')
//                    ->where([
//                        ['id', '=', $project_data[$key]["client_id"]]
//                    ])
//                    ->first();
//            $project_data[$key]["client"] = $client;
//            unset($project_data[$key]["client_id"]);

            $work_end_date = \App\ProjectResource::select('due_date')
                    ->where([
                        ['project_id', '=', $project_resource[$key]["project_id"]],
                        ['user_id', '=', $project_resource[$key]["user_id"]],
                        ['domain_id', '=', $project_resource[$key]["domain_id"]],
                        ['active_status', '=', $project_resource[$key]["active_status"]]
                    ])
                    ->first();

            $project_data[$key]["work_end_date"] = $work_end_date["due_date"];
            $project_data[$key]["active_status"] = $project_resource[$key]["active_status"];

            $time_allocation_log = \App\ResourceMatrixLog::where([
                        ['project_id', '=', $project_resource[$key]["project_id"]]
                    ])
                    ->get();

            foreach ($time_allocation_log as $key1 => $value1) {
                $created_by_name = \App\User::select('name')
                        ->where([
                            ['id', '=', $time_allocation_log[$key1]["created_by"]]
                        ])
                        ->first();

                $time_allocation_log[$key1]["created_by_name"] = $created_by_name["name"];
//                unset($time_allocation_log["updated_by"]);
            }
            $project_data[$key]["time_allocation_log"] = $time_allocation_log;
        }

        return $project_data;
    }

    function userArray($user) {
        $user_data = [];
        foreach ($user->projectResource as $key => $value) {
            $user_data1 = \App\User::where([
                        ['id', '=', $user->projectResource[$key]["user_id"]]
                    ])
                    ->first();
            array_push($user_data, $user_data1);
        }
    }

}
