<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class CategoriesTechnologyMapping extends Model
{
    protected $table = 'categories_technology_mapping';
    protected $guarded = ['id', 'created_at', 'updated_at'];
    /* private $rules = array(
    'technology_id' => 'required|numeric',
    'category_id' => 'required|numeric'
    );
    private $errors;

    public function validate($data) {
    $validator = Validator::make($data, $this->rules);
    if ($validator->fails()) {
    $this->errors = $validator->errors();
    return false;
    }
    return true;
    }

    public function errors() {
    return $this->errors;
    }*/

    public function categoriesTechnologyMapping()
    {
        return $this->hasMany('App\Category', 'category_id');
    }

    public function technology()
    {
        return $this->belongsTo('App\Technology', 'technology_id');
    }
}
