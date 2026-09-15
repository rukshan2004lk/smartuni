<?php
session_start();
header("Content-Type: application/json");
require_once __DIR__ . "/../includes/connection.php";

$res = ["status" => "error", "message" => "An unexpected error occurred."];

$userRole = intval($_SESSION["user"]["role_id"] ?? 0);
if ($userRole !== 3) {
    $res["message"] = "Unauthorized: Only administrators can update booking statuses.";
    echo json_encode($res);
    exit;
}

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $bookingId = intval($_POST["booking_id"] ?? 0);
    $action    = trim($_POST["action"] ?? "");

    if ($bookingId <= 0) {
        $res["message"] = "Invalid booking ID.";
        echo json_encode($res);
        exit;
    }

    $newStatusId = 7; // default Pending
    if ($action === "approve") {
        $newStatusId = 8; // Confirmed / Approved

        // Check if another booking for the same facility, date, and overlapping time is already approved
        $target_rs = Database::search("SELECT * FROM `bookings` WHERE `id` = '$bookingId'");
        if ($target_rs && $target_rs->num_rows > 0) {
            $targetBk = $target_rs->fetch_assoc();
            $facId = intval($targetBk['facility_id']);
            $bDate = $targetBk['booking_date'];
            $sTime = $targetBk['start_time'];
            $eTime = $targetBk['end_time'];

            $approved_rs = Database::search("SELECT * FROM `bookings` 
                WHERE `facility_id` = '$facId' 
                AND `booking_date` = '$bDate' 
                AND `status_id` IN ('8', '2', '13') 
                AND `id` != '$bookingId' 
                AND (`start_time` < '$eTime' AND `end_time` > '$sTime')");

            if ($approved_rs && $approved_rs->num_rows > 0) {
                $alreadyApproved = $approved_rs->fetch_assoc();
                $res["message"] = "Cannot approve: Another booking (#BK-" . str_pad($alreadyApproved['id'], 4, "0", STR_PAD_LEFT) . ") has already been approved for this facility and time slot.";
                echo json_encode($res);
                exit;
            }
        }
    } else if ($action === "reject") {
        $newStatusId = 9; // Rejected
    } else {
        $res["message"] = "Invalid action specified.";
        echo json_encode($res);
        exit;
    }

    try {
        Database::iud("UPDATE `bookings` SET `status_id` = '$newStatusId' WHERE `id` = '$bookingId'");
        $actionName = ($action === "approve") ? "approved" : "rejected";
        $res["status"]  = "success";
        $res["message"] = "Booking #BK-" . str_pad($bookingId, 4, "0", STR_PAD_LEFT) . " has been {$actionName}.";
    } catch (Exception $e) {
        $res["message"] = "Database update failed: " . $e->getMessage();
    }
}

echo json_encode($res);
