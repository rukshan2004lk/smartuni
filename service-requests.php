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
$totalCount = 0;
$pendingCount = 0;
$progressCount = 0;
$resolvedCount = 0;

if ($sr_rs && $sr_rs->num_rows > 0) {
    while ($r = $sr_rs->fetch_assoc()) {
        $sr_list[] = $r;
        $totalCount++;
        $st = intval($r['status_id']);
        if ($st == 11) {
            $pendingCount++;
        } else if ($st == 12 || $st == 5) {
            $progressCount++;
        } else if ($st == 13 || $st == 8 || $st == 2 || $st == 6) {
            $resolvedCount++;
        }
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

        <!-- Header Section -->
        <div class="d-flex flex-column flex-md-row align-items-md-center justify-content-between gap-3 mb-4">
          <div>
            <span class="text-muted small fw-semibold text-uppercase">FACILITY MANAGEMENT · Maintenance &amp; Incident Triage</span>
            <h1 class="fw-bold fs-3 mb-1">Service Requests</h1>
            <p class="text-secondary small mb-0">Track campus repair tickets, lodge new infrastructure issues, and follow status updates in real-time.</p>
          </div>
          <?php if ($userRole === 3): ?>
          
          <?php else: ?>
            <a href="report-issue.php" class="btn btn-su-indigo d-inline-flex align-items-center gap-2 shadow-sm">
              <i class="bi bi-plus-circle-fill"></i> Report New Issue
            </a>
          <?php endif; ?>
        </div>

        <!-- Metric KPI Cards -->
        <div class="row g-3 mb-4">
          <div class="col-12 col-sm-6 col-xl-3">
            <div class="sr-stat-card d-flex align-items-center gap-3">
              <div class="sr-stat-icon total"><i class="bi bi-ticket-detailed"></i></div>
              <div>
                <div class="text-muted small fw-semibold">Total Requests</div>
                <div class="sr-stat-value"><?= $totalCount ?></div>
              </div>
            </div>
          </div>
          <div class="col-12 col-sm-6 col-xl-3">
            <div class="sr-stat-card d-flex align-items-center gap-3">
              <div class="sr-stat-icon pending"><i class="bi bi-clock-history"></i></div>
              <div>
                <div class="text-muted small fw-semibold">Submitted / Pending</div>
                <div class="sr-stat-value"><?= $pendingCount ?></div>
              </div>
            </div>
          </div>
          <div class="col-12 col-sm-6 col-xl-3">
            <div class="sr-stat-card d-flex align-items-center gap-3">
              <div class="sr-stat-icon progress"><i class="bi bi-gear-wide-connected"></i></div>
              <div>
                <div class="text-muted small fw-semibold">In Progress</div>
                <div class="sr-stat-value"><?= $progressCount ?></div>
              </div>
            </div>
          </div>
          <div class="col-12 col-sm-6 col-xl-3">
            <div class="sr-stat-card d-flex align-items-center gap-3">
              <div class="sr-stat-icon resolved"><i class="bi bi-check-circle"></i></div>
              <div>
                <div class="text-muted small fw-semibold">Resolved &amp; Closed</div>
                <div class="sr-stat-value"><?= $resolvedCount ?></div>
              </div>
            </div>
          </div>
        </div>

        <!-- Search & Filter Controls -->
        <div class="bg-white border rounded-4 p-3 shadow-sm mb-4">
          <div class="row g-3 align-items-center">
            <div class="col-12 col-md-5 col-lg-4">
              <div class="input-group">
                <span class="input-group-text bg-white border-end-0 text-muted"><i class="bi bi-search"></i></span>
                <input type="text" class="form-control border-start-0 ps-0" id="srSearchInput" placeholder="Search by title, location, #ref..." onkeyup="applySrFilters()">
              </div>
            </div>

            <div class="col-12 col-md-7 col-lg-8 d-flex flex-wrap align-items-center justify-content-md-end gap-2">
              <div class="d-flex align-items-center gap-1 overflow-x-auto pb-1 pb-md-0">
                <button class="btn btn-sm btn-su-indigo sr-filter-tab px-3 text-nowrap" onclick="filterServiceRequestsByStatus('ALL', this)">All (<?= $totalCount ?>)</button>
                <button class="btn btn-sm btn-su-outline sr-filter-tab px-3 text-nowrap" onclick="filterServiceRequestsByStatus('SUBMITTED', this)">Submitted (<?= $pendingCount ?>)</button>
                <button class="btn btn-sm btn-su-outline sr-filter-tab px-3 text-nowrap" onclick="filterServiceRequestsByStatus('IN_PROGRESS', this)">In Progress (<?= $progressCount ?>)</button>
                <button class="btn btn-sm btn-su-outline sr-filter-tab px-3 text-nowrap" onclick="filterServiceRequestsByStatus('RESOLVED', this)">Resolved (<?= $resolvedCount ?>)</button>
              </div>

              <select class="form-select form-select-sm w-auto rounded-3 border-secondary-subtle" id="srPrioritySelect" onchange="applySrFilters()">
                <option value="ALL">All Priorities</option>
                <option value="Urgent">Urgent Priority</option>
                <option value="High">High Priority</option>
                <option value="Medium">Medium Priority</option>
                <option value="Low">Low Priority</option>
              </select>
            </div>
          </div>
        </div>

        <!-- Service Requests Cards Container -->
        <div class="d-flex flex-column gap-3 mb-4" id="srCardsContainer">
          <?php if (empty($sr_list)): ?>
            <div class="p-5 text-center bg-white border rounded-4 shadow-sm">
              <i class="bi bi-tools fs-1 text-muted"></i>
              <h5 class="fw-bold text-dark mt-2">No Service Requests Found</h5>
              <p class="text-secondary small">There are currently no reported service or maintenance issues in the system.</p>
              <?php if ($userRole !== 3): ?>
                <a href="report-issue.php" class="btn btn-su-indigo btn-sm mt-2">+ Report New Issue</a>
              <?php endif; ?>
            </div>
          <?php else: ?>
            <?php foreach ($sr_list as $sr): ?>
              <?php
                $ref = "SR-" . str_pad($sr['id'], 4, "0", STR_PAD_LEFT);
                $cDate = date("M j, Y · g:i A", strtotime($sr['created_at'] ?? 'now'));
                $stName = $sr['status_name'] ?? 'Submitted';
                $stId = intval($sr['status_id'] ?? 11);
                $priorityRaw = $sr['priority'] ?? 'Medium';
                $priority = htmlspecialchars($priorityRaw);
                $priorityClean = preg_replace('/[^a-zA-Z0-9]/', '', $priorityRaw);
                $reporterName = (isset($sr['fname']) && !empty($sr['fname'])) ? trim($sr['fname'] . ' ' . ($sr['lname'] ?? '')) : '';
                $reporterReg  = (isset($sr['reg_number']) && !empty($sr['reg_number'])) ? $sr['reg_number'] : '';
                
                $badgeClass = "su-badge-amber";
                $statusGroup = "IN_PROGRESS";
                
                if ($stId == 13 || $stId == 8 || $stId == 2) {
                    $badgeClass = "su-badge-green";
                    $statusGroup = "RESOLVED";
                } else if ($stId == 6 || $stId == 9 || $stId == 10) {
                    $badgeClass = ($stId == 6) ? "su-badge-purple" : "su-badge-red";
                    $statusGroup = "RESOLVED";
                } else if ($stId == 11) {
                    $badgeClass = "su-badge-purple";
                    $statusGroup = "SUBMITTED";
                }

                // Stepper state determination
                $step1 = "completed"; // Submitted is always completed
                $step2 = "";
                $step3 = "";
                $progressWidth = "0%";

                if ($stId == 12 || $stId == 5) {
                    $step2 = "active";
                    $progressWidth = "40%";
                } else if ($stId == 13 || $stId == 8 || $stId == 2 || $stId == 6) {
                    $step2 = "completed";
                    $step3 = "completed";
                    $progressWidth = "80%";
                } else if ($stId == 9) {
                    $step2 = "rejected";
                    $progressWidth = "40%";
                }
              ?>
              <div class="sr-card sr-card-item" data-status-group="<?= $statusGroup ?>" data-priority="<?= $priorityClean ?>">
                <div class="d-flex flex-column flex-lg-row align-items-lg-start justify-content-between gap-3">
                  
                  <div class="flex-grow-1">
                    <div class="d-flex align-items-center gap-2 flex-wrap mb-2">
                      <span class="sr-card-ref">#<?= htmlspecialchars($ref) ?></span>
                      <span class="su-badge <?= $badgeClass ?>">● <?= htmlspecialchars($stName) ?></span>
                      <span class="priority-pill priority-<?= $priorityClean ?>"><?= $priority ?></span>
                    </div>

                    <h5 class="fw-bold text-dark mb-2"><?= htmlspecialchars($sr['title']) ?></h5>

                    <div class="d-flex flex-wrap align-items-center gap-3 text-secondary small mb-3">
                      <span><i class="bi bi-clock me-1 text-primary"></i> <?= htmlspecialchars($cDate) ?></span>
                      <span><i class="bi bi-geo-alt me-1 text-danger"></i> <?= htmlspecialchars($sr['location']) ?></span>
                      <?php if ($userRole === 3 && !empty($reporterName)): ?>
                        <span class="requester-chip">
                          <i class="bi bi-person-circle text-primary"></i>
                          <span><strong>Requester:</strong> <?= htmlspecialchars($reporterName) ?> (<?= htmlspecialchars($reporterReg) ?>)</span>
                        </span>
                      <?php endif; ?>
                    </div>

                    <?php if (!empty($sr['description'])): ?>
                      <p class="text-secondary small mb-0 bg-light p-3 rounded-3 border-start border-3 border-primary">
                        <?= nl2br(htmlspecialchars($sr['description'])) ?>
                      </p>
                    <?php endif; ?>
                  </div>

                  <!-- Right Side Action / Admin Dropdown -->
                  <div class="d-flex flex-column align-items-start align-items-lg-end gap-2 flex-shrink-0">
                    <?php if ($userRole === 3): ?>
                      <div class="bg-light p-2 rounded-3 border d-flex align-items-center gap-1 w-100">
                        <select class="form-select form-select-sm border-0 bg-transparent fw-semibold" id="statusSelect_<?= $sr['id'] ?>" style="min-width: 140px;">
                          <?php foreach ($status_options as $optVal => $optLabel): ?>
                            <option value="<?= $optVal ?>" <?= ($stId === $optVal) ? 'selected' : '' ?>><?= $optLabel ?></option>
                          <?php endforeach; ?>
                        </select>
                        <button type="button" class="btn btn-sm btn-su-indigo text-nowrap" onclick="updateServiceRequestStatus(<?= $sr['id'] ?>, document.getElementById('statusSelect_<?= $sr['id'] ?>').value)" title="Save status change">
                          <i class="bi bi-check-lg me-1"></i> Update
                        </button>
                      </div>
                    <?php endif; ?>

                    <a href="request-detail.php?id=<?= $sr['id'] ?>" class="btn btn-sm btn-su-outline px-3 text-nowrap w-100 w-lg-auto text-center">
                      Tracker &amp; History <i class="bi bi-arrow-right ms-1"></i>
                    </a>
                  </div>

                </div>


              </div>
            <?php endforeach; ?>
          <?php endif; ?>

          <!-- Filtered Empty State -->
          <div id="srEmptyState" class="p-5 text-center bg-white border rounded-4 shadow-sm d-none">
            <i class="bi bi-search fs-1 text-muted"></i>
            <h5 class="fw-bold text-dark mt-2">No Matching Service Requests</h5>
            <p class="text-secondary small mb-3">No maintenance tickets matched your current search keyword or filter settings.</p>
            <button class="btn btn-sm btn-su-indigo" onclick="resetSrFilters()">
              <i class="bi bi-arrow-counterclockwise me-1"></i> Reset Filters
            </button>
          </div>
        </div>

<?php require_once "includes/footer.php"; ?>
