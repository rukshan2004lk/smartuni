<?php
session_start();
require_once "../includes/connection.php";

$course_code   = trim($_POST["course_code"] ?? "");
$course_name   = trim($_POST["course_name"] ?? "");
$lecturer_name = trim($_POST["lecturer_name"] ?? "");
$day_of_week   = trim($_POST["day_of_week"] ?? "");
$start_time    = trim($_POST["start_time"] ?? "");
$end_time      = trim($_POST["end_time"] ?? "");

function parseTimeToMinutes($timeStr) {
    $timeStr = trim($timeStr);
    if (empty($timeStr)) return 0;
    $parts = explode(':', $timeStr);
    $h = intval($parts[0] ?? 0);
    $m = intval($parts[1] ?? 0);
    return ($h * 60) + $m;
}

$start_min = parseTimeToMinutes($start_time);
$end_min   = parseTimeToMinutes($end_time);

if (empty($course_code)) {
    echo "Please enter the Course Code.";
} else if (empty($course_name)) {
    echo "Please enter the Course Title.";
} else if (empty($day_of_week)) {
    echo "Please select the Day of Week.";
} else if (empty($start_time)) {
    echo "Please enter the Start Time.";
} else if (empty($end_time)) {
    echo "Please enter the End Time.";
} else if ($start_min >= $end_min) {
    echo "End Time must be after Start Time.";
} else if ($start_min < 510) { // 08:30 AM
    echo "Error: Lectures cannot start before 08:30 AM.";
} else if ($end_min > 1110) { // 06:30 PM (18:30)
    echo "Error: Lectures cannot end after 06:30 PM.";
} else if ($start_min < 810 && $end_min > 750) { // Lunch interval 12:30 - 13:30 (750 to 810 mins)
    echo "Error: Cannot schedule lectures during the Lunch Interval (12:30 PM - 01:30 PM).";
} else {
    Database::iud("INSERT INTO `timetable` 
        (`course_name`, `course_code`, `lecturer_name`, `day_of_week`, `start_time`, `end_time`, `status_id`) 
        VALUES 
        ('" . addslashes($course_name) . "', '" . addslashes($course_code) . "', '" . addslashes($lecturer_name) . "', '" . addslashes($day_of_week) . "', '" . addslashes($start_time) . "', '" . addslashes($end_time) . "', '1')");

    echo "success";
}
?>
