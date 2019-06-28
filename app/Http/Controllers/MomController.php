<?php

namespace App\Http\Controllers;

use App\Http\Transformers\MomTransformer;
use App\Mom;
use App\MomAttendees;
use App\MomClient;
use App\MomProject;
use App\MomTasks;
use Config;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Input;
use Illuminate\Support\Facades\Mail;

class MomController extends BaseController
{

    public function index(Request $request)
    {
        $page = $request->page;
        $limit = $request->limit;
        if (($page == null || $limit == null) || ($page == -1 || $limit == -1)) {
            $mom = Mom::orderBy('date', 'desc')->where([
                ['mom_status', '=', 0],
            ])->with('project', 'user')->paginate(200);
        } else {
            $mom = Mom::orderBy('date', 'desc')->where([
                ['mom_status', '=', 0],
            ])->with('project', 'user')->paginate($limit);

        }

        if ($mom->first()) {
            return $this->dispatchResponse(200, "", $mom);
        } else {
            return $this->dispatchResponse(200, "No Records Found!!", $mom);
        }
    }

    public function create()
    {
        $posted_data = Input::all();
        $project_id = [];
        $client_id = [];
        $task_list = [];
        $proj_name = "";

        if (isset($posted_data["project_id"])) {
            $project_id = $posted_data["project_id"];
            unset($posted_data["project_id"]);
        }

        if (isset($posted_data["client_names"])) {
            $client_id = $posted_data["client_names"];
            unset($posted_data["client_names"]);
        }

        // if (isset($posted_data["project_name"])) {
        //     $proj_name = $posted_data["project_name"];
        //     unset($posted_data["project_name"]);
        // }

        $user_id = $posted_data["users"];
        unset($posted_data["users"]);

        if (@$posted_data["tasks"]) {
            $task_list = $posted_data["tasks"];
            unset($posted_data["tasks"]);
        }

        try {
            DB::beginTransaction();
            $mom = new Mom();

            if ($mom->validate($posted_data)) {

                // $model = DB::table('mom')->insertGetId($posted_data);
                $model = Mom::create($posted_data);

                if ($model) {
                    $mom_id = $model->id;

                    $mom_proj = $this->createProject($project_id, $model);
                    if (!is_bool($mom_proj)) {
                        return $mom_proj;
                    }
                    $mom_user = $this->createUsers($user_id, $model);
                    if (!is_bool($mom_user)) {
                        return $mom_user;
                    }
                    $mom_client = $this->createClient($client_id, $model);
                    if (!is_bool($mom_client)) {
                        return $mom_client;
                    }
                    if (count($task_list) > 0) {
                        $mom_tasks = $this->createTasks($task_list, $model);
                        if (!is_bool($mom_tasks)) {
                            return $mom_tasks;
                        }
                    }

                    // $this->sendMailToUsers($model, $user_id);
                    DB::commit();
                    return $this->dispatchResponse(200, "MoM Created Successfully...!!", $model);
                }
            } else {
                DB::rollback();
                return $this->dispatchResponse(400, "Something went wrong.", $mom->errors());
            }
        } catch (\Exception $e) {
            DB::rollback();
            throw $e;
        }
    }

    public function update($id)
    {
        $posted_data = Input::all();
        $model = Mom::find((int) $id);

        $project_id = [];
        $client_id = [];

        if (isset($posted_data["project_id"])) {
            $project_id = $posted_data["project_id"];
            unset($posted_data["project_id"]);
        }

        if (isset($posted_data["client_names"])) {
            $client_id = $posted_data["client_names"];
            unset($posted_data["client_names"]);
        }

        $user_id = $posted_data["users"];
        unset($posted_data["users"]);

        // $task_list = $posted_data["tasks"];
        // unset($posted_data["tasks"]);

        try {
            DB::beginTransaction();
            $mom = new Mom();

            if ($mom->validate($posted_data)) {
                // $model = DB::table('mom')->insertGetId($posted_data);
                if ($model->update($posted_data)) {
                    $mom_id = $model->id;

                    $mom_proj = $this->updateProject($project_id, $model);
                    if (!is_bool($mom_proj)) {
                        return $mom_proj;
                    }
                    $mom_user = $this->updateUsers($user_id, $model);
                    if (!is_bool($mom_user)) {
                        return $mom_user;
                    }
                    $mom_client = $this->updateClient($client_id, $model);
                    if (!is_bool($mom_client)) {
                        return $mom_client;
                    }
                    DB::commit();
                    return $this->dispatchResponse(200, "MoM Updated Successfully...!!", $model);
                }
            } else {
                DB::rollback();
                return $this->dispatchResponse(400, "Something went wrong.", $mom->errors());
            }
        } catch (\Exception $e) {
            DB::rollback();
            throw $e;
        }
    }

    public function createUsers($user_id, $model)
    {
        if (count($user_id) > 0) {
            $arr_user = [];
            foreach ($user_id as $key => $value) {
                $data = [];
                $data["mom_id"] = $model["id"];
                $data["user_id"] = $value;
                array_push($arr_user, $data);
            }
            $momAttendees = new MomAttendees();
            if ($momAttendees->validate($arr_user)) {
                // $mom_user = MomAttendees::insert($arr_user);
                // $mom_user = DB::table('mom_attendees')->insert($arr_user);
                $mom_user = MomAttendees::insert($arr_user);
                return $mom_user;
            } else {
                DB::rollBack();
                return $this->dispatchResponse(400, "Something went wrong.", $momAttendees->errors());
            }
        }
    }

    public function updateUsers($user_id, $model)
    {
        if (count($user_id) > 0) {
            $user = MomAttendees::where("mom_id", $model["id"])->delete();
            $arr_user = [];
            foreach ($user_id as $key => $value) {
                $data = [];
                $data["mom_id"] = $model["id"];
                $data["user_id"] = $value;
                array_push($arr_user, $data);
            }
            $momAttendees = new MomAttendees();
            if ($momAttendees->validate($arr_user)) {
                // $mom_user = MomAttendees::insert($arr_user);
                // $mom_user = DB::table('mom_attendees')->insert($arr_user);
                $mom_user = MomAttendees::insert($arr_user);
                return $mom_user;
            } else {
                DB::rollBack();
                return $this->dispatchResponse(400, "Something went wrong.", $momAttendees->errors());
            }
        }
    }

    public function createProject($project_id, $model)
    {
        if (count($project_id) > 0) {
            $arr = [];
            foreach ($project_id as $key => $value) {
                $data = [];
                $data["mom_id"] = $model["id"];
                $data["project_id"] = $value;
                // $data["name"] = NULL;
                // if ($value == 1 && $proj_name != "") {
                //     $data["name"] = $proj_name;
                // }
                array_push($arr, $data);
            }
            $momProject = new MomProject();
            if ($momProject->validate($arr)) {
                // $mom_proj = MomProject::insert($arr);
                // $mom_proj = DB::table('mom_project')->insert($arr);
                $mom_proj = MomProject::insert($arr);
                return $mom_proj;
            } else {
                DB::rollBack();
                return $this->dispatchResponse(400, "Something went wrong.", $momProject->errors());
            }
        }
    }

    public function updateProject($project_id, $model)
    {
        if (count($project_id) > 0) {
            $project = MomProject::where("mom_id", $model["id"])->delete();

            $arr = [];
            foreach ($project_id as $key => $value) {
                $data = [];
                $data["mom_id"] = $model["id"];
                $data["project_id"] = $value;
                array_push($arr, $data);
            }
            $momProject = new MomProject();
            if ($momProject->validate($arr)) {
                // $mom_proj = MomProject::insert($arr);
                // $mom_proj = DB::table('mom_project')->insert($arr);
                $mom_proj = MomProject::insert($arr);
                return $mom_proj;
            } else {
                DB::rollBack();
                return $this->dispatchResponse(400, "Something went wrong.", $momProject->errors());
            }
        }
    }

    public function createClient($client_name, $model)
    {
        if (count($client_name) > 0) {
            $arr_client = [];
            foreach ($client_name as $key => $value) {
                $data = [];
                $data["mom_id"] = $model["id"];
                $data["name"] = $value;
                array_push($arr_client, $data);
            }
            $momClients = new MomClient();
            if ($momClients->validate($arr_client)) {
                // $mom_client = DB::table('mom_client')->insert($arr_client);
                $mom_client = MomClient::insert($arr_client);
                return $mom_client;
            } else {
                DB::rollBack();
                return $this->dispatchResponse(400, "Something went wrong.", $momClients->errors());
            }
        }
    }

    public function updateClient($client_name, $model)
    {
        if (count($client_name) > 0) {
            $client = MomClient::where("mom_id", $model["id"])->delete();
            $arr_client = [];
            foreach ($client_name as $key => $value) {
                $data = [];
                $data["mom_id"] = $model["id"];
                $data["name"] = $value;
                array_push($arr_client, $data);
            }
            $momClients = new MomClient();
            if ($momClients->validate($arr_client)) {
                // $mom_client = DB::table('mom_client')->insert($arr_client);
                $mom_client = MomClient::insert($arr_client);
                return $mom_client;
            } else {
                DB::rollBack();
                return $this->dispatchResponse(400, "Something went wrong.", $momClients->errors());
            }
        }
    }

    public function createTasks($task_list, $model)
    {
        if (count($task_list) > 0) {
            $arr_task = [];
            foreach ($task_list as $key => $value) {
                $data = [];
                $data["mom_id"] = $model["id"];
                $data["user_id"] = $value["user_id"];
                $data["name"] = $value["name"];
                $data["description"] = $value["description"];
                $data["type"] = $value["type"];
                $data["status"] = $value["status"];
                $data["start_date"] = $value["start_date"];
                $data["due_date"] = $value["due_date"];
                $data["created_by"] = $model["created_by"];
                $data["updated_by"] = $model["updated_by"];
                $data["created_at"] = $model["created_at"];
                $data["updated_at"] = $model["updated_at"];
                array_push($arr_task, $data);
            }
            $momTaskss = new MomTasks();
            if ($momTaskss->validate($arr_task, "MOM")) {
                // $mom_task = DB::table('mom_task_table')->insert($arr_task);
                $mom_task = MomTasks::insert($arr_task);
                return $mom_task;
            } else {
                DB::rollBack();
                return $this->dispatchResponse(400, "Something went wrong.", $momTaskss->errors());
            }
        }
    }

    public function sendMailToUsers($model, $user_ids)
    {
        $data = [
            'mom_title' => $model->title,
            'mom_date' => $model->date,
            'mom_venue' => $model->meeting_venue,
        ];

        config(['mail.username' => 'monica.syslogyx@gmail.com',
            'mail.password' => 'monicasyslogyx']);

        $emails = \App\User::select('email')->whereIn('id', $user_ids)->get();
        $email = [];
        foreach ($emails as $key => $value) {
            array_push($email, $value["email"]);
        }

        Mail::send('email.email_template', $data, function ($message) use ($email) {
            //            $file_name = public_path() . '/documents/' . $document->project->p_key . '/' . $document->file_name;
            $message->to($email);
            $message->subject('Minutes of Meeting Report');
            //            $message->attach($file_name);
        });

        if (count(Mail::failures()) > 0) {
            $errors = 'Failed to send email, please try again.';
            return $errors;
        }
    }

    public function updateStatus()
    {
        try {
            DB::beginTransaction();
            $posted_data = Input::all();
            if ($posted_data["status"] == "Closed") {
                $mom_task = MomTasks::where([
                    ["mom_id", "=", $posted_data["mom_id"]],
                    ["status", "!=", "Closed"],
                ])->get();
                // print_r($mom_task->toArray());
                // die();
                if ($mom_task->first()) {
                    return $this->dispatchResponse(400, "All task is not closed yet. First close all tasks of this MoM", null);
                } else {
                    $mom = Mom::where([
                        ["id", "=", $posted_data["mom_id"]],
                    ])->update(['status' => $posted_data["status"]]);

                    DB::commit();
                    return $this->dispatchResponse(200, "MoM status updated successfully...!!", $mom);
                }
            } else {
                $mom = Mom::where([
                    ["id", "=", $posted_data["mom_id"]],
                ])->update(['status' => $posted_data["status"]]);

                DB::commit();
                return $this->dispatchResponse(200, "MoM status updated successfully...!!", $mom);
            }
        } catch (\Exception $e) {
            DB::rollback();
            throw $e;
        }
    }

    public function updateTaskStatus()
    {
        try {
            DB::beginTransaction();
            $posted_data = Input::all();

            $mom = MomTasks::where([
                ["id", "=", $posted_data["task_id"]],
            ])->update(['status' => $posted_data["status"]]);

            DB::commit();
            return $this->dispatchResponse(200, "MoM status updated successfully...!!", $mom);

        } catch (\Exception $e) {
            DB::rollback();
            throw $e;
        }
    }

    public function view($id)
    {
        $model = Mom::find((int) $id);

        if ($model) {
            return $this->response->item($model, new MomTransformer())->setStatusCode(200);
        }

        // return $model = Client::find((int) $id);
    }

    public function removeMoM($id)
    {
        $mom_task = Mom::where([
            ["id", "=", $id],
        ])->update(['mom_status' => 1]);
        $model = Mom::find((int) $id);

        if ($model) {
            return $this->dispatchResponse(200, "MoM Deleted Successfull...!!", $model);
        }

        // return $this->response->item($model, new MomTransformer())->setStatusCode(200);
    }

    public function client_index(Request $request)
    {
        $page = $request->page;
        $limit = $request->limit;
        if (($page == null || $limit == null) || ($page == -1 || $limit == -1)) {
            $clients = MomClient::orderBy('name', 'asc')->paginate(200);
        } else {
            $clients = MomClient::orderBy('name', 'asc')->paginate($limit);
        }

        if ($clients->first()) {
            return $this->dispatchResponse(Config::get('constants.SUCESS_CODE'), "", $clients);
        } else {
            return $this->dispatchResponse(Config::get('constants.SUCESS_CODE'), "No Records Found!!", null);
        }
    }
}
