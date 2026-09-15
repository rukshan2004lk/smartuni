<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Redirect logged-in users directly to dashboard
if (isset($_SESSION['user']) && !empty($_SESSION['user'])) {
    header("Location: dashboard.php");
    exit();
}

$rememberedEmail = $_COOKIE['email'] ?? '';
$rememberedPassword = $_COOKIE['password'] ?? '';
$isRemembered = !empty($rememberedEmail);
?>

<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>SmartUni Campus OS - Sign In</title>

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

  <main class="su-main-wrapper">
    <div class="container su-card-container">
      <div class="row g-4 g-lg-5 align-items-center">

        <div class="col-12 col-md-6 col-lg-6">
          <div class="su-left-col">

            <div class="mb-4">
              <h1 class="su-heading">Welcome back</h1>
              <p class="su-subtext">Sign in with your University ID or campus credentials to access your workspace.</p>
            </div>

            <form id="loginForm">
              <div class="su-form-group">
                <div class="form-floating">
                  <input type="email" class="form-control" id="campusEmailInput" placeholder="mail@tec.rjt.ac.lk" value="<?= htmlspecialchars($rememberedEmail) ?>" required autocomplete="username" />
                  <label for="email">EMAIL</label>
                </div>
              </div>

              <div class="su-form-group">
                <div class="d-flex align-items-center justify-content-between mb-1">
                  <span class="su-label mb-0">PASSWORD</span>
                  <a href="#" class="su-forgot-link">Forgot password?</a>
                </div>
                <div class="su-password-wrapper">
                  <input type="password" class="form-control su-input w-100 pe-5" id="passwordInput" placeholder="••••••••••••" value="<?= htmlspecialchars($rememberedPassword) ?>" required />
                  <button type="button" class="su-password-toggle" id="togglePasswordBtn" aria-label="Toggle password visibility">
                    <i class="bi bi-eye" id="togglePasswordIcon"></i>
                  </button>
                </div>
              </div>

              <div class="su-remember-row">
                <div class="form-check d-flex align-items-center gap-2">
                  <input class="form-check-input" type="checkbox" id="rememberMeCheck" <?= $isRemembered ? 'checked' : '' ?> />
                  <label class="form-check-label" for="rememberMeCheck">Remember this device</label>
                </div>
                <div class="su-encrypted-badge">
                  <i class="bi bi-lock-fill"></i>
                  <span>Encrypted</span>
                </div>
              </div>

              <div id="msgDiv" class="alert alert-danger d-none d-flex align-items-center gap-2 py-2 px-3 mb-3 small" role="alert">
                <i class="bi bi-exclamation-circle-fill"></i>
                <span id="msgText"></span>
              </div>

              <button type="button" onclick="login()" class="btn btn-su-dark w-100" id="submitBtn">
                <span>Sign in</span>
                <i class="bi bi-arrow-right fs-6 ms-1"></i>
              </button>
            </form>

            <div class="su-account-prompt">
              Need an account? <a href="register.php">Contact your registrar</a>
            </div>

          </div>
        </div>

        <div class="col-12 col-md-6 col-lg-6">
          <div class="su-showcase-card">
            <img src="images/logo.jfif" alt="Science & Technology Commons"  class="su-showcase-img " " />
           
       
          </div>
        </div>

      </div>
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