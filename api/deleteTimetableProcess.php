<?php
session_start();
require_once "../includes/connection.php";

$userRole = intval($_SESSION["user"]["role_id"] ?? 0);
if ($userRole !== 3) {
    echo "Unauthorized: Only administrators can delete timetable entries.";
    exit();
}

$id = trim($_POST["id"] ?? "");

if (empty($id)) {
    echo "Invalid Timetable Entry ID.";
} else {
    // Delete entry from timetable database table
    Database::iud("DELETE FROM `timetable` WHERE `id` = '" . addslashes($id) . "'");
    echo "success";
}
?>
