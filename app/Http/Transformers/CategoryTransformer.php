<?php

namespace App\Http\Transformers;

use League\Fractal\TransformerAbstract;

class CategoryTransformer extends TransformerAbstract {

    public function transform(\App\Category $category) {
        return [
            'id' => $category->id,
            'name' => $category->name,    
            'alias' => $category->alias,     
            'technology'  => $category->technology,   
        ];
    }

}
