<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Input;
use Validator;

class RemoteValidationController extends BaseController
{

    private $messages = array(
        'unique_multiple' => 'Project and User combination already exists.',
    );
    private $errors;

    public function check_validation()
    {
        $posted_data = Input::all();
        // return $posted_data ;
        $rules = [];
        if (@$posted_data["client"]) {
            $data = json_decode($posted_data["client"]);
            $rules['email'] = 'required|email|unique:clients,email,' . $data->id;
            $validator = Validator::make((array) $data, $rules);
            if ($validator->fails()) {
                $this->errors = $validator->errors();
                //return $this->errors;
                return "false";
            }
        }

        if (@$posted_data["project"]) {

            $data = json_decode($posted_data["project"]);
            $type = $data->type;
            $rules['name'] = 'required|unique:projects,name,' . $data->id;
            // if($type == "New"){
            //     $rules['name'] = [
            //         'required',
            //         Rule::unique('projects')->where(function($query) {
            //           $query->where('type', '=', 2);
            //         })
            //     ];
            // }else if($type == "Old"){
            //     $rules['name'] = [
            //         'required',
            //         Rule::unique('projects')->where(function($query) {
            //           $query->where('type', '=', 1);
            //         })
            //     ];
            // }

            $validator = Validator::make((array) $data, $rules);
            if ($validator->fails()) {
                $this->errors = $validator->errors();
                //return $this->errors;
                return "false";
            }
        }

        if (@$posted_data["projecr_resource"]) {

            $data = json_decode($posted_data["projecr_resource"]);
            $rules['project_id'] = 'required|unique_multiple:project_resources,user_id,project_id';
            $rules['user_id'] = 'required|numeric';

            Validator::extend('unique_multiple', function ($attribute, $value, $parameters, $validator) {
                // Get the other fields
                $fields = $validator->getData();
                // Get table name from first parameter
                $table = array_shift($parameters);
                // Build the query
                $query = DB::table($table);
                // Add the field conditions
                foreach ($parameters as $i => $field) {
                    $query->where($field, $fields[$field]);
                }
                // Validation result will be false if any rows match the combination
                return ($query->count() == 0);
            });

            $validator = Validator::make((array) $data, $rules, $this->messages);
            if ($validator->fails()) {
                $this->errors = $validator->errors();
                //return $this->errors;
                return "false";
            }
        }
        //$this->validate($posted_data);
        return "true";
    }

    public function object_to_array($object)
    {
        return (array) $object;
    }

}
