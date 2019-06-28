<?php

namespace App\Http\Transformers;

use League\Fractal\TransformerAbstract;

class TaskTransformer extends TransformerAbstract {

    public function transform(\App\Task $task) {
        return [
            'id' => $task->id,
            'project_resource_id' => $task->project_resource_id,
            'milestone_id' =>  $task->milestone_id,
            'status_id' =>  $task->status_id,
            'technical_support_id' =>  $task->technical_support_id,
            'completion_date' =>  $task->completion_date,
            'title' =>  $task->title,
            'description' =>  $task->description,
            'parent_id' =>  $task->parent_id,
            'estimated_hours' =>  $task->estimated_hours,
            'comment' =>  $task->comment,
            'original_task_id' => $task->original_task_id,
            'project_resource' => $task->projectResource,
            'milestone' => $task->milestones,
//            'status' => $task->status,
            'technical_support' => $task->technicalSupport,
            'start_date' => $task->start_date,
            'task_list_id' => $task->task_list_id,
            'priority_id' => $task->priority_id
        ];
    }

    public function transformWithComment(\App\Task $task, $commentList) {
        return [
            'task' => $task,
            'comment_list' => $commentList
            
        ];
    }
}

