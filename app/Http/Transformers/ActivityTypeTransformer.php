<?php

namespace App\Http\Transformers;

use League\Fractal\TransformerAbstract;

class ActivityTypeTransformer extends TransformerAbstract {

    public function transform(\App\ActivityType $type) {
        return [
            'id' => $type->id,
            'name' => $type->name,            
        ];
    }

}


