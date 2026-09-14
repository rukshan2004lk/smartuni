document.addEventListener("DOMContentLoaded", () => {
  const sidebarLinks = document.querySelectorAll(".sidebar-link");
  const currentPath = window.location.pathname.split("/").pop() || "index.php";

  sidebarLinks.forEach((link) => {
    const href = link.getAttribute("href");
    if (href === currentPath) {
      link.classList.add("active");
    }
  });

  const sidebarToggleBtn = document.getElementById("sidebarToggleBtn");
  const appSidebar = document.querySelector(".app-sidebar");

  if (sidebarToggleBtn && appSidebar) {
    sidebarToggleBtn.addEventListener("click", () => {
      appSidebar.classList.toggle("show");
    });

    document.addEventListener("click", (e) => {
      if (
        window.innerWidth < 992 &&
        appSidebar.classList.contains("show") &&
        !appSidebar.contains(e.target) &&
        !sidebarToggleBtn.contains(e.target)
      ) {
        appSidebar.classList.remove("show");
      }
    });
  }

  const topbarNotifBtn = document.getElementById("topbarNotifBtn");
  if (topbarNotifBtn) {
    topbarNotifBtn.addEventListener("click", () => {
      alert(
        "Campus Notifications:\n1. CS301 Lecture relocated to Turing Hall 204\n2. HVAC Maintenance scheduled in West Wing",
      );
    });
  }

  const categoryCards = document.querySelectorAll(".category-card");
  categoryCards.forEach((card) => {
    card.addEventListener("click", () => {
      categoryCards.forEach((c) => c.classList.remove("selected"));
      card.classList.add("selected");
    });
  });

  const attendeeMinusBtn = document.getElementById("attendeeMinusBtn");
  const attendeePlusBtn = document.getElementById("attendeePlusBtn");
  const attendeeCountEl = document.getElementById("attendeeCount");

  if (attendeeMinusBtn && attendeePlusBtn && attendeeCountEl) {
    attendeeMinusBtn.addEventListener("click", () => {
      let val = parseInt(attendeeCountEl.textContent) || 1;
      if (val > 1) {
        attendeeCountEl.textContent = val - 1;
      }
    });

    attendeePlusBtn.addEventListener("click", () => {
      let val = parseInt(attendeeCountEl.textContent) || 1;
      if (val < 10) {
        attendeeCountEl.textContent = val + 1;
      }
    });
  }

  const slotGridButtons = document.querySelectorAll(".slot-picker-btn");
  const selectedSlotBar = document.getElementById("selectedSlotBar");
  const selectedSlotText = document.getElementById("selectedSlotText");

  slotGridButtons.forEach((btn) => {
    btn.addEventListener("click", () => {
      slotGridButtons.forEach((b) => b.classList.remove("selected"));
      btn.classList.add("selected");
      if (selectedSlotBar && selectedSlotText) {
        const time = btn.dataset.time || "14:00 - 16:00";
        const day = btn.dataset.day || "Thursday, Oct 24";
        selectedSlotText.textContent = `${day} · ${time}`;
        selectedSlotBar.style.display = "flex";
      }
    });
  });

  const togglePasswordBtn = document.getElementById("togglePasswordBtn");
  const passwordInput = document.getElementById("passwordInput");
  const togglePasswordIcon = document.getElementById("togglePasswordIcon");

  if (togglePasswordBtn && passwordInput && togglePasswordIcon) {
    togglePasswordBtn.addEventListener("click", () => {
      const isPassword = passwordInput.getAttribute("type") === "password";
      passwordInput.setAttribute("type", isPassword ? "text" : "password");
      if (isPassword) {
        togglePasswordIcon.classList.remove("bi-eye");
        togglePasswordIcon.classList.add("bi-eye-slash");
      } else {
        togglePasswordIcon.classList.remove("bi-eye-slash");
        togglePasswordIcon.classList.add("bi-eye");
      }
    });
  }

  const globalSearchInput = document.getElementById("globalSearchInput");
  if (globalSearchInput) {
    globalSearchInput.addEventListener("keydown", (e) => {
      if (e.key === "Enter") {
        window.location.href = `facilities.php?search=${encodeURIComponent(globalSearchInput.value)}`;
      }
    });
  }
});

function handleApproval(id, action) {
  const row = document.getElementById(`approval-row-${id}`);
  if (row) {
    if (action === "approve") {
      row.style.backgroundColor = "#f0fdf4";
      alert(`Booking #${id} has been APPROVED.`);
    } else if (action === "reject") {
      row.style.backgroundColor = "#fef2f2";
      alert(`Booking #${id} has been REJECTED.`);
    } else if (action === "resolve") {
      alert(`Conflict for Booking #${id} resolved.`);
    }
  }
}

function showMessage(text, isError = true) {
  const msgDiv = document.getElementById("msgDiv");
  const msgText = document.getElementById("msgText");

  msgText.textContent = text;
  msgDiv.classList.remove("d-none", "alert-danger", "alert-success");

  if (isError) {
    msgDiv.classList.add("alert-danger");
    msgDiv.querySelector("i").className = "bi bi-exclamation-circle-fill";
  } else {
    msgDiv.classList.add("alert-success");
    msgDiv.querySelector("i").className = "bi bi-check-circle-fill";
  }

  msgDiv.scrollIntoView({ behavior: "smooth", block: "nearest" });
}

function register() {
  const roleId = document.getElementById("role")
    ? document.getElementById("role").value.trim()
    : "";
  const fname = document.getElementById("fname").value.trim();
  const lname = document.getElementById("lname").value.trim();
  const mobileInput = document.getElementById("mobile");
  const mobile = mobileInput ? mobileInput.value.trim() : "";
  const regNumber = document.getElementById("regNumber").value.trim();
  const email = document.getElementById("email").value.trim();
  const password = document.getElementById("password").value;
  const confirmPassword = document.getElementById("confirmPassword").value;


  if (password !== confirmPassword) {
    showAlert("Passwords do not match.", "error");
    return;
  }

  const form = new FormData();
  form.append("role_id", roleId);
  form.append("fname", fname);
  form.append("lname", lname);
  form.append("mobile", mobile);
  form.append("reg_number", regNumber);
  form.append("email", email);
  form.append("password", password);

  const request = new XMLHttpRequest();

  request.onreadystatechange = function () {
    if (request.readyState === 4 && request.status === 200) {
      const response = request.responseText.trim();

      if (response === "success") {
        showMessage("Sign in successful! Redirecting...", "success");
        setTimeout(() => {
          window.location.href = "dashboard.php";
        }, 1000);
      } else {
        showMessage(response, "error");
      }
    }
  }; 

 
  request.open("POST", "api/registerProcess.php", true);
  request.send(form);
}

function login(){
  const email = document.getElementById("campusEmailInput").value.trim();
  const password = document.getElementById("passwordInput").value;
  
  const form = new FormData();
  form.append("email", email);
  form.append("password", password);

  const request = new XMLHttpRequest();
  
  request.onreadystatechange = function () {
    if (request.readyState === 4 && request.status === 200) {
      const response = request.responseText.trim();
      
      if (response === "success") {
        showMessage("Login successful! Redirecting...", "success");
        setTimeout(() => {
          window.location.href = "dashboard.php";
        }, 1000);
      } else {
        showMessage(response, "error");
      }
    }
  };

  request.open("POST", "api/loginProcess.php", true);
  request.send(form); 
}