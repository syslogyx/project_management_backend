<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Validator;

class CommentTaggedUser extends Model
{
    protected $table = 'comment_tagged_user_mapping';
    protected $guarded = ['id', 'created_at'];

    private $rules = array(
        'user_id' => 'required|numeric',
        'comment_id' => 'required|numeric',
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

    public function comment()
    {
        return $this->belongsTo('App\Comment');
    }

    public function user()
    {
        return $this->belongsTo('App\User');
    }
}
