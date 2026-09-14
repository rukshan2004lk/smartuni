<?php
session_start();
require_once "../includes/connection.php";

$id = trim($_POST["id"] ?? "");

if (empty($id)) {
    echo "Invalid Timetable Entry ID.";
} else {
    // Delete entry from timetable database table
    Database::iud("DELETE FROM `timetable` WHERE `id` = '" . addslashes($id) . "'");
    echo "success";
}
?>
