<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class ProjectCategoryTechnologyUser extends Model
{
    protected $table = 'project_category_technology_user_mapping';
    protected $guarded = ['id', 'created_at', 'updated_at'];
    private $rules = array(
        'name' => 'required | max:190|numeric|unique',
    );

    private $errors;

    public function errors()
    {
        return $this->errors;
    }

    public function user()
    {
        return $this->belongsTo('App\User');
    }

}
