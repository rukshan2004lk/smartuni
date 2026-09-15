<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
require_once "includes/connection.php";

$pageTitle = "SmartUni Portal - Dashboard";
$currentPage = "dashboard";
require_once "includes/header.php";

$user = $_SESSION['user'] ?? [
    'id' => 1,
    'fname' => 'Sandeesha',
    'lname' => 'Rukshan',
    'role_id' => 1
];

$userId   = intval($user['id'] ?? 1);
$userName = htmlspecialchars($user['fname'] ?? 'User');
$roleId   = intval($user['role_id'] ?? 1);

// Dynamic Queries
// 1. My Bookings Count
$my_bk_rs = Database::search("SELECT COUNT(*) AS total FROM `bookings` WHERE `user_id` = '$userId'");
$myBookingsCount = ($my_bk_rs && $row = $my_bk_rs->fetch_assoc()) ? intval($row['total']) : 0;

// 2. My Service Requests Count
$my_sr_rs = Database::search("SELECT COUNT(*) AS total FROM `service_requests` WHERE `user_id` = '$userId'");
$myRequestsCount = ($my_sr_rs && $row = $my_sr_rs->fetch_assoc()) ? intval($row['total']) : 0;

// 3. All Active Service Requests (Admin view)
$all_sr_rs = Database::search("SELECT COUNT(*) AS total FROM `service_requests`");
$allRequestsCount = ($all_sr_rs && $row = $all_sr_rs->fetch_assoc()) ? intval($row['total']) : 0;

// 4. Pending Booking Approvals (Admin view)
$pending_bk_rs = Database::search("SELECT COUNT(*) AS total FROM `bookings` WHERE `status_id` = 7");
$pendingApprovalsCount = ($pending_bk_rs && $row = $pending_bk_rs->fetch_assoc()) ? intval($row['total']) : 0;

// 5. Campus Events This Month Count
$events_rs = Database::search("SELECT COUNT(*) AS total FROM `events` WHERE MONTH(`date`) = MONTH(CURRENT_DATE()) AND YEAR(`date`) = YEAR(CURRENT_DATE())");
$eventsThisMonthCount = ($events_rs && $row = $events_rs->fetch_assoc()) ? intval($row['total']) : 0;
if ($eventsThisMonthCount === 0) {
    // Fallback to total events count
    $all_ev_rs = Database::search("SELECT COUNT(*) AS total FROM `events`");
    $eventsThisMonthCount = ($all_ev_rs && $row = $all_ev_rs->fetch_assoc()) ? intval($row['total']) : 0;
}

// 6. User Recent Bookings (For Lecturer view)
$recent_bk_rs = Database::search("SELECT b.*, f.name AS facility_name, f.location AS facility_location, s.name AS status_name 
    FROM `bookings` b 
    LEFT JOIN `facilities` f ON b.facility_id = f.id 
    LEFT JOIN `statuses` s ON b.status_id = s.id 
    WHERE b.user_id = '$userId' 
    ORDER BY b.id DESC LIMIT 2");
$recent_bookings = [];
if ($recent_bk_rs && $recent_bk_rs->num_rows > 0) {
    while ($row = $recent_bk_rs->fetch_assoc()) {
        $recent_bookings[] = $row;
    }
}

// 7. Today's Facility Bookings (For Admin view)
$today_fac_bk_rs = Database::search("SELECT b.*, f.name AS facility_name, f.location AS facility_location, u.fname, u.lname, s.name AS status_name 
    FROM `bookings` b 
    LEFT JOIN `facilities` f ON b.facility_id = f.id 
    LEFT JOIN `users` u ON b.user_id = u.id 
    LEFT JOIN `statuses` s ON b.status_id = s.id 
    WHERE b.booking_date = CURDATE() 
    ORDER BY b.start_time ASC");
$today_facility_bookings = [];
if ($today_fac_bk_rs && $today_fac_bk_rs->num_rows > 0) {
    while ($row = $today_fac_bk_rs->fetch_assoc()) {
        $today_facility_bookings[] = $row;
    }
}

// 8. Today's Class Schedule (For Student & Lecturer view)
if (!function_exists('parseTimeToMinutesDashboard')) {
    function parseTimeToMinutesDashboard($timeStr) {
        $timeStr = trim($timeStr);
        if (empty($timeStr)) return 0;
        $parts = explode(':', $timeStr);
        $h = intval($parts[0] ?? 0);
        $m = intval($parts[1] ?? 0);
        return ($h * 60) + $m;
    }
}

$todayDayName  = date("l");
$todayDayShort = date("D");

$today_tt_rs = Database::search("SELECT * FROM `timetable` WHERE LOWER(`day_of_week`) LIKE '%" . strtolower(addslashes($todayDayName)) . "%' OR LOWER(`day_of_week`) LIKE '%" . strtolower(addslashes($todayDayShort)) . "%' ORDER BY `start_time` ASC");
$today_timetable = [];
if ($today_tt_rs && $today_tt_rs->num_rows > 0) {
    while ($row = $today_tt_rs->fetch_assoc()) {
        $today_timetable[] = $row;
    }
}
$currentMin = (intval(date('H')) * 60) + intval(date('i'));
?>

        <!-- Welcome Banner -->
        <div class="mb-4 d-flex align-items-center justify-content-between">
          <div>
            <h1 class="fw-bold fs-3 mb-1">Welcome back, <?= $userName ?></h1>
            <p class="text-secondary small mb-0"><?= date("l, F j, Y") ?></p>
          </div>
        
        </div>

        <!-- Stat Cards Grid -->
        <div class="row g-3 mb-4">
          <?php if ($roleId === 3): ?>
            <!-- ADMIN STAT CARDS: Service Requests, Campus Events, Pending Approvals -->
            <div class="col-12 col-md-4">
              <div class="stat-card p-3 bg-white border rounded-3 shadow-sm d-flex justify-content-between align-items-center" onclick="location.href='service-requests.php'" style="cursor:pointer;">
                <div>
                  <div class="stat-label text-muted small">Current Service Requests</div>
                  <div class="stat-value fs-4 fw-bold"><?= $allRequestsCount ?></div>
                </div>
                <div class="stat-icon-wrapper fs-3 text-warning"><i class="bi bi-wrench-adjustable"></i></div>
              </div>
            </div>
            <div class="col-12 col-md-4">
              <div class="stat-card p-3 bg-white border rounded-3 shadow-sm d-flex justify-content-between align-items-center" onclick="location.href='events.php'" style="cursor:pointer;">
                <div>
                  <div class="stat-label text-muted small">Campus Events</div>
                  <div class="stat-value fs-4 fw-bold"><?= $eventsThisMonthCount ?></div>
                </div>
                <div class="stat-icon-wrapper fs-3 text-info"><i class="bi bi-calendar-event"></i></div>
              </div>
            </div>
            <div class="col-12 col-md-4">
              <div class="stat-card p-3 bg-white border rounded-3 shadow-sm d-flex justify-content-between align-items-center" onclick="location.href='staff-approvals.php'" style="cursor:pointer;">
                <div>
                  <div class="stat-label text-muted small">Pending Approvals</div>
                  <div class="stat-value fs-4 fw-bold"><?= $pendingApprovalsCount ?></div>
                </div>
                <div class="stat-icon-wrapper fs-3 text-primary"><i class="bi bi-check-circle"></i></div>
              </div>
            </div>

          <?php elseif ($roleId === 2): ?>
            <!-- LECTURER STAT CARDS: My Bookings, Service Requests, Campus Events -->
            <div class="col-12 col-md-4">
              <div class="stat-card p-3 bg-white border rounded-3 shadow-sm d-flex justify-content-between align-items-center" onclick="location.href='my-bookings.php'" style="cursor:pointer;">
                <div>
                  <div class="stat-label text-muted small">My Bookings</div>
                  <div class="stat-value fs-4 fw-bold"><?= $myBookingsCount ?></div>
                </div>
                <div class="stat-icon-wrapper fs-3 text-primary"><i class="bi bi-calendar-check"></i></div>
              </div>
            </div>
            <div class="col-12 col-md-4">
              <div class="stat-card p-3 bg-white border rounded-3 shadow-sm d-flex justify-content-between align-items-center" onclick="location.href='service-requests.php'" style="cursor:pointer;">
                <div>
                  <div class="stat-label text-muted small">Service Requests</div>
                  <div class="stat-value fs-4 fw-bold"><?= $myRequestsCount ?></div>
                </div>
                <div class="stat-icon-wrapper fs-3 text-warning"><i class="bi bi-wrench-adjustable"></i></div>
              </div>
            </div>
            <div class="col-12 col-md-4">
              <div class="stat-card p-3 bg-white border rounded-3 shadow-sm d-flex justify-content-between align-items-center" onclick="location.href='events.php'" style="cursor:pointer;">
                <div>
                  <div class="stat-label text-muted small">Campus Events</div>
                  <div class="stat-value fs-4 fw-bold"><?= $eventsThisMonthCount ?></div>
                </div>
                <div class="stat-icon-wrapper fs-3 text-info"><i class="bi bi-calendar-event"></i></div>
              </div>
            </div>

          <?php else: ?>
            <!-- STUDENT STAT CARDS: Service Requests, Campus Events -->
            <div class="col-12 col-md-6">
              <div class="stat-card p-3 bg-white border rounded-3 shadow-sm d-flex justify-content-between align-items-center" onclick="location.href='service-requests.php'" style="cursor:pointer;">
                <div>
                  <div class="stat-label text-muted small">Service Requests</div>
                  <div class="stat-value fs-4 fw-bold"><?= $myRequestsCount ?></div>
                </div>
                <div class="stat-icon-wrapper fs-3 text-warning"><i class="bi bi-wrench-adjustable"></i></div>
              </div>
            </div>
            <div class="col-12 col-md-6">
              <div class="stat-card p-3 bg-white border rounded-3 shadow-sm d-flex justify-content-between align-items-center" onclick="location.href='events.php'" style="cursor:pointer;">
                <div>
                  <div class="stat-label text-muted small">Campus Events</div>
                  <div class="stat-value fs-4 fw-bold"><?= $eventsThisMonthCount ?></div>
                </div>
                <div class="stat-icon-wrapper fs-3 text-info"><i class="bi bi-calendar-event"></i></div>
              </div>
            </div>
          <?php endif; ?>
        </div>

        <div class="row g-4">

          <!-- Left Main Content Column -->
          <div class="col-12 col-lg-8">

            <?php if ($roleId === 3): ?>
              <!-- ADMIN: Today's Facility Booking Schedule -->
              <div class="bg-white border rounded-3 p-4 mb-4 shadow-sm">
                <div class="d-flex align-items-center justify-content-between mb-3">
                  <h5 class="fw-bold mb-0"><i class="bi bi-building me-2 text-primary"></i>Today's Facility Booking Schedule</h5>
                  <a href="staff-approvals.php" class="text-primary small fw-semibold text-decoration-none">Manage Approvals</a>
                </div>

                <div class="d-flex flex-column gap-2">
                  <?php if (empty($today_facility_bookings)): ?>
                    <div class="p-4 text-center text-muted bg-light rounded-3">
                      <i class="bi bi-calendar-check fs-3 d-block mb-1 text-secondary"></i>
                      No facility reservations scheduled for today (<?= date("M j, Y") ?>).
                    </div>
                  <?php else: ?>
                    <?php foreach ($today_facility_bookings as $tbk): ?>
                      <?php
                        $tbkStart = date("g:i A", strtotime($tbk['start_time']));
                        $tbkEnd   = date("g:i A", strtotime($tbk['end_time']));
                        $reqName  = trim(($tbk['fname'] ?? '') . ' ' . ($tbk['lname'] ?? ''));
                        $stName   = $tbk['status_name'] ?? 'Pending';
                        $stId     = intval($tbk['status_id']);
                        $bClass   = "bg-warning-subtle text-warning-emphasis";
                        if ($stId === 8) $bClass = "bg-success-subtle text-success";
                        if ($stId === 9 || $stId === 10) $bClass = "bg-danger-subtle text-danger";
                      ?>
                      <div class="p-3 bg-light rounded-3 d-flex flex-column flex-sm-row align-items-sm-center justify-content-between gap-2 border-start border-4 border-primary">
                        <div class="d-flex align-items-center gap-3">
                          <span class="fw-bold text-dark small"><?= $tbkStart ?> – <?= $tbkEnd ?></span>
                          <div>
                            <div class="fw-bold text-dark"><?= htmlspecialchars($tbk['facility_name'] ?? 'Facility') ?></div>
                            <div class="text-muted small">Requester: <?= htmlspecialchars($reqName ?: 'Campus User') ?> · <?= htmlspecialchars($tbk['facility_location'] ?? '') ?></div>
                          </div>
                        </div>
                        <span class="su-badge badge <?= $bClass ?>"><?= htmlspecialchars($stName) ?></span>
                      </div>
                    <?php endforeach; ?>
                  <?php endif; ?>
                </div>
              </div>

            <?php else: ?>
              <!-- STUDENT & LECTURER: Today's Class Schedule -->
              <div class="bg-white border rounded-3 p-4 mb-4 shadow-sm">
                <div class="d-flex align-items-center justify-content-between mb-3">
                  <h5 class="fw-bold mb-0"><i class="bi bi-clock me-2 text-primary"></i>Today's Class Schedule</h5>
                  <a href="timetable.php" class="text-primary small fw-semibold text-decoration-none">Full Timetable</a>
                </div>

                <div class="d-flex flex-column gap-2">
                  <?php if (empty($today_timetable)): ?>
                    <div class="p-3 bg-light rounded-3 text-center text-muted small">
                      <i class="bi bi-calendar-x me-1"></i> No lectures scheduled for today (<?= date('l') ?>).
                      <a href="timetable.php" class="text-primary fw-semibold ms-1">View Full Timetable</a>
                    </div>
                  <?php else: ?>
                    <?php foreach ($today_timetable as $tt): ?>
                      <?php
                        $startMin = parseTimeToMinutesDashboard($tt['start_time']);
                        $endMin   = parseTimeToMinutesDashboard($tt['end_time']);

                        $startTimeDisp = date("h:i A", strtotime($tt['start_time']));
                        $endTimeDisp   = date("h:i A", strtotime($tt['end_time']));
                        $timeRangeStr  = $startTimeDisp . " - " . $endTimeDisp;

                        $statusBadge = '';
                        $borderClass = '';

                        if ($currentMin >= $startMin && $currentMin <= $endMin) {
                            $borderClass = 'border-start border-4 border-success';
                            $statusBadge = '<span class="su-badge su-badge-green badge bg-success-subtle text-success">In Progress</span>';
                        } else if ($currentMin < $startMin) {
                            $borderClass = 'border-start border-4 border-primary';
                            $statusBadge = '<span class="su-badge su-badge-indigo badge bg-primary-subtle text-primary">Upcoming</span>';
                        } else {
                            $borderClass = 'border-start border-4 border-secondary';
                            $statusBadge = '<span class="su-badge su-badge-gray badge bg-secondary-subtle text-secondary">Completed</span>';
                        }
                      ?>
                      <div class="p-3 bg-light rounded-3 d-flex flex-column flex-sm-row align-items-sm-center justify-content-between gap-2 <?= $borderClass ?>">
                        <div class="d-flex align-items-center gap-3">
                          <span class="fw-bold text-dark small" style="min-width: 120px;"><?= htmlspecialchars($timeRangeStr) ?></span>
                          <div>
                            <div class="fw-bold text-dark"><?= htmlspecialchars($tt['course_code']) ?> - <?= htmlspecialchars($tt['course_name']) ?></div>
                            <?php if (!empty($tt['location'])): ?>
                              <div class="text-muted small"><i class="bi bi-geo-alt-fill text-primary me-1"></i><?= htmlspecialchars($tt['location']) ?></div>
                            <?php else: ?>
                              <div class="text-muted small"><i class="bi bi-calendar-event me-1"></i><?= htmlspecialchars($tt['day_of_week']) ?></div>
                            <?php endif; ?>
                          </div>
                        </div>
                        <?= $statusBadge ?>
                      </div>
                    <?php endforeach; ?>
                  <?php endif; ?>
                </div>
              </div>
            <?php endif; ?>

            <?php if ($roleId === 2): ?>
              <!-- LECTURER ONLY: Recent Bookings Section -->
              <div class="bg-white border rounded-3 p-4 shadow-sm">
                <div class="d-flex align-items-center justify-content-between mb-3">
                  <h5 class="fw-bold mb-0"><i class="bi bi-calendar-check me-2 text-primary"></i>Recent Bookings</h5>
                  <a href="my-bookings.php" class="text-primary small fw-semibold text-decoration-none">View all</a>
                </div>
                <div class="row g-3">
                  <?php if (empty($recent_bookings)): ?>
                    <div class="col-12 text-muted small py-3 text-center">
                      No recent bookings found. <a href="facilities.php" class="text-primary">Book a facility now</a>
                    </div>
                  <?php else: ?>
                    <?php foreach ($recent_bookings as $rbk): ?>
                      <?php
                        $stName = $rbk['status_name'] ?? 'Pending';
                        $stId = intval($rbk['status_id']);
                        $badgeClass = "bg-warning-subtle text-warning-emphasis";
                        if ($stId === 8) $badgeClass = "bg-success-subtle text-success";
                        if ($stId === 9 || $stId === 10) $badgeClass = "bg-danger-subtle text-danger";
                        
                        $rbDate = date("M j", strtotime($rbk['booking_date']));
                        $rbStart = date("g:i A", strtotime($rbk['start_time']));
                        $rbEnd = date("g:i A", strtotime($rbk['end_time']));
                      ?>
                      <div class="col-12 col-md-6">
                        <div class="p-3 bg-light rounded-3">
                          <div class="d-flex justify-content-between align-items-center mb-2">
                            <span class="fw-bold text-dark"><?= htmlspecialchars($rbk['facility_name'] ?? 'Facility') ?></span>
                            <span class="su-badge badge <?= $badgeClass ?>"><?= htmlspecialchars($stName) ?></span>
                          </div>
                          <div class="text-muted small mb-1"><?= htmlspecialchars($rbk['facility_location'] ?? '') ?></div>
                          <div class="text-muted small"><i class="bi bi-clock me-1"></i><?= $rbDate ?>, <?= $rbStart ?> - <?= $rbEnd ?></div>
                        </div>
                      </div>
                    <?php endforeach; ?>
                  <?php endif; ?>
                </div>
              </div>
            <?php endif; ?>

          </div>

          <!-- Right Column: Role-Specific Quick Actions -->
          <div class="col-12 col-lg-4">

            <div class="bg-white border rounded-3 p-4 shadow-sm">
              <h5 class="fw-bold mb-3">Quick Actions</h5>
              <div class="d-flex flex-column gap-2">

                <?php if ($roleId === 3): ?>
                  <!-- ADMIN QUICK ACTIONS -->
                  <a href="staff-approvals.php" class="p-3 bg-light rounded-3 d-flex align-items-center justify-content-between text-dark text-decoration-none">
                    <div class="d-flex align-items-center gap-3">
                      <div class="bg-indigo-subtle p-2 rounded text-primary"><i class="bi bi-check-circle-fill fs-5"></i></div>
                      <span class="fw-semibold">Booking Approvals</span>
                    </div>
                    <i class="bi bi-chevron-right text-muted"></i>
                  </a>
                  <a href="users-list.php" class="p-3 bg-light rounded-3 d-flex align-items-center justify-content-between text-dark text-decoration-none">
                    <div class="d-flex align-items-center gap-3">
                      <div class="bg-success-subtle p-2 rounded text-success"><i class="bi bi-people-fill fs-5"></i></div>
                      <span class="fw-semibold">User Directory</span>
                    </div>
                    <i class="bi bi-chevron-right text-muted"></i>
                  </a>
                  <a href="events.php" class="p-3 bg-light rounded-3 d-flex align-items-center justify-content-between text-dark text-decoration-none">
                    <div class="d-flex align-items-center gap-3">
                      <div class="bg-info-subtle p-2 rounded text-info"><i class="bi bi-calendar-event-fill fs-5"></i></div>
                      <span class="fw-semibold">Campus Events</span>
                    </div>
                    <i class="bi bi-chevron-right text-muted"></i>
                  </a>
                  <a href="contacts.php" class="p-3 bg-light rounded-3 d-flex align-items-center justify-content-between text-dark text-decoration-none">
                    <div class="d-flex align-items-center gap-3">
                      <div class="bg-warning-subtle p-2 rounded text-warning"><i class="bi bi-shield-exclamation fs-5"></i></div>
                      <span class="fw-semibold">Safety &amp; Contacts</span>
                    </div>
                    <i class="bi bi-chevron-right text-muted"></i>
                  </a>

                <?php elseif ($roleId === 2): ?>
                  <!-- LECTURER QUICK ACTIONS: Book Facility, Report Issue, Safety Contacts -->
                  <a href="facilities.php" class="p-3 bg-light rounded-3 d-flex align-items-center justify-content-between text-dark text-decoration-none">
                    <div class="d-flex align-items-center gap-3">
                      <div class="bg-indigo-subtle p-2 rounded text-primary"><i class="bi bi-calendar-plus-fill fs-5"></i></div>
                      <span class="fw-semibold">Book a Facility</span>
                    </div>
                    <i class="bi bi-chevron-right text-muted"></i>
                  </a>
                  <a href="report-issue.php" class="p-3 bg-light rounded-3 d-flex align-items-center justify-content-between text-dark text-decoration-none">
                    <div class="d-flex align-items-center gap-3">
                      <div class="bg-warning-subtle p-2 rounded text-warning"><i class="bi bi-exclamation-triangle-fill fs-5"></i></div>
                      <span class="fw-semibold">Report an Issue</span>
                    </div>
                    <i class="bi bi-chevron-right text-muted"></i>
                  </a>
                  <a href="contacts.php" class="p-3 bg-light rounded-3 d-flex align-items-center justify-content-between text-dark text-decoration-none">
                    <div class="d-flex align-items-center gap-3">
                      <div class="bg-info-subtle p-2 rounded text-info"><i class="bi bi-shield-exclamation fs-5"></i></div>
                      <span class="fw-semibold">Safety &amp; Contacts</span>
                    </div>
                    <i class="bi bi-chevron-right text-muted"></i>
                  </a>

                <?php else: ?>
                  <!-- STUDENT QUICK ACTIONS: Report Issue, Safety Contacts -->
                  <a href="report-issue.php" class="p-3 bg-light rounded-3 d-flex align-items-center justify-content-between text-dark text-decoration-none">
                    <div class="d-flex align-items-center gap-3">
                      <div class="bg-warning-subtle p-2 rounded text-warning"><i class="bi bi-exclamation-triangle-fill fs-5"></i></div>
                      <span class="fw-semibold">Report an Issue</span>
                    </div>
                    <i class="bi bi-chevron-right text-muted"></i>
                  </a>
                  <a href="contacts.php" class="p-3 bg-light rounded-3 d-flex align-items-center justify-content-between text-dark text-decoration-none">
                    <div class="d-flex align-items-center gap-3">
                      <div class="bg-info-subtle p-2 rounded text-info"><i class="bi bi-shield-exclamation fs-5"></i></div>
                      <span class="fw-semibold">Safety &amp; Contacts</span>
                    </div>
                    <i class="bi bi-chevron-right text-muted"></i>
                  </a>
                <?php endif; ?>

              </div>
            </div>

          </div>

        </div>

<?php require_once "includes/footer.php"; ?>