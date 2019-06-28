<?php

namespace App\Http\Controllers;

use App\Http\Transformers\TechnicalSupportTransformer;
use App\TechnicalSupport;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Input;

class TechnicalSuportController extends BaseController
{

    public function index(Request $request)
    {

        $page = $request->page;
        $limit = $request->limit;
        if (($page == null || $limit == null) || ($page == -1 || $limit == -1)) {
            $model = TechnicalSupport::with('task', 'user')->paginate(25);
        } else {
            $model = TechnicalSupport::with('task', 'user')->paginate($limit);
        }

        if ($model->first()) {
            return $this->dispatchResponse(200, "", $model);
            //            return $this->response->item($model, new TechnicalSupportTransformer())->setStatusCode(200);
        } else {
            return $this->dispatchResponse(200, "No Records Found!!", $model);
        }

    }

    public function create()
    {
        $posted_data = Input::all();
        $posted_data["created_by"] = 1;
        $posted_data["updated_by"] = 1;

        $object = new TechnicalSupport();

        if ($object->validate($posted_data)) {
            $model = TechnicalSupport::create($posted_data);
            return $this->response->item($model, new TechnicalSupportTransformer())->setStatusCode(200);
        } else {
            throw new \Dingo\Api\Exception\StoreResourceFailedException('Unable to create Technical Support.', $object->errors());
        }
    }

    public function update($id)
    {
        $posted_data = Input::all();

        $model = TechnicalSupport::find((int) $id);

        if ($model->validate($posted_data)) {
            if ($model->update($posted_data)) {
                return $this->response->item($model, new TechnicalSupportTransformer())->setStatusCode(200);
            }

        } else {
            throw new \Dingo\Api\Exception\StoreResourceFailedException('Unable to update Technical Support.', $model->errors());
        }
    }

    public function view($id)
    {
        $model = TechnicalSupport::find((int) $id);
        if ($model) {
            return $this->response->item($model, new TechnicalSupportTransformer())->setStatusCode(200);
        }

    }

}
