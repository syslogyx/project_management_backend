<?php

namespace App\Http\Controllers;

use App\ProjectCategoryMapping;
use App\ProjectCategoryTechnology;
use Illuminate\Support\Facades\Input;

class ProjectCategoryTechnologyController extends BaseController
{

    public function deleteProjectTechnology($project_category_id, $technology_id, $user_id = null)
    {

        $proj_domain = \App\ProjectCategoryMapping::select('project_id', 'category_id')
            ->where([
                ['id', '=', $project_category_id],
            ])
            ->first();
        if ($proj_domain) {
            $proj_res = \App\ProjectResource::select('id')
                ->where([
                    ['project_id', '=', $proj_domain->project_id],
                    ['domain_id', '=', $proj_domain->category_id],
                ])
                ->get();

            if ($proj_res->count() > 0) {

                if ($user_id > 0) {
                    $proj_res = \App\ProjectResource::select('id')
                        ->where([
                            ['project_id', '=', $proj_domain->project_id],
                            ['domain_id', '=', $proj_domain->category_id],
                            ['user_id', '=', $user_id],
                        ])
                        ->first();

                    if ($proj_res) {
                        $proj_res_tech = \App\ProjectResourceTechnology::where([["project_resource_id", "=", $proj_res["id"]], ["technology_id", "=", $technology_id]])->delete();
                        // $proj_res->delete();

                        $proj_cat_tech = ProjectCategoryTechnology::where([
                            ['project_category_id', '=', $project_category_id],
                            ['technology_id', '=', $technology_id],
                        ])->delete();

                        $proj_cat_tech_list = ProjectCategoryTechnology::where([
                            ['project_category_id', '=', $project_category_id],
                        ])->first();

                        if (!$proj_cat_tech_list) {
                            $model = ProjectCategoryMapping::find((int) $project_category_id);
                            $project_cat = $model->delete();
                        }

                        if ($proj_cat_tech) {
                            return $this->dispatchResponse(200, "Record Deleted Successfully...!!", null);
                        }
                    }

                }

                $proj_res_tech = null;
                $proj_res_tech_arr = [];
                foreach ($proj_res as $key => $value) {
                    $proj_res_tech = \App\ProjectResourceTechnology::where([["project_resource_id", "=", $value["id"]], ["technology_id", "=", $technology_id]])->first();
                    if ($proj_res_tech) {
                        array_push($proj_res_tech_arr, $proj_res_tech);
                    }

                }

                if (sizeof($proj_res_tech_arr) == 0) {

                    $proj_cat_tech = ProjectCategoryTechnology::where([
                        ['project_category_id', '=', $project_category_id],
                        ['technology_id', '=', $technology_id],
                    ])->delete();

                    $proj_cat_tech_list = ProjectCategoryTechnology::where([
                        ['project_category_id', '=', $project_category_id],
                    ])->first();

                    if (!$proj_cat_tech_list) {
                        $model = ProjectCategoryMapping::find((int) $project_category_id);
                        $project_cat = $model->delete();
                    }

                    if ($proj_cat_tech) {
                        return $this->dispatchResponse(200, "Record Deleted Successfully...!!", null);
                    }
                } else {
                    return $this->dispatchResponse(200, "Resource is assigned to this technology.", null);
                }
            } else {
                $proj_cat_tech = ProjectCategoryTechnology::where([
                    ['project_category_id', '=', $project_category_id],
                    ['technology_id', '=', $technology_id],
                ])->delete();

                $proj_cat_tech_list = ProjectCategoryTechnology::where([
                    ['project_category_id', '=', $project_category_id],
                ])->first();

                if (!$proj_cat_tech_list) {
                    $model = ProjectCategoryMapping::find((int) $project_category_id);
                    $project_cat = $model->delete();
                }

                if ($proj_cat_tech) {
                    return $this->dispatchResponse(200, "Record Deleted Successfully...!!", null);
                }
            }
        }
    }

    public function addProjectTechnology()
    {
        $posted_data = Input::all();
        $project_id = $posted_data["project_id"];
        $proj_cat_id = $posted_data["project_category_id"];
        $technologies = $posted_data["technology_id"];

        $object = new ProjectCategoryTechnology();

        $response = [];
        foreach ($technologies as $key => $value) {
            $data["project_category_id"] = $proj_cat_id;
            $data["technology_id"] = $value;
            $data["created_by"] = 1;
            $data["updated_by"] = 1;

            if ($object->validate($data)) {
                $model = ProjectCategoryTechnology::create($data);

                if (isset($model->id)) {
                    $response[] = $model->toArray();
                }
            }
        }

        return $this->dispatchResponse(200, "Record Added Successfully...!!", $response);
    }

    public function getProjectCategoriesTechnology($project_cat_id, $technology_id)
    {
        $model = ProjectCategoryTechnology::where([
            ['project_category_id', '=', $project_cat_id],
            ['technology_id', '=', $technology_id],
        ])->first();

        if ($model) {
            return $model;
        }

    }

}
