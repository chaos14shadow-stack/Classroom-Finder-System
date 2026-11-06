<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Overview - CPC RoomFinder | Admin Portal</title>
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
  <link rel="stylesheet" href="<?= base_url('assets/css/style.css') ?>">
</head>
<body class="portal-layout">

  <!-- Admin Sidebar Component -->
  <?= view('partials/admin_sidebar', ['activeNav' => 'overview']) ?>

  <!-- Main View Content -->
  <main class="portal-main">
    <div class="portal-topbar">
      <div class="portal-title-group">
        <h1>Overview</h1>
        <p class="subtitle">Campus administration dashboard and real-time activity metrics</p>
      </div>
      <a href="<?= base_url('admin/overview') ?>" title="Home" style="color: var(--cpc-orange); font-size: 1.5rem;">
        <svg width="28" height="28" viewBox="0 0 24 24" fill="currentColor">
          <path d="M10 20v-6h4v6h5v-8h3L12 3 2 12h3v8z"></path>
        </svg>
      </a>
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

    <!-- 4 Statistics Cards -->
    <div class="stats-grid">
      <div class="stat-card">
        <div class="stat-label">Total Rooms</div>
        <div class="stat-value"><?= esc($totalRooms) ?></div>
      </div>

      <div class="stat-card">
        <div class="stat-label">Total Active Users</div>
        <div class="stat-value"><?= esc($totalUsers) ?></div>
      </div>

      <div class="stat-card">
        <div class="stat-label">Bookings Today</div>
        <div class="stat-value"><?= esc($bookingsToday) ?></div>
      </div>

      <div class="stat-card highlight-orange">
        <div class="stat-label" style="color: #ffffff;">Pending Approvals</div>
        <div class="stat-value"><?= esc($pendingApprovals) ?></div>
      </div>
    </div>

    <!-- Pinned Announcement Banner matching screenshot -->
    <?php if (!empty($pinnedAnnouncement)): ?>
      <div class="pinned-banner">
        <div class="banner-tag">Pinned announcement</div>
        <p style="font-weight: 700; font-size: 1.05rem; margin-bottom: 4px;"><?= esc($pinnedAnnouncement['title']) ?></p>
        <p style="margin: 0; font-size: 0.92rem; line-height: 1.5;"><?= esc($pinnedAnnouncement['message']) ?></p>
      </div>
    <?php endif; ?>

    <!-- Recent Bookings Section -->
    <div style="margin-top: 10px;">
      <h2 style="font-size: 1.45rem; font-weight: 700; margin-bottom: 16px;">Recent Bookings</h2>

      <div class="table-container">
        <table class="cpc-table" id="adminRecentBookingsTable">
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
            <?php if (!empty($recentBookings)): ?>
              <?php foreach ($recentBookings as $b): ?>
                <?php
                $dotClass = match(strtolower($b['status'])) {
                    'approved'  => 'dot-approved',
                    'pending'   => 'dot-pending',
                    default     => 'dot-cancelled',
                };
                $instructorName = trim(($b['first_name'] ?? '') . ' ' . ($b['last_name'] ?? ''));
                ?>
                <tr>
                  <td style="font-weight: 700; font-size: 1rem;"><?= esc($b['room_name']) ?></td>
                  <td style="color: #cbd5e1;"><?= esc($instructorName ?: 'Faculty') ?></td>
                  <td style="color: #cbd5e1; font-weight: 600;">
                    <?= date('g:i A', strtotime($b['start_time'])) ?> – <?= date('g:i A', strtotime($b['end_time'])) ?>
                  </td>
                  <td style="color: #cbd5e1;"><?= date('M j', strtotime($b['booking_date'])) ?></td>
                  <td>
                    <span class="status-pill">
                      <span class="status-dot <?= esc($dotClass) ?>"></span>
                      <span style="text-transform: capitalize;"><?= esc($b['status']) ?></span>
                    </span>
                  </td>
                  <td style="text-align: right;">
                    <?php if (strtolower($b['status']) === 'pending'): ?>
                      <div style="display: inline-flex; gap: 8px;">
                        <!-- Approve form button -->
                        <form method="POST" action="<?= base_url('admin/bookings/approve/' . $b['id']) ?>" style="display: inline;">
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
                        <!-- Reject form button -->
                        <form method="POST" action="<?= base_url('admin/bookings/reject/' . $b['id']) ?>" style="display: inline;">
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
                    <?php else: ?>
                      <span style="color: #64748b; font-size: 0.85rem;">—</span>
                    <?php endif; ?>
                  </td>
                </tr>
              <?php endforeach; ?>
            <?php else: ?>
              <tr>
                <td colspan="6" style="text-align: center; color: #94a3b8; padding: 32px 0;">
                  No recent bookings recorded.
                </td>
              </tr>
            <?php endif; ?>
          </tbody>
        </table>
      </div>
    </div>
  </main>

  <script src="<?= base_url('assets/js/main.js') ?>"></script>
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-YvpcrYf0tY3lHB60NNkmXc4s9bIOgUxi8T/jzmgXzuez/pHXEzlN77IfH8MRQ=" crossorigin="anonymous"></script>
</body>
</html>
