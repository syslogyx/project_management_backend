<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Validator;

class MeetingBreakLog extends Model
{
    protected $table = 'meeting_break_logs';
    protected $guarded = ['id', 'created_at', 'updated_at'];

    private $rules = array(
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

    public function eod()
    {
        return $this->belongsTo('App\EODReport', 'eod_id');
    }
}
