<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title><?= esc($title ?? 'Student Dashboard - CPC RoomFinder | Colegio De la Purisima Concepcion') ?></title>
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
  <link rel="stylesheet" href="<?= base_url('assets/css/style.css') ?>">
  <style>
    .student-layout {
      display: flex;
      min-height: 100vh;
      background: linear-gradient(135deg, #1b2668 0%, #101646 50%, #090c29 100%);
    }
    .student-sidebar {
      width: 280px;
      background: #11184c;
      padding: 32px 20px;
      display: flex;
      flex-direction: column;
      border-right: 1px solid rgba(255, 255, 255, 0.08);
      flex-shrink: 0;
    }
    .student-main {
      flex: 1;
      display: flex;
      flex-direction: column;
      padding: 40px 60px;
      position: relative;
    }
    .student-header {
      display: flex;
      justify-content: flex-end;
      align-items: center;
      margin-bottom: 40px;
    }
    .student-welcome-box {
      margin-bottom: 30px;
    }
    .student-welcome-box h1 {
      font-size: 2.8rem;
      font-weight: 700;
      color: #ffffff;
      margin-bottom: 6px;
    }
    .student-welcome-box p {
      font-size: 1.15rem;
      font-style: italic;
      color: #cbd5e1;
    }
    .student-action-tiles {
      display: flex;
      gap: 36px;
      align-items: center;
      justify-content: center;
      margin-top: 30px;
      flex-wrap: wrap;
    }
    .action-tile-btn {
      width: 170px;
      height: 170px;
      border-radius: 24px;
      background: #1c2763;
      border: 1px solid rgba(255, 255, 255, 0.12);
      display: flex;
      flex-direction: column;
      align-items: center;
      justify-content: center;
      gap: 16px;
      color: #ffffff;
      box-shadow: 0 16px 36px rgba(0, 0, 0, 0.35);
      transition: transform 0.25s ease, background 0.25s ease, box-shadow 0.25s ease;
      text-decoration: none;
    }
    .action-tile-btn:hover {
      transform: translateY(-8px);
      background: #253380;
      box-shadow: 0 20px 44px rgba(0, 0, 0, 0.45);
      color: #ffffff;
    }
    .action-tile-btn svg {
      width: 52px;
      height: 52px;
      color: #ffffff;
    }
    .action-tile-btn span {
      font-size: 1rem;
      font-weight: 700;
      letter-spacing: 0.3px;
    }
    @media (max-width: 768px) {
      .student-layout {
        flex-direction: column;
      }
      .student-sidebar {
        width: 100%;
        border-right: none;
        border-bottom: 1px solid rgba(255,255,255,0.08);
      }
      .student-main {
        padding: 24px 20px;
      }
    }
  </style>
</head>
<body>

  <?php
  $userName = session()->get('name') ?? 'John Doe';
  $firstName = session()->get('first_name') ?? 'Student';
  $firstLetter = substr((string)$firstName, 0, 1);
  $lastLetter = substr((string)(session()->get('last_name') ?? 'D'), 0, 1);
  $avatar = strtoupper($firstLetter . $lastLetter);
  ?>

  <div class="student-layout">
    <!-- Student Sidebar -->
    <aside class="student-sidebar">
      <div class="sidebar-profile" style="border-bottom: 1px solid rgba(255,255,255,0.1); padding-bottom: 20px; margin-bottom: 28px;">
        <div class="profile-avatar"><?= esc($avatar) ?></div>
        <div class="profile-info">
          <div class="profile-name"><?= esc($userName) ?></div>
          <div class="profile-role">Student</div>
        </div>
        <a href="<?= base_url('profile') ?>" class="profile-edit-btn" title="Edit Profile" aria-label="Edit Profile" style="display:inline-flex; align-items:center; justify-content:center; text-decoration:none;">
          <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
            <path d="M12 20h9"></path>
            <path d="M16.5 3.5a2.121 2.121 0 0 1 3 3L7 19l-4 1 1-4L16.5 3.5z"></path>
          </svg>
        </a>
      </div>

      <nav class="sidebar-nav">
        <a href="<?= base_url('student/rooms') ?>" class="nav-item">
          <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
            <circle cx="11" cy="11" r="8"></circle>
            <line x1="21" y1="21" x2="16.65" y2="16.65"></line>
          </svg>
          <span>Find rooms</span>
        </a>

        <a href="<?= base_url('student/announcements') ?>" class="nav-item">
          <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
            <path d="M18 8A6 6 0 0 0 6 8c0 7-3 9-3 9h18s-3-2-3-9"></path>
            <path d="M13.73 21a2 2 0 0 1-3.46 0"></path>
          </svg>
          <span>Announcements</span>
        </a>

        <a href="#systemInfoModal" data-modal-open="sysInfoModal" class="nav-item">
          <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
            <circle cx="12" cy="12" r="10"></circle>
            <line x1="12" y1="16" x2="12" y2="12"></line>
            <line x1="12" y1="8" x2="12.01" y2="8"></line>
          </svg>
          <span>System information</span>
        </a>
      </nav>
    </aside>

    <!-- Student Main Screen -->
    <main class="student-main">
      <header class="student-header">
        <a class="brand" href="<?= base_url() ?>">
          <img class="brand-icon" src="<?= base_url('assets/img/door-orange.svg') ?>" alt="Logo">
          <span>CPC RoomFinder</span>
        </a>
      </header>

      <div class="student-welcome-box">
        <h1>Welcome, <?= esc($firstName) ?>!</h1>
        <p>Let's find an open classroom for you today.</p>
      </div>

      <?php if (!empty($pinnedAnnouncement)): ?>
        <div class="pinned-banner" style="margin-bottom: 24px;">
          <div class="banner-tag">📌 Campus Notice</div>
          <p style="font-size: 1rem; font-weight: 700; color: #ffffff; margin-bottom: 4px;">
            <?= esc($pinnedAnnouncement['title']) ?>
          </p>
          <p style="font-size: 0.88rem; color: #cbd5e1; margin: 0;">
            <?= esc(mb_strimwidth($pinnedAnnouncement['message'], 0, 140, '...')) ?>
          </p>
        </div>
      <?php endif; ?>

      <!-- Action Tiles matching screenshot -->
      <div class="student-action-tiles">
        <!-- Log Out Tile -->
        <a href="<?= base_url('logout') ?>" class="action-tile-btn" id="btnStudentLogoutTile">
          <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
            <path d="M9 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h4"></path>
            <polyline points="16 17 21 12 16 7"></polyline>
            <line x1="21" y1="12" x2="9" y2="12"></line>
          </svg>
          <span>Log out</span>
        </a>

        <!-- Dashboard / Rooms Tile -->
        <a href="<?= base_url('student/rooms') ?>" class="action-tile-btn" id="btnStudentDashboardTile">
          <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
            <rect x="3" y="3" width="7" height="7" rx="1.5"></rect>
            <rect x="14" y="3" width="7" height="7" rx="1.5"></rect>
            <rect x="14" y="14" width="7" height="7" rx="1.5"></rect>
            <rect x="3" y="14" width="7" height="7" rx="1.5"></rect>
          </svg>
          <span>Find Rooms</span>
        </a>
      </div>
    </main>
  </div>

  <!-- System Information Modal -->
  <div class="cpc-modal-backdrop" id="sysInfoModal" role="dialog" aria-modal="true">
    <div class="cpc-modal-dialog">
      <button type="button" class="modal-close-btn" data-modal-close>&times;</button>
      <h2 class="modal-title">System Information</h2>
      <div style="font-size: 0.95rem; color: #cbd5e1; line-height: 1.6;">
        <p><strong>System:</strong> CPC RoomFinder Portal v1.0</p>
        <p><strong>Institution:</strong> Colegio De la Purisima Concepcion</p>
        <p><strong>Department Support:</strong> CET, CASE, CBA, Marine &amp; Psychology</p>
        <p style="margin-top: 14px;">Real-time room occupancy is updated when instructors and faculty reserve rooms. Vacant classrooms are open for study and review sessions.</p>
      </div>
      <div style="text-align: center; margin-top: 24px;">
        <button type="button" class="btn btn-navy" data-modal-close>Close</button>
      </div>
    </div>
  </div>

  <script src="<?= base_url('assets/js/main.js') ?>"></script>
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-YvpcrYf0tY3lHB60NNkmXc4s9bIOgUxi8T/jzmgXzuez/pHXEzlN77IfH8MRQ=" crossorigin="anonymous"></script>
</body>
</html>
