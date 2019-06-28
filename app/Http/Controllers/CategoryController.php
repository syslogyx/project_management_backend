<?php

namespace App\Http\Controllers;

use App\CategoriesTechnologyMapping;
use App\Category;
use App\Http\Transformers\CategoryTransformer;
use App\ProjectCategoryMapping;
use App\UserTechnologyMapping;
use Config;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Input;

class CategoryController extends BaseController
{

    public function index(Request $request)
    {
        $page = $request->page;
        $limit = $request->limit;
        if (($page == null || $limit == null) || ($page == -1 || $limit == -1)) {
            $categories = Category::where('id', '<>', '1')->orderBy('name', 'asc')->with("technology", "project")->paginate(25);
        } else {
            $categories = Category::where('id', '<>', '1')->orderBy('name', 'asc')->with("technology", "project")->paginate($limit);
        }

        if ($categories->first()) {
            return $this->dispatchResponse(200, "", $categories);
            //            return $this->response->item($categories, new Category())->setStatusCode(200);
        } else {
            return $this->dispatchResponse(Config::get('constants.SUCESS_CODE'), "No Records Found!!", null);
        }
    }

    public function create()
    {
        $posted_data = Input::all();
        $posted_data["created_by"] = 1;
        $posted_data["updated_by"] = 1;

        $objectCategory = new Category();

        if ($objectCategory->validate($posted_data)) {
            $technology_data = $posted_data["technology"];
            unset($posted_data["technology"]);
            $model = Category::create($posted_data);

            if ($model) {
                $category_id = $model->id;

                if ($technology_data != null) {
                    foreach ($technology_data as $key => $value) {
                        $data = [];
                        $data["technology_id"] = $value;
                        $data["category_id"] = $category_id;
                        $data["created_by"] = 1;
                        $data["updated_by"] = 1;

                        CategoriesTechnologyMapping::create($data);
                    }
                }
            }
            return $this->response->item($model, new CategoryTransformer())->setStatusCode(200);
        } else {
            throw new \Dingo\Api\Exception\StoreResourceFailedException('Unable to create category.', $objectCategory->errors());
        }
    }

    public function update($id)
    {
        $posted_data = Input::all();
        $model = Category::with("technology", "project")->find((int) $id);

        if ($model) {

            $techArr = $this->getTechnologyData($model["technology"]);

            $is_tech_assign_to_proj = \App\ProjectCategoryTechnology::select('technology_id')
                ->whereIn("technology_id", $techArr)->distinct()->get();

            $is_tech_assign_to_user = \App\UserTechnologyMapping::select('technology_id')
                ->whereIn("technology_id", $techArr)->distinct()->get();

            $is_tech_assign_to_proj = $this->getTechnologyId($is_tech_assign_to_proj);
            $is_tech_assign_to_user = $this->getTechnologyId($is_tech_assign_to_user);

            $result_merge = array_unique(array_merge($is_tech_assign_to_proj, $is_tech_assign_to_user));

            // $result = array_intersect($techArr, $posted_data["technology"]);

            $result_merge = array_values($result_merge);

            if (isset($posted_data["technology"])) {
                $count = 0;
                for ($i = 0; $i < count($result_merge); $i++) {
                    for ($j = 0; $j < count($posted_data["technology"]); $j++) {
                        if ($result_merge[$i] == $posted_data["technology"][$j]) {
                            $count = $count + 1;
                        }
                        continue;
                    }
                }
                if ($count != count($result_merge)) {
                    $tech = \App\Technology::whereIn("id", $result_merge)->get();

                    $tech_name = $this->getTechnologyName($tech);

                    return $this->dispatchResponse(400, implode(', ', $tech_name) . " - technologies are already assigned to any Project or User.", $tech);
                    // return $this->response->item($model, new CategoryTransformer())->setStatusCode(200);
                } else {
                    if ($techArr != null) {
                        foreach ($techArr as $key => $value) {
                            $whereArray = array('category_id' => $id, 'technology_id' => $value);
                            CategoriesTechnologyMapping::where($whereArray)->delete();
                        }
                    }
                }
            } else {
                if (count($result_merge) > 0) {

                    $tech = \App\Technology::whereIn("id", $result_merge)->get();

                    $tech_name = $this->getTechnologyName($tech);

                    return $this->dispatchResponse(400, implode(', ', $tech_name) . " - technologies are already assigned to any Project or User.", $tech);
                }
            }
        }

        $objectCategory = new Category();

        if ($objectCategory->validate($posted_data)) {

            if (isset($posted_data["technology"])) {
                $technology_data = $posted_data["technology"];
                unset($posted_data["technology"]);
                if ($model->update($posted_data)) {
                    if ($technology_data != null) {
                        $category_id = $id;
                        foreach ($technology_data as $key => $value) {
                            $data = [];
                            $data["technology_id"] = $value;
                            $data["category_id"] = $category_id;
                            $data["created_by"] = 1;
                            $data["updated_by"] = 1;
                            CategoriesTechnologyMapping::create($data);
                        }
                    }
                    return $this->response->item($model, new CategoryTransformer())->setStatusCode(200);
                }
                // $technology_data1 = array_intersect($technology_data, $techArr);
            } else {
                if ($model->update($posted_data)) {
                    $data = CategoriesTechnologyMapping::where('category_id', $id)->delete();
                    if ($data) {
                        return $this->response->item($model, new CategoryTransformer())->setStatusCode(200);
                    }

                }
            }
        } else {
            throw new \Dingo\Api\Exception\StoreResourceFailedException('Unable to update category.', $objectCategory->errors());
        }
    }

    public function view($id)
    {
        $category = Category::with("technology", "project")->find((int) $id);
        if ($category) {
            return $this->response->item($category, new CategoryTransformer())->setStatusCode(200);
        }

    }

    public function getTechnologyData($technologyArr)
    {

        $techArr = [];
        for ($i = 0; $i < count($technologyArr); $i++) {
            if ($technologyArr[$i]['id']) {
                $techArr[] = $technologyArr[$i]['id'];
            }
        }
        return $techArr;
    }

    public function getTechnologyName($technologyArr)
    {

        $techArr = [];
        for ($i = 0; $i < count($technologyArr); $i++) {
            if ($technologyArr[$i]['name']) {
                $techArr[] = $technologyArr[$i]['name'];
            }
        }
        return $techArr;
    }

    public function getTechnologyId($technologyArr)
    {

        $techArr = [];
        for ($i = 0; $i < count($technologyArr); $i++) {
            if ($technologyArr[$i]['technology_id']) {
                $techArr[] = $technologyArr[$i]['technology_id'];
            }
        }
        return $techArr;
    }

    public function listTechnologyCategoryWise($categoryId)
    {
        $model = CategoriesTechnologyMapping::where([
            ['category_id', '=', $categoryId],
        ])->with("technology")->get();
        if ($model) {
            return $model;
        }

    }

    public function listTechnologyMultipleCategoryWise()
    {
        $category_id = Input::get("id");
        if (!$category_id) {
            $model = \App\Technology::orderBy('name', 'asc')->with('parent', 'children')->get();

            if ($model) {
                return $model;
            }

        } else {
            $model = CategoriesTechnologyMapping::whereIn('category_id', $category_id)
                ->with("technology")->get();

            if ($model) {
                return $model;
            }

        }
    }

    public function categoryListWithResourcesCount()
    {
        // $categories = Category::orderBy('name', 'asc')->with("technology", "project",'user')->get();
        $categories = Category::where('id', '<>', '1')->orderBy('name', 'asc')->get();
        if (count($categories) > 0) {
            foreach ($categories as $key => $value) {
                $categories[$key]["count"] = UserTechnologyMapping::where('domain_id', $value->id)->distinct('user_id')->count('user_id');
                $user_name = [];
                $user_ids = [];
                if (count($value->user) > 0) {

                    foreach ($value->user as $key1 => $value1) {
                        array_push($user_name, $value1->name);
                        array_push($user_ids, $value1->id);
                    }
                }
                $categories[$key]["user_name"] = $user_name;
                $categories[$key]["user_ids"] = $user_ids;
            }
        }
        return $this->dispatchResponse(200, "Data.", $categories);
    }

    public function deleteCategory($category_id)
    {
        $categoryAssignedPrjCatAssocIDs = ProjectCategoryMapping::where('category_id', '=', $category_id)->pluck('id');
        $technologyAssignedCatAssocIDs = CategoriesTechnologyMapping::where('category_id', '=', $category_id)->pluck('id');

        if (count($categoryAssignedPrjCatAssocIDs) == 0 && count($technologyAssignedCatAssocIDs) == 0) {
            $query = Category::where([['id', '=', $category_id]])->delete();
            if ($query) {
                return $this->dispatchResponse(200, "Domain deleted Successfully...!!", null);
            }

        } else {
            return $this->dispatchResponse(201, "Domain is assigned to project/technology.", null);
        }
    }

}
