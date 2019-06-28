<?php

namespace App\Http\Transformers;

use League\Fractal\TransformerAbstract;

class TaskCommentLogTransformer extends TransformerAbstract {

    public function transform(\App\TaskCommentLog $table) {
        return [
            'id' => $table->id,
            'task_id' => $table->task_id,
            'comment' => $table->comment,
            'task' => $table->task
        ];
    }
}
