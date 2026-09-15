<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
require_once "includes/connection.php";

$pageTitle = "SmartUni Portal - Profile & Account Settings";
$currentPage = "settings";
require_once "includes/header.php";

$userId = $_SESSION['user']['id'] ?? 1;

// Fetch real user data from database
$user_rs = Database::search("SELECT u.*, r.name AS role_name FROM `users` u LEFT JOIN `roles` r ON u.role_id = r.id WHERE u.id = '$userId'");
$user = null;

if ($user_rs && $user_rs->num_rows > 0) {
    $user = $user_rs->fetch_assoc();
} else {
    // Fallback to first user in database
    $fallback_rs = Database::search("SELECT u.*, r.name AS role_name FROM `users` u LEFT JOIN `roles` r ON u.role_id = r.id ORDER BY u.id ASC LIMIT 1");
    if ($fallback_rs && $fallback_rs->num_rows > 0) {
        $user = $fallback_rs->fetch_assoc();
    }
}

$fname     = $user['fname'] ?? '';
$lname     = $user['lname'] ?? '';
$email     = $user['email'] ?? '';
$mobile    = $user['mobile'] ?? '';
$regNumber = $user['reg_number'] ?? '';
$roleName  = $user['role_name'] ?? '';
$rawPic     = $user['profile_pic'] ?? '';
$profilePic = (!empty($rawPic) && !str_contains($rawPic, 'unsplash')) ? $rawPic : 'images/user.png';
?>

        <div class="mb-4">
          <span class="text-muted small fw-semibold text-uppercase">ACCOUNT MANAGEMENT · Preferences</span>
          <h1 class="fw-bold fs-3 mb-1">Profile &amp; Settings</h1>
          <p class="text-secondary small mb-0">Manage your personal profile, notification preferences, and account credentials.</p>
        </div>

        <div class="row g-4 mb-4">
          
          <!-- Avatar & Profile Photo Upload Card -->
          <div class="col-12 col-md-4">
            <div class="bg-white border rounded-4 p-4 shadow-sm text-center">
              <div class="position-relative d-inline-block mb-3">
                <img 
                  id="profileAvatarPreview"
                  src="<?= htmlspecialchars($profilePic) ?>" 
                  alt="Profile Avatar" 
                  class="rounded-circle border border-3 border-primary-subtle object-fit-cover shadow-sm" 
                  width="120" 
                  height="120" 
                />
                <button 
                  type="button" 
                  class="btn btn-sm btn-primary rounded-circle position-absolute bottom-0 end-0 p-2 shadow"
                  onclick="document.getElementById('profilePicInput').click()"
                  title="Upload New Photo">
                  <i class="bi bi-camera-fill"></i>
                </button>
              </div>

              <input type="file" id="profilePicInput" accept="image/*" class="d-none" onchange="previewProfilePic(event)">

              <h5 class="fw-bold text-dark mb-1"><?= htmlspecialchars($fname . ' ' . $lname) ?></h5>
              <p class="text-muted small mb-2"><?= htmlspecialchars($roleName) ?> · ID: <?= htmlspecialchars($regNumber) ?></p>
              <span class="su-badge su-badge-green">● Active Account</span>
              
              <div class="mt-3 pt-3 border-top text-start text-secondary small">
                <div class="mb-1"><i class="bi bi-envelope me-2 text-primary"></i><?= htmlspecialchars($email) ?></div>
                <div class="mb-1"><i class="bi bi-telephone me-2 text-primary"></i><?= htmlspecialchars($mobile ?: 'Not set') ?></div>
                <div><i class="bi bi-calendar-check me-2 text-primary"></i>Member since <?= date("M Y", strtotime($user['created_at'] ?? 'now')) ?></div>
              </div>

              <button type="button" class="btn btn-sm btn-su-outline w-100 mt-3" onclick="document.getElementById('profilePicInput').click()">
                <i class="bi bi-upload me-1"></i> Change Profile Picture
              </button>
            </div>
          </div>

          <!-- Personal Information Form Card -->
          <div class="col-12 col-md-8">
            <div class="bg-white border rounded-4 p-4 shadow-sm">
              <h5 class="fw-bold text-dark mb-3"><i class="bi bi-person-gear me-2 text-primary"></i>Personal Information</h5>
              
              <form id="profileForm" onsubmit="event.preventDefault(); updateUserProfile();">
                <div class="row g-3 mb-3">
                  <div class="col-6">
                    <label class="form-label small fw-semibold text-secondary">First Name <span class="text-danger">*</span></label>
                    <input type="text" class="form-control rounded-3" id="profileFname" value="<?= htmlspecialchars($fname) ?>" required>
                  </div>
                  <div class="col-6">
                    <label class="form-label small fw-semibold text-secondary">Last Name <span class="text-danger">*</span></label>
                    <input type="text" class="form-control rounded-3" id="profileLname" value="<?= htmlspecialchars($lname) ?>" required>
                  </div>
                </div>

                <div class="mb-3">
                  <div class="d-flex justify-content-between align-items-center mb-1">
                    <label class="form-label small fw-semibold text-secondary mb-0">Email Address</label>
                    <span class="badge bg-light text-secondary border">System Managed</span>
                  </div>
                  <input type="email" class="form-control rounded-3 bg-light text-muted" id="profileEmail" value="<?= htmlspecialchars($email) ?>" readonly disabled>
                </div>

                <div class="row g-3 mb-4">
                  <div class="col-6">
                    <label class="form-label small fw-semibold text-secondary">Registration / ID Number</label>
                    <input type="text" class="form-control rounded-3" value="<?= htmlspecialchars($regNumber) ?>" readonly disabled>
                  </div>
                  <div class="col-6">
                    <label class="form-label small fw-semibold text-secondary">Mobile Number</label>
                    <input type="text" class="form-control rounded-3" id="profileMobile" value="<?= htmlspecialchars($mobile) ?>" placeholder="e.g. 0713218157">
                  </div>
                </div>

                <div class="d-flex justify-content-end gap-2">
                  <button type="submit" class="btn btn-su-indigo px-4 py-2">
                    <i class="bi bi-save me-1"></i> Save Changes
                  </button>
                </div>
              </form>
            </div>
          </div>

        </div>

<?php require_once "includes/footer.php"; ?>
