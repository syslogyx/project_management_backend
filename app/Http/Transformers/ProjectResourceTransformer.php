<?php

namespace App\Http\Transformers;

use League\Fractal\TransformerAbstract;

class ProjectResourceTransformer extends TransformerAbstract {

    public function transform(\App\ProjectResource $projecResouce) {
        $project_resc_tech = $this->getResourceTechnology($projecResouce->project_resource_technology);
        
        return [
            'id' => $projecResouce->id,
            'project_id' => $projecResouce->project_id,
            'user_id' => $projecResouce->user_id,
            'domain_id' => $projecResouce->domain_id,
            'status_id' => $projecResouce->status_id,
            'role' => $projecResouce->role,
            'start_date' => $projecResouce->start_date,
            'due_date' => $projecResouce->due_date,
            'project' => $projecResouce->project,
            'domain' => $projecResouce->domain,
            'user' => $projecResouce->user,
            'project_resource_technology' => $project_resc_tech
        ];
    }

    function getResourceTechnology($project_resource_technology) {
        $len1 = count($project_resource_technology);
        for ($j = 0; $j < $len1; $j++) {
            $tech = \App\Technology::where("id", "=", $project_resource_technology[$j]["technology_id"])->get();

//                print_r($tech);
            $project_resource_technology[$j]["technology"] = [
                "id" => $tech[0]["id"],
                "name" => $tech[0]["name"]
            ];
        }
        return $project_resource_technology;
    }

}
