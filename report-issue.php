<?php
require_once "includes/connection.php";

$pageTitle = "SmartUni Portal - Report Service Issue";
$currentPage = "service-requests";
require_once "includes/header.php";

?>

        <a href="service-requests.php" class="text-secondary small fw-semibold text-decoration-none d-inline-flex align-items-center gap-1 mb-3">
          <i class="bi bi-arrow-left"></i> Back to Service Requests
        </a>

        <div class="d-flex align-items-center justify-content-between mb-4">
          <div>
            <h1 class="fw-bold fs-3 mb-1">Report Service Issue</h1>
            <p class="text-secondary small mb-0">Submit campus facility, IT, or maintenance requests for rapid response.</p>
          </div>
          <span class="su-badge su-badge-purple">● Avg. triage time: 14 mins</span>
        </div>

        <form id="reportIssueForm" onsubmit="event.preventDefault(); submitServiceRequest();" class="bg-white border rounded-4 p-4 shadow-sm max-w-900 mx-auto">
          
          <!-- Manual Location Input -->
          <div class="mb-4">
            <span class="fw-bold text-dark fs-6 d-block mb-2">1. Location on Campus <span class="text-danger">*</span></span>
            <input type="text" class="form-control su-input" id="srLocation" placeholder="e.g. Turing Engineering Building Level 3, Room 314" required />
          </div>

          <!-- Issue Summary -->
          <div class="mb-4">
            <span class="fw-bold text-dark fs-6 d-block mb-2">2. Issue Title / Summary <span class="text-danger">*</span></span>
            <input type="text" class="form-control su-input mb-3" id="srTitle" placeholder="e.g. Ceiling AC duct emitting loud rattling noise or Wi-Fi connectivity issue" required />
            
            <label class="su-label">Detailed Notes & Behavior <span class="text-danger">*</span></label>
            <textarea class="form-control su-input" id="srDescription" rows="4" placeholder="Provide details, timing, symptoms, or any observed behavior..." required></textarea>
          </div>

          <!-- Priority -->
          <div class="mb-4">
            <div class="d-flex justify-content-between align-items-center mb-2">
              <span class="fw-bold text-dark fs-6">3. Priority & Urgency <span class="text-danger">*</span></span>
              <span class="text-muted small">Sets response SLA</span>
            </div>
            <select class="form-select su-input" id="srPriority">
              <option value="Low">Low (> 48 hrs SLA)</option>
              <option value="Medium" selected>Medium (≤ 24 hrs SLA)</option>
              <option value="High">High (≤ 6 hrs SLA)</option>
              <option value="Urgent">Urgent (Immediate SLA)</option>
            </select>
          </div>

          <!-- Form Action Footer -->
          <div class="d-flex flex-column flex-sm-row align-items-center justify-content-between gap-3 pt-3 border-top">
            <div class="text-success small fw-semibold">
              <i class="bi bi-shield-check me-1"></i> Submitted directly to Campus Operations Desk
            </div>
            <div class="d-flex align-items-center gap-3">
              <a href="service-requests.php" class="text-secondary small text-decoration-none">Cancel</a>
              <button type="submit" class="btn btn-su-indigo px-4 py-2">Submit Service Request</button>
            </div>
          </div>

        </form>

<?php require_once "includes/footer.php"; ?>

