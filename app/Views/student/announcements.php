<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Announcements - CPC RoomFinder | Student Portal</title>
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
  <link rel="stylesheet" href="<?= base_url('assets/css/style.css') ?>">
</head>
<body class="portal-layout">

  <!-- Student Sidebar -->
  <aside class="cpc-sidebar">
    <a class="sidebar-brand" href="<?= base_url('student/dashboard') ?>">
      <img src="<?= base_url('assets/img/door-orange.svg') ?>" style="width: 24px; height: 24px;" alt="Logo">
      <span>CPC RoomFinder</span>
    </a>

    <div class="sidebar-profile">
      <div class="profile-avatar">JD</div>
      <div class="profile-info">
        <div class="profile-name">John Doe</div>
        <div class="profile-role">Student</div>
      </div>
      <button type="button" class="profile-edit-btn" title="Edit Profile" aria-label="Edit Profile">
        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
          <path d="M12 20h9"></path>
          <path d="M16.5 3.5a2.121 2.121 0 0 1 3 3L7 19l-4 1 1-4L16.5 3.5z"></path>
        </svg>
      </button>
    </div>

    <nav class="sidebar-nav">
      <a href="<?= base_url('student/dashboard') ?>" class="nav-item">
        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
          <path d="M3 9l9-7 9 7v11a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2z"></path>
          <polyline points="9 22 9 12 15 12 15 22"></polyline>
        </svg>
        <span>Home</span>
      </a>

      <a href="<?= base_url('student/rooms') ?>" class="nav-item">
        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
          <circle cx="11" cy="11" r="8"></circle>
          <line x1="21" y1="21" x2="16.65" y2="16.65"></line>
        </svg>
        <span>Find Rooms</span>
      </a>

      <a href="<?= base_url('student/announcements') ?>" class="nav-item active">
        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
          <path d="M18 8A6 6 0 0 0 6 8c0 7-3 9-3 9h18s-3-2-3-9"></path>
          <path d="M13.73 21a2 2 0 0 1-3.46 0"></path>
        </svg>
        <span>Announcements</span>
      </a>
    </nav>

    <div class="sidebar-footer">
      <a href="<?= base_url('login') ?>" class="logout-link">
        <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
          <path d="M9 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h4"></path>
          <polyline points="16 17 21 12 16 7"></polyline>
          <line x1="21" y1="12" x2="9" y2="12"></line>
        </svg>
        <span>Log Out</span>
      </a>
    </div>
  </aside>

  <!-- Main View Content -->
  <main class="portal-main">
    <div class="portal-topbar">
      <div class="portal-title-group">
        <h1>Campus Announcements</h1>
        <p class="subtitle">Latest updates from CPC administration and faculty</p>
      </div>
      <a href="<?= base_url('student/dashboard') ?>" class="btn btn-navy btn-sm">← Back to Overview</a>
    </div>

    <!-- Pinned Announcement Banner -->
    <div class="pinned-banner">
      <div class="banner-tag">📌 Pinned announcement</div>
      <p style="font-size: 1rem; font-weight: 600; color: #ffffff;">
        Room DABBA closed for repairs Sept 8 – 15
      </p>
      <p style="font-size: 0.9rem; color: #cbd5e1; margin-top: 4px;">
        Undergoing repairs (lighting, AC, or furniture). Please check with your course instructors for alternative rooms.
      </p>
    </div>

    <div style="display: flex; flex-direction: column; gap: 16px;">
      <div class="stat-card">
        <div style="font-weight: 700; font-size: 1.1rem; color: #ffffff;">CET Student General Assembly</div>
        <p style="font-size: 0.9rem; color: #cbd5e1; margin: 6px 0 10px;">
          All Engineering and IT students are requested to attend the General Assembly this Friday at the University Auditorium.
        </p>
        <small style="color: #94a3b8;">Posted by CET Dean's Office &bull; 1 day ago</small>
      </div>
    </div>
  </main>

  <script src="<?= base_url('assets/js/main.js') ?>"></script>
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-YvpcrYf0tY3lHB60NNkmXc4s9bIOgUxi8T/jzmgXzuez/pHXEzlN77IfH8MRQ=" crossorigin="anonymous"></script>
</body>
</html>
