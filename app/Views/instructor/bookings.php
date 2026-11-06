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
              $dotClass = match(strtolower($b['status'])) {
                  'approved'  => 'dot-approved',
                  'pending'   => 'dot-pending',
                  default     => 'dot-cancelled',
              };
              ?>
              <tr data-status="<?= esc(strtolower($b['status'])) ?>">
                <td style="font-weight: 700; font-size: 1rem;">
                  <?= esc($b['room_name']) ?>
                  <div style="font-size: 0.78rem; color: #94a3b8; font-weight: normal;">
                    <?= esc($b['department_code'] ?? 'College') ?> &bull; <?= esc($b['capacity']) ?> seats
                  </div>
                </td>
                <td style="color: #cbd5e1;"><?= date('M j, Y', strtotime($b['booking_date'])) ?></td>
                <td style="color: #cbd5e1; font-weight: 600;">
                  <?= date('g:i A', strtotime($b['start_time'])) ?> – <?= date('g:i A', strtotime($b['end_time'])) ?>
                </td>
                <td style="color: #cbd5e1;"><?= esc($b['purpose']) ?></td>
                <td>
                  <span class="status-pill">
                    <span class="status-dot <?= esc($dotClass) ?>"></span>
                    <span style="text-transform: capitalize;"><?= esc($b['status']) ?></span>
                  </span>
                </td>
                <td style="text-align: right;">
                  <?php if (in_array(strtolower($b['status']), ['pending', 'approved'], true)): ?>
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
                        style="background: rgba(239, 68, 68, 0.2); border: 1px solid rgba(239, 68, 68, 0.4); color: #fca5a5; border-radius: var(--radius-pill); font-size: 0.78rem; font-weight: 700; padding: 4px 14px;"
                      >
                        Cancel
                      </button>
                    </form>
                  <?php else: ?>
                    <span style="color: #64748b; font-size: 0.85rem;">—</span>
                  <?php endif; ?>
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

  <script src="<?= base_url('assets/js/main.js') ?>"></script>
  <script>
    const bFilter = document.getElementById('bookingStatusFilter');
    if (bFilter) {
      bFilter.addEventListener('change', (e) => {
        const val = e.target.value.toLowerCase();
        document.querySelectorAll('#instructorBookingsTable tbody tr').forEach(row => {
          if (!row.dataset.status) return;
          if (val === 'all' || row.dataset.status === val) {
            row.style.display = '';
          } else {
            row.style.display = 'none';
          }
        });
      });
    }
  </script>
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-YvpcrYf0tY3lHB60NNkmXc4s9bIOgUxi8T/jzmgXzuez/pHXEzlN77IfH8MRQ=" crossorigin="anonymous"></script>
</body>
</html>
