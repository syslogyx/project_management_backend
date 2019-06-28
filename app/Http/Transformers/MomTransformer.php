<?php

namespace App\Http\Transformers;

use League\Fractal\TransformerAbstract;

class MomTransformer extends TransformerAbstract {

    public function transform(\App\Mom $mom) {
        return [
            "id" => $mom->id,
            "user_id" => $mom->user_id,
            "title" => $mom->title,
            "keyword" => $mom->keyword,
            "description" => $mom->description,
            "status" => $mom->status,
            "meeting_venue" => $mom->meeting_venue,
            "date" => $mom->date,
            "start_time" =>$mom->start_time,
            "end_time" => $mom->end_time,
            "tasks" => $mom->momTask,
            "projects" => $mom->project,
            "user" => $mom->user,
            "attendees" => $mom->momAttendees,
            "clients" => $mom->momClients
        ];
    }

}
