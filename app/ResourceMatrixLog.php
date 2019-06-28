<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Validator;

class ResourceMatrixLog extends Model
{
    protected $table = 'resource_matrix_log';
    protected $guarded = ['id', 'created_at', 'updated_at'];
    protected $hidden = ['updated_at', 'updated_by'];

    private $rules = array(
        'project_id' => 'required|numeric',
        'user_id' => 'required|numeric',
        'start_date' => 'nullable|date',
        'due_date' => 'nullable|date',
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
        return $this->belongsTo('App\Project');
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
