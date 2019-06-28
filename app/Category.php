<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Validator;

class Category extends Model
{
    protected $table = 'categories';
    protected $guarded = ['id', 'created_at', 'updated_at'];
    private $rules = array(
        'name' => 'required | max:190',
        'alias' => 'required | max:190',
    );
    private $errors;

    public function validate($data)
    {
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

//    protected static function boot() {
    //        Category::saved(function ($model) {
    //            print_r($model->toArray());die();
    //            return $model;
    //        });
    //    }

    public function technology()
    {
        return $this->belongsToMany('App\Technology', 'categories_technology_mapping', 'category_id', 'technology_id');
    }

    public function project()
    {
        return $this->belongsToMany('App\Project', 'project_category_mapping', 'category_id', 'project_id');
    }

    public function user()
    {
        return $this->belongsToMany('App\User', 'user_technology_mapping', 'domain_id', 'user_id')->distinct('user_id');
    }

}
