<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Validator;

class Domain extends Model
{

    protected $table = 'domains';
    protected $guarded = ['id', 'created_at', 'updated_at'];
    protected $hidden = ['created_at', 'updated_at', 'created_by', 'updated_by'];
    private $rules = array(
        'name' => 'required',
        'alias' => 'required',
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

    public function project()
    {
        return $this->hasMany('App\Project', 'domain_id');
    }

    public function projectResource()
    {
        return $this->hasMany('App\ProjectResource', 'domain_id');
    }

    /*public function project() {
return $this->hasMany('App\Project','technology_id');
}*/

}
