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
        <p class="subtitle">User directory, institutional role allocation, and account moderation</p>
      </div>
      
      <!-- Search & Filters right header -->
      <div style="display: flex; gap: 12px; align-items: center; flex-wrap: wrap;">
        <input 
          type="text" 
          id="userSearchInput" 
          placeholder="🔍 Search by name / ID No."
          value="<?= esc($search ?? '') ?>"
          style="padding: 8px 18px; border-radius: var(--radius-pill); background: var(--cpc-navy-card); border: 1px solid rgba(255,255,255,0.15); color: #fff; font-size: 0.88rem;"
        >

        <select 
          id="userRoleSelect"
          style="padding: 8px 16px; border-radius: var(--radius-pill); background: var(--cpc-navy-card); border: 1px solid rgba(255,255,255,0.15); color: #fff; font-size: 0.88rem; cursor: pointer;"
        >
          <option value="all">All Roles ▾</option>
          <option value="student">Student</option>
          <option value="instructor">Instructor</option>
          <option value="admin">Admin</option>
        </select>

        <select 
          id="userStatusSelect"
          style="padding: 8px 16px; border-radius: var(--radius-pill); background: var(--cpc-navy-card); border: 1px solid rgba(255,255,255,0.15); color: #fff; font-size: 0.88rem; cursor: pointer;"
        >
          <option value="all">All Status ▾</option>
          <option value="active">Active</option>
          <option value="inactive">Inactive</option>
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
            <?php if (!empty($users)): ?>
              <?php foreach ($users as $index => $u): ?>
                <?php
                $fullName = trim($u['first_name'] . ' ' . $u['last_name']);
                $isActive = ((int)$u['is_active'] === 1);
                $statusText = $isActive ? 'Active' : 'Inactive';
                $dotClass = $isActive ? 'dot-approved' : 'dot-cancelled';
                $firstLetter = substr((string)$u['first_name'], 0, 1);
                $lastLetter = substr((string)$u['last_name'], 0, 1);
                $initials = strtoupper($firstLetter . $lastLetter);
                $deptCourse = $u['course'] ?? ($u['department_code'] ?? ($u['department_name'] ?? 'General'));
                $yrLevel = $u['year_level'] ?? ($u['role_name'] === 'instructor' ? 'Faculty' : '—');
                ?>
                <tr 
                  class="user-table-row <?= $index === 0 ? 'active-row' : '' ?>" 
                  style="cursor: pointer;"
                  onclick='selectUser(<?= (int)$u['id'] ?>, <?= json_encode($fullName) ?>, <?= json_encode(ucfirst($u['role_name'])) ?>, <?= (int)$u['role_id'] ?>, <?= json_encode($u['id_number']) ?>, <?= json_encode($u['email']) ?>, <?= json_encode($deptCourse) ?>, <?= json_encode($yrLevel) ?>, <?= json_encode($statusText) ?>, <?= json_encode($initials) ?>, <?= $isActive ? 'true' : 'false' ?>)'
                  data-name="<?= esc(strtolower($fullName)) ?>"
                  data-id="<?= esc(strtolower($u['id_number'])) ?>"
                  data-role="<?= esc(strtolower($u['role_name'])) ?>"
                  data-status="<?= esc(strtolower($statusText)) ?>"
                >
                  <td style="font-weight: 600; color: #ffffff;"><?= esc($fullName) ?></td>
                  <td style="color: #cbd5e1;"><?= esc($u['id_number']) ?></td>
                  <td style="color: #cbd5e1; text-transform: capitalize;"><?= esc($u['role_name']) ?></td>
                  <td>
                    <span class="status-pill">
                      <span class="status-dot <?= esc($dotClass) ?>"></span>
                      <span style="font-size: 0.85rem; color: #cbd5e1;"><?= esc($statusText) ?></span>
                    </span>
                  </td>
                </tr>
              <?php endforeach; ?>
            <?php else: ?>
              <tr>
                <td colspan="4" style="text-align: center; color: #94a3b8; padding: 32px 0;">
                  No user accounts found.
                </td>
              </tr>
            <?php endif; ?>
          </tbody>
        </table>
      </div>

      <!-- Right User Detail Card -->
      <?php
      $firstUser = !empty($users) ? $users[0] : null;
      $fName = $firstUser ? trim($firstUser['first_name'] . ' ' . $firstUser['last_name']) : 'User Detail';
      $fRole = $firstUser ? ucfirst($firstUser['role_name']) : 'Role';
      $fId = $firstUser ? $firstUser['id_number'] : '—';
      $fEmail = $firstUser ? $firstUser['email'] : '—';
      $fCourse = $firstUser ? ($firstUser['course'] ?? ($firstUser['department_code'] ?? '—')) : '—';
      $fYr = $firstUser ? ($firstUser['year_level'] ?? '—') : '—';
      $fActive = $firstUser ? ((int)$firstUser['is_active'] === 1) : true;
      $fInitials = $firstUser ? strtoupper(substr($firstUser['first_name'], 0, 1) . substr($firstUser['last_name'], 0, 1)) : 'UD';
      $fUserId = $firstUser ? (int)$firstUser['id'] : 0;
      ?>
      <div 
        id="userProfileCard" 
        style="background: #151d52; border: 2px solid <?= $fActive ? '#22c55e' : '#ef4444' ?>; border-radius: 20px; padding: 26px 24px; box-shadow: 0 12px 30px rgba(0,0,0,0.4);"
      >
        <div style="display: flex; align-items: center; gap: 16px; margin-bottom: 22px;">
          <div 
            id="detailAvatar" 
            style="width: 56px; height: 56px; border-radius: 50%; background: #38bdf8; color: #0a1128; font-size: 1.4rem; font-weight: 800; display: flex; align-items: center; justify-content: center;"
          >
            <?= esc($fInitials) ?>
          </div>
          <div>
            <h3 style="font-size: 1.25rem; font-weight: 700; color: #ffffff; margin: 0;" id="detailName"><?= esc($fName) ?></h3>
            <p style="font-size: 0.85rem; color: #94a3b8; margin: 4px 0 0;" id="detailRole"><?= esc($fRole) ?></p>
          </div>
        </div>

        <div style="display: flex; flex-direction: column; gap: 12px; font-size: 0.88rem; border-top: 1px solid rgba(255,255,255,0.08); padding-top: 18px; margin-bottom: 24px;">
          <div style="display: flex; justify-content: space-between;">
            <span style="color: #94a3b8;">ID No.:</span>
            <span style="font-weight: 600; color: #ffffff;" id="detailId"><?= esc($fId) ?></span>
          </div>
          <div style="display: flex; justify-content: space-between;">
            <span style="color: #94a3b8;">Email:</span>
            <span style="font-weight: 600; color: #ffffff;" id="detailEmail"><?= esc($fEmail) ?></span>
          </div>
          <div style="display: flex; justify-content: space-between;">
            <span style="color: #94a3b8;">Course / Dept:</span>
            <span style="font-weight: 600; color: #ffffff;" id="detailCourse"><?= esc($fCourse) ?></span>
          </div>
          <div style="display: flex; justify-content: space-between;">
            <span style="color: #94a3b8;">Yr. level:</span>
            <span style="font-weight: 600; color: #ffffff;" id="detailYr"><?= esc($fYr) ?></span>
          </div>
          <div style="display: flex; justify-content: space-between; align-items: center;">
            <span style="color: #94a3b8;">Status:</span>
            <span style="font-weight: 700; color: <?= $fActive ? '#22c55e' : '#ef4444' ?>;" id="detailStatus"><?= $fActive ? 'Active' : 'Inactive' ?></span>
          </div>
        </div>

        <div style="display: flex; gap: 12px;">
          <button 
            type="button" 
            class="btn btn-orange" 
            style="flex: 1; padding: 9px; font-weight: 700;"
            onclick="openChangeRoleModal()"
          >
            Change Role
          </button>

          <!-- Toggle Status Form -->
          <form id="toggleUserForm" method="POST" action="<?= base_url('admin/users/toggle/' . $fUserId) ?>" style="flex: 1;">
            <?= csrf_field() ?>
            <button 
              type="submit" 
              class="btn" 
              id="btnToggleStatus"
              style="width: 100%; padding: 9px; background: transparent; border: 1.5px solid <?= $fActive ? '#ef4444' : '#22c55e' ?>; color: <?= $fActive ? '#ef4444' : '#22c55e' ?>; font-weight: 700;"
            >
              <?= $fActive ? 'Deactivate' : 'Activate' ?>
            </button>
          </form>
        </div>
      </div>

    </div>
  </main>

  <!-- Change Role Modal -->
  <div class="cpc-modal-backdrop" id="changeRoleModal" role="dialog" aria-modal="true">
    <div class="cpc-modal-dialog">
      <button type="button" class="modal-close-btn" data-modal-close>&times;</button>
      <h2 class="modal-title">Change User Role</h2>

      <form id="changeRoleForm" method="POST" action="">
        <?= csrf_field() ?>
        
        <p style="color: #cbd5e1; font-size: 0.92rem; margin-bottom: 16px;">
          Select a new system role for <strong id="modalRoleTargetName" style="color: #ffffff;">User</strong>:
        </p>

        <div class="form-group" style="margin-bottom: 24px;">
          <label style="font-size: 0.85rem; color: #cbd5e1;">Target Role</label>
          <select 
            name="role_id" 
            id="modalRoleSelect"
            class="form-input" 
            required
            style="border-radius: var(--radius-md); background: #0f1542; color: #fff; border: 1px solid rgba(255,255,255,0.15); cursor: pointer;"
          >
            <?php if (!empty($roles)): ?>
              <?php foreach ($roles as $r): ?>
                <option value="<?= esc($r['id']) ?>"><?= ucfirst(esc($r['name'])) ?> (<?= esc($r['description']) ?>)</option>
              <?php endforeach; ?>
            <?php endif; ?>
          </select>
        </div>

        <div style="display: flex; justify-content: flex-end; gap: 10px;">
          <button type="button" class="btn btn-navy" data-modal-close>Cancel</button>
          <button type="submit" class="btn btn-lime">Update Role</button>
        </div>
      </form>
    </div>
  </div>

  <script src="<?= base_url('assets/js/main.js') ?>"></script>
  <script>
    let selectedUserId = <?= (int)$fUserId ?>;
    let selectedUserName = <?= json_encode($fName) ?>;
    let selectedRoleId = 1;

    function selectUser(id, name, role, roleId, idNumber, email, course, yr, status, initials, isActive) {
      selectedUserId = id;
      selectedUserName = name;
      selectedRoleId = roleId;

      document.getElementById('detailName').textContent = name;
      document.getElementById('detailRole').textContent = role;
      document.getElementById('detailId').textContent = idNumber;
      document.getElementById('detailEmail').textContent = email;
      document.getElementById('detailCourse').textContent = course;
      document.getElementById('detailYr').textContent = yr;
      document.getElementById('detailStatus').textContent = status;
      document.getElementById('detailAvatar').textContent = initials;

      const card = document.getElementById('userProfileCard');
      const toggleBtn = document.getElementById('btnToggleStatus');
      const toggleForm = document.getElementById('toggleUserForm');

      toggleForm.action = '<?= base_url('admin/users/toggle/') ?>' + id;

      if (isActive) {
        card.style.borderColor = '#22c55e';
        document.getElementById('detailStatus').style.color = '#22c55e';
        toggleBtn.textContent = 'Deactivate';
        toggleBtn.style.borderColor = '#ef4444';
        toggleBtn.style.color = '#ef4444';
      } else {
        card.style.borderColor = '#ef4444';
        document.getElementById('detailStatus').style.color = '#ef4444';
        toggleBtn.textContent = 'Activate';
        toggleBtn.style.borderColor = '#22c55e';
        toggleBtn.style.color = '#22c55e';
      }
    }

    function openChangeRoleModal() {
      if (!selectedUserId) return;
      document.getElementById('modalRoleTargetName').textContent = selectedUserName;
      const roleForm = document.getElementById('changeRoleForm');
      roleForm.action = '<?= base_url('admin/users/change-role/') ?>' + selectedUserId;
      openModal(document.getElementById('changeRoleModal'));
    }

    // Filter functionality
    const searchInp = document.getElementById('userSearchInput');
    const roleSel = document.getElementById('userRoleSelect');
    const statSel = document.getElementById('userStatusSelect');

    function filterUsers() {
      const q = searchInp ? searchInp.value.toLowerCase().trim() : '';
      const r = roleSel ? roleSel.value.toLowerCase() : 'all';
      const s = statSel ? statSel.value.toLowerCase() : 'all';

      document.querySelectorAll('#adminUsersTable tbody tr').forEach(row => {
        if (!row.dataset.name) return;
        const name = row.dataset.name.toLowerCase();
        const id = row.dataset.id.toLowerCase();
        const role = row.dataset.role.toLowerCase();
        const status = row.dataset.status.toLowerCase();

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

    if (searchInp) searchInp.addEventListener('input', filterUsers);
    if (roleSel) roleSel.addEventListener('change', filterUsers);
    if (statSel) statSel.addEventListener('change', filterUsers);
  </script>
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-YvpcrYf0tY3lHB60NNkmXc4s9bIOgUxi8T/jzmgXzuez/pHXEzlN77IfH8MRQ=" crossorigin="anonymous"></script>
</body>
</html>
