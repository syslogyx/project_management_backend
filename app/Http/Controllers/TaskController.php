<?php

namespace App\Http\Controllers;

use App\Http\Transformers\TaskTransformer;
use App\MailUtility;
use App\Milestone;
use App\Project;
use App\ProjectResource;
// Suvrat Issue#3179
use App\Task;
////////////////////
use App\TechnicalSupport;
use App\User;
use App\Utilities;
use Config;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Input;
use Illuminate\Support\Facades\Mail;
// use Mail;
use PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

// require_once('PHPMailer/PHPMailer/class.phpmailer.php');

class TaskController extends BaseController
{

    //sample mail function which send an email
    public function sendMail()
    {
        // require("/var/www/project_mgmt/vendor/phpmailer/phpmailer/src/PHPMailer.php");
        // require("/var/www/project_mgmt/vendor/phpmailer/phpmailer/src/SMTP.php");

        require base_path() . "/vendor/phpmailer/phpmailer/src/PHPMailer.php";
        require base_path() . "/vendor/phpmailer/phpmailer/src/SMTP.php";

        $mail = new PHPMailer\PHPMailer();
        $mail->IsSMTP(); // enable SMTP

        // $mail->SMTPDebug = 4; // debugging: 1 = errors and messages, 2 = messages only
        $mail->SMTPAuth = true; // authentication enabled
        $mail->SMTPSecure = false; //'tls'; // secure transfer enabled REQUIRED for Gmail
        $mail->Host = "mail.syslogyx.com";
        $mail->Port = 25; // or 587
        $mail->IsHTML(true);
        $mail->Username = "projectmg@syslogyx.com";
        $mail->Password = "J13sui2%";
        $mail->SetFrom("projectmg@syslogyx.com");
        $mail->Subject = "Test";
        $mail->Body = "hello";
        // $mail->AddAddress("kalyani@syslogyx.com");
        $mail->AddAddress("monica.j@syslogyx.com");

        if (!$mail->Send()) {
            echo "Mailer Error: " . $mail->ErrorInfo;
        } else {
            echo "Message has been sent";
        }
    }

    public function index(Request $request)
    {
        $page = $request->page;
        $limit = $request->limit;

        if (($page == null || $limit == null) || ($page == -1 || $limit == -1)) {
            $tasks = Task::orderBy('updated_at', 'asc')->with('projectResource', 'milestones.project', 'status', 'technicalSupport')->paginate(200);
        } else {
            $tasks = Task::orderBy('updated_at', 'asc')->with('projectResource', 'milestones.project', 'status', 'technicalSupport')->paginate($limit);
        }

        if ($tasks->first()) {
            return $this->dispatchResponse(200, "", $tasks);
        } else {
            return $this->dispatchResponse(Config::get('constants.SUCESS_CODE'), "No Records Found!!", null);
        }
    }

    public function create()
    {
        $posted_data = Input::all();
        // $posted_data["created_by"] = 1;
        // $posted_data["updated_by"] = 1;

        $objectTask = new Task();

        $project_id = $posted_data["project_id"];
        unset($posted_data["project_id"]);

        if ($objectTask->validate($posted_data)) {
            try {

                $this->checkIsTaskAssign($posted_data["assigned_to"], $posted_data["estimated_time"], 0);

                DB::beginTransaction();

                $technicalSupportId = 0;
                if (@$posted_data['technical_support']) {
                    $technicalSupportId = $posted_data['technical_support'];
                    unset($posted_data['technical_support']);
                }

                $milestone_id = $posted_data["milestone_id"];

                $project_name = Project::find((int) $project_id);
                $milestone_name = Milestone::find((int) $milestone_id);

                $milestoneURL = "<a href='" . Config::get('constants.WEB_URL_CONSTANTS.MILESTONE_VIEW') . $milestone_name->project_id . "?id=" . $milestone_name->id . "'>" . $milestone_name->title . "</a>";

                //create task
                $posted_data["is_approved"] = 0;
                $model = Task::create($posted_data);
                $user_name = User::find((int) $posted_data["created_by"]);

                $userURL = "<a href='" . Config::get('constants.WEB_URL_CONSTANTS.USER_VIEW') . $user_name->id . "'>" . $user_name->name . "</a>";
                //Kalyani : create activity log
                $msg = sprintf(Config::get('constants.FEED_CONSTANTS_MSGS.TASK_CREATE'), $userURL, "<b>" . $posted_data["title"] . "</b>", $milestoneURL);

                $data = $this->create_task_activity_log($model->id, $project_id, $milestone_id, $msg, $posted_data["created_by"], $posted_data["created_by"]);

                $model->create_status_log = false;

                if ($technicalSupportId != 0) {
                    //add technical support
                    $this->addUpdateTechnicalSupport($technicalSupportId, $model->id, $posted_data["created_by"]);
                }

                DB::commit();
                return $this->dispatchResponse(200, Config::get('constants.SUCCESS_MESSAGES.TASK_CREATED'), $model);
                // return $this->response->item($model, new TaskTransformer())->setStatusCode(200);
            } catch (\Exception $e) {
                DB::rollback();
                throw $e;
            }
        } else {
            throw new \Dingo\Api\Exception\StoreResourceFailedException('Unable to create  task.', $objectTask->errors());
        }
    }

    //Add technical support
    public function addUpdateTechnicalSupport($technicalSupportId, $taskId, $updated_by)
    {
        if ($technicalSupportId != 0) {
            $model = TechnicalSupport::where('task_id', '=', $taskId)->get();

            if ($model->first()) {
                DB::table('technical_supports')
                    ->where('id', $model[0]->id)
                    ->update(array('user_id' => $technicalSupportId));
            } else {
                $postedArray = array();
                $postedArray['task_id'] = $taskId;
                $postedArray['user_id'] = $technicalSupportId;
                $postedArray['created_by'] = $updated_by;
                $postedArray['updated_by'] = $updated_by;
                TechnicalSupport::create($postedArray);
            }
        }
    }

    public function update($id)
    {
        $posted_data = Input::all();

        $model = Task::with('projectResource.user', 'milestones.project')->find((int) $id);
        $model->create_status_log = true;

        if ($model->validate($posted_data)) {
            try {
                DB::beginTransaction();

                if (@$posted_data['technical_support']) {
                    $technicalSupportId = $posted_data['technical_support'];
                    unset($posted_data['technical_support']);

                    //add technical support
                    $this->addUpdateTechnicalSupport($technicalSupportId, $id, $posted_data['updated_by']);
                }

                if (@$posted_data["estimated_time"]) {
                    // $totalEstimationTime = $posted_data["estimated_time"];

                    // if($model->estimated_time < $posted_data["estimated_time"]){
                    //     $totalEstimationTime = $posted_data["estimated_time"] - $model->estimated_time;
                    // }
                    // elseif ($model->estimated_time > $posted_data["estimated_time"]) {
                    //     $totalEstimationTime = $model->estimated_time - $posted_data["estimated_time"];
                    // }

                    //Check can we assign the task to the resource or not
                    $this->checkIsTaskAssign($posted_data["assigned_to"], $posted_data["estimated_time"], $id);
                }

                //calculate spent time on resolved status
                if (@$posted_data['status_id'] && $posted_data['status_id'] == Config::get('constants.STATUS_CONSTANT.RESOLVED')) {
                    $posted_data = $this->calculateSpentTimeAndBreakTime($model, $posted_data);
                    $todayDate = date("Y-m-d H:i:s");
                    $posted_data["stop_date"] = $todayDate;
                }

                //Kalyani : create activity log
                $this->create_task_activity_log_for_update($model, $posted_data, $id);

                //check reason and send mail to the manager
                if (@$posted_data["reason"] && $posted_data["reason"] != null) {
                    $posted_data["status_id"] = Config::get('constants.STATUS_CONSTANT.PENDING_APPROVED');

                    $this->sendMailToResManager($model);
                    //Kalyani : create activity log
                    $this->create_task_activity_log_for_update($model, $posted_data, $id);
                }
                if ($model->update($posted_data)) {
                    DB::commit();
                    // return $this->response->item($model, new TaskTransformer())->setStatusCode(200);
                    return $this->dispatchResponse(200, Config::get('constants.SUCCESS_MESSAGES.TASK_UPDATED'), $model);
                }
            } catch (\Exception $e) {
                DB::rollback();
                throw $e;
            }
        } else {
            throw new \Dingo\Api\Exception\StoreResourceFailedException('Unable to update tasks.', $model->errors());
        }
    }

    /**
     * Kalyani : Send email to the specific project manager if any resource spent more time  than estimated time
     */
    public function sendMailToResManager($model)
    {

        $projectId = $model->milestones->project->id;
        $project_resource = ProjectResource::with('user')
            ->where([
                ['project_id', '=', $projectId],
                ['domain_id', '=', '1'],
                ['active_status', '=', '1'],
            ])->get();

        if ($project_resource->first() && !empty($project_resource)) {

            $managerMailAddress = $project_resource[0]->user->email;

            $subject_name = $model->milestones->project->name . "-" . $model->title;

            $mail_body = "Task : " . $model->title . "</br>" . "Status : " . Config::get('constants.STATUS_CONSTANT.PENDING_APPROVED') . "</br>" . "Estimated Time : " . $model->estimated_time . "</br>" . "Total Spent Time : " . $model->spent_time . "</br>" . "Assigned To : " . $model->projectResource->user->name;

            MailUtility::sendMail($subject_name, $mail_body, array($managerMailAddress));
        }
    }

    public function view($id)
    {
        $task_data = Task::where('id', $id)->first()->toArray();

        $taskUpdatedDate = date('Y-m-d', strtotime($task_data['updated_at']));

        $curr_date = date("Y-m-d");

        if ($taskUpdatedDate != $curr_date) {
            Task::where('id', $id)->update(array('todays_spent_time' => '00:00:00'));
        }
        $model = Task::with('projectResource.user', 'projectResource.project', 'milestones.project', 'status', 'technicalSupport.user', 'eod_assoc')->find((int) $id);
        //get comment list of task
        $commentList = app('App\Http\Controllers\CommentController')->getCommentList(Config::get('constants.COMMENT_IDENTIFIER_CONST.TASK'), $id);

        if ($model) {
            //check comment is empty or not
            if (!$commentList->first()) {
                $commentList = null;
            }
            return $this->dispatchResponse(Config::get('constants.SUCESS_CODE'), Config::get('constants.SUCESS_MSG'), (new TaskTransformer)->transformWithComment($model, $commentList));
        } else {
            return $this->dispatchResponse(Config::get('constants.ERROR_CODE'), Config::get('constants.ERROR_MESSAGES.TASK_NOT_FOUND'), null);
        }
    }

    public function task_by_milestone_id($id)
    {
        $model = Task::where([
            ['milestone_id', '=', $id],
        ])->orderBy('updated_at', 'desc')->get();

        if ($model) {
            return $this->response->item($model, new TaskTransformer())->setStatusCode(200);
        }

    }

    /**
     * Kalyani : Update task status
     */
    public function updateTaskStatusByMiletoneId($milestoneId, $status_id)
    {

        $model = Task::where('milestone_id', '=', $milestoneId)->update(['status_id' => $status_id]);
    }

    /**
     * Kalyani : Update the status of milestone and respective task as well.
     */
    public function update_status($posted_data)
    {

        $id = $posted_data["id"];
        //get milestone by id
        $model = Task::find((int) $id);

        if ($model) {
            $status_id = $model["status_id"];

            if ($status_id == Config::get('constants.STATUS_CONSTANT.CLOSED')) {
                return $this->dispatchResponse(Config::get('constants.SUCESS_CODE'), Config::get('constants.ERROR_MESSAGES.TASK_IS_ALREADY_CLOASED'), null);
            }

            $this->create_task_activity_log_for_update($model, $posted_data, $id);
            $model['status_id'] = $posted_data["status_id"];

            try {
                DB::beginTransaction();
                //update statue
                if ($model->update($posted_data)) {
                    DB::commit();
                    return $this->dispatchResponse(Config::get('constants.SUCESS_CODE'), Config::get('constants.ERROR_MESSAGES.STATUS_UPDATE'), null);
                } else {
                    DB::rollback();
                    throw new \Dingo\Api\Exception\StoreResourceFailedException(Config::get('constants.ERROR_MESSAGES.UNABLE_UPDATE_TASK_API'), $model->errors());
                }
            } catch (\Exception $e) {
                DB::rollback();
                throw $e;
            }
        } else {
            return $this->dispatchResponse(Config::get('constants.SUCESS_CODE'), Config::get('constants.ERROR_MESSAGES.TASK_NOT_FOUND'), null);
        }
    }

    /**
     * Kalyani : Get the list of the task accoring to the filter
     */
    public function getTaskList(Request $request)
    {
        $page = $request->page;
        $limit = $request->limit;

        $posted_data = Input::all();

        $query = Task::query()->select('tasks.*');
        $query = $query->with('milestones.project', 'projectResource.user');
        $query = $this->searchFilterCondition($posted_data, $query);
        // $query = $this->select('tasks.id', 'tasks.title');

        // $query = $query->with('milestones');
        $query = $query->orderBy('tasks.updated_at', 'dec');

        if (($page == null || $limit == null) || ($page == -1 || $limit == -1)) {
            $tasks = $query->paginate(25);
        } else {
            $tasks = $query->paginate($limit);
        }

        if ($tasks !== null) {
            foreach ($tasks as $key => $value) {
                $user_name = User::where("id", $value->created_by)->pluck('name');
                $value["created_by_name"] = $user_name[0];
            }
        }

        if ($tasks->first()) {
            return $this->dispatchResponse(200, "", $tasks);
        } else {
            return $this->dispatchResponse(200, "No Records Found!!", $tasks);
        }
    }

    public function searchFilterCondition($posted_data, $query)
    {
        if (@$posted_data['milestone_id'] && $posted_data['milestone_id'] != 0) {
            $query = $query->where('milestone_id', '=', $posted_data['milestone_id']);
        }

        if (@$posted_data['project_id'] && $posted_data['project_id'] != 0) {
            $condition = [['milestones.project_id', '=', $posted_data['project_id']]];
            $query = $query->leftJoin('milestones', 'tasks.milestone_id', '=', 'milestones.id')
                ->where($condition);

            if (@$posted_data['user_id'] && $posted_data['user_id'] != 0) {
                $resourceId = [];
                // $resourceId=ProjectResource::leftJoin('projects', 'project_resources.project_id', '=' ,'projects.id')
                //     ->where("project_resources.project_id", $posted_data['project_id'])
                //     ->where(function($q) use ($posted_data) {
                //         $q->where('project_resources.user_id',$posted_data['user_id'])
                //         ->orWhere('projects.lead_id', $posted_data['user_id']);
                //     })
                //     ->select('project_resources.id')->get();

                $resourceId = ProjectResource::where("project_id", $posted_data['project_id'])
                    ->where("user_id", "=", $posted_data['user_id'])
                    ->select('project_resources.id')->get();

                // $query ->whereIn('tasks.assigned_to',$resourceId);
                // $query->where('tasks.created_by',$posted_data['user_id']);

                if (@$posted_data['task_cat'] && $posted_data['task_cat'] === "true") {
                    $query->whereIn('tasks.assigned_to', $resourceId);
                }
                if (@$posted_data['task_cat'] && $posted_data['task_cat'] === "false") {
                    $query->where('tasks.created_by', $posted_data['user_id']);
                }

                if (!@$posted_data['task_cat']) {
                    $query->whereIn('tasks.assigned_to', $resourceId);
                }

            }
        }

        if (@$posted_data['status_id'] && $posted_data['status_id'] != null && $posted_data['status_id'] != "") {
            $query = $query->where('tasks.status_id', '=', $posted_data['status_id']);
        }
        return $query;
    }

    /*
     *Kalyani : save activity log
     */
    public function create_task_activity_log($task_id, $project_id, $milestone_id, $msg, $created_by, $updated_by)
    {
        $data = [];
        $data["task_id"] = $task_id;
        $data["project_id"] = $project_id;
        $data["milestone_id"] = $milestone_id;
        $data["message"] = $msg;
        $data["created_by"] = $created_by;
        $data["updated_by"] = $updated_by;

        app('App\Http\Controllers\TaskLogsController')->create($data);
    }

    /*
     *Kalyani : save activity log for update
     */
    public function create_task_activity_log_for_update($actualdata, $requesteddata, $task_id)
    {
        $user = User::find((int) $requesteddata["updated_by"]);
        $user_name = $user->name;

        //Kalyani : create activity log
        $userURL = "<a href='" . Config::get('constants.WEB_URL_CONSTANTS.USER_VIEW') . $user->id . "'>" . $user->name . "</a>";

        // return $actualdata->status_id;
        //check milestone title
        if (@$requesteddata["title"] && $actualdata->title != $requesteddata["title"]) {
            $msg = sprintf(Config::get('constants.FEED_CONSTANTS_MSGS.TASK_TITLE_CHANGED'), $userURL);
            $msg = $msg . $actualdata->title . " to <b>" . $requesteddata["title"] . "</b>";
            $data = $this->create_task_activity_log($task_id, $actualdata->milestones->project->id, $actualdata->milestone_id, $msg, 1, 1);
        }

        // check description
        // if(@$requesteddata["description"] && $actualdata->description != $requesteddata["description"]){
        //     $msg = sprintf(Config::get('constants.FEED_CONSTANTS_MSGS.TASK_DESC_CHANGED'),"<b>".$user_name."</b>");
        //     $msg = $msg.$actualdata->description." to ".$requesteddata["description"];
        //     $data=$this->create_task_activity_log($task_id, $actualdata->milestones->project->id, $actualdata->milestone_id, $msg, 1, 1);
        // }

        // check status
        if (@$requesteddata["status_id"] && ($actualdata->status_id != $requesteddata["status_id"])) {

            if ($requesteddata["status_id"] == Config::get('constants.STATUS_CONSTANT.RESOLVED')) {
                $msg = sprintf(Config::get('constants.FEED_CONSTANTS_MSGS.TASK_RESOLVED_COMMENT'), $userURL, $requesteddata["comment"]);
                // $msg = $msg.$actualdata->status_id." to <b>".$requesteddata["status_id"]."</b>";
                $data = $this->create_task_activity_log($task_id, $actualdata->milestones->project->id, $actualdata->milestone_id, $msg, 1, 1);
            } else {
                if ($actualdata->status_id == Config::get('constants.STATUS_CONSTANT.RESOLVED') && $requesteddata["status_id"] == Config::get('constants.STATUS_CONSTANT.PENDING_APPROVED')) {

                    $msg = sprintf(Config::get('constants.FEED_CONSTANTS_MSGS.TASK_PENDING_APPROVAL_MSG'), $requesteddata["reason"]);

                    $data = $this->create_task_activity_log($task_id, $actualdata->milestones->project->id, $actualdata->milestone_id, $msg, 1, 1);
                } else {
                    $msg = sprintf(Config::get('constants.FEED_CONSTANTS_MSGS.TASK_STATUS_UPDATED'), $userURL);
                    $msg = $msg . $actualdata->status_id . " to <b>" . $requesteddata["status_id"] . "</b>";
                    $data = $this->create_task_activity_log($task_id, $actualdata->milestones->project->id, $actualdata->milestone_id, $msg, 1, 1);
                }
            }

        }

        // check priority
        if (@$requesteddata["priority_id"] && ($actualdata->priority_id != $requesteddata["priority_id"])) {

            if ($actualdata->priority_id != $requesteddata["priority_id"]) {
                $msg = sprintf(Config::get('constants.FEED_CONSTANTS_MSGS.TASK_PRIORITY_UPDATED'), $userURL);

                $msg = $msg . (Config::get('constants.PRIORITY_CONST_NAME.' . $actualdata->priority_id . '') . " to " . Config::get('constants.PRIORITY_CONST_NAME.' . $requesteddata["priority_id"] . ''));

                $data = $this->create_task_activity_log($task_id, $actualdata->milestones->project->id, $actualdata->milestone_id, $msg, 1, 1);
            }
        }

        //check start date
        if (@$requesteddata["start_date"]) {
            // if($actualdata->start_date != null){
            //     // $date[] = explode(" ", $actualdata->start_date);

            //     if($actualdata->start_date != $requesteddata["start_date"]){
            //         $msg = sprintf(Config::get('constants.FEED_CONSTANTS_MSGS.TASK_START_DATE_CHANGE'),$userURL);

            //         $msg = $msg.$actualdata->start_date." from ".$requesteddata["start_date"].".";
            //         $data=$this->create_task_activity_log($task_id, $actualdata->milestones->project->id, $actualdata->milestone_id, $msg, 1, 1);
            //     }
            // }
            // else{
            //     $msg = sprintf(Config::get('constants.FEED_CONSTANTS_MSGS.TASK_START_DATE_CHANGE'),$userURL);
            //     $msg = $msg.$requesteddata["start_date"].".";
            //     $data=$this->create_task_activity_log($task_id, $actualdata->milestones->project->id, $actualdata->milestone_id, $msg, 1, 1);
            // }
        }

        //check completion date
        if (@$requesteddata["completion_date"]) {
            if ($actualdata->completion_date != null) {
                // $date[] = explode(" ", $actualdata->completion_date);

                if ($actualdata->completion_date != $requesteddata["completion_date"]) {
                    $msg = sprintf(Config::get('constants.FEED_CONSTANTS_MSGS.TASK_COMPLETION_CHANGED'), $userURL);
                    $msg = $msg . $actualdata->completion_date . " from " . $requesteddata["completion_date"];

                    $data = $this->create_task_activity_log($task_id, $actualdata->milestones->project->id, $actualdata->milestone_id, $msg, 1, 1);
                }
            } else {
                $msg = sprintf(Config::get('constants.FEED_CONSTANTS_MSGS.TASK_COMPLETION_CHANGED'), $userURL);
                $msg = $msg . $requesteddata["completion_date"];

                $data = $this->create_task_activity_log($task_id, $actualdata->milestones->project->id, $actualdata->milestone_id, $msg, 1, 1);
            }
        }

        // check estimated time
        if (@$requesteddata["estimated_time"]) {
            $msg = sprintf(Config::get('constants.FEED_CONSTANTS_MSGS.TASK_ESTIMATED_TIME'), $userURL);
            if ($actualdata->estimated_time != null) {

                if ($actualdata->estimated_time != $requesteddata["estimated_time"]) {
                    $msg = $msg . $requesteddata["estimated_time"] . " hr from " . $actualdata->estimated_time . " hr.";

                    $data = $this->create_task_activity_log($task_id, $actualdata->milestones->project->id, $actualdata->milestone_id, $msg, 1, 1);
                }
            } else {
                $msg = $msg . $requesteddata["estimated_time"] . " hr.";
                $data = $this->create_task_activity_log($task_id, $actualdata->milestones->project->id, $actualdata->milestone_id, $msg, 1, 1);
            }
        }

        // check spent time
        if (@$requesteddata["spent_time"]) {
            // $msg = sprintf(Config::get('constants.FEED_CONSTANTS_MSGS.TASK_SPENT_TIME'),$userURL);
            // if($actualdata->spent_time != null){
            //     if($actualdata->spent_time != $requesteddata["spent_time"]){
            //         $msg = $msg.$requesteddata["spent_time"]." from ".$actualdata->spent_time;
            //         $data=$this->create_task_activity_log($task_id, $actualdata->milestones->project->id, $actualdata->milestone_id, $msg, 1, 1);
            //     }
            // }
            // else{
            //     $msg = $msg.$requesteddata["spent_time"];
            //     $data=$this->create_task_activity_log($task_id, $actualdata->milestones->project->id, $actualdata->milestone_id, $msg, 1, 1);
            // }
        }

        // check comment
        if (@$requesteddata["comment"] && $requesteddata["status_id"] != Config::get('constants.STATUS_CONSTANT.RESOLVED')) {

            if ($actualdata->comment != null) {
                if ($actualdata->comment != $requesteddata["comment"]) {

                    $msg = sprintf(Config::get('constants.FEED_CONSTANTS_MSGS.TASK_COMMENT_UPDATE'), $userURL);

                    $msg = $msg . $requesteddata["comment"] . " from " . $actualdata->comment . ".";
                    $data = $this->create_task_activity_log($task_id, $actualdata->milestones->project->id, $actualdata->milestone_id, $msg, 1, 1);
                }

            } else {
                $msg = sprintf(Config::get('constants.FEED_CONSTANTS_MSGS.TASK_COMMENT_ADDED'), $userURL, $requesteddata["comment"]);

                $data = $this->create_task_activity_log($task_id, $actualdata->milestones->project->id, $actualdata->milestone_id, $msg, 1, 1);
            }

            // $data=$this->create_task_activity_log($task_id, $requesteddata["project_id"], $requesteddata["milestone_id"], $msg, 1, 1);
        }

        // check assiged to
        if (@$requesteddata["assigned_to"]) {
            if ($actualdata->assigned_to != null) {

                if ($actualdata->assigned_to != $requesteddata["assigned_to"]) {
                    $msg = sprintf(Config::get('constants.FEED_CONSTANTS_MSGS.TASK_ASSIGNED_TO'), $userURL);

                    $project_resource = ProjectResource::find((int) $requesteddata["assigned_to"]);
                    $assignnedURL = "";
                    if ($project_resource) {
                        $user_name = User::find((int) $project_resource["user_id"]);
                        $userName = $user_name->name;

                        //Kalyani : create assigned user url
                        $assignnedURL = "<a href='" . Config::get('constants.WEB_URL_CONSTANTS.USER_VIEW') . $user_name->id . "'>" . $user_name->name . "</a>";
                    }

                    $oldAssignee = "";
                    if ($actualdata->projectResource != null && $actualdata->projectResource->user != null) {
                        $resourceName = $actualdata->projectResource->user->name;

                        //Kalyani : create assigned user url
                        $oldAssignee = "<a href='" . Config::get('constants.WEB_URL_CONSTANTS.USER_VIEW') . $actualdata->projectResource->user->id . "'>" . $resourceName . "</a>";
                    }

                    $msg = sprintf(Config::get('constants.FEED_CONSTANTS_MSGS.TASK_ASSIGNED_UPDATION'), $userURL, $oldAssignee, $assignnedURL);

                    $data = $this->create_task_activity_log($task_id, $actualdata->milestones->project->id, $actualdata->milestone_id, $msg, 1, 1);
                }

            } else {
                $project_resource = ProjectResource::find((int) $requesteddata["assigned_to"]);
                $userName = "";
                $assignnedURL = "";
                if ($project_resource) {
                    $user_name = User::find((int) $project_resource["user_id"]);
                    $userName = $user_name->name;

                    //Kalyani : create assigned user url
                    $assignnedURL = "<a href='" . Config::get('constants.WEB_URL_CONSTANTS.USER_VIEW') . $user_name->id . "'>" . $user_name->name . "</a>";
                }

                // if( $actualdata->project_resource != null && $actualdata->project_resource->user != null){
                //     $resourceName = $actualdata->project_resource->user->name;
                // }

                $msg = sprintf(Config::get('constants.FEED_CONSTANTS_MSGS.TASK_ASSIGNED_CREATION'), $assignnedURL, $userURL);

                $data = $this->create_task_activity_log($task_id, $actualdata->milestones->project->id, $actualdata->milestone_id, $msg, 1, 1);
            }
        }

        // Suvrat Issue#3352    //Line number: 647

        //Check for extension request
        if (@$requesteddata["extension_time"]) {
            $task = Task::find((int) $actualdata["id"]);

            $milestone_id = $task->milestone_id;

            $milestone = Milestone::find((int) $milestone_id);

            $project_id = $milestone->project_id;

            $project = Project::find((int) $project_id);

            $taskURL = "<a href=/" . Config::get('constants.FEED_CONSTANTS.PROJECT') . "/" . Config::get('constants.WEB_URL_CONSTANTS.TASK_VIEW') . "/view/" . $project_id . "?id=" . $task->id . ">" . $task->title . "</a>";

            $msg = sprintf(Config::get('constants.FEED_CONSTANTS_MSGS.TASK_EXTENSION_REQ'), $userURL, $taskURL);

            $this->create_task_activity_log($task_id, $project_id, $milestone_id, $msg, 1, 1);
        }

        if (@$requesteddata["remark"]) {
            $task = Task::find((int) $actualdata["id"]);

            $milestone_id = $task->milestone_id;

            $milestone = Milestone::find((int) $milestone_id);

            $project_id = $milestone->project_id;

            $project = Project::find((int) $project_id);

            $taskURL = "<a href=/" . Config::get('constants.FEED_CONSTANTS.PROJECT') . "/" . Config::get('constants.WEB_URL_CONSTANTS.TASK_VIEW') . "/view/" . $project_id . "?id=" . $task->id . ">" . $task->title . "</a>";

            $msg = sprintf(Config::get('constants.FEED_CONSTANTS_MSGS.TASK_EXTENSION_APPR'), $userURL, $taskURL);

            $this->create_task_activity_log($task_id, $project_id, $milestone_id, $msg, 1, 1);
        }

    }

    /**
     * Monica : Update status of the task
     */
    public function updateStatus()
    {
        $posted_data = Input::all();
        $task_id = $posted_data['id'];
        $status = $posted_data['status_id'];
        $updated_by = $posted_data['updated_by'];

        //get task by id
        $model = Task::with('milestones.project')->find((int) $task_id);

        if ($model) {
            // if($status == Config::get('constants.STATUS_CONSTANT.IN_PROGRESS') ||
            // $status == Config::get('constants.STATUS_CONSTANT.PAUSE')){
            try {
                DB::beginTransaction();
                $todayDate = date("Y-m-d H:i:s");

                $this->create_task_activity_log_for_update($model, $posted_data, $task_id);

                //calculate the total break time of the task
                if ($status == Config::get('constants.STATUS_CONSTANT.PAUSE') || $status == Config::get('constants.STATUS_CONSTANT.HOLD')) {

                    $posted_data['stop_date'] = $todayDate;
                    $posted_data['status_id'] = $status;

                    // $startTime = $model['start_date'];
                    $posted_data = $this->calculateSpentTimeAndBreakTime($model, $posted_data);
                } else if ($status == Config::get('constants.STATUS_CONSTANT.RESOLVED')) {

                    // $stopTime = $model['stop_date'];

                    //check stop time
                    // if($stopTime != null){
                    //     //calculate time difference
                    //     $breakTime = Utilities::calculateTimeDifference($stopTime, $todayDate);

                    //     if($model['break_time'] != null){
                    //         //Sum the time
                    //         $breakTime  = Utilities::sumTheTime($model['break_time'], $breakTime);
                    //     }
                    //     $posted_data['break_time'] = $breakTime;
                    // }

                    $posted_data['stop_date'] = $todayDate;
                    $posted_data['status_id'] = $status;

                    // $startTime = $model['start_date'];
                    $posted_data = $this->calculateSpentTimeAndBreakTime($model, $posted_data);
                } elseif ($status == Config::get('constants.STATUS_CONSTANT.START')) {
                    /*other task started*/
                    $isStarted = $this->checkIfTaskIsStartedOrNot($updated_by);
                    if (!$isStarted) {
                        $posted_data['start_date'] = $todayDate;
                        $posted_data['status_id'] = $status;
                        // $startTime = $model['start_date'];
                        $posted_data = $this->calculateSpentTimeAndBreakTime($model, $posted_data);
                    } else {
                        return $this->dispatchResponse(Config::get('constants.ERROR_CODE_201'), Config::get('constants.ERROR_MESSAGES.ANOTHER_TASK_STARTED'), $isStarted);
                    }
                }

                //update statue
                if ($model->update($posted_data)) {

                    DB::commit();
                    return $this->dispatchResponse(Config::get('constants.SUCESS_CODE'), Config::get('constants.ERROR_MESSAGES.STATUS_UPDATE'), $model);
                }
            } catch (\Exception $e) {
                DB::rollback();
                throw $e;
            }
            // }
        } else {
            return $this->dispatchResponse(Config::get('constants.SUCESS_CODE'), Config::get('constants.ERROR_MESSAGES.TASK_NOT_FOUND'), null);
        }
        // die();
    }

    /**
     * Check is task assign to the given resource or not
     */
    public function checkIsTaskAssign($resourceId, $taskEstimatedTime, $taskId)
    {
        // $posted_data = Input::all();
        // $project_resource_id = $posted_data["id"];
        $model = ProjectResource::find((int) $resourceId);

        if ($model) {
            //get working days between the dates
            $workingDays = Utilities::getWorkdays($model->start_date, $model->due_date);

            //multiple by 8 to get working hours of total working days
            $workingHours = $workingDays * 8;

            //calculate sum of all estimated time of user
            $totalEstimationTime = \DB::table('tasks')
                ->where('assigned_to', '=', $resourceId)
                ->whereNotIn('status_id', [Config::get('constants.STATUS_CONSTANT.CLOSED'),
                    Config::get('constants.STATUS_CONSTANT.DELETED'),
                    Config::get('constants.STATUS_CONSTANT.RESOLVED')]);

            if ($taskId != 0) {
                $totalEstimationTime = $totalEstimationTime->where('id', '<>', $taskId);
            }

            $totalEstimationTime = $totalEstimationTime->sum('estimated_time');

            $totalEstimationTime += $taskEstimatedTime;

            if ($totalEstimationTime > $workingHours) {
                // return $this->dispatchResponse(Config::get('constants.SUCESS_CODE'),Config::get('constants.ERROR_MESSAGES.CAN_NOT_ASSIGN_TASK'), null);
                throw new \Dingo\Api\Exception\StoreResourceFailedException(Config::get('constants.ERROR_MESSAGES.CAN_NOT_ASSIGN_TASK'), null);
            }

        } else {
            // return $this->dispatchResponse(Config::get('constants.ERROR_CODE'),Config::get('constants.ERROR_MESSAGES.INVALID_RESOURCE'), null);
            throw new \Dingo\Api\Exception\StoreResourceFailedException(Config::get('constants.ERROR_MESSAGES.INVALID_RESOURCE'), null);
        }
    }

    /**
     * Calculate total spent time of the task
     */
    public function calculateSpentTime($model, $posted_data)
    {
        $todayDate = date("Y-m-d H:i:s");
        $startTime = $model['start_date'];
        //check start time
        if ($startTime != null) {
            //calculate time difference
            $spentTime = Utilities::calculateTimeDifference($startTime, $todayDate);

            if ($model['spent_time'] != null) {
                //Sum the time
                $spentTime = Utilities::sumTheTime($model['spent_time'], $spentTime);
            }
            $posted_data['spent_time'] = $spentTime;
        }
        $stopTime = $model['stop_date'];

        //check stop time
        if ($stopTime != null) {
            //calculate time difference
            $breakTime = Utilities::calculateTimeDifference($stopTime, $todayDate);

            if ($model['break_time'] != null) {
                //Sum the time
                $breakTime = Utilities::sumTheTime($model['break_time'], $breakTime);
            }
            $posted_data['break_time'] = $breakTime;
        }
        return $posted_data;
    }

    public function calculateSpentTimeAndBreakTime($model, $posted_data)
    {
        $todayDate = date("Y-m-d H:i:s");
        $startTime = $model['start_date'];
        $stopTime = $model['stop_date'];
        //check start time
        if ($startTime != null || $stopTime != null) {
            if ($model['status_id'] == Config::get('constants.STATUS_CONSTANT.PAUSE') || $model['status_id'] == Config::get('constants.STATUS_CONSTANT.HOLD')) {
                if ($stopTime != null) {
                    //calculate time difference
                    $breakTime = Utilities::calculateTimeDifference($stopTime, $todayDate);
                    if ($model['break_time'] != null) {
                        //Sum the time
                        $breakTime = Utilities::sumTheTime($model['break_time'], $breakTime);
                    }
                    $posted_data['break_time'] = $breakTime;
                }
            } else if ($model['status_id'] == Config::get('constants.STATUS_CONSTANT.START') || $model['status_id'] == Config::get('constants.STATUS_CONSTANT.IN_PROGRESS')) {

                if ($startTime != null) {
                    //calculate time difference
                    $spentTime = Utilities::calculateTimeDifference($startTime, $todayDate);
                    $diff = app('App\Http\Controllers\MilestoneController')->daysBetween($startTime, $todayDate);

                    if ($diff == 0) {
                        if ($model['todays_spent_time'] != null) {
                            //Sum the time
                            $TspentTime = Utilities::sumTheTime($model['todays_spent_time'], $spentTime);
                        }
                        $posted_data['todays_spent_time'] = $TspentTime;
                    }

                    if ($model['spent_time'] != null) {
                        //Sum the time
                        $spentTime = Utilities::sumTheTime($model['spent_time'], $spentTime);
                    }
                    $posted_data['spent_time'] = $spentTime;

                }
            }
        }

        return $posted_data;
    }

    public function getTaskInfo(Request $request)
    {
        $requestBody = $request;
        $page = $request->page;
        $limit = $request->limit;
        $type = $request->type;
        $posted_data = Input::all();

        if ($type !== "all") {
            $user_id = $posted_data["user_id"];
        }

        if ($type == "assigned_to_me") {
            $user_ids = DB::table('project_resources')
                ->where("user_id", $user_id)
                ->pluck('id');

            $task_data = Task::orderBy('updated_at', 'desc')
                ->whereIn("assigned_to", $user_ids)
                ->with('projectResource.user', 'milestones.project', 'status', 'technicalSupport')
                ->get();

        } else if ($type == "assigned_by_me") {
            $task_data = Task::orderBy('updated_at', 'desc')
                ->where("created_by", $user_id)
                ->with('projectResource.user', 'milestones.project', 'status', 'technicalSupport')
                ->get();

        } else if ($type == "pending") {
            $task_data = null;
        } else if ($type == "all") {
            $task_data = Task::orderBy('updated_at', 'desc')
                ->with('projectResource.user', 'milestones.project', 'status', 'technicalSupport')
                ->get();
        }

        if ($task_data !== null) {
            foreach ($task_data as $key => $value) {
                $user_name = User::where("id", $value->created_by)->pluck('name');
                $value["created_by_name"] = $user_name[0];
            }
        }

        // if(($page == null|| $limit == null) || ($page == -1 || $limit == -1)){
        //     $data = $query->paginate(200);
        // }
        // else{
        //     $data = $query->paginate($limit);
        // }
        return $this->dispatchResponse(200, "Data.", $task_data);
    }

    public function filterTaskInfo(Request $request)
    {
        // DB::enableQueryLog();
        $page = $request->page;
        $limit = $request->limit;
        // return $limit;

        $posted_data = Input::all();

        // $query = Task::query()->select('tasks.*');   //previous

        //Suvrat Optimize the function
        $query = Task::query()->select('tasks.id', 'tasks.title', 'tasks.spent_time', 'tasks.estimated_time', 'tasks.status_id', 'tasks.updated_at', 'tasks.created_by', 'tasks.milestone_id', 'tasks.assigned_to', 'tasks.extension_time', 'tasks.is_approved');
        ///////////////////////////////
        $query->with('projectResource', 'projectResource.user', 'projectResource.project', 'milestones', 'milestones.project', 'technicalSupport'); //previous
        //Suvrat Optimize the function
        // $query->with('projectResource.project','projectResource.user','milestones');
        ///////////////////////////////

        // select tasks.title as Task_Name,milestones.title as Milestone_Name,projects.name as Project_Name,users.name as Assigned_By,tasks.spent_time as Spent_Time,tasks.estimated_time as Estimated_Time,tasks.status_id as Status,tasks.updated_at as Updated_On from `tasks`inner join milestones on tasks.milestone_id = milestones.id inner join projects on milestones.project_id = projects.id inner join project_resources on tasks.assigned_to = project_resources.id inner join users on project_resources.user_id = users.id where `tasks`.`status_id` != 'Resolved' and `tasks`.`status_id` != 'Approval-Pending' and `tasks`.`status_id` != 'Closed' and exists (select users.name from `project_resources` where `tasks`.`assigned_to` = `project_resources`.`id` and `user_id` = 2) order by tasks.updated_at desc    //optimized query

        if ($posted_data == "" || $posted_data == null) {
            $query->get();
        }

        if ($posted_data["type"]) {
            $type = $posted_data["type"];

            if ($type == "assigned_to_me") {
                if ($posted_data["project_id"]) {
                    $query->whereHas('projectResource', function ($query) use ($posted_data) {
                        $query->where('project_id', $posted_data["project_id"]);
                    })->get();
                }
                if ($posted_data["status_id"]) {
                    $query->where('tasks.status_id', $posted_data["status_id"])->get();
                } else {
                    $query->where('tasks.status_id', '!=', Config::get('constants.STATUS_CONSTANT.RESOLVED'))
                        ->where('tasks.status_id', '!=', Config::get('constants.STATUS_CONSTANT.PENDING_APPROVED'))
                        ->where('tasks.status_id', '!=', Config::get('constants.STATUS_CONSTANT.CLOSED'))->get();
                }
                if ($posted_data["user_id"]) {
                    $query->whereHas('projectResource', function ($query) use ($posted_data) {
                        $query->where('user_id', $posted_data["user_id"]);
                    })->get();
                }
                if ($posted_data["assigned_by"]) {
                    $query->where('tasks.created_by', $posted_data["assigned_by"])->get();
                }
            } else if ($type == "assigned_by_me") {
                if ($posted_data["project_id"]) {
                    $query->whereHas('projectResource', function ($query) use ($posted_data) {
                        $query->where('project_id', $posted_data["project_id"]);
                    })->get();
                }
                if ($posted_data["status_id"]) {
                    $query->where('tasks.status_id', $posted_data["status_id"])->get();
                } else {
                    $query->where('tasks.status_id', '!=', Config::get('constants.STATUS_CONSTANT.RESOLVED'))
                        ->where('tasks.status_id', '!=', Config::get('constants.STATUS_CONSTANT.PENDING_APPROVED'))
                        ->where('tasks.status_id', '!=', Config::get('constants.STATUS_CONSTANT.CLOSED'))->get();
                }
                if ($posted_data["user_id"]) {
                    $query->where('tasks.created_by', $posted_data["user_id"])->get();
                }
            } else if ($type == "pending") {
                $query->where('tasks.extension_time', '!=', null)->where('tasks.is_approved', '!=', true)->get();

                if ($posted_data["user_id"]) {
                    $user = User::find((int) $posted_data["user_id"]);
                    if ($user['hrms_role_id'] == 4) {
                        // $UserIDArrayU = User::where('mentor_id',$posted_data["user_id"])->pluck('id')->toArray();
                        // array_push($UserIDArrayU, (int)$posted_data["user_id"]);

                        //For project wisse lead id
                        // $UserIDArrayP = Project::where('lead_id',$posted_data["user_id"])->pluck('id');
                        // $projectResourceIDs = ProjectResource::whereIn('project_id',$UserIDArrayP)->where('domain_id','!=',1)->distinct('user_id')->pluck('user_id')->toArray();
                        // array_push($UserIDArrayP, (int)$posted_data["user_id"]);

                        // $UserIDArray = array_unique(array_merge($UserIDArrayU, $projectResourceIDs));

                        // if(count($UserIDArray) > 0){
                        // AND (users.mentor_id = 16 OR projects.lead_id = 16)
                        $query->join('project_resources', 'tasks.assigned_to', '=', 'project_resources.id')
                            ->join('projects', 'projects.id', '=', 'project_resources.project_id')
                            ->join('users', 'users.id', '=', 'project_resources.user_id')
                        // ->where('users.mentor_id', '=', $posted_data["user_id"])
                        // ->orWhere('projects.lead_id','=', $posted_data["user_id"] )
                        // ->get();
                            ->where(function ($q) use ($posted_data) {
                                $q->where('users.mentor_id', '=', $posted_data["user_id"])
                                    ->orWhere('projects.lead_id', '=', $posted_data["user_id"]);
                            })
                            ->get();

                        // $query->whereHas('projectResource', function($query) use($UserIDArray) {
                        //     $query->whereIn('user_id', $UserIDArray);
                        // })->get();
                        // }
                    } else if ($user['hrms_role_id'] == 5) {
                        $query->whereHas('projectResource', function ($query) use ($posted_data) {
                            $query->where('user_id', $posted_data["user_id"]);
                        })->get();
                    }
                }
                if ($posted_data["project_id"]) {
                    $query->whereHas('projectResource', function ($query) use ($posted_data) {
                        $query->where('project_id', $posted_data["project_id"]);
                    })->get();
                }
                if ($posted_data["status_id"]) {
                    $query->where('tasks.status_id', $posted_data["status_id"])->get();
                } else {
                    $query->where('tasks.status_id', '!=', Config::get('constants.STATUS_CONSTANT.RESOLVED'))
                        ->where('tasks.status_id', '!=', Config::get('constants.STATUS_CONSTANT.PENDING_APPROVED'))
                        ->where('tasks.status_id', '!=', Config::get('constants.STATUS_CONSTANT.CLOSED'))->get();
                }
                if ($posted_data["assigned_to"]) {
                    $query->whereHas('projectResource', function ($query) use ($posted_data) {
                        $query->where('user_id', $posted_data["assigned_to"]);
                    })->get();
                }
                if ($posted_data["assigned_by"]) {
                    $query->where('tasks.created_by', $posted_data["assigned_by"])->get();
                }
            } else if ($type == "all") {
                if ($posted_data["project_id"]) {
                    $query->whereHas('projectResource', function ($query) use ($posted_data) {
                        $query->where('project_id', $posted_data["project_id"]);
                    })->get();
                }
                if ($posted_data["status_id"]) {
                    $query->where('tasks.status_id', $posted_data["status_id"])->get();
                } else {
                    $query->where('tasks.status_id', '!=', Config::get('constants.STATUS_CONSTANT.RESOLVED'))
                        ->where('tasks.status_id', '!=', Config::get('constants.STATUS_CONSTANT.PENDING_APPROVED'))
                        ->where('tasks.status_id', '!=', Config::get('constants.STATUS_CONSTANT.CLOSED'))->get();
                }
                if ($posted_data["assigned_to"]) {
                    $query->whereHas('projectResource', function ($query) use ($posted_data) {
                        $query->where('user_id', $posted_data["assigned_to"]);
                    })->get();
                }
                if ($posted_data["assigned_by"]) {
                    $query->where('tasks.created_by', $posted_data["assigned_by"])->get();
                }
                if ($posted_data["user_id"]) {

                }
            }
        }

        if ($posted_data["sort_by"]) {
            $sort_by = $posted_data["sort_by"];

            if ($sort_by == "latest_update") {
                $query->orderBy('tasks.updated_at', 'desc');
            }
            if ($sort_by == "pn_a_z") {

                $query->join('project_resources', 'tasks.assigned_to', '=', 'project_resources.id')
                    ->join('projects', 'projects.id', '=', 'project_resources.project_id')
                    ->orderBy('projects.name', 'asc');
                // $query->whereHas('projectResource.project', function($query) use($sort_by) {
                //     $query->orderBy('projects.name', 'asc');
                // })->get();

            }
            if ($sort_by == "pn_z_a") {
                $query->join('project_resources', 'tasks.assigned_to', '=', 'project_resources.id')
                    ->join('projects', 'projects.id', '=', 'project_resources.project_id')
                    ->orderBy('projects.name', 'desc');
                // $query->whereHas('projectResource.project', function($query) use($sort_by) {
                //     $query->orderBy('projects.name', 'desc');
                // })->get();
            }
            if ($sort_by == "created_by_a_z") {
                $query->join('users', 'tasks.created_by', '=', 'users.id')
                    ->orderBy('users.name', 'asc');
            }
            if ($sort_by == "created_by_z_a") {
                $query->join('users', 'tasks.created_by', '=', 'users.id')
                    ->orderBy('users.name', 'desc');
            }
            //Suvrat Issue#3362 Implement proper filtering        //Line number: 1107
            if ($sort_by == "assigned_to_a_z") {
                $query->join('project_resources', 'tasks.assigned_to', '=', 'project_resources.id')
                    ->join('users', 'project_resources.user_id', '=', 'users.id')
                    ->orderBy('users.name', 'asc');
            }
            if ($sort_by == "assigned_to_z_a") {
                $query->join('project_resources', 'tasks.assigned_to', '=', 'project_resources.id')
                    ->join('users', 'project_resources.user_id', '=', 'users.id')
                    ->orderBy('users.name', 'desc');
            }
            //////////////////////
        }
        // print_r(DB::getQueryLog())  ;
        // die();
        if (($page != null && $page != -1) && ($limit != null && $limit != -1)) {
            $task_data = $query->paginate($limit);
        } else {
            $task_data = $query->paginate(20000);
        }

        //for temp use
        // if($posted_data["type"] == "pending"){
        //     $task_data = null;
        // }

        if ($task_data !== null) {
            foreach ($task_data as $key => $value) {
                $user_name = User::where("id", $value->created_by)->pluck('name');
                $value["created_by_name"] = $user_name[0];
            }
        }

        return $this->dispatchResponse(200, "Data.", $task_data);
    }

    public function checkIfTaskIsStartedOrNot($user_id)
    {
        $resourceId = ProjectResource::where("user_id", "=", $user_id)
            ->select('project_resources.id')->get();

        $query = Task::with('milestones.project', 'projectResource.user')
            ->whereIn('tasks.assigned_to', $resourceId)
            ->where('status_id', Config::get('constants.STATUS_CONSTANT.START'))
            ->get();

        if ($query->first()) {
            return $query;
        } else {
            return false;
        }
    }

    public function checkIfTaskIsStartedOrNotAPI($user_id)
    {
        $user_id = User::where("user_id", "=", $user_id)
            ->select('id')->first();
        if ($user_id != "") {
            $user_id = $user_id->id;
            $resourceId = ProjectResource::where("user_id", "=", $user_id)
                ->select('project_resources.id')->get();

            $query = Task::with('milestones.project', 'projectResource.user')
                ->whereIn('tasks.assigned_to', $resourceId)
                ->where('status_id', Config::get('constants.STATUS_CONSTANT.START'))
                ->get();

            if ($query->first()) {
                // return $query;
                return $this->dispatchResponse(200, "Data.", $query);
            } else {
                // return false;
                return $this->dispatchResponse(201, "Data Not Found.", $query);
            }
        } else {
            return $this->dispatchResponse(201, "Invalid User Id.", null);
        }
    }

    public function getCurrentDateTime()
    {
        $todayDate = date("Y-m-d H:i:s");
        return $this->dispatchResponse(200, "Data.", $todayDate);
    }
    /**
     * Sonal : Get Assigned USers List To milestone
     */
    public function getAssignedUserList($milestone_id)
    {
        $projectResourceIDs = Task::where('milestone_id', $milestone_id)->distinct('assigned_to')->pluck('assigned_to');

        $userIDs = ProjectResource::whereIn('id', $projectResourceIDs)->distinct('user_id')->pluck('user_id');

        $users = User::whereIn("id", $userIDs)->get();

        if ($users->first()) {
            return $this->dispatchResponse(200, "Data.", $users);
        } else {
            return $this->dispatchResponse(201, "Data Not Found.", $users);
        }
    }
    /**
     * Sonal : Update approval extension of the task
     */
    public function updateApprovalExtension()
    {
        $posted_data = Input::all();
        $task_id = $posted_data['task_id'];
        $isApprovedFlag = false;
        unset($posted_data['task_id']);

        $model = Task::find((int) $task_id);
        $posted_data['estimated_time'] = $model['estimated_time'];
        if ($model) {
            try {
                DB::beginTransaction();
                if (@$posted_data['remark'] && @$posted_data['is_approved']) {
                    $isApprovedFlag = true;
                    $posted_data['estimated_time'] = $posted_data['estimated_time'] + $model['extension_time'];
                    $posted_data['is_approved'] = $posted_data['is_approved'] != $model['is_approved?'] ? $posted_data['is_approved'] : $model['is_approved?'];
                }

                if ($model->update($posted_data)) {
                    DB::commit();
                    if ($isApprovedFlag == true) {
                        // Suvrat Issue#3352
                        //Create the activity log for approval here
                        $this->create_task_activity_log_for_update($model, $posted_data, $task_id);
                        //////////////////////////////////////////////
                        return $this->dispatchResponse(200, Config::get('constants.SUCCESS_MESSAGES.TASK_EXTENSION_APPROVED'), $model);
                    } else {
                        // Suvrat Issue#3352
                        //Create the activity log for request here
                        $this->create_task_activity_log_for_update($model, $posted_data, $task_id);
                        /////////////////////////
                        return $this->dispatchResponse(200, Config::get('constants.SUCCESS_MESSAGES.TASK_EXTENSION_REQUEST'), $model);
                    }
                }
            } catch (\Exception $e) {
                DB::rollback();
                throw $e;
            }
        } else {
            return $this->dispatchResponse(Config::get('constants.SUCESS_CODE'), Config::get('constants.ERROR_MESSAGES.TASK_NOT_FOUND'), null);
        }
    }
}
