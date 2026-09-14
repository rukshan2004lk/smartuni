<?php
session_start();
require_once __DIR__ . "/../includes/connection.php";

$userId      = $_SESSION["user"]["id"] ?? 1;
$location    = trim($_POST["location"] ?? "");
$title       = trim($_POST["title"] ?? "");
$description = trim($_POST["description"] ?? "");
$priority    = trim($_POST["priority"] ?? "Medium");

if (empty($location)) {
    echo "Please enter the Location on campus.";
} else if (empty($title)) {
    echo "Please enter an Issue Title / Summary.";
} else if (empty($description)) {
    echo "Please enter a detailed Description of the issue.";
} else {
    // Status ID 11 = Submitted
    Database::iud("INSERT INTO `service_requests` 
        (`user_id`, `title`, `location`, `description`, `priority`, `status_id`, `created_at`) 
        VALUES 
        ('" . intval($userId) . "', '" . addslashes($title) . "', '" . addslashes($location) . "', '" . addslashes($description) . "', '" . addslashes($priority) . "', '11', NOW())");

    $insertedId = Database::$connection->insert_id;
    $_SESSION['last_sr_id'] = $insertedId;

    echo "success:" . $insertedId;
}
?>
