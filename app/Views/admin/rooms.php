<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Manage Rooms - CPC RoomFinder | Admin Portal</title>
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
  <link rel="stylesheet" href="<?= base_url('assets/css/style.css') ?>">
</head>
<body class="portal-layout">

  <!-- Admin Sidebar Component -->
  <?= view('partials/admin_sidebar', ['activeNav' => 'rooms']) ?>

  <!-- Main View Content -->
  <main class="portal-main">
    <div class="portal-topbar">
      <div class="portal-title-group">
        <h1>Manage Rooms</h1>
        <p class="subtitle">Campus classrooms directory, capacity management, and live maintenance tracking</p>
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

    <?php if (session()->getFlashdata('errors')): ?>
      <div class="alert alert-danger" style="background: rgba(239, 68, 68, 0.15); border: 1px solid rgba(239, 68, 68, 0.4); color: #fca5a5; font-size: 0.9rem; padding: 12px 18px; border-radius: 10px; margin-bottom: 20px;">
        <ul style="margin: 0; padding-left: 18px;">
          <?php foreach (session()->getFlashdata('errors') as $err): ?>
            <li><?= esc($err) ?></li>
          <?php endforeach; ?>
        </ul>
      </div>
    <?php endif; ?>

    <!-- Search & Filter Controls matching Image 1 -->
    <div style="display: flex; gap: 14px; align-items: center; justify-content: space-between; margin-bottom: 30px; flex-wrap: wrap;">
      <div style="display: flex; gap: 14px; flex: 1; max-width: 650px; flex-wrap: wrap;">
        <input 
          type="text" 
          id="adminRoomSearch" 
          placeholder="🔍 Search Room No. / name"
          value="<?= esc($filters['q'] ?? '') ?>"
          style="flex: 1; min-width: 220px; padding: 10px 18px; border-radius: var(--radius-pill); background: var(--cpc-navy-card); border: 1px solid rgba(255,255,255,0.15); color: #fff; font-size: 0.9rem;"
        >

        <select 
          id="adminDeptSelect"
          style="padding: 10px 18px; border-radius: var(--radius-pill); background: var(--cpc-navy-card); border: 1px solid rgba(255,255,255,0.15); color: #fff; font-size: 0.9rem; cursor: pointer;"
        >
          <option value="all">All Department ▾</option>
          <?php if (!empty($departments)): ?>
            <?php foreach ($departments as $d): ?>
              <option value="<?= esc($d['code']) ?>"><?= esc($d['code']) ?> Department</option>
            <?php endforeach; ?>
          <?php else: ?>
            <option value="CET">CET Department</option>
            <option value="IT">IT Department</option>
            <option value="Psychology">Psychology Department</option>
          <?php endif; ?>
        </select>

        <select 
          id="adminStatusSelect"
          style="padding: 10px 18px; border-radius: var(--radius-pill); background: var(--cpc-navy-card); border: 1px solid rgba(255,255,255,0.15); color: #fff; font-size: 0.9rem; cursor: pointer;"
        >
          <option value="all">All Status ▾</option>
          <option value="Available">Available</option>
          <option value="Occupied">Occupied</option>
          <option value="Maintenance">Maintenance</option>
        </select>
      </div>

      <!-- Add Room Button -->
      <button 
        type="button" 
        class="btn btn-orange" 
        id="btnOpenAddRoom" 
        data-modal-open="addRoomModal"
        style="padding: 10px 24px; font-weight: 700;"
      >
        + Add Room
      </button>
    </div>

    <!-- Room Cards Grid -->
    <div style="display: grid; grid-template-columns: repeat(auto-fill, minmax(280px, 1fr)); gap: 20px;" id="adminRoomsGrid">
      <?php if (!empty($rooms)): ?>
        <?php foreach ($rooms as $r): ?>
          <?php
          $dotClass = match($r['status']) {
              'Available'   => 'dot-approved',
              'Occupied'    => 'dot-cancelled',
              'Maintenance' => 'dot-maintenance',
              default       => 'dot-pending',
          };
          ?>
          <div class="stat-card" data-room-name="<?= esc($r['room_name']) ?>" data-dept="<?= esc($r['department_code'] ?? '') ?>" data-status="<?= esc($r['status']) ?>">
            <div style="display: flex; justify-content: space-between; align-items: flex-start;">
              <div>
                <h3 style="font-size: 1.3rem; font-weight: 700; color: #ffffff;"><?= esc($r['room_name']) ?></h3>
                <p style="font-size: 0.85rem; color: #94a3b8; margin: 2px 0 0;"><?= esc($r['department_name'] ?? ($r['department_code'] ?? 'College Academic')) ?></p>
              </div>
              <button 
                type="button" 
                class="profile-edit-btn" 
                title="Edit Classroom"
                onclick='openEditRoom(<?= (int)$r['id'] ?>, <?= json_encode($r['room_name']) ?>, <?= (int)$r['department_id'] ?>, <?= json_encode($r['department_code'] ?? '') ?>, <?= (int)$r['capacity'] ?>, <?= json_encode($r['status']) ?>, <?= json_encode($r['maintenance_note'] ?? '') ?>)'
              >
                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                  <path d="M12 20h9"></path>
                  <path d="M16.5 3.5a2.121 2.121 0 0 1 3 3L7 19l-4 1 1-4L16.5 3.5z"></path>
                </svg>
              </button>
            </div>

            <?php if (!empty($r['occupancy']['badge_text'])): ?>
              <div style="font-size: 0.78rem; color: #fb923c; margin-top: 8px; font-weight: 600;">
                ⏳ <?= esc($r['occupancy']['badge_text']) ?>
              </div>
            <?php endif; ?>

            <?php if ($r['status'] === 'Maintenance' && !empty($r['maintenance_note'])): ?>
              <div style="font-size: 0.78rem; color: #f87171; margin-top: 6px; font-style: italic;">
                🔧 <?= esc($r['maintenance_note']) ?>
              </div>
            <?php endif; ?>

            <div style="display: flex; justify-content: space-between; align-items: center; margin-top: 20px;">
              <span class="status-pill">
                <span class="status-dot <?= esc($dotClass) ?>"></span>
                <span style="color: #cbd5e1; font-size: 0.85rem;"><?= esc($r['status']) ?></span>
              </span>
              <span style="font-size: 0.85rem; color: #94a3b8;">👥 <?= esc($r['capacity']) ?> seats</span>
            </div>
          </div>
        <?php endforeach; ?>
      <?php else: ?>
        <div style="grid-column: 1 / -1; text-align: center; color: #94a3b8; padding: 40px 0;">
          No classrooms found. Click <strong>+ Add Room</strong> to create one.
        </div>
      <?php endif; ?>
    </div>

  </main>

  <!-- Add Room Modal -->
  <div class="cpc-modal-backdrop" id="addRoomModal" role="dialog" aria-modal="true">
    <div class="cpc-modal-dialog">
      <button type="button" class="modal-close-btn" data-modal-close>&times;</button>
      <h2 class="modal-title">Add New Classroom</h2>

      <form method="POST" action="<?= base_url('admin/rooms') ?>">
        <?= csrf_field() ?>

        <div class="form-group" style="margin-bottom: 16px;">
          <label style="font-size: 0.85rem; color: #cbd5e1;">Room Name / No.</label>
          <input 
            type="text" 
            name="room_name" 
            class="form-input" 
            placeholder="e.g. Room 225" 
            required
            style="border-radius: var(--radius-md); background: #0f1542; color: #fff; border: 1px solid rgba(255,255,255,0.15);"
          >
        </div>

        <div class="form-group" style="margin-bottom: 16px;">
          <label style="font-size: 0.85rem; color: #cbd5e1;">Department Assignment</label>
          <select 
            name="department_id" 
            class="form-input" 
            required
            style="border-radius: var(--radius-md); background: #0f1542; color: #fff; border: 1px solid rgba(255,255,255,0.15); cursor: pointer;"
          >
            <?php if (!empty($departments)): ?>
              <?php foreach ($departments as $d): ?>
                <option value="<?= esc($d['id']) ?>"><?= esc($d['name']) ?> (<?= esc($d['code']) ?>)</option>
              <?php endforeach; ?>
            <?php endif; ?>
          </select>
        </div>

        <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 16px; margin-bottom: 16px;">
          <div class="form-group">
            <label style="font-size: 0.85rem; color: #cbd5e1;">Max Capacity</label>
            <input 
              type="number" 
              name="capacity" 
              class="form-input" 
              placeholder="30" 
              min="1" 
              required
              style="border-radius: var(--radius-md); background: #0f1542; color: #fff; border: 1px solid rgba(255,255,255,0.15);"
            >
          </div>

          <div class="form-group">
            <label style="font-size: 0.85rem; color: #cbd5e1;">Initial Status</label>
            <select 
              name="status" 
              class="form-input" 
              required
              style="border-radius: var(--radius-md); background: #0f1542; color: #fff; border: 1px solid rgba(255,255,255,0.15); cursor: pointer;"
            >
              <option value="Available">Available</option>
              <option value="Occupied">Occupied</option>
              <option value="Maintenance">Maintenance</option>
            </select>
          </div>
        </div>

        <div class="form-group" style="margin-bottom: 24px;">
          <label style="font-size: 0.85rem; color: #cbd5e1;">Maintenance Note (Optional)</label>
          <input 
            type="text" 
            name="maintenance_note" 
            class="form-input" 
            placeholder="e.g. Broken air conditioning unit" 
            style="border-radius: var(--radius-md); background: #0f1542; color: #fff; border: 1px solid rgba(255,255,255,0.15);"
          >
        </div>

        <div style="display: flex; justify-content: flex-end; gap: 12px;">
          <button type="button" class="btn btn-navy" data-modal-close>Cancel</button>
          <button type="submit" class="btn btn-orange" style="padding: 10px 28px;">Add Classroom</button>
        </div>
      </form>
    </div>
  </div>

  <!-- Edit Room Modal -->
  <div class="cpc-modal-backdrop" id="editRoomModal" role="dialog" aria-modal="true">
    <div class="cpc-modal-dialog">
      <button type="button" class="modal-close-btn" data-modal-close>&times;</button>
      <h2 class="modal-title" style="margin-bottom: 4px;">Edit Classroom</h2>

      <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px; border-bottom: 1px solid rgba(255,255,255,0.1); padding-bottom: 12px;">
        <div>
          <span style="font-weight: 700; font-size: 1.1rem; color: #ffffff;" id="editModalRoomTitle">Room 224</span>
          <span style="color: #94a3b8; margin-left: 8px;" id="editModalDeptSubtitle">CET Department</span>
        </div>
      </div>

      <form id="saveEditRoomForm" method="POST" action="">
        <?= csrf_field() ?>

        <div class="form-group" style="margin-bottom: 16px;">
          <label style="font-size: 0.85rem; color: #cbd5e1;">Room Name / No.</label>
          <input 
            type="text" 
            id="editRoomNameInput"
            name="room_name"
            class="form-input" 
            required
            style="border-radius: var(--radius-md); background: #0f1542; color: #fff; border: 1px solid rgba(255,255,255,0.15);"
          >
        </div>

        <div class="form-group" style="margin-bottom: 16px;">
          <label style="font-size: 0.85rem; color: #cbd5e1;">Department</label>
          <select 
            id="editRoomDeptSelect"
            name="department_id"
            class="form-input"
            required
            style="border-radius: var(--radius-md); background: #0f1542; color: #fff; border: 1px solid rgba(255,255,255,0.15); cursor: pointer;"
          >
            <?php if (!empty($departments)): ?>
              <?php foreach ($departments as $d): ?>
                <option value="<?= esc($d['id']) ?>"><?= esc($d['name']) ?> (<?= esc($d['code']) ?>)</option>
              <?php endforeach; ?>
            <?php endif; ?>
          </select>
        </div>

        <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 16px; margin-bottom: 16px;">
          <div class="form-group">
            <label style="font-size: 0.85rem; color: #cbd5e1;">Max Capacity:</label>
            <input 
              type="number" 
              id="editRoomCapInput"
              name="capacity"
              class="form-input" 
              min="1"
              required
              style="border-radius: var(--radius-md); background: #0f1542; color: #fff; border: 1px solid rgba(255,255,255,0.15);"
            >
          </div>

          <div class="form-group">
            <label style="font-size: 0.85rem; color: #cbd5e1;">Current Status:</label>
            <select 
              id="editRoomStatusSelect"
              name="status"
              class="form-input"
              required
              style="border-radius: var(--radius-md); background: #0f1542; color: #fff; border: 1px solid rgba(255,255,255,0.15); cursor: pointer;"
            >
              <option value="Available">Available</option>
              <option value="Occupied">Occupied</option>
              <option value="Maintenance">Maintenance</option>
            </select>
          </div>
        </div>

        <div class="form-group" style="margin-bottom: 24px;">
          <label style="font-size: 0.85rem; color: #cbd5e1;">Detail if room is Under-maintenance:</label>
          <input 
            type="text" 
            id="editRoomMaintReason"
            name="maintenance_note"
            class="form-input" 
            placeholder="Reason for maintenance"
            style="border-radius: var(--radius-md); background: #0f1542; color: #fff; border: 1px solid rgba(255,255,255,0.15);"
          >
        </div>

        <div style="display: flex; justify-content: space-between; align-items: center;">
          <button 
            type="button" 
            class="btn btn-sm" 
            style="background: rgba(239, 68, 68, 0.2); border: 1px solid rgba(239, 68, 68, 0.4); color: #fca5a5;"
            onclick="submitDeleteRoom()"
          >
            Delete Room
          </button>

          <div style="display: flex; gap: 10px;">
            <button type="button" class="btn btn-navy" data-modal-close>Cancel</button>
            <button type="submit" class="btn btn-lime" style="padding: 10px 24px;">Save Changes</button>
          </div>
        </div>
      </form>

      <!-- Hidden Delete Form -->
      <form id="deleteRoomForm" method="POST" action="" style="display: none;">
        <?= csrf_field() ?>
      </form>
    </div>
  </div>

  <script src="<?= base_url('assets/js/main.js') ?>"></script>
  <script>
    let currentEditRoomId = null;

    function openEditRoom(id, name, deptId, deptCode, cap, status, reason) {
      currentEditRoomId = id;
      document.getElementById('editModalRoomTitle').textContent = name;
      document.getElementById('editModalDeptSubtitle').textContent = deptCode ? deptCode + ' Department' : '';
      document.getElementById('editRoomNameInput').value = name;
      document.getElementById('editRoomDeptSelect').value = deptId;
      document.getElementById('editRoomCapInput').value = cap;
      document.getElementById('editRoomStatusSelect').value = status;
      document.getElementById('editRoomMaintReason').value = reason || '';
      
      const form = document.getElementById('saveEditRoomForm');
      form.action = '<?= base_url('admin/rooms/update/') ?>' + id;
      
      openModal(document.getElementById('editRoomModal'));
    }

    function submitDeleteRoom() {
      if (!currentEditRoomId) return;
      if (confirm('Are you sure you want to permanently delete this classroom? Rooms with active bookings cannot be removed.')) {
        const delForm = document.getElementById('deleteRoomForm');
        delForm.action = '<?= base_url('admin/rooms/delete/') ?>' + currentEditRoomId;
        delForm.submit();
      }
    }

    // Interactive filter
    const searchInp = document.getElementById('adminRoomSearch');
    const deptSel = document.getElementById('adminDeptSelect');
    const statSel = document.getElementById('adminStatusSelect');

    function filterRooms() {
      const q = searchInp ? searchInp.value.toLowerCase().trim() : '';
      const d = deptSel ? deptSel.value : 'all';
      const s = statSel ? statSel.value : 'all';

      document.querySelectorAll('#adminRoomsGrid .stat-card').forEach(card => {
        const name = (card.dataset.roomName || '').toLowerCase();
        const dept = card.dataset.dept || '';
        const status = card.dataset.status || '';

        const matchQ = !q || name.includes(q);
        const matchD = d === 'all' || dept === d;
        const matchS = s === 'all' || status === s;

        if (matchQ && matchD && matchS) {
          card.style.display = '';
        } else {
          card.style.display = 'none';
        }
      });
    }

    if (searchInp) searchInp.addEventListener('input', filterRooms);
    if (deptSel) deptSel.addEventListener('change', filterRooms);
    if (statSel) statSel.addEventListener('change', filterRooms);
  </script>
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-YvpcrYf0tY3lHB60NNkmXc4s9bIOgUxi8T/jzmgXzuez/pHXEzlN77IfH8MRQ=" crossorigin="anonymous"></script>
</body>
</html>
