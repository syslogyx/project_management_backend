<?php

namespace App\Http\Transformers;

use League\Fractal\TransformerAbstract;

class TechnicalSupportTransformer extends TransformerAbstract {

    public function transform(\App\TechnicalSupport $table) {
        return [
            'id' => $table->id,
            'user_id' => $table->user_id,
            'task_id' => $table->task_id,
            'description' => $table->description,
            'task' => $table->task,
            'user' => $table->user,  
        ];
    }
}
