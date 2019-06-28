<?php
use App\Permission;
use App\PermissionRole;
use App\Role;
use App\RoleUsers;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class EntrustSetupTables extends Migration
{
    /**
     * Run the migrations.
     *
     * @return  void
     */
    public function up()
    {
        // Create table for storing roles
        Schema::create('roles', function (Blueprint $table) {
            $table->increments('id');
            $table->string('name');
            $table->string('display_name')->nullable();
            $table->text('description')->nullable();
            $table->timestamps();
        });

        $data = array(
            array(
                'name' => 'Super Admin',
                'display_name' => 'Super Admin',
                'description' => "",
            ),
            array(
                'name' => 'Admin',
                'display_name' => 'Admin',
                'description' => "",
            ),
            array(
                'name' => 'Manager',
                'display_name' => 'Manager',
                'description' => "",
            ),
            array(
                'name' => 'Lead',
                'display_name' => 'Lead',
                'description' => "",
            ),
            array(
                'name' => 'Employee',
                'display_name' => 'Employee',
                'description' => "",
            ),

            array(
                'name' => 'Intern',
                'display_name' => 'Intern',
                'description' => "",
            ),
            array(
                'name' => 'CustomRole',
                'display_name' => 'Custom Role',
                'description' => "",
            ),

        );

        Role::insert($data);

        // Create table for associating roles to users (Many-to-Many)
        Schema::create('role_user', function (Blueprint $table) {
            $table->integer('user_id')->unsigned();
            $table->integer('role_id')->unsigned();

            $table->foreign('user_id')->references('id')->on('users')
                ->onUpdate('cascade')->onDelete('cascade');
            $table->foreign('role_id')->references('id')->on('roles')
                ->onUpdate('cascade')->onDelete('cascade');

            $table->primary(['user_id', 'role_id']);
        });

        $data = array(
            array(
                'role_id' => 1,
                'user_id' => 1,
            ),
            array(
                'role_id' => 2,
                'user_id' => 2,
            ),
            array(
                'role_id' => 3,
                'user_id' => 3,
            ),
            array(
                'role_id' => 4,
                'user_id' => 4,
            ),
            array(
                'role_id' => 5,
                'user_id' => 5,
            ),
            array(
                'role_id' => 6,
                'user_id' => 6,
            ),
        );

        RoleUsers::insert($data);

        // Create table for storing permissions
        Schema::create('permissions', function (Blueprint $table) {
            $table->increments('id');
            $table->string('name')->unique();
            $table->string('display_name')->nullable();
            $table->string('description')->nullable();
            $table->timestamps();
        });
        $data = array(

            array(
                'name' => 'user.projects.feeds.view',
                'display_name' => 'user.projects.feeds.view',
                'description' => '',
            ),

            array(
                'name' => 'user.projects.milestones.view',
                'display_name' => 'user.projects.milestones.view',
                'description' => '',
            ),
            array(
                'name' => 'user.projects.milestones.edit',
                'display_name' => 'user.projects.milestones.edit',
                'description' => '',
            ),
            array(
                'name' => 'user.projects.milestones.delete',
                'display_name' => 'user.projects.milestones.delete',
                'description' => '',
            ),
            array(
                'name' => 'user.projects.tasks.view',
                'display_name' => 'user.projects.tasks.view',
                'description' => '',
            ),
            array(
                'name' => 'user.projects.tasks.edit',
                'display_name' => 'user.projects.tasks.edit',
                'description' => '',
            ),
            array(
                'name' => 'user.projects.tasks.delete',
                'display_name' => 'user.projects.tasks.delete',
                'description' => '',
            ),
            array(
                'name' => 'user.projects.calendar.view',
                'display_name' => 'user.projects.calendar.view',
                'description' => '',
            ),
            array(
                'name' => 'user.projects.documents.view',
                'display_name' => 'user.projects.documents.view',
                'description' => '',
            ),
            array(
                'name' => 'user.projects.documents.edit',
                'display_name' => 'user.projects.documents.edit',
                'description' => '',
            ),
            array(
                'name' => 'user.projects.documents.delete',
                'display_name' => 'user.projects.documents.delete',
                'description' => '',
            ),
            array(
                'name' => 'user.projects.timesheet.view',
                'display_name' => 'user.projects.timesheet.view',
                'description' => '',
            ),
            array(
                'name' => 'user.projects.resources.view',
                'display_name' => 'user.projects.resources.view',
                'description' => '',
            ),

            array(
                'name' => 'user.projects.resources.edit',
                'display_name' => 'user.projects.resources.edit',
                'description' => '',
            ),
            array(
                'name' => 'user.projects.resources.delete',
                'display_name' => 'user.projects.resources.delete',
                'description' => '',
            ),

            array(
                'name' => 'user.projects.projectdoc.view',
                'display_name' => 'user.projects.projectdoc.view',
                'description' => '',
            ),
            array(
                'name' => 'user.projects.projectdoc.edit',
                'display_name' => 'user.projects.projectdoc.edit',
                'description' => '',
            ),
            array(
                'name' => 'user.projects.projectdoc.delete',
                'display_name' => 'user.projects.projectdoc.delete',
                'description' => '',
            ),

            array(
                'name' => 'user.projects.projectinfo.view',
                'display_name' => 'user.projects.projectinfo.view',
                'description' => '',
            ),
            array(
                'name' => 'user.projects.projectinfo.edit',
                'display_name' => 'user.projects.projectinfo.edit',
                'description' => '',
            ),
            array(
                'name' => 'user.projects.projectinfo.delete',
                'display_name' => 'user.projects.projectinfo.delete',
                'description' => '',
            ),

            array(
                'name' => 'user.resourcematrix.dashboard.view',
                'display_name' => 'user.resourcematrix.dashboard.view',
                'description' => '',
            ),
            array(
                'name' => 'user.resourcematrix.dashboard.edit',
                'display_name' => 'user.resourcematrix.dashboard.edit',
                'description' => '',
            ),
            array(
                'name' => 'user.resourcematrix.dashboard.delete',
                'display_name' => 'user.resourcematrix.dashboard.delete',
                'description' => '',
            ),

            array(
                'name' => 'user.resourcematrix.clients.view',
                'display_name' => 'user.resourcematrix.clients.view',
                'description' => '',
            ),
            array(
                'name' => 'user.resourcematrix.clients.edit',
                'display_name' => 'user.resourcematrix.clients.edit',
                'description' => '',
            ),
            array(
                'name' => 'user.resourcematrix.clients.delete',
                'display_name' => 'user.resourcematrix.clients.delete',
                'description' => '',
            ),

            array(
                'name' => 'user.resourcematrix.technologies.view',
                'display_name' => 'user.resourcematrix.technologies.view',
                'description' => '',
            ),
            array(
                'name' => 'user.resourcematrix.technologies.edit',
                'display_name' => 'user.resourcematrix.technologies.edit',
                'description' => '',
            ),
            array(
                'name' => 'user.resourcematrix.technologies.delete',
                'display_name' => 'user.resourcematrix.technologies.delete',
                'description' => '',
            ),
            array(
                'name' => 'user.resourcematrix.domains.view',
                'display_name' => 'user.resourcematrix.domains.view',
                'description' => '',
            ),
            array(
                'name' => 'user.resourcematrix.domains.edit',
                'display_name' => 'user.resourcematrix.domains.edit',
                'description' => '',
            ),
            array(
                'name' => 'user.resourcematrix.domains.delete',
                'display_name' => 'user.resourcematrix.domains.delete',
                'description' => '',
            ),

            array(
                'name' => 'user.resourcematrix.users.view',
                'display_name' => 'user.resourcematrix.users.view',
                'description' => '',
            ),
            array(
                'name' => 'user.resourcematrix.users.edit',
                'display_name' => 'user.resourcematrix.users.edit',
                'description' => '',
            ),
            array(
                'name' => 'user.resourcematrix.users.delete',
                'display_name' => 'user.resourcematrix.users.delete',
                'description' => '',
            ),
            array(
                'name' => 'user.resourcematrix.status.view',
                'display_name' => 'user.resourcematrix.status.view',
                'description' => '',
            ),
            array(
                'name' => 'user.resourcematrix.status.edit',
                'display_name' => 'user.resourcematrix.status.edit',
                'description' => '',
            ),
            array(
                'name' => 'user.resourcematrix.status.delete',
                'display_name' => 'user.resourcematrix.status.delete',
                'description' => '',
            ),

            array(
                'name' => 'user.resourcematrix.roles.view',
                'display_name' => 'user.resourcematrix.roles.view',
                'description' => '',
            ),
            array(
                'name' => 'user.resourcematrix.roles.edit',
                'display_name' => 'user.resourcematrix.roles.edit',
                'description' => '',
            ),
            array(
                'name' => 'user.resourcematrix.roles.delete',
                'display_name' => 'user.resourcematrix.roles.delete',
                'description' => '',
            ),

            array(
                'name' => 'user.resourcematrix.permissions.view',
                'display_name' => 'user.resourcematrix.permissions.view',
                'description' => '',
            ),
            array(
                'name' => 'user.resourcematrix.permissions.edit',
                'display_name' => 'user.resourcematrix.permissions.edit',
                'description' => '',
            ),
            array(
                'name' => 'user.resourcematrix.permissions.delete',
                'display_name' => 'user.resourcematrix.permissions.delete',
                'description' => '',
            ),

            array(
                'name' => 'user.projects.dashboard',
                'display_name' => 'user.projects.dashboard',
                'description' => '',
            ),
            array(
                'name' => 'user.projects.feeds',
                'display_name' => 'user.projects.feeds',
                'description' => '',
            ),
            array(

                'name' => 'user.projects.milestones',
                'display_name' => 'user.projects.milestones',
                'description' => '',
            ),
            array(

                'name' => 'user.projects.tasks',
                'display_name' => 'user.projects.tasks',
                'description' => '',

            ),
            array(

                'name' => 'user.projects.calendar',
                'display_name' => 'user.projects.calendar',
                'description' => '',

            ),
            array(

                'name' => 'user.projects.documents',
                'display_name' => 'user.projects.documents',
                'description' => '',
            ),
            array(

                'name' => 'user.projects.timesheet',
                'display_name' => 'user.projects.timesheet',
                'description' => '',
            ),
            array(

                'name' => 'user.projects.resources',
                'display_name' => 'user.projects.resources',
                'description' => '',
            ),
            array(

                'name' => 'user.projects.project.poc',
                'display_name' => 'user.projects.project.poc',
                'description' => '',
            ),

            array(

                'name' => 'user.projects.project.info.general',
                'display_name' => 'user.projects.project.info.general',
                'description' => '',
            ),
            array(

                'name' => 'user.projects.project.info.domain',
                'display_name' => 'user.projects.project.info.domain',
                'description' => '',
            ),
            array(

                'name' => 'user.dashboard',
                'display_name' => 'user.dashboard',
                'description' => '',

            ),
            array(
                'name' => 'user.feed',
                'display_name' => 'user.feed',
                'description' => '',
            ),
            array(

                'name' => 'user.projects',
                'display_name' => 'user.projects',
                'description' => '',
            ),

            array(

                'name' => 'user.eod.history',
                'display_name' => 'user.eod.history',
                'description' => '',
            ),
            array(
                'name' => 'user.eod.send',
                'display_name' => 'user.eod.send',
                'description' => '',
            ),
            array(

                'name' => 'user.resourcematrix.dashboard',
                'display_name' => 'user.resourcematrix.dashboard',
                'description' => '',
            ),
            array(

                'name' => 'user.resourcematrix.clients',
                'display_name' => 'user.resourcematrix.clients',
                'description' => '',
            ),
            array(

                'name' => 'user.resourcematrix.technologies',
                'display_name' => 'user.resourcematrix.technologies',
                'description' => '',
            ),
            array(

                'name' => 'user.resourcematrix.domains',
                'display_name' => 'user.resourcematrix.domains',
                'description' => '',
            ),
            array(

                'name' => 'user.resourcematrix.users',
                'display_name' => 'user.resourcematrix.users',
                'description' => '',
            ),
            array(

                'name' => 'user.resourcematrix.roles',
                'display_name' => 'user.resourcematrix.roles',
                'description' => '',
            ),
            array(

                'name' => 'user.resourcematrix.permissions',
                'display_name' => 'user.resourcematrix.permissions',
                'description' => '',

            ),

            array(

                'name' => 'user.dashboard.content.overview',
                'display_name' => 'user.dashboard.content.overview',
                'description' => '',

            ),
            array(

                'name' => 'user.dashboard.content.bug',
                'display_name' => 'user.dashboard.content.bug',
                'description' => '',

            ),
            array(

                'name' => 'user.dashboard.content.milestones',
                'display_name' => 'user.dashboard.content.milestones',
                'description' => '',

            ),
            array(

                'name' => 'user.dashboard.content.resourcematrix',
                'display_name' => 'user.dashboard.content.resourcematrix',
                'description' => '',

            ),
            array(

                'name' => 'user.dashboard.content.overdue.work.items',
                'display_name' => 'user.dashboard.content.overdue.work.items',
                'description' => '',

            ),
            array(

                'name' => 'user.dashboard.content.timesheet',
                'display_name' => 'user.dashboard.content.timesheet',
                'description' => '',

            ),
            array(

                'name' => 'user.dashboard.content.calendar.events',
                'display_name' => 'user.dashboard.content.calendar.events',
                'description' => '',

            ),
            array(

                'name' => 'user.dashboard.content.projects',
                'display_name' => 'user.dashboard.content.projects',
                'description' => '',

            ),
            array(

                'name' => 'user.dashboard.content.team.member',
                'display_name' => 'user.dashboard.content.team.member',
                'description' => '',

            ),
            array(

                'name' => 'user.dashboard.content.upcomin_delivery',
                'display_name' => 'user.dashboard.content.upcomin_delivery',
                'description' => '',

            ),
            array(

                'name' => 'user.dashboard.content.project_highlights',
                'display_name' => 'user.dashboard.content.project_highlights',
                'description' => '',

            ),

        );

        Permission::insert($data);

        // Create table for associating permissions to roles (Many-to-Many)
        Schema::create('permission_role', function (Blueprint $table) {
            $table->integer('permission_id')->unsigned();
            $table->integer('role_id')->unsigned();

            $table->foreign('permission_id')->references('id')->on('permissions')
                ->onUpdate('cascade')->onDelete('cascade');
            $table->foreign('role_id')->references('id')->on('roles')
                ->onUpdate('cascade')->onDelete('cascade');

            $table->primary(['permission_id', 'role_id']);
        });

        $data = array();
        for ($x = 0; $x < 50; $x++) {
            array_push($data, array(
                'role_id' => 1,
                'permission_id' => ($x + 1),
            ));
        }
        PermissionRole::insert($data);
    }

    /**
     * Reverse the migrations.
     *
     * @return  void
     */
    public function down()
    {
        Schema::drop('permission_role');
        Schema::drop('permissions');
        Schema::drop('role_user');
        Schema::drop('roles');
    }
}
