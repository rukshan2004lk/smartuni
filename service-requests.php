<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
require_once "includes/connection.php";

$pageTitle = "SmartUni Portal - Service Requests";
$currentPage = "service-requests";
require_once "includes/header.php";

$userRole = intval($_SESSION['user']['role_id'] ?? 1);
$userId   = intval($_SESSION['user']['id'] ?? 1);

if ($userRole === 3) {
    // Admin sees all campus service requests
    $sr_rs = Database::search("SELECT sr.*, s.name AS status_name, u.fname, u.lname, u.reg_number 
        FROM `service_requests` sr 
        LEFT JOIN `statuses` s ON sr.status_id = s.id 
        LEFT JOIN `users` u ON sr.user_id = u.id 
        ORDER BY sr.id DESC");
} else {
    // Student & Lecturer see their own requests
    $sr_rs = Database::search("SELECT sr.*, s.name AS status_name 
        FROM `service_requests` sr 
        LEFT JOIN `statuses` s ON sr.status_id = s.id 
        WHERE sr.user_id = '$userId' 
        ORDER BY sr.id DESC");
}

$sr_list = [];
if ($sr_rs && $sr_rs->num_rows > 0) {
    while ($r = $sr_rs->fetch_assoc()) {
        $sr_list[] = $r;
    }
}

// Available statuses for update
$status_options = [
    11 => 'Submitted',
    12 => 'In Progress',
    13 => 'Resolved',
    5  => 'Under Maintenance',
    6  => 'Closed',
    9  => 'Rejected'
];
?>

        <div class="d-flex flex-column flex-md-row align-items-md-center justify-content-between gap-3 mb-4">
          <div>
            <span class="text-muted small fw-semibold text-uppercase">PORTAL OVERVIEW · Maintenance &amp; Triage</span>
            <h1 class="fw-bold fs-3 mb-0">Service Requests</h1>
          </div>
          <?php if ($userRole === 3): ?>
            <span class="su-badge su-badge-green py-2 px-3"><i class="bi bi-shield-check me-1"></i> Admin Operations Directory</span>
          <?php else: ?>
            <a href="report-issue.php" class="btn btn-su-indigo d-inline-flex align-items-center gap-1">+ Report New Issue</a>
          <?php endif; ?>
        </div>

        <div class="d-flex flex-column gap-3 mb-4">
          <?php if (empty($sr_list)): ?>
            <div class="p-5 text-center bg-white border rounded-4 shadow-sm">
              <i class="bi bi-tools fs-1 text-muted"></i>
              <h5 class="fw-bold text-dark mt-2">No Service Requests Found</h5>
              <p class="text-secondary small">There are currently no reported service or maintenance issues.</p>
              <?php if ($userRole !== 3): ?>
                <a href="report-issue.php" class="btn btn-su-indigo btn-sm mt-2">Report New Issue</a>
              <?php endif; ?>
            </div>
          <?php else: ?>
            <?php foreach ($sr_list as $sr): ?>
              <?php
                $ref = "SR-" . str_pad($sr['id'], 4, "0", STR_PAD_LEFT);
                $cDate = date("M j, g:i A", strtotime($sr['created_at']));
                $stName = $sr['status_name'] ?? 'Submitted';
                $stId = intval($sr['status_id']);
                $reporterName = !empty($sr['fname']) ? trim($sr['fname'] . ' ' . ($sr['lname'] ?? '')) : '';
                $reporterReg  = !empty($sr['reg_number']) ? $sr['reg_number'] : '';
                
                $badgeClass = "su-badge-amber";
                if ($stId == 13 || $stId == 8 || $stId == 2) {
                    $badgeClass = "su-badge-green";
                } else if ($stId == 9 || $stId == 10) {
                    $badgeClass = "su-badge-red";
                } else if ($stId == 11) {
                    $badgeClass = "su-badge-purple";
                }
              ?>
              <div class="p-4 bg-white border rounded-4 shadow-sm d-flex flex-column flex-lg-row align-items-lg-center justify-content-between gap-3">
                <div class="d-flex align-items-center gap-3">
                  <div class="bg-warning-subtle p-3 rounded-3 text-warning-emphasis flex-shrink-0"><i class="bi bi-tools fs-3"></i></div>
                  <div>
                    <div class="d-flex align-items-center gap-2 flex-wrap">
                      <h5 class="fw-bold text-dark mb-0"><?= htmlspecialchars($sr['title']) ?></h5>
                      <span class="su-badge <?= $badgeClass ?>">● <?= htmlspecialchars($stName) ?></span>
                      <span class="badge bg-light text-dark border"><?= htmlspecialchars($sr['priority']) ?> Priority</span>
                    </div>
                    <div class="text-secondary small mt-1">
                      Submitted <?= htmlspecialchars($cDate) ?> · <?= htmlspecialchars($sr['location']) ?> · Ref: #<?= htmlspecialchars($ref) ?>
                      <?php if ($userRole === 3 && !empty($reporterName)): ?>
                        · <strong class="text-dark">Requester:</strong> <?= htmlspecialchars($reporterName) ?> (<?= htmlspecialchars($reporterReg) ?>)
                      <?php endif; ?>
                    </div>
                  </div>
                </div>

                <div class="d-flex align-items-center gap-2 flex-shrink-0">
                  <?php if ($userRole === 3): ?>
                    <!-- ADMIN STATUS UPDATE CONTROL -->
                    <div class="d-flex align-items-center gap-1">
                      <select class="form-select form-select-sm su-input me-1" id="statusSelect_<?= $sr['id'] ?>" style="min-width: 140px;">
                        <?php foreach ($status_options as $optVal => $optLabel): ?>
                          <option value="<?= $optVal ?>" <?= ($stId === $optVal) ? 'selected' : '' ?>><?= $optLabel ?></option>
                        <?php endforeach; ?>
                      </select>
                      <button type="button" class="btn btn-sm btn-su-indigo text-nowrap" onclick="updateServiceRequestStatus(<?= $sr['id'] ?>, document.getElementById('statusSelect_<?= $sr['id'] ?>').value)">
                        <i class="bi bi-check-lg me-1"></i> Update Status
                      </button>
                    </div>
                  <?php endif; ?>
                  <a href="request-detail.php?id=<?= $sr['id'] ?>" class="btn btn-sm btn-su-outline text-nowrap">Tracker <i class="bi bi-arrow-right ms-1"></i></a>
                </div>
              </div>
            <?php endforeach; ?>
          <?php endif; ?>
        </div>

<?php require_once "includes/footer.php"; ?>

