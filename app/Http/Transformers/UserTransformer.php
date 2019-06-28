<?php

namespace App\Http\Transformers;

use League\Fractal\TransformerAbstract;
// use App\User;

class UserTransformer extends TransformerAbstract {

    public function transform(\App\User $user) {
        $users_technology = $this->calculateDurationInMonthsAndYears($user->userTechnologyMapping);

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
            'user_id'=> $user->user_id,
            'total_experience'=> $user->total_experience,
            'project' => $user->project,
            'project_resource' => $user->projectResource,
            'technology' => $user->technology,
            'domains' => $user->domain,
            'userTechnologyMapping' => $users_technology,
            'resource_matrix_log' => $user->resourceMatrixLog,
            'mentor_name' => $user->mentorData['name'],
            'manager_name' => $user->managerData['name']
        ];
    }

    // Calculate duration in months from year & month
    function calculateDurationInMonthsAndYears($users_technology) {
        foreach ($users_technology as $key => $value) {
            $month = $users_technology[$key]["duration_in_month"];
//            unset($users_technology[$key]["duration_in_month"]);
            $years = floor($month / 12);
            $months = $month % 12;
            $users_technology[$key]["duration_months"] = $months;
            $users_technology[$key]["duration_years"] = $years;
//            $users_technology[$key]["technology"] = $model = \App\Technology::where([
//                    ['id', '=', $users_technology[$key]["technology_id"]]
//                ])->first();
        }

        return $users_technology;
    }

}
