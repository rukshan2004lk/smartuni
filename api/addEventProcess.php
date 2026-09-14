<?php
session_start();
header("Content-Type: application/json");
require_once __DIR__ . "/../includes/connection.php";

$res = ["status" => "error", "message" => "An unexpected error occurred."];

$userRole = intval($_SESSION["user"]["role_id"] ?? 0);
if ($userRole !== 3) {
    $res["message"] = "Unauthorized: Only administrators can add events.";
    echo json_encode($res);
    exit;
}

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $title       = trim($_POST["title"] ?? "");
    $description = trim($_POST["description"] ?? "");
    $date        = trim($_POST["date"] ?? "");
    $start_time  = trim($_POST["start_time"] ?? "");
    $end_time    = trim($_POST["end_time"] ?? "");
    $location    = trim($_POST["location"] ?? "");
    $category    = trim($_POST["category"] ?? "Other");

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

    $sql = "INSERT INTO `events` (`title`, `description`, `date`, `start_time`, `end_time`, `location`, `category`) 
            VALUES ('$titleEsc', '$descriptionEsc', '$dateEsc', $startTimeEsc, $endTimeEsc, '$locationEsc', '$categoryEsc')";

    try {
        Database::iud($sql);
        $res["status"]  = "success";
        $res["message"] = "Event created successfully!";
    } catch (Exception $e) {
        $res["message"] = "Failed to create event: " . $e->getMessage();
    }
}

echo json_encode($res);
