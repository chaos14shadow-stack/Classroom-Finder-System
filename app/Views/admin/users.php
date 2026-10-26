<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Manage Users - CPC RoomFinder | Admin Portal</title>
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
  <link rel="stylesheet" href="<?= base_url('assets/css/style.css') ?>">
</head>
<body class="portal-layout">

  <!-- Admin Sidebar Component -->
  <?= view('partials/admin_sidebar', ['activeNav' => 'users']) ?>

  <!-- Main View Content -->
  <main class="portal-main">
    <div class="portal-topbar">
      <div class="portal-title-group">
        <h1>Manage Users</h1>
      </div>
      
      <!-- Search & Filters right header -->
      <div style="display: flex; gap: 12px; align-items: center; flex-wrap: wrap;">
        <input 
          type="text" 
          id="userSearchInput" 
          placeholder="🔍 Search by name / ID No."
          style="padding: 8px 18px; border-radius: var(--radius-pill); background: var(--cpc-navy-card); border: 1px solid rgba(255,255,255,0.15); color: #fff; font-size: 0.88rem;"
        >

        <select 
          id="userRoleSelect"
          style="padding: 8px 16px; border-radius: var(--radius-pill); background: var(--cpc-navy-card); border: 1px solid rgba(255,255,255,0.15); color: #fff; font-size: 0.88rem; cursor: pointer;"
        >
          <option value="all">All Roles ▾</option>
          <option value="Student">Student</option>
          <option value="Instructor">Instructor</option>
        </select>

        <select 
          id="userStatusSelect"
          style="padding: 8px 16px; border-radius: var(--radius-pill); background: var(--cpc-navy-card); border: 1px solid rgba(255,255,255,0.15); color: #fff; font-size: 0.88rem; cursor: pointer;"
        >
          <option value="all">All Status ▾</option>
          <option value="Active">Active</option>
          <option value="Inactive">Inactive</option>
        </select>
      </div>
    </div>

    <!-- Main Grid: Table on Left, Profile Card on Right -->
    <div style="display: grid; grid-template-columns: 1fr 340px; gap: 30px; align-items: start;">
      
      <!-- Users Table Container -->
      <div class="table-container">
        <table class="cpc-table" id="adminUsersTable">
          <thead>
            <tr>
              <th>NAME</th>
              <th>ID NO.</th>
              <th>ROLE</th>
              <th>STATUS</th>
            </tr>
          </thead>
          <tbody>
            <!-- Row 1 -->
            <tr 
              class="user-table-row active-row" 
              style="cursor: pointer;"
              onclick="selectUser('John Doe', 'Student', '202401001', 'JohnDoe@gmail.com', 'BSIT', '3rd Year', 'Active', 'JD')"
              data-name="John Doe"
              data-id="202401001"
              data-role="Student"
              data-status="Active"
            >
              <td style="font-weight: 600;">John Doe</td>
              <td style="color: #cbd5e1;">202401001</td>
              <td style="color: #cbd5e1;">Student</td>
              <td>
                <span class="status-pill">
                  <span class="status-dot dot-approved"></span>
                  <span style="font-size: 0.85rem; color: #cbd5e1;">Active</span>
                </span>
              </td>
            </tr>

            <!-- Row 2 -->
            <tr 
              class="user-table-row" 
              style="cursor: pointer;"
              onclick="selectUser('Myles Dela Cruz', 'Instructor', '202400645', 'mdelacruz@purisima.edu.ph', 'CET', 'Faculty', 'Active', 'MC')"
              data-name="Myles Dela Cruz"
              data-id="202400645"
              data-role="Instructor"
              data-status="Active"
            >
              <td style="font-weight: 600;">Myles Dela Cruz</td>
              <td style="color: #cbd5e1;">202400645</td>
              <td style="color: #cbd5e1;">Instructor</td>
              <td>
                <span class="status-pill">
                  <span class="status-dot dot-approved"></span>
                  <span style="font-size: 0.85rem; color: #cbd5e1;">Active</span>
                </span>
              </td>
            </tr>

            <!-- Row 3 -->
            <tr 
              class="user-table-row" 
              style="cursor: pointer;"
              onclick="selectUser('Kathy Tuazon', 'Student', '202401289', 'kathy.tuazon@gmail.com', 'BSCS', '2nd Year', 'Active', 'KT')"
              data-name="Kathy Tuazon"
              data-id="202401289"
              data-role="Student"
              data-status="Active"
            >
              <td style="font-weight: 600;">Kathy Tuazon</td>
              <td style="color: #cbd5e1;">202401289</td>
              <td style="color: #cbd5e1;">Student</td>
              <td>
                <span class="status-pill">
                  <span class="status-dot dot-approved"></span>
                  <span style="font-size: 0.85rem; color: #cbd5e1;">Active</span>
                </span>
              </td>
            </tr>

            <!-- Row 4 -->
            <tr 
              class="user-table-row" 
              style="cursor: pointer;"
              onclick="selectUser('Nathan Ayopela', 'Instructor', '202401179', 'nayopela@purisima.edu.ph', 'CASE', 'Associate Prof', 'Active', 'NA')"
              data-name="Nathan Ayopela"
              data-id="202401179"
              data-role="Instructor"
              data-status="Active"
            >
              <td style="font-weight: 600;">Nathan Ayopela</td>
              <td style="color: #cbd5e1;">202401179</td>
              <td style="color: #cbd5e1;">Instructor</td>
              <td>
                <span class="status-pill">
                  <span class="status-dot dot-approved"></span>
                  <span style="font-size: 0.85rem; color: #cbd5e1;">Active</span>
                </span>
              </td>
            </tr>

            <!-- Row 5 -->
            <tr 
              class="user-table-row" 
              style="cursor: pointer;"
              onclick="selectUser('Nica Haliarsis', 'Student', '202300796', 'nica.haliarsis@gmail.com', 'BSIT', '4th Year', 'Active', 'NH')"
              data-name="Nica Haliarsis"
              data-id="202300796"
              data-role="Student"
              data-status="Active"
            >
              <td style="font-weight: 600;">Nica Haliarsis</td>
              <td style="color: #cbd5e1;">202300796</td>
              <td style="color: #cbd5e1;">Student</td>
              <td>
                <span class="status-pill">
                  <span class="status-dot dot-approved"></span>
                  <span style="font-size: 0.85rem; color: #cbd5e1;">Active</span>
                </span>
              </td>
            </tr>
          </tbody>
        </table>
      </div>

      <!-- Right User Detail Card matching Image 1 middle -->
      <div 
        id="userProfileCard" 
        style="background: #151d52; border: 2px solid #22c55e; border-radius: 20px; padding: 26px 24px; box-shadow: 0 12px 30px rgba(0,0,0,0.4);"
      >
        <div style="display: flex; align-items: center; gap: 16px; margin-bottom: 22px;">
          <div 
            id="detailAvatar" 
            style="width: 56px; height: 56px; border-radius: 50%; background: #38bdf8; color: #0a1128; font-size: 1.4rem; font-weight: 800; display: flex; align-items: center; justify-content: center;"
          >
            JD
          </div>
          <div>
            <h3 style="font-size: 1.25rem; font-weight: 700; color: #ffffff;" id="detailName">John Doe</h3>
            <p style="font-size: 0.85rem; color: #94a3b8;" id="detailRole">Student</p>
          </div>
        </div>

        <div style="display: flex; flex-direction: column; gap: 12px; font-size: 0.88rem; border-top: 1px solid rgba(255,255,255,0.08); padding-top: 18px; margin-bottom: 24px;">
          <div style="display: flex; justify-content: space-between;">
            <span style="color: #94a3b8;">ID No.:</span>
            <span style="font-weight: 600;" id="detailId">202401001</span>
          </div>
          <div style="display: flex; justify-content: space-between;">
            <span style="color: #94a3b8;">Email:</span>
            <span style="font-weight: 600;" id="detailEmail">JohnDoe@gmail.com</span>
          </div>
          <div style="display: flex; justify-content: space-between;">
            <span style="color: #94a3b8;">Course / Dept:</span>
            <span style="font-weight: 600;" id="detailCourse">BSIT</span>
          </div>
          <div style="display: flex; justify-content: space-between;">
            <span style="color: #94a3b8;">Yr. level:</span>
            <span style="font-weight: 600;" id="detailYr">3rd Year</span>
          </div>
          <div style="display: flex; justify-content: space-between; align-items: center;">
            <span style="color: #94a3b8;">Status:</span>
            <span style="font-weight: 700; color: #22c55e;" id="detailStatus">Active</span>
          </div>
        </div>

        <div style="display: flex; gap: 12px;">
          <button 
            type="button" 
            class="btn btn-orange" 
            style="flex: 1; padding: 9px;"
            onclick="showToast('Editing profile for ' + document.getElementById('detailName').textContent, 'info')"
          >
            Edit
          </button>
          <button 
            type="button" 
            class="btn" 
            style="flex: 1; padding: 9px; background: transparent; border: 1.5px solid #ef4444; color: #ef4444;"
            onclick="toggleUserStatus()"
          >
            Deactivate
          </button>
        </div>
      </div>

    </div>
  </main>

  <script src="<?= base_url('assets/js/main.js') ?>"></script>
  <script>
    function selectUser(name, role, id, email, course, yr, status, initials) {
      document.getElementById('detailName').textContent = name;
      document.getElementById('detailRole').textContent = role;
      document.getElementById('detailId').textContent = id;
      document.getElementById('detailEmail').textContent = email;
      document.getElementById('detailCourse').textContent = course;
      document.getElementById('detailYr').textContent = yr;
      document.getElementById('detailStatus').textContent = status;
      document.getElementById('detailAvatar').textContent = initials;
    }

    function toggleUserStatus() {
      const statusEl = document.getElementById('detailStatus');
      if (statusEl.textContent === 'Active') {
        statusEl.textContent = 'Inactive';
        statusEl.style.color = '#ef4444';
        showToast('User deactivated', 'error');
      } else {
        statusEl.textContent = 'Active';
        statusEl.style.color = '#22c55e';
        showToast('User reactivated', 'success');
      }
    }

    // Filter functionality
    const searchInp = document.getElementById('userSearchInput');
    const roleSel = document.getElementById('userRoleSelect');
    const statSel = document.getElementById('userStatusSelect');

    function filterUsers() {
      const q = searchInp.value.toLowerCase();
      const r = roleSel.value;
      const s = statSel.value;

      document.querySelectorAll('#adminUsersTable tbody tr').forEach(row => {
        const name = row.dataset.name.toLowerCase();
        const id = row.dataset.id.toLowerCase();
        const role = row.dataset.role;
        const status = row.dataset.status;

        const matchQ = !q || name.includes(q) || id.includes(q);
        const matchR = r === 'all' || role === r;
        const matchS = s === 'all' || status === s;

        if (matchQ && matchR && matchS) {
          row.style.display = '';
        } else {
          row.style.display = 'none';
        }
      });
    }

    searchInp.addEventListener('input', filterUsers);
    roleSel.addEventListener('change', filterUsers);
    statSel.addEventListener('change', filterUsers);
  </script>
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-YvpcrYf0tY3lHB60NNkmXc4s9bIOgUxi8T/jzmgXzuez/pHXEzlN77IfH8MRQ=" crossorigin="anonymous"></script>
</body>
</html>
