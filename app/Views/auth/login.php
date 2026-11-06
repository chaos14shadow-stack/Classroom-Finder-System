<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Log In - CPC RoomFinder | Colegio De la Purisima Concepcion</title>
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
  <link rel="stylesheet" href="<?= base_url('assets/css/style.css') ?>">
</head>
<body class="login-page">

  <!-- Header Bar -->
  <header class="cpc-navbar">
    <a class="brand" href="<?= base_url() ?>" id="loginBrandLink">
      <img class="brand-icon" src="<?= base_url('assets/img/door-orange.svg') ?>" alt="CPC RoomFinder Logo">
      <span>CPC RoomFinder</span>
    </a>

    <div class="school-badge">
      <img class="seal-icon" src="<?= base_url('assets/img/cpc_seal.png') ?>" alt="Colegio De la Purisima Concepcion Seal">
      <span>Colegio De la Purisima Concepcion</span>
    </div>
  </header>

  <!-- Two Column Main Container -->
  <main class="login-container">

    <!-- Left Column: Find & Request Showcase -->
    <section class="login-showcase">
      <div class="showcase-header">
        <h2>Find &amp; Request</h2>
        <p class="subhead">Available Campus Rooms in Seconds</p>
      </div>

      <!-- 2x2 Photo Collage Grid -->
      <div class="collage-grid">
        <div class="collage-item">
          <img src="<?= base_url('assets/img/collage_room1.jpg') ?>" alt="Classroom with windows and air conditioning">
        </div>
        
        <div class="collage-item">
          <img src="<?= base_url('assets/img/collage_phone.png') ?>" alt="CPC RoomFinder mobile app on smartphone">
        </div>

        <div class="collage-item">
          <img src="<?= base_url('assets/img/collage_dashboard.png') ?>" alt="CPC RoomFinder room search and status table">
        </div>

        <div class="collage-item">
          <img src="<?= base_url('assets/img/collage_room2.jpg') ?>" alt="Classroom with chalkboard, TV and student desks">
        </div>
      </div>

      <p class="showcase-caption">
        Locate vacant classrooms, check real-time availability, and notify your instructor to book a space
      </p>
    </section>

    <!-- Right Column: Login Card & Form -->
    <section class="login-card-wrapper">
      <div class="login-brand-header">
        <img class="login-door" src="<?= base_url('assets/img/door-orange.svg') ?>" alt="Door Icon">
        <div>
          <h1>Welcome !</h1>
          <h2>to CPC RoomFinder</h2>
        </div>
      </div>

      <?php if (session()->getFlashdata('error')): ?>
        <div class="alert alert-danger" style="background: rgba(239, 68, 68, 0.15); border: 1px solid rgba(239, 68, 68, 0.4); color: #fca5a5; font-size: 0.88rem; padding: 10px 14px; border-radius: 8px; margin-bottom: 16px;">
          <?= esc(session()->getFlashdata('error')) ?>
        </div>
      <?php endif; ?>

      <?php if (session()->getFlashdata('success')): ?>
        <div class="alert alert-success" style="background: rgba(34, 197, 94, 0.15); border: 1px solid rgba(34, 197, 94, 0.4); color: #86efac; font-size: 0.88rem; padding: 10px 14px; border-radius: 8px; margin-bottom: 16px;">
          <?= esc(session()->getFlashdata('success')) ?>
        </div>
      <?php endif; ?>

      <form 
        class="login-form" 
        id="cpcLoginForm"
        method="POST"
        action="<?= base_url('login') ?>"
        data-student-url="<?= base_url('student/dashboard') ?>"
        data-instructor-url="<?= base_url('instructor/bookings') ?>"
        data-admin-url="<?= base_url('admin/overview') ?>"
      >
        <?= csrf_field() ?>

        <div class="form-group">
          <label for="loginUsername">Username / ID Number:</label>
          <input 
            type="text" 
            class="form-input" 
            id="loginUsername" 
            name="username" 
            placeholder="202401001" 
            value="<?= esc(old('username')) ?>"
            required
            autocomplete="username"
          >
        </div>

        <div class="form-group">
          <label for="loginPassword">Password:</label>
          <div style="position: relative; width: 100%;">
            <input 
              type="password" 
              class="form-input" 
              id="loginPassword" 
              name="password" 
              placeholder="••••••••••••" 
              required
              autocomplete="current-password"
              style="padding-right: 46px;"
            >
            <button 
              type="button" 
              id="togglePasswordBtn" 
              title="Show password" 
              aria-label="Show password" 
              style="position: absolute; right: 14px; top: 50%; transform: translateY(-50%); background: none; border: none; padding: 4px; cursor: pointer; color: #64748b; display: flex; align-items: center; justify-content: center; z-index: 2;"
            >
              <svg id="eyeIconShow" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                <path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"></path>
                <circle cx="12" cy="12" r="3"></circle>
              </svg>
              <svg id="eyeIconHide" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="display: none;">
                <path d="M17.94 17.94A10.07 10.07 0 0 1 12 20c-7 0-11-8-11-8a18.45 18.45 0 0 1 5.06-5.94M9.9 4.24A9.12 9.12 0 0 1 12 4c7 0 11 8 11 8a18.5 18.5 0 0 1-2.16 3.19m-6.72-1.07a3 3 0 1 1-4.24-4.24"></path>
                <line x1="1" y1="1" x2="23" y2="23"></line>
              </svg>
            </button>
          </div>
        </div>

        <div class="login-actions">
          <button type="submit" class="btn btn-orange" id="btnLoginSubmit">Log In</button>
          <a href="<?= base_url() ?>" class="btn btn-navy" id="btnLoginBack">Back</a>
        </div>
      </form>

      <!-- Quick Tester Demo Role Switcher -->
      <div class="role-switcher-box">
        <p>Quick Demo Sign-In (Click to Autofill):</p>
        <div class="role-chips">
          <button type="button" class="role-chip" data-role="student" id="demoStudentBtn">🎓 Student</button>
          <button type="button" class="role-chip" data-role="instructor" id="demoInstructorBtn">👨‍🏫 Instructor</button>
          <button type="button" class="role-chip" data-role="admin" id="demoAdminBtn">⚙️ Admin</button>
        </div>
        <div style="margin-top: 14px; font-size: 0.85rem;">
          <span style="color: #94a3b8;">Don't have an account?</span> 
          <a href="<?= base_url('register') ?>" style="color: var(--cpc-orange); font-weight: 600; text-decoration: underline;">Create Account</a>
        </div>
      </div>
    </section>

  </main>

  <script src="<?= base_url('assets/js/main.js') ?>"></script>
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-YvpcrYf0tY3lHB60NNkmXc4s9bIOgUxi8T/jzmgXzuez/pHXEzlN77IfH8MRQ=" crossorigin="anonymous"></script>
</body>
</html>
