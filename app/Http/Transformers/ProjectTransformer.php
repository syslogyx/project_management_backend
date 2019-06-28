<?php

namespace App\Http\Transformers;

use League\Fractal\TransformerAbstract;
use Illuminate\Support\Facades\DB;

class ProjectTransformer extends TransformerAbstract {

    public function transform(\App\Project $project) {
        $domainInfo = $this->getDomains($project->domain, $project->id);
        return [
            'id' => $project->id,
            'name' => $project->name,
            // 'domain_id' =>  $project->domain_id,
            'client_id' => $project->client_id,
            'user_id' => $project->user_id,
            'lead_id' => $project->lead_id,
            'status_id' => $project->status_id,
            'start_date' => $project->start_date,
            'due_date' => $project->due_date,
            'revised_date' => $project->revised_date,
            'duration_in_days' => $project->duration_in_days,
            'description' => $project->description,
            'current_milestone_index' => $project->current_milestone_index,
            'company_name' => $project->company_name,
            'company_id' => $project->company_id,
            'company_start_date' => $project->company_start_date,
            'company_due_date' => $project->company_due_date,
            'role' => $project->role,
            'type' => $project->type,
            'duration_in_years' => $project->duration_in_years,
            'domain' => $domainInfo,
            'user' => $project->user,
            // 'status' =>  $project->status,
            'client' => $project->client,
            'milestones' => $project->milestones,
            'projectResource' => $project->projectResource,
            // 'technology' => $project->technology,
            'poc' => $project->projectPoc,
            'project_domain_technology' => $project->projectCategoryTechnology
        ];
    }

    function getDomains($domain, $project_id) {
//        print_r($domain);die();
        $domain_id = [];
        foreach ($domain as $key => $value) {
            array_push($domain_id, $value["id"]);
            $val = $value["id"];
            $model = DB::select(DB::raw("SELECT * FROM `technologies` a 
LEFT JOIN `project_category_technology_mapping` b ON a.id = b.technology_id
LEFT JOIN `project_category_mapping` c ON c.id = b.project_category_id
WHERE c.project_id = $project_id AND c.category_id = $val"));
            $domain[$key]["technology"] = $model;
             
        }

//DB::table('technologies')
//                    ->leftJoin('project_category_technology_mapping', 'technologies.id', '=', 'project_category_technology_mapping.technology_id')
//                    ->leftJoin('project_category_mapping', 'project_category_mapping.id', '=', 'project_category_technology_mapping.project_category_id')
//                    ->where('project_category_mapping.project_id', '=', $project_id)
//                    ->whereIn('project_category_mapping.category_id', '=', $value["id"])
//                    ->get();
//        $model = DB::select(DB::raw("SELECT * FROM `technologies` tech 
//LEFT JOIN `project_category_technology_mapping` pcattech ON tech.id = pcattech.technology_id
//LEFT JOIN `project_category_mapping` pcat ON pcat.id = pcattech.project_category_id
//WHERE pcat.project_id = $project_id AND pcat.category_id IN $domain_id"));
       return $domain;
    }

}
