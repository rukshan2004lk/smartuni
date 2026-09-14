<?php
session_start();
require_once __DIR__ . "/../includes/connection.php";

$userId      = $_SESSION["user"]["id"] ?? 1;
$userRole    = intval($_SESSION["user"]["role_id"] ?? 1);

if ($userRole !== 2) {
    echo "Only lecturers can request facility bookings.";
    exit();
}

$facilityId  = intval($_POST["facility_id"] ?? 0);
$bookingDate = trim($_POST["booking_date"] ?? "");
$startTime   = trim($_POST["start_time"] ?? "");
$endTime     = trim($_POST["end_time"] ?? "");
$purpose     = trim($_POST["purpose"] ?? "");
$attendees   = intval($_POST["attendees"] ?? 0);
$notes       = trim($_POST["notes"] ?? "");

if (!function_exists('parseTimeToMinutes')) {
    function parseTimeToMinutes($timeStr) {
        $timeStr = trim($timeStr);
        if (empty($timeStr)) return 0;
        $parts = explode(':', $timeStr);
        $h = intval($parts[0] ?? 0);
        $m = intval($parts[1] ?? 0);
        return ($h * 60) + $m;
    }
}

if ($facilityId <= 0) {
    echo "Invalid Facility selection.";
    exit();
}

// Fetch facility details for capacity and operating hours validation
$fac_rs = Database::search("SELECT * FROM `facilities` WHERE `id` = '" . intval($facilityId) . "'");
if (!$fac_rs || $fac_rs->num_rows === 0) {
    echo "Selected facility not found.";
    exit();
}

$facility = $fac_rs->fetch_assoc();
$maxCapacity = intval($facility['capacity']);

$facStartTime = !empty($facility['start_time']) ? trim($facility['start_time']) : '08:00';
$facEndTime   = !empty($facility['end_time']) ? trim($facility['end_time']) : '22:00';

$opStartMin = parseTimeToMinutes($facStartTime);
$opEndMin   = parseTimeToMinutes($facEndTime);

$startMin = parseTimeToMinutes($startTime);
$endMin   = parseTimeToMinutes($endTime);

$displayStart = date("g:i A", strtotime($facStartTime));
$displayEnd   = date("g:i A", strtotime($facEndTime));

$userDisplayStart = date("g:i A", strtotime($startTime));
$userDisplayEnd   = date("g:i A", strtotime($endTime));

if (empty($bookingDate)) {
    echo "Please select a Booking Date.";
} else if ($bookingDate < date("Y-m-d")) {
    echo "Booking Date cannot be in the past.";
} else if (empty($startTime)) {
    echo "Please select a Start Time.";
} else if (empty($endTime)) {
    echo "Please select an End Time.";
} else if ($startMin >= $endMin) {
    echo "End Time must be after Start Time.";
} else if ($startMin < $opStartMin) {
    echo "Selected Start Time (" . $userDisplayStart . ") is earlier than facility opening time (" . $displayStart . "). Operating hours: " . $displayStart . " – " . $displayEnd . " (" . $facStartTime . " – " . $facEndTime . ").";
} else if ($endMin > $opEndMin) {
    echo "Selected End Time (" . $userDisplayEnd . ") is later than facility closing time (" . $displayEnd . "). Operating hours: " . $displayStart . " – " . $displayEnd . " (" . $facStartTime . " – " . $facEndTime . ").";
} else if (empty($purpose)) {
    echo "Please enter or select the Purpose of Booking.";
} else if ($attendees <= 0) {
    echo "Please enter a valid number of attendees.";
} else if ($attendees > $maxCapacity) {
    echo "Number of attendees (" . $attendees . ") exceeds facility maximum capacity (" . $maxCapacity . " persons).";
} else {
    // Check for existing overlapping bookings for the same facility on the same date
    $conflict_rs = Database::search("SELECT * FROM `bookings` 
        WHERE `facility_id` = '" . intval($facilityId) . "' 
        AND `booking_date` = '" . addslashes($bookingDate) . "' 
        AND `status_id` NOT IN ('9', '10') 
        AND (`start_time` < '" . addslashes($endTime) . "' AND `end_time` > '" . addslashes($startTime) . "')");

    if ($conflict_rs && $conflict_rs->num_rows > 0) {
        $existing = $conflict_rs->fetch_assoc();
        $exStart = date("g:i A", strtotime($existing['start_time']));
        $exEnd   = date("g:i A", strtotime($existing['end_time']));
        echo "Booking Conflict: This facility is already booked on " . $bookingDate . " from " . $exStart . " to " . $exEnd . ". Please select a different time slot.";
        exit();
    }

    // Status ID 7 = Pending (or 8 = Confirmed)
    Database::iud("INSERT INTO `bookings` 
        (`user_id`, `facility_id`, `booking_date`, `start_time`, `end_time`, `purpose`, `capacity`, `status_id`, `created_at`, `notes`) 
        VALUES 
        ('" . intval($userId) . "', '" . intval($facilityId) . "', '" . addslashes($bookingDate) . "', '" . addslashes($startTime) . "', '" . addslashes($endTime) . "', '" . addslashes($purpose) . "', '" . intval($attendees) . "', '7', NOW(), '" . addslashes($notes) . "')");

    $insertedId = Database::$connection->insert_id;
    $_SESSION['last_booking_id'] = $insertedId;

    echo "success:" . $insertedId;
}
?>
