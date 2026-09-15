<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
require_once "includes/connection.php";

$pageTitle = "SmartUni Portal - Booking Confirmed";
$currentPage = "my-bookings";
require_once "includes/header.php";

$bookingId = isset($_GET['id']) ? intval($_GET['id']) : intval($_SESSION['last_booking_id'] ?? 0);
$booking = null;

if ($bookingId > 0) {
    $b_rs = Database::search("SELECT b.*, 
        f.name AS facility_name, f.location AS facility_location, f.category AS facility_category, 
        s.name AS status_name, 
        u.email AS user_email, u.fname, u.lname, u.reg_number 
        FROM `bookings` b 
        LEFT JOIN `facilities` f ON b.facility_id = f.id 
        LEFT JOIN `statuses` s ON b.status_id = s.id 
        LEFT JOIN `users` u ON b.user_id = u.id 
        WHERE b.id = '$bookingId'");
    if ($b_rs && $b_rs->num_rows > 0) {
        $booking = $b_rs->fetch_assoc();
    }
}

// Fallback to most recent booking in DB if specific ID not found
if (!$booking) {
    $userId = $_SESSION['user']['id'] ?? 1;
    $b_rs = Database::search("SELECT b.*, 
        f.name AS facility_name, f.location AS facility_location, f.category AS facility_category, 
        s.name AS status_name, 
        u.email AS user_email, u.fname, u.lname, u.reg_number 
        FROM `bookings` b 
        LEFT JOIN `facilities` f ON b.facility_id = f.id 
        LEFT JOIN `statuses` s ON b.status_id = s.id 
        LEFT JOIN `users` u ON b.user_id = u.id 
        WHERE b.user_id = '$userId' 
        ORDER BY b.id DESC LIMIT 1");
    if ($b_rs && $b_rs->num_rows > 0) {
        $booking = $b_rs->fetch_assoc();
    }
}

// Dynamic fields
$bookingRef        = $booking ? "BK-" . str_pad($booking['id'], 5, "0", STR_PAD_LEFT) : "#BK-8841";
$facilityName      = $booking['facility_name'] ?? 'Facility';
$facilityLocation  = $booking['facility_location'] ?? 'Main Campus';
$facilityCategory  = $booking['facility_category'] ?? 'Campus Facility';
$bookingDateStr    = !empty($booking['booking_date']) ? date("l, M j, Y", strtotime($booking['booking_date'])) : 'Oct 24, 2024';
$startTimeFormatted = !empty($booking['start_time']) ? date("g:i A", strtotime($booking['start_time'])) : '09:00 AM';
$endTimeFormatted   = !empty($booking['end_time']) ? date("g:i A", strtotime($booking['end_time'])) : '10:00 AM';
$statusName        = $booking['status_name'] ?? 'Pending';
$statusId          = intval($booking['status_id'] ?? 7);
$userEmail         = !empty($booking['user_email']) ? $booking['user_email'] : ($_SESSION['user']['email'] ?? 'student@smartuni.edu');
$regNumber         = !empty($booking['reg_number']) ? $booking['reg_number'] : '202488';
$purpose           = !empty($booking['purpose']) ? $booking['purpose'] : 'Academic Study';
$notes             = !empty($booking['notes']) ? $booking['notes'] : '';
$createdAtStr      = !empty($booking['created_at']) ? date("M j, Y · g:i A", strtotime($booking['created_at'])) : date("M j, Y · g:i A");

// Badge color logic based on status_id
$badgeClass = "su-badge-amber";
if ($statusId == 8) {
    $badgeClass = "su-badge-green";
} else if ($statusId == 9 || $statusId == 10) {
    $badgeClass = "su-badge-red";
}
?>

        <div class="text-center max-w-600 w-100 py-4 mx-auto">
          
          <div class="mx-auto mb-3 bg-success-subtle text-success rounded-circle d-flex align-items-center justify-content-center" style="width: 60px; height: 60px;">
            <i class="bi bi-check-lg fs-1"></i>
          </div>

          <h2 class="fw-bold text-dark mb-1">Booking Success</h2>
          <p class="text-secondary small mb-4">Ref #<strong><?= htmlspecialchars($bookingRef) ?></strong></p>

          <div class="bg-white border rounded-4 p-4 text-start shadow-sm mb-4">
            
            <div class="d-flex justify-content-between align-items-center mb-3 pb-2 border-bottom">
              <span class="text-muted fw-bold small text-uppercase">Reservation Summary</span>
              <span class="su-badge <?= $badgeClass ?>">● <?= htmlspecialchars($statusName) ?></span>
            </div>

            <div class="d-flex flex-column gap-3">
              <div class="d-flex justify-content-between">
                <span class="text-secondary small"><i class="bi bi-building me-2"></i>Venue</span>
                <span class="fw-bold text-dark small"><?= htmlspecialchars($facilityName) ?></span>
              </div>
              <div class="d-flex justify-content-between">
                <span class="text-secondary small"><i class="bi bi-geo-alt me-2"></i>Location</span>
                <span class="text-dark small"><?= htmlspecialchars($facilityLocation) ?> (<?= htmlspecialchars($facilityCategory) ?>)</span>
              </div>
              <div class="d-flex justify-content-between">
                <span class="text-secondary small"><i class="bi bi-clock me-2"></i>Date & Time</span>
                <span class="text-dark small fw-semibold"><?= htmlspecialchars($bookingDateStr) ?> · <?= htmlspecialchars($startTimeFormatted) ?> – <?= htmlspecialchars($endTimeFormatted) ?></span>
              </div>
              <div class="d-flex justify-content-between">
                <span class="text-secondary small"><i class="bi bi-journal-text me-2"></i>Purpose</span>
                <span class="text-dark small"><?= htmlspecialchars($purpose) ?></span>
              </div>
              <div class="d-flex justify-content-between">
                <span class="text-secondary small"><i class="bi bi-people me-2"></i>Attendees</span>
                <span class="text-dark small fw-semibold"><?= htmlspecialchars($booking['capacity'] ?? 1) ?> persons</span>
              </div>
              <?php if (!empty($notes)): ?>
              <div class="d-flex justify-content-between">
                <span class="text-secondary small"><i class="bi bi-sticky me-2"></i>Notes</span>
                <span class="text-dark small bg-light px-2 py-1 rounded border"><?= htmlspecialchars($notes) ?></span>
              </div>
              <?php endif; ?>
      
              <div class="d-flex justify-content-between">
                <span class="text-secondary small"><i class="bi bi-calendar-check me-2"></i>Created At</span>
                <span class="text-muted small"><?= htmlspecialchars($createdAtStr) ?></span>
              </div>
            </div>

    
          </div>

          <div class="d-flex flex-column flex-sm-row justify-content-center align-items-center gap-3 mb-3">
            <a href="my-bookings.php" class="btn btn-su-indigo px-4 py-2">
              <span>View My Bookings</span>
              <i class="bi bi-arrow-right ms-1"></i>
            </a>
            <a href="dashboard.php" class="btn btn-su-outline px-4 py-2">Return to Dashboard</a>
          </div>



        </div>

<?php require_once "includes/footer.php"; ?>
