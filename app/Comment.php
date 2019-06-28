<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Validator;

class Comment extends Model
{
    protected $table = 'comments';
    protected $guarded = ['id', 'created_at'];

    private $rules = array(
        'text' => 'required',
        'identifier' => 'required',
        'identifier_id' => 'required|numeric',
    );

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

    public function commentTaggedUser()
    {
        return $this->hasMany('App\CommentTaggedUser', 'comment_id');
    }

    public function user()
    {
        return $this->belongsTo('App\User', 'commented_by');
    }
}
