<?php
session_start();
header("Content-Type: application/json");
require_once __DIR__ . "/../includes/connection.php";

$res = ["status" => "error", "message" => "An unexpected error occurred."];

$userRole = intval($_SESSION["user"]["role_id"] ?? 0);
if ($userRole !== 3) {
    $res["message"] = "Unauthorized: Only administrators can modify user profiles and roles.";
    echo json_encode($res);
    exit;
}

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $userId    = intval($_POST["user_id"] ?? 0);
    $fname     = trim($_POST["fname"] ?? "");
    $lname     = trim($_POST["lname"] ?? "");
    $mobile    = trim($_POST["mobile"] ?? "");
    $regNumber = trim($_POST["reg_number"] ?? "");
    $roleId    = intval($_POST["role_id"] ?? 1);
    $statusId  = intval($_POST["status_id"] ?? 1);

    if ($userId <= 0) {
        $res["message"] = "Invalid user ID.";
        echo json_encode($res);
        exit;
    }

    if (empty($fname) || empty($lname)) {
        $res["message"] = "First name and Last name are required.";
        echo json_encode($res);
        exit;
    }

    $fnameEsc  = Database::escape($fname);
    $lnameEsc  = Database::escape($lname);
    $mobileEsc = Database::escape($mobile);
    $regEsc    = Database::escape($regNumber);

    $sql = "UPDATE `users` SET 
            `fname` = '$fnameEsc', 
            `lname` = '$lnameEsc', 
            `mobile` = '$mobileEsc', 
            `reg_number` = '$regEsc', 
            `role_id` = '$roleId', 
            `status_id` = '$statusId' 
            WHERE `id` = '$userId'";

    try {
        Database::iud($sql);
        $res["status"]  = "success";
        $res["message"] = "User profile, role, and status updated successfully!";
    } catch (Exception $e) {
        $res["message"] = "Failed to update user: " . $e->getMessage();
    }
}

echo json_encode($res);
