<?php

namespace App\Http\Controllers;

use App\Permission;
use App\PermissionRole;
use Artisan;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Input;
use \App\Role;
use \App\User;

class HomeController extends BaseController
{

    public function index()
    {
        $user = User::all();

        return response()->json(['status_code' => 200, 'message' => 'User created successfully', 'data' => $user]);
    }

    public function attachUserRole($userId)
    {
        $roles = $_GET['ids'];

        $roles = (explode(",", $roles));

        $user = User::with("roles")->find($userId);

        foreach ($roles as $key => $value) {
            $user->roles()->attach($value);
        }

        return response()->json(['status_code' => 200, 'message' => 'Roles attached to user successfully', 'data' => $user]);
    }

    public function getUserRole($usetId)
    {
        return User::with("roles")->find($usetId);
    }

    public function attachPermission($role_name)
    {
        $posted_data = Input::all();

        $role = Role::where('name', $role_name)->first();

        $query = DB::table('permission_role')->where('role_id', '=', $role["id"])->delete();

        if ($posted_data != null) {
            foreach ($posted_data['ids'] as $key => $value) {
                $permissionParam = $value;
                $permission = Permission::where('id', $permissionParam)->first();

                $role->attachpermission($permission);
            }
        }

        $role = $this->response->created();

        return $this->dispatchResponse(200, "Attached Successfully...!!", $role);
    }

    public function getPremissions($roleParam)
    {
        $role = Role::where('name', $roleParam)->first();
        return $this->response->array($role->perms);
    }

    public function getAllPremissions(Request $request)
    {
        $page = $request->page;
        $limit = $request->limit;
        if (($page == null || $limit == null) || ($page == -1 || $limit == -1)) {
            $permission = Permission::orderBy('name', 'Asc')->paginate(200);
        } else {
            $permission = Permission::orderBy('name', 'Asc')->paginate($limit);
        }

        if ($permission->first()) {
            return $this->dispatchResponse(200, "", $permission);
        } else {
            return $this->dispatchResponse(Config::get('constants.SUCESS_CODE'), "No Records Found!!", null);
        }
        // return $this->dispatchResponse(200, "Data", $permission);
    }

    public function getAllRoles(Request $request)
    {
        $page = $request->page;
        $limit = $request->limit;
        if (($page == null || $limit == null) || ($page == -1 || $limit == -1)) {
            $role = Role::paginate(200);
        } else {
            $role = Role::paginate($limit);
        }

        if ($role->first()) {
            return $this->dispatchResponse(200, "Data", $role);
        } else {
            return $this->dispatchResponse(Config::get('constants.SUCESS_CODE'), "No Records Found!!", null);
        }
    }

    public function createPermissions()
    {
        $posted_data = Input::all();

        $permission = new Permission();

        if ($permission->validate($posted_data)) {
            $model = Permission::create($posted_data);
            //return $this->response->item($model, new ActivityTypeTransformer())->setStatusCode(200);

            return $this->dispatchResponse(200, "Created Successfully...!!", $model);
        } else {
            throw new \Dingo\Api\Exception\StoreResourceFailedException('Unable to create  Permissions.', $permission->errors());
        }
    }

    public function createRoles()
    {
        $posted_data = Input::all();

        $role = new Role();

        if ($role->validate($posted_data)) {
            $model = Role::create($posted_data);
            //return $this->response->item($model, new ActivityTypeTransformer())->setStatusCode(200);
            return $this->dispatchResponse(200, "Created Successfully...!!", $model);
        } else {
            throw new \Dingo\Api\Exception\StoreResourceFailedException('Unable to create  Roles.', $role->errors());
        }
    }

    public function updateRoles($id)
    {
        $posted_data = Input::all();

        $model = Role::find((int) $id);

        if ($model->validate($posted_data)) {
            if ($model->update($posted_data))
            //return $this->response->item($model, new TaskTransformer())->setStatusCode(200);
            {
                return $this->dispatchResponse(200, "Updated Successfully...!!", $model);
            }

        } else {
            throw new \Dingo\Api\Exception\StoreResourceFailedException('Unable to update Roles.', $model->errors());
        }
    }

    public function updatePermissions($id)
    {
        $posted_data = Input::all();

        $model = Permission::find((int) $id);

        if ($model->validate($posted_data)) {
            if ($model->update($posted_data))
            //return $this->response->item($model, new TaskTransformer())->setStatusCode(200);
            {
                return $this->dispatchResponse(200, "Updated Successfully...!!", $model);
            }

        } else {
            throw new \Dingo\Api\Exception\StoreResourceFailedException('Unable to update Permissions.', $model->errors());
        }
    }

    public function viewRoles($id)
    {
        $model = Role::find((int) $id);
        if ($model) {
            return $this->dispatchResponse(200, "Data...!!", $model);
        }

    }

    public function viewPermissions($id)
    {
        $model = Permission::find((int) $id);
        if ($model) {
            return $this->dispatchResponse(200, "Data...!!", $model);
        }

    }

    public function deleteRolesOfUser($user_id, $role_id)
    {
        $query = DB::table('role_user')->where([['role_id', '=', $role_id], ['user_id', '=', $user_id]])->delete();
        if ($query) {
            return $this->dispatchResponse(200, "Deleted Successfully...!!", null);
        }

    }

    public function deletePermission($permission_id)
    {
        $permissionAssignedRoleIDs = PermissionRole::where('permission_id', '=', $permission_id)->distinct('role_id')->pluck('role_id');
        // return $permissionAssignedRoleIDs;

        if (count($permissionAssignedRoleIDs) == 0) {
            $query = Permission::where([['id', '=', $permission_id]])->delete();
            if ($query) {
                return $this->dispatchResponse(200, "Permission deleted Successfully...!!", null);
            }

        } else {
            return $this->dispatchResponse(201, "Permission is assigned to role.", null);
        }
    }

    public function config_clear()
    {
        $status = Artisan::call('config:clear');
        return $status;
        return '<h1>Configurations cleared</h1>';
    }

    public function schedule_run()
    {
        $status = Artisan::call('schedule:run');
        return $status;
        return '<h1>Schedular run</h1>';
    }
}
