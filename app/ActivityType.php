<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Validator;

class ActivityType extends Model
{

    protected $table = 'activity_types';
    protected $guarded = ['id'];

    private $rules = array(
        'name' => 'required|max:68',
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

    public function status()
    {
        return $this->hasMany('App\Status', 'activity_type_id');
    }

    public function projectActivityStatusLog()
    {
        return $this->hasMany('App\ProjectActivityStatusLog', 'activity_type_id');
    }

}
