<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
require_once "includes/connection.php";

$user = $_SESSION['user'] ?? null;

// Re-query database to check for real-time status updates by Admin/Staff
if ($user && isset($user['id'])) {
    $userId = intval($user['id']);
    $user_rs = Database::search("SELECT * FROM `users` WHERE `id` = '$userId'");
    if ($user_rs && $user_rs->num_rows > 0) {
        $freshUser = $user_rs->fetch_assoc();
        $_SESSION['user'] = $freshUser;
        $user = $freshUser;
    }
}

// If user is verified / active (status_id == 2), redirect to dashboard
if ($user && intval($user['status_id'] ?? 1) === 2) {
    header("Location: dashboard.php");
    exit();
}

$userName  = $user ? trim(($user['fname'] ?? '') . ' ' . ($user['lname'] ?? '')) : 'Student User';
$userEmail = $user['email'] ?? 'student@tec.rjt.ac.lk';
$regNumber = $user['reg_number'] ?? 'N/A';
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>SmartUni Campus OS - Account Verification Pending</title>
  <link rel="preconnect" href="https://fonts.googleapis.com" />
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin />
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet" />
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" />
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" />
  <link rel="stylesheet" href="assets/css/style.css" />
</head>
<body class="bg-light d-flex flex-column min-vh-100">

  <header class="su-header px-4 px-md-5 d-flex align-items-center justify-content-between bg-white border-bottom">
    <div class="d-flex align-items-center gap-3">
      <a href="index.php" class="su-brand-mark">
        <span class="fw-bold fs-5 tracking-tight text-dark"><i class="bi bi-mortarboard-fill me-2 text-primary"></i>SmartUni</span>
  
      </a>
    </div>

  </header>

  <main class="flex-fill d-flex align-items-center justify-content-center p-4">
    <div class="bg-white border rounded-4 p-5 text-center shadow-sm" style="max-width: 580px; width: 100%;">
      
      <div class="mx-auto mb-4 bg-warning-subtle text-warning-emphasis rounded-circle d-flex align-items-center justify-content-center" style="width: 72px; height: 72px;">
        <i class="bi bi-clock-history fs-1"></i>
      </div>

      <span class="su-badge su-badge-amber mb-3">● Account Pending Verification</span>
      
      <h3 class="fw-bold text-dark mb-2">Welcome, <?= htmlspecialchars($userName) ?></h3>
      <p class="text-secondary small mb-4">
        Your registration request has been submitted to the academic registrar. Once an administrator verifies your account, you will receive full access to campus facilities and booking services.
      </p>

      <div class="p-3 bg-light border rounded-3 text-start small mb-4">
        <div class="d-flex justify-content-between mb-2">
          <span class="text-muted">Account Email:</span>
          <span class="fw-bold text-dark"><?= htmlspecialchars($userEmail) ?></span>
        </div>
        <div class="d-flex justify-content-between mb-2">
          <span class="text-muted">Registration / ID:</span>
          <span class="fw-bold text-dark"><?= htmlspecialchars($regNumber) ?></span>
        </div>
        <div class="d-flex justify-content-between">
          <span class="text-muted">Current Status:</span>
          <span class="text-warning-emphasis fw-bold"><i class="bi bi-hourglass-split me-1"></i>Awaiting Staff Verification</span>
        </div>
      </div>

      <div class="d-flex flex-column flex-sm-row gap-2 justify-content-center">
        <button class="btn btn-su-indigo px-4 py-2" onclick="window.location.reload();">
          <i class="bi bi-arrow-clockwise me-1"></i> Check Status / Refresh
        </button>
        <a href="api/logoutProcess.php" class="btn btn-su-outline px-4 py-2">Sign Out</a>
        <a class="btn btn-su-outline px-3 py-2"href="javascript:void(0)" 
     class="text-secondary text-decoration-none" 
     style="cursor: pointer;"
     onclick="Swal.fire({
       title: 'Campus IT Helpdesk',
       html: 'Contact Number:<br><strong style=\'font-size: 1.25rem;\'><a href=\'tel:0713218157\' class=\'text-primary text-decoration-none\'>071 32 18 157</a></strong>',
       icon: 'info',
       confirmButtonText: 'Close',
       confirmButtonColor: '#0d6efd'
     });">
    Campus IT Helpdesk
  </a>
      </div>

    </div>
  </main>

  <footer class="su-footer bg-white border-top">
    <div class="container-fluid px-4 px-md-5">
      <div class="d-flex flex-column flex-md-row justify-content-between align-items-center gap-3">
        <div class="d-flex gap-3 text-muted small">
          <span>Official University Portal</span>
          <span>·</span>
          <span>Faculty of Technology, RUSL</span>
        </div>
        <div class="d-flex gap-3 text-muted small">
          <span>© 2025 SmartUni Inc.</span>
        </div>
      </div>
    </div>
  </footer>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
  <script src="assets/js/script.js"></script>
</body>
</html>
