<?php 
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
require_once __DIR__ . "/../includes/connection.php";

$email      = trim($_POST["e"] ?? $_POST["email"] ?? "");
$password   = trim($_POST["p"] ?? $_POST["password"] ?? "");
$rememberme = $_POST["r"] ?? $_POST["rememberme"] ?? "false";

if (empty($email) || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
    echo "Please enter a valid email address.";
} else if (!str_ends_with($email, "@tec.rjt.ac.lk")) {
    echo "Please use your official university email (@tec.rjt.ac.lk).";
} else if (strlen($password) < 8) {
    echo "Password must be at least 8 characters long.";
} else {
    $rs = Database::search("SELECT * FROM `users` WHERE `email`='" . Database::escape($email) . "'");
    
    if ($rs && $rs->num_rows > 0) {
        $user = $rs->fetch_assoc();

        $hash = $user["password_hash"] ?? $user["password"] ?? "";

        if (password_verify($password, $hash) || $password === $hash) {
            $_SESSION["user"] = $user;

            $statusId = intval($user["status_id"] ?? 1);

            if ($statusId === 3) {
                echo "Your account has been suspended. Please contact campus support.";
                exit();
            }

            if ($statusId === 1) {
                // Account is pending verification by admin/staff
                echo "pending_verification";
                exit();
            }

            if ($rememberme === "true" || $rememberme === true || $rememberme === "1" || $rememberme === "on") {
                setcookie("email", $email, time() + (86400 * 30), "/");
                setcookie("password", $password, time() + (86400 * 30), "/");
            } else {
                setcookie("email", "", time() - 3600, "/");
                setcookie("password", "", time() - 3600, "/");
            }

            echo "success";
        } else {
            echo "Invalid Password.";
        }
    } else {
        echo "User with this Email does not exist.";
    }
}
?>