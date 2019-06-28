<?php

namespace App\Http\Transformers;

use League\Fractal\TransformerAbstract;

class CommentTransformer extends TransformerAbstract {

    public function transform(\App\Comment $comment) {
        return [
            'id' => $comment->id,
            'text' => $comment->text,    
            'commented_by' => $comment->commented_by 
            // 'user' => $comment->user,
//            'status' =>  $project->status,
            // 'tagged_user' => $comment->commentTaggedUser, 
        ];
    }

     public function transformWithComment(Object $miletone, Object $commentList) {
        return [
            'miletone' => $miletone,
            'comment_list' => $commentList
            
        ];
    }

}
