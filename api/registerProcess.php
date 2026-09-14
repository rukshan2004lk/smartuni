<?php
session_start();
require_once "../includes/connection.php";


$role_id     = trim($_POST["role_id"] ?? "");
$fname       = trim($_POST["fname"] ?? "");
$lname       = trim($_POST["lname"] ?? "");
$mobile      = trim($_POST["mobile"] ?? "");
$reg_number  = trim($_POST["reg_number"] ?? "");
$email       = trim($_POST["email"] ?? "");
$password    = $_POST["password"] ?? "";


if (empty($role_id)) {
    echo "Please select a role.";
} else if (empty($fname)) {
    echo "Please enter your First Name.";
} else if (empty($lname)) {
    echo "Please enter your Last Name.";
} else if (empty($mobile) || !preg_match("/^07[0-9]{8}$/", $mobile)) {
    echo "Please enter a valid mobile number.";
} else if (empty($reg_number)) {
    echo "Please enter your Registration Number.";
} else if (empty($email) || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
    echo "Please enter a valid email address.";
} else if (!str_ends_with($email, "@tec.rjt.ac.lk")) {
    echo "Please use your official university email (@tec.rjt.ac.lk).";
} else if (strlen($password) < 8) {
    echo "Password must be at least 8 characters long.";
} else {

    
    $rs = Database::search("SELECT `id` FROM `users` WHERE `email`='" . $email . "' OR `reg_number`='" . $reg_number . "'");

    if ($rs->num_rows > 0) {
        echo "A user with this Email or Registration Number already exists.";
    } else {
 
        $d = new DateTime();
        $tz = new DateTimeZone("Asia/Colombo");
        $d->setTimezone($tz);
        $date = $d->format("Y-m-d H:i:s");

        $hashed_password = password_hash($password, PASSWORD_DEFAULT);

        // 5. Insert Record (include reg_number)
        Database::iud("INSERT INTO `users` 
            (`fname`, `lname`, `email`, `mobile`, `reg_number`, `password_hash`, `created_at`, `status_id`, `role_id`) 
            VALUES 
            ('" . $fname . "', '" . $lname . "', '" . $email . "', '" . $mobile . "', '" . $reg_number . "', '" . $hashed_password . "', '" . $date . "', '1', '" . $role_id . "')");

        echo "success";
    }
}