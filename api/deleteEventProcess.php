<?php
session_start();
header("Content-Type: application/json");
require_once __DIR__ . "/../includes/connection.php";

$res = ["status" => "error", "message" => "An unexpected error occurred."];

$userRole = intval($_SESSION["user"]["role_id"] ?? 0);
if ($userRole !== 3) {
    $res["message"] = "Unauthorized: Only administrators can delete events.";
    echo json_encode($res);
    exit;
}

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $id = intval($_POST["id"] ?? 0);

    if ($id <= 0) {
        $res["message"] = "Invalid event ID.";
        echo json_encode($res);
        exit;
    }

    try {
        Database::iud("DELETE FROM `events` WHERE `id` = '$id'");
        $res["status"]  = "success";
        $res["message"] = "Event deleted successfully!";
    } catch (Exception $e) {
        $res["message"] = "Failed to delete event: " . $e->getMessage();
    }
}

echo json_encode($res);
