<?php

namespace App\Http\Controllers;

use App\Http\Transformers\UserTechnologyMappingTransformer;
use App\UserTechnologyMapping;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Input;

class UserTechnologyMappingController extends BaseController
{

    public function index(Request $request)
    {

        $page = $request->page;
        $limit = $request->limit;
        if (($page == null || $limit == null) || ($page == -1 || $limit == -1)) {
            $userTechnologyMapping = UserTechnologyMapping::with("technology", "user")->paginate(25);
        } else {
            $userTechnologyMapping = UserTechnologyMapping::with("technology", "user")->paginate($limit);
        }

        if ($userTechnologyMapping->first()) {
            return $this->dispatchResponse(200, "", $userTechnologyMapping);
            //            return $this->response->item($userTechnologyMapping, new UserTechnologyMapping())->setStatusCode(200);
        } else {
            return $this->dispatchResponse(200, "No Records Found!!", $userTechnologyMapping);
        }
    }

    public function create()
    {
        $posted_data = Input::all();
        $posted_data["created_by"] = 1;
        $posted_data["updated_by"] = 1;

        $technologyData = $posted_data["userTechnology"];

        $objectUserTechnologyMapping = new UserTechnologyMapping();

        if ($objectUserTechnologyMapping->validate($posted_data)) {
            if ($technologyData != null || !empty($technologyData)) {
                $userId = $posted_data["user_id"];
                foreach ($technologyData as $key => $value) {

                    $is_technology_exist = $this->checkUserTechnology($userId, $technologyData[$key]["technology_id"], $technologyData[$key]["domain_id"]);

                    $data = [];
                    $data["user_id"] = $userId;
                    $data["technology_id"] = $technologyData[$key]["technology_id"];
                    $data["domain_id"] = $technologyData[$key]["domain_id"];
//                    $durationInMonths = $this->calculateDurationInMonths($technologyData[$key]["duration_years"], $technologyData[$key]["duration_months"]);
                    //                    $data["duration_in_month"] = $durationInMonths;
                    $data["created_by"] = 1;
                    $data["updated_by"] = 1;

                    if (!$is_technology_exist) {
                        $model = UserTechnologyMapping::create($data);
                        $update_data = false;
                    } else {
                        $model = UserTechnologyMapping::find((int) $is_technology_exist);
                        $user_technology_mapping = $model->update($data);
                        $update_data = true;
                        continue;
                    }
                }

                if ($model) {
                    if ($update_data) {
                        $model["updated_data"] = "true";

                        return $this->response->item($model, new UserTechnologyMappingTransformer())->setStatusCode(200);
                    } else {
                        return $this->response->item($model, new UserTechnologyMappingTransformer())->setStatusCode(200);
                    }
                } else {
                    throw new \Dingo\Api\Exception\StoreResourceFailedException('Unable to create user technology.', $objectUserTechnologyMapping->errors());
                }
            }
        } else {
            throw new \Dingo\Api\Exception\StoreResourceFailedException('Unable to create  user technology.', $objectUserTechnologyMapping->errors());
        }
    }

    public function update($id)
    {
        $posted_data = Input::all();

        $model = UserTechnologyMapping::find((int) $id);

        if ($model->validate($posted_data)) {
            $durationInMonths = $this->calculateDurationInMonths($posted_data["duration_years"], $posted_data["duration_months"]);
            unset($posted_data["duration_years"]);
            unset($posted_data["duration_months"]);
            $posted_data["duration_in_month"] = $durationInMonths;
            if ($model->update($posted_data)) {
                return $this->response->item($model, new UserTechnologyMappingTransformer())->setStatusCode(200);
            }

        } else {
            throw new \Dingo\Api\Exception\StoreResourceFailedException('Unable to update user technology.', $model->errors());
        }
    }

    public function view($id)
    {
        $userTechnologyMapping = UserTechnologyMapping::with("technology", "user", "domain")->find((int) $id);
        if ($userTechnologyMapping) {
            return $this->response->item($userTechnologyMapping, new UserTechnologyMappingTransformer())->setStatusCode(200);
        }

    }

    // Calculate duration in months from year & month
    public function calculateDurationInMonths($years, $months)
    {
        $durationInMonths = ($years * 12) + $months;
        return $durationInMonths;
    }

    public function checkUserTechnology($userId, $technologyId, $domainId)
    {
        $model = UserTechnologyMapping::where([
            ['user_id', '=', $userId],
            ['technology_id', '=', $technologyId],
            ['domain_id', '=', $domainId],
        ])->first();
        if ($model) {
            return $model->id;
        }

    }

    public function delete($id)
    {
        if ($id) {
            UserTechnologyMapping::find($id)->delete();
            return $this->dispatchResponse(200, "UserTechnologyMapping deleted successfully!!");
        }
    }

    public function getUserListByTechnology($id, Request $request)
    {
        if ($id) {
            $page = $request->page;
            $limit = $request->limit;
            if (($page == null || $limit == null) || ($page == -1 || $limit == -1)) {
                $model = UserTechnologyMapping::where([
                    ['technology_id', '=', $id],
                ])->with("user")->paginate(25);
            } else {
                $model = UserTechnologyMapping::where([
                    ['technology_id', '=', $id],
                ])->with("user")->paginate($limit);
            }

            if ($model) {
                return $this->dispatchResponse(200, "", $model);
            }

        }
    }

    public function getUserListByTechnologies()
    {
        $tech_id = Input::get("tech_id");
        $domain_id = Input::get("domain_id");
//        $this->pp($user_list);
        //        die();
        if ($tech_id) {
//            $query = "SELECT a.user_id,b.* FROM `user_technology_mapping` a " .
            //                    "INNER JOIN users b ON (a.user_id = b.id)" .
            //                    "WHERE a.technology_id IN (" . implode(",", $tech_id) . ") " .
            //                    "GROUP BY a.user_id";

            $query = "SELECT * FROM users WHERE id IN " .
            "(SELECT a.user_id FROM `user_technology_mapping` AS a " .
            "WHERE a.domain_id = " . $domain_id . " AND a.technology_id IN (" . implode(",", $tech_id) . ") " .
            "GROUP BY a.user_id HAVING COUNT(a.user_id) = " . count($tech_id) . " )";

            $user_list = DB::select($query);

//            $model = UserTechnologyMapping::whereIn('technology_id', $tech_id)->with("user")->get();
            if ($user_list) {
                return $this->dispatchResponse(200, "", $user_list);
            } else {
                return $this->dispatchResponse(200, "No Resocrd Founf", null);
            }
        }
    }

    public function getTechnologyListByUser($id, Request $request)
    {
        if ($id) {
            $page = $request->page;
            $limit = $request->limit;
            if (($page == null || $limit == null) || ($page == -1 || $limit == -1)) {
                $model = UserTechnologyMapping::where([
                    ['user_id', '=', $id],
                ])->with("technology")->paginate(25);
            } else {
                $model = UserTechnologyMapping::where([
                    ['user_id', '=', $id],
                ])->with("technology")->paginate($limit);
            }

            if ($model) {
                return $this->dispatchResponse(200, "", $model);
            }

        }
    }

    public function getTechnologyListOfUser($id, $domain_id)
    {
        if ($id) {
            $model = UserTechnologyMapping::where([
                ['user_id', '=', $id],
                ['domain_id', '=', $domain_id],
            ])->with("technology")->get();
            if ($model) {
                return $model;
            }

        }
    }

    public function getDomainListByUser($id)
    {
        if ($id) {
            $domain = UserTechnologyMapping::select('domain_id')
                ->where([
                    ['user_id', '=', $id],
                ])->groupBy("domain_id")->get();
            if ($domain) {
                $model = \App\Category::whereIn('id', $domain)->get();
                if ($model) {
                    return $model;
                }

            }
        }
    }

}
