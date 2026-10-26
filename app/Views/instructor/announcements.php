<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Announcements - CPC RoomFinder | Instructor Portal</title>
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
  <link rel="stylesheet" href="<?= base_url('assets/css/style.css') ?>">
</head>
<body class="portal-layout">

  <!-- Instructor Sidebar Component -->
  <?= view('partials/instructor_sidebar', ['activeNav' => 'announcements']) ?>

  <!-- Main View Content -->
  <main class="portal-main">
    <div class="portal-topbar">
      <div class="portal-title-group">
        <h1>Campus Announcements</h1>
        <p class="subtitle">Important updates regarding campus facilities and classrooms</p>
      </div>
      <a href="<?= base_url('instructor/bookings') ?>" class="btn btn-navy btn-sm">← Back</a>
    </div>

    <!-- Pinned Announcement Banner -->
    <div class="pinned-banner">
      <div class="banner-tag">📌 Pinned announcement</div>
      <p style="font-size: 1rem; font-weight: 600; color: #ffffff;">
        Room DABBA closed for repairs Sept 8 – 15
      </p>
      <p style="font-size: 0.9rem; color: #cbd5e1; margin-top: 4px;">
        Undergoing repairs (lighting, AC, or furniture). Please avoid scheduling classes in DABBA during this window.
      </p>
    </div>

    <div style="display: flex; flex-direction: column; gap: 16px;">
      <div class="stat-card">
        <div style="font-weight: 700; font-size: 1.1rem; color: #ffffff;">Midterm Examination Room Assignments</div>
        <p style="font-size: 0.9rem; color: #cbd5e1; margin: 6px 0 10px;">
          CET faculty are reminded that Computer Labs A and D are reserved exclusively for hands-on programming exams from Oct 28 to Nov 3.
        </p>
        <small style="color: #94a3b8;">Posted by Administrator &bull; 2 days ago</small>
      </div>

      <div class="stat-card">
        <div style="font-weight: 700; font-size: 1.1rem; color: #ffffff;">Air Conditioning Maintenance in Psychology Wing</div>
        <p style="font-size: 0.9rem; color: #cbd5e1; margin: 6px 0 10px;">
          Scheduled maintenance for Room 101 and 102 cooling units this Saturday from 8:00 AM to 12:00 PM.
        </p>
        <small style="color: #94a3b8;">Posted by Facilities Dept &bull; 4 days ago</small>
      </div>
    </div>
  </main>

  <script src="<?= base_url('assets/js/main.js') ?>"></script>
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-YvpcrYf0tY3lHB60NNkmXc4s9bIOgUxi8T/jzmgXzuez/pHXEzlN77IfH8MRQ=" crossorigin="anonymous"></script>
</body>
</html>
