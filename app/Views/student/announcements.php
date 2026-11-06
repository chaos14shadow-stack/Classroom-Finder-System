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
  <?= view('partials/student_sidebar', ['activeNav' => 'announcements']) ?>

  <!-- Main View Content -->
  <main class="portal-main">
    <div class="portal-topbar">
      <div class="portal-title-group">
        <h1>Campus Announcements</h1>
        <p class="subtitle">Latest updates from CPC administration and faculty</p>
      </div>
      <a href="<?= base_url('student/dashboard') ?>" class="btn btn-navy btn-sm">← Back to Dashboard</a>
    </div>

    <!-- Pinned Announcement Banner -->
    <?php if (!empty($pinnedAnnouncement)): ?>
      <div class="pinned-banner">
        <div class="banner-tag">📌 Pinned announcement</div>
        <p style="font-size: 1.05rem; font-weight: 700; color: #ffffff; margin-bottom: 6px;">
          <?= esc($pinnedAnnouncement['title']) ?>
        </p>
        <p style="font-size: 0.92rem; color: #cbd5e1; margin-top: 4px; line-height: 1.5;">
          <?= nl2br(esc($pinnedAnnouncement['message'])) ?>
        </p>
        <small style="color: #94a3b8; font-size: 0.8rem; display: block; margin-top: 8px;">
          Published by <?= esc(trim(($pinnedAnnouncement['first_name'] ?? 'Admin') . ' ' . ($pinnedAnnouncement['last_name'] ?? ''))) ?> &bull; <?= date('M d, Y', strtotime($pinnedAnnouncement['created_at'])) ?>
        </small>
      </div>
    <?php endif; ?>

    <div style="display: flex; flex-direction: column; gap: 16px;">
      <?php if (!empty($announcements)): ?>
        <?php foreach ($announcements as $ann): ?>
          <?php if (!empty($pinnedAnnouncement) && (int)$ann['id'] === (int)$pinnedAnnouncement['id']) continue; ?>
          <div class="stat-card" style="display: block; padding: 22px 28px;">
            <div style="font-weight: 700; font-size: 1.1rem; color: #ffffff;"><?= esc($ann['title']) ?></div>
            <p style="font-size: 0.92rem; color: #cbd5e1; margin: 8px 0 12px; line-height: 1.5;">
              <?= nl2br(esc($ann['message'])) ?>
            </p>
            <small style="color: #94a3b8; font-size: 0.8rem;">
              Posted by <?= esc(trim(($ann['first_name'] ?? 'Administration') . ' ' . ($ann['last_name'] ?? ''))) ?> &bull; <?= date('M d, Y', strtotime($ann['created_at'])) ?>
            </small>
          </div>
        <?php endforeach; ?>
      <?php else: ?>
        <div class="stat-card" style="text-align: center; color: #94a3b8; padding: 32px;">
          No announcements published at this time.
        </div>
      <?php endif; ?>
    </div>

  </main>

  <script src="<?= base_url('assets/js/main.js') ?>"></script>
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-YvpcrYf0tY3lHB60NNkmXc4s9bIOgUxi8T/jzmgXzuez/pHXEzlN77IfH8MRQ=" crossorigin="anonymous"></script>
</body>
</html>
