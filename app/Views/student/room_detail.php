<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title><?= esc($title ?? 'Classroom Schedule - CPC RoomFinder') ?></title>
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
  <link rel="stylesheet" href="<?= base_url('assets/css/style.css') ?>">
  <style>
    .room-detail-container {
      max-width: 1100px;
      width: 100%;
    }
    .room-hero-card {
      background: var(--cpc-navy-card);
      border: 1px solid rgba(255, 255, 255, 0.12);
      border-radius: var(--radius-xl);
      padding: 28px 32px;
      display: flex;
      justify-content: space-between;
      align-items: center;
      margin-bottom: 28px;
      flex-wrap: wrap;
      gap: 20px;
    }
    .room-spec-chips {
      display: flex;
      gap: 12px;
      flex-wrap: wrap;
      margin-top: 14px;
    }
    .room-spec-chip {
      background: rgba(255, 255, 255, 0.06);
      border: 1px solid rgba(255, 255, 255, 0.1);
      padding: 6px 14px;
      border-radius: var(--radius-pill);
      font-size: 0.85rem;
      color: #cbd5e1;
      display: inline-flex;
      align-items: center;
      gap: 6px;
    }
    .schedule-grid-card {
      background: var(--cpc-navy-card);
      border: 1px solid rgba(255, 255, 255, 0.12);
      border-radius: var(--radius-xl);
      padding: 28px 32px;
    }
    .schedule-table-wrap {
      overflow-x: auto;
      margin-top: 20px;
    }
    .schedule-table {
      width: 100%;
      border-collapse: separate;
      border-spacing: 6px;
      min-width: 680px;
    }
    .schedule-table th {
      background: #0f1542;
      color: #cbd5e1;
      font-size: 0.85rem;
      font-weight: 700;
      text-transform: uppercase;
      padding: 12px 14px;
      text-align: center;
      border-radius: 6px;
    }
    .schedule-table td {
      padding: 12px 14px;
      border-radius: 8px;
      font-size: 0.85rem;
      text-align: center;
      vertical-align: middle;
    }
    .slot-time {
      background: #0f1542;
      color: #94a3b8;
      font-weight: 600;
      white-space: nowrap;
    }
    .slot-occupied {
      background: rgba(239, 68, 68, 0.18);
      border: 1px solid rgba(239, 68, 68, 0.35);
      color: #fca5a5;
      font-weight: 600;
    }
    .slot-occupied small {
      display: block;
      font-size: 0.75rem;
      color: #cbd5e1;
      font-weight: 400;
      margin-top: 2px;
    }
    .slot-vacant {
      background: rgba(34, 197, 94, 0.12);
      border: 1px solid rgba(34, 197, 94, 0.25);
      color: #86efac;
      font-weight: 500;
    }
  </style>
</head>
<body class="portal-layout">

  <!-- Student Sidebar -->
  <?= view('partials/student_sidebar', ['activeNav' => 'rooms']) ?>

  <!-- Main View Content -->
  <main class="portal-main">
    <div class="portal-topbar">
      <div class="portal-title-group">
        <h1><?= esc($room['room_name']) ?> Schedule</h1>
        <p class="subtitle">Classroom timetable, capacity, and real-time availability</p>
      </div>

      <div style="display: flex; gap: 12px; align-items: center;">
        <a href="<?= base_url('student/rooms') ?>" class="btn btn-navy btn-sm">
          ← Back to Rooms Directory
        </a>
      </div>
    </div>

    <div class="room-detail-container">

      <!-- Hero Summary Card -->
      <div class="room-hero-card">
        <div>
          <div style="display: flex; align-items: center; gap: 14px; margin-bottom: 8px;">
            <h2 style="font-size: 1.8rem; font-weight: 800; color: #ffffff; margin: 0;"><?= esc($room['room_name']) ?></h2>
            <?php
            $statClass = match($room['status'] ?? 'Available') {
                'Available'   => 'dot-approved',
                'Occupied'    => 'dot-cancelled',
                'Maintenance' => 'dot-maintenance',
                default       => 'dot-pending',
            };
            ?>
            <span class="status-pill">
              <span class="status-dot <?= $statClass ?>"></span>
              <span style="color: #cbd5e1; font-weight: 600;"><?= esc($room['status'] ?? 'Available') ?></span>
            </span>
          </div>

          <p style="color: #94a3b8; font-size: 0.95rem; margin: 0;">
            Department: <strong style="color: #cbd5e1;"><?= esc($room['department_name'] ?? ($room['department_code'] ?? 'College Academic')) ?></strong>
          </p>

          <div class="room-spec-chips">
            <span class="room-spec-chip">
              <svg width="16" height="16" viewBox="0 0 24 24" fill="currentColor">
                <path d="M16 11c1.66 0 2.99-1.34 2.99-3S17.66 5 16 5c-1.66 0-3 1.34-3 3s1.34 3 3 3zm-8 0c1.66 0 2.99-1.34 2.99-3S9.66 5 8 5C6.34 5 5 6.34 5 8s1.34 3 3 3zm0 2c-2.33 0-7 1.17-7 3.5V19h14v-2.5c0-2.33-4.67-3.5-7-3.5zm8 0c-.29 0-.62.02-.97.05 1.16.84 1.97 1.97 1.97 3.45V19h6v-2.5c0-2.33-4.67-3.5-7-3.5z"></path>
              </svg>
              <strong><?= esc($room['capacity'] ?? 30) ?></strong> Seats Capacity
            </span>

            <?php if (!empty($occupancy['badge_text'])): ?>
              <span class="room-spec-chip" style="background: rgba(245, 130, 32, 0.15); border-color: rgba(245, 130, 32, 0.4); color: var(--cpc-orange); font-weight: 600;">
                ⏳ <?= esc($occupancy['badge_text']) ?>
              </span>
            <?php endif; ?>
          </div>
        </div>

        <div style="text-align: right;">
          <div style="font-size: 0.85rem; color: #94a3b8; margin-bottom: 6px;">Campus Location:</div>
          <div style="font-size: 1.1rem; font-weight: 700; color: var(--cpc-orange);"><?= esc($room['department_code'] ?? 'Main Campus') ?> Building</div>
        </div>
      </div>

      <!-- Student Read-Only Informational Alert -->
      <div class="pinned-banner" style="margin-bottom: 24px; border-color: rgba(56, 189, 248, 0.4); background: rgba(56, 189, 248, 0.08);">
        <div class="banner-tag" style="background: var(--cpc-cyan); color: #080c29;">STUDENT TIMETABLE VIEW</div>
        <p style="color: #e0f2fe; font-size: 0.95rem; margin-top: 4px;">
          This schedule is read-only. Students may check vacant periods for study groups or review classes. To formally reserve this room, please notify your subject instructor.
        </p>
      </div>

      <!-- Today's Approved Bookings -->
      <div class="schedule-grid-card" style="margin-bottom: 24px;">
        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 16px; flex-wrap: wrap; gap: 12px;">
          <h3 style="font-size: 1.25rem; font-weight: 700; color: #ffffff; margin: 0;">Today's Schedule (<?= date('F j, Y') ?>)</h3>
          <span style="font-size: 0.82rem; color: #94a3b8;">Chronological Bookings</span>
        </div>

        <?php if (!empty($schedule)): ?>
          <div style="display: flex; flex-direction: column; gap: 10px;">
            <?php foreach ($schedule as $slot): ?>
              <div style="background: #0f1542; border: 1px solid rgba(255,255,255,0.08); border-radius: 8px; padding: 12px 18px; display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 10px;">
                <div style="display: flex; align-items: center; gap: 12px;">
                  <span style="background: rgba(245, 130, 32, 0.15); color: var(--cpc-orange); font-weight: 700; padding: 4px 10px; border-radius: 6px; font-size: 0.85rem;">
                    <?= date('g:i A', strtotime($slot['start_time'])) ?> – <?= date('g:i A', strtotime($slot['end_time'])) ?>
                  </span>
                  <span style="color: #ffffff; font-weight: 600; font-size: 0.95rem;">
                    <?= esc($slot['purpose']) ?>
                  </span>
                </div>
                <div style="color: #94a3b8; font-size: 0.85rem;">
                  Instructor: <strong style="color: #cbd5e1;"><?= esc(trim(($slot['first_name'] ?? '') . ' ' . ($slot['last_name'] ?? ''))) ?></strong>
                </div>
              </div>
            <?php endforeach; ?>
          </div>
        <?php else: ?>
          <div style="background: rgba(34, 197, 94, 0.08); border: 1px solid rgba(34, 197, 94, 0.2); border-radius: 8px; padding: 16px 20px; color: #86efac; font-size: 0.92rem;">
            No reserved classes scheduled for today. This classroom is open for student study use during standard operating hours.
          </div>
        <?php endif; ?>
      </div>

    </div>
  </main>

  <script src="<?= base_url('assets/js/main.js') ?>"></script>
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-YvpcrYf0tY3lHB60NNkmXc4s9bIOgUxi8T/jzmgXzuez/pHXEzlN77IfH8MRQ=" crossorigin="anonymous"></script>
</body>
</html>
