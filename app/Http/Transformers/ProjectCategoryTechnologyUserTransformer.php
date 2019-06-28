<?php

namespace App\Http\Transformers;

use League\Fractal\TransformerAbstract;

class ProjectCategoryTechnologyUserTransformer extends TransformerAbstract {

    public function transform(\App\ProjectCategoryTechnologyUser $data) {
       
        return [
            'id' => $data->id,
            'project_technology_category_id' => $data->project_technology_category_id,
            'user_id' => $data->user_id,
            'user' => $data->user
        ];
    }

}
