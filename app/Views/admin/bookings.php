<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Manage Bookings - CPC RoomFinder | Admin Portal</title>
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
  <link rel="stylesheet" href="<?= base_url('assets/css/style.css') ?>">
</head>
<body class="portal-layout">

  <!-- Admin Sidebar Component -->
  <?= view('partials/admin_sidebar', ['activeNav' => 'bookings']) ?>

  <!-- Main View Content -->
  <main class="portal-main">
    <div class="portal-topbar">
      <div class="portal-title-group">
        <h1>Manage Bookings</h1>
        <p class="subtitle">Review, approve, or cancel faculty classroom reservation requests</p>
      </div>

      <!-- Search & Status Filter matching Image 1 bottom -->
      <div style="display: flex; gap: 14px; align-items: center; flex-wrap: wrap;">
        <input 
          type="text" 
          id="bookingSearchInp" 
          placeholder="🔍 Search Room No. / name"
          value="<?= esc($search ?? '') ?>"
          style="padding: 8px 18px; border-radius: var(--radius-pill); background: var(--cpc-navy-card); border: 1px solid rgba(255,255,255,0.15); color: #fff; font-size: 0.88rem;"
        >

        <select 
          id="bookingStatusSelect"
          style="padding: 8px 16px; border-radius: var(--radius-pill); background: var(--cpc-navy-card); border: 1px solid rgba(255,255,255,0.15); color: #fff; font-size: 0.88rem; cursor: pointer;"
        >
          <option value="all" <?= (empty($statusFilter) || strtolower($statusFilter) === 'all') ? 'selected' : '' ?>>All Status ▾</option>
          <option value="overtime" <?= (isset($statusFilter) && strtolower($statusFilter) === 'overtime') ? 'selected' : '' ?>>⚠️ Overtime</option>
          <option value="extension_requested" <?= (isset($statusFilter) && strtolower($statusFilter) === 'extension_requested') ? 'selected' : '' ?>>⏱️ Extension Requests</option>
          <option value="ongoing" <?= (isset($statusFilter) && strtolower($statusFilter) === 'ongoing') ? 'selected' : '' ?>>Ongoing</option>
          <option value="pending" <?= (isset($statusFilter) && strtolower($statusFilter) === 'pending') ? 'selected' : '' ?>>Pending</option>
          <option value="approved" <?= (isset($statusFilter) && strtolower($statusFilter) === 'approved') ? 'selected' : '' ?>>Approved</option>
          <option value="completed" <?= (isset($statusFilter) && strtolower($statusFilter) === 'completed') ? 'selected' : '' ?>>Completed</option>
          <option value="cancelled" <?= (isset($statusFilter) && in_array(strtolower($statusFilter), ['cancelled', 'rejected'])) ? 'selected' : '' ?>>Cancelled / Rejected</option>
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

    <!-- Bookings Table matching Image 1 bottom -->
    <div class="table-container">
      <table class="cpc-table" id="adminBookingsTable">
        <thead>
          <tr>
            <th>ROOMS</th>
            <th>INSTRUCTOR</th>
            <th>TIME</th>
            <th>DATE</th>
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

                $timeRange = date('g:i A', strtotime($b['start_time'])) . ' - ' . date('g:i A', strtotime($b['end_time']));
                $instructorName = trim(($b['first_name'] ?? '') . ' ' . ($b['last_name'] ?? ''));
                if (empty($instructorName)) {
                    $instructorName = 'User #' . $b['user_id'];
                }
              ?>
              <tr 
                data-room="<?= esc($b['room_name']) ?>" 
                data-instructor="<?= esc($instructorName) ?>" 
                data-status="<?= esc($st) ?>"
              >
                <td style="font-weight: 700; font-size: 1rem;">
                  <?= esc($b['room_name']) ?>
                  <?php if (!empty($b['purpose'])): ?>
                    <div style="font-size: 0.76rem; color: #94a3b8; font-weight: 400;"><?= esc($b['purpose']) ?></div>
                  <?php endif; ?>
                </td>
                <td style="color: #cbd5e1;"><?= esc($instructorName) ?></td>
                <td style="color: #cbd5e1;">
                  <?= esc($timeRange) ?>
                  <?php if (!empty($b['requested_end_time'])): ?>
                    <div style="font-size: 0.72rem; color: #f59e0b; font-weight: 700;">Req Ext: <?= date('g:i A', strtotime($b['requested_end_time'])) ?></div>
                  <?php endif; ?>
                  <?php if (!empty($b['vacated_at'])): ?>
                    <div style="font-size: 0.72rem; color: #38bdf8;">Vacated: <?= date('g:i A', strtotime($b['vacated_at'])) ?></div>
                  <?php endif; ?>
                </td>
                <td style="color: #cbd5e1;"><?= esc(date('M j, Y', strtotime($b['booking_date']))) ?></td>
                <td>
                  <span class="status-pill" id="b-status-<?= (int)$b['id'] ?>">
                    <span class="status-dot <?= esc($dotClass) ?>" <?= $dotStyle ?>></span>
                    <span style="color: #ffffff; text-transform: capitalize; font-weight: 600;">
                      <?= esc($statusDisplay) ?>
                    </span>
                  </span>
                  <?php if (!empty($b['is_delayed'])): ?>
                    <div style="font-size: 0.72rem; color: #fca5a5; margin-top: 4px;">⚠️ Delayed by previous class</div>
                  <?php endif; ?>
                </td>
                <td style="text-align: right;" id="b-actions-<?= (int)$b['id'] ?>">
                  <div style="display: inline-flex; gap: 6px; justify-content: flex-end; align-items: center; flex-wrap: wrap;">
                    <?php if ($st === 'extension_requested'): ?>
                      <!-- Extension Moderation -->
                      <form method="POST" action="<?= base_url('admin/bookings/approve-extension/' . (int)$b['id']) ?>" style="display: inline;">
                        <?= csrf_field() ?>
                        <button type="submit" class="btn btn-sm" style="background: #22c55e; color: #fff; font-size: 0.74rem; padding: 4px 10px; border-radius: var(--radius-pill); border: none;">Approve Ext</button>
                      </form>
                      <form method="POST" action="<?= base_url('admin/bookings/reject-extension/' . (int)$b['id']) ?>" style="display: inline;">
                        <?= csrf_field() ?>
                        <button type="submit" class="btn btn-sm" style="background: #ef4444; color: #fff; font-size: 0.74rem; padding: 4px 10px; border-radius: var(--radius-pill); border: none;">Reject Ext</button>
                      </form>
                    <?php endif; ?>

                    <?php if (($st === 'vacated' || ($st === 'ongoing' && !empty($b['vacated_at']))) && $b['status'] !== 'completed'): ?>
                      <!-- Complete Return Action -->
                      <form method="POST" action="<?= base_url('admin/bookings/complete-return/' . (int)$b['id']) ?>" style="display: inline;" onsubmit="return confirm('Finalize room return and mark booking as completed?');">
                        <?= csrf_field() ?>
                        <button type="submit" class="btn btn-sm" style="background: #22c55e; color: #fff; font-size: 0.74rem; padding: 4px 12px; border-radius: var(--radius-pill); border: none; font-weight: 700;">
                          ✓ Complete Return
                        </button>
                      </form>
                    <?php endif; ?>

                    <?php if (in_array($st, ['ongoing', 'overtime', 'approved'], true) && empty($b['vacated_at']) && $b['booking_date'] === date('Y-m-d')): ?>
                      <!-- Force Vacate by Admin -->
                      <form method="POST" action="<?= base_url('admin/bookings/vacate/' . (int)$b['id']) ?>" style="display: inline;" onsubmit="return confirm('Confirm that this classroom has been vacated?');">
                        <?= csrf_field() ?>
                        <button type="submit" class="btn btn-sm" style="background: rgba(56, 189, 248, 0.2); border: 1px solid rgba(56, 189, 248, 0.4); color: #38bdf8; font-size: 0.74rem; padding: 4px 10px; border-radius: var(--radius-pill);">
                          Vacate Room
                        </button>
                      </form>
                    <?php endif; ?>

                    <?php if (!empty($b['is_delayed']) || $st === 'approved'): ?>
                      <!-- Reassign to alternate classroom if delayed -->
                      <button 
                        type="button" 
                        class="btn btn-sm" 
                        style="background: rgba(168, 85, 247, 0.2); border: 1px solid rgba(168, 85, 247, 0.4); color: #c084fc; font-size: 0.74rem; padding: 4px 10px; border-radius: var(--radius-pill);"
                        onclick="openReassignModal(<?= (int)$b['id'] ?>, '<?= esc($b['room_name']) ?>', '<?= esc($instructorName) ?>')"
                      >
                        Reassign
                      </button>
                    <?php endif; ?>

                    <?php if ($st === 'pending'): ?>
                      <form method="POST" action="<?= base_url('admin/bookings/approve/' . (int)$b['id']) ?>" style="display: inline;">
                        <?= csrf_field() ?>
                        <button type="submit" class="btn-icon-only" style="background: #22c55e; color: #fff; border: none; cursor: pointer;" title="Approve Booking">✓</button>
                      </form>
                      <form method="POST" action="<?= base_url('admin/bookings/reject/' . (int)$b['id']) ?>" style="display: inline;" onsubmit="return confirm('Reject this booking request?');">
                        <?= csrf_field() ?>
                        <button type="submit" class="btn-icon-only" style="background: #ef4444; color: #fff; border: none; cursor: pointer;" title="Reject Booking">✕</button>
                      </form>
                    <?php elseif ($st === 'approved'): ?>
                      <form method="POST" action="<?= base_url('admin/bookings/reject/' . (int)$b['id']) ?>" style="display: inline;" onsubmit="return confirm('Cancel this approved reservation?');">
                        <?= csrf_field() ?>
                        <button type="submit" class="btn btn-sm" style="background: rgba(239, 68, 68, 0.2); color: #f87171; border: 1px solid rgba(239, 68, 68, 0.4); padding: 4px 10px; font-size: 0.74rem; border-radius: var(--radius-pill);">Cancel</button>
                      </form>
                    <?php endif; ?>
                  </div>
                </td>
              </tr>
            <?php endforeach; ?>
          <?php else: ?>
            <tr>
              <td colspan="6" style="text-align: center; color: #94a3b8; padding: 40px 0;">
                No bookings found matching the current criteria.
              </td>
            </tr>
          <?php endif; ?>
        </tbody>
      </table>
    </div>
  </main>

  <!-- REASSIGN CLASSROOM MODAL -->
  <div class="cpc-modal-backdrop" id="reassignModal" role="dialog" aria-modal="true">
    <div class="cpc-modal-dialog">
      <button type="button" class="modal-close-btn" data-modal-close>&times;</button>
      <h2 class="modal-title" style="text-align: left; font-size: 1.35rem;">🔄 Reassign to Alternate Classroom</h2>
      <p style="font-size: 0.85rem; color: #94a3b8; margin: -10px 0 20px;">
        Move <strong id="reassignInstructor" style="color: #fff;">Instructor</strong>'s session from <strong id="reassignOldRoom" style="color: #f59e0b;">Room</strong> to an available classroom to resolve an overtime conflict.
      </p>

      <form id="reassignForm" method="POST" action="">
        <?= csrf_field() ?>

        <div class="form-group" style="margin-bottom: 24px;">
          <label style="font-size: 0.85rem; color: #cbd5e1;">Select Available Classroom:</label>
          <select name="new_room_id" class="form-input" required style="border-radius: var(--radius-md); background: #0f1542; color: #fff; border: 1px solid rgba(255,255,255,0.15);">
            <option value="">-- Choose Classroom --</option>
            <?php if (!empty($availableRooms)): ?>
              <?php foreach ($availableRooms as $ar): ?>
                <option value="<?= (int)$ar['id'] ?>"><?= esc($ar['room_name']) ?> (Capacity: <?= (int)$ar['capacity'] ?>)</option>
              <?php endforeach; ?>
            <?php endif; ?>
          </select>
        </div>

        <div style="display: flex; justify-content: flex-end; gap: 10px;">
          <button type="button" class="btn btn-navy" data-modal-close>Cancel</button>
          <button type="submit" class="btn btn-orange">Confirm Reassignment</button>
        </div>
      </form>
    </div>
  </div>

  <script src="<?= base_url('assets/js/main.js') ?>"></script>
  <script>
    function openReassignModal(bookingId, roomName, instructor) {
      document.getElementById('reassignInstructor').textContent = instructor;
      document.getElementById('reassignOldRoom').textContent = roomName;
      document.getElementById('reassignForm').action = '<?= base_url('admin/bookings/reassign/') ?>' + bookingId;
      openModal(document.getElementById('reassignModal'));
    }

    // Live filter functionality
    const searchInp = document.getElementById('bookingSearchInp');
    const statusSel = document.getElementById('bookingStatusSelect');

    function filterBookings() {
      const q = searchInp ? searchInp.value.toLowerCase().trim() : '';
      const s = statusSel ? statusSel.value.toLowerCase() : 'all';

      document.querySelectorAll('#adminBookingsTable tbody tr').forEach(row => {
        // Skip empty placeholder row if any
        if (!row.dataset.room) return;

        const room = (row.dataset.room || '').toLowerCase();
        const instructor = (row.dataset.instructor || '').toLowerCase();
        const status = (row.dataset.status || '').toLowerCase();

        const matchQ = !q || room.includes(q) || instructor.includes(q);
        let matchS = (s === 'all');
        if (s === 'cancelled') {
          matchS = (status === 'cancelled' || status === 'rejected');
        } else if (s === 'ongoing') {
          matchS = (status === 'ongoing' || status === 'vacated');
        } else if (s !== 'all') {
          matchS = (status === s);
        }

        if (matchQ && matchS) {
          row.style.display = '';
        } else {
          row.style.display = 'none';
        }
      });
    }

    if (searchInp) searchInp.addEventListener('input', filterBookings);
    if (statusSel) {
      statusSel.addEventListener('change', function() {
        filterBookings();
        const s = statusSel.value.toLowerCase();
        const url = new URL(window.location.href);
        if (s === 'all') {
          url.searchParams.delete('status');
        } else {
          url.searchParams.set('status', s);
        }
        window.history.replaceState({}, '', url.toString());
      });
    }

    // Run filter on initial page load
    document.addEventListener('DOMContentLoaded', filterBookings);
    filterBookings();
  </script>
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-YvpcrYf0tY3lHB60NNkmXc4s9bIOgUxi8T/jzmgXzuez/pHXEzlN77IfH8MRQ=" crossorigin="anonymous"></script>
</body>
</html>
