<?php

namespace App\Http\Transformers;

use League\Fractal\TransformerAbstract;

class ResourceMatrixLogTransformer extends TransformerAbstract {

    public function transform(\App\ResourceMatrixLog $data) {
        return [
            'id' => $data->id,
            'project_id' => $data->project_id,
            'user_id' =>  $data->user_id,
            'remark' =>  $data->remark,
            'start_date' =>  $data->start_date,
            'due_date' =>  $data->due_date,  
            'project' =>  $data->project, 
            'user' =>  $data->user,   
        ];
    }
}
