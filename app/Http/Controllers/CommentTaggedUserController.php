<?php

namespace App\Http\Controllers;

use App\CommentTaggedUser;

class CommentTaggedUserController extends Controller
{
    /**
     * Kalyani : Check the user is already tagged for given comment or not
     */
    public function isUserIsAlreadyTagged($user_id, $comment_id)
    {
        $taggedCommentList = CommentTaggedUser::where([
            ['user_id', '=', $user_id], ['comment_id', '=', $comment_id],
        ])->get();
        return $taggedCommentList;
    }

    /**
     * Kalyani : update tagged user data
     */
    public function updateTaggeduser($taggedUser)
    {
        $model = CommentTaggedUser::find((int) $taggedUser['id']);

        if ($model->validate($taggedUser)) {
            $model->update($taggedUser);
        }
    }

    /**
     * Kalyani : Add tagged user
     */
    public function addTaggeduser($taggedUser)
    {
        $objectTask = new CommentTaggedUser();
        if ($objectTask->validate($taggedUser)) {
            $model = CommentTaggedUser::create($taggedUser);
        }
    }

}
