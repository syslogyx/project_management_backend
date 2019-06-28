<?php
namespace App;

use Config;

/**
 * @author Kalyani
 */
class Utilities
{

    /**
     * Kalyani : get status list
     */
    public static function getStatusList($type)
    {
        $statusArray = array();
        $statusList = Config::get('constants.STATUS_CONSTANT_IDS');

        //collect common status
        $statusArray = Utilities::statusList($statusList, $statusArray);

        //collect status according to the identifier
        if ($type == Config::get('constants.URL_CONSTANTS.TASK')) {
            $statusList = Config::get('constants.STATUS_CONSTANT_IDS.TASK_STATUS_CONS');
            $statusArray = Utilities::statusList($statusList, $statusArray);
        } elseif ($type == Config::get('constants.URL_CONSTANTS.PROJECT')) {
            $statusList = Config::get('constants.STATUS_CONSTANT_IDS.PROJECT_STATUS_CONS');
            $statusArray = Utilities::statusList($statusList, $statusArray);
        } elseif ($type == Config::get('constants.URL_CONSTANTS.MILESTONE')) {
            $statusList = Config::get('constants.STATUS_CONSTANT_IDS.MILESTONE_STATUS_CONS');
            $statusArray = Utilities::statusList($statusList, $statusArray);
        }
        return $statusArray;
    }

    private static function statusList($statusList, $statusArray)
    {
        foreach ($statusList as $key => $value) {
            if ((int) $key > 0) {
                $status = array("id" => $key, "name" => $value);
                array_push($statusArray, $status);
            }
        }
        return $statusArray;
    }

    /**
     * Kalyani : calculate time difference
     */
    public static function calculateTimeDifference($startdate, $endDate)
    {
        $seconds = strtotime($endDate) - strtotime($startdate);

        $days = floor($seconds / 86400);
        $hours = floor(($seconds - ($days * 86400)) / 3600);
        $minutes = floor(($seconds - ($days * 86400) - ($hours * 3600)) / 60);
        $seconds = floor(($seconds - ($days * 86400) - ($hours * 3600) - ($minutes * 60)));

        // $finalTime = $hours.":".$minutes.":".$seconds;
        // return $finalTime;
        return sprintf('%02d:%02d:%02d', $hours, $minutes, $seconds);
    }

    /**
     * Kalyani : sum time
     */
    public static function sumTheTime($time1, $time2)
    {
        if ($time1 == "") {
            $time1 = "00:00:00";
        }
        $times = array($time1, $time2);
        $seconds = 0;
        foreach ($times as $time) {
            list($hour, $minute, $second) = explode(':', $time);
            $seconds += $hour * 3600;
            $seconds += $minute * 60;
            $seconds += $second;
        }
        $hours = floor($seconds / 3600);
        $seconds -= $hours * 3600;
        $minutes = floor($seconds / 60);
        $seconds -= $minutes * 60;

        // return "{$hours}:{$minutes}:{$seconds}";
        return sprintf('%02d:%02d:%02d', $hours, $minutes, $seconds);
    }

    /**
     * Kalyani : Get the working  days for two days
     */
    public static function getWorkdays($date1, $date2, $workSat = false, $patron = null)
    {
        if (!defined('SATURDAY')) {
            define('SATURDAY', 6);
        }

        if (!defined('SUNDAY')) {
            define('SUNDAY', 0);
        }

        // Array of all public festivities
        $publicHolidays = array();
        // $publicHolidays = array('01-01', '01-06', '04-25', '05-01', '06-02', '08-15', '11-01', '12-08', '12-25', '12-26');

        // The Patron day (if any) is added to public festivities
        if ($patron) {
            $publicHolidays[] = $patron;
        }

        /*
         * Array of all Easter Mondays in the given interval
         */
        $yearStart = date('Y', strtotime($date1));
        $yearEnd = date('Y', strtotime($date2));

        for ($i = $yearStart; $i <= $yearEnd; $i++) {
            $easter = date('Y-m-d', easter_date($i));
            list($y, $m, $g) = explode("-", $easter);
            $monday = mktime(0, 0, 0, date($m), date($g) + 1, date($y));
            $easterMondays[] = $monday;
        }

        $start = strtotime($date1);
        $end = strtotime($date2);
        $workdays = 0;
        for ($i = $start; $i <= $end; $i = strtotime("+1 day", $i)) {
            $day = date("w", $i); // 0=sun, 1=mon, ..., 6=sat
            $mmgg = date('m-d', $i);
            if ($day != SUNDAY &&
                !in_array($mmgg, $publicHolidays) &&
                !in_array($i, $easterMondays) &&
                !($day == SATURDAY && $workSat == false)) {
                $workdays++;
            }
        }
        return intval($workdays);
    }

    /**
     * Kalyani : generate random token number
     */
    public static function generateRandomToken()
    {
        $token = openssl_random_pseudo_bytes(26);
        $token = bin2hex($token);
        return $token;
    }

    /**
     * Kalyani : Add/substract the time into given time
     * @$time : current time
     * @identifier : should be '+' or '-'
     * @numberOfHour : how much hour we want to add/substract
     */
    public static function addTimeIntoCurrentTime($time, $identifier, $numberOfHour)
    {
        if ($identifier == '+') {
            $timestamp = strtotime($time) + 60 * 60 * $numberOfHour;
            $time = date('h:i:s', $timestamp);
            return $time;
        } else if ($identifier == '-') {
            $timestamp = strtotime($time) - 60 * 60 * $numberOfHour;
            $time = date('h:i:s', $timestamp);
            return $time;
        }

        return null;
    }

    /**
     * Sonal : Convert Seconds into H:I:S time format
     */
    public static function convertSectoTimeformat($seconds)
    {
        $H = floor($seconds / 3600);
        $i = ($seconds / 60) % 60;
        $s = $seconds % 60;
        return sprintf("%02d:%02d:%02d", $H, $i, $s);
    }

    /**
     * Sonal : calculate time difference between two times H:I:S
     */
    public static function getTimeDifference($time1, $time2)
    {
        $time1 = strtotime("1980-01-01 $time1");
        $time2 = strtotime("1980-01-01 $time2");
        // if ($time2 < $time1) {
        //     $time2 += 86400;
        // }
        if ($time2 != '' && $time2 > $time1) {
            return date("H:i:s", strtotime("1980-01-01 00:00:00") + ($time2 - $time1));
        } else {
            return '00:00:00';
        }
    }

}
