<?php

namespace App\Http\Transformers;

use League\Fractal\TransformerAbstract;

class ProjectActivityStatusLogTransformer extends TransformerAbstract {

    public function transform(\App\ProjectActivityStatusLog $table) {
        return [
            'id' => $table->id,
            'activity_id' => $table->activity_id,
            'activity_type_id' => $table->activity_type_id,
            'status_id' => $table->status_id,
            'project_resource_id' => $table->project_resource_id,
            'spent_hour' => $table->spent_hour,
            'start_date' => $table->start_date,
            'due_date' => $table->due_date,
            'revised_date' => $table->revised_date,
            'activity_type' => $table->activityType,
            'project_resource' => $table->projectResource,
            'status' => $table->status,
            
        ];
    }
}
