<?php

namespace App\Http\Transformers;

use League\Fractal\TransformerAbstract;

class MilestoneTransformer extends TransformerAbstract {

    public function transform(\App\Milestone $milestones) {
        return [
            'id' => $milestones->id,
            'title' => $milestones->title,
            'project_id' => $milestones->project_id,         
            'status_id' =>  $milestones->status_id,
            'milestone_index' =>  $milestones->milestone_index,
            'due_date' =>  $milestones->due_date,
            'start_date' =>  $milestones->start_date,
            'revised_date' =>  $milestones->revised_date,
            'description' =>  $milestones->description,
            'project' =>  $milestones->project,
            'status' =>  $milestones->status,
            'task' => $milestones->task
        ];
    }

    public function transformWithComment(\App\Milestone $milestone, $commentList) {
        return [
            'milestone' => $milestone,
            'comment_list' => $commentList
        ];
    }
}
 