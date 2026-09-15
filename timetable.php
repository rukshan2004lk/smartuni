<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
require_once "includes/connection.php";

$pageTitle = "SmartUni Portal - Academic Timetable";
$currentPage = "timetable";

$userId = intval($_SESSION['user']['id'] ?? 0);

// Helper function to convert time string (HH:MM or HH:MM:SS) to total minutes from midnight
function parseTimeToMinutes($timeStr) {
    $timeStr = trim($timeStr);
    if (empty($timeStr)) return 0;
    $parts = explode(':', $timeStr);
    $h = intval($parts[0] ?? 0);
    $m = intval($parts[1] ?? 0);
    return ($h * 60) + $m;
}

// Fetch active entries from database table `timetable` for current user
$all_entries = [];
$timetable_rs = Database::search("SELECT * FROM `timetable` WHERE `user_id` = '$userId'");
if ($timetable_rs) {
    while ($row = $timetable_rs->fetch_assoc()) {
        $dayKey = strtolower($row['day_of_week']);
        if (str_contains($dayKey, 'mon')) $dayKey = 'mon';
        else if (str_contains($dayKey, 'tue')) $dayKey = 'tue';
        else if (str_contains($dayKey, 'wed')) $dayKey = 'wed';
        else if (str_contains($dayKey, 'thu')) $dayKey = 'thu';
        else if (str_contains($dayKey, 'fri')) $dayKey = 'fri';

        $row['start_min'] = parseTimeToMinutes($row['start_time']);
        $row['end_min']   = parseTimeToMinutes($row['end_time']);
        $row['day_key']   = $dayKey;

        $all_entries[] = $row;
    }
}

// Render dynamic cell matching single or double lectures
function renderSlotCell($dayKey, $slotStartStr, $slotEndStr) {
    global $all_entries;

    $slotStart = parseTimeToMinutes($slotStartStr);
    $slotEnd   = parseTimeToMinutes($slotEndStr);

    $matchingEntries = [];
    foreach ($all_entries as $entry) {
        if ($entry['day_key'] === $dayKey) {
            // Overlap check: entry starts before slot ends AND entry ends after slot starts
            if ($entry['start_min'] < $slotEnd && $entry['end_min'] > $slotStart) {
                $matchingEntries[] = $entry;
            }
        }
    }

    if (!empty($matchingEntries)) {
        $colors = ['tt-card-blue', 'tt-card-purple', 'tt-card-emerald', 'tt-card-amber', 'tt-card-rose'];
        foreach ($matchingEntries as $entry) {
            $cardColor = $colors[$entry['id'] % count($colors)];

            $durationMinutes = $entry['end_min'] - $entry['start_min'];
            $isDouble = ($durationMinutes > 75); // More than 1 hr 15 min (e.g. 2-hour double lecture)
            $isContinuation = ($entry['start_min'] < $slotStart);

            $jsonEntry = htmlspecialchars(json_encode($entry), ENT_QUOTES, 'UTF-8');

            echo '<div class="timetable-card ' . $cardColor . ' mb-2" style="cursor: pointer;" onclick="event.stopPropagation(); openEditModal(' . $jsonEntry . ');" title="Click to Edit Entry">';
            echo '  <div class="d-flex justify-content-between align-items-center mb-1">';
            echo '    <span class="tt-code">' . htmlspecialchars($entry['course_code']) . '</span>';

            if ($isDouble) {
                if ($isContinuation) {
                    echo '    <span class="badge bg-white text-dark p-1" style="font-size: 9px;"><i class="bi bi-arrow-right-circle me-1"></i>Double (Cont.)</span>';
                } else {
                    echo '    <span class="badge bg-warning text-dark fw-bold p-1" style="font-size: 9px;"><i class="bi bi-clock-history me-1"></i>Double Lecture</span>';
                }
            } else {
                echo '    <span class="badge bg-white text-dark p-1" style="font-size: 9px;">Scheduled</span>';
            }

            echo '  </div>';
            echo '  <div class="tt-title fw-bold">' . htmlspecialchars($entry['course_name']) . '</div>';
            if (!empty($entry['location'])) {
                echo '  <div class="tt-meta"><i class="bi bi-geo-alt-fill"></i> ' . htmlspecialchars($entry['location']) . '</div>';
            }
            echo '  <div class="tt-meta"><i class="bi bi-clock"></i> ' . htmlspecialchars($entry['start_time'] . ' - ' . $entry['end_time']) . '</div>';
            echo '</div>';
        }
    }
}

require_once "includes/header.php";
?>

        
        <div class="d-flex flex-column flex-md-row align-items-md-center justify-content-between gap-3 mb-4">
          <div>
           
            <h1 class="fw-bold fs-3 mb-1">Academic Timetable</h1>
            <p class="text-secondary small mb-0">View and manage your weekly lecture schedules, lab sessions, and academic commitments.</p>
          </div>
          <div class="d-flex align-items-center gap-2">
            <button class="btn btn-su-indigo d-inline-flex align-items-center gap-1" onclick="openAddModalForSlot('Monday', '08:30', '09:30');">
              <i class="bi bi-plus-lg"></i> Add Timetable Entry
            </button>
        
          </div>
        </div>

        <div class="table-responsive bg-white border rounded-4 p-3 shadow-sm mb-4">
          <table class="timetable-grid">
            <thead>
              <tr>
                <th class="timetable-time-col">TIME</th>
                <th class="timetable-header">MONDAY</th>
                <th class="timetable-header">TUESDAY</th>
                <th class="timetable-header">WEDNESDAY</th>
                <th class="timetable-header">THURSDAY</th>
                <th class="timetable-header">FRIDAY</th>
              </tr>
            </thead>
            <tbody>
              
              <!-- Slot 1: 08:30 - 09:30 -->
              <tr>
                <td class="timetable-time-col">08:30 - 09:30</td>
                <td class="timetable-cell" id="cell-mon-0830" onclick="openAddModalForSlot('Monday', '08:30', '09:30');"><?php renderSlotCell('mon', '08:30', '09:30'); ?></td>
                <td class="timetable-cell" id="cell-tue-0830" onclick="openAddModalForSlot('Tuesday', '08:30', '09:30');"><?php renderSlotCell('tue', '08:30', '09:30'); ?></td>
                <td class="timetable-cell" id="cell-wed-0830" onclick="openAddModalForSlot('Wednesday', '08:30', '09:30');"><?php renderSlotCell('wed', '08:30', '09:30'); ?></td>
                <td class="timetable-cell" id="cell-thu-0830" onclick="openAddModalForSlot('Thursday', '08:30', '09:30');"><?php renderSlotCell('thu', '08:30', '09:30'); ?></td>
                <td class="timetable-cell" id="cell-fri-0830" onclick="openAddModalForSlot('Friday', '08:30', '09:30');"><?php renderSlotCell('fri', '08:30', '09:30'); ?></td>
              </tr>

              <!-- Slot 2: 09:30 - 10:30 -->
              <tr>
                <td class="timetable-time-col">09:30 - 10:30</td>
                <td class="timetable-cell" id="cell-mon-0930" onclick="openAddModalForSlot('Monday', '09:30', '10:30');"><?php renderSlotCell('mon', '09:30', '10:30'); ?></td>
                <td class="timetable-cell" id="cell-tue-0930" onclick="openAddModalForSlot('Tuesday', '09:30', '10:30');"><?php renderSlotCell('tue', '09:30', '10:30'); ?></td>
                <td class="timetable-cell" id="cell-wed-0930" onclick="openAddModalForSlot('Wednesday', '09:30', '10:30');"><?php renderSlotCell('wed', '09:30', '10:30'); ?></td>
                <td class="timetable-cell" id="cell-thu-0930" onclick="openAddModalForSlot('Thursday', '09:30', '10:30');"><?php renderSlotCell('thu', '09:30', '10:30'); ?></td>
                <td class="timetable-cell" id="cell-fri-0930" onclick="openAddModalForSlot('Friday', '09:30', '10:30');"><?php renderSlotCell('fri', '09:30', '10:30'); ?></td>
              </tr>

              <!-- Slot 3: 10:30 - 11:30 -->
              <tr>
                <td class="timetable-time-col">10:30 - 11:30</td>
                <td class="timetable-cell" id="cell-mon-1030" onclick="openAddModalForSlot('Monday', '10:30', '11:30');"><?php renderSlotCell('mon', '10:30', '11:30'); ?></td>
                <td class="timetable-cell" id="cell-tue-1030" onclick="openAddModalForSlot('Tuesday', '10:30', '11:30');"><?php renderSlotCell('tue', '10:30', '11:30'); ?></td>
                <td class="timetable-cell" id="cell-wed-1030" onclick="openAddModalForSlot('Wednesday', '10:30', '11:30');"><?php renderSlotCell('wed', '10:30', '11:30'); ?></td>
                <td class="timetable-cell" id="cell-thu-1030" onclick="openAddModalForSlot('Thursday', '10:30', '11:30');"><?php renderSlotCell('thu', '10:30', '11:30'); ?></td>
                <td class="timetable-cell" id="cell-fri-1030" onclick="openAddModalForSlot('Friday', '10:30', '11:30');"><?php renderSlotCell('fri', '10:30', '11:30'); ?></td>
              </tr>

              <!-- Slot 4: 11:30 - 12:30 -->
              <tr>
                <td class="timetable-time-col">11:30 - 12:30</td>
                <td class="timetable-cell" id="cell-mon-1130" onclick="openAddModalForSlot('Monday', '11:30', '12:30');"><?php renderSlotCell('mon', '11:30', '12:30'); ?></td>
                <td class="timetable-cell" id="cell-tue-1130" onclick="openAddModalForSlot('Tuesday', '11:30', '12:30');"><?php renderSlotCell('tue', '11:30', '12:30'); ?></td>
                <td class="timetable-cell" id="cell-wed-1130" onclick="openAddModalForSlot('Wednesday', '11:30', '12:30');"><?php renderSlotCell('wed', '11:30', '12:30'); ?></td>
                <td class="timetable-cell" id="cell-thu-1130" onclick="openAddModalForSlot('Thursday', '11:30', '12:30');"><?php renderSlotCell('thu', '11:30', '12:30'); ?></td>
                <td class="timetable-cell" id="cell-fri-1130" onclick="openAddModalForSlot('Friday', '11:30', '12:30');"><?php renderSlotCell('fri', '11:30', '12:30'); ?></td>
              </tr>

              <!-- Lunch Break / Recess -->
              <tr>
                <td class="timetable-time-col text-muted bg-light" style="font-size: 10px;">12:30 - 13:30</td>
                <td colspan="5" class="text-center text-muted bg-light border-0 py-2 small fw-semibold" style="letter-spacing: 1px; font-size: 11px;">
                  <i class="bi bi-cup-hot me-1"></i> LUNCH BREAK / RECESS
                </td>
              </tr>

              <!-- Slot 5: 13:30 - 14:30 -->
              <tr>
                <td class="timetable-time-col">13:30 - 14:30</td>
                <td class="timetable-cell" id="cell-mon-1330" onclick="openAddModalForSlot('Monday', '13:30', '14:30');"><?php renderSlotCell('mon', '13:30', '14:30'); ?></td>
                <td class="timetable-cell" id="cell-tue-1330" onclick="openAddModalForSlot('Tuesday', '13:30', '14:30');"><?php renderSlotCell('tue', '13:30', '14:30'); ?></td>
                <td class="timetable-cell" id="cell-wed-1330" onclick="openAddModalForSlot('Wednesday', '13:30', '14:30');"><?php renderSlotCell('wed', '13:30', '14:30'); ?></td>
                <td class="timetable-cell" id="cell-thu-1330" onclick="openAddModalForSlot('Thursday', '13:30', '14:30');"><?php renderSlotCell('thu', '13:30', '14:30'); ?></td>
                <td class="timetable-cell" id="cell-fri-1330" onclick="openAddModalForSlot('Friday', '13:30', '14:30');"><?php renderSlotCell('fri', '13:30', '14:30'); ?></td>
              </tr>

              <!-- Slot 6: 14:30 - 15:30 -->
              <tr>
                <td class="timetable-time-col">14:30 - 15:30</td>
                <td class="timetable-cell" id="cell-mon-1430" onclick="openAddModalForSlot('Monday', '14:30', '15:30');"><?php renderSlotCell('mon', '14:30', '15:30'); ?></td>
                <td class="timetable-cell" id="cell-tue-1430" onclick="openAddModalForSlot('Tuesday', '14:30', '15:30');"><?php renderSlotCell('tue', '14:30', '15:30'); ?></td>
                <td class="timetable-cell" id="cell-wed-1430" onclick="openAddModalForSlot('Wednesday', '14:30', '15:30');"><?php renderSlotCell('wed', '14:30', '15:30'); ?></td>
                <td class="timetable-cell" id="cell-thu-1430" onclick="openAddModalForSlot('Thursday', '14:30', '15:30');"><?php renderSlotCell('thu', '14:30', '15:30'); ?></td>
                <td class="timetable-cell" id="cell-fri-1430" onclick="openAddModalForSlot('Friday', '14:30', '15:30');"><?php renderSlotCell('fri', '14:30', '15:30'); ?></td>
              </tr>

              <!-- Slot 7: 15:30 - 16:30 -->
              <tr>
                <td class="timetable-time-col">15:30 - 16:30</td>
                <td class="timetable-cell" id="cell-mon-1530" onclick="openAddModalForSlot('Monday', '15:30', '16:30');"><?php renderSlotCell('mon', '15:30', '16:30'); ?></td>
                <td class="timetable-cell" id="cell-tue-1530" onclick="openAddModalForSlot('Tuesday', '15:30', '16:30');"><?php renderSlotCell('tue', '15:30', '16:30'); ?></td>
                <td class="timetable-cell" id="cell-wed-1530" onclick="openAddModalForSlot('Wednesday', '15:30', '16:30');"><?php renderSlotCell('wed', '15:30', '16:30'); ?></td>
                <td class="timetable-cell" id="cell-thu-1530" onclick="openAddModalForSlot('Thursday', '15:30', '16:30');"><?php renderSlotCell('thu', '15:30', '16:30'); ?></td>
                <td class="timetable-cell" id="cell-fri-1530" onclick="openAddModalForSlot('Friday', '15:30', '16:30');"><?php renderSlotCell('fri', '15:30', '16:30'); ?></td>
              </tr>

              <!-- Slot 8: 16:30 - 17:30 -->
              <tr>
                <td class="timetable-time-col">16:30 - 17:30</td>
                <td class="timetable-cell" id="cell-mon-1630" onclick="openAddModalForSlot('Monday', '16:30', '17:30');"><?php renderSlotCell('mon', '16:30', '17:30'); ?></td>
                <td class="timetable-cell" id="cell-tue-1630" onclick="openAddModalForSlot('Tuesday', '16:30', '17:30');"><?php renderSlotCell('tue', '16:30', '17:30'); ?></td>
                <td class="timetable-cell" id="cell-wed-1630" onclick="openAddModalForSlot('Wednesday', '16:30', '17:30');"><?php renderSlotCell('wed', '16:30', '17:30'); ?></td>
                <td class="timetable-cell" id="cell-thu-1630" onclick="openAddModalForSlot('Thursday', '16:30', '17:30');"><?php renderSlotCell('thu', '16:30', '17:30'); ?></td>
                <td class="timetable-cell" id="cell-fri-1630" onclick="openAddModalForSlot('Friday', '16:30', '17:30');"><?php renderSlotCell('fri', '16:30', '17:30'); ?></td>
              </tr>

              <!-- Slot 9: 17:30 - 18:30 -->
              <tr>
                <td class="timetable-time-col">17:30 - 18:30</td>
                <td class="timetable-cell" id="cell-mon-1730" onclick="openAddModalForSlot('Monday', '17:30', '18:30');"><?php renderSlotCell('mon', '17:30', '18:30'); ?></td>
                <td class="timetable-cell" id="cell-tue-1730" onclick="openAddModalForSlot('Tuesday', '17:30', '18:30');"><?php renderSlotCell('tue', '17:30', '18:30'); ?></td>
                <td class="timetable-cell" id="cell-wed-1730" onclick="openAddModalForSlot('Wednesday', '17:30', '18:30');"><?php renderSlotCell('wed', '17:30', '18:30'); ?></td>
                <td class="timetable-cell" id="cell-thu-1730" onclick="openAddModalForSlot('Thursday', '17:30', '18:30');"><?php renderSlotCell('thu', '17:30', '18:30'); ?></td>
                <td class="timetable-cell" id="cell-fri-1730" onclick="openAddModalForSlot('Friday', '17:30', '18:30');"><?php renderSlotCell('fri', '17:30', '18:30'); ?></td>
              </tr>

            </tbody>
          </table>
        </div>

  <!-- ADD TIMETABLE MODAL -->
  <div class="modal fade" id="addTimetableModal" tabindex="-1" aria-labelledby="addTimetableModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
      <div class="modal-content rounded-4 border-0 shadow">
        <div class="modal-header border-bottom">
          <h5 class="modal-title fw-bold text-dark" id="addTimetableModalLabel">
            <i class="bi bi-calendar-plus text-primary me-2"></i>Add Timetable Entry
          </h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>
        <form id="addTimetableForm">
          <div class="modal-body p-4">
            
            <div class="row g-3 mb-3">
              <div class="col-md-5">
                <label class="su-label">Course Code <span class="text-danger">*</span></label>
                <input type="text" class="form-control su-input" id="ttCourseCode" placeholder="e.g. CS405" required />
              </div>
              <div class="col-md-7">
                <label class="su-label">Course Title <span class="text-danger">*</span></label>
                <input type="text" class="form-control su-input" id="ttCourseTitle" placeholder="e.g. Cloud Computing" required />
              </div>
            </div>

            <div class="mb-3">
              <label class="su-label">Location</label>
              <input type="text" class="form-control su-input" id="ttLocation" placeholder="e.g. S502" />
            </div>

            <div class="row g-3 mb-3">
              <div class="col-md-12">
                <label class="su-label">Day of Week <span class="text-danger">*</span></label>
                <select class="form-select su-input" id="ttDay" required>
                  <option value="Monday">Monday</option>
                  <option value="Tuesday">Tuesday</option>
                  <option value="Wednesday">Wednesday</option>
                  <option value="Thursday">Thursday</option>
                  <option value="Friday">Friday</option>
                </select>
              </div>
            </div>

            <div class="row g-3 mb-3">
              <div class="col-md-6">
                <label class="su-label">Start Time <span class="text-danger">*</span> (08:30 AM – 05:30 PM)</label>
                <input type="time" class="form-control su-input" id="ttStartTime" value="08:30" required />
              </div>
              <div class="col-md-6">
                <label class="su-label">End Time <span class="text-danger">*</span> (08:30 AM – 06:30 PM)</label>
                <input type="time" class="form-control su-input" id="ttEndTime" value="09:30" required />
              </div>
            </div>

          </div>
          <div class="modal-footer border-top p-3">
            <button type="button" class="btn btn-su-outline" data-bs-dismiss="modal">Cancel</button>
            <button type="button" onclick="addTimetable();" class="btn btn-su-indigo px-4">Add to Timetable</button>
          </div>
        </form>
      </div>
    </div>
  </div>

  <!-- EDIT TIMETABLE MODAL -->
  <div class="modal fade" id="editTimetableModal" tabindex="-1" aria-labelledby="editTimetableModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
      <div class="modal-content rounded-4 border-0 shadow">
        <div class="modal-header border-bottom">
          <h5 class="modal-title fw-bold text-dark" id="editTimetableModalLabel">
            <i class="bi bi-pencil-square text-primary me-2"></i>Edit Timetable Entry
          </h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>
        <form id="editTimetableForm">
          <input type="hidden" id="editTtId" />
          <div class="modal-body p-4">
            
            <div class="row g-3 mb-3">
              <div class="col-md-5">
                <label class="su-label">Course Code <span class="text-danger">*</span></label>
                <input type="text" class="form-control su-input" id="editTtCourseCode" placeholder="e.g. CS405" required />
              </div>
              <div class="col-md-7">
                <label class="su-label">Course Title <span class="text-danger">*</span></label>
                <input type="text" class="form-control su-input" id="editTtCourseTitle" placeholder="e.g. Cloud Computing" required />
              </div>
            </div>

            <div class="mb-3">
              <label class="su-label">Location</label>
              <input type="text" class="form-control su-input" id="editTtLocation" placeholder="e.g. Turing Hall 204 / Lab 01" />
            </div>

            <div class="row g-3 mb-3">
              <div class="col-md-12">
                <label class="su-label">Day of Week <span class="text-danger">*</span></label>
                <select class="form-select su-input" id="editTtDay" required>
                  <option value="Monday">Monday</option>
                  <option value="Tuesday">Tuesday</option>
                  <option value="Wednesday">Wednesday</option>
                  <option value="Thursday">Thursday</option>
                  <option value="Friday">Friday</option>
                </select>
              </div>
            </div>

            <div class="row g-3 mb-3">
              <div class="col-md-6">
                <label class="su-label">Start Time <span class="text-danger">*</span> (08:30 AM – 05:30 PM)</label>
                <input type="time" class="form-control su-input" id="editTtStartTime" required />
              </div>
              <div class="col-md-6">
                <label class="su-label">End Time <span class="text-danger">*</span> (08:30 AM – 06:30 PM)</label>
                <input type="time" class="form-control su-input" id="editTtEndTime" required />
              </div>
            </div>

          </div>
          <div class="modal-footer border-top p-3 d-flex justify-content-between align-items-center">
            <button type="button" class="btn btn-su-outline" data-bs-dismiss="modal">Cancel</button>
            <div class="d-flex align-items-center gap-2">
              <button type="button" onclick="updateTimetable();" class="btn btn-su-indigo px-4">
                <i class="bi bi-check-lg me-1"></i>Save Changes
              </button>
              <button type="button" onclick="deleteTimetable();" class="btn btn-outline-danger px-3">
                <i class="bi bi-trash3 me-1"></i>Delete
              </button>
            </div>
          </div>
        </form>
      </div>
    </div>
  </div>

<?php
require_once "includes/footer.php";
?>
