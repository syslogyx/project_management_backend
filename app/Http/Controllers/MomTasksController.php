<?php

namespace App\Http\Controllers;

use App\MomTasks;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Input;

class MomTasksController extends BaseController
{

    public function createTasks()
    {
        try {
            DB::beginTransaction();
            $posted_data = Input::all();

            $momTaskss = new MomTasks();
            if ($momTaskss->validate($posted_data, "Tasks")) {
//                $mom_task = DB::table('mom_task_table')->insert($posted_data);
                $mom_task = MomTasks::create($posted_data);
                DB::commit();
                return $this->dispatchResponse(200, "Task created successfully...!!", $mom_task);
            } else {
                DB::rollBack();
                return $this->dispatchResponse(400, "Something went wrong.", $momTaskss->errors());
            }
        } catch (\Exception $e) {
            DB::rollback();
            throw $e;
        }
    }

    public function updateStatus()
    {
        try {
            DB::beginTransaction();
            $posted_data = Input::all();
            $mom_task = MomTasks::where([
                // ["mom_id","=",$posted_data["mom_id"]],
                ["id", "=", $posted_data["task_id"]],
            ])->update(['status' => $posted_data["status"]]);
            DB::commit();
            return $this->dispatchResponse(200, "Task status updated successfully...!!", $mom_task);
        } catch (\Exception $e) {
            DB::rollback();
            throw $e;
        }
    }

}
