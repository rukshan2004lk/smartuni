<?php

require_once "includes/connection.php";

$roles_rs = Database::search("SELECT * FROM `roles`");
?>

<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>SmartUni Campus OS - Create Account</title>
  <link rel="preconnect" href="https://fonts.googleapis.com" />
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin />
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet" />
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" />
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" />
  <link rel="stylesheet" href="assets/css/style.css" />
</head>

<body>

  <header class="su-header px-4 px-md-5 d-flex align-items-center justify-content-between">
    <div class="d-flex align-items-center gap-3">
      <a href="index.php" class="su-brand-mark">
        <img src="http://localhost:3845/assets/01b83241b8479fb6103a1211b15a12a97447db7a.png" alt="SmartUni Logo" class="su-brand-logo-img" onerror="this.style.display='none'; this.nextElementSibling.style.display='inline-flex';" />
        <span style="display:none;" class="fw-bold fs-5 tracking-tight text-dark"><i class="bi bi-mortarboard-fill me-1 text-dark"></i>SmartUni</span>
      </a>
  
    </div>

   
     
  </header>

  <main class="su-auth-container">
    <div class="su-auth-box">

      <div class="text-center mb-4">
        <h1 class="su-heading fs-3">Create your account</h1>
        <p class="su-subtext">Single sign-on access to coursework, research data, and campus portals.</p>
      </div>

      <form action="verification-pending.html">

        <div class="mb-3">
          <label for="role" class="su-label">Academic Affiliation</label>
          <select id="role" class="form-select su-input">
            <?php while ($role = $roles_rs->fetch_assoc()) { ?>
              <option value="<?php echo $role['id']; ?>">
                <?php echo $role['name']; ?>
              </option>
            <?php } ?>
          </select>
        </div>
        <div class="row g-3 mb-3">
          <div class="col-md-6">
            <div class="d-flex justify-content-between align-items-center mb-1">
              <label for="fname" class="su-label mb-0">First Name</label>
              <span class="text-muted small" style="font-size: 10px;">As on official ID</span>
            </div>
            <input id="fname" type="text" class="form-control su-input" placeholder="First Name" required />
          </div>
          <div class="col-md-6">
            <div class="d-flex justify-content-between align-items-center mb-1">
              <label for="lname" class="su-label mb-0">Last Name</label>
            </div>
            <input id="lname" type="text" class="form-control su-input" placeholder="Last Name" required />
          </div>
        </div>


        <div class="mb-3">
          <div class="d-flex justify-content-between align-items-center mb-1">
            <label for="mobile" class="su-label mb-0">Phone Number</label>
          </div>
          <div class="input-group">
            <input id="mobile" type="text" class="form-control su-input" placeholder="07x-xxx-xxxx" required />
            <span class="input-group-text bg-white border-start-0 text-muted"><i class="bi bi-phone"></i></span>
          </div>
        </div>

        <div class="mb-3">
          <div class="d-flex justify-content-between align-items-center mb-1">
            <label for="regNumber" class="su-label mb-0">Reg Number</label>

          </div>
          <div class="input-group">
            <input id="regNumber" type="text" class="form-control su-input" placeholder="ITT/20xx/xxx" required />
            <span class="input-group-text bg-white border-start-0 text-muted"><i class="bi bi-person-badge"></i></span>
          </div>
        </div>

        <div class="mb-3">
          <div class="d-flex justify-content-between align-items-center mb-1">
            <label for="email" class="su-label mb-0">Institutional Email</label>

          </div>
          <div class="input-group">
            <input id="email" type="email" class="form-control su-input" placeholder="mail@tec.rjt.ac.lk" required />
            <span class="input-group-text bg-white border-start-0 text-muted"><i class="bi bi-envelope"></i></span>
          </div>
        </div>

        <div class="row g-3 mb-2">
          <div class="col-md-6">
            <div class="d-flex justify-content-between align-items-center mb-1">
              <label for="password" class="su-label mb-0">Create Password</label>
            </div>
            <input id="password" type="password" class="form-control su-input" placeholder="••••••••••••" required />
          </div>
          <div class="col-md-6">
            <div class="d-flex justify-content-between align-items-center mb-1">
              <label for="confirmPassword" class="su-label mb-0">Confirm Password</label>
           
            </div>
            <input id="confirmPassword" type="password" class="form-control su-input" placeholder="••••••••••••" required />
          </div>
        </div>

        <div class="mb-4">
      
          <div class="d-flex justify-content-between align-items-center">
           
            <span class="text-muted" style="font-size: 10.5px;">8+ chars · 1 uppercase · 1 symbol</span>
          </div>
        </div>

        <div id="msgDiv" class="alert alert-danger d-none d-flex align-items-center gap-2 py-2 px-3 mb-3 small" role="alert">
          <i class="bi bi-exclamation-circle-fill"></i>
          <span id="msgText"></span>
        </div>

        <button type="button" onclick="register();" class="btn btn-su-dark w-100 py-2 mb-3">
          <span>Create account & continue</span>
          <i class="bi bi-arrow-right ms-1"></i>
        </button>

        <div class="d-flex justify-content-between align-items-center text-muted fs-7 mt-3">
          <span>Already registered? <a href="index.php" class="text-primary fw-medium">Sign in</a></span>
          <a href="contacts.php" class="text-secondary">Campus IT Helpdesk</a>
        </div>

      </form>

    </div>
  </main>

 <footer class="su-footer">
  <div class="container-fluid px-0 max-w-1400">
    <div class="d-flex align-items-center justify-content-center">
      <small class="text-center text-muted">
        © 2025 SmartUni, Inc. Developed by IT Students, Faculty of Technology, RUSL
      </small>
    </div>
  </div>
</footer>

  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
  <script src="assets/js/script.js"></script>
</body>

</html>