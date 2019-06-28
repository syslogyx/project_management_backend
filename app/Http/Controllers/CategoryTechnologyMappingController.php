<?php

namespace App\Http\Controllers;

use App\CategoriesTechnologyMapping;
use App\ProjectCategoryTechnology;
use Illuminate\Http\Request;

class CategoryTechnologyMappingController extends BaseController
{
    public function listTechnologyCategoryWise($categoryId, Request $request)
    {

        $page = $request->page;
        $limit = $request->limit;
        if (($page == null || $limit == null) || ($page == -1 || $limit == -1)) {
            $model = CategoriesTechnologyMapping::where([
                ['category_id', '=', $categoryId],
            ])->with("technology")->paginate(25);
        } else {
            $model = CategoriesTechnologyMapping::where([
                ['category_id', '=', $categoryId],
            ])->with("technology")->paginate($limit);
        }

        if ($model) {
            return $this->dispatchResponse(200, "", $model);
        }

    }

    public function listProjectDomainWiseTechnologies($proj_categoryId)
    {
        $model = ProjectCategoryTechnology::where([
            ['project_category_id', '=', $proj_categoryId],
        ])->with("technology")->get();
        if ($model) {
            return $model;
        }

    }
}
