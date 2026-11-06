<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title><?= esc($title ?? 'My Profile - CPC RoomFinder') ?></title>
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
  <link rel="stylesheet" href="<?= base_url('assets/css/style.css') ?>">
  <style>
    .profile-card-wrapper {
      max-width: 860px;
      margin: 0 auto;
      display: flex;
      flex-direction: column;
      gap: 28px;
      width: 100%;
    }
    .profile-header-card {
      display: flex;
      align-items: center;
      gap: 24px;
      background: var(--cpc-navy-card);
      border: 1px solid rgba(255, 255, 255, 0.12);
      border-radius: var(--radius-xl);
      padding: 28px 32px;
    }
    .profile-avatar-large {
      width: 76px;
      height: 76px;
      border-radius: 50%;
      background: linear-gradient(135deg, var(--cpc-orange), #ff9838);
      color: #11153b;
      font-size: 1.9rem;
      font-weight: 800;
      display: flex;
      align-items: center;
      justify-content: center;
      box-shadow: 0 4px 16px var(--cpc-orange-glow);
      flex-shrink: 0;
    }
    .profile-role-badge {
      display: inline-block;
      padding: 4px 14px;
      border-radius: var(--radius-pill);
      font-size: 0.8rem;
      font-weight: 700;
      text-transform: uppercase;
      letter-spacing: 0.5px;
      background: rgba(245, 130, 32, 0.2);
      color: var(--cpc-orange);
      margin-top: 6px;
    }
    .profile-section-card {
      background: var(--cpc-navy-card);
      border: 1px solid rgba(255, 255, 255, 0.12);
      border-radius: var(--radius-xl);
      padding: 30px 32px;
    }
    .profile-section-title {
      font-size: 1.2rem;
      font-weight: 700;
      color: #ffffff;
      margin-bottom: 20px;
      display: flex;
      align-items: center;
      gap: 10px;
      border-bottom: 1px solid rgba(255, 255, 255, 0.08);
      padding-bottom: 12px;
    }
    .profile-form-grid {
      display: grid;
      grid-template-columns: 1fr 1fr;
      gap: 20px;
    }
    .profile-field-group {
      display: flex;
      flex-direction: column;
      gap: 8px;
    }
    .profile-field-group.full-width {
      grid-column: 1 / -1;
    }
    .profile-field-label {
      font-size: 0.88rem;
      font-weight: 600;
      color: #cbd5e1;
    }
    .profile-input-control {
      background: #0f1542;
      border: 1.5px solid #2d397c;
      border-radius: 8px;
      color: #ffffff;
      padding: 10px 16px;
      font-size: 0.92rem;
      outline: none;
      transition: border-color var(--transition-fast);
      width: 100%;
    }
    .profile-input-control:focus {
      border-color: var(--cpc-orange);
    }
    .profile-input-control:disabled,
    .profile-input-control[readonly] {
      background: rgba(15, 21, 66, 0.5);
      color: #94a3b8;
      cursor: not-allowed;
      border-color: rgba(255, 255, 255, 0.08);
    }
    .input-with-lock {
      position: relative;
      display: flex;
      align-items: center;
    }
    .lock-icon {
      position: absolute;
      right: 14px;
      font-size: 0.9rem;
      color: #64748b;
      pointer-events: none;
    }
    @media (max-width: 768px) {
      .profile-form-grid {
        grid-template-columns: 1fr;
      }
      .profile-header-card {
        flex-direction: column;
        text-align: center;
      }
    }
  </style>
</head>
<body class="portal-layout">

  <!-- Role-Based Navigation Sidebar Partials -->
  <?php
  $userRole = strtolower((string)(session()->get('role') ?? ($userRole ?? 'student')));
  $displayName = session()->get('name') ?? 'John Doe';
  $firstName = session()->get('first_name') ?? 'John';
  $lastName = session()->get('last_name') ?? 'Doe';
  $idNumber = session()->get('id_number') ?? '202401001';
  $email = session()->get('email') ?? 'user@purisima.edu.ph';
  $contact = session()->get('contact_number') ?? '';
  $firstLetter = substr((string)$firstName, 0, 1);
  $lastLetter = substr((string)$lastName, 0, 1);
  $avatar = strtoupper($firstLetter . $lastLetter);

  if ($userRole === 'admin') {
      echo view('partials/admin_sidebar', ['activeNav' => 'profile']);
      $roleLabel = 'System Administrator';
      $deptOrCourse = 'College Administration / IT';
  } elseif ($userRole === 'instructor') {
      echo view('partials/instructor_sidebar', ['activeNav' => 'profile']);
      $roleLabel = 'Faculty / Instructor';
      $deptOrCourse = session()->get('department_name') ?? 'Academic Department';
  } else {
      echo view('partials/student_sidebar', ['activeNav' => 'profile']);
      $roleLabel = 'Student';
      $deptOrCourse = (session()->get('course') ?? 'BSIT') . ' (' . (session()->get('year_level') ?? '3rd Year') . ')';
  }
  ?>

  <!-- Main View Content -->
  <main class="portal-main">
    <div class="portal-topbar">
      <div class="portal-title-group">
        <h1>Account Profile</h1>
        <p class="subtitle">Manage your personal information and account security</p>
      </div>

      <a href="<?= base_url($userRole === 'admin' ? 'admin/overview' : ($userRole === 'instructor' ? 'instructor/bookings' : 'student/dashboard')) ?>" class="btn btn-navy btn-sm">
        ← Back to Dashboard
      </a>
    </div>

    <div class="profile-card-wrapper">

      <?php if (session()->getFlashdata('success')): ?>
        <div class="alert alert-success" style="background: rgba(34, 197, 94, 0.15); border: 1px solid rgba(34, 197, 94, 0.4); color: #86efac; font-size: 0.9rem; padding: 12px 18px; border-radius: 10px;">
          ✅ <?= esc(session()->getFlashdata('success')) ?>
        </div>
      <?php endif; ?>

      <?php if (session()->getFlashdata('error')): ?>
        <div class="alert alert-danger" style="background: rgba(239, 68, 68, 0.15); border: 1px solid rgba(239, 68, 68, 0.4); color: #fca5a5; font-size: 0.9rem; padding: 12px 18px; border-radius: 10px;">
          ⚠️ <?= esc(session()->getFlashdata('error')) ?>
        </div>
      <?php endif; ?>

      <?php if (session()->getFlashdata('errors')): ?>
        <div class="alert alert-danger" style="background: rgba(239, 68, 68, 0.15); border: 1px solid rgba(239, 68, 68, 0.4); color: #fca5a5; font-size: 0.9rem; padding: 12px 18px; border-radius: 10px;">
          <strong>Please resolve the following:</strong>
          <ul style="margin: 6px 0 0 18px;">
            <?php foreach (session()->getFlashdata('errors') as $err): ?>
              <li><?= esc($err) ?></li>
            <?php endforeach; ?>
          </ul>
        </div>
      <?php endif; ?>

      <!-- Header Card with Large Avatar and Role Badge -->
      <div class="profile-header-card">
        <div class="profile-avatar-large"><?= esc($avatar) ?></div>
        <div>
          <h2 style="font-size: 1.6rem; font-weight: 700; color: #ffffff; margin: 0;"><?= esc($displayName) ?></h2>
          <span class="profile-role-badge"><?= esc($roleLabel) ?></span>
          <p style="margin: 8px 0 0; color: #94a3b8; font-size: 0.88rem;">
            CPC ID: <strong style="color: #cbd5e1;"><?= esc($idNumber) ?></strong> &bull; <?= esc($deptOrCourse) ?>
          </p>
        </div>
      </div>

      <!-- Section 1: Personal Details Form -->
      <div class="profile-section-card">
        <h3 class="profile-section-title">
          <span>👤</span> Personal Information
        </h3>

        <form id="editProfileForm" method="POST" action="<?= base_url('profile/update') ?>">
          <?= csrf_field() ?>

          <div class="profile-form-grid">

            <!-- ID Number: Strictly Read-Only / Disabled with Lock Icon per Roadmap -->
            <div class="profile-field-group">
              <label class="profile-field-label">Student / Employee ID Number</label>
              <div class="input-with-lock">
                <input 
                  type="text" 
                  class="profile-input-control" 
                  value="<?= esc($idNumber) ?>" 
                  disabled 
                  readonly 
                  aria-readonly="true"
                >
                <span class="lock-icon" title="ID Number is permanently locked by university records">🔒</span>
              </div>
              <small style="font-size: 0.78rem; color: #64748b; font-style: italic;">
                Assigned by CPC Registrar / HR. Cannot be modified.
              </small>
            </div>

            <!-- Role Badge Display -->
            <div class="profile-field-group">
              <label class="profile-field-label">Institutional Role</label>
              <div class="input-with-lock">
                <input 
                  type="text" 
                  class="profile-input-control" 
                  value="<?= esc(strtoupper($userRole)) ?>" 
                  disabled 
                  readonly
                >
                <span class="lock-icon" title="Role managed by administrator">🔒</span>
              </div>
              <small style="font-size: 0.78rem; color: #64748b; font-style: italic;">
                System privileges are assigned by Administrator.
              </small>
            </div>

            <div class="profile-field-group">
              <label class="profile-field-label" for="profileFirstName">First Name</label>
              <input 
                type="text" 
                class="profile-input-control" 
                id="profileFirstName" 
                name="first_name" 
                value="<?= esc($firstName) ?>" 
                required
              >
            </div>

            <div class="profile-field-group">
              <label class="profile-field-label" for="profileLastName">Last Name</label>
              <input 
                type="text" 
                class="profile-input-control" 
                id="profileLastName" 
                name="last_name" 
                value="<?= esc($lastName) ?>" 
                required
              >
            </div>

            <div class="profile-field-group">
              <label class="profile-field-label" for="profileEmail">Email Address</label>
              <input 
                type="email" 
                class="profile-input-control" 
                id="profileEmail" 
                name="email" 
                value="<?= esc($email) ?>" 
                required
              >
            </div>

            <div class="profile-field-group">
              <label class="profile-field-label" for="profileContact">Contact Mobile Number</label>
              <input 
                type="tel" 
                class="profile-input-control" 
                id="profileContact" 
                name="contact_number" 
                value="<?= esc($contact) ?>"
                placeholder="+63 9XX XXX XXXX"
              >
            </div>

            <div class="profile-field-group full-width">
              <label class="profile-field-label" for="profileDept">Department / Program</label>
              <input 
                type="text" 
                class="profile-input-control" 
                id="profileDept" 
                value="<?= esc($deptOrCourse) ?>"
                disabled
                readonly
              >
            </div>
          </div>

          <div style="display: flex; justify-content: flex-end; margin-top: 24px;">
            <button type="submit" class="btn btn-orange" style="padding: 10px 28px; font-weight: 700;">
              Save Profile Changes
            </button>
          </div>
        </form>
      </div>

      <!-- Section 2: Change Password Form -->
      <div class="profile-section-card">
        <h3 class="profile-section-title">
          <span>🔐</span> Change Password
        </h3>

        <form id="changePasswordForm" method="POST" action="<?= base_url('profile/change-password') ?>">
          <?= csrf_field() ?>

          <div class="profile-form-grid">
            <div class="profile-field-group full-width">
              <label class="profile-field-label" for="currPassword">Current Password</label>
              <input 
                type="password" 
                class="profile-input-control" 
                id="currPassword" 
                name="current_password" 
                placeholder="Enter current password" 
                required
              >
            </div>

            <div class="profile-field-group">
              <label class="profile-field-label" for="newPassword">New Password</label>
              <input 
                type="password" 
                class="profile-input-control" 
                id="newPassword" 
                name="new_password" 
                placeholder="Minimum 8 characters" 
                required
                minlength="8"
              >
            </div>

            <div class="profile-field-group">
              <label class="profile-field-label" for="confirmNewPassword">Confirm New Password</label>
              <input 
                type="password" 
                class="profile-input-control" 
                id="confirmNewPassword" 
                name="password_confirmation" 
                placeholder="Re-enter new password" 
                required
                minlength="8"
              >
            </div>
          </div>

          <div style="display: flex; justify-content: flex-end; margin-top: 24px;">
            <button type="submit" class="btn btn-navy" style="padding: 10px 28px; font-weight: 700;">
              Update Password
            </button>
          </div>
        </form>
      </div>

    </div>
  </main>

  <script src="<?= base_url('assets/js/main.js') ?>"></script>
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-YvpcrYf0tY3lHB60NNkmXc4s9bIOgUxi8T/jzmgXzuez/pHXEzlN77IfH8MRQ=" crossorigin="anonymous"></script>
</body>
</html>
