<?php
$pageTitle = "SmartUni Portal - Campus Contacts";
$currentPage = "contacts";
require_once "includes/header.php";
?>

        <!-- Header -->
        <div class="mb-4">
          <span class="text-muted small fw-semibold text-uppercase">Campus Directory › Hotlines &amp; Support</span>
          <h1 class="fw-bold fs-3 mb-1">Campus &amp; Emergency Contacts</h1>
          <p class="text-secondary small mb-0">Quick directory of essential campus hotlines, department extensions, and student services.</p>
        </div>

        <!-- Urgent Emergency Contacts -->
        <div class="mb-5">
          <h5 class="fw-bold text-dark mb-3"><i class="bi bi-shield-fill-exclamation text-danger me-2"></i>Emergency Hotlines</h5>
          
          <div class="row g-3">
            <div class="col-12 col-md-4">
              <div class="bg-white border rounded-4 p-4 shadow-sm h-100 d-flex flex-column justify-content-between">
                <div>
                  <div class="d-flex align-items-center gap-2 mb-2">
                    <div class="bg-danger-subtle text-danger p-2 rounded-3">
                      <i class="bi bi-shield-lock-fill fs-5"></i>
                    </div>
                    <div>
                      <h6 class="fw-bold text-dark mb-0">Campus Security</h6>
                      <span class="text-muted small">24/7 Patrol Dispatch</span>
                    </div>
                  </div>
                  <h4 class="fw-bold text-danger my-3">+1 (555) 019-9911</h4>
                  <div class="text-secondary small"><i class="bi bi-geo-alt me-1"></i> Gatehouse A &amp; Patrol Hub</div>
                </div>
                <div class="mt-4 pt-3 border-top d-flex align-items-center justify-content-between">
                  <span class="badge bg-danger-subtle text-danger">Emergency Only</span>
                  <a href="tel:5550199911" class="btn btn-sm btn-danger px-3 py-2 fw-semibold rounded-3"><i class="bi bi-telephone-fill me-1"></i> Call Now</a>
                </div>
              </div>
            </div>

            <div class="col-12 col-md-4">
              <div class="bg-white border rounded-4 p-4 shadow-sm h-100 d-flex flex-column justify-content-between">
                <div>
                  <div class="d-flex align-items-center gap-2 mb-2">
                    <div class="bg-warning-subtle text-warning-emphasis p-2 rounded-3">
                      <i class="bi bi-hospital-fill fs-5"></i>
                    </div>
                    <div>
                      <h6 class="fw-bold text-dark mb-0">Health Center &amp; First Aid</h6>
                      <span class="text-muted small">Student Medical Clinic</span>
                    </div>
                  </div>
                  <h4 class="fw-bold text-dark my-3">+1 (555) 019-9120</h4>
                  <div class="text-secondary small"><i class="bi bi-geo-alt me-1"></i> Student Health Wing B</div>
                </div>
                <div class="mt-4 pt-3 border-top d-flex align-items-center justify-content-between">
                  <span class="badge bg-warning-subtle text-warning-emphasis">Medical Support</span>
                  <a href="tel:5550199120" class="btn btn-sm btn-warning px-3 py-2 fw-semibold rounded-3 text-dark"><i class="bi bi-telephone-fill me-1"></i> Call Now</a>
                </div>
              </div>
            </div>

            <div class="col-12 col-md-4">
              <div class="bg-white border rounded-4 p-4 shadow-sm h-100 d-flex flex-column justify-content-between">
                <div>
                  <div class="d-flex align-items-center gap-2 mb-2">
                    <div class="bg-primary-subtle text-primary p-2 rounded-3">
                      <i class="bi bi-headset fs-5"></i>
                    </div>
                    <div>
                      <h6 class="fw-bold text-dark mb-0">IT &amp; Systems Helpdesk</h6>
                      <span class="text-muted small">Technical &amp; Portal Support</span>
                    </div>
                  </div>
                  <h4 class="fw-bold text-primary my-3">+1 (555) 019-4040</h4>
                  <div class="text-secondary small"><i class="bi bi-geo-alt me-1"></i> Library Commons Mezzanine</div>
                </div>
                <div class="mt-4 pt-3 border-top d-flex align-items-center justify-content-between">
                  <span class="badge bg-primary-subtle text-primary">Support 24/7</span>
                  <a href="tel:5550194040" class="btn btn-sm btn-su-indigo px-3 py-2 fw-semibold rounded-3"><i class="bi bi-telephone-fill me-1"></i> Call Now</a>
                </div>
              </div>
            </div>
          </div>
        </div>

        <!-- Searchable Department Directory -->
        <div>
          <div class="d-flex flex-column flex-md-row align-items-md-center justify-content-between gap-3 mb-4">
            <div>
              <h5 class="fw-bold mb-1">Campus Department Directory</h5>
              <p class="text-secondary small mb-0">Direct phone numbers and locations for university services.</p>
            </div>
            <div class="position-relative" style="min-width: 280px;">
              <i class="bi bi-search position-absolute top-50 start-0 translate-middle-y ms-3 text-muted"></i>
              <input type="text" id="contactSearchInput" class="form-control su-input ps-5 rounded-3" placeholder="Search department or location..." onkeyup="filterContacts()" />
            </div>
          </div>

          <div class="row g-3" id="contactsGrid">
            
            <div class="col-12 col-md-6 col-lg-4 contact-card">
              <div class="bg-white border rounded-4 p-4 shadow-sm h-100 d-flex flex-column justify-content-between">
                <div>
                  <div class="d-flex justify-content-between align-items-center mb-2">
                    <h6 class="fw-bold text-dark mb-0 contact-name">Facilities &amp; Maintenance</h6>
                    <i class="bi bi-tools text-primary fs-5"></i>
                  </div>
                  <p class="text-muted small mb-2 contact-desc">Building Repairs &amp; Utility Issues</p>
                  <div class="fw-bold text-dark mb-1 contact-phone"><i class="bi bi-telephone me-1 text-primary"></i> +1 (555) 019-9999</div>
                  <div class="text-secondary small contact-loc"><i class="bi bi-geo-alt me-1"></i> Facilities Engineering HQ</div>
                </div>
                <div class="mt-3 pt-3 border-top text-muted small d-flex justify-content-between align-items-center">
                  <span><i class="bi bi-clock me-1"></i> Mon-Fri 08:00 - 17:00</span>
                  <a href="tel:5550199999" class="text-primary fw-semibold text-decoration-none">Call <i class="bi bi-arrow-right"></i></a>
                </div>
              </div>
            </div>

            <div class="col-12 col-md-6 col-lg-4 contact-card">
              <div class="bg-white border rounded-4 p-4 shadow-sm h-100 d-flex flex-column justify-content-between">
                <div>
                  <div class="d-flex justify-content-between align-items-center mb-2">
                    <h6 class="fw-bold text-dark mb-0 contact-name">Student Affairs &amp; Counseling</h6>
                    <i class="bi bi-heart-pulse text-primary fs-5"></i>
                  </div>
                  <p class="text-muted small mb-2 contact-desc">Mental Wellbeing &amp; Student Rights</p>
                  <div class="fw-bold text-dark mb-1 contact-phone"><i class="bi bi-telephone me-1 text-primary"></i> +1 (555) 019-2210</div>
                  <div class="text-secondary small contact-loc"><i class="bi bi-geo-alt me-1"></i> Admin Hall Rm 104</div>
                </div>
                <div class="mt-3 pt-3 border-top text-muted small d-flex justify-content-between align-items-center">
                  <span><i class="bi bi-clock me-1"></i> Mon-Fri 09:00 - 17:00</span>
                  <a href="tel:5550192210" class="text-primary fw-semibold text-decoration-none">Call <i class="bi bi-arrow-right"></i></a>
                </div>
              </div>
            </div>

            <div class="col-12 col-md-6 col-lg-4 contact-card">
              <div class="bg-white border rounded-4 p-4 shadow-sm h-100 d-flex flex-column justify-content-between">
                <div>
                  <div class="d-flex justify-content-between align-items-center mb-2">
                    <h6 class="fw-bold text-dark mb-0 contact-name">Registrar &amp; Records Office</h6>
                    <i class="bi bi-journal-bookmark text-primary fs-5"></i>
                  </div>
                  <p class="text-muted small mb-2 contact-desc">Transcripts &amp; Academic Records</p>
                  <div class="fw-bold text-dark mb-1 contact-phone"><i class="bi bi-telephone me-1 text-primary"></i> +1 (555) 019-1100</div>
                  <div class="text-secondary small contact-loc"><i class="bi bi-geo-alt me-1"></i> Central Hall Rm 210</div>
                </div>
                <div class="mt-3 pt-3 border-top text-muted small d-flex justify-content-between align-items-center">
                  <span><i class="bi bi-clock me-1"></i> Mon-Thu 09:00 - 16:30</span>
                  <a href="tel:5550191100" class="text-primary fw-semibold text-decoration-none">Call <i class="bi bi-arrow-right"></i></a>
                </div>
              </div>
            </div>

            <div class="col-12 col-md-6 col-lg-4 contact-card">
              <div class="bg-white border rounded-4 p-4 shadow-sm h-100 d-flex flex-column justify-content-between">
                <div>
                  <div class="d-flex justify-content-between align-items-center mb-2">
                    <h6 class="fw-bold text-dark mb-0 contact-name">Hostel &amp; Residence Warden</h6>
                    <i class="bi bi-house-door text-primary fs-5"></i>
                  </div>
                  <p class="text-muted small mb-2 contact-desc">Residence Hall Proctors &amp; Keys</p>
                  <div class="fw-bold text-dark mb-1 contact-phone"><i class="bi bi-telephone me-1 text-primary"></i> +1 (555) 019-5512</div>
                  <div class="text-secondary small contact-loc"><i class="bi bi-geo-alt me-1"></i> West Quad Residence Hall</div>
                </div>
                <div class="mt-3 pt-3 border-top text-muted small d-flex justify-content-between align-items-center">
                  <span><i class="bi bi-clock me-1"></i> Daily 07:00 - 23:00</span>
                  <a href="tel:5550195512" class="text-primary fw-semibold text-decoration-none">Call <i class="bi bi-arrow-right"></i></a>
                </div>
              </div>
            </div>

            <div class="col-12 col-md-6 col-lg-4 contact-card">
              <div class="bg-white border rounded-4 p-4 shadow-sm h-100 d-flex flex-column justify-content-between">
                <div>
                  <div class="d-flex justify-content-between align-items-center mb-2">
                    <h6 class="fw-bold text-dark mb-0 contact-name">Lab Safety &amp; Supervisors</h6>
                    <i class="bi bi-flask text-primary fs-5"></i>
                  </div>
                  <p class="text-muted small mb-2 contact-desc">Science &amp; Engineering Labs</p>
                  <div class="fw-bold text-dark mb-1 contact-phone"><i class="bi bi-telephone me-1 text-primary"></i> +1 (555) 019-3341</div>
                  <div class="text-secondary small contact-loc"><i class="bi bi-geo-alt me-1"></i> Bldg C Lab Wing, Rm 102</div>
                </div>
                <div class="mt-3 pt-3 border-top text-muted small d-flex justify-content-between align-items-center">
                  <span><i class="bi bi-clock me-1"></i> Mon-Fri 08:00 - 18:00</span>
                  <a href="tel:5550193341" class="text-primary fw-semibold text-decoration-none">Call <i class="bi bi-arrow-right"></i></a>
                </div>
              </div>
            </div>

            <div class="col-12 col-md-6 col-lg-4 contact-card">
              <div class="bg-white border rounded-4 p-4 shadow-sm h-100 d-flex flex-column justify-content-between">
                <div>
                  <div class="d-flex justify-content-between align-items-center mb-2">
                    <h6 class="fw-bold text-dark mb-0 contact-name">Library &amp; Learning Commons</h6>
                    <i class="bi bi-book text-primary fs-5"></i>
                  </div>
                  <p class="text-muted small mb-2 contact-desc">Research Help &amp; Study Room Desk</p>
                  <div class="fw-bold text-dark mb-1 contact-phone"><i class="bi bi-telephone me-1 text-primary"></i> +1 (555) 019-7700</div>
                  <div class="text-secondary small contact-loc"><i class="bi bi-geo-alt me-1"></i> Main Campus Library</div>
                </div>
                <div class="mt-3 pt-3 border-top text-muted small d-flex justify-content-between align-items-center">
                  <span><i class="bi bi-clock me-1"></i> Mon-Sun 08:00 - 22:00</span>
                  <a href="tel:5550197700" class="text-primary fw-semibold text-decoration-none">Call <i class="bi bi-arrow-right"></i></a>
                </div>
              </div>
            </div>

          </div>
        </div>

        <script>
          function filterContacts() {
            const searchInput = document.getElementById('contactSearchInput');
            if (!searchInput) return;
            const input = searchInput.value.toLowerCase().trim();
            const cards = document.querySelectorAll('.contact-card');
            
            cards.forEach(card => {
              const text = card.textContent.toLowerCase();
              if (!input || text.includes(input)) {
                card.style.display = 'block';
              } else {
                card.style.display = 'none';
              }
            });
          }
        </script>

<?php require_once "includes/footer.php"; ?>
