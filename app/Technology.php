<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Validator;

class Technology extends Model
{

    protected $table = 'technologies';
    protected $guarded = ['id', 'created_at', 'updated_at'];
    private $rules = array(
        'name' => 'required | max:190|unique:technologies,name,',
        'alias' => 'required',
        'parent_id' => 'nullable',
    );
    private $errors;

    public function validate($data)
    {
        if ($this->id) {
            $this->rules['name'] .= $this->id;
        }

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

    public function project()
    {
        return $this->belongsToMany('App\Technology', 'project_technologies', 'technology_id', 'project_id');
    }
    public function technology()
    {

    }

    public function user()
    {
        return $this->belongsToMany('App\User', 'technology_user', 'technology_id', 'user_id');
    }

    public function categories()
    {
        return $this->belongsToMany('App\Category', 'categories_technology_mapping', 'technology_id', 'category_id');
    }

    /**
     * Get parent Technology owned by a given Technology.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasOne
     */
    public function parent()
    {
        return $this->hasOne('App\Technology', 'id', 'parent_id');
    }

    /**
     * Get child Technology owned by a given Technology.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function children()
    {
        return $this->hasMany('App\Technology', 'parent_id', 'id');
    }

}
