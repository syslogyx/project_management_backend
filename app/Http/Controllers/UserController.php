<?php

namespace App\Http\Controllers;

use App\Http\Transformers\UserTransformer;
use App\MailUtility;
use App\MomAttendees;
use App\Project;
use App\ProjectResource;
use App\RoleUsers;
use App\User;
use App\UserTechnologyMapping;
use App\Utilities;
use Config;
use DateTime;
use DB;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Input;
use PDF;

// use GuzzleHttp\Message\Response;

class UserController extends BaseController
{

    public function index(Request $request)
    {

        $page = $request->page;
        $limit = $request->limit;
        if (($page == null || $limit == null) || ($page == -1 || $limit == -1)) {
            $user = User::orderBy('name', 'asc')->with("project", "technology", "userTechnologyMapping", "resourceMatrixLog", "projectResource.project")->paginate(200);
        } else {
            $user = User::orderBy('name', 'asc')->with("project", "technology", "userTechnologyMapping", "resourceMatrixLog", "projectResource.project")->paginate($limit);
        }

        if ($user->first()) {
            // $this->calculateTotalExperience($user);

            //Suvrat No need to calculate the technologywise experience when fetching the data as it make the load times extremely long(~13s) //comment later after testing
            $this->calculateTechnologyWiseExperience($user);
            ///////////////////////////////////////////

            return $this->dispatchResponse(200, "", $user);
            //return $this->response->item($user, new UserTransformer())->setStatusCode(200);
        } else {
            return $this->dispatchResponse(200, "No Records Found!!", $user);
        }
    }

    public function indexData(Request $request)
    {
        $user = User::orderBy('name', 'asc')->paginate(200);
        if ($user->first()) {

            //Suvrat No use for this as there is no need to calculate the technology wise experience for fetching userlist for filter and this takes whole 13 seconds to calculate  //comment later after testing
            $this->calculateTechnologyWiseExperience($user);
            ///////////////////////////////////////////////////////

            return $this->dispatchResponse(200, "", $user);
        } else {
            return $this->dispatchResponse(200, "No Records Found!!", $user);
        }
    }

    public function sync(request $request)
    {
        // $url = 'https://hrms.syslogyx.com/users/getAllUser.json';
        $url = Config::get('constants.HRMS_LIVE_URL') . 'users/getAllUser.json';
        // $url = 'http://172.16.1.171:8767/users/getAllUser.json';

        //$response = $this->curlCall($url);

        $data = $this->getOtherSourceResponce($url);

        if (count($data) > 0) {
            foreach ($data as $key => $value) {
                $managerID = User::where('user_id', $value["manager_user_id"])->pluck('id')->first();
                $mentorID = User::where('user_id', $value["mentor_user_id"])->pluck('id')->first();

                $user_data = [];
                $user_data["user_id"] = $value["id"];
                $user_data["name"] = $value["name"];
                $user_data["email"] = $value["email"];
                $user_data["password"] = $value["password"];
                $user_data["gender"] = $value["gender"];
                $user_data["email_internal"] = $value["internal_email"];
                $user_data["email_external"] = $value["external_email"];
                $user_data["department"] = $value["department_name"];
                $user_data["designation"] = $value["designation_name"];
                $user_data["avatar"] = $value["upload_profile_pic"];
                $user_data["doj"] = $value["emp_doj"];
                $user_data["total_experience"] = $value["total_experience"];
                $user_data["manager_id"] = $managerID;
                $user_data["mentor_id"] = $mentorID;
                $user_data["hrms_role_id"] = $value["role_id"];
                $user_data["is_updated"] = 1;
                $model = $this->createOrUpdate($user_data);
            }
            return $this->dispatchResponse(200, "Synced successfully...!!");
        } else {
            return $this->dispatchResponse(200, "Something went wrong...!!");
        }

    }

    public function sync_user_by_id($id)
    {
        // $url = 'http://hrms.syslogyx.com/users/getByUserId/' . $id;
        $url = Config::get('constants.HRMS_LIVE_URL') . 'users/getByUserId/' . $id;

        $data = $this->getOtherSourceResponce($url);

        // // $response = $this->curlCall($url);

        $user_data = [];
        $user_data["user_id"] = $data[0]["id"];
        $user_data["name"] = $data[0]["name"];
        $user_data["email"] = $data[0]["email"];
        $user_data["password"] = $data[0]["password"];
        $user_data["gender"] = $data[0]["gender"];
        $user_data["email_internal"] = $data[0]["internal_email"];
        $user_data["email_external"] = $data[0]["external_email"];
        $user_data["department"] = $data[0]["department_name"];
        $user_data["designation"] = $data[0]["designation_name"];
        $user_data["avatar"] = $data[0]["upload_profile_pic"];
        $user_data["doj"] = $data[0]["emp_doj"];
        $user_data["total_experience"] = $data[0]["total_experience"];
        $user_data["hrms_role_id"] = $data[0]["role_id"];
        $user_data["manager_id"] = User::where('user_id', $data[0]["manager_user_id"])->pluck('id')->first();
        $user_data["mentor_id"] = User::where('user_id', $data[0]["mentor_user_id"])->pluck('id')->first();
        $user_data["is_updated"] = 1;
        $model = $this->createOrUpdate($user_data);

        return $this->dispatchResponse(200, "Synced successfully...!!", $model);
    }

    public static function createOrUpdate($formatted_array)
    {
        $row = User::where("user_id", $formatted_array['user_id'])->first();

        if ($row === null) {
            User::insert($formatted_array);
        } else {
            //  $row->save($formatted_array);
            User::where('user_id', $formatted_array['user_id'])
                ->update($formatted_array);
        }

        $affected_row = User::where("user_id", $formatted_array['user_id'])->first();
        return $affected_row;
    }

    public function show(Request $request, $email)
    {
        //show user details according to the specified email
        $user = User::with("project", "projectResource", "resourceMatrixLog", "managerData", "mentorData")->where('email', $email)->first();

        $userObject = new User();

        if ($user) {
            return $this->response->item($user, new UserTransformer())->setStatusCode(200);
        } else {
            throw new \Dingo\Api\Exception\StoreResourceFailedException('Invalid email address entered.', $userObject->errors());
        }
    }

    public function view($id)
    {
        $user = User::with("project", "projectResource", "resourceMatrixLog", "managerData", "mentorData")->find((int) $id);
        if ($user) {
            $users = [];
            array_push($users, $user);
            // $this->calculateTotalExperience($users);
            $this->calculateTechnologyWiseExperience($users);

            return $this->response->item($user, new UserTransformer())->setStatusCode(200);
        }
    }

    public function getProjectListByUser($user_id, $type)
    {
        // $query = "SELECT a.* , b.name AS proj_name, b.company_name,b.company_id,
        //     b.duration_in_years,b.type, c.name AS cat_name, d.technology_id AS tech_id,
        //     e.name AS tech_name
        //     FROM `project_resources` a
        //     LEFT JOIN `projects` b ON (b.id = a.project_id)
        //     LEFT JOIN `categories` c ON (c.id = a.domain_id)
        //     LEFT JOIN `project_resource_technology_mapping` d ON (d.project_resource_id = a.id)
        //     LEFT JOIN `technologies` e ON (d.technology_id = e.id)
        //     WHERE a.user_id =" . $user_id;
        ///////////////////////////////////////////////////////////////

        // Suvrat  Convert raw SQL query into query builder format
        $query = DB::table('project_resources as a')
            ->select('a.*', 'b.name as proj_name', 'b.company_name', 'b.company_id', 'b.duration_in_years', 'b.type', 'c.name as cat_name', 'd.technology_id as tech_id', 'e.name as tech_name')
            ->leftJoin('projects as b', 'a.project_id', '=', 'b.id')
            ->leftJoin('categories as c', 'a.domain_id', '=', 'c.id')
            ->leftJoin('project_resource_technology_mapping as d', 'a.id', '=', 'd.project_resource_id')
            ->leftJoin('technologies as e', 'd.technology_id', '=', 'e.id')
            ->where('a.user_id', $user_id)
            ->when($type, function ($query, $type) {
                return $query->where('b.type', $type);
            })
            ->orderBy('b.company_id')
            ->get();
        ////////////////////////////////////////////////////////////////

        // if ($type) {
        // $query .= " AND b.type=" . $type;
        // }
        // $query .= " ORDER BY b.company_id";

        $outArr = [];

        // $project_list = DB::select($query);
        $project_list = $query;

        $len = count($project_list);

        for ($i = 0; $i < $len; $i++) {
            $d1 = new DateTime($project_list[$i]->start_date);
            $d2 = new DateTime($project_list[$i]->due_date);
            $current = new DateTime();
            if ($d2 > $current) {
                $d2 = $current;
            }

            if ($d1 < $d2) {
                $diff = date_diff($d1, $d2);
                $diff = ($diff->format("%a"));
            } else {
                $diff = "0";
            }

            $temp = [
                "id" => $project_list[$i]->project_id,
                "name" => $project_list[$i]->proj_name,
                "company_id" => $project_list[$i]->company_id,
                "company_name" => $project_list[$i]->company_name,
                "role" => $project_list[$i]->role,
                "start_date" => $project_list[$i]->start_date,
                "due_date" => $project_list[$i]->due_date,
                "user_id" => $user_id,
                //"duration_in_years" => $project_list[$i]->duration_in_years,
                "duration_in_years" => $this->daysToYearConversion($diff),
                "type" => $project_list[$i]->type,
                "domains" => [],
            ];

            $domain = [
                "id" => $project_list[$i]->domain_id,
                "name" => $project_list[$i]->cat_name,
                "technologies" => [],
            ];

            //if ($project_list[$i]->tech_id)
            $technology = ["id" => $project_list[$i]->tech_id, "name" => $project_list[$i]->tech_name];

            //check whether project exist and add.
            $index = $this->is_key_exist($outArr, "project", $temp["id"]);
            if ($index > -1) {
                if ($technology["id"] !== null) {
                    $domain["technologies"][] = $technology;
                }

                if ($technology["id"]) {
                    $temp["domains"][] = $domain;
                }

                $outArr[] = $temp;
            }

            //check whether domain exist and add.
            $index = $this->is_key_exist($outArr, "domain", ["project_id" => $temp["id"], "cat_id" => $domain["id"]]);
            if ($index > -1) {
                $outArr[$index]["domains"][] = $domain;

                if ($technology["id"] !== null) {
                    //$outArr[$index]["domains"][0]["technologies"][] = $technology;
                }
            }

            //create technology here.
            if ($technology["id"]) {
                $indexArr = $this->is_key_exist($outArr, "tech", ["project_id" => $temp["id"], "cat_id" => $domain["id"], "tech_id" => $technology["id"]]);
                if (is_array($indexArr)) {
                    $outArr[$indexArr["proj_index"]]["domains"][$indexArr["domain_index"]]["technologies"][] = $technology;
                }

            }
        }
        if ($project_list) {
            $hrms_user_id = User::where("id", $user_id)->first();
            $final_arr = $this->usersProjectList($outArr, $hrms_user_id["user_id"]);
            $final_arr = $this->calculateTotalExperienceByUserId($final_arr);
            return $this->dispatchResponse(200, "", $final_arr);
        } else {
            return $this->dispatchResponse(200, "No Data Found...!!", null);
        }
    }

    // check if exist or not.
    public function is_key_exist($data, $key, $val)
    {

        if (empty($data)) {
            return 1;
        }

        switch ($key) {
            case "project":{
                    foreach ($data as $d) {
                        if ($d["id"] == $val) {
                            return -1;
                        }

                    }
                    return 1;
                    break;
                }
            case "domain":{

                    $len = count($data);
                    for ($i = 0; $i < $len; $i++) {
                        if ($data[$i]["id"] == $val["project_id"]) {
                            if (isset($data[$i]["domains"])) {
                                $len1 = count($data[$i]["domains"]);
                                for ($j = 0; $j < $len1; $j++) {
                                    if ($data[$i]["domains"][$j]["id"] == $val["cat_id"]) {
                                        return -1;
                                    }

                                }
                                return $i;
                            }
                        }
                    }
                    break;
                }
            case "tech":{
                    $len = count($data);
                    for ($i = 0; $i < $len; $i++) {
                        if ($data[$i]["id"] == $val["project_id"]) {
                            if (isset($data[$i]["domains"])) {
                                $len1 = count($data[$i]["domains"]);
                                for ($j = 0; $j < $len1; $j++) {
                                    if ($data[$i]["domains"][$j]["id"] === $val["cat_id"]) {
                                        if (isset($data[$i]["domains"][$j]["technologies"])) {
                                            $len2 = count($data[$i]["domains"][$j]["technologies"]);
                                            for ($k = 0; $k < $len2; $k++) {
                                                if ($data[$i]["domains"][$j]["technologies"][$k]["id"] == $val["tech_id"]) {
                                                    return -1;
                                                }

                                            }
                                            $index = ["proj_index" => $i, "domain_index" => $j];
                                            return ["proj_index" => $i, "domain_index" => $j];
                                        }
                                    }
                                }
                            }
                        }
                    }
                    break;
                }
        }
    }

    // not in use
    public function calculateTotalExperience($users)
    {
        foreach ($users as $key => $value) {
            //            $query = "SELECT  a.company_name,MIN(a.start_date) as start_date,MAX(a.due_date) as due_date," .
            //                    " IF(MAX(a.due_date) > CURRENT_DATE, DATEDIFF(CURRENT_DATE,MIN(a.start_date)),DATEDIFF(MAX(a.due_date),MIN(a.start_date)))" .
            //                    " AS days" .
            //                    " FROM `projects` a" .
            //                    " LEFT JOIN `project_resources` b ON (a.id = b.project_id) " .
            //                    " WHERE a.user_id = " . $value["id"] . "  OR b.user_id = " . $value["id"] .
            //                    " GROUP BY company_name ";
            $query = "SELECT  a.name,b.* FROM `projects` a" .
                " LEFT JOIN `project_resources` b" .
                " ON (a.id = b.project_id)" .
                " WHERE a.user_id = " . $value["id"] . "  OR b.user_id = " . $value["id"] .
                " ORDER BY b.start_date;";

            $user_list = DB::select($query);

            $date_lists = json_decode(json_encode($user_list), true);
            //print_r($date_lists);
            $start_date = "";
            $end_date = "";
            $prev_start_date = "";
            $prev_end_date = "";
            $new_array = [];

            if ($date_lists) {
                $count = count($date_lists);
                //print_r($count);
                for ($i = 0; $i < $count; $i++) {
                    if ($i == 0) {
                        $d1 = new DateTime($date_lists[$i]["start_date"]);
                        $d2 = new DateTime($date_lists[$i]["due_date"]);
                        $current = new DateTime();
                        if ($d2 > $current) {
                            $d2 = $current;
                            //break;
                        }
                        //$diff = $d1->diff($d2)->m + ($d1->diff($d2)->y * 12);
                        $diff = date_diff($d1, $d2);
                        $date_lists[$i]["days"] = $diff->format("%a");
                        //$date_lists[$i]["months"] = $diff;
                        array_push($new_array, $date_lists[$i]);
                    } else {
                        $data = end($new_array);
                        $prev_start_date = $data["start_date"];
                        $prev_end_date = $data["due_date"];
                        $start_date = $date_lists[$i]["start_date"];
                        $end_date = $date_lists[$i]["due_date"];

                        if ($prev_end_date < $start_date) {

                            $idle_start_date = $start_date;
                            $idle_end_date = $prev_end_date;

                            $d1 = new DateTime($idle_start_date);
                            $d2 = new DateTime($idle_end_date);

                            $current = new DateTime();
                            if ($d2 > $current) {
                                $d2 = $current;
                            }

                            $idle_diff = date_diff($d1, $d2);
                            $date_lists[$i]["idle_days"] = $idle_diff->format("%a");

                            $start_date = $start_date;
                            $end_date = $end_date;
                            $date_lists[$i]["start_date"] = $start_date;
                            $date_lists[$i]["due_date"] = $end_date;

                            $d1 = new DateTime($date_lists[$i]["start_date"]);
                            $d2 = new DateTime($date_lists[$i]["due_date"]);
                            if ($d2 > $current) {
                                $d2 = $current;
                            }

                            $diff = date_diff($d1, $d2);
                            $date_lists[$i]["days"] = $diff->format("%a");
                            array_push($new_array, $date_lists[$i]);
                        } else {

                            if ($end_date > $prev_end_date) {
                                $time = strtotime($prev_end_date);
                                $final = date("Y-m-d", strtotime("+1 day", $time));

                                $start_date = $final; //+ 1 day;
                                $end_date = $end_date;

                                $date_lists[$i]["start_date"] = $start_date;
                                $date_lists[$i]["due_date"] = $end_date;

                                $d1 = new DateTime($date_lists[$i]["start_date"]);
                                $d2 = new DateTime($date_lists[$i]["due_date"]);
                                $current = new DateTime();
                                if ($d2 > $current) {
                                    $d2 = $current;
                                    //  break;
                                }
                                //  $diff = $d1->diff($d2)->m + ($d1->diff($d2)->y * 12);
                                $diff = date_diff($d1, $d2);
                                $date_lists[$i]["days"] = $diff->format("%a");
                                array_push($new_array, $date_lists[$i]);
                            } else {

                            }
                        }
                    }
                }
            }
            $total = 0;
            foreach ($new_array as $k2 => $v2) {
                $total = $total + $v2["days"];
            }
            $total = $this->daysToYearConversion($total);
            //  $update = \App\User::where([
            //      ['id', '=', $value["id"]]
            //  ])->update(['total_experience' => $total]);
            //  print_r($total);
            //  print_r($date_lists);
        }
        return $user_list;
    }

    public function calculateTechnologyWiseExperience($users)
    {
        //Suvrat we only need to get the first isUpdated entry as in the if:true block, every entry gets updated isUpdate value this results in improving load times upto 90%
        $isUpdated = DB::table('users')->select('id', 'is_updated')->first();

        if ($isUpdated->is_updated == 0) {

            foreach ($users as $key => $value) {
                //Suvrat Set the value of isUpdated field as true(1) each  day for first query run
                $regulator = \App\User::where([['id', '=', $value["id"]]])->update(['is_updated' => 1]);
                /////////////////////////////////////////////////////////////////////////////
                $query = "SELECT a.id, a.name FROM `technologies` a" .
                    " WHERE a.id IN (SELECT b.technology_id FROM `user_technology_mapping` b" .
                    " WHERE b.user_id = " . $value["id"] . " )";

                $tech_id_list = DB::select($query);

                foreach ($tech_id_list as $k => $v) {

                    // $query1 = "SELECT c.start_date, c.due_date FROM `project_resources` d, `project_resource_technology_mapping` c " .
                    //         " WHERE d.id IN (" .
                    //         " SELECT c.project_resource_id FROM `project_resource_technology_mapping` c " .
                    //         " WHERE c.technology_id = " . $v->id . ")AND d.user_id = " . $value["id"] .
                    //         " ORDER BY c.start_date";

                    $query1 = "SELECT c.start_date, c.due_date FROM project_resources d INNER JOIN project_resource_technology_mapping c ON d.id = c.project_resource_id WHERE c.technology_id = " . $v->id . " AND d.user_id = " . $value["id"] . " ORDER BY c.start_date";

                    $date_list = DB::select($query1);

                    $date_lists = json_decode(json_encode($date_list), true);

                    $start_date = "";
                    $end_date = "";
                    $prev_start_date = "";
                    $prev_end_date = "";
                    $new_array = [];

                    if ($date_lists) {
                        $count = count($date_lists);
                        for ($i = 0; $i < $count; $i++) {
                            if ($i == 0) {
                                $d1 = new DateTime($date_lists[$i]["start_date"]);
                                $d2 = new DateTime($date_lists[$i]["due_date"]);
                                $current = new DateTime();
                                if ($d2 > $current) {
                                    $d2 = $current;
                                }
                                if ($d1 <= $current) {
                                    $diff = date_diff($d1, $d2);
                                    $date_lists[$i]["days"] = ($diff->format("%a") + 1);
                                    array_push($new_array, $date_lists[$i]);
                                }

                            } else {
                                $data = end($new_array);
                                $prev_start_date = $data["start_date"];
                                $prev_end_date = $data["due_date"];
                                $start_date = $date_lists[$i]["start_date"];
                                $end_date = $date_lists[$i]["due_date"];

                                if ($prev_end_date < $start_date) {
                                    $start_date = $start_date;
                                    $end_date = $end_date;
                                    $date_lists[$i]["start_date"] = $start_date;
                                    $date_lists[$i]["due_date"] = $end_date;

                                    $d1 = new DateTime($date_lists[$i]["start_date"]);
                                    $d2 = new DateTime($date_lists[$i]["due_date"]);
                                    $current = new DateTime();
                                    if ($d2 > $current) {
                                        $d2 = $current;
                                        //  break;
                                    }
                                    //  $diff = $d1->diff($d2)->m + ($d1->diff($d2)->y * 12);
                                    if ($d1 <= $current) {
                                        $diff = date_diff($d1, $d2);
                                        $date_lists[$i]["days"] = ($diff->format("%a") + 1);
                                        array_push($new_array, $date_lists[$i]);
                                    }
                                } else {
                                    if ($end_date > $prev_end_date) {
                                        $time = strtotime($prev_end_date);
                                        $final = date("Y-m-d", strtotime("+1 day", $time));

                                        $start_date = $final; //+ 1 day;
                                        $end_date = $end_date;

                                        $date_lists[$i]["start_date"] = $start_date;
                                        $date_lists[$i]["due_date"] = $end_date;

                                        $d1 = new DateTime($date_lists[$i]["start_date"]);
                                        $d2 = new DateTime($date_lists[$i]["due_date"]);
                                        $current = new DateTime();
                                        if ($d2 > $current) {
                                            $d2 = $current;
                                            //  break;
                                        }
                                        //  $diff = $d1->diff($d2)->m + ($d1->diff($d2)->y * 12);
                                        if ($d1 <= $current) {
                                            $diff = date_diff($d1, $d2);
                                            $date_lists[$i]["days"] = ($diff->format("%a") + 1);
                                            array_push($new_array, $date_lists[$i]);
                                        }
                                    } else {

                                    }
                                }
                            }
                        }
                    }
                    $total = 0;
                    foreach ($new_array as $k2 => $v2) {
                        $total = $total + $v2["days"];
                    }

                    // if(sizeof($new_array)>0){
                    //     $data_of_new = $this->convertDaysToYearMonthAndDays ($total, $new_array[0]["start_date"]);
                    //     print_r("data_of_new");
                    //     print_r($data_of_new);
                    // }

                    $totals = $this->daysToYearConversion($total);
                    $update = \App\UserTechnologyMapping::where([
                        ['user_id', '=', $value["id"]],
                        ['technology_id', '=', $v->id],
                    ])->update(['duration' => $totals, 'duration_in_month' => $total]);
                }
            }
        } else {
            //do nothing
        }
        return true;
    }

    // not in use
    public function date_interval($date1, $date2)
    {
        $date1 = new DateTime($date1);
        $date2 = new DateTime($date2);

        $interval = date_diff($date2, $date1);
        return $interval->format('%y.%m');
        //        return $interval->format('%days');
        //        return $interval->format('%y.%m.%d');
        //        $interval = abs(strtotime($date2) - strtotime($date1));
        //        return $interval;
        //        return ((($y = $interval->format('%y')) > 0) ? $y . ' Year' . ($y > 1 ? 's' : '') . ', ' : '') . ((($m = $interval->format('%m')) > 0) ? $m . ' Month' . ($m > 1 ? 's' : '') . ', ' : '') . ((($d = $interval->format('%d')) > 0) ? $d . ' Day' . ($d > 1 ? 's' : '') : '');
        //        return (($y > 0) ? $y . ' Year' . ($y > 1 ? 's' : '') . ', ' : '') . (($m > 0) ? $m . ' Month' . ($m > 1 ? 's' : '') . ', ' : '') . (($d > 0) ? $d . ' Day' . ($d > 1 ? 's' : '') : '');
    }

    public function daysToYearConversion($convert)
    {
        //  $convert = $convert + 1;

        $years = ($convert / 365.2525); // days / 365 days
        $years = floor($years); // Remove all decimals

        $month = ($convert - ($years * 365.2525)) / 30.5;
        $month = floor($month); // Remove all decimals

        $days = ($convert - ($years * 365.2525) - ($month * 30.5));
        $days = floor($days); // Remove all decimals

        // $years = ($convert / 365) ; // days / 365 days
        // $years = floor($years); // Remove all decimals

        // $month = ($convert % 365) / 30.5; // I choose 30.5 for Month (30,31) ;)
        // $month = floor($month); // Remove all decimals

        // $days = ($convert % 365) % 30.5; // the rest of days

        return (($years > 0) ? $years . ' Year' . ($years > 1 ? 's' : '') . ' ' : '') . (($month > 0) ? $month . ' Month' . ($month > 1 ? 's' : '') . ' ' : '') . (($days > 0) ? $days . ' Day' . ($days > 1 ? 's' : '') : '');
    }

    public function usersProjectList($arr, $user_id)
    {
        $count = count($arr);
        $final_arr = [];

        for ($i = 0; $i < $count; $i++) {
            if ($i == 0) {
                $data = [
                    "company_id" => $arr[$i]["company_id"],
                    "company_name" => $arr[$i]["company_name"],
                    "project_list" => [],
                ];
                array_push($data["project_list"], $arr[$i]);
                array_push($final_arr, $data);
            } else {
                if (in_array($arr[$i]["company_id"], array_column($final_arr, 'company_id'))) {
                    $key = array_search($arr[$i]["company_id"], array_column($final_arr, 'company_id'));
                    array_push($final_arr[$key]["project_list"], $arr[$i]);
                } else {
                    $data = [
                        "company_id" => $arr[$i]["company_id"],
                        "company_name" => $arr[$i]["company_name"],
                        "project_list" => [],
                    ];
                    array_push($data["project_list"], $arr[$i]);
                    array_push($final_arr, $data);
                }
            }
        }

        //get company data from hrms and add start date and end date of that company in array

        // $url = 'http://hrms.syslogyx.com/company_experience/getCompanyByUserId/' . $user_id;

        $url = Config::get('constants.HRMS_LIVE_URL') . 'company_experience/getCompanyByUserId/' . $user_id;

        $data = $this->getOtherSourceResponce($url);

        // $client = new Client();
        // $response = $client->get($url);

        // $body = (string)$response->getBody();

        // $trimmedBody = preg_replace(
        //     '/
        //       ^
        //       [\pZ\p{Cc}\x{feff}]+
        //       |
        //       [\pZ\p{Cc}\x{feff}]+$
        //      /ux',
        //     '',
        //     $body
        //   );

        // $responseData = json_decode($trimmedBody, TRUE);

        // // $response = $this->curlCall($url);

        // // // Decode the response
        // // $responseData = json_decode($response, TRUE);

        // $data = $responseData;

        if ($data && !empty($data)) {
            // $data = $data["data"];
            for ($i = 0; $i < count($data); $i++) {
                for ($j = 0; $j < count($final_arr); $j++) {
                    if ($data[$i]["id"] == $final_arr[$j]["company_id"]) {
                        $final_arr[$j]["comp_start_date"] = $data[$i]["companyStartDate"];
                        $final_arr[$j]["comp_due_date"] = $data[$i]["companyEndDate"];
                    }
                }
            }
            if (count($data) !== count($final_arr)) {

            }
        }
        for ($i = 0; $i < count($final_arr); $i++) {
            for ($j = 0; $j < count($final_arr[$i]["project_list"]); $j++) {
                $query1 = "SELECT MIN(start_date) AS start_date FROM `project_resources` WHERE project_id = " . $final_arr[$i]["project_list"][$j]["id"] . " AND user_id = " . $final_arr[$i]["project_list"][$j]["user_id"];
                $proj_start_date = DB::select($query1);

                $query2 = "SELECT MAX(due_date) AS end_date FROM `project_resources` WHERE project_id = " . $final_arr[$i]["project_list"][$j]["id"] . " AND user_id = " . $final_arr[$i]["project_list"][$j]["user_id"];
                $proj_end_date = DB::select($query2);

                $proj_start_date = json_decode(json_encode($proj_start_date), true);
                $proj_end_date = json_decode(json_encode($proj_end_date), true);

                $start_date = explode(" ", $proj_start_date[0]["start_date"]);
                $due_date = explode(" ", $proj_end_date[0]["end_date"]);

                $final_arr[$i]["project_list"][$j]["start_date"] = $start_date[0];
                $final_arr[$i]["project_list"][$j]["due_date"] = $due_date[0];
            }
        }
        return $final_arr;
    }

    public function calculateTotalExperienceByUserId($company_details)
    {

        $count = count($company_details);
        for ($i = 0; $i < $count; $i++) {
            $count1 = count($company_details[$i]["project_list"]);

            //sort data according to start date
            usort($company_details[$i]["project_list"], array($this, 'date_sort'));

            $start_date = "";
            $end_date = "";
            $prev_start_date = "";
            $prev_end_date = "";
            $new_array = [];
            $idle_info = [];
            for ($j = 0; $j < $count1; $j++) {
                if ($j == 0) {
                    if (array_key_exists("comp_start_date", $company_details[$i])) {
                        $prev_start_date = $company_details[$i]["comp_start_date"];
                        $prev_end_date = $company_details[$i]["comp_due_date"];
                        $start_date = $company_details[$i]["project_list"][$j]["start_date"];
                        $end_date = $company_details[$i]["project_list"][$j]["due_date"];

                        // $comp_start_date = $company_details[$i]["comp_start_date"];
                        if ($prev_start_date) {
                            if ($prev_start_date < $start_date) {

                                $idle_start_date = $start_date;
                                $idle_end_date = $prev_start_date;

                                $time1 = strtotime($idle_start_date);
                                $final_s = date("Y-m-d", strtotime("-1 day", $time1));
                                //$final_s = date("Y-m-d", $time1);

                                $time2 = strtotime($idle_end_date);
                                $final_e = date("Y-m-d", strtotime("+1 day", $time2));
                                // $final_e = date("Y-m-d", $time2);
                                //$company_details[$i]["project_list"][$j]["idle_start_date"] = $final_e;
                                //$company_details[$i]["project_list"][$j]["idle_due_date"] = $final_s;

                                $d1 = new DateTime($final_s);
                                $d2 = new DateTime($final_e);

                                $current = new DateTime();
                                if ($d2 > $current) {
                                    $d2 = $current;
                                }

                                $idle_diff = date_diff($d1, $d2);
                                // $company_details[$i]["project_list"][$j]["idle_days"] = $idle_diff->format("%a");
                                //$company_details[$i]["project_list"][$j]["idle_days_duration"] = $this->daysToYearConversion($idle_diff->format("%a"));

                                $data = [];
                                $data["idle_start_date"] = $final_e;
                                $data["idle_due_date"] = $final_s;
                                $data["idle_days"] = $idle_diff->format("%a");
                                $data["idle_days_duration"] = $this->daysToYearConversion($idle_diff->format("%a"));

                                array_push($idle_info, $data);
                            }
                        }
                    }

                    $d1 = new DateTime($company_details[$i]["project_list"][$j]["start_date"]);
                    $d2 = new DateTime($company_details[$i]["project_list"][$j]["due_date"]);
                    $current = new DateTime();
                    if ($d2 > $current) {
                        $d2 = $current;
                    }
                    $diff = date_diff($d1, $d2);
                    $company_details[$i]["project_list"][$j]["days"] = ($diff->format("%a"));
                    array_push($new_array, $company_details[$i]["project_list"][$j]);
                } else {
                    $data = end($new_array);
                    $prev_start_date = $data["start_date"];
                    $prev_end_date = $data["due_date"];
                    $start_date = $company_details[$i]["project_list"][$j]["start_date"];
                    $end_date = $company_details[$i]["project_list"][$j]["due_date"];
                    if ($prev_start_date == $start_date) {
                        if ($prev_end_date > $end_date) {
                            $end_date = $prev_end_date;
                        }
                    }
                    if ($prev_end_date < $start_date) {

                        $idle_start_date = $start_date;
                        $idle_end_date = $prev_end_date;

                        $time1 = strtotime($idle_start_date);
                        //$final_s = date("Y-m-d", strtotime("-1 day", $time1));
                        $final_s = date("Y-m-d", $time1);

                        $time2 = strtotime($idle_end_date);
                        $final_e = date("Y-m-d", strtotime("+1 day", $time2));
                        //$final_e = date("Y-m-d", $time2);
                        //$company_details[$i]["project_list"][$j]["idle_start_date"] = $final_e;
                        //$company_details[$i]["project_list"][$j]["idle_due_date"] = $final_s;

                        $d1 = new DateTime($final_s);
                        $d2 = new DateTime($final_e);

                        $current = new DateTime();
                        if ($d2 > $current) {
                            $d2 = $current;
                        }

                        $idle_diff = date_diff($d1, $d2);
                        //$company_details[$i]["project_list"][$j]["idle_days"] = $idle_diff->format("%a");
                        //$company_details[$i]["project_list"][$j]["idle_days_duration"] = $this->daysToYearConversion($idle_diff->format("%a"));

                        $start_date = $start_date;
                        $end_date = $end_date;

                        $company_details[$i]["project_list"][$j]["start_date"] = $start_date;
                        $company_details[$i]["project_list"][$j]["due_date"] = $end_date;

                        $d1 = new DateTime($company_details[$i]["project_list"][$j]["start_date"]);
                        $d2 = new DateTime($company_details[$i]["project_list"][$j]["due_date"]);
                        if ($d2 > $current) {
                            $d2 = $current;
                        }

                        $diff = date_diff($d1, $d2);

                        $data = [];
                        $data["idle_start_date"] = $final_e;
                        $data["idle_due_date"] = $final_s;
                        $data["idle_days"] = $idle_diff->format("%a");
                        $data["idle_days_duration"] = $this->daysToYearConversion($idle_diff->format("%a"));

                        array_push($idle_info, $data);
                        $company_details[$i]["project_list"][$j]["days"] = ($diff->format("%a") + 1);
                        array_push($new_array, $company_details[$i]["project_list"][$j]);
                    } else {
                        if ($end_date > $prev_end_date) {
                            $time = strtotime($prev_end_date);
                            $final = date("Y-m-d", strtotime("+1 day", $time));

                            $start_date1 = $final; //+ 1 day;
                            $end_date = $end_date;

                            $company_details[$i]["project_list"][$j]["start_date"] = $start_date1;
                            $company_details[$i]["project_list"][$j]["due_date"] = $end_date;

                            $d1 = new DateTime($company_details[$i]["project_list"][$j]["start_date"]);
                            $d2 = new DateTime($company_details[$i]["project_list"][$j]["due_date"]);
                            $current = new DateTime();
                            if ($d2 > $current) {
                                $d2 = $current;
                            }
                            // $diff = $d1->diff($d2)->m + ($d1->diff($d2)->y * 12);
                            $diff = date_diff($d1, $d2);

                            $company_details[$i]["project_list"][$j]["days"] = ($diff->format("%a") + 1);
                            array_push($new_array, $company_details[$i]["project_list"][$j]);

                            $company_details[$i]["project_list"][$j]["start_date"] = $start_date;
                        } else {

                        }
                    }
                }
                if ($j == ($count1 - 1)) {
                    if (array_key_exists("comp_due_date", $company_details[$i])) {
                        $data = end($new_array);
                        $prev_start_date_proj = $data["start_date"];
                        $prev_end_date_proj = $data["due_date"];

                        $prev_end_date = $company_details[$i]["comp_due_date"];
                        $start_date = $company_details[$i]["project_list"][$j]["start_date"];
                        $end_date = $company_details[$i]["project_list"][$j]["due_date"];

                        if ($start_date == $prev_start_date_proj) {
                            if ($prev_end_date_proj > $end_date) {
                                $end_date = $prev_end_date_proj;
                            }
                        } else {
                            if ($prev_end_date_proj > $end_date) {
                                $end_date = $prev_end_date_proj;
                            }
                        }

                        //$comp_start_date = $company_details[$i]["comp_start_date"];
                        if ($prev_end_date) {
                            if ($prev_end_date > $end_date) {
                                $idle_start_date = $prev_end_date;
                                $idle_end_date = $end_date;

                                $time1 = strtotime($idle_start_date);
                                //$final_s = date("Y-m-d", strtotime("-1 day", $time1));
                                $final_s = date("Y-m-d", $time1);

                                $time2 = strtotime($idle_end_date);
                                $final_e = date("Y-m-d", strtotime("+1 day", $time2));
                                //$final_e = date("Y-m-d", $time2);
                                //$company_details[$i]["project_list"][$j]["idle_start_date1"] = $final_e;
                                //$company_details[$i]["project_list"][$j]["idle_due_date1"] = $final_s;

                                $d1 = new DateTime($final_s);
                                $d2 = new DateTime($final_e);

                                $current = new DateTime();
                                if ($d2 > $current) {
                                    $d2 = $current;
                                }

                                $idle_diff = date_diff($d1, $d2);

                                $data = [];
                                $data["idle_start_date"] = $final_e;
                                $data["idle_due_date"] = $final_s;
                                $data["idle_days"] = $idle_diff->format("%a");
                                $data["idle_days_duration"] = $this->daysToYearConversion($idle_diff->format("%a"));

                                array_push($idle_info, $data);
                                //$company_details[$i]["project_list"][$j]["idle_days1"] = $idle_diff->format("%a");
                                //$company_details[$i]["project_list"][$j]["idle_days_duration1"] = $this->daysToYearConversion($idle_diff->format("%a"));
                            }
                        }
                    }

                }

                $company_details[$i]["project_list"][$j]["idle_info"] = $idle_info;
            }
            $total = 0;
            $total_idle = 0;
            $total_duration = 0;
            // print_r($new_array);
            //die();
            foreach ($new_array as $k2 => $v2) {
                $total = $total + $v2["days"];
                if (isset($v2['idle_days'])) {
                    $total_idle = $total_idle + $v2["idle_days"];
                }
                $total_duration = $total + $total_idle;
            }

            foreach ($idle_info as $k3 => $v3) {
                $total_idle = $total_idle + $v3["idle_days"];
                $total_duration = $total + $total_idle;
            }

            $total = $this->daysToYearConversion($total);
            $total_duration = $this->daysToYearConversion($total_duration);
            $total_idle = $this->daysToYearConversion($total_idle);

            $company_details[$i]["total"] = $total;
            $company_details[$i]["total_idle"] = $total_idle;
            $company_details[$i]["total_duration"] = $total_duration;
        }
        return $company_details;
    }

    private static function date_sort($a, $b)
    {
        return strtotime($a["start_date"]) - strtotime($b["start_date"]);
    }

    public function curlCall($url)
    {
        $ch = curl_init($url);
        curl_setopt_array($ch, array(
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_HTTPHEADER => array(
                'Content-Type: application/json',
            ),
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_SSL_VERIFYHOST => false,
            CURLOPT_SSL_VERIFYPEER => false,
            CURLOPT_PROXY => false,
        ));

        // Send the request
        $response = curl_exec($ch);

        // Check for errors
        if ($response === false) {
            die(curl_error($ch));
        }
        return $response;
    }

    // Time format is UNIX timestamp or
    // PHP strtotime compatible strings
    public function dateDiff($time1, $time2, $precision = 6)
    {
        // If not numeric then convert texts to unix timestamps
        if (!is_int($time1)) {
            $time1 = strtotime($time1);
        }
        if (!is_int($time2)) {
            $time2 = strtotime($time2);
        }

        // If time1 is bigger than time2
        // Then swap time1 and time2
        if ($time1 > $time2) {
            $ttime = $time1;
            $time1 = $time2;
            $time2 = $ttime;
        }

        // Set up intervals and diffs arrays
        $intervals = array('year', 'month', 'day', 'hour', 'minute', 'second');
        $diffs = array();

        // Loop thru all intervals
        foreach ($intervals as $interval) {
            // Create temp time from time1 and interval
            $ttime = strtotime('+1 ' . $interval, $time1);
            // Set initial values
            $add = 1;
            $looped = 0;
            // Loop until temp time is smaller than time2
            while ($time2 >= $ttime) {
                // Create new temp time from time1 and interval
                $add++;
                $ttime = strtotime("+" . $add . " " . $interval, $time1);
                $looped++;
            }

            $time1 = strtotime("+" . $looped . " " . $interval, $time1);
            $diffs[$interval] = $looped;
        }

        $count = 0;
        $times = array();
        // Loop thru all diffs
        foreach ($diffs as $interval => $value) {
            // Break if we have needed precission
            if ($count >= $precision) {
                break;
            }
            // Add value and interval
            // if value is bigger than 0
            if ($value > 0) {
                // Add s if value is not 1
                if ($value != 1) {
                    $interval .= "s";
                }
                // Add value and interval to times array
                $times[] = $value . " " . $interval;
                $count++;
            }
        }

        // Return string with times
        return implode(", ", $times);
    }

    public function convertDaysToYearMonthAndDays($total_days, $start_date)
    {
        $months_arr_leap = [31, 29];
        $months_arr = [31, 28, 31, 30, 31, 30, 31, 31, 30, 31, 30, 31];

        $year = date('Y', strtotime($start_date));
        $month = date('m', strtotime($start_date));
        // $month = (int)$month;
        // $month = $month - 1;
        $day = date('d', strtotime($start_date));
        // $day = (int)$day;

        $is_leap_year = $this->is_leap_year($year);

        $day_count = 0;
        $month_count = 0;
        $year_count = 0;
        $remaining_days = $total_days;

        // if($day > 1){
        //     $day_count = $months_arr[$month] - $day;
        //     $month = $month +1;
        // }
        print_r("remaining_days");
        print_r($remaining_days);
        // echo $total_days->format('%m month, %d days');
        $number = $this->days_in_month($month, $year); // 31
        echo "There were {$number} days";

        // while($remaining_days >= 31) {
        // $remaining_days = $remaining_days - 30;
        // for($i = 0; $i < count($months_arr); $i++){
        // $remaining_days = $total_days - $months_arr[$i];
        //      $month_count++;

        //      if($month_count == 12){
        //          $month_count = 0;
        //          $year_count++;
        //      }
        // print_r("remaining_days");
        // print_r($remaining_days);

        //      print_r("year_count");
        //      print_r($year_count);

        //      print_r("month_count");
        //      print_r($month_count);

        //      print_r("day_count");
        //      print_r($day_count);

        //      // if($remaining_days <= 31){
        //      //     $day_count += $remaining_days;
        //      //     if($day_count >= 30){
        //      //         $day_count = $day_count - 30;
        //      //         $month_count++;
        //      //         if($month_count == 12){
        //      //             $month_count = 0;
        //      //             $year_count++;
        //      //         }
        //      //     }
        //      //     return;
        //      // }
        //     }
        // }

        // $fgdfg = (($year_count > 0) ? $year_count . ' Year' . ($year_count > 1 ? 's' : '') . ' ' : '') . (($month_count > 0) ? $month_count . ' Month' . ($month_count > 1 ? 's' : '') . ' ' : '') . (($day_count > 0) ? $day_count . ' Day' . ($day_count > 1 ? 's' : '') : '');
        // print_r($fgdfg);
        die();
    }

    public function is_leap_year($year)
    {
        return ((($year % 4) == 0) && ((($year % 100) != 0) || (($year % 400) == 0)));
    }

    public function days_in_month($month, $year)
    {
        // calculate number of days in a month
        return $month == 2 ? ($year % 4 ? 28 : ($year % 100 ? 29 : ($year % 400 ? 28 : 29))) : (($month - 1) % 7 % 2 ? 30 : 31);
    }

    /**
     * Kalyani : Check the given mail address and send the mail to reset password
     */
    public function forgetPassword()
    {
        $posted_data = Input::all();
        $userEmailAddress = $posted_data["email"];

        $user = User::where("email", $userEmailAddress)->first();

        if ($user != null) {
            //get random token number
            $posted_data['token'] = Utilities::generateRandomToken();

            //get expiry time of the token
            $posted_data['expiry'] = Utilities::addTimeIntoCurrentTime(date("h:i:s"), '+', 1);

            //set token and expiry in user table
            User::where("email", $userEmailAddress)
                ->update($posted_data);

            $subject_name = 'Reset your Syslogyx account password';

            $mail_body = "<h3>Password Reset Request</h3> </br> Dear Syslogyx User,</br></br>
                We have received your request to reset your password. Please click the link below to complete the reset request: </br></br>" . $posted_data['token'];

            MailUtility::sendMail($subject_name, $mail_body, array('kalyani@syslogyx.com'));

            return $this->dispatchResponse(200, Config::get('constants.SUCCESS_MESSAGES.MAIL_SEND'), null);
        } else {
            return $this->dispatchResponse(Config::get('constants.ERROR_CODE'), Config::get('constants.ERROR_MESSAGES.UNREGISTERED_MAIL_ADDREE'), null);
        }
    }

    /**
     * Kalyani : Check password expiry
     */
    public function passwordExpiry(Request $request)
    {
        $token = $request['token'];
        $currentTime = date('h:i:s');

        $user = User::where([["expiry", ">", $currentTime]])->get();

        if ($user->first()) {
            return $this->dispatchResponse(Config::get('constants.SUCESS_CODE'), Config::get('constants.SUCESS_MSG'), null);
        } else {
            return $this->dispatchResponse(Config::get('constants.ERROR_CODE'), Config::get('constants.ERROR_MESSAGES.MAIL_ADDRESS_EXPIRED'), null);
        }
    }

    /**
     * Kalyani : reset password of the given token user
     */
    public function resetPassword()
    {
        $posted_data = Input::all();
        $userToken = $posted_data["token"];

        $user = User::where("token", $userToken)->first();

        if ($user != null) {
            $user->update($posted_data);
            return $this->dispatchResponse(Config::get('constants.SUCESS_CODE'), Config::get('constants.SUCCESS_MESSAGES.RESET_PASSOWRD'), null);
        } else {
            return $this->dispatchResponse(Config::get('constants.ERROR_CODE'), Config::get('constants.ERROR_MESSAGES.INVALID_TOKEN'), null);
        }
    }

    /**
     * PDF generation for resource details.
     */
    public function generatePDFForResources($id)
    {
        $user = User::with("userTechnologyMapping", "userTechnologyMapping.technology", "userTechnologyMapping.domain")->find((int) $id);
        if ($user) {
            $users = [];
            array_push($users, $user);
            $this->calculateTechnologyWiseExperience($users);
            $exp = explode('.', $user->total_experience);
            $yr = $exp[0] > 1 ? $exp[0] . " years" : $exp[0] . " year";
            $mo = "";
            if (count($exp) > 1) {
                $mo = $exp[1] > 1 ? $exp[1] . " months" : $exp[1] . " month";
            }
            $user["total_experience"] = $yr . " " . $mo;
            $user["report_date"] = date("d-m-Y");

            // }} {{}} {{explode('.',$user->total_experience)[1]}} {{explode('.',$user->total_experience)[1] > 1 ? "months" : "month"}}

            // $sql = "SELECT p.* FROM `projects` as p LEFT JOIN `project_resources` as pr ON p.id = pr.project_id WHERE pr.user_id = ".$id." OR p.user_id = ".$id." GROUP BY p.id";

            // $project_data = DB::select($sql);

            // $project_data = json_decode(json_encode($project_data), True);

            $project_data = Project::select('projects.name', 'projects.id', DB::raw('count(tasks.id) AS total_tasks'), DB::raw('SUM(tasks.estimated_time) AS total_estimation, SEC_TO_TIME( SUM( TIME_TO_SEC ( tasks.spent_time ))) AS total_spent'))
                ->join('project_resources', 'projects.id', '=', 'project_resources.project_id')
                ->join('tasks', 'tasks.assigned_to', '=', 'project_resources.id')
            // ->where('projects.user_id',$id)
            // ->orWhere('projects.lead_id',$id)
                ->Where('project_resources.user_id', $id)
                ->groupBy('projects.id')
                ->groupBy('projects.name')
                ->orderBy('projects.name', 'asc')
                ->get();

            // return $project_data;

            // foreach ($project_data as $key => $value) {
            //    $milestoneList = Milestone::where([
            //         ['project_id', '=', $value->id]
            //     ])->get();

            //     $totalMilestoneCount = 0;
            //     $totalTaskCount = 0;
            //     $milestones = [];
            //     if ($milestoneList!=null && !$milestoneList->isEmpty()) {

            //         //get total count of milestones
            //         $totalMilestoneCount = count($milestoneList);

            //         foreach ($milestoneList as $key1 => $value1) {
            //             array_push($milestones, $value1->id);
            //         }

            //         //get total count of tasks
            //         if(count($milestones) != 0){
            //             $taskList = Task::whereIn('milestone_id', $milestones)
            //                 ->where('status_id', '<>', Config::get('constants.STATUS_CONSTANT.DELETED'))
            //                 ->get();
            //             $totalTaskCount = count($taskList);
            //         }
            //     }

            //     $project_data[$key]["total_milestones"] = $totalMilestoneCount;
            //     $project_data[$key]["total_tasks"] = $totalTaskCount;

            //     $sql = 'SUM(estimated_time) AS total_estimation, SEC_TO_TIME( SUM( TIME_TO_SEC ( spent_time ))) AS total_spent';

            //     $query_data = DB::table('tasks')
            //          ->select(DB::raw($sql))
            //          ->whereIn('milestone_id',$milestones)
            //          ->get();

            //     $project_data[$key]['total_estimation'] = $query_data[0]->total_estimation;
            //     $project_data[$key]['total_spent'] = $query_data[0]->total_spent;

            // }

            $user["project_data"] = $project_data;

            $now = new DateTime();
            // $now = $now->format('Y-m-d H:i:s');
            $now = $now->format('d-m-Y');
            view()->share(compact('user'));
            $pdf = PDF::loadView('report/resources');
            return $pdf->stream($user->name . '_' . $now . '.pdf');

            // return $user;
        }
    }

    /**
     *Sonal: Get manager and mentor list
     */
    public function getAllManagerOrLeadList($hrms_role_id)
    {
        $user = User::where('hrms_role_id', $hrms_role_id)->get();
        if ($user->first()) {
            return $this->dispatchResponse(200, "User List", $user);
        } else {
            return $this->dispatchResponse(201, "No Records Found!!", $user);
        }
    }
    /**
     *Sonal: Get User List having only role of admin/manager/lead
     */
    public function getUserListForTaskCretaion()
    {
        $role_id_array = [1, 2, 3, 4];
        $userIDArray = RoleUsers::whereIn('role_id', $role_id_array)->distinct('user_id')->pluck('user_id');
        $user = User::whereIn('id', $userIDArray)->orderBy('name', 'asc')->get();
        if ($user->first()) {
            return $this->dispatchResponse(200, "User List", $user);
        } else {
            return $this->dispatchResponse(201, "No Records Found!!", $user);
        }
    }
    /**
     *Sonal: Delete User by userId
     */
    public function deleteUser($user_id)
    {
        $UserAssignedProjectIDs = Project::where('user_id', '=', $user_id)->pluck('id');

        $technologyAssignedUserAssocIDs = UserTechnologyMapping::where('user_id', '=', $user_id)->pluck('id');

        $UserAssignedPrjResourceIDs = ProjectResource::where('user_id', '=', $user_id)->pluck('id');

        $UserAssignedMoMIDs = MomAttendees::where('user_id', '=', $user_id)->pluck('mom_id');

        if (count($UserAssignedProjectIDs) == 0 && count($technologyAssignedUserAssocIDs) == 0 && count($UserAssignedPrjResourceIDs) == 0 && count($UserAssignedMoMIDs) == 0) {

            $query = User::where([['id', '=', $user_id]])->delete();
            if ($query) {
                return $this->dispatchResponse(200, "User deleted Successfully...!!", null);
            }

        } else {
            return $this->dispatchResponse(201, "User is involved in Project/ MoM.", null);
        }
    }
}
