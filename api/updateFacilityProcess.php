<?php
session_start();
require_once "../includes/connection.php";

$userRole = intval($_SESSION["user"]["role_id"] ?? 0);
if ($userRole !== 3) {
    echo "Unauthorized: Only administrators can add/edit facilities.";
    exit();
}

// 1. Fetch the ID of the facility you are updating
$id          = intval($_POST["id"] ?? 0); 

$name        = trim($_POST["name"] ?? "");
$category    = trim($_POST["category"] ?? "");
$capacity    = intval($_POST["capacity"] ?? 0);
$location    = trim($_POST["location"] ?? "");
$description = trim($_POST["description"] ?? "");
$startTime   = trim($_POST["start_time"] ?? "08:00");
$endTime     = trim($_POST["end_time"] ?? "22:00");
$equipment   = trim($_POST["equipment"] ?? "");

if ($id <= 0) {
    echo "Invalid facility ID.";
} else if (empty($name)) {
    echo "Please enter the Facility Name.";
} else if (empty($category)) {
    echo "Please select the Category.";
} else if ($capacity <= 0) {
    echo "Please enter a valid Capacity.";
} else if (empty($location)) {
    echo "Please enter the Location.";
} else {
    
    // Base query setup (excluding image)
    $updateQuery = "UPDATE `facilities` SET 
        `name` = '" . addslashes($name) . "', 
        `category` = '" . addslashes($category) . "', 
        `capacity` = '" . $capacity . "', 
        `location` = '" . addslashes($location) . "', 
        `description` = '" . addslashes($description) . "', 
        `start_time` = '" . addslashes($startTime) . "', 
        `end_time` = '" . addslashes($endTime) . "', 
        `equipment` = '" . addslashes($equipment) . "'";

    // 2. Only handle and update the image if a NEW file was actually uploaded
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
            // Append the image update to the query ONLY if upload was successful
            $updateQuery .= ", `image` = '" . addslashes($imagePath) . "'";
        }
    }

    // Add the WHERE clause at the very end
    $updateQuery .= " WHERE `id` = " . $id;

    Database::iud($updateQuery);

    echo "success";
}
?>