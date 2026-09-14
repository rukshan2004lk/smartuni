<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
require_once "includes/connection.php";

$pageTitle = "SmartUni Portal - Request Tracker";
$currentPage = "service-requests";
require_once "includes/header.php";

$srId = isset($_GET['id']) ? intval($_GET['id']) : intval($_SESSION['last_sr_id'] ?? 0);
$sr = null;

if ($srId > 0) {
    $sr_rs = Database::search("SELECT sr.*, 
        s.name AS status_name, 
        u.fname, u.lname, u.reg_number, u.email AS user_email 
        FROM `service_requests` sr 
        LEFT JOIN `statuses` s ON sr.status_id = s.id 
        LEFT JOIN `users` u ON sr.user_id = u.id 
        WHERE sr.id = '$srId'");
    if ($sr_rs && $sr_rs->num_rows > 0) {
        $sr = $sr_rs->fetch_assoc();
    }
}

// Fallback to latest request if specific ID not found
if (!$sr) {
    $userId = $_SESSION['user']['id'] ?? 1;
    $sr_rs = Database::search("SELECT sr.*, 
        s.name AS status_name, 
        u.fname, u.lname, u.reg_number, u.email AS user_email 
        FROM `service_requests` sr 
        LEFT JOIN `statuses` s ON sr.status_id = s.id 
        LEFT JOIN `users` u ON sr.user_id = u.id 
        WHERE sr.user_id = '$userId' 
        ORDER BY sr.id DESC LIMIT 1");
    if ($sr_rs && $sr_rs->num_rows > 0) {
        $sr = $sr_rs->fetch_assoc();
    }
}

// Data mapping
$srRef         = $sr ? "SR-" . str_pad($sr['id'], 4, "0", STR_PAD_LEFT) : "#SR-4092";
$title         = $sr['title'] ?? 'HVAC Unit Noise & Vibration in Robotics Lab';
$description   = $sr['description'] ?? 'Ceiling AC duct emitting loud rattling noise and erratic cooling.';
$location      = !empty($sr['location']) ? $sr['location'] : 'TEB Room 314';
$priority      = $sr['priority'] ?? 'Medium';
$statusName    = $sr['status_name'] ?? 'Submitted';
$statusId      = intval($sr['status_id'] ?? 11);
$reporterName  = !empty($sr['fname']) ? $sr['fname'] . " " . $sr['lname'] : 'Alex Chen';
$reporterReg   = !empty($sr['reg_number']) ? $sr['reg_number'] : '202488';
$createdTime   = !empty($sr['created_at']) ? date("M j, Y \a\\t g:i A", strtotime($sr['created_at'])) : date("M j, Y \a\\t g:i A");

// Badge color logic
$badgeClass = "su-badge-amber";
if ($statusId == 13 || $statusId == 8 || $statusId == 2) {
    $badgeClass = "su-badge-green";
} else if ($statusId == 9 || $statusId == 10) {
    $badgeClass = "su-badge-red";
} else if ($statusId == 11) {
    $badgeClass = "su-badge-purple";
}
?>

        <a href="service-requests.php" class="text-secondary small fw-semibold text-decoration-none d-inline-flex align-items-center gap-1 mb-3">
          <i class="bi bi-arrow-left"></i> Back to Service Requests / Request #<?= htmlspecialchars($srRef) ?>
        </a>

        <div class="max-w-900 mx-auto">

          <!-- Header -->
          <div class="d-flex flex-column flex-md-row align-items-md-center justify-content-between gap-2 mb-4">
            <div>
              <h1 class="fw-bold fs-3 mb-1"><?= htmlspecialchars($title) ?></h1>
              <p class="text-secondary small mb-0">Submitted on <?= htmlspecialchars($createdTime) ?> · Location: <?= htmlspecialchars($location) ?></p>
            </div>
            <div>
              <span class="su-badge <?= $badgeClass ?> fs-6 py-2 px-3">● <?= htmlspecialchars($statusName) ?></span>
            </div>
          </div>

          <!-- Progress Stepper -->
          <div class="bg-white border rounded-4 p-4 shadow-sm mb-4">
            <div class="su-stepper">
              <div class="su-stepper-line"></div>
              <div class="su-stepper-line-active" style="width: <?= ($statusId == 13) ? '100%' : (($statusId == 12) ? '70%' : '25%') ?>;"></div>

              <div class="su-step-item completed">
                <div class="su-step-icon"><i class="bi bi-check"></i></div>
                <div class="su-step-title">Submitted</div>
                <div class="su-step-time"><?= date("g:i A", strtotime($sr['created_at'] ?? 'now')) ?></div>
              </div>

              <div class="su-step-item <?= ($statusId == 12 || $statusId == 13) ? 'completed' : 'active' ?>">
                <div class="su-step-icon"><?= ($statusId == 12 || $statusId == 13) ? '<i class="bi bi-check"></i>' : '●' ?></div>
                <div class="su-step-title">Under Review</div>
                <div class="su-step-time">Operations Desk</div>
              </div>

              <div class="su-step-item <?= ($statusId == 12 || $statusId == 13) ? 'active' : '' ?>">
                <div class="su-step-icon"><?= ($statusId == 13) ? '<i class="bi bi-check"></i>' : (($statusId == 12) ? '●' : '') ?></div>
                <div class="su-step-title <?= ($statusId == 12) ? 'text-primary fw-bold' : '' ?>">In Progress</div>
                <div class="su-step-time"><?= ($statusId == 12) ? 'Technician Dispatched' : 'Pending' ?></div>
              </div>

              <div class="su-step-item <?= ($statusId == 13) ? 'completed' : '' ?>">
                <div class="su-step-icon"><?= ($statusId == 13) ? '<i class="bi bi-check"></i>' : '' ?></div>
                <div class="su-step-title <?= ($statusId == 13) ? 'text-success fw-bold' : 'text-muted' ?>">Resolved</div>
                <div class="su-step-time"><?= ($statusId == 13) ? 'Completed' : 'Pending' ?></div>
              </div>
            </div>
          </div>

          <!-- Request Details Card -->
          <div class="bg-white border rounded-4 p-4 shadow-sm mb-4">
            <div class="d-flex justify-content-between align-items-center mb-4 pb-2 border-bottom">
              <h5 class="fw-bold mb-0">Request Details</h5>
              <span class="text-muted small">Ref: <?= htmlspecialchars($srRef) ?></span>
            </div>

            <div class="row g-3 mb-4">
              <div class="col-6 col-md-4">
                <div class="text-muted small">Campus Location</div>
                <div class="fw-bold text-dark"><?= htmlspecialchars($location) ?></div>
              </div>
              <div class="col-6 col-md-4">
                <div class="text-muted small">Priority</div>
                <div class="fw-bold text-dark"><?= htmlspecialchars($priority) ?></div>
              </div>
              <div class="col-6 col-md-4">
                <div class="text-muted small">Reporter</div>
                <div class="fw-bold text-dark"><?= htmlspecialchars($reporterName) ?> (ID: <?= htmlspecialchars($reporterReg) ?>)</div>
              </div>
            </div>

            <div class="mb-4">
              <div class="text-muted small mb-1">Issue Description</div>
              <p class="text-dark bg-light p-3 rounded-3 small mb-0">
                <?= nl2br(htmlspecialchars($description)) ?>
              </p>
            </div>
          </div>

          <?php if (intval($_SESSION['user']['role_id'] ?? 1) === 3): ?>
            <!-- ADMIN STATUS MANAGEMENT CARD -->
            <div class="bg-white border rounded-4 p-4 shadow-sm mb-4 border-start border-4 border-primary">
              <div class="d-flex align-items-center justify-content-between mb-3 border-bottom pb-2">
                <h5 class="fw-bold mb-0 text-dark"><i class="bi bi-shield-check me-2 text-primary"></i>Admin Control Panel: Update Request Status</h5>
                <span class="badge bg-primary-subtle text-primary fw-bold">Admin Only</span>
              </div>
              <p class="text-secondary small mb-3">Update the processing status for this service request below. The reporter will be notified instantly.</p>
              
              <div class="row g-3 align-items-center">
                <div class="col-12 col-md-6">
                  <label class="su-label">Select New Processing Status</label>
                  <select class="form-select su-input" id="adminDetailStatusSelect">
                    <option value="11" <?= ($statusId === 11) ? 'selected' : '' ?>>Submitted (New Request)</option>
                    <option value="12" <?= ($statusId === 12) ? 'selected' : '' ?>>In Progress (Technician Dispatched)</option>
                    <option value="13" <?= ($statusId === 13) ? 'selected' : '' ?>>Resolved (Issue Fixed)</option>
                    <option value="5"  <?= ($statusId === 5)  ? 'selected' : '' ?>>Under Maintenance</option>
                    <option value="6"  <?= ($statusId === 6)  ? 'selected' : '' ?>>Closed</option>
                    <option value="9"  <?= ($statusId === 9)  ? 'selected' : '' ?>>Rejected</option>
                  </select>
                </div>
                <div class="col-12 col-md-6 pt-md-4">
                  <button type="button" class="btn btn-su-indigo px-4 py-2" onclick="updateServiceRequestStatus(<?= $sr['id'] ?? $srId ?>, document.getElementById('adminDetailStatusSelect').value)">
                    <i class="bi bi-arrow-repeat me-1"></i> Update Request Status
                  </button>
                </div>
              </div>
            </div>
          <?php endif; ?>

        </div>

<?php require_once "includes/footer.php"; ?>


