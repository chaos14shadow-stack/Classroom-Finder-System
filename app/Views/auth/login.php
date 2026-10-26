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

      <form 
        class="login-form" 
        id="cpcLoginForm"
        data-student-url="<?= base_url('student/dashboard') ?>"
        data-instructor-url="<?= base_url('instructor/bookings') ?>"
        data-admin-url="<?= base_url('admin/overview') ?>"
      >
        <div class="form-group">
          <label for="loginUsername">Username:</label>
          <input 
            type="text" 
            class="form-input" 
            id="loginUsername" 
            name="username" 
            placeholder="202401289" 
            required
            autocomplete="username"
          >
        </div>

        <div class="form-group">
          <label for="loginPassword">Password:</label>
          <input 
            type="password" 
            class="form-input" 
            id="loginPassword" 
            name="password" 
            placeholder="••••••••••••" 
            required
            autocomplete="current-password"
          >
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
