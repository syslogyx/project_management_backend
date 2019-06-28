<?php

return [

    /*
     * HRMS URL constant
     */
     'HRMS_LIVE_URL' => 'http://172.16.1.180/',
    //'HRMS_LIVE_URL' => 'https://hrms.syslogyx.com/',
    //'HRMS_LIVE_URL' => 'http://hrmstest.vyako.com/',

    /*
     * Add feed constants
     */
    'FEED_CONSTANTS' => [
        'PROJECT' => 'project',
        'MILESTONE' => 'milestone',
        'TASK' => 'task',
        'PROJECT_STATUS_UPDATED' => 'project_status_updated',
    ],

    /*
     * Add feed constants
     */
    'FEED_CONSTANTS_MSGS' => [
        'PROJECT_CREATE' => '%s created by %s.',

        'PROJECT_NAME_CHANGED' => '%s name updated by %s from ',
        'PROJECT_START_DATE_CHANGED' => '%s start date updated by %s from ',
        'PROJECT_END_DATE_CHANGED' => '%s end date updated by %s from ',
        'PROJECT_DESC_CHANGED' => '%s description updated by %s from ',
        'PROJECT_MANAGER_CHANGED' => '%s manager changed by %s from ',
        'PROJECT_STATUS_UPDATED' => '%s status updated by %s from ',
        'PROJECT_RESOURCE_ADDED' => '%s started working on %s for %s',
        'PROJECT_RESOURCE_UPDATED' => '%s removed from the project %s.',

        'MILESTONE_CREATE' => '%s created by %s.',
        'MILESTONE_TITLE_CHANGED' => '%s title updated by %s from ',
        'MILESTONE_DESC_CHANGED' => 'Milestone description has been changed by %s from ',
        'MILESTONE_START_DATE_CHANGE' => '%s start date updated by %s from ',
        'MILESTONE_END_DATE_CHANGED' => '%s end date updated by %s from ',
        'MILESTONE_STATUS_UPDATED' => '%s status updated by %s from ',

        // 'MILESTONE_INDEX_CHANGES' => 'Milestone index has been changed by %s from ',

        'TASK_CREATE' => '%s created %s for milestone %s.',
        'TASK_TITLE_CHANGED' => '%s changed title from ',
        'TASK_DESC_CHANGED' => '%s changed description from ',
        'TASK_STATUS_UPDATED' => '%s changed status from ',
        'TASK_PRIORITY_UPDATED' => '%s changed priority from ',

        'TASK_START_DATE_CHANGE' => '%s updated start date to ',
        'TASK_COMPLETION_CHANGED' => '%s updated completion date to ',
        // 'TASK_SPENT_TIME' => 'Task spent time has been changed by %s to ',

        'TASK_RESOLVED_COMMENT' => 'Task Resolved by %s. Added a new comment <b>%s</b>.',

        'TASK_ESTIMATED_TIME' => '%s updated estimated time to ',

        'TASK_ASSIGNED_CREATION' => 'Assigned to %s by %s.',
        'TASK_ASSIGNED_UPDATION' => 'Assignee changed by %s from %s to %s.',

        'TASK_COMMENT_ADDED' => '%s added a new comment %s.',
        'TASK_COMMENT_UPDATE' => '%s updated comment to ',

        // 'MILESTONE_UPDATE' => 'Milestone updated ',
        // 'TASK_CREATE' => 'Task created.',
        'TASK_UPDATE' => 'Task updated ',
        'TASK_PENDING_APPROVAL_MSG' => 'Task is pending for approval due to overtime.</br> Overtime Reason: %s ',

        //Suvrat Issue#3352            //Line number: 72
        'TASK_EXTENSION_REQ' => '%s requested a time extension for task %s.',
        'TASK_EXTENSION_APPR' => '%s approved the request for a time extension for task %s.',
        //////////////////////
    ],

    /*
     * Add error messages
     */
    'ERROR_MESSAGES' => [
        'STATUS_UPDATE' => 'Status updated successfully.',
        'MILESTONE_NOT_FOUND' => 'Milestone could not found.',
        'UNABLE_UPDATE_MILESTONE_API' => 'Unable to update Milestones.',
        'UNABLE_UPDATE_TASK_API' => 'Unable to update Task.',
        'TASK_IS_ALREADY_CLOASED' => 'Task is cloased. You can not update status.',
        'TASK_NOT_FOUND' => 'Task could not found.',
        'INVALID_RESOURCE' => 'Resource not found.',
        'CAN_NOT_ASSIGN_TASK' => "Estimated Hrs exceeds resource's availability",
        'EOD_NOT_FOUND' => 'EOD not found.',
        'SOMETHING_WENT_WRONG' => 'Something went wrong.',
        'UNREGISTERED_MAIL_ADDREE' => 'Invalid mail address or mail address is not registered.',
        'MAIL_ADDRESS_EXPIRED' => 'Your token is expired please re-login again.',
        'INVALID_TOKEN' => 'Invalid token.',
        'ANOTHER_TASK_STARTED' => 'You already have task in progress.',
    ],

    /*
     * Add success messages
     */
    'SUCCESS_MESSAGES' => [
        'SUCCESS_MESSAGES' => 'Comment deleted successfully.',
        'COMMENT_ADDED' => 'Comment added successfully.',
        'COMMENT_UPDATED' => 'Comment updated successfully.',
        'TASK_CREATED' => 'Task created successfully.',
        'TASK_UPDATED' => 'Task updated successfully.',
        'EOD_CREATED' => 'EOD created successfully.',
        'EOD_UPDATED' => 'EOD updated successfully.',
        'MAIL_SEND' => 'We have sent you an email. Please check your mail.',
        'RESET_PASSOWRD' => 'Your password reset successfully. Please login.',
        'EOD_ALREADY_SENT' => 'EOD already sent for given date.',
        'TASK_EXTENSION_REQUEST' => 'Task extension requested successfully.',
        'TASK_EXTENSION_APPROVED' => 'Task extension approved.',
    ],

    'SUCESS_CODE' => '200',
    'ERROR_CODE' => '200',
    'ERROR_CODE_201' => '201',
    'SUCESS_MSG' => 'Success',

    /*
     * Add status constants
     */
    'STATUS_CONSTANT' => [
        'PENDING' => 'Pending',
        'ON-GOING' => 'On-going',
        'HOLD' => 'On-hold',
        'CLOSED' => 'Closed',
        'PAUSE' => 'Paused',
        'DELETED' => 'Deleted',
        'ACTIVE' => 'Active',
        'RESOLVED' => 'Resolved',
        'IN_PROGRESS' => 'In-progress',
        'ASSIGNED' => 'Assigned',
        'COMPLETED' => 'Completed',
        'START' => 'Start',
        'STOP' => 'Stop',
        'PENDING_APPROVED' => 'Approval-Pending',
        'APPROVED' => 'Approved',
    ],

    /*
     * Add status constants with ids
     */
    'STATUS_CONSTANT_IDS' => [
        '1' => 'Pending',
        '3' => 'On-hold',
        '9' => 'In-progress',
        // '6' => 'Deleted',
        '4' => 'Closed',
        "TASK_STATUS_CONS" => [
            // '3' => 'On-hold',
            '5' => 'Paused',
            // '2' => 'On-going',
            '8' => 'Resolved',
            // '9' => 'In-progress',
            '10' => 'Assigned',
            '14' => 'Approval-Pending',
            '12' => 'Start',
            // '13' => 'Stop',
        ],
        "PROJECT_STATUS_CONS" => [
            '7' => 'Active',
            // '3' => 'On-hold',
            // '1' => 'Pending',
            // '3' => 'On-hold',
            // '9' => 'In-progress',
            // '6' => 'Deleted',
            // '4' => 'Closed',
        ],
        "MILESTONE_STATUS_CONS" => [
            // '9' => 'In-progress',
            // '3' => 'On-hold',
            // '11' => 'Completed',
            // '1' => 'Pending',
            // '3' => 'On-hold',
            // '9' => 'In-progress',
            // '6' => 'Deleted',
            // '4' => 'Closed',
        ],
    ],

    /*
     * URL identifier
     */
    'URL_CONSTANTS' => [
        'MILESTONE' => 'milestone',
        'TASK' => 'task',
        'PROJECT' => 'project',
    ],

    /*
     * Web URL identifier
     */
    'WEB_URL_CONSTANTS' => [
        'MILESTONE_VIEW' => '/project/milestone/view/',
        'TASK_VIEW' => 'task',
        'USER_VIEW' => '/user/info/',
        'PROJECT_VIEW' => '/project/view/',
    ],

    /*
     * Add comment identifiers constants
     */
    'COMMENT_IDENTIFIER_CONST' => [
        'MILESTONE' => 'MILESTONE',
        'TASK' => 'TASK',
    ],

    /*
     * Priority constants
     */
    'PRIORITY_CONST' => [
        'HIGH' => 1,
        'MEDIUM' => 2,
        'LOW' => 3,
    ],

    'PRIORITY_CONST_NAME' => [
        1 => 'High',
        2 => 'Medium',
        3 => 'Low',
    ],
];
