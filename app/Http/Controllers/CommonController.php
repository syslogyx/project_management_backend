<?php

namespace App\Http\Controllers;

use App\Milestone;
use Config;
use Illuminate\Support\Facades\Input;

class CommonController extends BaseController
{

    /**
     * Kalyani : Update the status of milestone /task.
     */
    public function update_status($identifier)
    {

        //get the request body
        $posted_data = Input::all();

        if ($identifier == Config::get('constants.URL_CONSTANTS.MILESTONE')) {
            return app('App\Http\Controllers\MilestoneController')->update_status($posted_data);
        } elseif ($identifier == Config::get('constants.URL_CONSTANTS.TASK')) {
            return app('App\Http\Controllers\TaskController')->update_status($posted_data);
        }

    }

}
