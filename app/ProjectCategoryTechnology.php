<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use Validator;

class ProjectCategoryTechnology extends Model
{

    protected $table = 'project_category_technology_mapping';
    protected $guarded = ['id', 'created_at', 'updated_at'];
    public $rules = array(
        'project_category_id' => 'required|unique_multiple:project_category_technology_mapping,technology_id,project_category_id',
        'technology_id' => 'required',
    );
    private $messages = array(
        'unique_multiple' => 'Project and Technology combination already exists.',
    );
    private $errors;

    public function validate($data)
    {

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

        $validator = Validator::make($data, $this->rules, $this->messages);
        if ($validator->fails()) {
            $this->errors = $validator->errors();
            return false;
        }
        return true;
    }

    public function errors()
    {
        array('unique_multiple' => 'This combination already exists.');
        return $this->errors;
    }

    public function projectCategoryMapping()
    {
        return $this->belongsTo('App\ProjectCategoryMapping', 'project_category_id');
    }

    public function technology()
    {
        return $this->belongsTo('App\Technology', 'technology_id');
    }

    public function category()
    {
        return $this->belongsTo('App\ProjectCategoryTechnology');
    }

}
