<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Validator;

class MomAttendees extends Model
{
    //mom_attendees
    protected $table = 'mom_attendees';
    protected $guarded = ['id', 'created_at', 'updated_at'];
    private $rules = array(
        '*.mom_id' => 'required|numeric',
        '*.user_id' => 'required|numeric',
    );
    private $messages = array(
        '*.mom_id.required' => 'A MoM id is required',
        '*.user_id.required' => 'A user id is required',
    );
    private $errors;

    public function validate($data)
    {
        $validator = Validator::make($data, $this->rules, $this->messages);
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
