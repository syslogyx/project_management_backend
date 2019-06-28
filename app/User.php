<?php

namespace App;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Validator;
use Zizaco\Entrust\Traits\EntrustUserTrait;

class User extends Authenticatable
{

    use Notifiable,
        EntrustUserTrait;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'email', 'password', "name", "gender", "status", "user_id", "total_experience",
        "email_internal",
        "email_external",
        "department",
        "designation",
        "avatar",
    ];
    public $roles;
    protected $table = 'users';
    protected $guarded = ['id', 'created_at', 'updated_at'];
    protected $hidden = ['password', 'remember_token', 'created_at', 'updated_at', 'created_by', 'updated_by'];
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

    public function project()
    {
        return $this->hasMany('App\Project', 'user_id');
    }

    public function projectResource()
    {
        return $this->hasMany('App\ProjectResource', 'user_id');
    }

    public function userTechnologyMapping()
    {
        return $this->hasMany('App\UserTechnologyMapping');
    }

    public function technology()
    {
        return $this->belongsToMany('App\Technology', 'user_technology_mapping', 'user_id', 'technology_id');
    }

    public function domain()
    {
        return $this->belongsToMany('App\Category', 'user_technology_mapping', 'user_id', 'domain_id');
    }

    public function roles()
    {
        return $this->belongsToMany('App\Role', 'role_user', 'user_id', 'role_id');
    }

    public function mom()
    {
        return $this->belongsToMany('App\Mom', 'mom_attendees', 'user_id', 'mom_id');
    }

    public function momTasks()
    {
        return $this->hasOne('App\MomTasks');
    }

    public function resourceMatrixLog()
    {
        return $this->hasMany('App\ResourceMatrixLog', 'user_id');
    }

    public function managerData()
    {
        return $this->belongsTo('App\User', 'manager_id');
    }

    public function mentorData()
    {
        return $this->belongsTo('App\User', 'mentor_id');
    }

    public function getRoles()
    {

        $this->roles = array(
            0 => array(
                'role' => 'admin',
                'roleId' => '1',
                'permissionList' => array(
                    0 => array(
                        'permissionId' => '3',
                        'permissionTag' => 'user.role.manage.permission',
                        'permissionDesc' => 'user.role.manage.permission',
                        'permissionType' => 'route',
                    ),
                    1 => array(
                        'permissionId' => '2',
                        'permissionTag' => 'user.role.edit',
                        'permissionDesc' => 'user.role.edit',
                        'permissionType' => 'route',
                    ),
                    2 => array(
                        'permissionId' => '1',
                        'permissionTag' => 'user.role.active',
                        'permissionDesc' => 'user.role.active',
                        'permissionType' => 'route',
                    ),
                ),
            ),
            1 => array(
                'role' => 'manager',
                'roleId' => '2',
                'permissionList' => array(
                    0 => array(
                        'permissionId' => '3',
                        'permissionTag' => 'user.role.manage.permission',
                        'permissionDesc' => 'user.role.manage.permission',
                        'permissionType' => 'route',
                    ),
                    1 => array(
                        'permissionId' => '2',
                        'permissionTag' => 'user.role.edit',
                        'permissionDesc' => 'user.role.edit',
                        'permissionType' => 'route',
                    ),
                    2 => array(
                        'permissionId' => '1',
                        'permissionTag' => 'user.role.active',
                        'permissionDesc' => 'user.role.active',
                        'permissionType' => 'route',
                    ),
                ),
            ),
        );

        return $this->roles;
    }

}
