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

/* ==========================================
   ACADEMIC TIMETABLE FUNCTIONS
   ========================================== */

function parseTimeToMinutes(t) {
  if (!t) return 0;
  const parts = t.split(":");
  return (parseInt(parts[0], 10) * 60) + parseInt(parts[1], 10);
}

function openAddModalForSlot(day, startTime, endTime) {
  document.getElementById("ttDay").value = day;
  document.getElementById("ttStartTime").value = startTime;
  document.getElementById("ttEndTime").value = endTime;
  const addModal = new bootstrap.Modal(document.getElementById("addTimetableModal"));
  addModal.show();
}

function openEditModal(entry) {
  document.getElementById("editTtId").value = entry.id;
  document.getElementById("editTtCourseCode").value = entry.course_code || "";
  document.getElementById("editTtCourseTitle").value = entry.course_name || "";
  document.getElementById("editTtLocation").value = entry.location || "";
  document.getElementById("editTtDay").value = entry.day_of_week || "Monday";
  document.getElementById("editTtStartTime").value = entry.start_time || "08:30";
  document.getElementById("editTtEndTime").value = entry.end_time || "09:30";

  const editModal = new bootstrap.Modal(document.getElementById("editTimetableModal"));
  editModal.show();
}

function addTimetable() {
  const courseCode   = document.getElementById("ttCourseCode").value.trim();
  const courseName   = document.getElementById("ttCourseTitle").value.trim();
  const location     = document.getElementById("ttLocation").value.trim();
  const dayOfWeek    = document.getElementById("ttDay").value;
  const startTime    = document.getElementById("ttStartTime").value;
  const endTime      = document.getElementById("ttEndTime").value;

  if (courseCode === "") {
    Swal.fire({ icon: "warning", title: "Validation Error", text: "Please enter the Course Code.", confirmButtonColor: "#4f46e5" });
    return;
  }
  if (courseName === "") {
    Swal.fire({ icon: "warning", title: "Validation Error", text: "Please enter the Course Title.", confirmButtonColor: "#4f46e5" });
    return;
  }
  if (dayOfWeek === "") {
    Swal.fire({ icon: "warning", title: "Validation Error", text: "Please select the Day of Week.", confirmButtonColor: "#4f46e5" });
    return;
  }
  if (startTime === "") {
    Swal.fire({ icon: "warning", title: "Validation Error", text: "Please enter the Start Time.", confirmButtonColor: "#4f46e5" });
    return;
  }
  if (endTime === "") {
    Swal.fire({ icon: "warning", title: "Validation Error", text: "Please enter the End Time.", confirmButtonColor: "#4f46e5" });
    return;
  }

  const startMin = parseTimeToMinutes(startTime);
  const endMin   = parseTimeToMinutes(endTime);

  if (startMin >= endMin) {
    Swal.fire({ icon: "warning", title: "Invalid Time Range", text: "End Time must be after Start Time.", confirmButtonColor: "#4f46e5" });
    return;
  }
  if (startMin < 510) {
    Swal.fire({ icon: "warning", title: "Invalid Time Range", text: "Lectures cannot start before 08:30 AM.", confirmButtonColor: "#4f46e5" });
    return;
  }
  if (endMin > 1110) {
    Swal.fire({ icon: "warning", title: "Invalid Time Range", text: "Lectures cannot end after 06:30 PM.", confirmButtonColor: "#4f46e5" });
    return;
  }
  if (startMin < 810 && endMin > 750) {
    Swal.fire({ icon: "error", title: "Lunch Break Conflict", text: "Cannot schedule lectures during the Lunch Interval (12:30 PM - 01:30 PM).", confirmButtonColor: "#4f46e5" });
    return;
  }

  const form = new FormData();
  form.append("course_code", courseCode);
  form.append("course_name", courseName);
  form.append("location", location);
  form.append("day_of_week", dayOfWeek);
  form.append("start_time", startTime);
  form.append("end_time", endTime);

  const request = new XMLHttpRequest();
  request.onreadystatechange = function () {
    if (request.readyState === 4 && request.status === 200) {
      const response = request.responseText.trim();
      if (response === "success") {
        Swal.fire({
          icon: "success",
          title: "Success!",
          text: "Timetable entry added successfully!",
          timer: 1200,
          showConfirmButton: false,
          willClose: () => {
            window.location.reload();
          }
        }).then(() => {
          window.location.reload();
        });
      } else {
        Swal.fire({ icon: "error", title: "Error", text: response, confirmButtonColor: "#4f46e5" });
      }
    }
  };
  request.open("POST", "api/addTimetableProcess.php", true);
  request.send(form);
}

function updateTimetable() {
  const id           = document.getElementById("editTtId").value;
  const courseCode   = document.getElementById("editTtCourseCode").value.trim();
  const courseName   = document.getElementById("editTtCourseTitle").value.trim();
  const location     = document.getElementById("editTtLocation").value.trim();
  const dayOfWeek    = document.getElementById("editTtDay").value;
  const startTime    = document.getElementById("editTtStartTime").value;
  const endTime      = document.getElementById("editTtEndTime").value;

  if (id === "") {
    Swal.fire({ icon: "error", title: "Error", text: "Invalid Entry ID.", confirmButtonColor: "#4f46e5" });
    return;
  }
  if (courseCode === "") {
    Swal.fire({ icon: "warning", title: "Validation Error", text: "Please enter the Course Code.", confirmButtonColor: "#4f46e5" });
    return;
  }
  if (courseName === "") {
    Swal.fire({ icon: "warning", title: "Validation Error", text: "Please enter the Course Title.", confirmButtonColor: "#4f46e5" });
    return;
  }
  if (dayOfWeek === "") {
    Swal.fire({ icon: "warning", title: "Validation Error", text: "Please select the Day of Week.", confirmButtonColor: "#4f46e5" });
    return;
  }
  if (startTime === "") {
    Swal.fire({ icon: "warning", title: "Validation Error", text: "Please enter the Start Time.", confirmButtonColor: "#4f46e5" });
    return;
  }
  if (endTime === "") {
    Swal.fire({ icon: "warning", title: "Validation Error", text: "Please enter the End Time.", confirmButtonColor: "#4f46e5" });
    return;
  }

  const startMin = parseTimeToMinutes(startTime);
  const endMin   = parseTimeToMinutes(endTime);

  if (startMin >= endMin) {
    Swal.fire({ icon: "warning", title: "Invalid Time Range", text: "End Time must be after Start Time.", confirmButtonColor: "#4f46e5" });
    return;
  }
  if (startMin < 510) {
    Swal.fire({ icon: "warning", title: "Invalid Time Range", text: "Lectures cannot start before 08:30 AM.", confirmButtonColor: "#4f46e5" });
    return;
  }
  if (endMin > 1110) {
    Swal.fire({ icon: "warning", title: "Invalid Time Range", text: "Lectures cannot end after 06:30 PM.", confirmButtonColor: "#4f46e5" });
    return;
  }
  if (startMin < 810 && endMin > 750) {
    Swal.fire({ icon: "error", title: "Lunch Break Conflict", text: "Cannot schedule lectures during the Lunch Interval (12:30 PM - 01:30 PM).", confirmButtonColor: "#4f46e5" });
    return;
  }

  const form = new FormData();
  form.append("id", id);
  form.append("course_code", courseCode);
  form.append("course_name", courseName);
  form.append("location", location);
  form.append("day_of_week", dayOfWeek);
  form.append("start_time", startTime);
  form.append("end_time", endTime);

  const request = new XMLHttpRequest();
  request.onreadystatechange = function () {
    if (request.readyState === 4 && request.status === 200) {
      const response = request.responseText.trim();
      if (response === "success") {
        Swal.fire({
          icon: "success",
          title: "Updated!",
          text: "Timetable entry updated successfully!",
          timer: 1200,
          showConfirmButton: false,
          willClose: () => {
            window.location.reload();
          }
        }).then(() => {
          window.location.reload();
        });
      } else {
        Swal.fire({ icon: "error", title: "Error", text: response, confirmButtonColor: "#4f46e5" });
      }
    }
  };
  request.open("POST", "api/updateTimetableProcess.php", true);
  request.send(form);
}

function deleteTimetable() {
  const id = document.getElementById("editTtId").value;
  if (id === "") {
    Swal.fire({ icon: "error", title: "Error", text: "Invalid Entry ID.", confirmButtonColor: "#4f46e5" });
    return;
  }

  Swal.fire({
    title: "Delete Timetable Entry?",
    text: "Are you sure you want to delete this timetable entry? This action cannot be undone.",
    icon: "warning",
    showCancelButton: true,
    confirmButtonColor: "#dc2626",
    cancelButtonColor: "#6b7280",
    confirmButtonText: "Yes, Delete",
    cancelButtonText: "Cancel"
  }).then((result) => {
    if (result.isConfirmed) {
      const form = new FormData();
      form.append("id", id);

      const request = new XMLHttpRequest();
      request.onreadystatechange = function () {
        if (request.readyState === 4 && request.status === 200) {
          const response = request.responseText.trim();
          if (response === "success") {
            Swal.fire({
              icon: "success",
              title: "Deleted!",
              text: "Timetable entry deleted successfully!",
              timer: 1200,
              showConfirmButton: false,
              willClose: () => {
                window.location.reload();
              }
            }).then(() => {
              window.location.reload();
            });
          } else {
            Swal.fire({ icon: "error", title: "Error", text: response, confirmButtonColor: "#4f46e5" });
          }
        }
      };
      request.open("POST", "api/deleteTimetableProcess.php", true);
      request.send(form);
    }
  });
}

/* ==========================================
   FACILITIES FUNCTIONS
   ========================================== */

function addFacility() {
  const name           = document.getElementById("facName").value.trim();
  const category       = document.getElementById("facCategory").value;
  const capacity       = document.getElementById("facCapacity").value.trim();
  const locationName   = document.getElementById("facLocation").value.trim();
  const startTime      = document.getElementById("facStartTime") ? document.getElementById("facStartTime").value.trim() : "08:00";
  const endTime        = document.getElementById("facEndTime") ? document.getElementById("facEndTime").value.trim() : "22:00";
  const imageInput     = document.getElementById("facImage");
  const description    = document.getElementById("facDescription") ? document.getElementById("facDescription").value.trim() : "";
  const equipment      = document.getElementById("facEquipment") ? document.getElementById("facEquipment").value.trim() : "";

  if (name === "") {
    Swal.fire({ icon: "warning", title: "Validation Error", text: "Please enter the Facility Name.", confirmButtonColor: "#4f46e5" });
    return;
  }
  if (category === "") {
    Swal.fire({ icon: "warning", title: "Validation Error", text: "Please select a Category.", confirmButtonColor: "#4f46e5" });
    return;
  }
  if (capacity === "" || parseInt(capacity, 10) <= 0) {
    Swal.fire({ icon: "warning", title: "Validation Error", text: "Please enter a valid Capacity.", confirmButtonColor: "#4f46e5" });
    return;
  }
  if (locationName === "") {
    Swal.fire({ icon: "warning", title: "Validation Error", text: "Please enter the Location.", confirmButtonColor: "#4f46e5" });
    return;
  }

  const form = new FormData();
  form.append("name", name);
  form.append("category", category);
  form.append("capacity", capacity);
  form.append("location", locationName);
  form.append("start_time", startTime);
  form.append("end_time", endTime);
  if (imageInput && imageInput.files.length > 0) {
    form.append("image", imageInput.files[0]);
  }
  form.append("description", description);
  form.append("equipment", equipment);

  const request = new XMLHttpRequest();
  request.onreadystatechange = function () {
    if (request.readyState === 4 && request.status === 200) {
      const response = request.responseText.trim();
      if (response === "success") {
        Swal.fire({
          icon: "success",
          title: "Facility Added!",
          text: "New facility added successfully!",
          timer: 1500,
          showConfirmButton: false
        }).then(() => {
          location.reload();
        });
      } else {
        Swal.fire({ icon: "error", title: "Error", text: response, confirmButtonColor: "#4f46e5" });
      }
    }
  };
  request.open("POST", "api/addFacilityProcess.php", true);
  request.send(form);
}

/* ==========================================
   BOOKING CONFIRMATION FUNCTIONS
   ========================================== */

function formatTime12h(timeStr) {
  if (!timeStr) return "";
  const parts = timeStr.split(":");
  let h = parseInt(parts[0], 10);
  const m = parts[1] || "00";
  const ampm = h >= 12 ? "PM" : "AM";
  h = h % 12;
  h = h ? h : 12;
  return `${h}:${m} ${ampm}`;
}

function submitBooking() {
  const facilityIdEl    = document.getElementById("facilityId");
  const bookingDateEl  = document.getElementById("bookingDate");
  const startTimeEl    = document.getElementById("startTime");
  const endTimeEl      = document.getElementById("endTime");
  const purposeEl      = document.getElementById("purpose");
  const attendeesEl    = document.getElementById("attendees");
  const notesEl        = document.getElementById("notes");
  const capacityEl     = document.getElementById("facilityCapacity");
  const facStartEl     = document.getElementById("facilityStartTime");
  const facEndEl       = document.getElementById("facilityEndTime");

  const facilityId  = facilityIdEl ? facilityIdEl.value : "0";
  const bookingDate = bookingDateEl ? bookingDateEl.value.trim() : "";
  const startTime   = startTimeEl ? startTimeEl.value.trim() : "";
  const endTime     = endTimeEl ? endTimeEl.value.trim() : "";
  const purpose     = purposeEl ? purposeEl.value.trim() : "";
  const attendees   = attendeesEl ? parseInt(attendeesEl.value, 10) || 0 : 0;
  const notes       = notesEl ? notesEl.value.trim() : "";
  const maxCapacity = capacityEl ? parseInt(capacityEl.value, 10) || 10 : 10;
  const facStart    = facStartEl ? facStartEl.value.trim() : "08:00";
  const facEnd      = facEndEl ? facEndEl.value.trim() : "22:00";

  if (!bookingDate) {
    Swal.fire({ icon: "warning", title: "Validation Error", text: "Please select a Booking Date.", confirmButtonColor: "#4f46e5" });
    return;
  }

  const today = new Date().toISOString().split("T")[0];
  if (bookingDate < today) {
    Swal.fire({ icon: "warning", title: "Validation Error", text: "Booking Date cannot be in the past.", confirmButtonColor: "#4f46e5" });
    return;
  }

  if (!startTime) {
    Swal.fire({ icon: "warning", title: "Validation Error", text: "Please select a Start Time.", confirmButtonColor: "#4f46e5" });
    return;
  }

  if (!endTime) {
    Swal.fire({ icon: "warning", title: "Validation Error", text: "Please select an End Time.", confirmButtonColor: "#4f46e5" });
    return;
  }

  const startMin = parseTimeToMinutes(startTime);
  const endMin   = parseTimeToMinutes(endTime);

  if (startMin >= endMin) {
    Swal.fire({ icon: "warning", title: "Invalid Time Range", text: "End Time must be after Start Time.", confirmButtonColor: "#4f46e5" });
    return;
  }

  const opStartMin = parseTimeToMinutes(facStart);
  const opEndMin   = parseTimeToMinutes(facEnd);

  const displayFacStart = formatTime12h(facStart);
  const displayFacEnd   = formatTime12h(facEnd);
  const userStartDisp   = formatTime12h(startTime);
  const userEndDisp     = formatTime12h(endTime);

  if (startMin < opStartMin) {
    Swal.fire({
      icon: "error",
      title: "Outside Operating Hours",
      text: `Selected Start Time (${userStartDisp}) is earlier than facility opening time (${displayFacStart}). Operating hours are ${displayFacStart} – ${displayFacEnd} (${facStart} – ${facEnd}).`,
      confirmButtonColor: "#4f46e5"
    });
    return;
  }

  if (endMin > opEndMin) {
    Swal.fire({
      icon: "error",
      title: "Outside Operating Hours",
      text: `Selected End Time (${userEndDisp}) is later than facility closing time (${displayFacEnd}). Operating hours are ${displayFacStart} – ${displayFacEnd} (${facStart} – ${facEnd}).`,
      confirmButtonColor: "#4f46e5"
    });
    return;
  }

  if (!purpose) {
    Swal.fire({ icon: "warning", title: "Validation Error", text: "Please select the Purpose of Booking.", confirmButtonColor: "#4f46e5" });
    return;
  }

  if (attendees <= 0) {
    Swal.fire({ icon: "warning", title: "Validation Error", text: "Please enter a valid number of attendees.", confirmButtonColor: "#4f46e5" });
    return;
  }

  if (attendees > maxCapacity) {
    Swal.fire({
      icon: "warning",
      title: "Capacity Exceeded",
      text: `Number of attendees (${attendees}) exceeds maximum facility capacity (${maxCapacity} persons).`,
      confirmButtonColor: "#4f46e5"
    });
    return;
  }

  const form = new FormData();
  form.append("facility_id", facilityId);
  form.append("booking_date", bookingDate);
  form.append("start_time", startTime);
  form.append("end_time", endTime);
  form.append("purpose", purpose);
  form.append("attendees", attendees);
  form.append("notes", notes);

  const request = new XMLHttpRequest();
  request.onreadystatechange = function () {
    if (request.readyState === 4 && request.status === 200) {
      const response = request.responseText.trim();
      if (response.startsWith("success")) {
        const parts = response.split(":");
        const bookingId = parts[1] ? parts[1].trim() : "";
        Swal.fire({
          icon: "success",
          title: "Booking Submitted!",
          text: "Your booking request has been submitted successfully.",
          timer: 1800,
          showConfirmButton: false
        }).then(() => {
          window.location.href = bookingId ? `booking-success.php?id=${bookingId}` : "booking-success.php";
        });
      } else {
        Swal.fire({ icon: "error", title: "Booking Error", text: response, confirmButtonColor: "#4f46e5" });
      }
    }
  };
  request.open("POST", "api/addBookingProcess.php", true);
  request.send(form);
}

/* ==========================================
   SERVICE REQUEST FUNCTIONS
   ========================================== */

function submitServiceRequest() {
  const locationEl = document.getElementById("srLocation");
  const titleEl    = document.getElementById("srTitle");
  const descEl     = document.getElementById("srDescription");
  const priorityEl = document.getElementById("srPriority");

  const location    = locationEl ? locationEl.value.trim() : "";
  const title       = titleEl ? titleEl.value.trim() : "";
  const description = descEl ? descEl.value.trim() : "";
  const priority    = priorityEl ? priorityEl.value : "Medium";

  if (!location) {
    Swal.fire({ icon: "warning", title: "Validation Error", text: "Please enter the location on campus.", confirmButtonColor: "#4f46e5" });
    return;
  }

  if (!title) {
    Swal.fire({ icon: "warning", title: "Validation Error", text: "Please enter an issue title / summary.", confirmButtonColor: "#4f46e5" });
    return;
  }

  if (!description) {
    Swal.fire({ icon: "warning", title: "Validation Error", text: "Please enter a detailed description of the issue.", confirmButtonColor: "#4f46e5" });
    return;
  }

  const form = new FormData();
  form.append("location", location);
  form.append("title", title);
  form.append("description", description);
  form.append("priority", priority);

  const request = new XMLHttpRequest();
  request.onreadystatechange = function () {
    if (request.readyState === 4 && request.status === 200) {
      const response = request.responseText.trim();
      if (response.startsWith("success")) {
        const parts = response.split(":");
        const srId = parts[1] ? parts[1].trim() : "";
        Swal.fire({
          icon: "success",
          title: "Service Request Submitted!",
          text: "Your issue report has been submitted to campus operations desk.",
          timer: 1800,
          showConfirmButton: false
        }).then(() => {
          window.location.href = srId ? `request-detail.php?id=${srId}` : "service-requests.php";
        });
      } else {
        Swal.fire({ icon: "error", title: "Submission Error", text: response, confirmButtonColor: "#4f46e5" });
      }
    }
  };
  request.open("POST", "api/addServiceRequestProcess.php", true);
  request.send(form);
}

// ----------------------------------------------------
// Event Management (Add, Edit, Delete, Filter)
// ----------------------------------------------------

function openAddEventModal() {
  const modalEl = document.getElementById('eventModal');
  if (!modalEl) return;
  document.getElementById('eventModalTitle').textContent = 'Add New Event';
  document.getElementById('eventId').value = '';
  document.getElementById('eventTitle').value = '';
  document.getElementById('eventCategory').value = 'Academic';
  document.getElementById('eventDate').value = '';
  document.getElementById('eventStartTime').value = '';
  document.getElementById('eventEndTime').value = '';
  document.getElementById('eventLocation').value = '';
  document.getElementById('eventDescription').value = '';
  
  const bsModal = new bootstrap.Modal(modalEl);
  bsModal.show();
}

function openEditEventModal(id, title, category, date, startTime, endTime, location, description) {
  const modalEl = document.getElementById('eventModal');
  if (!modalEl) return;
  document.getElementById('eventModalTitle').textContent = 'Edit Event';
  document.getElementById('eventId').value = id;
  document.getElementById('eventTitle').value = title;
  document.getElementById('eventCategory').value = category || 'Academic';
  document.getElementById('eventDate').value = date;
  document.getElementById('eventStartTime').value = startTime || '';
  document.getElementById('eventEndTime').value = endTime || '';
  document.getElementById('eventLocation').value = location || '';
  document.getElementById('eventDescription').value = description || '';

  const bsModal = bootstrap.Modal.getInstance(modalEl) || new bootstrap.Modal(modalEl);
  bsModal.show();
}

function saveEvent() {
  const id = document.getElementById('eventId').value;
  const title = document.getElementById('eventTitle').value.trim();
  const category = document.getElementById('eventCategory').value;
  const date = document.getElementById('eventDate').value;
  const startTime = document.getElementById('eventStartTime').value;
  const endTime = document.getElementById('eventEndTime').value;
  const location = document.getElementById('eventLocation').value.trim();
  const description = document.getElementById('eventDescription').value.trim();

  if (!title) {
    Swal.fire({ icon: 'warning', title: 'Missing Title', text: 'Please enter an event title.', confirmButtonColor: '#4f46e5' });
    return;
  }
  if (!date) {
    Swal.fire({ icon: 'warning', title: 'Missing Date', text: 'Please select an event date.', confirmButtonColor: '#4f46e5' });
    return;
  }

  const formData = new FormData();
  formData.append('title', title);
  formData.append('category', category);
  formData.append('date', date);
  formData.append('start_time', startTime);
  formData.append('end_time', endTime);
  formData.append('location', location);
  formData.append('description', description);

  let targetUrl = 'api/addEventProcess.php';
  if (id) {
    formData.append('id', id);
    targetUrl = 'api/editEventProcess.php';
  }

  const xhr = new XMLHttpRequest();
  xhr.onreadystatechange = function () {
    if (xhr.readyState === 4) {
      if (xhr.status === 200) {
        try {
          const res = JSON.parse(xhr.responseText);
          if (res.status === 'success') {
            Swal.fire({
              icon: 'success',
              title: id ? 'Event Updated!' : 'Event Created!',
              text: res.message,
              timer: 1500,
              showConfirmButton: false
            }).then(() => {
              window.location.reload();
            });
          } else {
            Swal.fire({ icon: 'error', title: 'Error', text: res.message, confirmButtonColor: '#4f46e5' });
          }
        } catch (e) {
          Swal.fire({ icon: 'error', title: 'Error', text: 'Failed to process server response.', confirmButtonColor: '#4f46e5' });
        }
      } else {
        Swal.fire({ icon: 'error', title: 'Server Error', text: 'Network request failed.', confirmButtonColor: '#4f46e5' });
      }
    }
  };
  xhr.open('POST', targetUrl, true);
  xhr.send(formData);
}

function deleteEvent(id) {
  if (!id) return;
  Swal.fire({
    title: 'Delete Event?',
    text: 'Are you sure you want to remove this event bulletin?',
    icon: 'warning',
    showCancelButton: true,
    confirmButtonColor: '#ef4444',
    cancelButtonColor: '#6b7280',
    confirmButtonText: 'Yes, delete it!'
  }).then((result) => {
    if (result.isConfirmed) {
      const formData = new FormData();
      formData.append('id', id);

      const xhr = new XMLHttpRequest();
      xhr.onreadystatechange = function () {
        if (xhr.readyState === 4 && xhr.status === 200) {
          try {
            const res = JSON.parse(xhr.responseText);
            if (res.status === 'success') {
              Swal.fire({
                icon: 'success',
                title: 'Deleted!',
                text: res.message,
                timer: 1500,
                showConfirmButton: false
              }).then(() => {
                window.location.reload();
              });
            } else {
              Swal.fire({ icon: 'error', title: 'Error', text: res.message, confirmButtonColor: '#4f46e5' });
            }
          } catch (e) {
            Swal.fire({ icon: 'error', title: 'Error', text: 'Failed to process server response.', confirmButtonColor: '#4f46e5' });
          }
        }
      };
      xhr.open('POST', 'api/deleteEventProcess.php', true);
      xhr.send(formData);
    }
  });
}

function filterEvents(category, btn) {
  const cards = document.querySelectorAll('.event-card');
  const buttons = document.querySelectorAll('.category-filter-btn');

  buttons.forEach(b => {
    b.classList.remove('btn-su-indigo');
    b.classList.add('btn-su-outline');
  });
  if (btn) {
    btn.classList.remove('btn-su-outline');
    btn.classList.add('btn-su-indigo');
  }

  cards.forEach(card => {
    const cardCat = card.getAttribute('data-category');
    if (category === 'All' || cardCat.toLowerCase() === category.toLowerCase()) {
      card.style.display = 'block';
    } else {
      card.style.display = 'none';
    }
  });
}

// ----------------------------------------------------
// Staff Booking Approvals
// ----------------------------------------------------

function updateBookingStatus(bookingId, action) {
  if (!bookingId || !action) return;
  const actionText = action === 'approve' ? 'Approve' : 'Reject';
  const confirmColor = action === 'approve' ? '#10b981' : '#ef4444';

  Swal.fire({
    title: `${actionText} Booking?`,
    text: `Are you sure you want to ${action} booking #BK-${String(bookingId).padStart(4, '0')}?`,
    icon: action === 'approve' ? 'question' : 'warning',
    showCancelButton: true,
    confirmButtonColor: confirmColor,
    cancelButtonColor: '#6b7280',
    confirmButtonText: `Yes, ${action}!`
  }).then((result) => {
    if (result.isConfirmed) {
      const formData = new FormData();
      formData.append('booking_id', bookingId);
      formData.append('action', action);

      const xhr = new XMLHttpRequest();
      xhr.onreadystatechange = function () {
        if (xhr.readyState === 4 && xhr.status === 200) {
          try {
            const res = JSON.parse(xhr.responseText);
            if (res.status === 'success') {
              Swal.fire({
                icon: 'success',
                title: 'Updated!',
                text: res.message,
                timer: 1500,
                showConfirmButton: false
              }).then(() => {
                window.location.reload();
              });
            } else {
              Swal.fire({ icon: 'error', title: 'Error', text: res.message, confirmButtonColor: '#4f46e5' });
            }
          } catch (e) {
            Swal.fire({ icon: 'error', title: 'Error', text: 'Failed to process server response.', confirmButtonColor: '#4f46e5' });
          }
        }
      };
      xhr.open('POST', 'api/updateBookingStatusProcess.php', true);
      xhr.send(formData);
    }
  });
}

function filterApprovalRows(status, btn) {
  const rows = document.querySelectorAll('.approval-row');
  const tabs = document.querySelectorAll('.approval-tab-btn');

  tabs.forEach(t => {
    t.classList.remove('active', 'fw-bold', 'text-primary');
    t.classList.add('text-secondary');
  });

  if (btn) {
    btn.classList.add('active', 'fw-bold', 'text-primary');
    btn.classList.remove('text-secondary');
  }

  rows.forEach(row => {
    const rowStatus = row.getAttribute('data-status');
    if (status === 'All' || rowStatus.toLowerCase() === status.toLowerCase()) {
      row.style.display = '';
    } else {
      row.style.display = 'none';
    }
  });
}

// ----------------------------------------------------
// Booking Detail Modal & PDF Export
// ----------------------------------------------------

let currentSelectedBooking = null;

function showBookingDetails(data) {
  currentSelectedBooking = data;
  const modalEl = document.getElementById('bookingDetailModal');
  if (!modalEl) return;

  document.getElementById('modalRefNo').textContent = '#' + data.ref;
  const badgeEl = document.getElementById('modalStatusBadge');
  badgeEl.className = 'su-badge ' + data.badgeClass;
  badgeEl.textContent = '● ' + data.statusName;

  document.getElementById('modalFacilityName').textContent = data.facilityName;
  document.getElementById('modalFacilityLocation').textContent = data.location || 'N/A';

  document.getElementById('modalRequesterName').textContent = data.requester;
  document.getElementById('modalRequesterEmail').textContent = data.email || 'N/A';
  document.getElementById('modalRequesterReg').textContent = data.regNum || 'N/A';

  document.getElementById('modalDateTime').textContent = data.date + ' (' + data.time + ')';
  document.getElementById('modalCapacity').textContent = (data.capacity || 1) + ' persons';
  document.getElementById('modalPurpose').textContent = data.purpose || 'General Booking';
  document.getElementById('modalNotes').textContent = data.notes || 'None';
  document.getElementById('modalCreatedAt').textContent = data.createdAt || 'N/A';

  const actionsContainer = document.getElementById('modalActionsContainer');
  if (actionsContainer) {
    let actionBtnsHtml = '';
    if (data.statusId !== 8) {
      actionBtnsHtml += `<button class="btn btn-sm btn-success rounded-3 px-3 me-2" onclick="updateBookingStatus(${data.id}, 'approve')"><i class="bi bi-check-lg me-1"></i> Approve</button>`;
    }
    if (data.statusId !== 9) {
      actionBtnsHtml += `<button class="btn btn-sm btn-outline-danger rounded-3 px-3" onclick="updateBookingStatus(${data.id}, 'reject')"><i class="bi bi-x-lg me-1"></i> Reject</button>`;
    }
    actionsContainer.innerHTML = actionBtnsHtml;
  }

  const bsModal = bootstrap.Modal.getInstance(modalEl) || new bootstrap.Modal(modalEl);
  bsModal.show();
}

function exportSingleBookingPDF(data) {
  const bk = data || currentSelectedBooking;
  if (!bk) return;

  const printWin = window.open('', '_blank', 'width=800,height=900');
  const htmlContent = `
    <!DOCTYPE html>
    <html>
    <head>
      <title>Booking Approval Slip - #${bk.ref}</title>
      <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
      <style>
        body { font-family: system-ui, -apple-system, sans-serif; padding: 40px; color: #1e293b; }
        .invoice-header { border-bottom: 2px solid #4f46e5; padding-bottom: 20px; margin-bottom: 30px; }
        .logo-title { font-weight: 800; font-size: 24px; color: #4f46e5; }
        .detail-label { font-size: 12px; font-weight: 600; text-transform: uppercase; color: #64748b; margin-bottom: 4px; }
        .detail-value { font-size: 15px; font-weight: 600; color: #0f172a; margin-bottom: 20px; }
        .badge-box { display: inline-block; padding: 6px 12px; border-radius: 6px; font-weight: 700; font-size: 13px; }
        @media print {
          body { padding: 0; }
          .no-print { display: none !important; }
        }
      </style>
    </head>
    <body>
      <div class="d-flex justify-content-between align-items-center mb-4 no-print">
        <button class="btn btn-primary" onclick="window.print()"><i class="bi bi-printer me-1"></i> Save as PDF / Print</button>
        <button class="btn btn-secondary" onclick="window.close()">Close Window</button>
      </div>

      <div class="invoice-header d-flex justify-content-between align-items-center">
        <div>
          <div class="logo-title">SmartUni Portal</div>
          <div class="text-muted small">Facility Booking Approval Slip</div>
        </div>
        <div class="text-end">
          <h4 class="fw-bold mb-0">#${bk.ref}</h4>
          <div class="text-muted small">Generated: ${new Date().toLocaleDateString()}</div>
        </div>
      </div>

      <div class="row">
        <div class="col-6">
          <div class="detail-label">Facility Details</div>
          <div class="detail-value">${bk.facilityName}<br><span class="text-muted fw-normal">${bk.location || ''}</span></div>

          <div class="detail-label">Requester Information</div>
          <div class="detail-value">${bk.requester}<br><span class="text-muted fw-normal">Email: ${bk.email || 'N/A'} | ID: ${bk.regNum || 'N/A'}</span></div>
        </div>

        <div class="col-6">
          <div class="detail-label">Date &amp; Time Schedule</div>
          <div class="detail-value">${bk.date}<br><span class="text-muted fw-normal">${bk.time}</span></div>

          <div class="detail-label">Current Status</div>
          <div class="detail-value"><span class="badge-box bg-light border text-dark">${bk.statusName}</span></div>
        </div>
      </div>

      <div class="border-top pt-3 mt-2">
        <div class="detail-label">Purpose of Booking</div>
        <div class="detail-value mb-3">${bk.purpose || 'General Booking'}</div>

        <div class="detail-label">Additional Notes / Remarks</div>
        <div class="detail-value">${bk.notes || 'None'}</div>
      </div>

      <div class="mt-5 pt-4 border-top text-muted small d-flex justify-content-between align-items-center">
        <span>SmartUni Campus Operations &amp; Space Management System</span>
        <span>Ref #${bk.ref}</span>
      </div>

      <script>
        window.onload = function() {
          setTimeout(function() { window.print(); }, 500);
        };
      </script>
    </body>
    </html>
  `;

  printWin.document.write(htmlContent);
  printWin.document.close();
}

function exportApprovalsListPDF() {
  const printWin = window.open('', '_blank', 'width=900,height=900');
  const tableEl = document.querySelector('.su-table');
  const tableContent = tableEl ? tableEl.outerHTML : '<p>No data available</p>';

  const htmlContent = `
    <!DOCTYPE html>
    <html>
    <head>
      <title>SmartUni - Booking Approvals List Export</title>
      <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
      <style>
        body { font-family: system-ui, -apple-system, sans-serif; padding: 30px; color: #1e293b; }
        .logo-title { font-weight: 800; font-size: 22px; color: #4f46e5; }
        table { width: 100%; border-collapse: collapse; margin-top: 20px; }
        th, td { border: 1px solid #e2e8f0; padding: 10px; font-size: 13px; }
        th { background-color: #f8fafc; font-weight: 700; }
        .btn, .actions-column { display: none !important; }
        @media print {
          body { padding: 0; }
          .no-print { display: none !important; }
        }
      </style>
    </head>
    <body>
      <div class="d-flex justify-content-between align-items-center mb-4 no-print">
        <button class="btn btn-primary" onclick="window.print()"><i class="bi bi-printer me-1"></i> Save as PDF / Print</button>
        <button class="btn btn-secondary" onclick="window.close()">Close Window</button>
      </div>

      <div class="d-flex justify-content-between align-items-center border-bottom pb-3">
        <div>
          <div class="logo-title">SmartUni Portal</div>
          <div class="text-muted small">Campus Facility Booking Approvals Report</div>
        </div>
        <div class="text-end text-muted small">
          Exported: ${new Date().toLocaleString()}
        </div>
      </div>

      <div class="mt-3">
        ${tableContent}
      </div>

      <script>
        window.onload = function() {
          setTimeout(function() { window.print(); }, 500);
        };
      </script>
    </body>
    </html>
  `;

  printWin.document.write(htmlContent);
  printWin.document.close();
}

// ----------------------------------------------------
// Profile & Settings Management
// ----------------------------------------------------

function previewProfilePic(event) {
  const file = event.target.files[0];
  if (file) {
    const reader = new FileReader();
    reader.onload = function (e) {
      const imgEl = document.getElementById('profileAvatarPreview');
      if (imgEl) {
        imgEl.src = e.target.result;
      }
    };
    reader.readAsDataURL(file);
  }
}

function updateUserProfile() {
  const fname = document.getElementById('profileFname').value.trim();
  const lname = document.getElementById('profileLname').value.trim();
  const email = document.getElementById('profileEmail').value.trim();
  const mobile = document.getElementById('profileMobile').value.trim();
  const picInput = document.getElementById('profilePicInput');

  if (!fname || !lname) {
    Swal.fire({ icon: 'warning', title: 'Missing Info', text: 'First name and Last name are required.', confirmButtonColor: '#4f46e5' });
    return;
  }

  if (!email) {
    Swal.fire({ icon: 'warning', title: 'Missing Email', text: 'Email address is required.', confirmButtonColor: '#4f46e5' });
    return;
  }

  const formData = new FormData();
  formData.append('fname', fname);
  formData.append('lname', lname);
  formData.append('email', email);
  formData.append('mobile', mobile);

  if (picInput && picInput.files && picInput.files[0]) {
    formData.append('profile_pic', picInput.files[0]);
  }

  const xhr = new XMLHttpRequest();
  xhr.onreadystatechange = function () {
    if (xhr.readyState === 4 && xhr.status === 200) {
      try {
        const res = JSON.parse(xhr.responseText);
        if (res.status === 'success') {
          Swal.fire({
            icon: 'success',
            title: 'Profile Updated!',
            text: res.message,
            timer: 1500,
            showConfirmButton: false
          }).then(() => {
            window.location.reload();
          });
        } else {
          Swal.fire({ icon: 'error', title: 'Update Error', text: res.message, confirmButtonColor: '#4f46e5' });
        }
      } catch (e) {
        Swal.fire({ icon: 'error', title: 'Error', text: 'Failed to process server response.', confirmButtonColor: '#4f46e5' });
      }
    }
  };
  xhr.open('POST', 'api/updateProfileProcess.php', true);
  xhr.send(formData);
}

// ----------------------------------------------------
// User Management & Role/Status Edits
// ----------------------------------------------------

function openEditUserModal(user) {
  const modalEl = document.getElementById('editUserModal');
  if (!modalEl) return;

  document.getElementById('editUserId').value = user.id;
  document.getElementById('editUserFname').value = user.fname;
  document.getElementById('editUserLname').value = user.lname;
  document.getElementById('editUserEmail').value = user.email;
  document.getElementById('editUserReg').value = user.reg_number || '';
  document.getElementById('editUserMobile').value = user.mobile || '';
  document.getElementById('editUserRole').value = user.role_id;
  document.getElementById('editUserStatus').value = user.status_id;

  const bsModal = bootstrap.Modal.getInstance(modalEl) || new bootstrap.Modal(modalEl);
  bsModal.show();
}

function saveUserEdit() {
  const userId = document.getElementById('editUserId').value;
  const fname = document.getElementById('editUserFname').value.trim();
  const lname = document.getElementById('editUserLname').value.trim();
  const mobile = document.getElementById('editUserMobile').value.trim();
  const regNumber = document.getElementById('editUserReg').value.trim();
  const roleId = document.getElementById('editUserRole').value;
  const statusId = document.getElementById('editUserStatus').value;

  if (!fname || !lname) {
    Swal.fire({ icon: 'warning', title: 'Missing Info', text: 'First name and Last name are required.', confirmButtonColor: '#4f46e5' });
    return;
  }

  const formData = new FormData();
  formData.append('user_id', userId);
  formData.append('fname', fname);
  formData.append('lname', lname);
  formData.append('mobile', mobile);
  formData.append('reg_number', regNumber);
  formData.append('role_id', roleId);
  formData.append('status_id', statusId);

  const xhr = new XMLHttpRequest();
  xhr.onreadystatechange = function () {
    if (xhr.readyState === 4 && xhr.status === 200) {
      try {
        const res = JSON.parse(xhr.responseText);
        if (res.status === 'success') {
          Swal.fire({
            icon: 'success',
            title: 'User Saved!',
            text: res.message,
            timer: 1500,
            showConfirmButton: false
          }).then(() => {
            window.location.reload();
          });
        } else {
          Swal.fire({ icon: 'error', title: 'Error', text: res.message, confirmButtonColor: '#4f46e5' });
        }
      } catch (e) {
        Swal.fire({ icon: 'error', title: 'Error', text: xhr.responseText ? xhr.responseText : 'Failed to process server response.', confirmButtonColor: '#4f46e5' });
      }
    }
  };
  xhr.open('POST', 'api/updateUserProcess.php', true);
  xhr.send(formData);
}

function filterUserRows(filterVal, btn) {
  const rows = document.querySelectorAll('.user-table-row');
  const buttons = document.querySelectorAll('.user-filter-btn');

  buttons.forEach(b => {
    b.classList.remove('btn-su-indigo');
    b.classList.add('btn-su-outline');
  });
  if (btn) {
    btn.classList.remove('btn-su-outline');
    btn.classList.add('btn-su-indigo');
  }

  rows.forEach(row => {
    const role = row.getAttribute('data-role');
    const status = row.getAttribute('data-status');
    if (filterVal === 'All' || role.toLowerCase() === filterVal.toLowerCase() || status.toLowerCase() === filterVal.toLowerCase()) {
      row.style.display = '';
    } else {
      row.style.display = 'none';
    }
  });
}

function searchUsersTable() {
  const input = document.getElementById('userSearchInput').value.toLowerCase();
  const rows = document.querySelectorAll('.user-table-row');

  rows.forEach(row => {
    const text = row.textContent.toLowerCase();
    if (text.includes(input)) {
      row.style.display = '';
    } else {
      row.style.display = 'none';
    }
  });
}

/* ==========================================
   USER MANAGEMENT FUNCTIONS (users-list.php)
   ========================================== */
function filterUserRows(role, btn) {
  const rows = document.querySelectorAll('.user-table-row');
  const buttons = document.querySelectorAll('.user-filter-btn');

  buttons.forEach(b => {
    b.classList.remove('btn-su-indigo');
    b.classList.add('btn-su-outline');
  });
  if (btn) {
    btn.classList.remove('btn-su-outline');
    btn.classList.add('btn-su-indigo');
  }

  rows.forEach(row => {
    const rowRole = row.getAttribute('data-role');
    if (role === 'All' || (rowRole && rowRole.toLowerCase() === role.toLowerCase())) {
      row.style.display = '';
    } else {
      row.style.display = 'none';
    }
  });
}

function searchUsersTable() {
  const inputEl = document.getElementById('userSearchInput');
  if (!inputEl) return;
  const input = inputEl.value.toLowerCase().trim();
  const rows = document.querySelectorAll('.user-table-row');

  rows.forEach(row => {
    const text = row.textContent.toLowerCase();
    if (!input || text.includes(input)) {
      row.style.display = '';
    } else {
      row.style.display = 'none';
    }
  });
}

function openEditUserModal(userObj) {
  const modalEl = document.getElementById('editUserModal');
  if (!modalEl || !userObj) return;

  document.getElementById('editUserId').value = userObj.id || '';
  document.getElementById('editUserFname').value = userObj.fname || '';
  document.getElementById('editUserLname').value = userObj.lname || '';
  document.getElementById('editUserEmail').value = userObj.email || '';
  document.getElementById('editUserReg').value = userObj.reg_number || '';
  document.getElementById('editUserMobile').value = userObj.mobile || '';
  document.getElementById('editUserRole').value = userObj.role_id || '1';
  document.getElementById('editUserStatus').value = userObj.status_id || '1';

  const bsModal = bootstrap.Modal.getInstance(modalEl) || new bootstrap.Modal(modalEl);
  bsModal.show();
}

function saveUserEdit() {
  const userId = document.getElementById('editUserId').value;
  const fname = document.getElementById('editUserFname').value.trim();
  const lname = document.getElementById('editUserLname').value.trim();
  const regNumber = document.getElementById('editUserReg').value.trim();
  const mobile = document.getElementById('editUserMobile').value.trim();
  const roleId = document.getElementById('editUserRole').value;
  const statusId = document.getElementById('editUserStatus').value;

  if (!fname || !lname) {
    if (typeof Swal !== 'undefined') {
      Swal.fire({ icon: 'warning', title: 'Missing Information', text: 'First name and Last name are required.', confirmButtonColor: '#4f46e5' });
    } else {
      alert('First name and Last name are required.');
    }
    return;
  }

  const formData = new FormData();
  formData.append('user_id', userId);
  formData.append('fname', fname);
  formData.append('lname', lname);
  formData.append('reg_number', regNumber);
  formData.append('mobile', mobile);
  formData.append('role_id', roleId);
  formData.append('status_id', statusId);

  const xhr = new XMLHttpRequest();
  xhr.onreadystatechange = function () {
    if (xhr.readyState === 4 && xhr.status === 200) {
      try {
        const res = JSON.parse(xhr.responseText);
        if (res.status === 'success') {
          if (typeof Swal !== 'undefined') {
            Swal.fire({
              icon: 'success',
              title: 'User Updated!',
              text: res.message,
              timer: 1500,
              showConfirmButton: false
            }).then(() => {
              window.location.reload();
            });
          } else {
            alert(res.message);
            window.location.reload();
          }
        } else {
          if (typeof Swal !== 'undefined') {
            Swal.fire({ icon: 'error', title: 'Error', text: res.message, confirmButtonColor: '#4f46e5' });
          } else {
            alert(res.message);
          }
        }
      } catch (e) {
        if (typeof Swal !== 'undefined') {
          Swal.fire({ icon: 'error', title: 'Error', text: 'Failed to process server response.', confirmButtonColor: '#4f46e5' });
        } else {
          alert('Failed to process server response.');
        }
      }
    }
  };
  xhr.open('POST', 'api/updateUserProcess.php', true);
  xhr.send(formData);
}

// ----------------------------------------------------
// Authentication (Login & Register Process Handlers)
// ----------------------------------------------------

function login() {
  const emailInput = document.getElementById("campusEmailInput");
  const passwordInput = document.getElementById("passwordInput");
  const rememberCheck = document.getElementById("rememberMeCheck");
  const msgDiv = document.getElementById("msgDiv");
  const msgText = document.getElementById("msgText");

  if (!emailInput || !passwordInput) return;

  const email = emailInput.value.trim();
  const password = passwordInput.value;
  const rememberme = rememberCheck ? rememberCheck.checked : false;

  if (msgDiv) msgDiv.classList.add("d-none");

  const form = new FormData();
  form.append("email", email);
  form.append("password", password);
  form.append("rememberme", rememberme);

  const xhr = new XMLHttpRequest();
  xhr.onreadystatechange = function () {
    if (xhr.readyState === 4) {
      if (xhr.status === 200) {
        const res = xhr.responseText.trim();
        if (res === "success") {
          window.location.href = "dashboard.php";
        } else if (res === "pending_verification") {
          window.location.href = "verification-pending.php";
        } else {
          if (msgDiv && msgText) {
            msgText.textContent = res;
            msgDiv.classList.remove("d-none");
          } else {
            alert(res);
          }
        }
      } else {
        if (msgDiv && msgText) {
          msgText.textContent = "Network error. Please try again.";
          msgDiv.classList.remove("d-none");
        }
      }
    }
  };
  xhr.open("POST", "api/loginProcess.php", true);
  xhr.send(form);
}

function register() {
  const roleEl = document.getElementById("role");
  const fnameEl = document.getElementById("fname");
  const lnameEl = document.getElementById("lname");
  const mobileEl = document.getElementById("mobile");
  const regNumberEl = document.getElementById("regNumber");
  const emailEl = document.getElementById("email");
  const passwordEl = document.getElementById("password");
  const confirmPasswordEl = document.getElementById("confirmPassword");
  const msgDiv = document.getElementById("msgDiv");
  const msgText = document.getElementById("msgText");

  if (msgDiv) msgDiv.classList.add("d-none");

  if (passwordEl && confirmPasswordEl && passwordEl.value !== confirmPasswordEl.value) {
    if (msgDiv && msgText) {
      msgText.textContent = "Passwords do not match.";
      msgDiv.classList.remove("d-none");
    } else {
      alert("Passwords do not match.");
    }
    return;
  }

  const form = new FormData();
  form.append("role_id", roleEl ? roleEl.value : "1");
  form.append("fname", fnameEl ? fnameEl.value.trim() : "");
  form.append("lname", lnameEl ? lnameEl.value.trim() : "");
  form.append("mobile", mobileEl ? mobileEl.value.trim() : "");
  form.append("reg_number", regNumberEl ? regNumberEl.value.trim() : "");
  form.append("email", emailEl ? emailEl.value.trim() : "");
  form.append("password", passwordEl ? passwordEl.value : "");

  const xhr = new XMLHttpRequest();
  xhr.onreadystatechange = function () {
    if (xhr.readyState === 4) {
      if (xhr.status === 200) {
        const res = xhr.responseText.trim();
        if (res === "success") {
          alert("Account created successfully! Please sign in with your credentials.");
          window.location.href = "index.php";
        } else {
          if (msgDiv && msgText) {
            msgText.textContent = res;
            msgDiv.classList.remove("d-none");
          } else {
            alert(res);
          }
        }
      }
    }
  };
  xhr.open("POST", "api/registerProcess.php", true);
  xhr.send(form);
}

function updateServiceRequestStatus(srId, statusId) {
  if (!srId || !statusId) return;

  const form = new FormData();
  form.append("request_id", srId);
  form.append("status_id", statusId);

  const xhr = new XMLHttpRequest();
  xhr.onreadystatechange = function () {
    if (xhr.readyState === 4) {
      if (xhr.status === 200) {
        const res = xhr.responseText.trim();
        if (res === "success") {
          if (typeof Swal !== "undefined") {
            Swal.fire({
              icon: "success",
              title: "Status Updated!",
              text: "Service Request status has been updated successfully.",
              timer: 1500,
              showConfirmButton: false
            }).then(() => {
              location.reload();
            });
          } else {
            alert("Service Request status updated successfully!");
            location.reload();
          }
        } else {
          if (typeof Swal !== "undefined") {
            Swal.fire({ icon: "error", title: "Error", text: res });
          } else {
            alert(res);
          }
        }
      }
    }
  };
  xhr.open("POST", "api/updateServiceRequestStatusProcess.php", true);
  xhr.send(form);
}

/* ==========================================
   DYNAMIC CALENDAR ENGINE
   ========================================== */
let calCurrentDate = new Date();
let calSelectedDate = null;
let calEventsData = [];

function initCalendar(events) {
  calEventsData = events || [];
  renderSmartCalendar();
}

function renderSmartCalendar() {
  const gridEl = document.getElementById("calendarGrid");
  const monthYearEl = document.getElementById("calMonthYearTitle");
  if (!gridEl || !monthYearEl) return;

  const year = calCurrentDate.getFullYear();
  const month = calCurrentDate.getMonth();

  const monthNames = [
    "January", "February", "March", "April", "May", "June",
    "July", "August", "September", "October", "November", "December"
  ];

  monthYearEl.textContent = `${monthNames[month]} ${year}`;

  const firstDayIndex = new Date(year, month, 1).getDay();
  const totalDaysInMonth = new Date(year, month + 1, 0).getDate();
  const prevMonthLastDay = new Date(year, month, 0).getDate();

  const today = new Date();
  const todayStr = `${today.getFullYear()}-${String(today.getMonth() + 1).padStart(2, '0')}-${String(today.getDate()).padStart(2, '0')}`;

  let html = "";

  // Previous month padding days
  for (let x = firstDayIndex; x > 0; x--) {
    const dayNum = prevMonthLastDay - x + 1;
    html += `<div class="calendar-day-cell other-month"><span class="day-num">${dayNum}</span></div>`;
  }

  // Current month days
  for (let day = 1; day <= totalDaysInMonth; day++) {
    const mStr = String(month + 1).padStart(2, '0');
    const dStr = String(day).padStart(2, '0');
    const fullDateStr = `${year}-${mStr}-${dStr}`;

    const isToday = (fullDateStr === todayStr) ? "is-today" : "";
    const isSelected = (fullDateStr === calSelectedDate) ? "is-selected" : "";

    // Find events for this date
    const dayEvents = calEventsData.filter(ev => ev.date === fullDateStr);
    
    let dotsHtml = "";
    if (dayEvents.length > 0) {
      // Get unique categories for this day
      const categories = [...new Set(dayEvents.map(ev => ev.category || 'Other'))];
      dotsHtml = `<div class="cal-dots-container">` + 
        categories.slice(0, 3).map(cat => `<span class="cal-dot cal-dot-${cat}" title="${cat}"></span>`).join("") + 
        `</div>`;
    }

    const titleAttr = dayEvents.length > 0 ? `title="${dayEvents.length} event(s) on ${fullDateStr}"` : '';

    html += `
      <div class="calendar-day-cell ${isToday} ${isSelected}" 
           ${titleAttr}
           onclick="onCalendarDayClick('${fullDateStr}')">
        <span class="day-num">${day}</span>
        ${dotsHtml}
      </div>
    `;
  }

  // Next month padding days to round up to complete rows of 7
  const totalCellsSoFar = firstDayIndex + totalDaysInMonth;
  const nextDays = (totalCellsSoFar % 7 === 0) ? 0 : 7 - (totalCellsSoFar % 7);
  for (let j = 1; j <= nextDays; j++) {
    html += `<div class="calendar-day-cell other-month"><span class="day-num">${j}</span></div>`;
  }

  gridEl.innerHTML = html;
}

function changeCalMonth(delta) {
  calCurrentDate.setMonth(calCurrentDate.getMonth() + delta);
  renderSmartCalendar();
}

function resetCalToday() {
  calCurrentDate = new Date();
  calSelectedDate = null;
  renderSmartCalendar();
  clearDateFilter();
}

function onCalendarDayClick(dateStr) {
  if (calSelectedDate === dateStr) {
    // Unselect
    calSelectedDate = null;
    clearDateFilter();
  } else {
    calSelectedDate = dateStr;
    filterEventsByDate(dateStr);
  }
  renderSmartCalendar();
}

function filterEventsByDate(dateStr) {
  const cards = document.querySelectorAll('.event-card');
  const banner = document.getElementById('dateFilterBanner');
  const dateText = document.getElementById('selectedDateText');

  let matchCount = 0;
  cards.forEach(card => {
    const cardDate = card.getAttribute('data-date');
    if (cardDate === dateStr) {
      card.style.display = 'block';
      matchCount++;
    } else {
      card.style.display = 'none';
    }
  });

  if (banner && dateText) {
    const parts = dateStr.split('-');
    let formatted = dateStr;
    if (parts.length === 3) {
      const d = new Date(parseInt(parts[0]), parseInt(parts[1]) - 1, parseInt(parts[2]));
      formatted = d.toLocaleDateString("en-US", { month: "short", day: "numeric", year: "numeric" });
    }
    dateText.textContent = `${formatted} (${matchCount} event${matchCount === 1 ? '' : 's'})`;
    banner.classList.remove('d-none');
  }

  // Pre-fill date in event modal input if opened
  const dateInput = document.getElementById('eventDate');
  if (dateInput) {
    dateInput.value = dateStr;
  }
}

function clearDateFilter() {
  calSelectedDate = null;
  const banner = document.getElementById('dateFilterBanner');
  if (banner) {
    banner.classList.add('d-none');
  }
  
  // Show events based on active category filter button
  const activeCategoryBtn = document.querySelector('.category-filter-btn.btn-su-indigo');
  if (activeCategoryBtn) {
    activeCategoryBtn.click();
  } else {
    const cards = document.querySelectorAll('.event-card');
    cards.forEach(card => card.style.display = 'block');
  }
  renderSmartCalendar();
}

/* ==========================================
   SERVICE REQUESTS LIVE FILTERING & SEARCH
   ========================================== */
let activeSrStatusFilter = 'ALL';

function filterServiceRequestsByStatus(statusKey, btn) {
  activeSrStatusFilter = statusKey;
  
  // Update button active states
  const btns = document.querySelectorAll('.sr-filter-tab');
  btns.forEach(b => {
    b.classList.remove('btn-su-indigo', 'active');
    b.classList.add('btn-su-outline');
  });
  if (btn) {
    btn.classList.remove('btn-su-outline');
    btn.classList.add('btn-su-indigo', 'active');
  }

  applySrFilters();
}

function applySrFilters() {
  const searchInput = document.getElementById('srSearchInput');
  const prioritySelect = document.getElementById('srPrioritySelect');
  const cards = document.querySelectorAll('.sr-card-item');
  const emptyState = document.getElementById('srEmptyState');

  const query = searchInput ? searchInput.value.toLowerCase().trim() : '';
  const priority = prioritySelect ? prioritySelect.value : 'ALL';

  let visibleCount = 0;

  cards.forEach(card => {
    const cardStatus = card.getAttribute('data-status-group') || 'ALL';
    const cardPriority = card.getAttribute('data-priority') || 'ALL';
    const cardText = card.textContent.toLowerCase();

    const matchesStatus = (activeSrStatusFilter === 'ALL' || cardStatus === activeSrStatusFilter);
    const matchesPriority = (priority === 'ALL' || cardPriority === priority);
    const matchesQuery = (!query || cardText.includes(query));

    if (matchesStatus && matchesPriority && matchesQuery) {
      card.style.display = 'block';
      visibleCount++;
    } else {
      card.style.display = 'none';
    }
  });

  if (emptyState) {
    if (visibleCount === 0 && cards.length > 0) {
      emptyState.classList.remove('d-none');
    } else {
      emptyState.classList.add('d-none');
    }
  }
}

function resetSrFilters() {
  const searchInput = document.getElementById('srSearchInput');
  const prioritySelect = document.getElementById('srPrioritySelect');
  if (searchInput) searchInput.value = '';
  if (prioritySelect) prioritySelect.value = 'ALL';
  
  const firstTab = document.querySelector('.sr-filter-tab');
  filterServiceRequestsByStatus('ALL', firstTab);
}