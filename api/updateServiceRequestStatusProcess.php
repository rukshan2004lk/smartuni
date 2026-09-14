<?php
session_start();
require_once __DIR__ . "/../includes/connection.php";

$userRole = intval($_SESSION["user"]["role_id"] ?? 0);
if ($userRole !== 3) {
    echo "Unauthorized: Only administrators can update service request statuses.";
    exit();
}

$srId     = intval($_POST["request_id"] ?? $_POST["sr_id"] ?? 0);
$statusId = intval($_POST["status_id"] ?? 0);

if ($srId <= 0) {
    echo "Invalid Service Request ID.";
    exit();
}

if ($statusId <= 0) {
    echo "Invalid Status selected.";
    exit();
}

// Verify request exists
$check_rs = Database::search("SELECT * FROM `service_requests` WHERE `id` = '$srId'");
if (!$check_rs || $check_rs->num_rows === 0) {
    echo "Service Request not found.";
    exit();
}

Database::iud("UPDATE `service_requests` SET `status_id` = '$statusId' WHERE `id` = '$srId'");

echo "success";
?>
