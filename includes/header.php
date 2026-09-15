<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Enforce authentication check for portal pages
if (!isset($_SESSION['user']) || empty($_SESSION['user'])) {
    header("Location: index.php");
    exit();
}

$currentUser = $_SESSION['user'];
$currentUserRole = intval($currentUser['role_id'] ?? 1);
if ($currentUser && intval($currentUser['status_id'] ?? 1) === 1) {
    $activePageFile = basename($_SERVER['PHP_SELF']);
    if ($activePageFile !== 'verification-pending.php' && $activePageFile !== 'contacts.php') {
        header("Location: verification-pending.php");
        exit();
    }
}

if (!isset($pageTitle)) {
  $pageTitle = 'SmartUni Portal';
}
if (!isset($currentPage)) {
  $currentPage = '';
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title><?php echo htmlspecialchars($pageTitle); ?></title>
  <link rel="preconnect" href="https://fonts.googleapis.com" />
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin />
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet" />
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" />
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" />
  <link rel="stylesheet" href="assets/css/style.css" />
  <?php if (isset($extraCss)) { echo $extraCss; } ?>
</head>

<body>

  <div class="app-layout">

    <aside class="app-sidebar">
      <div>
        <a href="dashboard.php" class="sidebar-header text-decoration-none d-flex align-items-center gap-2">
          <div class="sidebar-logo-icon"><i class="bi bi-mortarboard-fill"></i></div>
          <span class="sidebar-logo-text">SmartUni</span>
        </a>
        <nav class="sidebar-nav">
          <a href="dashboard.php" class="sidebar-link <?php echo ($currentPage == 'dashboard') ? 'active' : ''; ?>"><i class="bi bi-grid-fill"></i> Dashboard</a>
          <?php if ($currentUserRole !== 3): ?>
            <a href="timetable.php" class="sidebar-link <?php echo ($currentPage == 'timetable') ? 'active' : ''; ?>"><i class="bi bi-calendar3"></i> Timetable</a>
          <?php endif; ?>
          <?php if ($currentUserRole !== 1): ?>
            <a href="facilities.php" class="sidebar-link <?php echo ($currentPage == 'facilities') ? 'active' : ''; ?>"><i class="bi bi-building"></i> Facilities</a>
          <?php endif; ?>
          <?php if ($currentUserRole === 2 ): ?>
            <a href="my-bookings.php" class="sidebar-link <?php echo ($currentPage == 'my-bookings') ? 'active' : ''; ?>"><i class="bi bi-calendar-check"></i> My Bookings</a>
          <?php endif; ?>
          <a href="service-requests.php" class="sidebar-link <?php echo ($currentPage == 'service-requests') ? 'active' : ''; ?>"><i class="bi bi-tools"></i> Service Requests</a>
          <a href="events.php" class="sidebar-link <?php echo ($currentPage == 'events') ? 'active' : ''; ?>"><i class="bi bi-calendar-event"></i> Events</a>
          <a href="contacts.php" class="sidebar-link <?php echo ($currentPage == 'contacts') ? 'active' : ''; ?>"><i class="bi bi-shield-exclamation"></i> Safety &amp; Contacts</a>
          <?php if ($currentUserRole === 3): ?>
            <a href="staff-approvals.php" class="sidebar-link <?php echo ($currentPage == 'staff-approvals') ? 'active' : ''; ?>"><i class="bi bi-person-badge"></i> Staff Approvals</a>
            <a href="users-list.php" class="sidebar-link <?php echo ($currentPage == 'users-list') ? 'active' : ''; ?>"><i class="bi bi-people-fill"></i> User Directory</a>
          <?php endif; ?>
        </nav>
      </div>

      <?php
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
        // Automatically clean up legacy unsplash image URLs in database
        try {
            Database::iud("UPDATE `users` SET `profile_pic` = 'images/user.png' WHERE `profile_pic` LIKE '%unsplash%'");
        } catch (Exception $e) {}

        if (isset($_SESSION['user']['profile_pic']) && str_contains($_SESSION['user']['profile_pic'], 'unsplash')) {
            $_SESSION['user']['profile_pic'] = 'images/user.png';
        }

        $navUser = $_SESSION['user'] ?? [
            'fname' => 'Sandeesha',
            'reg_number' => '1234567',
            'profile_pic' => 'images/user.png'
        ];
        $rawHeaderPic = $navUser['profile_pic'] ?? '';
        $headerPic = (!empty($rawHeaderPic) && !str_contains($rawHeaderPic, 'unsplash')) ? $rawHeaderPic : 'images/user.png';
        $headerName = $navUser['fname'] ?? 'Sandeesha';
        $headerReg = $navUser['reg_number'] ?? '1234567';
      ?>
      <div class="sidebar-user-footer d-flex align-items-center justify-content-between mt-auto">
        <div class="d-flex align-items-center gap-2 overflow-hidden me-2">
          <img 
            src="<?= htmlspecialchars($headerPic) ?>" 
            alt="<?= htmlspecialchars($headerName) ?>" 
            class="rounded-circle flex-shrink-0 object-fit-cover" 
            width="38" 
            height="38" 
          />
          <div class="text-truncate">
            <div class="fw-semibold text-dark text-truncate lh-sm small"><?= htmlspecialchars($headerName) ?></div>
            <div class="text-muted small" style="font-size: 11px;">ID: <?= htmlspecialchars($headerReg) ?></div>
          </div>
        </div>

        <div class="d-flex align-items-center gap-1 flex-shrink-0">
          <a href="settings.php" class="user-action-btn" title="Settings" aria-label="Settings">
            <i class="bi bi-gear"></i>
          </a>
          <a href="api/logoutProcess.php" class="user-action-btn text-danger" title="Logout" aria-label="Logout">
            <i class="bi bi-box-arrow-right"></i>
          </a>
        </div>
      </div>
    </aside>

    <div class="app-main">

      <header class="app-topbar d-flex align-items-center justify-content-between">
        <button class="topbar-icon-btn d-lg-none" id="sidebarToggleBtn" aria-label="Toggle menu">
          <i class="bi bi-list"></i>
        </button>

        <!-- Right corner mobile logo (hidden on large screens) -->
        <a href="dashboard.php" class="d-flex d-lg-none align-items-center gap-2 text-decoration-none ms-auto">
          <div class="sidebar-logo-icon" style="width: 32px; height: 32px; font-size: 16px;"><i class="bi bi-mortarboard-fill"></i></div>
          <span class="sidebar-logo-text fw-bold" style="font-size: 18px;">SmartUni</span>
        </a>
      </header>

      <div class="page-content">