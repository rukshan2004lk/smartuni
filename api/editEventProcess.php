<?php
session_start();
header("Content-Type: application/json");
require_once __DIR__ . "/../includes/connection.php";

$res = ["status" => "error", "message" => "An unexpected error occurred."];

$userRole = intval($_SESSION["user"]["role_id"] ?? 0);
if ($userRole !== 3) {
    $res["message"] = "Unauthorized: Only administrators can edit events.";
    echo json_encode($res);
    exit;
}

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $id          = intval($_POST["id"] ?? 0);
    $title       = trim($_POST["title"] ?? "");
    $description = trim($_POST["description"] ?? "");
    $date        = trim($_POST["date"] ?? "");
    $start_time  = trim($_POST["start_time"] ?? "");
    $end_time    = trim($_POST["end_time"] ?? "");
    $location    = trim($_POST["location"] ?? "");
    $category    = trim($_POST["category"] ?? "Other");

    if ($id <= 0) {
        $res["message"] = "Invalid event ID.";
        echo json_encode($res);
        exit;
    }

    if (empty($title)) {
        $res["message"] = "Event title is required.";
        echo json_encode($res);
        exit;
    }

    if (empty($date)) {
        $res["message"] = "Event date is required.";
        echo json_encode($res);
        exit;
    }

    $titleEsc       = Database::escape($title);
    $descriptionEsc = Database::escape($description);
    $dateEsc        = Database::escape($date);
    $startTimeEsc   = !empty($start_time) ? "'" . Database::escape($start_time) . "'" : "NULL";
    $endTimeEsc     = !empty($end_time) ? "'" . Database::escape($end_time) . "'" : "NULL";
    $locationEsc    = Database::escape($location);
    $categoryEsc    = Database::escape($category);

    $sql = "UPDATE `events` SET 
            `title` = '$titleEsc', 
            `description` = '$descriptionEsc', 
            `date` = '$dateEsc', 
            `start_time` = $startTimeEsc, 
            `end_time` = $endTimeEsc, 
            `location` = '$locationEsc', 
            `category` = '$categoryEsc' 
            WHERE `id` = '$id'";

    try {
        Database::iud($sql);
        $res["status"]  = "success";
        $res["message"] = "Event updated successfully!";
    } catch (Exception $e) {
        $res["message"] = "Failed to update event: " . $e->getMessage();
    }
}

echo json_encode($res);
