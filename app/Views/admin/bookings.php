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
      </div>

      <!-- Search & Status Filter matching Image 1 bottom -->
      <div style="display: flex; gap: 14px; align-items: center; flex-wrap: wrap;">
        <input 
          type="text" 
          id="bookingSearchInp" 
          placeholder="🔍 Search Room No. / name"
          style="padding: 8px 18px; border-radius: var(--radius-pill); background: var(--cpc-navy-card); border: 1px solid rgba(255,255,255,0.15); color: #fff; font-size: 0.88rem;"
        >

        <select 
          id="bookingStatusSelect"
          style="padding: 8px 16px; border-radius: var(--radius-pill); background: var(--cpc-navy-card); border: 1px solid rgba(255,255,255,0.15); color: #fff; font-size: 0.88rem; cursor: pointer;"
        >
          <option value="all">All Status ▾</option>
          <option value="Pending">Pending</option>
          <option value="Approved">Approved</option>
          <option value="Cancelled">Cancelled</option>
        </select>
      </div>
    </div>

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
          <!-- Row 1: Room 224 Pending -->
          <tr data-room="Room 224" data-instructor="J. Doe" data-status="Pending">
            <td style="font-weight: 700; font-size: 1rem;">Room 224</td>
            <td style="color: #cbd5e1;">J. Doe</td>
            <td style="color: #cbd5e1;">9 - 12 PM</td>
            <td style="color: #cbd5e1;">Sep 9</td>
            <td>
              <span class="status-pill" id="b-status-224">
                <span class="status-dot dot-pending"></span>
                <span>Pending</span>
              </span>
            </td>
            <td style="text-align: right;" id="b-actions-224">
              <div style="display: inline-flex; gap: 8px;">
                <button 
                  type="button" 
                  class="btn-icon-only" 
                  style="background: #22c55e; color: #fff; border: none; cursor: pointer;" 
                  title="Approve Booking"
                  onclick="handleApprove('224')"
                >
                  ✓
                </button>
                <button 
                  type="button" 
                  class="btn-icon-only" 
                  style="background: #ef4444; color: #fff; border: none; cursor: pointer;" 
                  title="Cancel Booking"
                  onclick="handleCancel('224')"
                >
                  ✕
                </button>
              </div>
            </td>
          </tr>

          <!-- Row 2: Room 101 Approved -->
          <tr data-room="Room 101" data-instructor="M. Tuazon" data-status="Approved">
            <td style="font-weight: 700; font-size: 1rem;">Room 101</td>
            <td style="color: #cbd5e1;">M. Tuazon</td>
            <td style="color: #cbd5e1;">1 - 2:30 PM</td>
            <td style="color: #cbd5e1;">Sep 9</td>
            <td>
              <span class="status-pill" id="b-status-101">
                <span class="status-dot dot-approved"></span>
                <span style="color: #4ade80;">Approved</span>
              </span>
            </td>
            <td style="text-align: right;" id="b-actions-101">
              <button 
                type="button" 
                class="btn btn-sm" 
                style="background: rgba(239, 68, 68, 0.2); color: #f87171; border: 1px solid rgba(239, 68, 68, 0.4); padding: 4px 12px; font-size: 0.78rem;"
                onclick="handleCancel('101')"
              >
                Cancel
              </button>
            </td>
          </tr>

          <!-- Row 3: DABBA Cancelled -->
          <tr data-room="DABBA" data-instructor="J. Dela Cruz" data-status="Cancelled">
            <td style="font-weight: 700; font-size: 1rem;">DABBA</td>
            <td style="color: #cbd5e1;">J. Dela Cruz</td>
            <td style="color: #cbd5e1;">3 - 5 PM</td>
            <td style="color: #cbd5e1;">Sep 10</td>
            <td>
              <span class="status-pill" id="b-status-dabba">
                <span class="status-dot dot-cancelled"></span>
                <span style="color: #f87171;">Cancelled</span>
              </span>
            </td>
            <td style="text-align: right;" id="b-actions-dabba">
              <button 
                type="button" 
                class="btn btn-sm" 
                style="background: rgba(34, 197, 94, 0.2); color: #4ade80; border: 1px solid rgba(34, 197, 94, 0.4); padding: 4px 12px; font-size: 0.78rem;"
                onclick="handleApprove('dabba')"
              >
                Re-approve
              </button>
            </td>
          </tr>
        </tbody>
      </table>
    </div>
  </main>

  <script src="<?= base_url('assets/js/main.js') ?>"></script>
  <script>
    function handleApprove(id) {
      document.getElementById('b-status-' + id).innerHTML = '<span class="status-dot dot-approved"></span><span style="color: #4ade80;">Approved</span>';
      document.getElementById('b-actions-' + id).innerHTML = '<span style="color: #4ade80; font-size: 0.85rem; font-weight: 600;">Approved</span>';
      showToast('Booking Approved!', 'success');
    }

    function handleCancel(id) {
      document.getElementById('b-status-' + id).innerHTML = '<span class="status-dot dot-cancelled"></span><span style="color: #f87171;">Cancelled</span>';
      document.getElementById('b-actions-' + id).innerHTML = '<span style="color: #f87171; font-size: 0.85rem; font-weight: 600;">Cancelled</span>';
      showToast('Booking Cancelled.', 'error');
    }

    // Filter functionality
    const searchInp = document.getElementById('bookingSearchInp');
    const statusSel = document.getElementById('bookingStatusSelect');

    function filterBookings() {
      const q = searchInp.value.toLowerCase();
      const s = statusSel.value;

      document.querySelectorAll('#adminBookingsTable tbody tr').forEach(row => {
        const room = row.dataset.room.toLowerCase();
        const instructor = row.dataset.instructor.toLowerCase();
        const status = row.dataset.status;

        const matchQ = !q || room.includes(q) || instructor.includes(q);
        const matchS = s === 'all' || status === s;

        if (matchQ && matchS) {
          row.style.display = '';
        } else {
          row.style.display = 'none';
        }
      });
    }

    searchInp.addEventListener('input', filterBookings);
    statusSel.addEventListener('change', filterBookings);
  </script>
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-YvpcrYf0tY3lHB60NNkmXc4s9bIOgUxi8T/jzmgXzuez/pHXEzlN77IfH8MRQ=" crossorigin="anonymous"></script>
</body>
</html>
