<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace App\Http\Controllers\Auth;

//use Illuminate\Routing\Controller;
use App\Http\Controllers\Controller;
use App\Http\Transformers\UserAuthTransformer;
//use Illuminate\Http\Response;
use App\Menu;
use App\Permission;
use App\PermissionRole;
use App\Role;
use App\RoleUsers;
use App\User;
use Illuminate\Http\Request;
use Tymon\JWTAuth\Exceptions\JWTException;
use Tymon\JWTAuth\Facades\JWTAuth;

//use Symfony\Component\Debug\ErrorHandler;

/**
 * Description of AuthController
 *
 * @author chandrashekar
 */
class AuthController extends Controller
{

    public function __construct()
    {
        $this->middleware("guest", ["except" => "getLogout"]);
    }

    public function authenticate(Request $request)
    {
        $credentials = $request->only("email", "password");
        // print_r($credentials);die();
        $userObject = new User();
        try {
            if (!$token = JWTAuth::attempt($credentials)) {
                // return $this->response->error(["error" => "User credentials are not correct!"], 401);
                $response = 'Your email and / or password are incorrect.';
                // return $response;
                // throw new \Exception($response);
                throw new \Dingo\Api\Exception\StoreResourceFailedException($response, $userObject->errors());
            }
        } catch (JWTException $ex) {
            return $this->response->error(["error", "Something went wrong."]);
        }

        $email = $request["email"];

        $user = User::where('email', $email)->first();

        $user->remember_token = $token;
        $user->save();

        if ($user) {
            $menuList = $this->getMenuList($user['id']);
            $userRole = RoleUsers::where('user_id', $user['id'])
                ->select('role_id')->get();
            $userRoleName = Role::whereIn('id', $userRole)
                ->select('name')->get();
            $user['roleName'] = $userRoleName;
            $permissionList = $this->getPermissionList($user['id']);
            $user['menu_list'] = $menuList;
            $user['permissionGroupList'] = $permissionList;
            return $this->response->item($user, new UserAuthTransformer())->setStatusCode(200);
        } else {
            throw new \Dingo\Api\Exception\StoreResourceFailedException('Invalid email address entered.', $userObject->errors());
        }

    }

    public function getMenuList($userId)
    {

        return $menuList = Menu::get();

        // if ($menuList->first()) {
        //     return $this->dispatchResponse(200, "", $menuList);

        // } else {
        //     return $this->dispatchResponse(200, "No Records Found!!", $menuList);
        // }
    }
    public function getPermissionList($userId)
    {
        $userRole = RoleUsers::where('user_id', $userId)
            ->select('role_id')->get();

        $roleID = [];
        foreach ($userRole as $key => $value) {
            array_push($roleID, $value->role_id);
        }

        $permissionId = PermissionRole::whereIn('role_id', $roleID)->select('permission_id')->get();

        return $permissionId = Permission::whereIn('id', $permissionId)->get();

        // if ($menuList->first()) {
        //     return $this->dispatchResponse(200, "", $menuList);

        // } else {
        //     return $this->dispatchResponse(200, "No Records Found!!", $menuList);
        // }
    }

    /*Sonal: Get Login user Menu roles and permission data*/
    public function getLoginUserData($user_id)
    {
        $user = User::where('id', $user_id)->first();

        $menuList = $this->getMenuList($user['id']);

        $userRole = RoleUsers::where('user_id', $user['id'])->select('role_id')->get();

        $userRoleName = Role::whereIn('id', $userRole)->select('name')->get();

        $permissionList = $this->getPermissionList($user['id']);

        $user['roleName'] = $userRoleName;
        $user['menu_list'] = $menuList;
        $user['permissionGroupList'] = $permissionList;

        if ($user) {
            return response()->json(['status_code' => 200, 'message' => 'User Data.', 'data' => $user]);
        } else {
            return response()->json(['status_code' => 201, 'message' => 'No Records Found!!', 'data' => null]);
        }
    }

}
