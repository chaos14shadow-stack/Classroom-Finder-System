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
        <p class="subtitle">Booking history and upcoming reservations</p>
      </div>

      <div style="display: flex; gap: 14px; align-items: center;">
        <a href="<?= base_url('instructor/book') ?>" class="btn btn-orange btn-sm" id="btnNewBooking">
          + Book a Room
        </a>

        <!-- Status Filter matching screenshot -->
        <select 
          id="bookingStatusFilter"
          style="background: var(--cpc-navy-card); border: 1px solid rgba(255,255,255,0.15); border-radius: var(--radius-pill); color: #fff; padding: 8px 18px; font-weight: 700; font-size: 0.82rem; cursor: pointer;"
        >
          <option value="all">STATUS ▾</option>
          <option value="pending">Pending</option>
          <option value="approved">Approved</option>
          <option value="cancelled">Cancelled</option>
        </select>
      </div>
    </div>

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
          <!-- Row 1: Room 224 Pending -->
          <tr data-status="pending">
            <td style="font-weight: 700; font-size: 1rem;">Room 224</td>
            <td style="color: #cbd5e1;">Sep 9</td>
            <td style="color: #cbd5e1;">9 - 12 PM</td>
            <td style="color: #cbd5e1;">GEC 8</td>
            <td>
              <span class="status-pill">
                <span class="status-dot dot-pending"></span>
                <span>Pending</span>
              </span>
            </td>
            <td style="text-align: right;">
              <button 
                type="button" 
                class="profile-edit-btn" 
                title="Edit Reservation" 
                onclick="showToast('Editing reservation for Room 224', 'info')"
              >
                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                  <path d="M12 20h9"></path>
                  <path d="M16.5 3.5a2.121 2.121 0 0 1 3 3L7 19l-4 1 1-4L16.5 3.5z"></path>
                </svg>
              </button>
            </td>
          </tr>

          <!-- Row 2: Room 101 Approved -->
          <tr data-status="approved">
            <td style="font-weight: 700; font-size: 1rem;">Room 101</td>
            <td style="color: #cbd5e1;">Sep 9</td>
            <td style="color: #cbd5e1;">1 - 2:30 PM</td>
            <td style="color: #cbd5e1;">GEI 1</td>
            <td>
              <span class="status-pill">
                <span class="status-dot dot-approved"></span>
                <span style="color: #4ade80;">Approved</span>
              </span>
            </td>
            <td style="text-align: right;">
              <button 
                type="button" 
                class="profile-edit-btn" 
                title="Edit Reservation" 
                onclick="showToast('Editing reservation for Room 101', 'info')"
              >
                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                  <path d="M12 20h9"></path>
                  <path d="M16.5 3.5a2.121 2.121 0 0 1 3 3L7 19l-4 1 1-4L16.5 3.5z"></path>
                </svg>
              </button>
            </td>
          </tr>

          <!-- Row 3: DABBA Cancelled -->
          <tr data-status="cancelled">
            <td style="font-weight: 700; font-size: 1rem;">DABBA</td>
            <td style="color: #cbd5e1;">Sep 10</td>
            <td style="color: #cbd5e1;">3 - 5 PM</td>
            <td style="color: #cbd5e1;">Seminar</td>
            <td>
              <span class="status-pill">
                <span class="status-dot dot-cancelled"></span>
                <span style="color: #f87171;">Cancelled</span>
              </span>
            </td>
            <td style="text-align: right;">
              <button 
                type="button" 
                class="profile-edit-btn" 
                title="Cancelled" 
                style="color: #ef4444;"
                onclick="showToast('Reservation was cancelled due to facility maintenance.', 'error')"
              >
                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                  <circle cx="12" cy="12" r="10"></circle>
                  <line x1="15" y1="9" x2="9" y2="15"></line>
                  <line x1="9" y1="9" x2="15" y2="15"></line>
                </svg>
              </button>
            </td>
          </tr>
        </tbody>
      </table>
    </div>
  </main>

  <script src="<?= base_url('assets/js/main.js') ?>"></script>
  <script>
    document.getElementById('bookingStatusFilter').addEventListener('change', (e) => {
      const val = e.target.value.toLowerCase();
      const rows = document.querySelectorAll('#instructorBookingsTable tbody tr');
      rows.forEach(r => {
        if (val === 'all' || r.dataset.status === val) {
          r.style.display = '';
        } else {
          r.style.display = 'none';
        }
      });
    });
  </script>
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-YvpcrYf0tY3lHB60NNkmXc4s9bIOgUxi8T/jzmgXzuez/pHXEzlN77IfH8MRQ=" crossorigin="anonymous"></script>
</body>
</html>
