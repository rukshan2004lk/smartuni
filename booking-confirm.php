<?php
require_once "includes/connection.php";

$pageTitle = "SmartUni Portal - Confirm Booking Details";
$currentPage = "facilities";
require_once "includes/header.php";

$facility_id = isset($_GET['facility_id']) ? intval($_GET['facility_id']) : 0;
$facility = null;

if ($facility_id > 0) {
    $fac_rs = Database::search("SELECT * FROM `facilities` WHERE `id` = '$facility_id'");
    if ($fac_rs && $fac_rs->num_rows > 0) {
        $facility = $fac_rs->fetch_assoc();
    }
}

if (!$facility) {
    $fac_rs = Database::search("SELECT * FROM `facilities` ORDER BY `id` ASC LIMIT 1");
    if ($fac_rs && $fac_rs->num_rows > 0) {
        $facility = $fac_rs->fetch_assoc();
    }
}

$facilityId   = $facility['id'] ?? 0;
$facilityName = $facility['name'] ?? 'Facility';
$location     = $facility['location'] ?? 'Main Campus';
$capacity     = intval($facility['capacity'] ?? 10);

$facStartTime = !empty($facility['start_time']) ? trim($facility['start_time']) : '08:00';
$facEndTime   = !empty($facility['end_time']) ? trim($facility['end_time']) : '22:00';

$displayStart = date("g:i A", strtotime($facStartTime));
$displayEnd   = date("g:i A", strtotime($facEndTime));
$opHoursDisplay = "$displayStart – $displayEnd ($facStartTime – $facEndTime)";
$defaultEndTime = date('H:i', strtotime($facStartTime . ' + 1 hour'));
?>

        <div class="max-w-700 mx-auto">
          
          <a href="facility-detail.php?id=<?= $facilityId ?>" class="text-secondary small fw-semibold text-decoration-none d-inline-flex align-items-center gap-1 mb-3">
            <i class="bi bi-arrow-left"></i> Back to <?= htmlspecialchars($facilityName) ?>
          </a>

          <h1 class="fw-bold fs-3 mb-4">Confirm Booking Details</h1>

          <!-- Summary Card -->
          <div class="p-3 bg-light border rounded-3 d-flex align-items-center gap-3 mb-4">
            <div class="bg-white border p-3 rounded-3 text-primary"><i class="bi bi-building fs-4"></i></div>
            <div class="w-100">
              <div class="fw-bold text-dark fs-6"><?= htmlspecialchars($facilityName) ?> · <?= htmlspecialchars($location) ?></div>
              <div class="text-secondary small d-flex align-items-center gap-3 mt-1 flex-wrap">
                <span><i class="bi bi-clock me-1"></i> Operating Hours: <strong><?= htmlspecialchars($displayStart) ?> – <?= htmlspecialchars($displayEnd) ?></strong></span>
                <span><i class="bi bi-people me-1"></i> Capacity: <strong><?= $capacity ?> persons</strong></span>
              </div>
            </div>
          </div>

          <form id="bookingConfirmForm" onsubmit="event.preventDefault(); submitBooking();">
            <input type="hidden" id="facilityId" value="<?= $facilityId ?>">
            <input type="hidden" id="facilityCapacity" value="<?= $capacity ?>">
            <input type="hidden" id="facilityStartTime" value="<?= htmlspecialchars($facStartTime) ?>">
            <input type="hidden" id="facilityEndTime" value="<?= htmlspecialchars($facEndTime) ?>">
            <input type="hidden" id="facilityOpHoursDisplay" value="<?= htmlspecialchars($opHoursDisplay) ?>">

            <!-- Date and Time Inputs -->
            <div class="row g-3 mb-4">
              <div class="col-md-4">
                <label class="su-label">Booking Date</label>
                <input type="date" class="form-control su-input" id="bookingDate" min="<?= date('Y-m-d') ?>" value="<?= date('Y-m-d') ?>" required>
              </div>
              <div class="col-md-4">
                <label class="su-label">Start Time</label>
                <input type="time" class="form-control su-input" id="startTime" value="<?= htmlspecialchars($facStartTime) ?>" required>
              </div>
              <div class="col-md-4">
                <label class="su-label">End Time</label>
                <input type="time" class="form-control su-input" id="endTime" value="<?= htmlspecialchars($defaultEndTime) ?>" required>
              </div>
              <div class="col-12">
                <div class="p-2 bg-indigo-subtle border border-indigo-subtle rounded-3 small text-indigo d-flex align-items-center gap-2" style="background-color: #eef2ff; color: #4338ca; border-color: #c7d2fe;">
                  <i class="bi bi-info-circle-fill"></i>
                  <span>Operating Hours for this facility: <strong><?= htmlspecialchars($opHoursDisplay) ?></strong>. Bookings must fall within these hours.</span>
                </div>
              </div>
            </div>

            <!-- Purpose of Booking -->
            <div class="mb-4">
              <label class="su-label">Purpose of Booking</label>
              <select class="form-select su-input" id="purpose">
                <option value="Group Study / Project Prep" selected>Group Study / Project Prep</option>
                <option value="Individual Focused Work">Individual Focused Work</option>
                <option value="Club / Society Planning">Club / Society Planning</option>
                <option value="TA / Faculty Office Hours">TA / Faculty Office Hours</option>
                <option value="Lab Work / Presentation Practice">Lab Work / Presentation Practice</option>
              </select>
            </div>

            <!-- Number of Attendees (Spin buttons removed via CSS) -->
            <div class="mb-4">
              <div class="d-flex justify-content-between align-items-center mb-1">
                <label class="su-label mb-0">Number of Attendees</label>
                <span class="text-muted small" style="font-size: 11px;">Max <?= $capacity ?> persons allowed</span>
              </div>
              <input type="number" class="form-control su-input" id="attendees" min="1" max="<?= $capacity ?>" value="1" placeholder="Enter number of attendees" required>
            </div>

            <!-- Notes Column Input -->
            <div class="mb-4">
              <div class="d-flex justify-content-between align-items-center mb-1">
                <label class="su-label mb-0">Special Notes or Requirements</label>
                <span class="text-muted small" style="font-size: 11px;">Saved to booking notes</span>
              </div>
              <textarea class="form-control su-input" id="notes" rows="3" placeholder="Any specific setup requirements or accessibility accommodations..."></textarea>
            </div>

            <button type="submit" class="btn btn-su-indigo w-100 py-3 mb-3 fs-6">
              <span>Submit Booking Request</span>
            </button>

            <div class="text-center">
              <a href="facilities.php" class="text-secondary small text-decoration-none">Cancel</a>
            </div>

          </form>

        </div>

<?php require_once "includes/footer.php"; ?>

