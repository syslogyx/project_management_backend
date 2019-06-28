<?php

namespace App\Http\Controllers;

use App\Client;
use App\EODReport;
use App\EODTaskAssoc;
use App\EODTaskComment;
use App\Http\Transformers\ClientTransformer;
use App\MomClient;
use App\Project;
//////////
use App\Task;
use App\User;
use App\Utilities;
use Config;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Input;

// use GuzzleHttp\RequestOptions;

class ClientController extends BaseController
{

    public function index(Request $request)
    {
        $page = $request->page;
        $limit = $request->limit;
        if (($page == null || $limit == null) || ($page == -1 || $limit == -1)) {
            $clients = Client::orderBy('name', 'asc')->with('project')->paginate(200);
        } else {
            $clients = Client::orderBy('name', 'asc')->with('project')->paginate($limit);
        }

        if ($clients->first()) {
            return $this->dispatchResponse(Config::get('constants.SUCESS_CODE'), "", $clients);
        } else {
            return $this->dispatchResponse(Config::get('constants.SUCESS_CODE'), "No Records Found!!", null);
        }
    }

    public function create()
    {
        $posted_data = Input::all();
        // $posted_data["created_by"] = 1;
        // $posted_data["updated_by"] = 1;
        /* $posted_data["name"] = "test";
        $posted_data["project_id"] = 1; */

        $objectClient = new Client();

        if ($objectClient->validate($posted_data)) {
            $model = Client::create($posted_data);
            return $this->response->item($model, new ClientTransformer())->setStatusCode(200);
        } else {
            throw new \Dingo\Api\Exception\StoreResourceFailedException('Unable to create  Client.', $objectClient->errors());
        }
    }

    public function update($id)
    {
        $posted_data = Input::all();

        $model = Client::find((int) $id);

        if ($model->validate($posted_data)) {
            if ($model->update($posted_data)) {
                return $this->response->item($model, new ClientTransformer())->setStatusCode(200);
            }

        } else {
            throw new \Dingo\Api\Exception\StoreResourceFailedException('Unable to update Client.', $model->errors());
        }
    }

    public function view($id)
    {
        $model = Client::find((int) $id);

        if ($model) {
            return $this->response->item($model, new ClientTransformer())->setStatusCode(200);
        }

//       return $model = Client::find((int) $id);
    }

    public function deleteClient($client_id)
    {
        $clientName = Client::where('id', $client_id)->pluck('name')->first();

        $clientAssignedProjectID = Project::where('client_id', '=', $client_id)->pluck('id');

        $clientAssignedMOMID = MomClient::where('name', '=', $clientName)->pluck('mom_id');

        if (count($clientAssignedProjectID) == 0 && count($clientAssignedMOMID) == 0) {
            $query = Client::where([['id', '=', $client_id]])->delete();
            if ($query) {
                return $this->dispatchResponse(200, "Client deleted Successfully...!!", null);
            }

        } else {
            return $this->dispatchResponse(201, "Client is involved in Project/ MoM.", null);
        }
    }

    public function cronjobEOD(Request $request)
    {
        $url = Config::get('constants.HRMS_LIVE_URL') . 'users/getTodaysLoggedUserIdsList';

        $data = $this->getOtherSourceResponce($url);

        if (count($data) > 0) {
            foreach ($data as $key => $value) {
                $userID = User::where('user_id', $value['user_id'])->pluck('id')->first();
                // $userID = User::where('user_id',$data[1]['user_id'])->pluck('id')->first();

                $userHRMSdata = $this->GetUserHRMSDetails($request, $userID)['data'];

                $eodData = $this->checkTodaysEODSendStatus($userID);
                if (isset($eodData)) {
                    $eodData = $eodData->toArray();

                    $userLoginEodTimeDiff = Utilities::getTimeDifference($userHRMSdata['login_time'], $eodData['updated_at']);

                    $userLoginLogoutTimeDiff = $userHRMSdata['total_hrms_working_time_with_break'];
                    if ($userLoginEodTimeDiff == $userLoginLogoutTimeDiff) {
                        EODReport::where('id', $eodData['id'])->update(
                            array('hrms_time' => '00:00:00'));
                    } else {
                        $hrmsTime = Utilities::getTimeDifference($userLoginEodTimeDiff, $userLoginLogoutTimeDiff);
                        EODReport::where('id', $eodData['id'])->update(
                            array('hrms_time' => $hrmsTime));
                    }

                    // return 'EOD updated and logout';
                } else {
                    //EOD not send
                    if (count($userHRMSdata['tasks']) > 0) {
                        foreach ($userHRMSdata['tasks'] as $key1 => $value1) {
                            if ($value1['status_id'] == Config::get('constants.STATUS_CONSTANT.START')) {
                                //Pause task with time 8PM
                                $this->pauseTaskStatus($value1['id']);
                            }
                        }
                        $this->createSchedularEOD($userHRMSdata, $userID);
                    } else {
                        $this->createSchedularEOD($userHRMSdata, $userID);
                    }

                    $host = gethostname();
                    // $ip = gethostbyname($_SERVER["REMOTE_ADDR"]);
                    $ip = '115.124.122.143';

                    $logoutURL = Config::get('constants.HRMS_LIVE_URL') . 'users/updateUserLogoutTimeFromProjectmgmtScheduler?user_id=' . $value['user_id'] . '&logout_client_ip=' . $ip;

                    $this->getOtherSourceResponce($logoutURL);
                }

            }
        }
        return $this->dispatchResponse(200, Config::get('constants.SUCCESS_MESSAGES.EOD_CREATED'), null);
    }

    public function GetUserHRMSDetails($req, $user_id)
    {
        $date = date("Y-m-d");
        $req = new Request();
        $req->user_id = $user_id;
        $req->date = $date;

        $hrmsTodaysData = (array) app('App\Http\Controllers\EODReportController')->getEODReportTaskAndTimingList($req);

        $originalhrmsTodaysData = $hrmsTodaysData['original'];
        return $originalhrmsTodaysData;
    }

    public function checkTodaysEODSendStatus($user_id)
    {
        $date = date("Y-m-d");
        // $userID = User::where('user_id',$user_id)->pluck('id')->first();
        return $eod = EODReport::with('meeting_break', 'miscellaneous_records', 'eod_task.task.taskComment')->where([['date', 'like', $date . '%'], ['user_id', '=', $user_id]])->first();
    }

    public function pauseTaskStatus($task_id)
    {
        $model = Task::with('milestones.project')->find((int) $task_id);

        $todayDate = date("Y-m-d 20:00:00");

        $status = Config::get('constants.STATUS_CONSTANT.PAUSE');

        $posted_data = [
            "stop_date" => $todayDate,
            "status_id" => $status,
        ];
        // app('App\Http\Controllers\TaskController')->create_task_activity_log_for_update($model, $posted_data, $task_id);
        $posted_data = $this->calculateSpentTimeAndBreakTime($model, $posted_data);
        $model->update($posted_data);
    }

    public function calculateSpentTimeAndBreakTime($model, $posted_data)
    {
        // $todayDate = date("Y-m-d H:i:s");
        $todayDate = date("Y-m-d 20:00:00");
        $startTime = $model['start_date'];
        $stopTime = $model['stop_date'];
        if ($startTime != null) {
            $spentTime = Utilities::calculateTimeDifference($startTime, $todayDate);
            $diff = app('App\Http\Controllers\MilestoneController')->daysBetween($startTime, $todayDate);

            if ($diff == 0) {
                if ($model['todays_spent_time'] != null) {
                    $TspentTime = Utilities::sumTheTime($model['todays_spent_time'], $spentTime);
                }
                $posted_data['todays_spent_time'] = $TspentTime;
            }

            if ($model['spent_time'] != null) {
                $spentTime = Utilities::sumTheTime($model['spent_time'], $spentTime);
            }
            $posted_data['spent_time'] = $spentTime;
        }
        return $posted_data;
    }

    public function createSchedularEOD($userHRMSdata, $userID)
    {
        $eodArray['user_id'] = $userID;
        $eodArray['status_id'] = Config::get('constants.STATUS_CONSTANT.PENDING');
        $eodArray['date'] = date("Y-m-d");
        $eodArray['hrms_time'] = '00:00:00';
        $miscellaneous_reason = 'Not Mension';
        $model = EODReport::create($eodArray);
        if ($model->first()) {
            if ($userHRMSdata['miscellaneous_time'] != '00:00:00') {
                app('App\Http\Controllers\EODReportController')->insertMiscellaneousData($userHRMSdata['miscellaneous_time'], $miscellaneous_reason, $model->id);
            }

            if (count($userHRMSdata['meeting_logs']) > 0) {
                app('App\Http\Controllers\EODReportController')->insertMeetingData($userHRMSdata['meeting_logs'], $model->id);
            }

            if (count($userHRMSdata['break_logs']) > 0) {
                app('App\Http\Controllers\EODReportController')->insertBreakData($userHRMSdata['break_logs'], $model->id);
            }

            if (count($userHRMSdata['tasks']) > 0) {
                foreach ($userHRMSdata['tasks'] as $key => $value) {

                    $eodTaskData['eod_id'] = $model->id;
                    $eodTaskData['task_id'] = $value->id;
                    $eodTaskData['status_id'] = $value->status_id;
                    $eodTaskData['todays_spent_time'] = $value->todays_spent_time;
                    $eodTaskData['todays_total_spent_time'] = $value->spent_time;
                    $eodTaskData['lead_id'] = $value->projectResource->project->lead_id;
                    // Task::where('id', $value['task_id'])->update(array('todays_spent_time' => '00:00:00'));
                    $taskCommentList = $value->task_comment;
                    if ($taskCommentList != null && !empty($taskCommentList)) {
                        foreach ($taskCommentList as $key => $value1) {
                            $objectTask = array();
                            $objectTask['task_id'] = $eodTaskData['task_id'];
                            $objectTask['eod_id'] = $eodTaskData['eod_id'];
                            $objectTask['comment'] = $value1;
                            EODTaskComment::create($objectTask);
                        }
                    }

                    EODTaskAssoc::create($eodTaskData);
                }
            } else {
                return $loginTime = explode(" ", $userHRMSdata['login_time'])[1];
                // return $userHRMSdata['login_time'];
                $todayDateLogoutTime = '20:00:00';
                $miscellaneous_reason1 = 'Not Mension';
                $missTime = Utilities::getTimeDifference($loginTime, $todayDateLogoutTime);
                app('App\Http\Controllers\EODReportController')->insertMiscellaneousData($missTime, $miscellaneous_reason1, $model->id);
            }
        }
    }

}
