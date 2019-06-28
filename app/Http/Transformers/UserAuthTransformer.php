<?php

namespace App\Http\Transformers;

use League\Fractal\TransformerAbstract;

class UserAuthTransformer extends TransformerAbstract {

    public function transform(\App\User $user) {



        return [
            'id' => $user->id,
            'name' => $user->name,
            'email' => $user->email,
            'gender' => $user->gender,
            'status' => $user->status,
            'authToken' => $user->remember_token,
            'role' => $user->roles,
            'roleName' => $user->roleName,            
            'permissionGroupList' => $user->permissionGroupList,
            'menu_list' => $user->menu_list,
            'email_internal' => $user->email_internal,
            'email_external' => $user->email_external,
            'department' => $user->department,
            'designation' => $user->designation,
            'avatar' => $user->avatar,
            'user_id'=> $user->user_id,
        ];
    }
}
