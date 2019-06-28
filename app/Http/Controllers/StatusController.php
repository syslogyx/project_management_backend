<?php

namespace App\Http\Controllers;

use App\Http\Transformers\StatusTransformer;
use App\Status;
use App\Utilities;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Input;

class StatusController extends BaseController
{

    public function index(Request $request)
    {

        $type = $request->type;
        return $this->dispatchResponse(200, "", Utilities::getStatusList($type));
        /* $status = Status::orderBy('name', 'asc')->with('activityType')->paginate(200);
    if ($status->first()) {
    return $this->dispatchResponse(200, "", $status);
    //            return $this->response->item($status, new StatusTransformer())->setStatusCode(200);
    } else {
    return $this->dispatchResponse(200, "No Records Found!!", $status);
    }*/

    }

    public function create()
    {
        $posted_data = Input::all();
        $posted_data["created_by"] = 1;
        $posted_data["updated_by"] = 1;

        $object = new Status();

        if ($object->validate($posted_data)) {
            $model = Status::create($posted_data);
            return $this->response->item($model, new StatusTransformer())->setStatusCode(200);
        } else {
            throw new \Dingo\Api\Exception\StoreResourceFailedException('Unable to create  Status.', $object->errors());
        }
    }

    public function update($id)
    {
        $posted_data = Input::all();

        $model = Status::find((int) $id);

        if ($model->validate($posted_data)) {
            if ($model->update($posted_data)) {
                return $this->response->item($model, new StatusTransformer())->setStatusCode(200);
            }

        } else {
            throw new \Dingo\Api\Exception\StoreResourceFailedException('Unable to update Status.', $model->errors());
        }
    }

    public function view($id)
    {
        $model = Status::find((int) $id);
        if ($model) {
            return $this->response->item($model, new StatusTransformer())->setStatusCode(200);
        }

    }

}
