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
          <option value="all" <?= (empty($statusFilter) || $statusFilter === 'all') ? 'selected' : '' ?>>All Status ▾</option>
          <option value="Pending" <?= (isset($statusFilter) && strtolower($statusFilter) === 'pending') ? 'selected' : '' ?>>Pending</option>
          <option value="Approved" <?= (isset($statusFilter) && strtolower($statusFilter) === 'approved') ? 'selected' : '' ?>>Approved</option>
          <option value="Cancelled" <?= (isset($statusFilter) && in_array(strtolower($statusFilter), ['cancelled', 'rejected'])) ? 'selected' : '' ?>>Cancelled / Rejected</option>
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
                $dotClass = 'dot-pending';
                $pillColor = '#f59e0b';
                if ($st === 'approved') {
                    $dotClass = 'dot-approved';
                    $pillColor = '#4ade80';
                } elseif ($st === 'cancelled' || $st === 'rejected') {
                    $dotClass = 'dot-cancelled';
                    $pillColor = '#f87171';
                }

                $timeRange = date('g:i A', strtotime($b['start_time'])) . ' - ' . date('g:i A', strtotime($b['end_time']));
                $instructorName = trim(($b['first_name'] ?? '') . ' ' . ($b['last_name'] ?? ''));
                if (empty($instructorName)) {
                    $instructorName = 'User #' . $b['user_id'];
                }
              ?>
              <tr 
                data-room="<?= esc($b['room_name']) ?>" 
                data-instructor="<?= esc($instructorName) ?>" 
                data-status="<?= esc(ucfirst($st)) ?>"
              >
                <td style="font-weight: 700; font-size: 1rem;">
                  <?= esc($b['room_name']) ?>
                  <?php if (!empty($b['purpose'])): ?>
                    <div style="font-size: 0.76rem; color: #94a3b8; font-weight: 400;"><?= esc($b['purpose']) ?></div>
                  <?php endif; ?>
                </td>
                <td style="color: #cbd5e1;"><?= esc($instructorName) ?></td>
                <td style="color: #cbd5e1;"><?= esc($timeRange) ?></td>
                <td style="color: #cbd5e1;"><?= esc(date('M j, Y', strtotime($b['booking_date']))) ?></td>
                <td>
                  <span class="status-pill" id="b-status-<?= (int)$b['id'] ?>">
                    <span class="status-dot <?= esc($dotClass) ?>"></span>
                    <span style="color: <?= esc($pillColor) ?>; text-transform: capitalize;"><?= esc($b['status']) ?></span>
                  </span>
                </td>
                <td style="text-align: right;" id="b-actions-<?= (int)$b['id'] ?>">
                  <?php if ($st === 'pending'): ?>
                    <div style="display: inline-flex; gap: 8px;">
                      <form method="POST" action="<?= base_url('admin/bookings/approve/' . (int)$b['id']) ?>" style="display: inline;">
                        <?= csrf_field() ?>
                        <button 
                          type="submit" 
                          class="btn-icon-only" 
                          style="background: #22c55e; color: #fff; border: none; cursor: pointer;" 
                          title="Approve Booking"
                        >
                          ✓
                        </button>
                      </form>
                      <form method="POST" action="<?= base_url('admin/bookings/reject/' . (int)$b['id']) ?>" style="display: inline;" onsubmit="return confirm('Reject this booking request?');">
                        <?= csrf_field() ?>
                        <button 
                          type="submit" 
                          class="btn-icon-only" 
                          style="background: #ef4444; color: #fff; border: none; cursor: pointer;" 
                          title="Reject Booking"
                        >
                          ✕
                        </button>
                      </form>
                    </div>
                  <?php elseif ($st === 'approved'): ?>
                    <form method="POST" action="<?= base_url('admin/bookings/reject/' . (int)$b['id']) ?>" style="display: inline;" onsubmit="return confirm('Cancel this approved reservation?');">
                      <?= csrf_field() ?>
                      <button 
                        type="submit" 
                        class="btn btn-sm" 
                        style="background: rgba(239, 68, 68, 0.2); color: #f87171; border: 1px solid rgba(239, 68, 68, 0.4); padding: 4px 12px; font-size: 0.78rem; cursor: pointer;"
                      >
                        Cancel
                      </button>
                    </form>
                  <?php else: ?>
                    <form method="POST" action="<?= base_url('admin/bookings/approve/' . (int)$b['id']) ?>" style="display: inline;">
                      <?= csrf_field() ?>
                      <button 
                        type="submit" 
                        class="btn btn-sm" 
                        style="background: rgba(34, 197, 94, 0.2); color: #4ade80; border: 1px solid rgba(34, 197, 94, 0.4); padding: 4px 12px; font-size: 0.78rem; cursor: pointer;"
                      >
                        Re-approve
                      </button>
                    </form>
                  <?php endif; ?>
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

  <script src="<?= base_url('assets/js/main.js') ?>"></script>
  <script>
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
    if (statusSel) statusSel.addEventListener('change', filterBookings);
  </script>
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-YvpcrYf0tY3lHB60NNkmXc4s9bIOgUxi8T/jzmgXzuez/pHXEzlN77IfH8MRQ=" crossorigin="anonymous"></script>
</body>
</html>
