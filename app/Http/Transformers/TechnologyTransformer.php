<?php

namespace App\Http\Transformers;

use League\Fractal\TransformerAbstract;

class TechnologyTransformer extends TransformerAbstract {

    public function transform(\App\Technology $technology) {
        return [
            'id' => $technology->id,
            'name' => $technology->name,    
            'alias' => $technology->alias,  
            'parent_id' => $technology->parent_id     
        ];
    }

}
