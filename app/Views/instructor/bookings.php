<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>My Bookings - CPC RoomFinder | Instructor Portal</title>
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
  <link rel="stylesheet" href="<?= base_url('assets/css/style.css') ?>">
</head>
<body class="portal-layout">

  <!-- Instructor Sidebar Component -->
  <?= view('partials/instructor_sidebar', ['activeNav' => 'bookings']) ?>

  <!-- Main View Content -->
  <main class="portal-main">
    <div class="portal-topbar">
      <div class="portal-title-group">
        <h1>My Bookings</h1>
        <p class="subtitle">Booking history and upcoming classroom reservations</p>
      </div>

      <div style="display: flex; gap: 14px; align-items: center;">
        <a href="<?= base_url('instructor/book') ?>" class="btn btn-orange btn-sm" id="btnNewBooking">
          + Book a Room
        </a>

        <!-- Status Filter -->
        <select 
          id="bookingStatusFilter"
          style="background: var(--cpc-navy-card); border: 1px solid rgba(255,255,255,0.15); border-radius: var(--radius-pill); color: #fff; padding: 8px 18px; font-weight: 700; font-size: 0.82rem; cursor: pointer;"
        >
          <option value="all" <?= ($statusFilter ?? '') === 'all' ? 'selected' : '' ?>>STATUS ▾</option>
          <option value="pending" <?= ($statusFilter ?? '') === 'pending' ? 'selected' : '' ?>>Pending</option>
          <option value="approved" <?= ($statusFilter ?? '') === 'approved' ? 'selected' : '' ?>>Approved</option>
          <option value="ongoing" <?= ($statusFilter ?? '') === 'ongoing' ? 'selected' : '' ?>>Ongoing</option>
          <option value="overtime" <?= ($statusFilter ?? '') === 'overtime' ? 'selected' : '' ?>>Overtime</option>
          <option value="completed" <?= ($statusFilter ?? '') === 'completed' ? 'selected' : '' ?>>Completed</option>
          <option value="rejected" <?= ($statusFilter ?? '') === 'rejected' ? 'selected' : '' ?>>Rejected</option>
          <option value="cancelled" <?= ($statusFilter ?? '') === 'cancelled' ? 'selected' : '' ?>>Cancelled</option>
        </select>
      </div>
    </div>

    <?php if (session()->getFlashdata('success')): ?>
      <div class="alert alert-success" style="background: rgba(34, 197, 94, 0.15); border: 1px solid rgba(34, 197, 94, 0.4); color: #86efac; font-size: 0.9rem; padding: 12px 18px; border-radius: 10px; margin-bottom: 20px;">
        ✅ <?= esc(session()->getFlashdata('success')) ?>
      </div>
    <?php endif; ?>

    <?php if (session()->getFlashdata('error')): ?>
      <div class="alert alert-danger" style="background: rgba(239, 68, 68, 0.15); border: 1px solid rgba(239, 68, 68, 0.4); color: #fca5a5; font-size: 0.9rem; padding: 12px 18px; border-radius: 10px; margin-bottom: 20px;">
        ⚠️ <?= esc(session()->getFlashdata('error')) ?>
      </div>
    <?php endif; ?>

    <!-- Bookings Table Container -->
    <div class="table-container">
      <table class="cpc-table" id="instructorBookingsTable">
        <thead>
          <tr>
            <th>ROOMS</th>
            <th>DATE</th>
            <th>TIME</th>
            <th>PURPOSE</th>
            <th>STATUS</th>
            <th style="text-align: right;">ACTION</th>
          </tr>
        </thead>
        <tbody>
          <?php if (!empty($bookings)): ?>
            <?php foreach ($bookings as $b): ?>
              <?php
              $st = strtolower($b['status']);
              $dotClass = match($st) {
                  'approved'                          => 'dot-approved',
                  'completed'                         => 'dot-approved',
                  'cancelled', 'rejected', 'overtime' => 'dot-cancelled',
                  default                             => 'dot-pending', // pending, ongoing, extension_requested
              };
              $dotStyle = ($st === 'completed') ? 'style="background: var(--cpc-cyan);"' : '';
              $statusDisplay = match($st) {
                  'ongoing' => (!empty($b['vacated_at'])) ? 'Ongoing (Vacated)' : 'Ongoing',
                  default   => ucwords(str_replace('_', ' ', $st)),
              };
              ?>
              <tr data-status="<?= esc($st) ?>">
                <td style="font-weight: 700; font-size: 1rem;">
                  <?= esc($b['room_name']) ?>
                  <div style="font-size: 0.78rem; color: #94a3b8; font-weight: normal;">
                    <?= esc($b['department_code'] ?? 'College') ?> &bull; <?= esc($b['capacity']) ?> seats
                  </div>
                </td>
                <td style="color: #cbd5e1;"><?= date('M j, Y', strtotime($b['booking_date'])) ?></td>
                <td style="color: #cbd5e1; font-weight: 600;">
                  <?= date('g:i A', strtotime($b['start_time'])) ?> – <?= date('g:i A', strtotime($b['end_time'])) ?>
                  <?php if (!empty($b['requested_end_time'])): ?>
                    <div style="font-size: 0.72rem; color: #f59e0b;">Requested ext: <?= date('g:i A', strtotime($b['requested_end_time'])) ?></div>
                  <?php endif; ?>
                  <?php if (!empty($b['vacated_at'])): ?>
                    <div style="font-size: 0.72rem; color: #38bdf8;">Vacated: <?= date('g:i A', strtotime($b['vacated_at'])) ?></div>
                  <?php endif; ?>
                </td>
                <td style="color: #cbd5e1;">
                  <?= esc($b['purpose']) ?>
                  <?php if (!empty($b['is_delayed'])): ?>
                    <span class="badge" style="background: rgba(239,68,68,0.2); color: #fca5a5; font-size: 0.7rem; display: block; margin-top: 4px;">⚠️ Room potentially delayed by previous class</span>
                  <?php endif; ?>
                </td>
                <td>
                  <span class="status-pill">
                    <span class="status-dot <?= esc($dotClass) ?>" <?= $dotStyle ?>></span>
                    <span style="color: #ffffff; text-transform: capitalize; font-weight: 600;">
                      <?= esc($statusDisplay) ?>
                    </span>
                  </span>
                </td>
                <td style="text-align: right;">
                  <div style="display: inline-flex; gap: 8px; justify-content: flex-end; align-items: center; flex-wrap: wrap;">
                    <!-- 1. Confirm Room Vacated (Eligible for active or overdue/overtime bookings) -->
                    <?php if (in_array($st, ['approved', 'ongoing', 'overtime', 'extension_requested'], true) && empty($b['vacated_at']) && $b['booking_date'] === date('Y-m-d')): ?>
                      <form method="POST" action="<?= base_url('instructor/bookings/vacate/' . $b['id']) ?>" style="display: inline;" onsubmit="return confirm('Confirm that you have vacated this classroom?');">
                        <?= csrf_field() ?>
                        <button 
                          type="submit" 
                          class="btn btn-sm" 
                          style="background: #22c55e; color: #ffffff; border-radius: var(--radius-pill); font-size: 0.76rem; font-weight: 700; padding: 4px 12px; border: none;"
                          title="Confirm that you have vacated this room"
                        >
                          ✓ Confirm Vacated
                        </button>
                      </form>
                    <?php endif; ?>

                    <!-- 2. Request Extension (For ongoing or overtime bookings) -->
                    <?php if (in_array($st, ['approved', 'ongoing', 'overtime'], true) && $b['booking_date'] === date('Y-m-d')): ?>
                      <button 
                        type="button" 
                        class="btn btn-sm" 
                        style="background: rgba(245,158,11,0.2); border: 1px solid rgba(245,158,11,0.4); color: #fcd34d; border-radius: var(--radius-pill); font-size: 0.76rem; font-weight: 700; padding: 4px 10px;"
                        onclick="openExtensionModal(<?= (int)$b['id'] ?>, '<?= esc($b['room_name']) ?>', '<?= esc($b['end_time']) ?>')"
                        title="Request extension if your class is running overtime"
                      >
                        ⏱️ Extend
                      </button>
                    <?php endif; ?>

                    <!-- 3. Cancel (Pending or Future Approved) -->
                    <?php if (in_array($st, ['pending', 'approved'], true)): ?>
                      <form 
                        method="POST" 
                        action="<?= base_url('instructor/bookings/cancel/' . $b['id']) ?>" 
                        style="display: inline;" 
                        onsubmit="return confirm('Are you sure you want to cancel this booking request?');"
                      >
                        <?= csrf_field() ?>
                        <button 
                          type="submit" 
                          class="btn btn-sm" 
                          style="background: rgba(239, 68, 68, 0.2); border: 1px solid rgba(239, 68, 68, 0.4); color: #fca5a5; border-radius: var(--radius-pill); font-size: 0.76rem; font-weight: 700; padding: 4px 10px;"
                        >
                          Cancel
                        </button>
                      </form>
                    <?php elseif (!in_array($st, ['ongoing', 'overtime', 'extension_requested'], true)): ?>
                      <span style="color: #64748b; font-size: 0.85rem;">—</span>
                    <?php endif; ?>
                  </div>
                </td>
              </tr>
            <?php endforeach; ?>
          <?php else: ?>
            <tr>
              <td colspan="6" style="text-align: center; color: #94a3b8; padding: 36px 0;">
                No bookings found. Click <strong>+ Book a Room</strong> to request a classroom.
              </td>
            </tr>
          <?php endif; ?>
        </tbody>
      </table>
    </div>

  </main>

  <!-- REQUEST EXTENSION MODAL -->
  <div class="cpc-modal-backdrop" id="extensionModal" role="dialog" aria-modal="true">
    <div class="cpc-modal-dialog">
      <button type="button" class="modal-close-btn" data-modal-close>&times;</button>
      <h2 class="modal-title" style="text-align: left; font-size: 1.35rem;">⏱️ Request Time Extension</h2>
      <p style="font-size: 0.85rem; color: #94a3b8; margin: -10px 0 20px;">
        If your class or examination in <strong id="modalRoomName" style="color: #fff;">Room</strong> is running overtime, select a new end time for administrator review.
      </p>

      <form id="extensionForm" method="POST" action="">
        <?= csrf_field() ?>

        <div class="form-group" style="margin-bottom: 16px;">
          <label style="font-size: 0.85rem; color: #cbd5e1;">Proposed New End Time:</label>
          <input 
            type="time" 
            name="requested_end_time" 
            id="modalReqTime"
            min="07:00"
            max="21:00"
            class="form-input" 
            required 
            style="border-radius: var(--radius-md); background: #0f1542; color: #fff; border: 1px solid rgba(255,255,255,0.15);"
          >
          <small style="color: #94a3b8; font-size: 0.75rem;">Must be later than scheduled end (<span id="modalCurrentEnd" style="color: #cbd5e1; font-weight: 700;"></span>) and cannot exceed 9:00 PM.</small>
        </div>

        <div class="form-group" style="margin-bottom: 24px;">
          <label style="font-size: 0.85rem; color: #cbd5e1;">Reason for Extension (Optional):</label>
          <input 
            type="text" 
            name="reason" 
            placeholder="e.g. Practical examination running late, lab experiment in progress" 
            class="form-input" 
            style="border-radius: var(--radius-md); background: #0f1542; color: #fff; border: 1px solid rgba(255,255,255,0.15);"
          >
        </div>

        <div style="display: flex; justify-content: flex-end; gap: 10px;">
          <button type="button" class="btn btn-navy" data-modal-close>Cancel</button>
          <button type="submit" class="btn btn-orange">Submit Request</button>
        </div>
      </form>
    </div>
  </div>

  <script src="<?= base_url('assets/js/main.js') ?>"></script>
  <script>
    function openExtensionModal(bookingId, roomName, currentEnd) {
      document.getElementById('modalRoomName').textContent = roomName;
      document.getElementById('modalCurrentEnd').textContent = currentEnd;
      document.getElementById('extensionForm').action = '<?= base_url('instructor/bookings/extend/') ?>' + bookingId;
      const reqInput = document.getElementById('modalReqTime');
      reqInput.min = currentEnd;
      reqInput.max = '21:00';
      openModal(document.getElementById('extensionModal'));
    }

    const bFilter = document.getElementById('bookingStatusFilter');
    function filterInstructorBookings() {
      const val = bFilter ? bFilter.value.toLowerCase() : 'all';
      document.querySelectorAll('#instructorBookingsTable tbody tr').forEach(row => {
        if (!row.dataset.status) return;
        const status = row.dataset.status.toLowerCase();
        let match = (val === 'all');
        if (val === 'ongoing') {
          match = (status === 'ongoing' || status === 'vacated');
        } else if (val === 'cancelled') {
          match = (status === 'cancelled' || status === 'rejected');
        } else if (val !== 'all') {
          match = (status === val);
        }

        if (match) {
          row.style.display = '';
        } else {
          row.style.display = 'none';
        }
      });
    }

    if (bFilter) {
      bFilter.addEventListener('change', function() {
        filterInstructorBookings();
        const val = bFilter.value.toLowerCase();
        const url = new URL(window.location.href);
        if (val === 'all') {
          url.searchParams.delete('status');
        } else {
          url.searchParams.set('status', val);
        }
        window.history.replaceState({}, '', url.toString());
      });
    }

    document.addEventListener('DOMContentLoaded', filterInstructorBookings);
    filterInstructorBookings();
  </script>
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-YvpcrYf0tY3lHB60NNkmXc4s9bIOgUxi8T/jzmgXzuez/pHXEzlN77IfH8MRQ=" crossorigin="anonymous"></script>
</body>
</html>
