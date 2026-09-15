<?php
session_start();
require_once "../includes/connection.php";

if (!isset($_SESSION["user"]) || empty($_SESSION["user"])) {
    echo "Unauthorized: Please sign in to delete timetable entries.";
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
