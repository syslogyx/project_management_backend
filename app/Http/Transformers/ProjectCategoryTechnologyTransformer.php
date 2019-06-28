<?php

namespace App\Http\Transformers;

use League\Fractal\TransformerAbstract;

class ProjectCategoryTechnologyTransformer extends TransformerAbstract {

    public function transform(\App\ProjectCategoryTechnology $project) {

        $domain = $this->domainList($project->projectCategoryMapping);
         
        return [
            'id' => $project->id,
            'project_category_id' => $project->project_category_id,
            'technology_id' => $project->technology_id,
            'technology' => $project->technology,
            'domain' => $domain,
        ];
    }

    function domainList($projectCategoryMapping) {
        $category_id = $projectCategoryMapping->category_id;
        $model = \App\Category::where([
                    ['id', '=', $category_id]
                ])->first();
        return $model;
    }

}
