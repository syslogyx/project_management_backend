    <?php

use Illuminate\Http\Request;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
 */

Route::middleware('auth:api')->get('/user', function (Request $request) {
    return $request->user();
});

$api = app("Dingo\Api\Routing\Router");

$api->version("v1", function ($api) {

    $api->get("/config-clear", "App\Http\Controllers\HomeController@config_clear");
    $api->get("/schedule-run", "App\Http\Controllers\HomeController@schedule_run");

    // generate token
    $api->post("authenticate", "App\Http\Controllers\Auth\AuthController@authenticate");

    $api->get("user/login_data/{user_id}", "App\Http\Controllers\Auth\AuthController@getLoginUserData");

    $api->put("{identifier}/update_status", "App\Http\Controllers\CommonController@update_status");

    // test api
    $api->get("hello", "App\Http\Controllers\HomeController@index");

    //user role and permission api
    $api->get("users/{user_id}/add/roles", "App\Http\Controllers\HomeController@attachUserRole");

    $api->get("users/{user_id}/roles", "App\Http\Controllers\HomeController@getUserRole");

    $api->post("role/{role_name}/permission/add", "App\Http\Controllers\HomeController@attachPermission");

    $api->get("role/{role_name}/permissions", "App\Http\Controllers\HomeController@getPremissions");

    $api->get("permissions", "App\Http\Controllers\HomeController@getAllPremissions");

    $api->get("roles", "App\Http\Controllers\HomeController@getAllRoles");

    $api->post("create/permission", "App\Http\Controllers\HomeController@createPermissions");

    $api->post("create/role", "App\Http\Controllers\HomeController@createRoles");

    $api->post("role/{id}/update", "App\Http\Controllers\HomeController@updateRoles");

    $api->post("permission/{id}/update", "App\Http\Controllers\HomeController@updatePermissions");

    $api->get("role/{id}/view", "App\Http\Controllers\HomeController@viewRoles");

    $api->get("permission/{id}/view", "App\Http\Controllers\HomeController@viewPermissions");

    $api->delete("user/{user_id}/role/{role_id}", "App\Http\Controllers\HomeController@deleteRolesOfUser");

    $api->delete("permission/{permission_id}/delete", "App\Http\Controllers\HomeController@deletePermission");

    // technology's api
    $api->get("technologies", "App\Http\Controllers\TechnologyController@index");

    $api->post("technology/create", "App\Http\Controllers\TechnologyController@create");

    $api->put("technology/{id}/update", "App\Http\Controllers\TechnologyController@update");

    $api->get("technology/{id}/view", "App\Http\Controllers\TechnologyController@view");

    $api->delete("technology/{technology_id}/delete", "App\Http\Controllers\TechnologyController@deleteTechnology");

    // domain's api
    $api->get("domain", "App\Http\Controllers\DomainController@index");

    $api->post("domain/create", "App\Http\Controllers\DomainController@create");

    $api->put("domain/{id}/update", "App\Http\Controllers\DomainController@update");

    $api->get("domain/{id}/view", "App\Http\Controllers\DomainController@view");

    // clients's api
    $api->get("clients", "App\Http\Controllers\ClientController@index");

    $api->post("client/create", "App\Http\Controllers\ClientController@create");

    $api->put("client/{id}/update", "App\Http\Controllers\ClientController@update");

    $api->get("client/{id}/view", "App\Http\Controllers\ClientController@view");

    $api->post("client/{client_id}/delete", "App\Http\Controllers\ClientController@deleteClient");

    $api->post("eod/cronjob_eod", "App\Http\Controllers\ClientController@cronjobEOD");

    // project document's api
    $api->post("project-document/upload", "App\Http\Controllers\ProjectDocumentController@upload");

    // activity type's api
    $api->get("activity-types", "App\Http\Controllers\ActivityTypeController@index");

    $api->post("activity-type/create", "App\Http\Controllers\ActivityTypeController@create");

    $api->put("activity-type/{id}/update", "App\Http\Controllers\ActivityTypeController@update");

    $api->get("activity-type/{id}/view", "App\Http\Controllers\ActivityTypeController@view");

    // user's api
    $api->get("users", "App\Http\Controllers\UserController@index");
    $api->get("users_data", "App\Http\Controllers\UserController@indexData");

    $api->get("user/sync", "App\Http\Controllers\UserController@sync");

    $api->get("user/{email}", "App\Http\Controllers\UserController@show");

    $api->get("user/{id}/view", "App\Http\Controllers\UserController@view");

    $api->get("user/sync/{id}", "App\Http\Controllers\UserController@sync_user_by_id");

    $api->get("user/{id}/pdf", "App\Http\Controllers\UserController@generatePDFForResources");

    $api->get("user/manager_or_lead/{hrms_role_id}", "App\Http\Controllers\UserController@getAllManagerOrLeadList");

    $api->get("user_list/task_creation", "App\Http\Controllers\UserController@getUserListForTaskCretaion");

    $api->delete("user/{user_id}/delete", "App\Http\Controllers\UserController@deleteUser");

    // project's api
    $api->get("projects", "App\Http\Controllers\ProjectController@index");

    $api->post("project/create", "App\Http\Controllers\ProjectController@create");

    $api->put("project/{id}/update", "App\Http\Controllers\ProjectController@update");

    $api->get("project/{id}/view", "App\Http\Controllers\ProjectController@view");

    $api->get("project/{id}/task", "App\Http\Controllers\ProjectController@ptojectTaskList");

    $api->put("project/status_update", "App\Http\Controllers\ProjectController@update_status");

    // $api->post("project/list", "App\Http\Controllers\ProjectController@getProjectResourceList");

    $api->post("project/list", "App\Http\Controllers\ProjectResourceController@getProjectResourceList");

    $api->get("project/{id}/pdf", "App\Http\Controllers\ProjectController@generatePdfForProjects");

    // task's api
    $api->get("tasks", "App\Http\Controllers\TaskController@index");

    $api->post("task/create", "App\Http\Controllers\TaskController@create");

    $api->post("tasks/list", "App\Http\Controllers\TaskController@getTaskList");

    $api->put("task/{id}/update", "App\Http\Controllers\TaskController@update");

    $api->get("task/{id}/view", "App\Http\Controllers\TaskController@view");

    $api->get("task/taskByMilestoneId/{id}", "App\Http\Controllers\TaskController@task_by_milestone_id");

    $api->put("task/update-status", "App\Http\Controllers\TaskController@updateStatus");

    $api->get("tasks/sendmail", "App\Http\Controllers\TaskController@sendMail");

    $api->get("task/{milestone_id}/assigned_users", "App\Http\Controllers\TaskController@getAssignedUserList");

    $api->post("task/extend_time", "App\Http\Controllers\TaskController@updateApprovalExtension");

    // milestone's api
    $api->get("milestones", "App\Http\Controllers\MilestoneController@index");

    $api->post("milestone/create", "App\Http\Controllers\MilestoneController@create");

    $api->put("milestone/{id}/update", "App\Http\Controllers\MilestoneController@update");

    $api->get("milestone/{id}/view", "App\Http\Controllers\MilestoneController@view");

    $api->post("milestone/list", "App\Http\Controllers\MilestoneController@getMilestoneList");

    $api->get("project/{projectId}/milestone/{milestneIndex}", "App\Http\Controllers\MilestoneController@getCurrentMilestone");

    $api->get("project/{projectId}/milestneIndex", "App\Http\Controllers\MilestoneController@getCurrentMilestoneIndex");

    $api->get("milestone-by-project/{projectId}/view", "App\Http\Controllers\MilestoneController@milestoneByProject");

    $api->put("milestone/update-dates", "App\Http\Controllers\MilestoneController@updateDates");

    // Project Resource's api
    $api->get("project-resources", "App\Http\Controllers\ProjectResourceController@index");

    $api->post("project-resource/create", "App\Http\Controllers\ProjectResourceController@create");

    $api->put("project-resource/{id}/update", "App\Http\Controllers\ProjectResourceController@update");

    $api->get("project-resource/{id}/view", "App\Http\Controllers\ProjectResourceController@view");

    $api->get("project-resource/project/{projectId}/view", "App\Http\Controllers\ProjectResourceController@view_by_project_id");

    //new-update project resorce date
    $api->put("project-resource/udate", "App\Http\Controllers\ProjectResourceTechnologyController@update_dates");
    $api->put("project-resource/udate/date", "App\Http\Controllers\ProjectResourceTechnologyController@update_date_for_resourse");
    // $api->put("project-resource/task/is-assign", "App\Http\Controllers\ProjectResourceTechnologyController@checkIsTaskAssign");

    // Status api
    $api->get("status", "App\Http\Controllers\StatusController@index");

    $api->post("status/create", "App\Http\Controllers\StatusController@create");

    $api->put("status/{id}/update", "App\Http\Controllers\StatusController@update");

    $api->get("status/{id}/view", "App\Http\Controllers\StatusController@view");

    // Task Comment Logs api
    $api->get("task-comment-logs", "App\Http\Controllers\TaskCommentLogController@index");

    $api->post("task-comment-log/create", "App\Http\Controllers\TaskCommentLogController@create");

    $api->put("task-comment-log/{id}/update", "App\Http\Controllers\TaskCommentLogController@update");

    $api->get("task-comment-log/{id}/view", "App\Http\Controllers\TaskCommentLogController@view");

    // Technical support's api
    $api->get("technical-supports", "App\Http\Controllers\TechnicalSuportController@index");

    $api->post("technical-support/create", "App\Http\Controllers\TechnicalSuportController@create");

    $api->put("technical-support/{id}/update", "App\Http\Controllers\TechnicalSuportController@update");

    $api->get("technical-support/{id}/view", "App\Http\Controllers\TechnicalSuportController@view");

    // Project Activity Status Logs api
    $api->get("project-activity-status-logs", "App\Http\Controllers\ProjectActivityStatusLogController@index");

    $api->post("project-activity-status-log/create", "App\Http\Controllers\ProjectActivityStatusLogController@create");

    $api->put("project-activity-status-log/{id}/update", "App\Http\Controllers\ProjectActivityStatusLogController@update");

    $api->get("project-activity-status-log/{id}/view", "App\Http\Controllers\ProjectActivityStatusLogController@view");

    $api->post("project-activity-status-log/view-by-project-id", "App\Http\Controllers\ProjectActivityStatusLogController@view_by_id");

    // Category's api
    $api->get("categories", "App\Http\Controllers\CategoryController@index");

    $api->post("category/create", "App\Http\Controllers\CategoryController@create");

    $api->put("category/{id}/update", "App\Http\Controllers\CategoryController@update");

    $api->get("category/{id}/view", "App\Http\Controllers\CategoryController@view");

    $api->delete("category/{category_id}/delete", "App\Http\Controllers\CategoryController@deleteCategory");

    // User technology mapping api
    $api->get("user-technology-mapping", "App\Http\Controllers\UserTechnologyMappingController@index");

    $api->post("user-technology-mapping/create", "App\Http\Controllers\UserTechnologyMappingController@create");

    $api->put("user-technology-mapping/{id}/update", "App\Http\Controllers\UserTechnologyMappingController@update");

    $api->get("user-technology-mapping/{id}/view", "App\Http\Controllers\UserTechnologyMappingController@view");

    $api->delete("user-technology-mapping/{id}/delete", "App\Http\Controllers\UserTechnologyMappingController@delete");

    //user list technology wise
    $api->get("technology/{id}/users", "App\Http\Controllers\UserTechnologyMappingController@getUserListByTechnology");

    $api->post("technology/users", "App\Http\Controllers\UserTechnologyMappingController@getUserListByTechnologies");

    //Search
    $api->post('/search', 'App\Http\Controllers\SearchController@filter');

    $api->post('/mom/filter', 'App\Http\Controllers\SearchController@filterMoM');

    // Resource matrix log api
    $api->post("matrix/update-date", "App\Http\Controllers\ResourceMatrixController@create");

    $api->get("matrix", "App\Http\Controllers\ResourceMatrixController@index");

    //Remote validation
    $api->post("validate", "App\Http\Controllers\RemoteValidationController@check_validation");

    // Project poc api
    $api->get("project-poc", "App\Http\Controllers\ProjectPocController@index");

    $api->post("project-poc/create", "App\Http\Controllers\ProjectPocController@create");

    $api->put("project-poc/{id}/update", "App\Http\Controllers\ProjectPocController@update");

    $api->get("project-poc/{id}/view", "App\Http\Controllers\ProjectPocController@view");

    $api->get("/{id}/project-poc", "App\Http\Controllers\ProjectPocController@getPocByProjectId");

    $api->post("project-poc/change_status", "App\Http\Controllers\ProjectPocController@changePOCStatus");

    //category wise tecgnology
    $api->get("category/{categoryId}/technologies", "App\Http\Controllers\CategoryTechnologyMappingController@listTechnologyCategoryWise");
    $api->post("category/technologies", "App\Http\Controllers\CategoryController@listTechnologyMultipleCategoryWise");

    //category wise tecgnology of particular project
    $api->get("project-category/{proj_categoryId}/technologies", "App\Http\Controllers\CategoryTechnologyMappingController@listProjectDomainWiseTechnologies");

    //project wise category
    $api->get("project/{project_id}/domains", "App\Http\Controllers\ProjectCategoryMappingController@domains_of_project_id");

    //Create domain and technology of project
    $api->post("project/domain-technology/create", "App\Http\Controllers\ProjectController@createProjectCategoryTechnologies");

    //delete domain and technology of project
    $api->delete("project/domain/{project_category_id}/delete", "App\Http\Controllers\ProjectCategoryMappingController@deleteProjectCategory");

    $api->delete("project/domain/{project_category_id}/technology/{technology_id}/delete/{user_id}", "App\Http\Controllers\ProjectCategoryTechnologyController@deleteProjectTechnology");

    //Add project technologies
    $api->post("project/domain-technology/add", "App\Http\Controllers\ProjectCategoryTechnologyController@addProjectTechnology");

    //Add user technology wise
    $api->get("project-resource/list", "App\Http\Controllers\ProjectResourceTechnologyController@index");

    $api->post("project-resource/add", "App\Http\Controllers\ProjectResourceTechnologyController@create");

    //$api->put("project-resource/{id}/edit", "App\Http\Controllers\ProjectResourceTechnologyController@update");

    $api->delete("project-resource/{id}/delete", "App\Http\Controllers\ProjectResourceTechnologyController@deleteResource");

    //technology list user wise
    $api->get("users/{id}/technologies", "App\Http\Controllers\UserTechnologyMappingController@getTechnologyListByUser");

    $api->get("users/{id}/domain/{domain_id}", "App\Http\Controllers\UserTechnologyMappingController@getTechnologyListOfUser");

    $api->get("users/{id}/milestone", "App\Http\Controllers\MilestoneController@getPendingTaskList");

    // domain list user wise
    $api->get("users/{id}/domains", "App\Http\Controllers\UserTechnologyMappingController@getDomainListByUser");

    //project list of user
    $api->get("user/{user_id}/projects/type/{type}", "App\Http\Controllers\UserController@getProjectListByUser");

    //get user logs of resource matrix
    $api->post("resource-matrix/logs", "App\Http\Controllers\ResourceMatrixController@getLogs");

    //Update manager
    $api->post("project/update-manager", "App\Http\Controllers\ProjectController@updateManager");

    // MOM
    $api->get("moms", "App\Http\Controllers\MomController@index");

    $api->post("mom/create", "App\Http\Controllers\MomController@create");

    $api->put("mom/{id}/update", "App\Http\Controllers\MomController@update");

    $api->get("mom/{id}/view", "App\Http\Controllers\MomController@view");

    $api->get("mom/{id}/remove", "App\Http\Controllers\MomController@removeMoM");

    $api->get("mom/clients", "App\Http\Controllers\MomController@client_index");

    $api->post("mom/task/create", "App\Http\Controllers\MomTasksController@createTasks");

    $api->post("mom/task-status/update", "App\Http\Controllers\MomTasksController@updateStatus");

    $api->post("mom/status/update", "App\Http\Controllers\MomController@updateStatus");

    // Comments api
    $api->post("comment/add", "App\Http\Controllers\CommentController@add");

    $api->put("comment/{id}/update", "App\Http\Controllers\CommentController@update");

    $api->delete("comment/{id}/delete", "App\Http\Controllers\CommentController@delete");

    $api->post("project-logs/list", "App\Http\Controllers\ProjectLogsController@getLogsList");

    // menus api
    $api->get("menus", "App\Http\Controllers\MenuController@index");

    $api->get("user/menus/{userId}", "App\Http\Controllers\MenuController@getMenuList");

    //eod api
    $api->post("eod/tasks/list", "App\Http\Controllers\EODReportController@getEODReportTaskList");

    $api->post("eod/todays_working_log", "App\Http\Controllers\EODReportController@getEODReportTaskAndTimingList");

    $api->post("eod/createnew", "App\Http\Controllers\EODReportController@createEODNew");

    $api->put("eod/create", "App\Http\Controllers\EODReportController@createEOD");

    $api->post("eod/{id}/update", "App\Http\Controllers\EODReportController@updateEOD");

    $api->post("eod/list", "App\Http\Controllers\EODReportController@getEODReportList");

    $api->get("eod/{id}/view", "App\Http\Controllers\EODReportController@viewEOD");

    $api->get("eod/{id}/view/{user_id}", "App\Http\Controllers\EODReportController@viewEODData");

    $api->put("eod/task-comment/add", "App\Http\Controllers\EODReportController@addEODTaskComment");
    $api->get("eod/{user_id}/user_list", "App\Http\Controllers\EODReportController@getUserListUnderLead");

    $api->post("eod/update_hrms_time", "App\Http\Controllers\EODReportController@updateHRMSTimeInEOD");

    $api->get("eod/status/{user_id}", "App\Http\Controllers\EODReportController@checkTodaysEODStatus");

    //forgot password api
    $api->post("users/forgot-password", "App\Http\Controllers\UserController@forgetPassword");

    $api->get("users/password-expiry", "App\Http\Controllers\UserController@passwordExpiry");

    $api->post("users/reset-password", "App\Http\Controllers\UserController@resetPassword");

    //API for dashboards
    $api->get("dashboard/projects", "App\Http\Controllers\ProjectController@topFiveProjectListWithResourcesCount");

    $api->get("dashboard/resource-matrix", "App\Http\Controllers\CategoryController@categoryListWithResourcesCount");

    $api->get("dashboard/project-highlights", "App\Http\Controllers\ProjectController@getProjectHighlights");

    $api->get("dashboard/delivery-schedule", "App\Http\Controllers\ProjectController@getDeliverySchedules");

    $api->post("dashboard/task-info", "App\Http\Controllers\TaskController@getTaskInfo");

    $api->post("dashboard/task-info/filter", "App\Http\Controllers\TaskController@filterTaskInfo");

    $api->get("getTime", "App\Http\Controllers\TaskController@getCurrentDateTime");

    $api->get("getTaskStatus/user/{user_id}", "App\Http\Controllers\TaskController@checkIfTaskIsStartedOrNotAPI");

});

$api->version("v1", ['middleware' => 'api.auth'], function ($api) {
    // $api->get("technologies", "App\Http\Controllers\TechnologyController@index");
    // $api->get("task/{id}/view", "App\Http\Controllers\TaskController@view");
});
