<?php

namespace App\Http\Controllers;

use App\Domain;
use App\Http\Transformers\DomainTransformer;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Input;

class DomainController extends BaseController
{

    public function index(Request $request)
    {

        $page = $request->page;
        $limit = $request->limit;
        if (($page == null || $limit == null) || ($page == -1 || $limit == -1)) {
            $domain = Domain::orderBy('name', 'asc')->paginate(25);
        } else {
            $domain = Domain::orderBy('name', 'asc')->paginate($limit);
        }

        if ($domain->first()) {
            return $this->dispatchResponse(200, "", $domain);
            //            return $this->response->item($domain, new DomainTransformer())->setStatusCode(200);
        } else {
            return $this->dispatchResponse(200, "No Records Found!!", $domain);
        }
    }

    public function create()
    {
        $posted_data = Input::all();
        $posted_data["created_by"] = 1;
        $posted_data["updated_by"] = 1;

        $domainObject = new Domain();

        if ($domainObject->validate($posted_data)) {
            $model = Domain::create($posted_data);
            return $this->response->item($model, new DomainTransformer())->setStatusCode(200);
        } else {
            throw new \Dingo\Api\Exception\StoreResourceFailedException('Unable to create  Domain.', $domainObject->errors());
        }
    }

    public function update($id)
    {
        $posted_data = Input::all();
        $model = Domain::find((int) $id);

        $domainObject = new Domain();

        if ($domainObject->validate($posted_data)) {
            if ($model->update($posted_data)) {
                return $this->response->item($model, new DomainTransformer())->setStatusCode(200);
            }

        } else {
            throw new \Dingo\Api\Exception\StoreResourceFailedException('Unable to update  Domain.', $domainObject->errors());
        }
    }

    public function view($id)
    {
        $model = Domain::find((int) $id);
        if ($model) {
            return $this->response->item($model, new DomainTransformer())->setStatusCode(200);
        }

    }

}
