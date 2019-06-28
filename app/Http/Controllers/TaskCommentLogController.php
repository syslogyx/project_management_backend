<?php

namespace App\Http\Controllers;

use App\Http\Transformers\TaskCommentLogTransformer;
use App\TaskCommentLog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Input;

class TaskCommentLogController extends BaseController
{

    public function index(Request $request)
    {
        $page = $request->page;
        $limit = $request->limit;

        if (($page == null || $limit == null) || ($page == -1 || $limit == -1)) {
            $task_log = TaskCommentLog::with('task')->paginate(25);
        } else {
            $task_log = TaskCommentLog::with('task')->paginate($limit);
        }

        if ($task_log->first()) {
            return $this->dispatchResponse(200, "", $task_log);
        } else {
            return $this->dispatchResponse(200, "No Records Found!!", $task_log);
        }

    }

    public function create()
    {
        $posted_data = Input::all();
        $posted_data["created_by"] = 1;
        $posted_data["updated_by"] = 1;

        $object = new TaskCommentLog();

        if ($object->validate($posted_data)) {
            $model = TaskCommentLog::create($posted_data);
            return $this->response->item($model, new TaskCommentLogTransformer())->setStatusCode(200);
        } else {
            throw new \Dingo\Api\Exception\StoreResourceFailedException('Unable to create  Task Comment Logs.', $object->errors());
        }
    }

    public function update($id)
    {
        $posted_data = Input::all();

        $model = TaskCommentLog::find((int) $id);

        if ($model->validate($posted_data)) {
            if ($model->update($posted_data)) {
                return $this->response->item($model, new TaskCommentLogTransformer())->setStatusCode(200);
            }

        } else {
            throw new \Dingo\Api\Exception\StoreResourceFailedException('Unable to update Task Comment Logs.', $model->errors());
        }
    }

    public function view($id)
    {
        $model = TaskCommentLog::find((int) $id);
        if ($model) {
            return $this->response->item($model, new TaskCommentLogTransformer())->setStatusCode(200);
        }

    }

    public function task_by_milestone_id($id)
    {
        $model = TaskCommentLog::find((int) $id);
        if ($model) {
            return $this->response->item($model, new TaskCommentLogTransformer())->setStatusCode(200);
        }

    }

}
