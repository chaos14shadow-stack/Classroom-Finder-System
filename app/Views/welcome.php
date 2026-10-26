<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Welcome - CPC RoomFinder | Colegio De la Purisima Concepcion</title>
  <link rel="stylesheet" href="<?= base_url('assets/css/style.css') ?>">
</head>
<body class="welcome-body">

  <!-- Top Navigation Bar -->
  <header class="cpc-navbar">
    <a class="brand" href="<?= base_url() ?>" id="navBrandLogo">
      <img class="brand-icon" src="<?= base_url('assets/img/door-orange.svg') ?>" alt="CPC RoomFinder Logo">
      <span>CPC RoomFinder</span>
    </a>
    
    <button class="menu-btn" id="menuToggleBtn" aria-label="Toggle Navigation Menu">
      <span></span>
      <span></span>
      <span></span>
    </button>
  </header>

  <!-- Slideout Drawer Navigation -->
  <aside class="cpc-drawer" id="cpcDrawer" aria-label="Mobile Navigation">
    <div class="drawer-header">
      <div class="brand">
        <img class="brand-icon" src="<?= base_url('assets/img/door-orange.svg') ?>" alt="Logo">
        <span>CPC RoomFinder</span>
      </div>
      <button class="drawer-close" id="drawerClose" aria-label="Close menu">&times;</button>
    </div>
    <nav class="drawer-nav">
      <a href="<?= base_url() ?>" class="active">🏠 Home</a>
      <a href="<?= base_url('login') ?>">🔐 Log In</a>
      <a href="<?= base_url('register') ?>">📝 Create Account</a>
      <hr style="border: 0; border-top: 1px solid rgba(255,255,255,0.1); margin: 8px 0;">
      <a href="<?= base_url('student/dashboard') ?>">🎓 Student Portal</a>
      <a href="<?= base_url('instructor/bookings') ?>">👨‍🏫 Instructor Portal</a>
      <a href="<?= base_url('admin/overview') ?>">⚙️ Administrator Portal</a>
    </nav>
  </aside>
  <div class="drawer-backdrop" id="drawerBackdrop"></div>

  <!-- Decorative Abstract Background Elements -->
  <img class="welcome-deco waves" src="<?= base_url('assets/img/waves.svg') ?>" alt="" aria-hidden="true">
  <img class="welcome-deco shapes" src="<?= base_url('assets/img/shapes.svg') ?>" alt="" aria-hidden="true">

  <!-- Central Welcome Hero Section -->
  <main class="welcome-hero">
    <div class="welcome-hero-title">
      <img class="door-large" src="<?= base_url('assets/img/door-navy.svg') ?>" alt="RoomFinder Door" id="heroDoorIcon">
      <div class="text-group">
        <h1>Welcome !</h1>
        <h2>to CPC RoomFinder</h2>
      </div>
    </div>

    <p class="welcome-tagline">"Your Classroom, One Click Away"</p>

    <div class="welcome-actions">
      <a class="btn btn-navy" href="<?= base_url('login') ?>" id="btnHeroLogin">Log In</a>
      <a class="btn btn-navy" href="<?= base_url('register') ?>" id="btnHeroCreate">Create Account</a>
    </div>

    <!-- Quick Preview Switcher for Reviewing all Dashboards -->
    <div class="quick-preview-bar">
      <span>Quick Preview:</span>
      <a href="<?= base_url('student/dashboard') ?>" title="View Student Dashboard">Student</a>
      <a href="<?= base_url('instructor/bookings') ?>" title="View Instructor Dashboard">Instructor</a>
      <a href="<?= base_url('admin/overview') ?>" title="View Admin Overview">Admin</a>
    </div>
  </main>

  <script src="<?= base_url('assets/js/main.js') ?>"></script>
</body>
</html>