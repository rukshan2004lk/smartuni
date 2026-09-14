<?php
session_start();
header("Content-Type: application/json");
require_once "../includes/connection.php";

$res = ["status" => "error", "message" => "An unexpected error occurred."];

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $userId = $_SESSION['user']['id'] ?? 1;

    $fname  = trim($_POST["fname"] ?? "");
    $lname  = trim($_POST["lname"] ?? "");
    $email  = trim($_POST["email"] ?? "");
    $mobile = trim($_POST["mobile"] ?? "");

    if (empty($fname) || empty($lname)) {
        $res["message"] = "First name and Last name are required.";
        echo json_encode($res);
        exit;
    }

    if (empty($email)) {
        $res["message"] = "Email address is required.";
        echo json_encode($res);
        exit;
    }

    $fnameEsc  = Database::escape($fname);
    $lnameEsc  = Database::escape($lname);
    $mobileEsc = Database::escape($mobile);

    $profilePicPath = null;

    // Handle Profile Picture File Upload
    if (isset($_FILES['profile_pic']) && $_FILES['profile_pic']['error'] === UPLOAD_ERR_OK) {
        $fileTmpPath = $_FILES['profile_pic']['tmp_name'];
        $fileName    = $_FILES['profile_pic']['name'];
        $fileExtension = strtolower(pathinfo($fileName, PATHINFO_EXTENSION));

        $allowedExtensions = ['jpg', 'jpeg', 'png', 'webp'];
        if (in_array($fileExtension, $allowedExtensions)) {
            $uploadDir = "../images/profiles/";
            if (!is_dir($uploadDir)) {
                mkdir($uploadDir, 0777, true);
            }

            $newFileName = "user_" . $userId . "_" . time() . "." . $fileExtension;
            $destPath = $uploadDir . $newFileName;

            if (move_uploaded_file($fileTmpPath, $destPath)) {
                $profilePicPath = "images/profiles/" . $newFileName;
            }
        } else {
            $res["message"] = "Invalid image format. Allowed formats: JPG, JPEG, PNG, WEBP.";
            echo json_encode($res);
            exit;
        }
    }

    $updateSql = "UPDATE `users` SET 
        `fname` = '$fnameEsc', 
        `lname` = '$lnameEsc', 
        `mobile` = '$mobileEsc'";

    if ($profilePicPath !== null) {
        $picEsc = Database::escape($profilePicPath);
        $updateSql .= ", `profile_pic` = '$picEsc'";
    }

    $updateSql .= " WHERE `id` = '$userId'";

    try {
        Database::iud($updateSql);

        // Refresh Session Data
        $user_rs = Database::search("SELECT * FROM `users` WHERE `id` = '$userId'");
        if ($user_rs && $user_rs->num_rows > 0) {
            $_SESSION['user'] = $user_rs->fetch_assoc();
        }

        $res["status"]  = "success";
        $res["message"] = "Profile and settings updated successfully!";
        if ($profilePicPath !== null) {
            $res["profile_pic"] = $profilePicPath;
        }
    } catch (Exception $e) {
        $res["message"] = "Failed to update profile: " . $e->getMessage();
    }
}

echo json_encode($res);
