<?php

namespace App\Http\Controllers;

use App\Menu;
use App\MenuType;
use App\Permission;
use App\PermissionRole;
use App\RoleMenuPermissions;
use App\RoleUsers;

class MenuController extends BaseController
{
    public function index()
    {
        $menuList = MenuType::with("menu")->paginate(200);
        if ($menuList->first()) {
            return $this->dispatchResponse(200, "", $menuList);

        } else {
            return $this->dispatchResponse(200, "No Records Found!!", $menuList);
        }
    }
    public function getMenuList($userId)
    {
        $userRole = RoleUsers::where('user_id', $userId)
            ->select('role_id')->get();

        $menuId = RoleMenuPermissions::where('role_id', $userRole[0]['role_id'])->select('menu_id')->get();

        $menuList = Menu::whereIn('id', $menuId)->get();

        $permissionId = PermissionRole::where('role_id', $userRole[0]['role_id'])->select('permission_id')->get();

        $permissionList = Permission::whereIn('id', $permissionId)->get();

        $response['menuList'] = $menuList;
        $response['permissionList'] = $permissionList;

        if ($menuList->first()) {
            return $this->dispatchResponse(200, "", $response);

        } else {
            return $this->dispatchResponse(200, "No Records Found!!", $menuList);
        }
    }
}
