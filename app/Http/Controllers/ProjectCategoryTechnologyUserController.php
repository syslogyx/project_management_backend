<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\ProjectCategoryTechnologyUser;
use App\ProjectCategoryTechnology;
use Illuminate\Support\Facades\Input;
use App\Http\Transformers\ProjectCategoryTechnologyUserTransformer;

class ProjectCategoryTechnologyUserController extends Controller {

//    function create() {
//        $posted_data = Input::all();
//
//        if ($posted_data) {
//            $model = ProjectCategoryTechnology::where([
//                        ['project_category_id', '=', $posted_data['project_cat_id']],
//                        ['technology_id', '=', $posted_data['technology_id']]
//                    ])->first();
//
//            $object = new ProjectCategoryTechnologyUser();
//            // print_r($model['id']);die;
//            if ($model['id']) {
//                $isPresentProjCatTecUser = $this->getPresentProjCatTecUser($model['id'], $posted_data['user_id']);
//
//                // print_r($isPresentProjCatTecUser);die;
//                if (!$isPresentProjCatTecUser) {
//                    foreach ($posted_data['user_id'] as $key => $value) {
//                        $data = [];
//                        $data['project_technology_category_id'] = $model['id'];
//                        $data['user_id'] = $value;
//                        $data['created_by'] = 1;
//                        $data['updated_by'] = 1;
//
//                        $model = ProjectCategoryTechnologyUser::create($data);
//                    }
//
//                    return $this->response->item($model, new ProjectCategoryTechnologyUserTransformer())->setStatusCode(200);
//                } else {
//                    throw new \Dingo\Api\Exception\StoreResourceFailedException('Sorry!, Unable to create resource. Resource with this technology Already Exist.', $object->errors());
//                }
//            } else {
//                throw new \Dingo\Api\Exception\StoreResourceFailedException('Sorry!, Category Technology combination doesn\'t exist.', $object->errors());
//            }
//        }
//    }
//
//    function update($id) {
//        $posted_data = Input::all();
//
//        $model = ProjectCategoryTechnologyUser::find((int) $id);
//
//        if ($model) {
//            $isPresentProjCatTecUser = $this->getPresentProjCatTecUser($model['id'], $posted_data['user_id']);
//            if (!$isPresentProjCatTecUser) {
//                if ($model->update($posted_data))
//                    return $this->response->item($model, new ProjectCategoryTechnologyUserTransformer())->setStatusCode(200);
//            }
//        } else {
//            throw new \Dingo\Api\Exception\StoreResourceFailedException('Unable to update.', $model->errors());
//        }
//    }
//
//    function getPresentProjCatTecUser($ProjCatTecId, $users) {
//        $model = ProjectCategoryTechnologyUser::where([
//                    ['project_technology_category_id', '=', $ProjCatTecId]
//                ])->whereIN('user_id', $users)
//                ->first();
//
//        if ($model) {
//            //$data =  $this->response->item($model, new MilestoneTransformer())->setStatusCode(200);
//            return $model;
//        }
//    }

}
