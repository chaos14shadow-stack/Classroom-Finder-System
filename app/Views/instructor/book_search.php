<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Book a Room - CPC RoomFinder | Instructor Portal</title>
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
  <link rel="stylesheet" href="<?= base_url('assets/css/style.css') ?>">
  <style>
    .filter-pill-bar {
      display: flex;
      gap: 16px;
      align-items: center;
      margin-bottom: 28px;
      flex-wrap: wrap;
    }
    .search-pill-input {
      background: #ffffff;
      color: #1e293b;
      border: none;
      border-radius: var(--radius-pill);
      padding: 10px 20px 10px 40px;
      font-size: 0.9rem;
      font-weight: 500;
      outline: none;
      width: 280px;
      position: relative;
    }
    .search-pill-wrapper {
      position: relative;
      display: inline-flex;
      align-items: center;
    }
    .search-pill-wrapper svg {
      position: absolute;
      left: 14px;
      width: 18px;
      height: 18px;
      color: #64748b;
      pointer-events: none;
    }
    .select-pill-btn {
      background: #ffffff;
      color: #1e293b;
      border: none;
      border-radius: var(--radius-pill);
      padding: 10px 22px;
      font-size: 0.9rem;
      font-weight: 600;
      cursor: pointer;
      appearance: none;
      background-image: url("data:image/svg+xml;charset=UTF-8,%3csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 24 24' fill='none' stroke='%231e293b' stroke-width='2.5' stroke-linecap='round' stroke-linejoin='round'%3e%3cpolyline points='6 9 12 15 18 9'%3e%3c/polyline%3e%3c/svg%3e");
      background-repeat: no-repeat;
      background-position: right 14px center;
      background-size: 14px;
      padding-right: 36px;
      outline: none;
    }
    .room-directory-table {
      width: 100%;
      border-collapse: separate;
      border-spacing: 0 12px;
    }
    .room-directory-table th {
      padding: 12px 24px;
      font-size: 0.88rem;
      font-weight: 700;
      letter-spacing: 0.4px;
      text-transform: uppercase;
      color: #cbd5e1;
      text-align: left;
    }
    .room-directory-table tbody tr {
      background: #171d50;
      border-radius: 12px;
      cursor: pointer;
      transition: transform var(--transition-fast), background var(--transition-fast);
    }
    .room-directory-table tbody tr:hover {
      background: #1e2667;
      transform: translateY(-2px);
    }
    .room-directory-table td {
      padding: 18px 24px;
      font-size: 0.95rem;
      color: #ffffff;
      vertical-align: middle;
    }
    .room-directory-table td:first-child {
      border-top-left-radius: 12px;
      border-bottom-left-radius: 12px;
      font-weight: 700;
      font-size: 1.05rem;
    }
    .room-directory-table td:last-child {
      border-top-right-radius: 12px;
      border-bottom-right-radius: 12px;
    }
  </style>
</head>
<body class="portal-layout">

  <!-- Instructor Sidebar Component -->
  <?= view('partials/instructor_sidebar', ['activeNav' => 'book']) ?>

  <!-- Main View Content -->
  <main class="portal-main">
    <div class="portal-topbar">
      <div class="portal-title-group">
        <h1>Book a Classroom</h1>
        <p class="subtitle">Select a room to request or proceed directly to reservation form</p>
      </div>

      <div style="display: flex; gap: 12px; align-items: center;">
        <a href="<?= base_url('instructor/book/form') ?>" class="btn btn-lime btn-sm">
          Direct Booking Form →
        </a>
      </div>
    </div>

    <!-- Filter Pill Bar matching mockup -->
    <div class="filter-pill-bar">
      <div class="search-pill-wrapper">
        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
          <circle cx="11" cy="11" r="8"></circle>
          <line x1="21" y1="21" x2="16.65" y2="16.65"></line>
        </svg>
        <input 
          type="text" 
          class="search-pill-input" 
          id="instructorRoomSearchInp" 
          placeholder="Search Room No. / name"
          value="<?= esc($filters['q'] ?? '') ?>"
        >
      </div>

      <select class="select-pill-btn" id="instructorDeptSelect">
        <option value="all">Department</option>
        <?php if (!empty($departments)): ?>
          <?php foreach ($departments as $d): ?>
            <option value="<?= esc($d['code']) ?>" <?= ($filters['department'] ?? '') === $d['code'] ? 'selected' : '' ?>>
              <?= esc($d['code']) ?>
            </option>
          <?php endforeach; ?>
        <?php else: ?>
          <option value="CET">CET</option>
          <option value="Psychology">Psychology</option>
          <option value="Marine">Marine</option>
          <option value="IT">IT</option>
        <?php endif; ?>
      </select>

      <select class="select-pill-btn" id="instructorCapSelect">
        <option value="all">Capacity</option>
        <option value="20" <?= ($filters['capacity'] ?? 0) == 20 ? 'selected' : '' ?>>20+ Seats</option>
        <option value="30" <?= ($filters['capacity'] ?? 0) == 30 ? 'selected' : '' ?>>30+ Seats</option>
        <option value="50" <?= ($filters['capacity'] ?? 0) == 50 ? 'selected' : '' ?>>50+ Seats</option>
      </select>

      <select class="select-pill-btn" id="instructorStatusSelect">
        <option value="all">Status</option>
        <option value="Available">Available</option>
        <option value="Occupied">Occupied</option>
        <option value="Maintenance">Maintenance</option>
      </select>
    </div>

    <!-- Rooms Table matching Middle Panel Screenshot -->
    <table class="room-directory-table" id="instructorRoomsTable">
      <thead>
        <tr>
          <th>ROOMS</th>
          <th>DEPARTMENT</th>
          <th>CAPACITY</th>
          <th>STATUS</th>
        </tr>
      </thead>
      <tbody>
        <?php if (!empty($rooms)): ?>
          <?php foreach ($rooms as $r): ?>
            <?php
            $dotClass = match($r['status']) {
                'Available'   => 'dot-approved',
                'Occupied'    => 'dot-cancelled',
                'Maintenance' => 'dot-maintenance',
                default       => 'dot-pending',
            };
            $isMaint = ($r['status'] === 'Maintenance');
            $clickAction = $isMaint 
                ? "showToast('" . esc($r['room_name']) . " is currently under facility maintenance.', 'error')"
                : "window.location.href='" . base_url('instructor/book/form?room=' . urlencode($r['room_name']) . '&room_id=' . $r['id']) . "'";
            ?>
            <tr 
              onclick="<?= $clickAction ?>" 
              title="<?= $isMaint ? 'Under Maintenance' : 'Click to book ' . esc($r['room_name']) ?>"
              data-room="<?= esc($r['room_name']) ?>" 
              data-dept="<?= esc($r['department_code'] ?? '') ?>" 
              data-capacity="<?= esc($r['capacity']) ?>" 
              data-status="<?= esc($r['status']) ?>"
            >
              <td>
                <span style="color: #ffffff;"><?= esc($r['room_name']) ?></span>
                <?php if ($isMaint && !empty($r['maintenance_note'])): ?>
                  <div style="font-size: 0.78rem; color: #f87171; font-weight: normal; margin-top: 3px;">
                    🔧 <?= esc($r['maintenance_note']) ?>
                  </div>
                <?php endif; ?>
              </td>
              <td style="color: #cbd5e1;"><?= esc($r['department_code'] ?? ($r['department_name'] ?? 'General')) ?></td>
              <td>
                <span style="display: inline-flex; align-items: center; gap: 8px;">
                  <svg width="18" height="18" viewBox="0 0 24 24" fill="currentColor" style="color: #cbd5e1;">
                    <path d="M16 11c1.66 0 2.99-1.34 2.99-3S17.66 5 16 5c-1.66 0-3 1.34-3 3s1.34 3 3 3zm-8 0c1.66 0 2.99-1.34 2.99-3S9.66 5 8 5C6.34 5 5 6.34 5 8s1.34 3 3 3zm0 2c-2.33 0-7 1.17-7 3.5V19h14v-2.5c0-2.33-4.67-3.5-7-3.5zm8 0c-.29 0-.62.02-.97.05 1.16.84 1.97 1.97 1.97 3.45V19h6v-2.5c0-2.33-4.67-3.5-7-3.5z"></path>
                  </svg>
                  <span><?= esc($r['capacity']) ?></span>
                </span>
              </td>
              <td>
                <span class="status-pill">
                  <span class="status-dot <?= esc($dotClass) ?>"></span>
                  <span style="color: #cbd5e1;"><?= esc($r['status']) ?></span>
                </span>
              </td>
            </tr>
          <?php endforeach; ?>
        <?php else: ?>
          <tr>
            <td colspan="4" style="text-align: center; color: #94a3b8; padding: 36px 0;">
              No classrooms available.
            </td>
          </tr>
        <?php endif; ?>
      </tbody>
    </table>

  </main>

  <script src="<?= base_url('assets/js/main.js') ?>"></script>
  <script>
    // Live filter logic
    const iSearch = document.getElementById('instructorRoomSearchInp');
    const iDept = document.getElementById('instructorDeptSelect');
    const iCap = document.getElementById('instructorCapSelect');
    const iStat = document.getElementById('instructorStatusSelect');

    function filterInstructorRooms() {
      const q = iSearch ? iSearch.value.toLowerCase().trim() : '';
      const d = iDept ? iDept.value : 'all';
      const c = iCap ? iCap.value : 'all';
      const s = iStat ? iStat.value : 'all';

      document.querySelectorAll('#instructorRoomsTable tbody tr').forEach(row => {
        if (!row.dataset.room) return;
        const room = row.dataset.room.toLowerCase();
        const dept = row.dataset.dept;
        const cap = parseInt(row.dataset.capacity, 10);
        const stat = row.dataset.status;

        const matchQ = !q || room.includes(q);
        const matchD = d === 'all' || dept === d;
        const matchC = c === 'all' || cap >= parseInt(c, 10);
        const matchS = s === 'all' || stat === s;

        if (matchQ && matchD && matchC && matchS) {
          row.style.display = '';
        } else {
          row.style.display = 'none';
        }
      });
    }

    if (iSearch) iSearch.addEventListener('input', filterInstructorRooms);
    if (iDept) iDept.addEventListener('change', filterInstructorRooms);
    if (iCap) iCap.addEventListener('change', filterInstructorRooms);
    if (iStat) iStat.addEventListener('change', filterInstructorRooms);
  </script>
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-YvpcrYf0tY3lHB60NNkmXc4s9bIOgUxi8T/jzmgXzuez/pHXEzlN77IfH8MRQ=" crossorigin="anonymous"></script>
</body>
</html>
