<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
require_once "includes/connection.php";

$pageTitle = "SmartUni Portal - My Bookings";
$currentPage = "my-bookings";
require_once "includes/header.php";

$userId = $_SESSION['user']['id'] ?? 1;

$bookings_rs = Database::search("SELECT b.*, f.name AS facility_name, f.location AS facility_location, s.name AS status_name 
    FROM `bookings` b 
    LEFT JOIN `facilities` f ON b.facility_id = f.id 
    LEFT JOIN `statuses` s ON b.status_id = s.id 
    WHERE b.user_id = '$userId' 
    ORDER BY b.id DESC");

$bookings_list = [];
if ($bookings_rs && $bookings_rs->num_rows > 0) {
    while ($row = $bookings_rs->fetch_assoc()) {
        $bookings_list[] = $row;
    }
}
?>

        <div class="d-flex flex-column flex-md-row align-items-md-center justify-content-between gap-3 mb-4">
          <div>
            <span class="text-muted small fw-semibold text-uppercase">PORTAL OVERVIEW · Academic Term 2025</span>
            <h1 class="fw-bold fs-3 mb-0">My Bookings</h1>
          </div>
          <div class="d-flex gap-2">
            <a href="facilities.php" class="btn btn-su-indigo d-inline-flex align-items-center gap-1">+ New Booking</a>
          </div>
        </div>

        <div class="d-flex flex-column gap-3 mb-4">
          <?php if (empty($bookings_list)): ?>
            <div class="p-5 text-center bg-white border rounded-4 shadow-sm">
              <i class="bi bi-calendar-x fs-1 text-muted"></i>
              <h5 class="fw-bold text-dark mt-2">No Bookings Found</h5>
              <p class="text-secondary small">You haven't made any facility bookings yet.</p>
              <a href="facilities.php" class="btn btn-su-indigo btn-sm mt-2">Browse Facilities</a>
            </div>
          <?php else: ?>
            <?php foreach ($bookings_list as $b): ?>
              <?php
                $ref = "BK-" . str_pad($b['id'], 5, "0", STR_PAD_LEFT);
                $bDate = date("l, M j, Y", strtotime($b['booking_date']));
                $sTime = date("g:i A", strtotime($b['start_time']));
                $eTime = date("g:i A", strtotime($b['end_time']));
                $stName = $b['status_name'] ?? 'Pending';
                $stId = intval($b['status_id']);
                
                $badgeClass = "su-badge-amber";
                if ($stId == 8) {
                    $badgeClass = "su-badge-green";
                } else if ($stId == 9 || $stId == 10) {
                    $badgeClass = "su-badge-red";
                }
              ?>
              <div class="p-3 bg-white border rounded-3 shadow-sm d-flex flex-column flex-md-row align-items-md-center justify-content-between gap-3">
                <div class="d-flex align-items-center gap-3">
                  <div class="bg-indigo-subtle p-3 rounded-3 text-primary"><i class="bi bi-building fs-4"></i></div>
                  <div>
                    <div class="d-flex align-items-center gap-2">
                      <h6 class="fw-bold text-dark mb-0"><?= htmlspecialchars($b['facility_name']) ?></h6>
                      <span class="su-badge <?= $badgeClass ?>">● <?= htmlspecialchars($stName) ?></span>
                    </div>
                    <div class="text-secondary small mt-1">
                      <i class="bi bi-clock me-1"></i> <?= htmlspecialchars($bDate) ?> · <?= htmlspecialchars($sTime) ?> – <?= htmlspecialchars($eTime) ?>
                      <span class="ms-2">📍 <?= htmlspecialchars($b['facility_location']) ?></span>
                      <span class="ms-2">👥 <?= htmlspecialchars($b['capacity'] ?? 1) ?> Attendees</span>
                      <span class="ms-2 text-muted">Ref: #<?= htmlspecialchars($ref) ?></span>
                    </div>
                  </div>
                </div>
                <a href="booking-success.php?id=<?= $b['id'] ?>" class="btn btn-sm btn-su-outline text-nowrap">View Details <i class="bi bi-arrow-right ms-1"></i></a>
              </div>
            <?php endforeach; ?>
          <?php endif; ?>
        </div>

        <div class="d-flex align-items-center justify-content-between text-muted small pt-2 border-top">
          <span>Showing <?= count($bookings_list) ?> bookings</span>
        </div>

<?php require_once "includes/footer.php"; ?>
