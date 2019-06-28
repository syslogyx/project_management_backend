<?php

namespace App\Http\Controllers;

use App\ProjectCategoryMapping;
use App\ProjectCategoryTechnology;

class ProjectCategoryMappingController extends BaseController
{

    public function index()
    {
        $model = ProjectCategoryMapping::with("project", "category")->paginate(200);
        if ($model->first()) {
            return $this->dispatchResponse(200, "", $model);
        } else {
            return $this->dispatchResponse(200, "No Records Found!!", $model);
        }
    }

    public function domains_of_project_id($project_id)
    {
        $model = ProjectCategoryMapping::with("category")->where([
            ['project_id', '=', $project_id],
        ])->get();
        if ($model) {
            return $model;
        }

    }

    public function deleteProjectCategory($project_category_id)
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

            if ($proj_res) {
                $proj_res_tech = null;
                foreach ($proj_res as $key => $value) {
                    $proj_res_tech = \App\ProjectResourceTechnology::where("project_resource_id", "=", $value["id"])->first();
                }
                if (!$proj_res_tech) {
                    $proj_cat_tech = ProjectCategoryTechnology::where([
                        ['project_category_id', '=', $project_category_id],
                    ])->delete();

                    if ($proj_cat_tech) {
                        $model = ProjectCategoryMapping::find((int) $project_category_id);
                        $project_cat = $model->delete();

                        if ($project_cat) {
                            return $this->dispatchResponse(200, "Record Deleted Successfully...!!", null);
                        }
                    }
                } else {
                    return $this->dispatchResponse(200, "Resource is assigned to this technology.", null);
                }
            }
        }
    }

}
