<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class EODReport extends Model
{
    protected $table = 'eod_report';
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

    public function user()
    {
        return $this->belongsTo('App\User', 'user_id');
    }

    public function eod_task()
    {
        return $this->hasMany('App\EODTaskAssoc', 'eod_id');
    }

    public function meeting_break()
    {
        return $this->hasMany('App\MeetingBreakLog', 'eod_id');
    }

    public function miscellaneous_records()
    {
        return $this->hasMany('App\EODMiscellaneousRecords', 'eod_id');
    }
}
