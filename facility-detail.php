<?php
require_once "includes/connection.php";

$userRole = intval($_SESSION['user']['role_id'] ?? 1);
if ($userRole === 1) { // Student
    echo "<script>window.location.href = 'dashboard.php';</script>";
    exit();
}

$id = intval($_GET['id'] ?? 0);
$facility = null;

if ($id > 0) {
    $rs = Database::search("SELECT * FROM `facilities` WHERE `id` = '" . intval($id) . "'");
    if ($rs && $rs->num_rows > 0) {
        $facility = $rs->fetch_assoc();
    }
}

// Fallback to first facility if ID invalid or not found
if (!$facility) {
    $rs = Database::search("SELECT * FROM `facilities` ORDER BY `id` ASC LIMIT 1");
    if ($rs && $rs->num_rows > 0) {
        $facility = $rs->fetch_assoc();
    }
}

if (!$facility) {
    header("Location: facilities.php");
    exit();
}

$pageTitle = "SmartUni Portal - Facility Detail: " . $facility['name'];
$currentPage = "facilities";

$extraCss = '
  <style>
    .slot-table {
      width: 100%;
      border-collapse: separate;
      border-spacing: 6px;
    }
    .slot-cell {
      height: 38px;
      border-radius: 6px;
      border: 1px solid #e2e8f0;
      background-color: #ffffff;
      font-size: 11px;
      display: flex;
      align-items: center;
      justify-content: center;
      cursor: pointer;
      transition: all 0.2s ease;
    }
    .slot-cell:hover {
      border-color: #4f46e5;
      background-color: #eef2ff;
    }
    .slot-cell.reserved {
      background-color: #fef2f2;
      border-color: #fecaca;
      color: #ef4444;
      cursor: not-allowed;
    }
    .slot-cell.selected {
      background-color: #4f46e5;
      color: #ffffff;
      border-color: #4f46e5;
      font-weight: 600;
    }
  </style>
';
require_once "includes/header.php";

$imgUrl = !empty($facility['image']) ? $facility['image'] : 'https://images.unsplash.com/photo-1541829070764-84a7d30dd3f3?q=80&w=1000&auto=format&fit=crop';
$equipmentItems = !empty($facility['equipment']) ? array_map('trim', explode(',', $facility['equipment'])) : ['High-Speed Wi-Fi', 'Power Outlets', 'Climate Control'];
?>

        <a href="facilities.php" class="text-secondary small fw-semibold text-decoration-none d-inline-flex align-items-center gap-1 mb-3">
          <i class="bi bi-arrow-left"></i> Back to Facilities
        </a>

        <div class="d-flex flex-column flex-md-row align-items-md-center justify-content-between gap-2 mb-4">
          <div>
            <div class="d-flex align-items-center gap-2 mb-1">
              <h1 class="fw-bold fs-3 mb-0"><?php echo htmlspecialchars($facility['name']); ?></h1>
         
            </div>
            <p class="text-secondary small mb-0"><?php echo htmlspecialchars($facility['category']); ?> · <?php echo htmlspecialchars($facility['location']); ?></p>
          </div>

        </div>

        <div class="row g-4 mb-4">
          
          <div class="col-12 col-lg-7">
            <div class="position-relative rounded-4 overflow-hidden shadow-sm" style="height: 340px;">
              <img src="<?php echo htmlspecialchars($imgUrl); ?>" alt="<?php echo htmlspecialchars($facility['name']); ?>" class="w-100 h-100 object-fit-cover" />
              <div class="position-absolute bottom-0 start-0 m-3 p-2 bg-dark bg-opacity-75 text-white rounded-pill small d-flex align-items-center gap-2 px-3">
                <i class="bi bi-building-fill text-info"></i> <?php echo htmlspecialchars($facility['category']); ?>
              </div>
            </div>
          </div>

          <div class="col-12 col-lg-5">
            <div class="bg-white border rounded-4 p-4 h-100 shadow-sm d-flex flex-column justify-content-between">
              <div>
                <span class="text-muted fw-bold small text-uppercase tracking-wider">Facility Parameters</span>
                <div class="d-flex flex-column gap-3 mt-3">
                  <div class="d-flex align-items-center justify-content-between border-bottom pb-2">
                    <span class="text-secondary small"><i class="bi bi-people me-2"></i>Capacity</span>
                    <span class="fw-bold text-dark small"><?php echo htmlspecialchars($facility['capacity']); ?> persons max</span>
                  </div>
                  <div class="d-flex align-items-center justify-content-between border-bottom pb-2">
                    <span class="text-secondary small"><i class="bi bi-clock me-2"></i>Operating Hours</span>
                    <?php
                      $sTime = !empty($facility['start_time']) ? $facility['start_time'] : '08:00';
                      $eTime = !empty($facility['end_time']) ? $facility['end_time'] : '22:00';
                      $opHoursText = date("g:i A", strtotime($sTime)) . " – " . date("g:i A", strtotime($eTime)) . "";
                    ?>
                    <span class="fw-bold text-dark small"><?php echo htmlspecialchars($opHoursText); ?></span>
                  </div>
                  <div class="d-flex align-items-center justify-content-between border-bottom pb-2">
                    <span class="text-secondary small"><i class="bi bi-geo-alt me-2"></i>Location</span>
                    <span class="fw-bold text-dark small"><?php echo htmlspecialchars($facility['location']); ?></span>
                  </div>

                </div>
              </div>

              <?php if (!empty($facility['description'])): ?>
                <div class="mt-3">
                  <span class="text-muted fw-bold small text-uppercase tracking-wider">Overview</span>
                  <p class="text-secondary small mt-1 mb-0"><?php echo htmlspecialchars($facility['description']); ?></p>
                </div>
              <?php endif; ?>

              <div class="mt-3">
                <span class="text-muted fw-bold small text-uppercase tracking-wider">Included Equipment &amp; Amenities</span>
                <div class="d-flex flex-wrap gap-2 mt-2">
                  <?php foreach ($equipmentItems as $eq): ?>
                    <span class="badge bg-light text-dark border"><i class="bi bi-check2-square text-primary me-1"></i> <?php echo htmlspecialchars($eq); ?></span>
                  <?php endforeach; ?>
                </div>
              </div>
            </div>
          </div>

        </div>

  <?php if ($userRole === 2): ?>
    <div class="p-3 bg-white border rounded-4 shadow-sm d-flex justify-content-center align-items-center" id="selectedSlotBar">
      <a href="booking-confirm.php?facility_id=<?php echo $facility['id']; ?>" class="btn btn-su-indigo px-4 py-2">
        <span>Continue to Booking</span>
        <i class="bi bi-arrow-right ms-1"></i>
      </a>
    </div>
  <?php else: ?>
    <div class="p-3 bg-light border rounded-4 shadow-sm text-center text-muted small">
      <i class="bi bi-info-circle me-1"></i> Facility reservation requests are reserved for Academic Staff &amp; Lecturers.
    </div>
  <?php endif; ?>

<?php require_once "includes/footer.php"; ?>
