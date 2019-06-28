<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Validator;

class ProjectDocument extends Model
{

    protected $table = 'project_documents';
    protected $guarded = ['id', 'created_at', 'updated_at'];
    private $rules = array(
        'title' => 'required | max:190',
        'file_name' => 'required | max:190',
        'type' => 'required',
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

}
