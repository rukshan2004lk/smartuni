<?php
session_start();
require_once "../includes/connection.php";

$name        = trim($_POST["name"] ?? "");
$category    = trim($_POST["category"] ?? "");
$capacity    = intval($_POST["capacity"] ?? 0);
$location    = trim($_POST["location"] ?? "");
$description = trim($_POST["description"] ?? "");
$startTime   = trim($_POST["start_time"] ?? "08:00");
$endTime     = trim($_POST["end_time"] ?? "22:00");
$equipment   = trim($_POST["equipment"] ?? "");

if (empty($name)) {
    echo "Please enter the Facility Name.";
} else if (empty($category)) {
    echo "Please select the Category.";
} else if ($capacity <= 0) {
    echo "Please enter a valid Capacity.";
} else if (empty($location)) {
    echo "Please enter the Location.";
} else {
    $imagePath = "https://images.unsplash.com/photo-1541829070764-84a7d30dd3f3?q=80&w=600&auto=format&fit=crop";

    // Handle file upload if provided
    if (isset($_FILES["image"]) && $_FILES["image"]["error"] === 0) {
        $targetDir = "../assets/img/facilities/";
        if (!file_exists($targetDir)) {
            mkdir($targetDir, 0777, true);
        }

        $fileName = $_FILES["image"]["name"];
        $ext = strtolower(pathinfo($fileName, PATHINFO_EXTENSION));
        $allowedExts = ["jpg", "jpeg", "png", "webp", "gif", "svg"];

        if (!in_array($ext, $allowedExts)) {
            echo "Invalid image file format. Please upload JPG, PNG, WEBP, or GIF.";
            exit();
        }

        $newFileName = "facility_" . time() . "_" . uniqid() . "." . $ext;
        $targetFilePath = $targetDir . $newFileName;

        if (move_uploaded_file($_FILES["image"]["tmp_name"], $targetFilePath)) {
            $imagePath = "assets/img/facilities/" . $newFileName;
        }
    }

    Database::iud("INSERT INTO `facilities` 
        (`name`, `category`, `capacity`, `location`, `status_id`, `image`, `description`, `start_time`, `end_time`, `equipment`) 
        VALUES 
        ('" . addslashes($name) . "', '" . addslashes($category) . "', '" . intval($capacity) . "', '" . addslashes($location) . "', '4', '" . addslashes($imagePath) . "', '" . addslashes($description) . "', '" . addslashes($startTime) . "', '" . addslashes($endTime) . "', '" . addslashes($equipment) . "')");

    echo "success";
}
?>
