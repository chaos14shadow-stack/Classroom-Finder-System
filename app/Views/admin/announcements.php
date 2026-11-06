<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Announcements - CPC RoomFinder | Admin Portal</title>
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
  <link rel="stylesheet" href="<?= base_url('assets/css/style.css') ?>">
</head>
<body class="portal-layout">

  <!-- Admin Sidebar Component -->
  <?= view('partials/admin_sidebar', ['activeNav' => 'announcements']) ?>

  <!-- Main View Content -->
  <main class="portal-main">
    <div class="portal-topbar">
      <div class="portal-title-group">
        <h1>Announcements</h1>
        <p class="subtitle">Broadcast campus-wide room schedule notices and updates</p>
      </div>

      <div style="display: flex; gap: 16px; align-items: center;">
        <button 
          type="button" 
          class="btn btn-orange" 
          id="btnOpenAddAnnouncement" 
          onclick="openAddAnnouncement()"
          style="padding: 9px 24px; font-weight: 700;"
        >
          + ADD
        </button>

        <a href="<?= base_url('admin/overview') ?>" title="Home" style="color: var(--cpc-orange); font-size: 1.5rem;">
          <svg width="28" height="28" viewBox="0 0 24 24" fill="currentColor">
            <path d="M10 20v-6h4v6h5v-8h3L12 3 2 12h3v8z"></path>
          </svg>
        </a>
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

    <?php if (session()->getFlashdata('errors')): ?>
      <div class="alert alert-danger" style="background: rgba(239, 68, 68, 0.15); border: 1px solid rgba(239, 68, 68, 0.4); color: #fca5a5; font-size: 0.9rem; padding: 12px 18px; border-radius: 10px; margin-bottom: 20px;">
        <ul style="margin: 0; padding-left: 20px;">
          <?php foreach (session()->getFlashdata('errors') as $err): ?>
            <li><?= esc($err) ?></li>
          <?php endforeach; ?>
        </ul>
      </div>
    <?php endif; ?>

    <!-- Announcements Cards List -->
    <div style="display: flex; flex-direction: column; gap: 14px; width: 100%;" id="announcementsList">
      <?php if (!empty($announcements)): ?>
        <?php foreach ($announcements as $ann): ?>
          <?php
            $isPinned = (int)$ann['is_pinned'] === 1;
            $cardStyle = 'padding: 18px 24px; display: flex; flex-direction: row; justify-content: space-between; align-items: center;';
            if ($isPinned) {
                $cardStyle .= ' border: 1.5px solid var(--cpc-cyan); background: rgba(21, 28, 77, 0.9);';
            }
          ?>
          <div class="stat-card" style="<?= $cardStyle ?>" id="ann-card-<?= (int)$ann['id'] ?>">
            <div style="flex: 1; margin-right: 18px;">
              <h3 style="font-size: 1.05rem; font-weight: 700; color: #ffffff;" class="ann-title">
                <?= esc($ann['title']) ?>
              </h3>
              <p style="font-size: 0.85rem; color: #94a3b8; font-style: italic; margin-top: 4px; white-space: pre-wrap;" class="ann-body">
                <?= esc($ann['message']) ?>
              </p>
              <div style="font-size: 0.75rem; color: #64748b; margin-top: 6px;">
                Posted <?= esc(date('M j, Y', strtotime($ann['created_at']))) ?>
                <?php if (!empty($ann['first_name'])): ?>
                  by <?= esc($ann['first_name'] . ' ' . $ann['last_name']) ?>
                <?php endif; ?>
              </div>
            </div>

            <div style="display: flex; gap: 14px; align-items: center;">
              <!-- Quick Pin Toggle Form -->
              <form method="POST" action="<?= base_url('admin/announcements/update/' . (int)$ann['id']) ?>" style="display: inline;">
                <?= csrf_field() ?>
                <input type="hidden" name="title" value="<?= esc($ann['title']) ?>">
                <input type="hidden" name="message" value="<?= esc($ann['message']) ?>">
                <?php if (!$isPinned): ?>
                  <input type="hidden" name="is_pinned" value="1">
                  <button 
                    type="submit" 
                    class="profile-edit-btn" 
                    title="Pin Announcement to Top"
                    style="color: #64748b;"
                  >
                    <svg width="20" height="20" viewBox="0 0 24 24" fill="currentColor">
                      <path d="M16 12V4h1V2H7v2h1v8l-2 2v2h5.2v6h1.6v-6H18v-2l-2-2z"></path>
                    </svg>
                  </button>
                <?php else: ?>
                  <!-- If currently pinned, submitting without is_pinned=1 unpins it -->
                  <button 
                    type="submit" 
                    class="profile-edit-btn" 
                    title="Unpin Announcement"
                    style="color: var(--cpc-cyan);"
                  >
                    <svg width="20" height="20" viewBox="0 0 24 24" fill="currentColor">
                      <path d="M16 12V4h1V2H7v2h1v8l-2 2v2h5.2v6h1.6v-6H18v-2l-2-2z"></path>
                    </svg>
                  </button>
                <?php endif; ?>
              </form>

              <!-- Edit button -->
              <button 
                type="button" 
                class="profile-edit-btn" 
                title="Edit Announcement"
                onclick='openEditAnnouncement(<?= (int)$ann['id'] ?>, <?= json_encode($ann['title']) ?>, <?= json_encode($ann['message']) ?>, <?= $isPinned ? 'true' : 'false' ?>)'
              >
                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                  <path d="M12 20h9"></path>
                  <path d="M16.5 3.5a2.121 2.121 0 0 1 3 3L7 19l-4 1 1-4L16.5 3.5z"></path>
                </svg>
              </button>

              <!-- Delete button -->
              <form method="POST" action="<?= base_url('admin/announcements/delete/' . (int)$ann['id']) ?>" style="display: inline;" onsubmit="return confirm('Are you sure you want to delete this announcement?');">
                <?= csrf_field() ?>
                <button 
                  type="submit" 
                  class="profile-edit-btn" 
                  style="color: #ef4444;" 
                  title="Delete Announcement"
                >
                  <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <polyline points="3 6 5 6 21 6"></polyline>
                    <path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2"></path>
                  </svg>
                </button>
              </form>
            </div>
          </div>
        <?php endforeach; ?>
      <?php else: ?>
        <div class="stat-card" style="padding: 32px; text-align: center; color: #94a3b8;">
          No announcements published yet. Click <strong>+ ADD</strong> to broadcast an announcement.
        </div>
      <?php endif; ?>
    </div>
  </main>

  <!-- ADD / EDIT ANNOUNCEMENT MODAL -->
  <div class="cpc-modal-backdrop" id="addAnnouncementModal" role="dialog" aria-modal="true">
    <div class="cpc-modal-dialog">
      <button type="button" class="modal-close-btn" data-modal-close>&times;</button>
      <h2 class="modal-title" id="annModalTitle" style="text-align: left; text-transform: none; font-size: 1.45rem;">Add Announcement</h2>
      <p style="font-size: 0.82rem; color: #94a3b8; margin: -14px 0 20px;">This will be visible to all instructors and students.</p>

      <form id="announcementForm" method="POST" action="<?= base_url('admin/announcements') ?>">
        <?= csrf_field() ?>

        <div class="form-group" style="margin-bottom: 18px;">
          <label style="font-size: 0.88rem; color: #cbd5e1;">Announcement Title:</label>
          <input 
            type="text" 
            name="title"
            id="annTitleInput"
            class="form-input" 
            placeholder="(Title / Subject)"
            required
            style="border-radius: var(--radius-md); background: #0f1542; color: #fff; border: 1px solid rgba(255,255,255,0.15);"
          >
        </div>

        <div class="form-group" style="margin-bottom: 20px;">
          <label style="font-size: 0.88rem; color: #cbd5e1;">Message:</label>
          <textarea 
            name="message"
            id="annMessageInput"
            class="form-input" 
            rows="5"
            placeholder="Type message here.."
            required
            style="border-radius: var(--radius-md); background: #0f1542; color: #fff; border: 1px solid rgba(255,255,255,0.15); resize: vertical;"
          ></textarea>
        </div>

        <div style="display: flex; align-items: center; justify-content: space-between; margin-bottom: 26px;">
          <label style="display: flex; align-items: center; gap: 8px; cursor: pointer; font-size: 0.9rem; color: #cbd5e1;">
            <input type="checkbox" name="is_pinned" id="annPinCheckbox" value="1" style="accent-color: #22c55e; width: 18px; height: 18px;">
            <span>Pin this announcement to top</span>
          </label>
        </div>

        <div style="display: flex; justify-content: flex-end; gap: 10px;">
          <button type="button" class="btn btn-navy" data-modal-close>Cancel</button>
          <button type="submit" class="btn btn-orange" style="padding: 10px 36px;">Save</button>
        </div>
      </form>
    </div>
  </div>

  <script src="<?= base_url('assets/js/main.js') ?>"></script>
  <script>
    function openAddAnnouncement() {
      document.getElementById('annModalTitle').textContent = 'Add Announcement';
      document.getElementById('annTitleInput').value = '';
      document.getElementById('annMessageInput').value = '';
      document.getElementById('annPinCheckbox').checked = false;
      document.getElementById('announcementForm').action = '<?= base_url('admin/announcements') ?>';
      openModal(document.getElementById('addAnnouncementModal'));
    }

    function openEditAnnouncement(id, title, message, isPinned) {
      document.getElementById('annModalTitle').textContent = 'Edit Announcement';
      document.getElementById('annTitleInput').value = title;
      document.getElementById('annMessageInput').value = message;
      document.getElementById('annPinCheckbox').checked = isPinned;
      document.getElementById('announcementForm').action = '<?= base_url('admin/announcements/update/') ?>' + id;
      openModal(document.getElementById('addAnnouncementModal'));
    }
  </script>
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-YvpcrYf0tY3lHB60NNkmXc4s9bIOgUxi8T/jzmgXzuez/pHXEzlN77IfH8MRQ=" crossorigin="anonymous"></script>
</body>
</html>
