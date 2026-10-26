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
      </div>
      <a href="<?= base_url('admin/overview') ?>" title="Home" style="color: var(--cpc-orange); font-size: 1.5rem;">
        <svg width="28" height="28" viewBox="0 0 24 24" fill="currentColor">
          <path d="M10 20v-6h4v6h5v-8h3L12 3 2 12h3v8z"></path>
        </svg>
      </a>
    </div>

    <!-- 4 Statistics Cards matching Image 4 middle -->
    <div class="stats-grid">
      <div class="stat-card">
        <div class="stat-label">Total Rooms</div>
        <div class="stat-value">3</div>
      </div>

      <div class="stat-card">
        <div class="stat-label">Total Users</div>
        <div class="stat-value">10</div>
      </div>

      <div class="stat-card">
        <div class="stat-label">Bookings Today</div>
        <div class="stat-value">1</div>
      </div>

      <div class="stat-card highlight-orange">
        <div class="stat-label" style="color: #ffffff;">Pending Approvals</div>
        <div class="stat-value">1</div>
      </div>
    </div>

    <!-- Pinned Announcement Banner matching screenshot -->
    <div class="pinned-banner">
      <div class="banner-tag">Pinned announcement</div>
      <p>Room DABBA will be closed for repairs from Sept 8 – 15. Please avoid booking it during this period.</p>
    </div>

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
            <!-- Row 1: Room 224 Pending with Approve/Reject buttons -->
            <tr id="row-booking-224">
              <td style="font-weight: 700; font-size: 1rem;">Room 224</td>
              <td style="color: #cbd5e1;">J. Doe</td>
              <td style="color: #cbd5e1;">9 - 12 PM</td>
              <td style="color: #cbd5e1;">Sep 9</td>
              <td>
                <span class="status-pill" id="status-pill-224">
                  <span class="status-dot dot-pending"></span>
                  <span>Pending</span>
                </span>
              </td>
              <td style="text-align: right;">
                <div style="display: inline-flex; gap: 8px;" id="actions-224">
                  <!-- Green checkmark button -->
                  <button 
                    type="button" 
                    class="btn-icon-only" 
                    style="background: #22c55e; color: #fff; border: none; cursor: pointer;" 
                    title="Approve Booking"
                    onclick="approveBooking('224')"
                  >
                    ✓
                  </button>
                  <!-- Red cross button -->
                  <button 
                    type="button" 
                    class="btn-icon-only" 
                    style="background: #ef4444; color: #fff; border: none; cursor: pointer;" 
                    title="Reject Booking"
                    onclick="rejectBooking('224')"
                  >
                    ✕
                  </button>
                </div>
              </td>
            </tr>

            <!-- Row 2: Room 101 Approved -->
            <tr>
              <td style="font-weight: 700; font-size: 1rem;">Room 101</td>
              <td style="color: #cbd5e1;">M. Tuazon</td>
              <td style="color: #cbd5e1;">1 - 2:30 PM</td>
              <td style="color: #cbd5e1;">Sep 9</td>
              <td>
                <span class="status-pill">
                  <span class="status-dot dot-approved"></span>
                  <span style="color: #4ade80;">Approved</span>
                </span>
              </td>
              <td style="text-align: right; color: #94a3b8; font-size: 0.85rem;">—</td>
            </tr>

            <!-- Row 3: DABBA Cancelled -->
            <tr>
              <td style="font-weight: 700; font-size: 1rem;">DABBA</td>
              <td style="color: #cbd5e1;">J. Dela Cruz</td>
              <td style="color: #cbd5e1;">3 - 5 PM</td>
              <td style="color: #cbd5e1;">Sep 10</td>
              <td>
                <span class="status-pill">
                  <span class="status-dot dot-cancelled"></span>
                  <span style="color: #f87171;">Cancelled</span>
                </span>
              </td>
              <td style="text-align: right; color: #94a3b8; font-size: 0.85rem;">—</td>
            </tr>
          </tbody>
        </table>
      </div>
    </div>
  </main>

  <script src="<?= base_url('assets/js/main.js') ?>"></script>
  <script>
    function approveBooking(id) {
      document.getElementById('status-pill-' + id).innerHTML = '<span class="status-dot dot-approved"></span><span style="color: #4ade80;">Approved</span>';
      document.getElementById('actions-' + id).innerHTML = '<span style="color: #4ade80; font-size: 0.85rem; font-weight: 600;">Approved</span>';
      showToast('Booking for Room ' + id + ' Approved!', 'success');
    }
    function rejectBooking(id) {
      document.getElementById('status-pill-' + id).innerHTML = '<span class="status-dot dot-cancelled"></span><span style="color: #f87171;">Cancelled</span>';
      document.getElementById('actions-' + id).innerHTML = '<span style="color: #f87171; font-size: 0.85rem; font-weight: 600;">Rejected</span>';
      showToast('Booking for Room ' + id + ' Rejected.', 'error');
    }
  </script>
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-YvpcrYf0tY3lHB60NNkmXc4s9bIOgUxi8T/jzmgXzuez/pHXEzlN77IfH8MRQ=" crossorigin="anonymous"></script>
</body>
</html>
