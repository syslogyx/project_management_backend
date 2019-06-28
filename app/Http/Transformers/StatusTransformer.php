<?php

namespace App\Http\Transformers;

use League\Fractal\TransformerAbstract;

class StatusTransformer extends TransformerAbstract {

    public function transform(\App\Status $status) {
        return [
            'id' => $status->id,
            'activity_type_id' => $status->activity_type_id,
            'name' => $status->name,
            'activity_type' =>$status->activityType
        ];
    }
}
