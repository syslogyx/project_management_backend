<?php

namespace App\Http\Controllers;

use App\CategoriesTechnologyMapping;
use App\Http\Transformers\TechnologyTransformer;
use App\Technology;
use App\UserTechnologyMapping;
use Config;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Input;

class TechnologyController extends BaseController
{

    public function index(Request $request)
    {

        $page = $request->page;
        $limit = $request->limit;
        if (($page == null || $limit == null) || ($page == -1 || $limit == -1)) {
            $technologies = Technology::orderBy('name', 'asc')->with('parent', 'children')->paginate(250);
        } else {
            $technologies = Technology::orderBy('name', 'asc')->with('parent', 'children')->paginate($limit);
        }

        if ($technologies->first()) {
            return $this->dispatchResponse(200, "", $technologies);
        } else {
            return $this->dispatchResponse(Config::get('constants.SUCESS_CODE'), "No Records Found!!", null);
        }

    }

    public function create()
    {
        $posted_data = Input::all();
        $posted_data["created_by"] = 1;
        $posted_data["updated_by"] = 1;

        $objectTechnology = new Technology();

        if (!isset($posted_data["parent_id"])) {
            $posted_data["parent_id"] = null;
        }
        if ($objectTechnology->validate($posted_data)) {
            $model = Technology::create($posted_data);
            return $this->response->item($model, new TechnologyTransformer())->setStatusCode(200);
        } else {
            throw new \Dingo\Api\Exception\StoreResourceFailedException('Unable to create  technology.', $objectTechnology->errors());
        }
    }

    public function update($id)
    {
        $posted_data = Input::all();
        $model = Technology::find((int) $id);

        if ($model->validate($posted_data)) {
            if ($model->update($posted_data)) {
                return $this->response->item($model, new TechnologyTransformer())->setStatusCode(200);
            }

        } else {
            throw new \Dingo\Api\Exception\StoreResourceFailedException('Unable to update  technology.', $model->errors());
        }
    }

    public function view($id)
    {
        $technology = Technology::find((int) $id);
        if ($technology) {
            return $this->response->item($technology, new TechnologyTransformer())->setStatusCode(200);
        }

    }

    public function deleteTechnology($technology_id)
    {
        $technologyAssignedCatAssocIDs = CategoriesTechnologyMapping::where('technology_id', '=', $technology_id)->pluck('id');

        $technologyAssignedUserAssocIDs = UserTechnologyMapping::where('technology_id', '=', $technology_id)->pluck('id');

        if (count($technologyAssignedCatAssocIDs) == 0 && count($technologyAssignedUserAssocIDs) == 0) {
            $query = Technology::where([['id', '=', $technology_id]])->delete();
            if ($query) {
                return $this->dispatchResponse(200, "Technology deleted Successfully...!!", null);
            }

        } else {
            return $this->dispatchResponse(201, "Technology is assigned to category.", null);
        }
    }

}
