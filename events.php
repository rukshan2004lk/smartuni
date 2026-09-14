<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
require_once "includes/connection.php";

$pageTitle = "SmartUni Portal - Events & Official Notices";
$currentPage = "events";
$extraCss = '
  <style>
    .calendar-grid {
      display: grid;
      grid-template-columns: repeat(7, 1fr);
      gap: 6px;
      text-align: center;
    }
    .calendar-day {
      padding: 10px;
      border-radius: 8px;
      font-size: 13px;
      font-weight: 500;
      cursor: pointer;
      position: relative;
    }
    .calendar-day:hover {
      background-color: #f1f5f9;
    }
    .calendar-day.active-day {
      background-color: #4f46e5;
      color: #ffffff;
      font-weight: 700;
    }
    .cal-dot {
      width: 5px;
      height: 5px;
      border-radius: 50%;
      display: inline-block;
      margin: 0 1px;
    }
  </style>
';
require_once "includes/header.php";

$userRole = intval($_SESSION['user']['role_id'] ?? 1);

// Fetch all events from database
$events_rs = Database::search("SELECT * FROM `events` ORDER BY `date` ASC, `start_time` ASC");
$events_list = [];
if ($events_rs && $events_rs->num_rows > 0) {
    while ($row = $events_rs->fetch_assoc()) {
        $events_list[] = $row;
    }
}
?>

        <div class="d-flex flex-column flex-md-row align-items-md-center justify-content-between gap-3 mb-4">
          <div>
            <span class="text-muted small fw-semibold text-uppercase">Campus Hub › Bulletins &amp; Notices</span>
            <h1 class="fw-bold fs-3 mb-1">Events &amp; Official Notices</h1>
            <p class="text-secondary small mb-0">A quiet digest of official campus happenings, academic lectures, and student initiatives.</p>
          </div>
          <?php if ($userRole === 3): ?>
            <button class="btn btn-su-indigo d-inline-flex align-items-center gap-1 text-nowrap" onclick="openAddEventModal()">
              <i class="bi bi-plus-circle me-1"></i> Add New Event
            </button>
          <?php endif; ?>
        </div>

        <div class="d-flex flex-wrap align-items-center justify-content-between gap-3 mb-4">
          <div class="d-flex flex-wrap gap-2">
            <button class="btn btn-sm btn-su-indigo category-filter-btn px-3" onclick="filterEvents('All', this)">All (<?= count($events_list) ?>)</button>
            <button class="btn btn-sm btn-su-outline category-filter-btn px-3" onclick="filterEvents('Academic', this)">Academic</button>
            <button class="btn btn-sm btn-su-outline category-filter-btn px-3" onclick="filterEvents('Facilities', this)">Facilities</button>
            <button class="btn btn-sm btn-su-outline category-filter-btn px-3" onclick="filterEvents('Clubs', this)">Clubs</button>
            <button class="btn btn-sm btn-su-outline category-filter-btn px-3" onclick="filterEvents('Alert', this)">Alert</button>
            <button class="btn btn-sm btn-su-outline category-filter-btn px-3" onclick="filterEvents('Other', this)">Other</button>
          </div>
        </div>

        

        <div class="bg-white border rounded-4 p-4 shadow-sm mb-4">
          <div class="d-flex align-items-center justify-content-between mb-3">
            <h5 class="fw-bold mb-0"><i class="bi bi-calendar-event me-2 text-primary"></i>Calendar Overview</h5>
          </div>

          <div class="calendar-grid text-muted small fw-bold mb-2">
            <div>Su</div><div>Mo</div><div>Tu</div><div>We</div><div>Th</div><div>Fr</div><div>Sa</div>
          </div>

          <div class="calendar-grid mb-3">
            <div class="calendar-day text-muted opacity-50">27</div><div class="calendar-day text-muted opacity-50">28</div><div class="calendar-day text-muted opacity-50">29</div><div class="calendar-day text-muted opacity-50">30</div><div class="calendar-day text-muted opacity-50">31</div>
            <div class="calendar-day">1</div><div class="calendar-day">2</div>
            <div class="calendar-day">3</div><div class="calendar-day">4</div><div class="calendar-day">5</div><div class="calendar-day">6 <span class="cal-dot bg-danger"></span></div><div class="calendar-day">7</div><div class="calendar-day">8 <span class="cal-dot bg-warning"></span></div><div class="calendar-day">9</div>
            <div class="calendar-day">10</div><div class="calendar-day">11</div><div class="calendar-day active-day">12</div><div class="calendar-day">13</div><div class="calendar-day">14</div><div class="calendar-day">15 <span class="cal-dot bg-success"></span></div><div class="calendar-day">16</div>
            <div class="calendar-day">17</div><div class="calendar-day">18 <span class="cal-dot bg-primary"></span></div><div class="calendar-day">19</div><div class="calendar-day">20 <span class="cal-dot bg-success"></span></div><div class="calendar-day">21</div><div class="calendar-day">22</div><div class="calendar-day">23</div>
          </div>

          <div class="d-flex align-items-center gap-4 text-muted small pt-2 border-top">
            <span><span class="cal-dot bg-primary me-1"></span> Academic</span>
            <span><span class="cal-dot bg-warning me-1"></span> Facilities</span>
            <span><span class="cal-dot bg-success me-1"></span> Clubs</span>
            <span><span class="cal-dot bg-danger me-1"></span> Alert</span>
            <span><span class="cal-dot bg-info me-1"></span> Other</span>
          </div>
        </div>

        <div class="row g-4 mb-4" id="eventsContainer">
          <?php if (empty($events_list)): ?>
            <div class="col-12">
              <div class="p-5 text-center bg-white border rounded-4 shadow-sm">
                <i class="bi bi-calendar-x fs-1 text-muted"></i>
                <h5 class="fw-bold text-dark mt-2">No Events Found</h5>
                <p class="text-secondary small">There are currently no events posted in the system database.</p>
                <?php if ($userRole === 3): ?>
                  <button class="btn btn-su-indigo btn-sm mt-2" onclick="openAddEventModal()">+ Create First Event</button>
                <?php endif; ?>
              </div>
            </div>
          <?php else: ?>
            <?php foreach ($events_list as $ev): ?>
              <?php
                $cat = htmlspecialchars($ev['category'] ?? 'Other');
                $badgeClass = "su-badge-purple";
                if ($cat === "Academic") {
                    $badgeClass = "su-badge-green";
                } else if ($cat === "Facilities") {
                    $badgeClass = "su-badge-amber";
                } else if ($cat === "Alert") {
                    $badgeClass = "su-badge-red";
                } else if ($cat === "Other") {
                    $badgeClass = "su-badge-purple";
                }

                $formattedDate = date("M d, Y", strtotime($ev['date']));
                $timeDisplay = "";
                if (!empty($ev['start_time'])) {
                    $timeDisplay .= date("h:i A", strtotime($ev['start_time']));
                    if (!empty($ev['end_time'])) {
                        $timeDisplay .= " - " . date("h:i A", strtotime($ev['end_time']));
                    }
                }
                
                $metaString = $formattedDate;
                if ($timeDisplay !== "") {
                    $metaString .= " · " . $timeDisplay;
                }
                if (!empty($ev['location'])) {
                    $metaString .= " · " . htmlspecialchars($ev['location']);
                }

                // JS escape strings for inline edit modal
                $jsTitle = htmlspecialchars(addslashes($ev['title']), ENT_QUOTES);
                $jsCategory = htmlspecialchars(addslashes($ev['category']), ENT_QUOTES);
                $jsDate = htmlspecialchars($ev['date'], ENT_QUOTES);
                $jsStartTime = htmlspecialchars($ev['start_time'] ?? '', ENT_QUOTES);
                $jsEndTime = htmlspecialchars($ev['end_time'] ?? '', ENT_QUOTES);
                $jsLocation = htmlspecialchars(addslashes($ev['location'] ?? ''), ENT_QUOTES);
                $jsDescription = htmlspecialchars(addslashes($ev['description'] ?? ''), ENT_QUOTES);
              ?>
              <div class="col-12 col-md-6 col-lg-4 event-card" data-category="<?= $cat ?>">
                <div class="bg-white border rounded-4 p-4 shadow-sm h-100 d-flex flex-column justify-content-between">
                  <div>
                    <div class="d-flex justify-content-between align-items-center mb-2">
                      <span class="su-badge <?= $badgeClass ?>"><?= $cat ?></span>
                      <?php if ($userRole === 3): ?>
                        <div class="dropdown">
                          <button class="btn btn-sm btn-link text-muted p-0 text-decoration-none" type="button" data-bs-toggle="dropdown">
                            <i class="bi bi-three-dots-vertical"></i>
                          </button>
                          <ul class="dropdown-menu dropdown-menu-end shadow-sm border-0">
                            <li>
                              <button class="dropdown-item small" onclick="openEditEventModal(<?= $ev['id'] ?>, '<?= $jsTitle ?>', '<?= $jsCategory ?>', '<?= $jsDate ?>', '<?= $jsStartTime ?>', '<?= $jsEndTime ?>', '<?= $jsLocation ?>', '<?= $jsDescription ?>')">
                                <i class="bi bi-pencil me-2 text-primary"></i> Edit Event
                              </button>
                            </li>
                            <li>
                              <button class="dropdown-item small text-danger" onclick="deleteEvent(<?= $ev['id'] ?>)">
                                <i class="bi bi-trash me-2"></i> Delete Event
                              </button>
                            </li>
                          </ul>
                        </div>
                      <?php endif; ?>
                    </div>
                    <div class="text-secondary small mb-2"><i class="bi bi-clock me-1"></i> <?= $metaString ?></div>
                    <h5 class="fw-bold text-dark mb-2"><?= htmlspecialchars($ev['title']) ?></h5>
                    <p class="text-secondary small mb-0"><?= nl2br(htmlspecialchars($ev['description'] ?? '')) ?></p>
                  </div>
                  <?php if ($userRole === 3): ?>
                    <div class="d-flex justify-content-between align-items-center mt-3 pt-3 border-top">
                      <button class="btn btn-sm btn-outline-primary rounded-pill px-3" onclick="openEditEventModal(<?= $ev['id'] ?>, '<?= $jsTitle ?>', '<?= $jsCategory ?>', '<?= $jsDate ?>', '<?= $jsStartTime ?>', '<?= $jsEndTime ?>', '<?= $jsLocation ?>', '<?= $jsDescription ?>')">
                        <i class="bi bi-pencil me-1"></i> Edit
                      </button>
                      <button class="btn btn-sm btn-outline-danger rounded-pill px-3" onclick="deleteEvent(<?= $ev['id'] ?>)">
                        <i class="bi bi-trash me-1"></i> Delete
                      </button>
                    </div>
                  <?php endif; ?>
                </div>
              </div>
            <?php endforeach; ?>
          <?php endif; ?>
        </div>

        <!-- Add / Edit Event Modal -->
        <div class="modal fade" id="eventModal" tabindex="-1" aria-labelledby="eventModalTitle" aria-hidden="true">
          <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content border-0 shadow rounded-4">
              <div class="modal-header border-bottom-0 pb-0">
                <h5 class="modal-title fw-bold text-dark" id="eventModalTitle">Add New Event</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
              </div>
              <div class="modal-body p-4">
                <input type="hidden" id="eventId" value="">
                
                <div class="mb-3">
                  <label class="form-label small fw-semibold text-secondary">Event Title <span class="text-danger">*</span></label>
                  <input type="text" class="form-control rounded-3" id="eventTitle" placeholder="e.g. Annual Tech Symposium">
                </div>

                <div class="row g-3 mb-3">
                  <div class="col-6">
                    <label class="form-label small fw-semibold text-secondary">Category <span class="text-danger">*</span></label>
                    <select class="form-select rounded-3" id="eventCategory">
                      <option value="Academic">Academic</option>
                      <option value="Facilities">Facilities</option>
                      <option value="Clubs">Clubs</option>
                      <option value="Alert">Alert</option>
                      <option value="Other" selected>Other</option>
                    </select>
                  </div>
                  <div class="col-6">
                    <label class="form-label small fw-semibold text-secondary">Date <span class="text-danger">*</span></label>
                    <input type="date" class="form-control rounded-3" id="eventDate">
                  </div>
                </div>

                <div class="row g-3 mb-3">
                  <div class="col-6">
                    <label class="form-label small fw-semibold text-secondary">Start Time</label>
                    <input type="time" class="form-control rounded-3" id="eventStartTime">
                  </div>
                  <div class="col-6">
                    <label class="form-label small fw-semibold text-secondary">End Time</label>
                    <input type="time" class="form-control rounded-3" id="eventEndTime">
                  </div>
                </div>

                <div class="mb-3">
                  <label class="form-label small fw-semibold text-secondary">Location</label>
                  <input type="text" class="form-control rounded-3" id="eventLocation" placeholder="e.g. Auditorium Hall A">
                </div>

                <div class="mb-3">
                  <label class="form-label small fw-semibold text-secondary">Description</label>
                  <textarea class="form-control rounded-3" id="eventDescription" rows="3" placeholder="Provide event details, schedule, or guidelines..."></textarea>
                </div>
              </div>
              <div class="modal-footer border-top-0 pt-0 px-4 pb-4">
                <button type="button" class="btn btn-secondary rounded-3 px-3" data-bs-dismiss="modal">Cancel</button>
                <button type="button" class="btn btn-su-indigo rounded-3 px-4" onclick="saveEvent()">Save Event</button>
              </div>
            </div>
          </div>
        </div>

<?php require_once "includes/footer.php"; ?>
