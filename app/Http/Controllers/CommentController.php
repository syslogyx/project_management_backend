<?php

namespace App\Http\Controllers;

use App\Comment;
use Config;
use Illuminate\Support\Facades\Input;

class CommentController extends BaseController
{
    /**
     * Kalyani : Add comment
     */
    public function add()
    {
        $posted_data = Input::all();
        // $posted_data["created_by"] = 1;
        // $posted_data["updated_by"] = 1;
        // $posted_data["commented_by"] = 1;
        $posted_data["status_id"] = Config::get('constants.STATUS_CONSTANT.ACTIVE');

        $taggedUserList = null;
        if (@$posted_data["tagged_user"]) {
            $taggedUserList = $posted_data["tagged_user"];
        }

        //remove the tagged list user object from array
        unset($posted_data["tagged_user"]);

        $objectTask = new Comment();

        if ($objectTask->validate($posted_data)) {
            $model = Comment::create($posted_data);

            //added tagged user of comment
            $this->addTaggedUserOfComment($taggedUserList, $model['id']);

            return $this->dispatchResponse(Config::get('constants.SUCESS_CODE'), Config::get('constants.SUCCESS_MESSAGES.COMMENT_ADDED'), $model);
            // return $this->response->item($model, new CommentTransformer())->setStatusCode(200);
        } else {
            throw new \Dingo\Api\Exception\StoreResourceFailedException('Unable to add comment.', $objectTask->errors());
        }
    }

    /**
     * Kalyani : Update existing comment
     */
    public function update($id)
    {
        $posted_data = Input::all();

        $model = Comment::find((int) $id);

        $taggedUserList = null;
        if (@$posted_data["tagged_user"]) {
            $taggedUserList = $posted_data["tagged_user"];
        }

        //remove the tagged list user object from array
        unset($posted_data["tagged_user"]);

        if ($model->validate($posted_data)) {
            if ($model->update($posted_data)) {
                //added tagged user of comment
                $this->addTaggedUserOfComment($taggedUserList, $id);

                return $this->dispatchResponse(Config::get('constants.SUCESS_CODE'), Config::get('constants.SUCCESS_MESSAGES.COMMENT_UPDATED'), $model);
            }
        } else {
            throw new \Dingo\Api\Exception\StoreResourceFailedException('Unable to update comment.', $model->errors());
        }
    }

    /**
     * Kalyani : make entry of tagged user of comment
     */
    public function addTaggedUserOfComment($taggedUserList, $comment_id)
    {
        if ($taggedUserList != null && sizeof($taggedUserList) != 0) {

            for ($taggedUserIndex = 0; $taggedUserIndex < sizeof($taggedUserList); $taggedUserIndex++) {
                $taggedUser = $taggedUserList[$taggedUserIndex];

                $user_id = $taggedUser['user_id'];
                $status_id = $taggedUser['status_id'];

                //check user is already tagged for comment or not
                $taggedComment = app('App\Http\Controllers\CommentTaggedUserController')->isUserIsAlreadyTagged($user_id, $comment_id);

                //set comment id into the request object
                $taggedUser['comment_id'] = $comment_id;
                if ($taggedComment != null && !empty($taggedComment) && sizeof($taggedComment) != 0) {

                    app('App\Http\Controllers\CommentTaggedUserController')->updateTaggeduser($taggedUser);
                } else {
                    // $taggedUser['comment_id'] = $comment_id;
                    app('App\Http\Controllers\CommentTaggedUserController')->addTaggeduser($taggedUser);
                }
            }
        }
    }

    public function delete($id)
    {
        // $posted_data = Input::all();

        $model = Comment::find((int) $id)->update(['status_id' => Config::get('constants.STATUS_CONSTANT.DELETED')]);
        // $model["status_id"] = Config::get('constants.STATUS_CONSTANT.DELETED');

        // $modelArray =  (array) $model;
        // if ($model->update($modelArray))
        return $this->dispatchResponse(Config::get('constants.SUCESS_CODE'), Config::get('constants.SUCCESS_MESSAGES.SUCCESS_MESSAGES'), null);

    }

    public function getCommentList($identifier, $id)
    {
        $commentList = Comment::where([
            ['identifier', '=', $identifier], ['identifier_id', '=', $id], ['status_id', '<>', Config::get('constants.STATUS_CONSTANT.DELETED'),
            ],
        ])->with(['user', 'commentTaggedUser' => function ($query) {
            $query->where('status_id', '<>', 'Delete');
        }, 'commentTaggedUser.user'])->get();
        return $commentList;
    }

}
