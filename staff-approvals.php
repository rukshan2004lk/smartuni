<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
require_once "includes/connection.php";

$pageTitle = "SmartUni Staff Portal - Booking Approvals";
$currentPage = "staff-approvals";
require_once "includes/header.php";

// Fetch all bookings with facility, user, and status details
$bookings_rs = Database::search("SELECT b.*, f.name AS facility_name, f.location AS facility_location, u.fname, u.lname, u.email, u.reg_number, s.name AS status_name 
    FROM `bookings` b 
    LEFT JOIN `facilities` f ON b.facility_id = f.id 
    LEFT JOIN `users` u ON b.user_id = u.id 
    LEFT JOIN `statuses` s ON b.status_id = s.id 
    ORDER BY b.id DESC");

$bookings_list = [];
$pendingCount = 0;
$confirmedCount = 0;
$rejectedCount = 0;

if ($bookings_rs && $bookings_rs->num_rows > 0) {
    while ($row = $bookings_rs->fetch_assoc()) {
        $bookings_list[] = $row;
        $stId = intval($row['status_id']);
        if ($stId === 7 || $stId === 1 || $stId === 11) {
            $pendingCount++;
        } else if ($stId === 8 || $stId === 2 || $stId === 13) {
            $confirmedCount++;
        } else if ($stId === 9 || $stId === 10) {
            $rejectedCount++;
        }
    }
}
?>

        <div class="d-flex flex-column flex-md-row align-items-md-center justify-content-between gap-3 mb-4">
          <div>
            <span class="text-muted small fw-semibold text-uppercase">STAFF OPERATIONS · Space Reservations</span>
            <h1 class="fw-bold fs-3 mb-1">Booking Approvals</h1>
            <p class="text-secondary small mb-0">Review and manage facility booking requests submitted by campus users.</p>
          </div>
          <div class="d-flex align-items-center gap-2">
            <span class="su-badge su-badge-amber">● <?= $pendingCount ?> Pending</span>
            <span class="su-badge su-badge-green">● <?= $confirmedCount ?> Confirmed</span>
            <span class="su-badge su-badge-red">● <?= $rejectedCount ?> Rejected</span>
          </div>
        </div>

        <div class="d-flex flex-column flex-md-row align-items-md-center justify-content-between gap-3 mb-4">
          <ul class="nav nav-tabs border-bottom-0">
            <li class="nav-item">
              <button class="nav-link active fw-bold text-primary approval-tab-btn" onclick="filterApprovalRows('All', this)">All (<?= count($bookings_list) ?>)</button>
            </li>
            <li class="nav-item">
              <button class="nav-link text-secondary approval-tab-btn" onclick="filterApprovalRows('Pending', this)">Pending (<?= $pendingCount ?>)</button>
            </li>
            <li class="nav-item">
              <button class="nav-link text-secondary approval-tab-btn" onclick="filterApprovalRows('Confirmed', this)">Confirmed (<?= $confirmedCount ?>)</button>
            </li>
            <li class="nav-item">
              <button class="nav-link text-secondary approval-tab-btn" onclick="filterApprovalRows('Rejected', this)">Rejected (<?= $rejectedCount ?>)</button>
            </li>
          </ul>

          <button class="btn btn-su-outline text-nowrap" onclick="exportApprovalsListPDF()">
            <i class="bi bi-file-earmark-pdf text-danger me-1"></i> Export PDF
          </button>
        </div>

        <div class="su-table-card mb-4">
          <div class="table-responsive">
            <table class="su-table">
              <thead>
                <tr>
                  <th>REF #</th>
                  <th>FACILITY</th>
                  <th>REQUESTER</th>
                  <th>DATE &amp; TIME</th>
                  <th>PURPOSE</th>
                  <th>STATUS</th>
                  <th class="text-end">ACTIONS</th>
                </tr>
              </thead>
              <tbody>
                <?php if (empty($bookings_list)): ?>
                  <tr>
                    <td colspan="7" class="text-center py-5 text-muted">
                      <i class="bi bi-calendar-check fs-2 d-block mb-2 text-secondary"></i>
                      No booking requests found in database.
                    </td>
                  </tr>
                <?php else: ?>
                  <?php foreach ($bookings_list as $bk): ?>
                    <?php
                      $bkId = $bk['id'];
                      $ref = "BK-" . str_pad($bkId, 4, "0", STR_PAD_LEFT);
                      $facilityName = $bk['facility_name'] ?? 'Facility #' . $bk['facility_id'];
                      $location = $bk['facility_location'] ?? '';
                      $requester = trim(($bk['fname'] ?? '') . ' ' . ($bk['lname'] ?? ''));
                      if (empty($requester)) {
                          $requester = "User #" . $bk['user_id'];
                      }
                      $email = $bk['email'] ?? '';
                      $regNum = $bk['reg_number'] ?? '';

                      $bDate = date("M j, Y", strtotime($bk['booking_date']));
                      $bTime = date("h:i A", strtotime($bk['start_time'])) . ' – ' . date("h:i A", strtotime($bk['end_time']));
                      $purpose = $bk['purpose'] ?? 'General Booking';
                      $notes = $bk['notes'] ?? '';
                      $createdAt = date("M j, Y, g:i A", strtotime($bk['created_at']));

                      $stId = intval($bk['status_id']);
                      $stName = $bk['status_name'] ?? 'Pending';
                      
                      $statusCategory = "Pending";
                      $badgeClass = "su-badge-amber";

                      if ($stId === 8 || $stId === 2 || $stId === 13) {
                          $statusCategory = "Confirmed";
                          $badgeClass = "su-badge-green";
                      } else if ($stId === 9 || $stId === 10) {
                          $statusCategory = "Rejected";
                          $badgeClass = "su-badge-red";
                      } else {
                          $statusCategory = "Pending";
                          $badgeClass = "su-badge-amber";
                      }

                      // JS payload for details view
                      $jsPayload = json_encode([
                        'id'           => $bkId,
                        'ref'          => $ref,
                        'facilityName' => $facilityName,
                        'location'     => $location,
                        'requester'    => $requester,
                        'email'        => $email,
                        'regNum'       => $regNum,
                        'date'         => $bDate,
                        'time'         => $bTime,
                        'capacity'     => intval($bk['capacity'] ?? 1),
                        'purpose'      => $purpose,
                        'notes'        => $notes,
                        'statusId'     => $stId,
                        'statusName'   => $stName,
                        'badgeClass'   => $badgeClass,
                        'createdAt'    => $createdAt
                      ], JSON_HEX_APOS | JSON_HEX_QUOT);
                    ?>
                    <tr class="approval-row cursor-pointer" data-status="<?= $statusCategory ?>" onclick="showBookingDetails(<?= htmlspecialchars($jsPayload, ENT_QUOTES) ?>)">
                      <td>
                        <span class="fw-bold text-dark">#<?= htmlspecialchars($ref) ?></span>
                      </td>
                      <td>
                        <div class="fw-bold text-dark"><?= htmlspecialchars($facilityName) ?></div>
                        <?php if (!empty($location)): ?>
                          <div class="text-muted small"><i class="bi bi-geo-alt me-1"></i><?= htmlspecialchars($location) ?></div>
                        <?php endif; ?>
                      </td>
                      <td>
                        <div class="fw-semibold text-dark"><?= htmlspecialchars($requester) ?></div>
                        <?php if (!empty($regNum)): ?>
                          <div class="text-muted small">ID: <?= htmlspecialchars($regNum) ?></div>
                        <?php endif; ?>
                      </td>
                      <td>
                        <div class="fw-bold text-dark"><?= htmlspecialchars($bDate) ?></div>
                        <div class="text-muted small"><?= htmlspecialchars($bTime) ?></div>
                      </td>
                      <td>
                        <div class="text-dark small fw-medium"><?= htmlspecialchars($purpose) ?></div>
                        <div class="text-secondary small"><i class="bi bi-people me-1"></i><?= intval($bk['capacity'] ?? 1) ?> Attendees</div>
                        <?php if (!empty($notes)): ?>
                          <div class="text-muted small">Note: <?= htmlspecialchars($notes) ?></div>
                        <?php endif; ?>
                      </td>
                      <td>
                        <span class="su-badge <?= $badgeClass ?>">● <?= htmlspecialchars($stName) ?></span>
                      </td>
                      <td class="text-end" onclick="event.stopPropagation()">
                        <button class="btn btn-sm btn-outline-secondary rounded-3 px-2 me-1" title="View Full Details" onclick="showBookingDetails(<?= htmlspecialchars($jsPayload, ENT_QUOTES) ?>)">
                          <i class="bi bi-eye"></i> Details
                        </button>
                        <?php if ($stId !== 8): ?>
                          <button class="btn btn-sm btn-success rounded-3 px-3 me-1" onclick="updateBookingStatus(<?= $bkId ?>, 'approve')">
                            <i class="bi bi-check-lg me-1"></i> Approve
                          </button>
                        <?php endif; ?>
                        <?php if ($stId !== 9): ?>
                          <button class="btn btn-sm btn-outline-danger rounded-3 px-3" onclick="updateBookingStatus(<?= $bkId ?>, 'reject')">
                            <i class="bi bi-x-lg me-1"></i> Reject
                          </button>
                        <?php endif; ?>
                      </td>
                    </tr>
                  <?php endforeach; ?>
                <?php endif; ?>
              </tbody>
            </table>
          </div>
        </div>

        <!-- Full Request Details Modal -->
        <div class="modal fade" id="bookingDetailModal" tabindex="-1" aria-labelledby="bookingDetailModalLabel" aria-hidden="true">
          <div class="modal-dialog modal-dialog-centered modal-lg">
            <div class="modal-content border-0 shadow rounded-4">
              <div class="modal-header border-bottom-0 pb-0">
                <div class="d-flex align-items-center gap-2">
                  <h5 class="modal-title fw-bold text-dark mb-0" id="bookingDetailModalLabel">Request Details</h5>
                  <span class="fw-bold text-primary" id="modalRefNo"></span>
                  <span id="modalStatusBadge"></span>
                </div>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
              </div>

              <div class="modal-body p-4">
                <div class="bg-light p-3 rounded-3 mb-4">
                  <div class="row g-3">
                    <div class="col-12 col-md-6">
                      <span class="text-uppercase text-muted small fw-bold">Facility Requested</span>
                      <h6 class="fw-bold text-dark mb-1" id="modalFacilityName"></h6>
                      <div class="text-secondary small"><i class="bi bi-geo-alt me-1"></i><span id="modalFacilityLocation"></span></div>
                    </div>
                    <div class="col-12 col-md-6">
                      <span class="text-uppercase text-muted small fw-bold">Requester Profile</span>
                      <h6 class="fw-bold text-dark mb-1" id="modalRequesterName"></h6>
                      <div class="text-secondary small"><i class="bi bi-envelope me-1"></i><span id="modalRequesterEmail"></span> · ID: <span id="modalRequesterReg"></span></div>
                    </div>
                  </div>
                </div>

                <div class="row g-3 mb-3">
                  <div class="col-12 col-md-5">
                    <label class="form-label text-muted small fw-semibold">Scheduled Date &amp; Time</label>
                    <div class="p-3 border rounded-3 bg-white fw-bold text-dark" id="modalDateTime"></div>
                  </div>
                  <div class="col-12 col-md-3">
                    <label class="form-label text-muted small fw-semibold">Attendees</label>
                    <div class="p-3 border rounded-3 bg-white text-dark small fw-bold" id="modalCapacity"></div>
                  </div>
                  <div class="col-12 col-md-4">
                    <label class="form-label text-muted small fw-semibold">Submission Date</label>
                    <div class="p-3 border rounded-3 bg-white text-secondary small" id="modalCreatedAt"></div>
                  </div>
                </div>

                <div class="mb-3">
                  <label class="form-label text-muted small fw-semibold">Purpose of Booking</label>
                  <div class="p-3 border rounded-3 bg-white text-dark small" id="modalPurpose"></div>
                </div>

                <div class="mb-3">
                  <label class="form-label text-muted small fw-semibold">Notes / Additional Remarks</label>
                  <div class="p-3 border rounded-3 bg-white text-secondary small" id="modalNotes"></div>
                </div>
              </div>

              <div class="modal-footer border-top-0 pt-0 px-4 pb-4 d-flex justify-content-between">
                <button type="button" class="btn btn-outline-primary rounded-3" onclick="exportSingleBookingPDF()">
                  <i class="bi bi-file-earmark-pdf text-danger me-1"></i> Export Slip to PDF
                </button>
                <div class="d-flex align-items-center gap-2">
                  <div id="modalActionsContainer"></div>
                  <button type="button" class="btn btn-secondary rounded-3 px-3" data-bs-dismiss="modal">Close</button>
                </div>
              </div>
            </div>
          </div>
        </div>

<?php require_once "includes/footer.php"; ?>
