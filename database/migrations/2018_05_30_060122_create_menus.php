<?php

use App\Menu;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateMenus extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('menus', function (Blueprint $table) {
            $table->increments('id');

            $table->integer('parent_id')->unsigned()->nullable();
            $table->string('menu_name');
            $table->string('permissionTag')->nullable();
            $table->text('desc')->nullable();
            $table->string('url')->nullable();
            $table->string('icon');
            $table->integer('type_id')->unsigned();
            $table->foreign('type_id')->references('id')->on('menu_type')
                ->onUpdate('cascade')->onDelete('cascade');

            $table->timestamps();
        });

        $data = array(
            array(
                'parent_id' => 1,
                'menu_name' => 'Dashboard',
                'desc' => "",
                'permissionTag' => 'user.projects.dashboard',
                'url' => '/project/view',
                'icon' => 'fa fa-dashboard',
                'type_id' => 1,
            ),
            array(
                'parent_id' => 2,
                'menu_name' => 'Feeds',
                'desc' => "",
                'permissionTag' => 'user.projects.feeds',
                'url' => '/feed',
                'icon' => 'fa fa-newspaper-o',
                'type_id' => 1,
            ),
            array(
                'parent_id' => 3,
                'menu_name' => 'Milestones',
                'desc' => "",
                'permissionTag' => 'user.projects.milestones',
                'url' => '/milestone',
                'icon' => 'fa fa-flag-o',
                'type_id' => 1,
            ),
            array(
                'parent_id' => 4,
                'menu_name' => 'Tasks',
                'desc' => "",
                'permissionTag' => 'user.projects.tasks',
                'url' => '/task',
                'icon' => 'fa fa-check-square-o',
                'type_id' => 1,
            ),
            array(
                'parent_id' => 5,
                'menu_name' => 'Calendar',
                'desc' => "",
                'permissionTag' => 'user.projects.calendar',
                'url' => '/calendar',
                'icon' => 'fa fa-calendar',
                'type_id' => 1,
            ),
            array(
                'parent_id' => 6,
                'menu_name' => 'Documents',
                'desc' => "",
                'permissionTag' => 'user.projects.documents',
                'url' => '/document',
                'icon' => 'fa fa-file-text-o',
                'type_id' => 1,
            ),
            array(
                'parent_id' => 7,
                'menu_name' => 'Timesheet',
                'desc' => "",
                'permissionTag' => 'user.projects.timesheet',
                'url' => '/time-sheet',
                'icon' => 'fa fa-file-text-o',
                'type_id' => 1,
            ),
            array(
                'parent_id' => 8,
                'menu_name' => 'Resources',
                'desc' => "",
                'permissionTag' => 'user.projects.resources',
                'url' => '/project/resource',
                'icon' => 'fa fa-users',
                'type_id' => 1,
            ),
            array(
                'parent_id' => 9,
                'menu_name' => 'Project POC',
                'desc' => "",
                'permissionTag' => 'user.projects.project.poc',
                'url' => '/project-poc',
                'icon' => 'fa fa-hand-o-left',
                'type_id' => 1,
            ),
            array(
                'parent_id' => 10,
                'menu_name' => 'Project Info',
                'desc' => "",
                'permissionTag' => '',
                'url' => '/project/info',
                'icon' => 'fa fa-info',
                'type_id' => 1,
            ),
            array(
                'parent_id' => 10,
                'menu_name' => 'General Information',
                'desc' => "",
                'permissionTag' => 'user.projects.project.info.general',
                'url' => 'project/info/genral-info',
                'icon' => 'fa fa-info',
                'type_id' => 1,
            ),
            array(
                'parent_id' => 10,
                'menu_name' => 'Domain/Technology Information',
                'desc' => "",
                'permissionTag' => 'user.projects.project.info.domain',
                'url' => 'project/info/domain-info/',
                'icon' => 'fa fa-info',
                'type_id' => 1,
            ),

            array(
                'parent_id' => 13,
                'menu_name' => 'Dashboard',
                'desc' => "",
                'permissionTag' => 'user.dashboard',
                'url' => '',
                'icon' => 'fa fa-dashboard',
                'type_id' => 2,
            ),
            array(
                'parent_id' => 14,
                'menu_name' => 'Feed',
                'desc' => "",
                'permissionTag' => 'user.feed',
                'url' => 'all-feeds',
                'icon' => 'fa fa-hand-o-left',
                'type_id' => 2,
            ),
            array(
                'parent_id' => 15,
                'menu_name' => 'Project',
                'desc' => "",
                'permissionTag' => 'user.projects',
                'url' => 'all-projects',
                'icon' => 'fa fa-info',
                'type_id' => 2,
            ),

            array(
                'parent_id' => 16,
                'menu_name' => 'EOD',
                'desc' => "",
                'permissionTag' => '',
                'url' => '',
                'icon' => 'fa fa-info',
                'type_id' => 2,
            ),
            array(
                'parent_id' => 16,
                'menu_name' => 'EOD History',
                'desc' => "",
                'permissionTag' => 'user.eod.history',
                'url' => '/eod/eod_list',
                'icon' => 'fa fa-info',
                'type_id' => 2,
            ),
            array(
                'parent_id' => 16,
                'menu_name' => 'Send EOD',
                'desc' => "",
                'permissionTag' => 'user.eod.send',
                'url' => '/eod/send',
                'icon' => 'fa fa-info',
                'type_id' => 2,
            ),

            array(
                'parent_id' => 17,
                'menu_name' => 'Dashboard',
                'desc' => "",
                'permissionTag' => 'user.resourcematrix.dashboard',
                'url' => '/matrix',
                'icon' => 'fa fa-dashboard',
                'type_id' => 3,
            ),
            array(
                'parent_id' => 18,
                'menu_name' => 'Clients',
                'desc' => "",
                'permissionTag' => 'user.resourcematrix.clients',
                'url' => '/client',
                'icon' => 'fa fa-newspaper-o',
                'type_id' => 3,
            ),
            array(
                'parent_id' => 19,
                'menu_name' => 'Technologies',
                'desc' => "",
                'permissionTag' => 'user.resourcematrix.technologies',
                'url' => '/technology',
                'icon' => 'fa fa-flag-o',
                'type_id' => 3,
            ),
            array(
                'parent_id' => 20,
                'menu_name' => 'Domains',
                'desc' => "",
                'permissionTag' => 'user.resourcematrix.domains',
                'url' => '/category',
                'icon' => 'fa fa-check-square-o',
                'type_id' => 3,
            ),
            array(
                'parent_id' => 21,
                'menu_name' => 'Users',
                'desc' => "",
                'permissionTag' => 'user.resourcematrix.users',
                'url' => '/user',
                'icon' => 'fa fa-calendar',
                'type_id' => 3,
            ),
            array(
                'parent_id' => 22,
                'menu_name' => 'Roles',
                'desc' => "",
                'permissionTag' => 'user.resourcematrix.roles',
                'url' => '/roles',
                'icon' => 'fa fa-file-text-o',
                'type_id' => 3,
            ),
            array(
                'parent_id' => 23,
                'menu_name' => 'Permissions',
                'desc' => "",
                'permissionTag' => 'user.resourcematrix.permissions',
                'url' => '/permissions',
                'icon' => 'fa fa-file-text-o',
                'type_id' => 3,
            ),

            array(
                'parent_id' => 24,
                'menu_name' => 'My Overview',
                'url' => '',
                'permissionTag' => 'user.dashboard.content.overview',
                'desc' => "Get a bird's-eye view of your Tasks, Bugs, and Milestones",
                'icon' => 'fa fa-file-text-o',
                'type_id' => 4,
            ),
            array(
                'parent_id' => 25,
                'menu_name' => 'My Bugs',
                'url' => '',
                'permissionTag' => 'user.dashboard.content.bug',
                'desc' => "View the details of the Bugs that need your attention",
                'icon' => 'fa fa-file-text-o',
                'type_id' => 4,
            ),
            array(
                'parent_id' => 26,
                'menu_name' => 'My Milestones',
                'url' => '',
                'permissionTag' => 'user.dashboard.content.milestones',
                'desc' => "Keep an eye on your overdue and upcoming milestones",
                'icon' => 'fa fa-file-text-o',
                'type_id' => 4,
            ),
            array(
                'parent_id' => 27,
                'menu_name' => 'Resource Matrix',
                'url' => '/matrix',
                'permissionTag' => 'user.dashboard.content.resourcematrix',
                'desc' => "Know more about your resources",
                'icon' => 'fa fa-file-text-o',
                'type_id' => 4,
            ),
            array(
                'parent_id' => 28,
                'menu_name' => 'My Overdue Work Items',
                'url' => '',
                'permissionTag' => 'user.dashboard.content.overdue.work.items',
                'desc' => "",
                'icon' => 'fa fa-file-text-o',
                'type_id' => 4,
            ),
            array(
                'parent_id' => 29,
                'menu_name' => 'My Timesheet',
                'url' => '',
                'permissionTag' => 'user.dashboard.content.timesheet',
                'desc' => "",
                'icon' => 'fa fa-file-text-o',
                'type_id' => 4,
            ),
            array(
                'parent_id' => 30,
                'menu_name' => 'My Calendar - Events',
                'url' => '',
                'permissionTag' => 'user.dashboard.content.calendar.events',
                'desc' => "",
                'icon' => 'fa fa-file-text-o',
                'type_id' => 4,
            ),
            array(
                'parent_id' => 31,
                'menu_name' => 'Projects',
                'url' => '/dashboard-projects',
                'permissionTag' => 'user.dashboard.content.projects',
                'desc' => "",
                'icon' => 'fa fa-file-text-o',
                'type_id' => 4,
            ),
            array(
                'parent_id' => 32,
                'menu_name' => 'Team Members',
                'url' => '/dashboard-users',
                'permissionTag' => 'user.dashboard.content.team.member',
                'desc' => "",
                'icon' => 'fa fa-file-text-o',
                'type_id' => 4,
            ),

        );

        Menu::insert($data);
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('menus');
    }
}
