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
      </div>
      <a href="<?= base_url('admin/overview') ?>" title="Home" style="color: var(--cpc-orange); font-size: 1.5rem;">
        <svg width="28" height="28" viewBox="0 0 24 24" fill="currentColor">
          <path d="M10 20v-6h4v6h5v-8h3L12 3 2 12h3v8z"></path>
        </svg>
      </a>
    </div>

    <!-- Search & Filter Controls matching Image 1 -->
    <div style="display: flex; gap: 14px; align-items: center; justify-content: space-between; margin-bottom: 30px; flex-wrap: wrap;">
      <div style="display: flex; gap: 14px; flex: 1; max-width: 650px; flex-wrap: wrap;">
        <input 
          type="text" 
          id="adminRoomSearch" 
          placeholder="🔍 Search Room No. / name"
          style="flex: 1; min-width: 220px; padding: 10px 18px; border-radius: var(--radius-pill); background: var(--cpc-navy-card); border: 1px solid rgba(255,255,255,0.15); color: #fff; font-size: 0.9rem;"
        >

        <select 
          id="adminDeptSelect"
          style="padding: 10px 18px; border-radius: var(--radius-pill); background: var(--cpc-navy-card); border: 1px solid rgba(255,255,255,0.15); color: #fff; font-size: 0.9rem; cursor: pointer;"
        >
          <option value="all">All Department ▾</option>
          <option value="CET">CET Department</option>
          <option value="IT">IT Department</option>
          <option value="Psychology">Psychology Department</option>
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
      
      <!-- Room 224 -->
      <div class="stat-card" data-room-name="Room 224" data-dept="CET" data-status="Available">
        <div style="display: flex; justify-content: space-between; align-items: flex-start;">
          <div>
            <h3 style="font-size: 1.3rem; font-weight: 700;">Room 224</h3>
            <p style="font-size: 0.85rem; color: #94a3b8;">CET Department</p>
          </div>
          <button 
            type="button" 
            class="profile-edit-btn" 
            title="Edit Room"
            onclick="openEditRoom('Room 224', 'CET Department', 24, 'Available', '')"
          >
            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
              <path d="M12 20h9"></path>
              <path d="M16.5 3.5a2.121 2.121 0 0 1 3 3L7 19l-4 1 1-4L16.5 3.5z"></path>
            </svg>
          </button>
        </div>
        <div style="display: flex; justify-content: space-between; align-items: center; margin-top: 24px;">
          <span class="status-pill">
            <span class="status-dot dot-approved"></span>
            <span style="color: #cbd5e1; font-size: 0.85rem;">Available</span>
          </span>
          <span style="font-size: 0.85rem; color: #94a3b8;">👥 24</span>
        </div>
      </div>

      <!-- Room 223 -->
      <div class="stat-card" data-room-name="Room 223" data-dept="CET" data-status="Available">
        <div style="display: flex; justify-content: space-between; align-items: flex-start;">
          <div>
            <h3 style="font-size: 1.3rem; font-weight: 700;">Room 223</h3>
            <p style="font-size: 0.85rem; color: #94a3b8;">CET Department</p>
          </div>
          <button 
            type="button" 
            class="profile-edit-btn" 
            title="Edit Room"
            onclick="openEditRoom('Room 223', 'CET Department', 30, 'Available', '')"
          >
            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
              <path d="M12 20h9"></path>
              <path d="M16.5 3.5a2.121 2.121 0 0 1 3 3L7 19l-4 1 1-4L16.5 3.5z"></path>
            </svg>
          </button>
        </div>
        <div style="display: flex; justify-content: space-between; align-items: center; margin-top: 24px;">
          <span class="status-pill">
            <span class="status-dot dot-approved"></span>
            <span style="color: #cbd5e1; font-size: 0.85rem;">Available</span>
          </span>
          <span style="font-size: 0.85rem; color: #94a3b8;">👥 30</span>
        </div>
      </div>

      <!-- Lab D -->
      <div class="stat-card" data-room-name="Lab D" data-dept="IT" data-status="Maintenance">
        <div style="display: flex; justify-content: space-between; align-items: flex-start;">
          <div>
            <h3 style="font-size: 1.3rem; font-weight: 700;">Lab D</h3>
            <p style="font-size: 0.85rem; color: #94a3b8;">IT Department</p>
          </div>
          <button 
            type="button" 
            class="profile-edit-btn" 
            title="Edit Room"
            onclick="openEditRoom('Lab D', 'IT Department', 20, 'Maintenance', 'Aircon repair and cable reinstall')"
          >
            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
              <path d="M12 20h9"></path>
              <path d="M16.5 3.5a2.121 2.121 0 0 1 3 3L7 19l-4 1 1-4L16.5 3.5z"></path>
            </svg>
          </button>
        </div>
        <div style="display: flex; justify-content: space-between; align-items: center; margin-top: 24px;">
          <span class="status-pill">
            <span class="status-dot dot-maintenance"></span>
            <span style="color: #cbd5e1; font-size: 0.85rem;">Maintenance</span>
          </span>
          <span style="font-size: 0.85rem; color: #94a3b8;">👥 20</span>
        </div>
      </div>

      <!-- Room 101 -->
      <div class="stat-card" data-room-name="Room 101" data-dept="Psychology" data-status="Occupied">
        <div style="display: flex; justify-content: space-between; align-items: flex-start;">
          <div>
            <h3 style="font-size: 1.3rem; font-weight: 700;">Room 101</h3>
            <p style="font-size: 0.85rem; color: #94a3b8;">Psychology Department</p>
          </div>
          <button 
            type="button" 
            class="profile-edit-btn" 
            title="Edit Room"
            onclick="openEditRoom('Room 101', 'Psychology Department', 30, 'Occupied', '')"
          >
            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
              <path d="M12 20h9"></path>
              <path d="M16.5 3.5a2.121 2.121 0 0 1 3 3L7 19l-4 1 1-4L16.5 3.5z"></path>
            </svg>
          </button>
        </div>
        <div style="margin-top: 14px; font-size: 0.8rem; color: #94a3b8;">
          Booked by M. Tuazon until 2:30 PM
        </div>
        <div style="display: flex; justify-content: space-between; align-items: center; margin-top: 14px;">
          <span class="status-pill">
            <span class="status-dot dot-cancelled"></span>
            <span style="color: #cbd5e1; font-size: 0.85rem;">Occupied</span>
          </span>
          <span style="font-size: 0.85rem; color: #94a3b8;">👥 30</span>
        </div>
      </div>

    </div>
  </main>

  <!-- ADD ROOM MODAL (Image 1 top) -->
  <div class="cpc-modal-backdrop" id="addRoomModal" role="dialog" aria-modal="true">
    <div class="cpc-modal-dialog">
      <button type="button" class="modal-close-btn" data-modal-close>&times;</button>
      <h2 class="modal-title">ADD ROOM</h2>

      <form id="createRoomForm" onsubmit="event.preventDefault(); showToast('Room created successfully!', 'success'); closeModal(document.getElementById('addRoomModal'));">
        <div class="form-group" style="margin-bottom: 18px;">
          <label style="font-size: 0.88rem; color: #cbd5e1;">Room No. / Name</label>
          <input 
            type="text" 
            class="form-input" 
            placeholder="Room 224"
            required
            style="border-radius: var(--radius-md); background: #0f1542; color: #fff; border: 1px solid rgba(255,255,255,0.15);"
          >
        </div>

        <div class="form-group" style="margin-bottom: 18px;">
          <label style="font-size: 0.88rem; color: #cbd5e1;">Department</label>
          <select 
            class="form-input"
            required
            style="border-radius: var(--radius-md); background: #0f1542; color: #fff; border: 1px solid rgba(255,255,255,0.15); cursor: pointer;"
          >
            <option value="CET">CET ▾</option>
            <option value="IT">IT ▾</option>
            <option value="CASE">CASE ▾</option>
            <option value="Marine">Marine ▾</option>
            <option value="Psychology">Psychology ▾</option>
          </select>
        </div>

        <div class="form-group" style="margin-bottom: 26px;">
          <label style="font-size: 0.88rem; color: #cbd5e1;">Room Max. Capacity:</label>
          <input 
            type="number" 
            class="form-input" 
            placeholder="e.g. 30"
            required
            style="border-radius: var(--radius-md); background: #0f1542; color: #fff; border: 1px solid rgba(255,255,255,0.15);"
          >
        </div>

        <div style="display: flex; justify-content: flex-end;">
          <button type="submit" class="btn btn-lime" style="padding: 10px 28px;">Create Room</button>
        </div>
      </form>
    </div>
  </div>

  <!-- EDIT ROOM MODAL (Image 1 bottom) -->
  <div class="cpc-modal-backdrop" id="editRoomModal" role="dialog" aria-modal="true">
    <div class="cpc-modal-dialog">
      <button type="button" class="modal-close-btn" data-modal-close>&times;</button>
      <h2 class="modal-title">EDIT ROOM</h2>
      
      <div style="margin-bottom: 20px; border-bottom: 1px solid rgba(255,255,255,0.1); padding-bottom: 12px; display: flex; justify-content: space-between;">
        <div>
          <span style="font-weight: 700; font-size: 1.1rem; color: #ffffff;" id="editModalRoomTitle">Room 204</span>
          <span style="color: #94a3b8; margin-left: 8px;" id="editModalDeptSubtitle">CET Department</span>
        </div>
        <span style="color: #cbd5e1;" id="editModalCapSubtitle">Capacity: 24</span>
      </div>

      <form id="saveEditRoomForm" onsubmit="event.preventDefault(); showToast('Changes saved successfully!', 'success'); closeModal(document.getElementById('editRoomModal'));">
        <div class="form-group" style="margin-bottom: 16px;">
          <label style="font-size: 0.85rem; color: #cbd5e1;">Room No. / Name</label>
          <input 
            type="text" 
            id="editRoomNameInput"
            class="form-input" 
            placeholder="change room No. / name"
            style="border-radius: var(--radius-md); background: #0f1542; color: #fff; border: 1px solid rgba(255,255,255,0.15);"
          >
        </div>

        <div class="form-group" style="margin-bottom: 16px;">
          <label style="font-size: 0.85rem; color: #cbd5e1;">Department</label>
          <select 
            id="editRoomDeptSelect"
            class="form-input"
            style="border-radius: var(--radius-md); background: #0f1542; color: #fff; border: 1px solid rgba(255,255,255,0.15); cursor: pointer;"
          >
            <option value="CET">CET, Marine, CASE, etc. ▾</option>
            <option value="CET">CET</option>
            <option value="IT">IT</option>
            <option value="CASE">CASE</option>
            <option value="Marine">Marine</option>
          </select>
        </div>

        <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 16px; margin-bottom: 16px;">
          <div class="form-group">
            <label style="font-size: 0.85rem; color: #cbd5e1;">Room Max. Capacity:</label>
            <input 
              type="number" 
              id="editRoomCapInput"
              class="form-input" 
              placeholder="e.g. 30"
              style="border-radius: var(--radius-md); background: #0f1542; color: #fff; border: 1px solid rgba(255,255,255,0.15);"
            >
          </div>

          <div class="form-group">
            <label style="font-size: 0.85rem; color: #cbd5e1;">Current Status:</label>
            <select 
              id="editRoomStatusSelect"
              class="form-input"
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
            class="form-input" 
            placeholder="type the reason (e.g. aircon repair, broken ceilings, etc)"
            style="border-radius: var(--radius-md); background: #0f1542; color: #fff; border: 1px solid rgba(255,255,255,0.15);"
          >
        </div>

        <div style="display: flex; justify-content: flex-end;">
          <button type="submit" class="btn btn-lime" style="padding: 10px 28px;">Save Changes</button>
        </div>
      </form>
    </div>
  </div>

  <script src="<?= base_url('assets/js/main.js') ?>"></script>
  <script>
    function openEditRoom(name, dept, cap, status, reason) {
      document.getElementById('editModalRoomTitle').textContent = name;
      document.getElementById('editModalDeptSubtitle').textContent = dept;
      document.getElementById('editModalCapSubtitle').textContent = 'Capacity: ' + cap;
      document.getElementById('editRoomNameInput').value = name;
      document.getElementById('editRoomCapInput').value = cap;
      document.getElementById('editRoomStatusSelect').value = status;
      document.getElementById('editRoomMaintReason').value = reason || '';
      openModal(document.getElementById('editRoomModal'));
    }

    // Interactive filter
    const searchInp = document.getElementById('adminRoomSearch');
    const deptSel = document.getElementById('adminDeptSelect');
    const statSel = document.getElementById('adminStatusSelect');

    function filterRooms() {
      const q = searchInp.value.toLowerCase();
      const d = deptSel.value;
      const s = statSel.value;

      document.querySelectorAll('#adminRoomsGrid .stat-card').forEach(card => {
        const name = card.dataset.roomName.toLowerCase();
        const dept = card.dataset.dept;
        const status = card.dataset.status;

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

    searchInp.addEventListener('input', filterRooms);
    deptSel.addEventListener('change', filterRooms);
    statSel.addEventListener('change', filterRooms);
  </script>
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-YvpcrYf0tY3lHB60NNkmXc4s9bIOgUxi8T/jzmgXzuez/pHXEzlN77IfH8MRQ=" crossorigin="anonymous"></script>
</body>
</html>
