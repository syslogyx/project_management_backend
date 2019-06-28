<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Validator;

class UserTechnologyMapping extends Model
{

    protected $table = 'user_technology_mapping';
    protected $guarded = ['id', 'created_at', 'updated_at'];
    private $rules = array(
        'user_id' => 'required|numeric',
//        'userTechnology.*.technology_id' => 'required|numeric|unique:user_technology_mapping,technology_id',
        //         'technology_id' => 'required|numeric',
        //         'duration_in_month' => 'required'
        'userTechnology.*.technology_id' => 'required',
//        'userTechnology.*.duration_years' => 'required',
        //        'userTechnology.*.duration_months' => 'required'
    );
    private $errors;

    public function validate($data)
    {
//        if ($this->id)
        //            $this->rules['name'] .= $this->id;

        $validator = Validator::make($data, $this->rules);
        if ($validator->fails()) {
            $this->errors = $validator->errors();
            return false;
        }
        return true;
    }

    public function errors()
    {
        return $this->errors;
    }

    public function technology()
    {
        return $this->belongsTo('App\Technology');
    }

    public function user()
    {
        return $this->belongsTo('App\User');
    }
    public function domain()
    {
        return $this->belongsTo('App\Category');
    }

}
