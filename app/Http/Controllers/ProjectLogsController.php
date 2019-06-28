<?php

namespace App\Http\Controllers;

use App\ProjectLogs;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Input;
use \DB;

class ProjectLogsController extends BaseController
{
    public function create($requestedData)
    {
        // print_r($requestedData);
        // die();
        $model = ProjectLogs::create($requestedData);
        // $feed_id = $model->id;

    }

    /**
     * Kalyani : get logs list
     */
    public function getLogsList(Request $request)
    {
        $page = $request->page;
        $limit = $request->limit;

        $posted_data = Input::all();

        $project_id = 0;
        $milestone_id = 0;
        $task_id = 0;
        $start_date = null;
        $due_date = null;

        if (@$posted_data['project_id']) {
            $project_id = $posted_data['project_id'];
        }

        if (@$posted_data['milestone_id']) {
            $milestone_id = $posted_data['milestone_id'];
        }

        if (@$posted_data['task_id']) {
            $task_id = $posted_data['task_id'];
        }

        if (@$posted_data['start_date']) {
            $start_date = $posted_data['start_date'] . " 00:00:00";
        }

        if (@$posted_data['due_date'])
        // $due_date = $posted_data['due_date']." 23:59:59";
        {
            $due_date = $posted_data['due_date'] . " 00:00:00";
        }

        //check condition and accordingly fetch list
        if ($project_id != 0 && $milestone_id != 0 && $task_id != 0) {
            $query = $this->getTaskLogs($task_id, $milestone_id, $project_id, $start_date, $due_date);

            //Check field name
            if (@$posted_data['field_name'] && $posted_data['field_name'] != null && $posted_data['field_name'] != "") {

                $query = $query->where('message', 'LIKE', '%' . $posted_data['field_name'] . '%');
            }

        } elseif ($project_id != 0 && $milestone_id != 0) {

            // $taskLogs = $this->getTaskLogs(0, $milestone_id, 0, $start_date, $due_date);

            $milestoneLogs = $this->getMilestoneLogs($milestone_id, $project_id, $start_date, $due_date);

            // $query = $this->getProjectLogs($project_id, $start_date, $due_date);

            // $query = $query->union($milestoneLogs);
            $query = $milestoneLogs;
            // ->union($taskLogs);

        } else if ($project_id != 0) {

            $milestoneLogs = $this->getMilestoneLogs(0, $project_id, $start_date, $due_date);

            $query = $this->getProjectLogs($project_id, $start_date, $due_date);

            $query = $query->union($milestoneLogs);

        } else {
            return $this->dispatchResponse(300, "Please select project.", null);
        }

        //add pagination
        if (($page == null || $limit == null) || ($page == -1 || $limit == -1)) {
            $tasks = $query->get();
        } else {
            $tasks = $query->get();
        }

        if ($tasks->first()) {
            return $this->dispatchResponse(200, "", $tasks);
        } else {
            return $this->dispatchResponse(200, "No Records Found!!", $tasks);
        }
    }

    public function searchFilterCondition($posted_data, $query)
    {
        if (@$posted_data['project_id'] && $posted_data['project_id'] != 0) {
            $query = $query->where('milestone_id', '=', $posted_data['milestone_id']);
        }

        if (@$posted_data['status_id'] && $posted_data['status_id'] != null && $posted_data['status_id'] != "") {
            $query = $query->where('status_id', '=', $posted_data['status_id']);
        }
        return $query;
    }

    /**
     * Kalyani : Make query to get the task logs
     */
    public function getTaskLogs($task_id, $milestone_id, $project_id, $start_date, $due_date)
    {
        $query = \DB::table('task_logs')
            ->select('task_logs.message', 'task_logs.created_at', 'task_logs.updated_at');
        if ($project_id != 0) {
            $query = $query->where('project_id', "=", $project_id);
        }

        if ($milestone_id != 0) {
            $query = $query->where('milestone_id', "=", $milestone_id);
        }

        if ($task_id != 0) {
            $query = $query->where('task_id', "=", $task_id);
        }

        //Add condition for start date and due date
        if ($start_date != null && $start_date != "" && $due_date != null && $due_date != "") {

            $query = $query->where('created_at', '>=', $start_date)
                ->where('updated_at', '<=', $due_date);
        }

        return $query;
    }

    /**
     * Kalyani : Make query to get the milestone logs
     */
    public function getMilestoneLogs($milestone_id, $project_id, $start_date, $due_date)
    {
        $query = \DB::table('milestone_logs')
            ->select('milestone_logs.message', 'milestone_logs.created_at', 'milestone_logs.updated_at');

        if ($project_id != 0) {
            $query = $query->where('project_id', "=", $project_id);
        }

        if ($milestone_id != 0) {
            $query = $query->where('milestone_id', "=", $milestone_id);
        }

        //Add condition for start date and due date
        if ($start_date != null && $start_date != "" && $due_date != null && $due_date != "") {

            $query = $query->where('created_at', '>=', $start_date)
                ->where('updated_at', '<=', $due_date);
        }

        return $query;
    }

    /**
     * Kalyani : Make query to get the project logs
     */
    public function getProjectLogs($project_id, $start_date, $due_date)
    {
        $query = \DB::table('project_logs')
            ->select('project_logs.message', 'project_logs.created_at', 'project_logs.updated_at');

        if ($project_id != 0) {
            $query = $query->where('project_id', "=", $project_id);
        }

        //Add condition for start date and due date
        if ($start_date != null && $start_date != "" && $due_date != null && $due_date != "") {

            $query = $query->where('created_at', '>=', $start_date)
                ->where('updated_at', '<=', $due_date);
        }

        return $query;
    }
}
