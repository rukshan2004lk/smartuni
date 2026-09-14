<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
require_once "includes/connection.php";

$pageTitle = "SmartUni Portal - User Management";
$currentPage = "users-list";
require_once "includes/header.php";

// Fetch all users with role and status names
$users_rs = Database::search("SELECT u.*, r.name AS role_name, s.name AS status_name 
    FROM `users` u 
    LEFT JOIN `roles` r ON u.role_id = r.id 
    LEFT JOIN `statuses` s ON u.status_id = s.id 
    ORDER BY u.id DESC");

$users_list = [];
$studentCount = 0;
$lecturerCount = 0;
$adminCount = 0;

if ($users_rs && $users_rs->num_rows > 0) {
    while ($row = $users_rs->fetch_assoc()) {
        $users_list[] = $row;
        $rId = intval($row['role_id']);
        if ($rId === 1) $studentCount++;
        else if ($rId === 2) $lecturerCount++;
        else if ($rId === 3) $adminCount++;
    }
}

// Fetch available roles & statuses for dropdowns
$roles_list = [];
$roles_rs = Database::search("SELECT * FROM `roles` ORDER BY `id` ASC");
if ($roles_rs && $roles_rs->num_rows > 0) {
    while ($r = $roles_rs->fetch_assoc()) {
        $roles_list[] = $r;
    }
}

$user_statuses = [];
$statuses_rs = Database::search("SELECT * FROM `statuses` WHERE `id` IN (1, 2, 3) ORDER BY `id` ASC");
if ($statuses_rs && $statuses_rs->num_rows > 0) {
    while ($s = $statuses_rs->fetch_assoc()) {
        $user_statuses[] = $s;
    }
}
?>

        <div class="d-flex flex-column flex-md-row align-items-md-center justify-content-between gap-3 mb-4">
          <div>
            <span class="text-muted small fw-semibold text-uppercase">ADMINISTRATION · User Directory</span>
            <h1 class="fw-bold fs-3 mb-1">User Management</h1>
            <p class="text-secondary small mb-0">View, manage roles, and update account verification statuses across campus users.</p>
          </div>
          <div class="d-flex align-items-center gap-2">
            <span class="su-badge su-badge-purple">● <?= $studentCount ?> Students</span>
            <span class="su-badge su-badge-amber">● <?= $lecturerCount ?> Lecturers</span>
            <span class="su-badge su-badge-green">● <?= $adminCount ?> Admins</span>
          </div>
        </div>

        <div class="d-flex flex-column flex-md-row align-items-md-center justify-content-between gap-3 mb-4">
          <div class="d-flex flex-wrap gap-2">
            <button class="btn btn-sm btn-su-indigo user-filter-btn px-3" onclick="filterUserRows('All', this)">All (<?= count($users_list) ?>)</button>
            <button class="btn btn-sm btn-su-outline user-filter-btn px-3" onclick="filterUserRows('Student', this)">Students (<?= $studentCount ?>)</button>
            <button class="btn btn-sm btn-su-outline user-filter-btn px-3" onclick="filterUserRows('Lecturer', this)">Lecturers (<?= $lecturerCount ?>)</button>
            <button class="btn btn-sm btn-su-outline user-filter-btn px-3" onclick="filterUserRows('Admin', this)">Admins (<?= $adminCount ?>)</button>
          </div>

          <div class="position-relative" style="min-width: 260px;">
            <i class="bi bi-search position-absolute top-50 start-0 translate-middle-y ms-3 text-muted"></i>
            <input type="text" id="userSearchInput" class="form-control su-input ps-5 rounded-3" placeholder="Search user name, email, ID..." onkeyup="searchUsersTable()" />
          </div>
        </div>

        <div class="su-table-card mb-4">
          <div class="table-responsive">
            <table class="su-table">
              <thead>
                <tr>
                  <th>USER PROFILE</th>
                  <th>REG / ID NUMBER</th>
                  <th>MOBILE</th>
                  <th>ROLE</th>
                  <th>ACCOUNT STATUS</th>
                  <th class="text-end">ACTIONS</th>
                </tr>
              </thead>
              <tbody>
                <?php if (empty($users_list)): ?>
                  <tr>
                    <td colspan="6" class="text-center py-5 text-muted">
                      <i class="bi bi-people fs-2 d-block mb-2 text-secondary"></i>
                      No user accounts found in database.
                    </td>
                  </tr>
                <?php else: ?>
                  <?php foreach ($users_list as $u): ?>
                    <?php
                      $uId = $u['id'];
                      $fullName = trim(($u['fname'] ?? '') . ' ' . ($u['lname'] ?? ''));
                      $email = $u['email'] ?? '';
                      $mobile = $u['mobile'] ?? 'N/A';
                      $regNum = $u['reg_number'] ?? 'N/A';
                      $roleName = $u['role_name'] ?? 'Student';
                      $statusName = $u['status_name'] ?? 'Pending Verification';
                      $stId = intval($u['status_id']);
                      $rId = intval($u['role_id']);
                      
                      $avatar = !empty($u['profile_pic']) ? $u['profile_pic'] : 'https://images.unsplash.com/photo-1534528741775-53994a69daeb?q=80&w=120&auto=format&fit=crop';

                      // Status Badge Colors
                      $statusBadgeClass = "su-badge-amber";
                      if ($stId === 2) {
                          $statusBadgeClass = "su-badge-green";
                          $statusName = "Verified / Active";
                      } else if ($stId === 3) {
                          $statusBadgeClass = "su-badge-red";
                          $statusName = "Suspended";
                      } else {
                          $statusBadgeClass = "su-badge-amber";
                          $statusName = "Pending Verification";
                      }

                      // Role Badge Colors
                      $roleBadgeClass = "badge bg-secondary-subtle text-dark border";
                      if ($rId === 3) {
                          $roleBadgeClass = "badge bg-primary-subtle text-primary border border-primary-subtle";
                      } else if ($rId === 2) {
                          $roleBadgeClass = "badge bg-warning-subtle text-warning-emphasis border border-warning-subtle";
                      }

                      // JSON Payload for modal edit
                      $jsUserPayload = json_encode($u, JSON_HEX_APOS | JSON_HEX_QUOT);
                    ?>
                    <tr class="user-table-row" data-role="<?= htmlspecialchars($roleName) ?>" data-status="<?= htmlspecialchars($statusName) ?>">
                      <td>
                        <div class="d-flex align-items-center gap-3">
                          <img src="<?= htmlspecialchars($avatar) ?>" alt="<?= htmlspecialchars($fullName) ?>" class="rounded-circle object-fit-cover flex-shrink-0" width="40" height="40" />
                          <div>
                            <div class="fw-bold text-dark"><?= htmlspecialchars($fullName) ?></div>
                            <div class="text-muted small"><i class="bi bi-envelope me-1"></i><?= htmlspecialchars($email) ?></div>
                          </div>
                        </div>
                      </td>
                      <td>
                        <span class="fw-semibold text-dark"><?= htmlspecialchars($regNum) ?></span>
                      </td>
                      <td>
                        <span class="text-secondary small"><?= htmlspecialchars($mobile ?: 'N/A') ?></span>
                      </td>
                      <td>
                        <span class="<?= $roleBadgeClass ?> fw-semibold"><?= htmlspecialchars($roleName) ?></span>
                      </td>
                      <td>
                        <span class="su-badge <?= $statusBadgeClass ?>">● <?= htmlspecialchars($statusName) ?></span>
                      </td>
                      <td class="text-end">
                        <button class="btn btn-sm btn-outline-primary rounded-3 px-3" onclick="openEditUserModal(<?= htmlspecialchars($jsUserPayload, ENT_QUOTES) ?>)">
                          <i class="bi bi-pencil me-1"></i> Edit Role &amp; Status
                        </button>
                      </td>
                    </tr>
                  <?php endforeach; ?>
                <?php endif; ?>
              </tbody>
            </table>
          </div>
        </div>

        <!-- Edit User Modal -->
        <div class="modal fade" id="editUserModal" tabindex="-1" aria-labelledby="editUserModalLabel" aria-hidden="true">
          <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content border-0 shadow rounded-4">
              <div class="modal-header border-bottom-0 pb-0">
                <h5 class="modal-title fw-bold text-dark" id="editUserModalLabel">Edit User Role &amp; Status</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
              </div>
              <div class="modal-body p-4">
                <input type="hidden" id="editUserId" value="">

                <div class="row g-3 mb-3">
                  <div class="col-6">
                    <label class="form-label small fw-semibold text-secondary">First Name <span class="text-danger">*</span></label>
                    <input type="text" class="form-control rounded-3" id="editUserFname" required>
                  </div>
                  <div class="col-6">
                    <label class="form-label small fw-semibold text-secondary">Last Name <span class="text-danger">*</span></label>
                    <input type="text" class="form-control rounded-3" id="editUserLname" required>
                  </div>
                </div>

                <div class="mb-3">
                  <label class="form-label small fw-semibold text-secondary">Email Address</label>
                  <input type="email" class="form-control rounded-3 bg-light text-muted" id="editUserEmail" readonly disabled>
                </div>

                <div class="row g-3 mb-3">
                  <div class="col-6">
                    <label class="form-label small fw-semibold text-secondary">Registration / ID</label>
                    <input type="text" class="form-control rounded-3" id="editUserReg">
                  </div>
                  <div class="col-6">
                    <label class="form-label small fw-semibold text-secondary">Mobile Number</label>
                    <input type="text" class="form-control rounded-3" id="editUserMobile">
                  </div>
                </div>

                <div class="row g-3 mb-3">
                  <div class="col-6">
                    <label class="form-label small fw-semibold text-secondary">Assigned Role <span class="text-danger">*</span></label>
                    <select class="form-select rounded-3" id="editUserRole">
                      <?php foreach ($roles_list as $role): ?>
                        <option value="<?= $role['id'] ?>"><?= htmlspecialchars($role['name']) ?></option>
                      <?php endforeach; ?>
                    </select>
                  </div>
                  <div class="col-6">
                    <label class="form-label small fw-semibold text-secondary">Account Status <span class="text-danger">*</span></label>
                    <select class="form-select rounded-3" id="editUserStatus">
                      <option value="2">Verified / Active</option>
                      <option value="1">Pending Verification</option>
                      <option value="3">Suspended</option>
                    </select>
                  </div>
                </div>

              </div>
              <div class="modal-footer border-top-0 pt-0 px-4 pb-4">
                <button type="button" class="btn btn-secondary rounded-3 px-3" data-bs-dismiss="modal">Cancel</button>
                <button type="button" class="btn btn-su-indigo rounded-3 px-4" onclick="saveUserEdit()">Save User Changes</button>
              </div>
            </div>
          </div>
        </div>

<?php require_once "includes/footer.php"; ?>
