<?php

namespace App\Http\Controllers;

use App\Http\Transformers\MilestoneTransformer;
use App\Milestone;
use App\Task;
use App\User;
use Config;
use DateTime;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Input;

class MilestoneController extends BaseController
{

    public function index(Request $request)
    {

        $page = $request->page;
        $limit = $request->limit;
        if (($page == null || $limit == null) || ($page == -1 || $limit == -1)) {
            $milestones = Milestone::orderBy('updated_at', 'desc')->with('project', 'status', 'project.projectResource.user')->paginate(200);
        } else {
            $milestones = Milestone::orderBy('updated_at', 'desc')->with('project', 'status', 'project.projectResource.user')->paginate($limit);
        }

        if ($milestones->first()) {
            return $this->dispatchResponse(200, "", $milestones);
            //            return $this->response->item($milestones, new MilestoneTransformer())->setStatusCode(200);
        } else {
            return $this->dispatchResponse(Config::get('constants.SUCESS_CODE'), "No Records Found!!", null);
        }
    }

    public function create()
    {
        $posted_data = Input::all();
        // $posted_data["created_by"] = 1;
        // $posted_data["updated_by"] = 1;

        $objectMilestone = new Milestone();

        if ($objectMilestone->validate($posted_data)) {
            $project_id = $posted_data["project_id"];
            $milestone_index = $posted_data["milestone_index"];
            $isPresentMilestone = $this->getCurrentMilestone($project_id, $milestone_index);
            if (!$isPresentMilestone) {
                try {
                    DB::beginTransaction();
                    $model = Milestone::create($posted_data);

                    // $project_name = Project::find((int) $posted_data["project_id"]);

                    $user_name = User::find((int) $posted_data["created_by"]);

                    //Kalyani : create activity log
                    $milestoneURL = Config::get('constants.WEB_URL_CONSTANTS.MILESTONE_VIEW') . $model->project_id . "?id=" . $model->id;

                    $userURL = "<a href='" . Config::get('constants.WEB_URL_CONSTANTS.USER_VIEW') . $user_name->id . "'>" . $user_name->name . "</a>";

                    //Kalyani : create activity log
                    $msg = sprintf(Config::get('constants.FEED_CONSTANTS_MSGS.MILESTONE_CREATE'), "<a href='" . $milestoneURL . "'>" . $model->title . "</a>", $userURL);

                    $data = $this->create_milestone_activity_log($model->id, $project_id, $msg, 1, 1);

                    DB::commit();
                    return $this->response->item($model, new MilestoneTransformer())->setStatusCode(200);
                } catch (\Exception $e) {
                    DB::rollback();
                    throw $e;
                }
            } else {
                throw new \Dingo\Api\Exception\StoreResourceFailedException('Unable to create Milestone. Version already exist.', $objectMilestone->errors());
            }
        } else {
            throw new \Dingo\Api\Exception\StoreResourceFailedException('Unable to create Milestone.', $objectMilestone->errors());
        }
    }

    public function update($id)
    {
        $posted_data = Input::all();

        $model = Milestone::find((int) $id);

        if ($model->validate($posted_data)) {
            $project_id = $posted_data["project_id"];
            $milestone_index = $posted_data["milestone_index"];

            $isPresentMilestone = $this->getCurrentMilestone($project_id, $milestone_index);

            if ($isPresentMilestone == null || ($isPresentMilestone->milestone_index == $model["milestone_index"])) {
                try {
                    DB::beginTransaction();
                    //Kalyani : create activity log
                    $this->create_milestone_activity_log_for_update($model, $posted_data, $id);
                    if ($model->update($posted_data)) {
                        DB::commit();
                        return $this->response->item($model, new MilestoneTransformer())->setStatusCode(200);
                    } else {
                        DB::rollback();
                        throw new \Dingo\Api\Exception\StoreResourceFailedException('Unable to update Milestone.', $model->errors());
                    }
                } catch (\Exception $e) {
                    DB::rollback();
                    throw $e;
                }

            } else {
                throw new \Dingo\Api\Exception\StoreResourceFailedException('Unable to create Milestone. Project with this Milsestone Index Already Exist.', $model->errors());
            }
        } else {
            throw new \Dingo\Api\Exception\StoreResourceFailedException('Unable to update Milestone.', $model->errors());
        }
    }

    public function view($id)
    {
        $model = Milestone::find((int) $id);

        //get comment list of task
        $commentList = app('App\Http\Controllers\CommentController')->getCommentList(Config::get('constants.COMMENT_IDENTIFIER_CONST.MILESTONE'), $id);

        if ($model) {
            //check comment is empty or not
            if (!$commentList->first()) {
                $commentList = null;
            }
            return $this->dispatchResponse(Config::get('constants.SUCESS_CODE'), Config::get('constants.SUCESS_MSG'), (new MilestoneTransformer)->transform($model, $commentList));
        } else {
            return $this->dispatchResponse(Config::get('constants.ERROR_CODE'), Config::get('constants.ERROR_MESSAGES.MILESTONE_NOT_FOUND'), null);
        }

        // $email = ['kalyani@syslogyx.com'];

        // $data = [
        //     'mom_title' => '$model->title',
        //     'mom_date' => '$model->date',
        //     'mom_venue' => '$model->meeting_venue'
        // ];

        // config(['mail.username' => 'projectmg@syslogyx.com',
        // 'mail.password' => 'J13sui2%']);

        //send an email
        // Mail::send('email.email_template', $data, function($message) use ($email) {

        //     $message->to($email);
        //     $message->subject('Minutes of Meeting Report');
        //     //            $file_name = public_path() . '/documents/' . $document->project->p_key . '/' . $document->file_name;
        //     //            $message->attach($file_name);
        // });

        // print_r(Mail);
        // die();
        //     if (count(Mail::failures()) > 0) {
        //         print_r('Failed to send email, please try again.');
        //     }
        //     else{
        //         print_r('Mail send Successfully!');
        //     }
        //     die();
    }

    /**
     * Kalyani : Update the status of milestone and respective task as well.
     */
    public function update_status($posted_data)
    {

        $milestone_id = $posted_data["id"];
        //get milestone by id
        $model = Milestone::find((int) $milestone_id);
        if ($model) {
            //Kalyani : create activity log
            $this->create_milestone_activity_log_for_update($model, $posted_data, $milestone_id);

            $status_id = $posted_data["status_id"];
            $model['status_id'] = $status_id;
            $todayDate = date("Y-m-d H:i:s");

            try {
                DB::beginTransaction();

                //update statue
                if ($model->update($posted_data)) {

                    if ($status_id == 'Closed' || $status_id == 'Stop') {
                        //update all task
                        $taskList = app('App\Http\Controllers\TaskController')->updateTaskStatusByMiletoneId($milestone_id, $status_id);
                        $delay = $this->daysBetween($model->due_date, $todayDate);
                        if ($delay >= 0) {
                            $model->delay = $delay;
                        } else {
                            $model->delay = $delay + (-1);
                        }

                        $model->revised_date = $todayDate;
                        $model->save();
                    }
                    DB::commit();
                    return $this->dispatchResponse(Config::get('constants.SUCESS_CODE'), Config::get('constants.ERROR_MESSAGES.STATUS_UPDATE'), null);
                } else {
                    DB::rollback();
                    throw new \Dingo\Api\Exception\StoreResourceFailedException(Config::get('constants.ERROR_MESSAGES.UNABLE_UPDATE_MILESTONE_API'), $model->errors());
                }
            } catch (\Exception $e) {
                DB::rollback();
                throw $e;
            }
        } else {
            return $this->dispatchResponse(Config::get('constants.SUCESS_CODE'), Config::get('constants.ERROR_MESSAGES.MILESTONE_NOT_FOUND'), null);
        }

    }

    public function getCurrentMilestone($projectId, $milestneIndex)
    {
        $model = Milestone::where([
            ['project_id', '=', $projectId],
            ['milestone_index', '=', $milestneIndex],
        ])->first();

        if ($model) {
            return $model;
        }
    }

    public function getCurrentMilestoneIndex($projectId)
    {
        $model = Milestone::where([
            ['project_id', '=', $projectId],
        ])->pluck('milestone_index')->last();

        if ($model) {
            return $model;
        }
    }

    /*
     *Kalyani : save activity log
     */
    public function create_milestone_activity_log($milestone_id, $project_id, $msg, $created_by, $updated_by)
    {
        $data = [];
        $data["milestone_id"] = $milestone_id;
        $data["project_id"] = $project_id;
        $data["message"] = $msg;
        $data["created_by"] = $created_by;
        $data["updated_by"] = $updated_by;

        app('App\Http\Controllers\MilestoneLogsController')->create($data);
    }

    /*
     *Kalyani : save activity log for update
     */
    public function create_milestone_activity_log_for_update($actualdata, $requesteddata, $milestone_id)
    {

        $user = User::find((int) $requesteddata["updated_by"]);
        $user_name = $user->name;
        $userURL = "<a href='" . Config::get('constants.WEB_URL_CONSTANTS.USER_VIEW') . $user->id . "'>" . $user->name . "</a>";

        //Kalyani : create activity log
        $milestoneURL = "<a href='" . Config::get('constants.WEB_URL_CONSTANTS.MILESTONE_VIEW') . $actualdata->project_id . "?id=" . $actualdata->id . "'>" . $actualdata->title . "</a>";

        //check milestone title
        if (@$requesteddata["title"] && $actualdata->title != $requesteddata["title"]) {
            $msg = sprintf(Config::get('constants.FEED_CONSTANTS_MSGS.MILESTONE_TITLE_CHANGED'), $milestoneURL, $userURL);

            $msg = $msg . $actualdata->title . " to <b>" . $requesteddata["title"] . "</b>";
            $data = $this->create_milestone_activity_log($milestone_id, $requesteddata["project_id"], $msg, $requesteddata["created_by"], $requesteddata["updated_by"]);
        }

        //check start date
        if (@$requesteddata["start_date"]) {
            $date[] = explode(" ", $actualdata->start_date);

            $oldDate = new DateTime($date[0][0]);
            $formetedOldDate = $oldDate->format('d-m-Y');
            $newDate = new DateTime($requesteddata["start_date"]);
            $formetedNewDate = $newDate->format('d-m-Y');
            if ($date[0][0] != $requesteddata["start_date"]) {
                $msg = sprintf(Config::get('constants.FEED_CONSTANTS_MSGS.MILESTONE_START_DATE_CHANGE'), $milestoneURL, $userURL);

                $msg = $msg . $formetedOldDate . " to <b>" . $formetedNewDate . "</b>";

                $data = $this->create_milestone_activity_log($milestone_id, $requesteddata["project_id"], $msg, 1, 1);
            }
        }

        //check due date of project
        if (@$requesteddata["due_date"]) {
            // print_r($actualdata->due_date);
            $date1[] = explode(" ", $actualdata->due_date);
            $oldDate = new DateTime($date1[0][0]);
            $formetedOldDate = $oldDate->format('d-m-Y');
            $newDate = new DateTime($requesteddata["due_date"]);
            $formetedNewDate = $newDate->format('d-m-Y');
            if ($date1[0][0] != $requesteddata["due_date"]) {
                $msg = sprintf(Config::get('constants.FEED_CONSTANTS_MSGS.MILESTONE_END_DATE_CHANGED'), $milestoneURL, $userURL);
                $msg = $msg . $formetedOldDate . " to <b>" . $formetedNewDate . "</b>";

                $data = $this->create_milestone_activity_log($milestone_id, $requesteddata["project_id"], $msg, 1, 1);
            }
        }

        // check description
        // if(@$requesteddata["description"] && $actualdata->description != $requesteddata["description"]){
        //     $msg = sprintf(Config::get('constants.FEED_CONSTANTS_MSGS.MILESTONE_DESC_CHANGED'),"<b>".$user_name."</b>");
        //     $msg = $msg.$actualdata->description." to <b>".$requesteddata["description"]."</b>";
        //      $data=$this->create_milestone_activity_log($milestone_id, $requesteddata["project_id"], $msg, 1, 1);
        // }

        // check milestone index
        // if(@$requesteddata["milestone_index"] && $actualdata->milestone_index != $requesteddata["milestone_index"]){
        //     $msg = sprintf(Config::get('constants.FEED_CONSTANTS_MSGS.PROJECT_MANAGER_CHANGED'),"Super Admin");

        //     $msg = $msg.$actualdata->user->name." to ".$newmanager->name;
        //     $data=$this->create_milestone_activity_log($milestone_id, $requesteddata["project_id"], $msg, 1, 1);
        // }

        // check status
        if (@$requesteddata["status_id"] && $actualdata->status_id != $requesteddata["status_id"]) {
            $msg = sprintf(Config::get('constants.FEED_CONSTANTS_MSGS.MILESTONE_STATUS_UPDATED'), $milestoneURL, $userURL);

            $msg = $msg . $actualdata->status_id . " to <b>" . $requesteddata["status_id"] . "</b>";
            $data = $this->create_milestone_activity_log($milestone_id, $requesteddata["project_id"], $msg, 1, 1);
        }
    }

    /**
     * Kalyani : Get the list of the task accoring to the filter
     */
    public function getMilestoneList(Request $request)
    {
        $page = $request->page;
        $limit = $request->limit;
        $currentDate = date('Y-m-d');

        $posted_data = Input::all();

        $cond = DB::raw("(CASE WHEN (status_id = 'Closed') THEN milestones.delay ELSE DATEDIFF('" . $currentDate . "',milestones.due_date) END) AS delay");

        $query = Milestone::query()->with('task', 'project');
        // $query = $query->with('milestone','projectResource.user');
        $query = $this->searchFilterCondition($posted_data, $query);
        $query = $query->orderBy('created_at', 'asc');
        $query->select('milestones.*', $cond);

        if (($page == null || $limit == null) || ($page == -1 || $limit == -1)) {
            $milestone = $query->paginate(200);
        } else {
            $milestone = $query->paginate($limit);
        }

        if ($milestone->first()) {
            return $this->dispatchResponse(200, "", $milestone);
        } else {
            return $this->dispatchResponse(200, "No Records Found!!", $milestone);
        }
    }

    public function searchFilterCondition($posted_data, $query)
    {
        if (@$posted_data['project_id'] && $posted_data['project_id'] != 0) {
            $query = $query->where('project_id', '=', $posted_data['project_id']);
        }

        if (@$posted_data['status_id'] && $posted_data['status_id'] != null && $posted_data['status_id'] != "") {
            $query = $query->where('status_id', '=', $posted_data['status_id']);
        }
        return $query;
    }

    /**
     * Kalyani : check pending task list of user
     */
    public function getPendingTaskList()
    {
        $todayDate = date("Y-m-d");
        $todayDate = $todayDate . ' 00:00:00';

        $milestone = Milestone::where([
            ['due_date', '>=', $todayDate],
            ['status_id', '<>', Config::get('constants.STATUS_CONSTANT.CLOSED')],
        ])->get();

        // print_r($milestone);
        // die();

        if ($milestone->first()) {
            $array = array();
            for ($i = 0; $i < sizeof($milestone); $i++) {
                array_push($array, $milestone[$i]->id);
            }

            //get pending task of milestone
            $task = Task::whereIn('milestone_id', $array)
                ->where('status_id', '=', Config::get('constants.STATUS_CONSTANT.PENDING'))->get();

            if ($task->first()) {
                return $this->dispatchResponse(200, "", $task);
            } else {
                return $this->dispatchResponse(200, "No Records Found!!", $task);
            }

        } else {
            return $this->dispatchResponse(200, "No Records Found!!", $milestone);
        }
    }

    /**
     * Kalyani : Update milestones date in bulk
     */
    public function updateDates()
    {
        $posted_data = Input::all();
        if ($posted_data) {
            // $project_id = $posted_data['project_id'];
            $milestones_data = $posted_data['milestones_data'];

            foreach ($milestones_data as $key => $value) {

                $model = Milestone::find((int) $value["id"]);

                $model->start_date = $value["start_date"];
                $model->due_date = $value["due_date"];
                $model->save();
            }

            if ($model) {
                return $this->dispatchResponse(200, "Records Updated Successfully...!!", null);
            } else {
                throw new \Dingo\Api\Exception\StoreResourceFailedException('Unable to update.', $model->errors());
            }
        }
    }

    /**
     * Number of days between two dates.
     *
     * @param date $dt1    First date
     * @param date $dt2    Second date
     * @return int
     */
    public function daysBetween($dt1, $dt2)
    {
        $dt1 = new DateTime($dt1); // today is 2015-09-02
        $dt2 = new DateTime($dt2); // 20 days ago
        $diff = $dt1->diff($dt2)->format("%r%a");
        return $diff;
        // return date_diff(
        //     date_create($dt2),
        //     date_create($dt1)
        // )->format("%r%a");
    }

}
