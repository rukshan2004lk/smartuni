<?php
session_start();
require_once "../includes/connection.php";

if (!isset($_SESSION["user"]) || empty($_SESSION["user"])) {
    echo "Unauthorized: Please sign in to delete timetable entries.";
    exit();
}

$id       = trim($_POST["id"] ?? "");
$userId   = intval($_SESSION["user"]["id"]);
$userRole = intval($_SESSION["user"]["role_id"] ?? 1);

if (empty($id)) {
    echo "Invalid Timetable Entry ID.";
} else {
    // Check if entry exists and user is owner or admin
    $check_rs = Database::search("SELECT `user_id` FROM `timetable` WHERE `id` = '" . addslashes($id) . "'");
    if (!$check_rs || $check_rs->num_rows === 0) {
        echo "Error: Timetable entry not found.";
        exit();
    }
    $entry = $check_rs->fetch_assoc();
    if ($userRole !== 3 && intval($entry["user_id"]) !== $userId) {
        echo "Unauthorized: You can only delete your own timetable entries.";
        exit();
    }

    // Delete entry from timetable database table
    Database::iud("DELETE FROM `timetable` WHERE `id` = '" . addslashes($id) . "'");
    echo "success";
}
?>
