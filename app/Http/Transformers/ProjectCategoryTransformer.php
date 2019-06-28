<?php

namespace App\Http\Transformers;

use League\Fractal\TransformerAbstract;

class ProjectCategoryTransformer extends TransformerAbstract {

    public function transform(\App\ProjectCategoryMapping $project) {

//        $domain = $this->domainList($project->projectCategoryMapping);
         
        return [
            'id' => $project->id,
            'project_id' => $project->project_id,
            'category_id' => $project->category_id,
            'domain' => $project->category,
            'project' => $project->project,
        ];
    }

//    function domainList($projectCategoryMapping) {
//        $category_id = $projectCategoryMapping->category_id;
//        $model = \App\Category::where([
//                    ['id', '=', $category_id]
//                ])->first();
//        return $model;
//    }

}
