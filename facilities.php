<?php
require_once "includes/connection.php";

$pageTitle = "SmartUni Portal - Campus Facilities";
$currentPage = "facilities";
require_once "includes/header.php";

$userRole = intval($_SESSION['user']['role_id'] ?? 1);
if ($userRole === 1) { // Student
    echo "<script>window.location.href = 'dashboard.php';</script>";
    exit();
}

$searchQuery = trim($_GET['search'] ?? '');
$categoryFilter = trim($_GET['category'] ?? 'All Types');

// Build SQL query
$sql = "SELECT * FROM `facilities` WHERE 1=1";
if (!empty($categoryFilter) && $categoryFilter !== 'All Types') {
    $sql .= " AND `category` = '" . addslashes($categoryFilter) . "'";
}
if (!empty($searchQuery)) {
    $sql .= " AND (`name` LIKE '%" . addslashes($searchQuery) . "%' OR `location` LIKE '%" . addslashes($searchQuery) . "%' OR `category` LIKE '%" . addslashes($searchQuery) . "%')";
}
$sql .= " ORDER BY `id` DESC";

$facilities_rs = Database::search($sql);
$facilities_list = [];
if ($facilities_rs) {
    while ($row = $facilities_rs->fetch_assoc()) {
        $facilities_list[] = $row;
    }
}

$categories = ['All Types', 'Study Pods', 'Computer Labs', 'Meeting Rooms', 'Lecture Halls', 'Studios'];
?>

        <div class="d-flex flex-column flex-md-row align-items-md-center justify-content-between gap-3 mb-4">
          <form action="facilities.php" method="GET" class="position-relative flex-grow-1 max-w-600">
            <i class="bi bi-search position-absolute top-50 start-0 translate-middle-y ms-3 text-muted"></i>
            <input type="text" name="search" class="form-control su-input ps-5" placeholder="Search study pods, lecture halls, lab equipment..." value="<?php echo htmlspecialchars($searchQuery); ?>" />
            <?php if (!empty($categoryFilter) && $categoryFilter !== 'All Types'): ?>
              <input type="hidden" name="category" value="<?php echo htmlspecialchars($categoryFilter); ?>" />
            <?php endif; ?>
          </form>
          
          <?php if ($userRole === 3): ?>
            <div class="d-flex align-items-center gap-2">
              <button class="btn btn-su-indigo d-inline-flex align-items-center gap-1" data-bs-toggle="modal" data-bs-target="#addFacilityModal">
                <i class="bi bi-plus-lg"></i> Add Facility
              </button>
            </div>
          <?php endif; ?>
        </div>

        <!-- Category Filter Tabs -->
        <div class="d-flex flex-wrap gap-2 mb-4">
          <?php foreach ($categories as $cat): ?>
            <?php 
              $isActive = ($categoryFilter === $cat);
              $btnClass = $isActive ? 'btn-su-indigo' : 'btn-su-outline';
              $url = 'facilities.php?category=' . urlencode($cat) . (!empty($searchQuery) ? '&search=' . urlencode($searchQuery) : '');
            ?>
            <a href="<?php echo $url; ?>" class="btn btn-sm <?php echo $btnClass; ?> px-3">
              <?php echo htmlspecialchars($cat); ?>
            </a>
          <?php endforeach; ?>
        </div>

        <!-- Facilities Cards Grid -->
        <div class="row g-4 mb-4">
          
          <?php if (empty($facilities_list)): ?>
            <div class="col-12 text-center py-5 bg-white border rounded-4 shadow-sm">
              <i class="bi bi-building-x text-muted display-4 mb-3 d-block"></i>
              <h5 class="fw-bold text-dark">No Facilities Found</h5>
              <p class="text-secondary small mb-3">No campus facilities match your current search or category filter criteria.</p>
              <a href="facilities.php" class="btn btn-sm btn-su-outline px-4">Reset Filters</a>
            </div>
          <?php else: ?>
            <?php foreach ($facilities_list as $fac): ?>
              <?php
                $imgUrl = !empty($fac['image']) ? $fac['image'] : 'https://images.unsplash.com/photo-1541829070764-84a7d30dd3f3?q=80&w=600&auto=format&fit=crop';
            
              ?>
              <div class="col-12 col-md-6 col-lg-4">
                <div class="facility-card h-100 d-flex flex-column justify-content-between">
                  <div>
                    <div class="facility-card-img-wrapper">
                      <img src="<?php echo htmlspecialchars($imgUrl); ?>" alt="<?php echo htmlspecialchars($fac['name']); ?>" class="facility-card-img" />
                      
                    </div>
                    <div class="facility-card-body">
                      <div>
                        <h5 class="facility-title"><?php echo htmlspecialchars($fac['name']); ?></h5>
                        <p class="facility-subtitle text-dark fw-semibold mb-1"><?php echo htmlspecialchars($fac['category']); ?> · Capacity: <?php echo htmlspecialchars($fac['capacity']); ?> people</p>
                        <?php
                          $fs = !empty($fac['start_time']) ? $fac['start_time'] : '08:00';
                          $fe = !empty($fac['end_time']) ? $fac['end_time'] : '22:00';
                          $fsDisp = date("g:i A", strtotime($fs));
                          $feDisp = date("g:i A", strtotime($fe));
                        ?>
                        <p class="facility-subtitle text-muted mb-0"><i class="bi bi-clock me-1 text-primary"></i> Operating: <strong><?php echo $fsDisp; ?> – <?php echo $feDisp; ?></strong> </p>
                      </div>
                      <div class="d-flex justify-content-between align-items-center mt-3">
                        <span class="facility-meta text-uppercase"><?php echo htmlspecialchars($fac['location']); ?></span>
                        <?php if ($userRole === 2): ?>
                          <a href="facility-detail.php?id=<?php echo $fac['id']; ?>" class="btn btn-sm btn-link text-primary fw-semibold p-0">View Details &amp; Reserve &rarr;</a>
                        <?php else: ?>
                          <a href="facility-detail.php?id=<?php echo $fac['id']; ?>" class="btn btn-sm btn-link text-secondary fw-semibold p-0">View Details &rarr;</a>
                        <?php endif; ?>
                      </div>
                    </div>
                  </div>
                </div>
              </div>
            <?php endforeach; ?>
          <?php endif; ?>

        </div>

        <div class="d-flex flex-column flex-sm-row align-items-center justify-content-between gap-3 text-muted small pt-2 border-top">
          <span>Showing <?php echo count($facilities_list); ?> facilities</span>
          <div class="d-flex align-items-center gap-2">
            <span>Page 1 of 1</span>
          </div>
        </div>

  <!-- ADD FACILITY MODAL -->
  <div class="modal fade" id="addFacilityModal" tabindex="-1" aria-labelledby="addFacilityModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg">
      <div class="modal-content rounded-4 border-0 shadow">
        <div class="modal-header border-bottom">
          <h5 class="modal-title fw-bold text-dark" id="addFacilityModalLabel">
            <i class="bi bi-building-add text-primary me-2"></i>Add New Campus Facility
          </h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>
        <form id="addFacilityForm">
          <div class="modal-body p-4">
            
            <div class="row g-3 mb-3">
              <div class="col-md-7">
                <label class="su-label">Facility Name <span class="text-danger">*</span></label>
                <input type="text" class="form-control su-input" id="facName" placeholder="e.g. Innovation Pod 04" required />
              </div>
              <div class="col-md-5">
                <label class="su-label">Category <span class="text-danger">*</span></label>
                <select class="form-select su-input" id="facCategory" required>
                  <option value="Study Pods">Study Pods</option>
                  <option value="Computer Labs">Computer Labs</option>
                  <option value="Meeting Rooms">Meeting Rooms</option>
                  <option value="Lecture Halls">Lecture Halls</option>
                  <option value="Studios">Studios</option>
                  <option value="Other">Other</option>
                </select>
              </div>
            </div>

            <div class="row g-3 mb-3">
              <div class="col-md-4">
                <label class="su-label">Capacity (People/Seats) <span class="text-danger">*</span></label>
                <input type="number" class="form-control su-input" id="facCapacity" placeholder="e.g. 8" min="1" required />
              </div>
              <div class="col-md-8">
                <label class="su-label">Location / Building &amp; Floor <span class="text-danger">*</span></label>
                <input type="text" class="form-control su-input" id="facLocation" placeholder="e.g. Technology Building, Floor 2" required />
              </div>
            </div>

            <div class="row g-3 mb-3">
              <div class="col-md-3">
                <label class="su-label">Opening Time <span class="text-danger">*</span></label>
                <input type="time" class="form-control su-input" id="facStartTime" value="08:00" required />
              </div>
              <div class="col-md-3">
                <label class="su-label">Closing Time <span class="text-danger">*</span></label>
                <input type="time" class="form-control su-input" id="facEndTime" value="22:00" required />
              </div>
              <div class="col-md-6">
                <label class="su-label">Facility Image (File Upload)</label>
                <input type="file" class="form-control su-input" id="facImage" accept="image/*" />
              </div>
            </div>

            <div class="mb-3">
              <label class="su-label">Equipment &amp; Amenities</label>
              <input type="text" class="form-control su-input" id="facEquipment" placeholder="e.g. 4K Display (55&quot;), Whiteboard, USB-C Dock, AC" />
            </div>

            <div class="mb-3">
              <label class="su-label">Description</label>
              <textarea class="form-control su-input" id="facDescription" rows="3" placeholder="Brief description of the facility..."></textarea>
            </div>

          </div>
          <div class="modal-footer border-top p-3">
            <button type="button" class="btn btn-su-outline" data-bs-dismiss="modal">Cancel</button>
            <button type="button" onclick="addFacility();" class="btn btn-su-indigo px-4">Add Facility</button>
          </div>
        </form>
      </div>
    </div>
  </div>

<?php require_once "includes/footer.php"; ?>
