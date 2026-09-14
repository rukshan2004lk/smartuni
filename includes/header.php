<?php
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
        <div class="sidebar-header">
          <div class="sidebar-logo-icon"><i class="bi bi-mortarboard-fill"></i></div>
          <span class="sidebar-logo-text">SmartUni</span>
        </div>
        <nav class="sidebar-nav">
          <a href="dashboard.php" class="sidebar-link <?php echo ($currentPage == 'dashboard') ? 'active' : ''; ?>"><i class="bi bi-grid-fill"></i> Dashboard</a>
          <a href="timetable.php" class="sidebar-link <?php echo ($currentPage == 'timetable') ? 'active' : ''; ?>"><i class="bi bi-calendar3"></i> Timetable</a>
          <a href="facilities.php" class="sidebar-link <?php echo ($currentPage == 'facilities') ? 'active' : ''; ?>"><i class="bi bi-building"></i> Facilities</a>
          <a href="my-bookings.php" class="sidebar-link <?php echo ($currentPage == 'my-bookings') ? 'active' : ''; ?>"><i class="bi bi-calendar-check"></i> My Bookings</a>
          <a href="service-requests.php" class="sidebar-link <?php echo ($currentPage == 'service-requests') ? 'active' : ''; ?>"><i class="bi bi-tools"></i> Service Requests</a>
          <a href="events.php" class="sidebar-link <?php echo ($currentPage == 'events') ? 'active' : ''; ?>"><i class="bi bi-calendar-event"></i> Events</a>
          <a href="contacts.php" class="sidebar-link <?php echo ($currentPage == 'contacts') ? 'active' : ''; ?>"><i class="bi bi-shield-exclamation"></i> Safety &amp; Contacts</a>
          <a href="staff-approvals.php" class="sidebar-link <?php echo ($currentPage == 'staff-approvals') ? 'active' : ''; ?>"><i class="bi bi-person-badge"></i> Staff Approvals</a>
        </nav>
      </div>

      <div class="sidebar-user-footer d-flex align-items-center justify-content-between">
        <div class="d-flex align-items-center gap-2 overflow-hidden me-2">
          <img 
            src="https://images.unsplash.com/photo-1534528741775-53994a69daeb?q=80&w=120&auto=format&fit=crop" 
            alt="Sandeesha" 
            class="rounded-circle flex-shrink-0" 
            width="38" 
            height="38" 
          />
          <div class="text-truncate">
            <div class="fw-semibold text-dark text-truncate lh-sm small">Sandeesha</div>
            <div class="text-muted small" style="font-size: 11px;">ID: 202488</div>
          </div>
        </div>

        <div class="d-flex align-items-center gap-1 flex-shrink-0">
          <a href="settings.php" class="user-action-btn" title="Settings" aria-label="Settings">
            <i class="bi bi-gear"></i>
          </a>
          <a href="index.php" class="user-action-btn text-danger" title="Logout" aria-label="Logout">
            <i class="bi bi-box-arrow-right"></i>
          </a>
        </div>
      </div>
    </aside>

    <div class="app-main">

      <header class="app-topbar">
        <button class="topbar-icon-btn d-lg-none" id="sidebarToggleBtn" aria-label="Toggle menu">
          <i class="bi bi-list"></i>
        </button>
      </header>

      <div class="page-content">
