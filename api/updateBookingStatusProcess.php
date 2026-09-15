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
