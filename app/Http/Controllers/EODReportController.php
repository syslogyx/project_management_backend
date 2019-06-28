<?php

namespace App\Http\Controllers;

use App\EODMiscellaneousRecords;
use App\EODReport;
use App\EODTaskAssoc;
use App\EODTaskComment;
use App\MeetingBreakLog;
use App\Project;
use App\ProjectResource;
use App\Task;
use App\User;
use App\Utilities;
use Config;
use DB;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Input;

class EODReportController extends BaseController
{
    /**
     * Sonal : Get list of task for EOD Report with meeting, break, Miscellaneous and HRMS time data
     */
    public function getEODReportTaskAndTimingList(Request $request)
    {
        // $user_id = $request->user_id;
        // $date = $request->date;
        // $posted_data = Input::all();

        $userId = 0;
        $date = date("Y-m-d");

        if (@$request->user_id && $request->user_id != 0) {
            $userId = $request->user_id;
        }
        if ($request->date && $request->date != null) {
            $date = $request->date;
        }

        $eod_data = [];

        $userWorkingData = $this->getUserAttendanceDetails($userId, $date);

        if ($userWorkingData) {
            $working_time = $userWorkingData['total_effective_working_duration'] > 0 ? $userWorkingData['total_effective_working_duration'] : 0;
            $eod_data['login_time'] = date('Y-m-d h:i:s', strtotime($userWorkingData['login_time']));
            $eod_data['logout_time'] = date('Y-m-d h:i:s', strtotime($userWorkingData['logout_time']));
            $eod_data['total_hrms_working_time'] = Utilities::convertSectoTimeformat($working_time * 60);
            $eod_data['total_hrms_working_time_with_break'] = Utilities::convertSectoTimeformat($userWorkingData['total_effective_working_duration_withbreak'] * 60);
            $eod_data['break_logs'] = $userWorkingData['break_dtls']['break_logs'];
            $eod_data['meeting_logs'] = $userWorkingData['break_dtls']['meeting_logs'];
        } else {
            $eod_data['login_time'] = '';
            $eod_data['logout_time'] = '';
            $eod_data['total_hrms_working_time'] = '';
            $eod_data['total_hrms_working_time_with_break'] = '';
            $eod_data['break_logs'] = [];
            $eod_data['meeting_logs'] = [];
        }

        $toatalMeetingTimeInMinute = 0;
        if(count($eod_data['meeting_logs']) > 0){
            foreach ($eod_data['meeting_logs'] as $key => $value) {
               $toatalMeetingTimeInMinute = $toatalMeetingTimeInMinute + $value['total_duration'];
            }
        }
        $eod_data['total_meeting_time'] = Utilities::convertSectoTimeformat($toatalMeetingTimeInMinute*60);

        $task_data = $this->getTaskListData($userId, $date);
        $eod_data['tasks'] = $task_data["task"];
        $eod_data['task_total_spend_sec'] = $task_data["task_total_spend_sec"];
        $eod_data['task_total_spend_time'] = Utilities::convertSectoTimeformat($eod_data['task_total_spend_sec']);

        $totalTaskMeetingTime =  $eod_data['task_total_spend_sec'] + $toatalMeetingTimeInMinute*60;
        $eod_data['total_task_meeting_time'] = Utilities::convertSectoTimeformat($totalTaskMeetingTime);

        if ($eod_data['total_hrms_working_time'] != '') {
            $eod_data['miscellaneous_time'] = Utilities::getTimeDifference($eod_data['total_task_meeting_time'], $eod_data['total_hrms_working_time']);
        } else {
            $eod_data['miscellaneous_time'] = '00:00:00';
        }

        $previousEOD = EODReport::with('meeting_break', 'miscellaneous_records', 'eod_task.task.taskComment')->where([['date', 'like', $date . '%'], ['user_id', '=', $userId]])->first();

        if ($previousEOD) {
            $eod_data['miscellaneous_records'] = $previousEOD['miscellaneous_records'];
            $eod_data['total_updated_EOD_mis_time'] = '00:00:00';
            foreach ($previousEOD['miscellaneous_records'] as $key => $value) {
                $eod_data['total_updated_EOD_mis_time'] = Utilities::sumTheTime($eod_data['total_updated_EOD_mis_time'], $value['miscellaneous_time']);
                $eod_data['remaining_updated_EOD_mis_time'] = Utilities::getTimeDifference($eod_data['total_updated_EOD_mis_time'], $eod_data['miscellaneous_time']);
            }
        } else {
            $eod_data['miscellaneous_records'] = [];
            $eod_data['total_updated_EOD_mis_time'] = '00:00:00';
            $eod_data['remaining_updated_EOD_mis_time'] = $eod_data['miscellaneous_time'];
        }

        if ($eod_data) {
            return $this->dispatchResponse(200, "User Working Record.", $eod_data);
        } else {
            return $this->dispatchResponse(201, "No Records Found!!", null);
        }
    }

    public function getTaskListData($userId, $date)
    {
        $query = Task::query()->with('projectResource.user', 'projectResource.project')
            ->where([
                // ['status_id', '=', Config::get('constants.STATUS_CONSTANT.RESOLVED')],
                ['updated_at', 'like', $date . '%'],
            ])
            ->whereIn('status_id', array(Config::get('constants.STATUS_CONSTANT.START'),
                Config::get('constants.STATUS_CONSTANT.IN_PROGRESS'),
                Config::get('constants.STATUS_CONSTANT.STOP'),
                Config::get('constants.STATUS_CONSTANT.PAUSE'),
                Config::get('constants.STATUS_CONSTANT.PENDING_APPROVED'),
                Config::get('constants.STATUS_CONSTANT.PENDING'),
                Config::get('constants.STATUS_CONSTANT.RESOLVED')));

        if ($userId != 0) {
            $projectResoureList = ProjectResource::query()->select('id')->where('user_id', '=', $userId)->get();
            $query = $query->whereIn('assigned_to', $projectResoureList);

            $task_total_spend_sec = $query->sum(DB::raw("TIME_TO_SEC(todays_spent_time)"));
        }
        //get tasks list
        $tasks = $query->get();
        $data = [
            "task" => $tasks,
            "task_total_spend_sec" => $task_total_spend_sec,
        ];
        return $data;
    }

    public function getUserAttendanceDetails($userId, $date)
    {
        $hrmsUserID = User::where('id', $userId)->pluck('user_id')->first();
        $url = Config::get('constants.HRMS_LIVE_URL') . 'users/getUsersAttendanceDetails/' . $hrmsUserID . '/' . $date;
        $data = $this->getOtherSourceResponce($url);
        return $data;
    }
    /**
     * Sonal : Create New EOD report
     */
    public function createEODNew()
    {
        $posted_data = Input::all();
        $taskList = null;

        if (@$posted_data["task_list"]) {
            $taskList = $posted_data["task_list"];
        }

        if ($taskList != null && !empty($taskList)
            && @$posted_data["user_id"] && $posted_data["user_id"] != 0) {
            try {
                DB::beginTransaction();
                $eodArray = array();
                $eodArray['date'] = date("Y-m-d");
                if (@$posted_data["date"]) {
                    $eodArray['date'] = $posted_data["date"];
                }
                $req = new Request();
                $req->user_id = $posted_data["user_id"];
                $req->date = $posted_data["date"];

                $hrmsTodaysData = (array) $this->getEODReportTaskAndTimingList($req);
                $originalhrmsTodaysData = $hrmsTodaysData['original'];

                $eod = $this->checkEODAlreadySent($eodArray['date'], $posted_data["user_id"]);

                if ($eod == null || !$eod->first() || $eod->isEmpty()) {
                    $eodArray['user_id'] = $posted_data["user_id"];
                    $eodArray['status_id'] = Config::get('constants.STATUS_CONSTANT.PENDING');
                    $eodArray['hrms_time'] = "00:00:00";
                    $model = EODReport::create($eodArray);
                    if ($model->first()) {
                        if ($originalhrmsTodaysData['data']['miscellaneous_time'] != '00:00:00') {
                            $this->insertMiscellaneousData($originalhrmsTodaysData['data']['miscellaneous_time'], $posted_data['miscellaneous_reason'], $model->id);
                        }

                        if (count($originalhrmsTodaysData['data']['meeting_logs']) > 0) {
                            $this->insertMeetingData($originalhrmsTodaysData['data']['meeting_logs'], $model->id);
                        }

                        if (count($originalhrmsTodaysData['data']['break_logs']) > 0) {
                            $this->insertBreakData($originalhrmsTodaysData['data']['break_logs'], $model->id);
                        }

                        foreach ($taskList as $key => $value) {
                            $value['eod_id'] = $model->id;
                            // Task::where('id', $value['task_id'])->update(array('todays_spent_time' => '00:00:00'));
                            $taskCommentList = $value['comments'];
                            if ($taskCommentList != null && !empty($taskCommentList)) {
                                foreach ($taskCommentList as $key => $value1) {
                                    $objectTask = array();
                                    $objectTask['task_id'] = $value['task_id'];
                                    $objectTask['eod_id'] = $model->id;
                                    $objectTask['comment'] = $value1;
                                    EODTaskComment::create($objectTask);
                                }
                            }
                            unset($value['comments']);
                            EODTaskAssoc::create($value);
                        }
                        DB::commit();
                        return $this->dispatchResponse(200, Config::get('constants.SUCCESS_MESSAGES.EOD_CREATED'), $model);
                    }
                } else {
                    $eodData = $eod->first();
                    EODReport::where('id', $eodData['id'])->update(
                        array('hrms_time' => "00:00:00"));

                    if ($originalhrmsTodaysData['data']['miscellaneous_time'] != '00:00:00') {
                        $this->insertMiscellaneousData($originalhrmsTodaysData['data']['miscellaneous_time'], $posted_data['miscellaneous_reason'], $eodData['id']);
                    }

                    if (count($originalhrmsTodaysData['data']['meeting_logs']) > 0) {
                        $this->insertMeetingData($originalhrmsTodaysData['data']['meeting_logs'], $eodData['id']);
                    }

                    if (count($originalhrmsTodaysData['data']['break_logs']) > 0) {
                        $this->insertBreakData($originalhrmsTodaysData['data']['break_logs'], $eodData['id']);
                    }

                    foreach ($taskList as $key => $value) {
                        $eodTaskData = EODTaskAssoc::where('eod_id', $eodData['id'])->where('task_id', $value['task_id'])->first();
                        if ($eodTaskData == '' || $eodTaskData == null || empty($eodTaskData)) {
                            $value['eod_id'] = $eodData['id'];
                            Task::where('id', $value['task_id'])->update(array('todays_spent_time' => '00:00:00'));
                            $taskCommentList = $value['comments'];
                            if ($taskCommentList != null && !empty($taskCommentList)) {
                                foreach ($taskCommentList as $key => $value1) {
                                    $objectTask = array();
                                    $objectTask['task_id'] = $value['task_id'];
                                    $objectTask['eod_id'] = $eodData['id'];
                                    $objectTask['comment'] = $value1;
                                    EODTaskComment::create($objectTask);
                                }
                            }
                            unset($value['comments']);
                            EODTaskAssoc::create($value);
                        } else {
                            // Task::where('id', $value['task_id'])->update(array('todays_spent_time' => '00:00:00'));
                            unset($value['comments']);
                            $eodTaskData->update($value);
                        }
                    }
                    DB::commit();
                    return $this->dispatchResponse(200, Config::get('constants.SUCCESS_MESSAGES.EOD_UPDATED'));
                }
            } catch (\Exception $e) {
                DB::rollback();
                throw $e;
            }
        } else {
            try {
                DB::beginTransaction();
                $eodArray = array();
                $eodArray['date'] = date("Y-m-d");
                if (@$posted_data["date"]) {
                    $eodArray['date'] = $posted_data["date"];
                }
                $req = new Request();
                $req->user_id = $posted_data["user_id"];
                $req->date = $posted_data["date"];

                $hrmsTodaysData = (array) $this->getEODReportTaskAndTimingList($req);
                $originalhrmsTodaysData = $hrmsTodaysData['original'];

                $eod = $this->checkEODAlreadySent($eodArray['date'], $posted_data["user_id"]);

                if ($eod == null || !$eod->first() || $eod->isEmpty()) {
                    $eodArray['user_id'] = $posted_data["user_id"];
                    $eodArray['status_id'] = Config::get('constants.STATUS_CONSTANT.PENDING');
                    $eodArray['hrms_time'] = "00:00:00";
                    $model = EODReport::create($eodArray);
                    if ($model->first()) {
                        if ($originalhrmsTodaysData['data']['miscellaneous_time'] != '00:00:00') {
                            $this->insertMiscellaneousData($originalhrmsTodaysData['data']['miscellaneous_time'], $posted_data['miscellaneous_reason'], $model->id);
                        }

                        if (count($originalhrmsTodaysData['data']['meeting_logs']) > 0) {
                            $this->insertMeetingData($originalhrmsTodaysData['data']['meeting_logs'], $model->id);
                        }

                        if (count($originalhrmsTodaysData['data']['break_logs']) > 0) {
                            $this->insertBreakData($originalhrmsTodaysData['data']['break_logs'], $model->id);
                        }
                        DB::commit();
                        return $this->dispatchResponse(200, Config::get('constants.SUCCESS_MESSAGES.EOD_CREATED'), $model);
                    }
                } else {
                    $eodData = $eod->first();
                    EODReport::where('id', $eodData['id'])->update(
                        array('hrms_time' => "00:00:00"));

                    if ($originalhrmsTodaysData['data']['miscellaneous_time'] != '00:00:00') {
                        $this->insertMiscellaneousData($originalhrmsTodaysData['data']['miscellaneous_time'], $posted_data['miscellaneous_reason'], $eodData['id']);
                    }

                    if (count($originalhrmsTodaysData['data']['meeting_logs']) > 0) {
                        $this->insertMeetingData($originalhrmsTodaysData['data']['meeting_logs'], $eodData['id']);
                    }

                    if (count($originalhrmsTodaysData['data']['break_logs']) > 0) {
                        $this->insertBreakData($originalhrmsTodaysData['data']['break_logs'], $eodData['id']);
                    }
                    DB::commit();
                    return $this->dispatchResponse(200, Config::get('constants.SUCCESS_MESSAGES.EOD_UPDATED'));
                }
            } catch (\Exception $e) {
                DB::rollback();
                throw $e;
            }
        }
    }

    public function checkEODAlreadySent($date, $userID)
    {
        $eod = EODReport::with('meeting_break', 'miscellaneous_records', 'eod_task.task.taskComment')->where([['date', 'like', $date . '%'], ['user_id', '=', $userID]])->get();
        return $eod;
    }

    public function insertMiscellaneousData($miscellaneousTime, $miscellaneousReason, $eod_id)
    {
        $row = [];
        $prevMiscellaneousData = EODMiscellaneousRecords::where('eod_id', $eod_id)->get();
        if (count($prevMiscellaneousData) > 0) {
            $total_miscellaneous_time = '00:00:00';
            foreach ($prevMiscellaneousData as $key => &$value) {
                $total_miscellaneous_time = Utilities::sumTheTime($total_miscellaneous_time, $value['miscellaneous_time']);
            }

            if ($total_miscellaneous_time != $miscellaneousTime) {
                $row["eod_id"] = $eod_id;
                $row["miscellaneous_time"] = Utilities::getTimeDifference($total_miscellaneous_time, $miscellaneousTime);
                $row["miscellaneous_reason"] = $miscellaneousReason;
            }
        } else {
            $row["eod_id"] = $eod_id;
            $row["miscellaneous_time"] = $miscellaneousTime;
            $row["miscellaneous_reason"] = $miscellaneousReason;
        }

        if ($row != '' || !empty($row)) {
            EODMiscellaneousRecords::insert($row);
        }
    }

    public function insertMeetingData($meetingData, $eod_id)
    {
        foreach ($meetingData as $key => &$row) {
            $prevMeetingData = MeetingBreakLog::where('activity_type', 'Meeting')->where('hrms_meeting_break_id', $row["id"])->where('eod_id', $eod_id)->first();
            if ($prevMeetingData == '' || $prevMeetingData == null) {
                $row["eod_id"] = $eod_id;
                $row["hrms_meeting_break_id"] = $row['id'];
                $row["activity_type"] = 'Meeting';
                unset($row["id"]);
            } else {
                unset($meetingData[$key]);
            }
        }
        if (count($meetingData) > 0) {
            MeetingBreakLog::insert($meetingData);
        }
    }

    public function insertBreakData($breakData, $eod_id)
    {
        foreach ($breakData as $key => &$row) {
            $prevBreakData = MeetingBreakLog::where('activity_type', 'Break')->where('hrms_meeting_break_id', $row["id"])->where('eod_id', $eod_id)->first();
            if ($prevBreakData == '' || $prevBreakData == null) {
                $row["eod_id"] = $eod_id;
                $row["hrms_meeting_break_id"] = $row['id'];
                $row["activity_type"] = 'Break';
                unset($row["id"], $row["start_ip"], $row["start_devicename"], $row["end_ip"], $row["end_devicename"]);
            } else {
                unset($breakData[$key]);
            }
        }
        if (count($breakData) > 0) {
            MeetingBreakLog::insert($breakData);
        }
    }
    /**
     * Sonal Update EOD report Function for Changing Approval Status of EOD
     */
    public function updateEOD($id)
    {
        $model = EODReport::with('user', 'eod_task.task')->find((int) $id);

        if ($model != null) {
            $posted_data = Input::all();
            $taskList = $posted_data["task_list"];

            if ($taskList != null && !empty($taskList)
                && @$posted_data["user_id"] && $posted_data["user_id"] != 0) {
                try {
                    DB::beginTransaction();

                    unset($posted_data["task_list"]);

                    $model->update($posted_data);

                    if ($model->first()) {
                        foreach ($taskList as $key => $value) {
                            $model1 = EODTaskAssoc::find((int) $value['id']);
                            if ($model1->first()) {
                                $data = [];
                                if (@$value['comments'] || $value['comments'] != "" || $value['comments'] != null) {
                                    $data = [
                                        "task_id" => $value['task_id'],
                                        "eod_id" => $id,
                                        "comments" => $value['comments'],
                                    ];
                                }
                                unset($value['comments']);
                                $model1->update($value);
                                if (!empty($data)) {
                                    $this->addTaskCommentToEOD($data);
                                }

                            }
                        }
                        DB::commit();
                        return $this->dispatchResponse(200, Config::get('constants.SUCCESS_MESSAGES.EOD_UPDATED'), null);
                    }
                } catch (\Exception $e) {
                    DB::rollback();
                    throw $e;
                }
            }
        } else {
            return $this->dispatchResponse(Config::get('constants.ERROR_CODE'), Config::get('constants.ERROR_MESSAGES.EOD_NOT_FOUND'), null);
        }
    }

    public function addTaskCommentToEOD($posted_data)
    {
        try {
            // if ($objectTask->validate($posted_data)) {
            DB::beginTransaction();
            $commentList = $posted_data['comments'];
            if ($commentList != null && !empty($commentList)) {

                // foreach ($commentList as $key => $value) {
                $objectTask = array();
                $objectTask['task_id'] = $posted_data['task_id'];
                $objectTask['eod_id'] = $posted_data['eod_id'];
                $objectTask['comment'] = $posted_data['comments'];

                $model = EODTaskComment::create($objectTask);
                //}

                DB::commit();
                return $this->dispatchResponse(200, Config::get('constants.SUCCESS_MESSAGES.COMMENT_ADDED'), null);
            } else {
                return $this->dispatchResponse(300, 'Empty Comment List.', null);
            }
        } catch (\Exception $e) {
            DB::rollback();
            throw $e;
        }
    }
    /**
     * Sonal : get the EOD Report list
     */
    public function getEODReportList(Request $request)
    {

        $page = $request->page;
        $limit = $request->limit;

        $posted_data = Input::all();

        $userId = 0;
        $leadId = 0;
        $date = null;
        $token = false;

        if (@$posted_data['user_id'] && $posted_data['user_id'] != 0 && $posted_data['user_id'] != null) {
            $userId = $posted_data['user_id'];
        }

        if (@$posted_data['lead_id'] && $posted_data['lead_id'] != 0 && $posted_data['lead_id'] != null) {
            $leadId = $posted_data['lead_id'];
        }

        if (@$posted_data['token'] && $posted_data['token'] == true) {
            $token = $posted_data['token'];
        }

        if (@$posted_data['date'] && $posted_data['date'] != null) {
            $date = $posted_data['date'];
        }

        $finalUserIDsArray = [];

        if ($leadId != 0) {

            $curr_date = $posted_data['date'];
            // $curr_date = date("Y-m-d");

            $query = 'SELECT pr.user_id from projects as p INNER join project_resources as pr on pr.project_id = p.id INNER join tasks as t on t.assigned_to = pr.id WHERE p.lead_id = ' . $leadId . ' AND Cast(t.updated_at AS Date) = "' . $curr_date . '" AND pr.domain_id != 1';

            $user_data = DB::select($query);
            $userListArray = User::where('mentor_id', $leadId)->pluck('id')->toArray();

            // $projectArray=Project::where('lead_id',$leadId)->pluck('id');
            // $projectResourceIDs = ProjectResource::whereIn('project_id',$projectArray)->where('domain_id','!=',1)->distinct('user_id')->pluck('user_id')->toArray();
            $projectResourceIDs = [];
            foreach ($user_data as $key => $value) {
                array_push($projectResourceIDs, $value->user_id);
            }

            $finalUserIDsArray = array_unique(array_merge($userListArray, $projectResourceIDs));
            array_push($finalUserIDsArray, $leadId);
        }

        $query = EODReport::with('user', 'eod_task.task.taskComment', 'eod_task.task.projectResource.user', 'eod_task.task.projectResource.project', 'meeting_break', 'eod_task.task.technicalSupport', 'eod_task.task.technicalSupport.user');

        if ($userId != 0 && $leadId != $userId) {
            $query = $query->where('user_id', '=', $userId);
        }

        if ($token && $leadId == $userId) {
            $query = $query->where('user_id', '=', $userId);
        }

        if ($date != null) {
            $query = $query->where('date', '=', $date);
        }

        if (!empty($finalUserIDsArray)) {
            $query = $query->whereIn('user_id', $finalUserIDsArray);
        }

        if (($page != null && $page != 0) && ($limit != null && $limit != 0)) {
            $eodList = $query->orderBy('date', 'desc')->paginate($limit);
        } else {
            $eodList = $query->orderBy('date', 'desc')->paginate(25);
        }

        if ($eodList->first()) {
            return $this->dispatchResponse(200, "EOD List Found", $eodList);
        } else {
            return $this->dispatchResponse(200, "No Records Found!!", null);
        }
    }
    /**
     * Sonal : View EOD Report
     */
    public function viewEODData($id, $user_id)
    {
        if ($user_id == 'null') {
            $model = EODReport::with('user', 'meeting_break', 'miscellaneous_records', 'eod_task.task.taskComment', 'eod_task.task.projectResource.user', 'eod_task.task.technicalSupport', 'eod_task.task.technicalSupport.user')->find((int) $id);
        } else {
            $eodUserID = EODReport::where('id', $id)->pluck('user_id')->first();
            $mentorID = User::where('id', $eodUserID)->pluck('mentor_id')->first();

            if ($mentorID == $user_id) {
                //display all 6 nodes data
                $model = EODReport::with('user', 'miscellaneous_records', 'eod_task.task.taskComment', 'meeting_break', 'eod_task.task.projectResource.user', 'eod_task.task.technicalSupport', 'eod_task.task.technicalSupport.user')->find((int) $id);
            } else {
                if ($eodUserID == $user_id) {
                    $model = EODReport::with('user', 'miscellaneous_records', 'eod_task.task.taskComment', 'meeting_break', 'eod_task.task.projectResource.user', 'eod_task.task.technicalSupport', 'eod_task.task.technicalSupport.user')->find((int) $id);
                } else {
                    //display only task data
                    $model = EODReport::with(array('eod_task' => function ($que) use ($user_id) {
                        $que->with('task.taskComment', 'task.projectResource.user')->where('lead_id', $user_id)->get();
                    }))->with('user')->find((int) $id);
                }
            }
        }

        if ($model) {
            return $this->dispatchResponse(Config::get('constants.SUCESS_CODE'), Config::get('constants.SUCESS_MSG'), $model);
        } else {
            return $this->dispatchResponse(Config::get('constants.ERROR_CODE'), Config::get('constants.ERROR_MESSAGES.EOD_NOT_FOUND'), null);
        }
    }
    /**
     * Sonal : Get user list under user wise/ project wise lead_id
     */
    public function getUserListUnderLead($user_id)
    {
        $finalUserIDsArray = [];

        $userListArray = User::where('mentor_id', $user_id)->pluck('id')->toArray();

        $projectArray = Project::where('lead_id', $user_id)->pluck('id');

        $projectResourceIDs = ProjectResource::whereIn('project_id', $projectArray)->where('domain_id', '!=', 1)->distinct('user_id')->pluck('user_id')->toArray();

        $finalUserIDsArray = array_unique(array_merge($userListArray, $projectResourceIDs));

        array_push($finalUserIDsArray, $user_id);

        $model = User::whereIn('id', $finalUserIDsArray)->get();

        if ($model->first()) {
            return $this->dispatchResponse(200, "User List", $model);
        } else {
            return $this->dispatchResponse(200, "No Records Found!!", null);
        }
    }
    /**
     * Sonal : Update HRMS time in EOD
     */
    public function updateHRMSTimeInEOD()
    {
        $posted_data = Input::all();

        $todayDate = date("Y-m-d");

        try {
            DB::beginTransaction();

            $userID = User::where('user_id', $posted_data['user_id'])->pluck('id')->first();

            $model = EODReport::where([['user_id', '=', $userID], ['date', '=', $todayDate]])->first();

            $hrms_time = Utilities::calculateTimeDifference($model['updated_at'], $posted_data['current_date_time']);

            if ($model != null) {
                EODReport::where('id', $model['id'])->update(['hrms_time' => $hrms_time]);

                DB::commit();

                return $this->dispatchResponse(200, Config::get('constants.SUCCESS_MESSAGES.EOD_UPDATED'), null);
            } else {
                return $this->dispatchResponse(Config::get('constants.ERROR_CODE'), Config::get('constants.ERROR_MESSAGES.EOD_NOT_FOUND'), null);
            }
        } catch (\Exception $e) {
            DB::rollback();
            throw $e;
        }
    }

    public function checkTodaysEODStatus($user_id)
    {
        $date = date("Y-m-d");
        $userID = User::where('user_id', $user_id)->pluck('id')->first();
        $eod = $this->checkEODAlreadySent($date, $userID);
        if ($eod == null || !$eod->first() || $eod->isEmpty()) {
            return "False";
        } else {
            return "True";
        }
    }

    /**
     * Kalyani : get the EOD Report list
     */
    public function getEODReportListOld(Request $request)
    {
        $page = $request->page;
        $limit = $request->limit;

        $posted_data = Input::all();

        $userId = 0;
        $date = null;

        if (@$posted_data['user_id'] && $posted_data['user_id'] != 0 && $posted_data['user_id'] != null) {
            $userId = $posted_data['user_id'];
        }
        if (@$posted_data['date'] && $posted_data['date'] != null) {
            $date = $posted_data['date'];
        }

        $query = EODReport::with('user', 'eod_task.task');

        if ($userId != 0) {
            $query = $query->where('user_id', '=', $userId);
        }
        if ($date != null) {
            $query = $query->where('date', '=', $date);
        }

        if (($page != null && $page != 0) && ($limit != null && $limit != 0)) {
            $eodList = $query->orderBy('date', 'desc')->paginate($limit);
        } else {
            $eodList = $query->orderBy('date', 'desc')->paginate(25);
        }

        if ($eodList->first()) {
            return $this->dispatchResponse(200, "", $eodList);
        } else {
            return $this->dispatchResponse(200, "No Records Found!!", null);
        }
    }
    /**
     * Kalyani : View EOD Report
     */
    public function viewEOD($id)
    {
        $model = EODReport::with('user', 'eod_task.task.taskComment', 'meeting_break', 'eod_task.task.projectResource.user', 'eod_task.task.technicalSupport', 'eod_task.task.technicalSupport.user')->find((int) $id);

        if ($model) {
            return $this->dispatchResponse(Config::get('constants.SUCESS_CODE'), Config::get('constants.SUCESS_MSG'), $model);
        } else {
            return $this->dispatchResponse(Config::get('constants.ERROR_CODE'), Config::get('constants.ERROR_MESSAGES.EOD_NOT_FOUND'), null);
        }
    }
    /**
     * Kalyani : Add EOD Task Comment
     */
    public function addEODTaskComment()
    {
        $posted_data = Input::all();
        try {
            // if ($objectTask->validate($posted_data)) {
            DB::beginTransaction();

            $commentList = $posted_data['comments'];
            if ($commentList != null && !empty($commentList)) {

                foreach ($commentList as $key => $value) {
                    $objectTask = array();
                    $objectTask['task_id'] = $posted_data['task_id'];
                    $objectTask['eod_id'] = $posted_data['eod_id'];
                    $objectTask['comment'] = $value;

                    $model = EODTaskComment::create($objectTask);
                }

                DB::commit();
                return $this->dispatchResponse(200, Config::get('constants.SUCCESS_MESSAGES.COMMENT_ADDED'), null);
            } else {
                return $this->dispatchResponse(300, 'Empty Comment List.', null);
            }
            // }
            // else{
            //     throw new \Dingo\Api\Exception\StoreResourceFailedException('Unable to add comment.', $objectTask->errors());
            // }
        } catch (\Exception $e) {
            DB::rollback();
            throw $e;
        }
    }
    /**
     * Kalyani : Get the list of the task for EOD Report
     */
    public function getEODReportTaskList(Request $request)
    {
        $posted_data = Input::all();

        $userId = 0;
        $date = date("Y-m-d");

        if (@$posted_data['user_id'] && $posted_data['user_id'] != 0) {
            $userId = $posted_data['user_id'];
        }
        if (@$posted_data['date'] && $posted_data['date'] != null) {
            $date = $posted_data['date'];
        }

        $query = Task::query()->with('projectResource.user', 'projectResource.project')
            ->where([
                // ['status_id', '=', Config::get('constants.STATUS_CONSTANT.RESOLVED')],
                ['updated_at', 'like', $date . '%'],
            ])
            ->whereIn('status_id', array(Config::get('constants.STATUS_CONSTANT.START'),
                Config::get('constants.STATUS_CONSTANT.IN_PROGRESS'),
                Config::get('constants.STATUS_CONSTANT.STOP'),
                Config::get('constants.STATUS_CONSTANT.PAUSE'),
                Config::get('constants.STATUS_CONSTANT.PENDING_APPROVED'),
                Config::get('constants.STATUS_CONSTANT.PENDING'),
                Config::get('constants.STATUS_CONSTANT.RESOLVED')));

        if ($userId != 0) {
            $projectResoureList = ProjectResource::query()->select('id')
                ->where('user_id', '=', $userId)->get();
            $query = $query->whereIn('assigned_to', $projectResoureList);
        }
        //get tasks list
        $tasks = $query->get();

        if ($tasks->first()) {
            return $this->dispatchResponse(200, "", $tasks);
        } else {
            return $this->dispatchResponse(200, "No Records Found!!", null);
        }
    }
    /**
     * Kalyani : Create EOD report
     */
    public function createEOD()
    {
        $posted_data = Input::all();
        $taskList = $posted_data["task_list"];

        if ($taskList != null && !empty($taskList)
            && @$posted_data["user_id"] && $posted_data["user_id"] != 0) {
            try {
                DB::beginTransaction();
                $eodArray = array();
                $eodArray['date'] = date("Y-m-d");
                if (@$posted_data["date"]) {
                    $eodArray['date'] = $posted_data["date"];
                }
                $eod = $this->checkEODAlreadySent($eodArray['date'], $posted_data["user_id"]);

                if ($eod == null || !$eod->first() || $eod->isEmpty()) {

                    $eodArray['user_id'] = $posted_data["user_id"];
                    $eodArray['status_id'] = Config::get('constants.STATUS_CONSTANT.PENDING');

                    $model = EODReport::create($eodArray);
                    if ($model->first()) {
                        foreach ($taskList as $key => $value) {
                            $value['eod_id'] = $model->id;
                            $value['todays_spent_time'] = $value['todays_spent_time'];

                            Task::where('id', $value['task_id'])->update(array('todays_spent_time' => '00:00:00'));

                            $taskCommentList = $value['comments'];

                            if ($taskCommentList != null && !empty($taskCommentList)) {
                                foreach ($taskCommentList as $key => $value1) {
                                    $objectTask = array();
                                    $objectTask['task_id'] = $value['task_id'];
                                    $objectTask['eod_id'] = $model->id;
                                    $objectTask['comment'] = $value1;

                                    EODTaskComment::create($objectTask);
                                }
                            }
                            unset($value['comments']);
                            EODTaskAssoc::create($value);
                        }
                        DB::commit();

                        return $this->dispatchResponse(200, Config::get('constants.SUCCESS_MESSAGES.EOD_CREATED'), $model);
                    }
                } else {
                    return $this->dispatchResponse(200, Config::get('constants.SUCCESS_MESSAGES.EOD_ALREADY_SENT'), null);
                }
            } catch (\Exception $e) {
                DB::rollback();
                throw $e;
            }
        }
    }

}
