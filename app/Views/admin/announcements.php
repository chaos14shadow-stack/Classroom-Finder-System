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
      </div>

      <div style="display: flex; gap: 16px; align-items: center;">
        <button 
          type="button" 
          class="btn btn-orange" 
          id="btnOpenAddAnnouncement" 
          data-modal-open="addAnnouncementModal"
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

    <!-- Announcements Cards List matching Image 4 bottom -->
    <div style="display: flex; flex-direction: column; gap: 14px; max-width: 900px;" id="announcementsList">
      
      <!-- Card 1: Pinned -->
      <div 
        class="stat-card" 
        style="padding: 18px 24px; display: flex; flex-direction: row; justify-content: space-between; align-items: center; border: 1.5px solid var(--cpc-cyan); background: rgba(21, 28, 77, 0.9);"
        id="ann-card-1"
      >
        <div>
          <h3 style="font-size: 1.05rem; font-weight: 700; color: #ffffff;" class="ann-title">Room DABBA closed for repairs Sept 8-15</h3>
          <p style="font-size: 0.85rem; color: #94a3b8; font-style: italic; margin-top: 4px;" class="ann-body">
            Undergoing repairs (lighting, AC, or furniture).
          </p>
        </div>

        <div style="display: flex; gap: 14px; align-items: center;">
          <!-- Pin button (pinned) -->
          <button 
            type="button" 
            class="profile-edit-btn" 
            title="Pinned Announcement"
            style="color: var(--cpc-cyan);"
            onclick="togglePin(this)"
          >
            <svg width="20" height="20" viewBox="0 0 24 24" fill="currentColor">
              <path d="M16 12V4h1V2H7v2h1v8l-2 2v2h5.2v6h1.6v-6H18v-2l-2-2z"></path>
            </svg>
          </button>

          <!-- Edit button -->
          <button 
            type="button" 
            class="profile-edit-btn" 
            title="Edit Announcement"
            onclick="openEditAnnouncement('Room DABBA closed for repairs Sept 8-15', 'Undergoing repairs (lighting, AC, or furniture).', true)"
          >
            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
              <path d="M12 20h9"></path>
              <path d="M16.5 3.5a2.121 2.121 0 0 1 3 3L7 19l-4 1 1-4L16.5 3.5z"></path>
            </svg>
          </button>

          <!-- Delete button -->
          <button 
            type="button" 
            class="profile-edit-btn" 
            style="color: #ef4444;" 
            title="Delete Announcement"
            onclick="deleteAnnouncement('ann-card-1')"
          >
            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
              <polyline points="3 6 5 6 21 6"></polyline>
              <path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2"></path>
            </svg>
          </button>
        </div>
      </div>

      <!-- Card 2 -->
      <div 
        class="stat-card" 
        style="padding: 18px 24px; display: flex; flex-direction: row; justify-content: space-between; align-items: center;"
        id="ann-card-2"
      >
        <div>
          <h3 style="font-size: 1.05rem; font-weight: 700; color: #ffffff;" class="ann-title">[Faculty Meeting &amp; Room Allocations]</h3>
          <p style="font-size: 0.85rem; color: #94a3b8; font-style: italic; margin-top: 4px;" class="ann-body">
            Message: All department heads are requested to finalize term schedules by Friday.
          </p>
        </div>

        <div style="display: flex; gap: 14px; align-items: center;">
          <button 
            type="button" 
            class="profile-edit-btn" 
            title="Pin Announcement"
            style="color: #64748b;"
            onclick="togglePin(this)"
          >
            <svg width="20" height="20" viewBox="0 0 24 24" fill="currentColor">
              <path d="M16 12V4h1V2H7v2h1v8l-2 2v2h5.2v6h1.6v-6H18v-2l-2-2z"></path>
            </svg>
          </button>

          <button 
            type="button" 
            class="profile-edit-btn" 
            title="Edit Announcement"
            onclick="openEditAnnouncement('[Faculty Meeting & Room Allocations]', 'All department heads are requested to finalize term schedules by Friday.', false)"
          >
            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
              <path d="M12 20h9"></path>
              <path d="M16.5 3.5a2.121 2.121 0 0 1 3 3L7 19l-4 1 1-4L16.5 3.5z"></path>
            </svg>
          </button>

          <button 
            type="button" 
            class="profile-edit-btn" 
            style="color: #ef4444;" 
            title="Delete Announcement"
            onclick="deleteAnnouncement('ann-card-2')"
          >
            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
              <polyline points="3 6 5 6 21 6"></polyline>
              <path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2"></path>
            </svg>
          </button>
        </div>
      </div>

    </div>
  </main>

  <!-- ADD / EDIT ANNOUNCEMENT MODAL (Image 1 top right) -->
  <div class="cpc-modal-backdrop" id="addAnnouncementModal" role="dialog" aria-modal="true">
    <div class="cpc-modal-dialog">
      <button type="button" class="modal-close-btn" data-modal-close>&times;</button>
      <h2 class="modal-title" id="annModalTitle" style="text-align: left; text-transform: none; font-size: 1.45rem;">Add Announcement</h2>
      <p style="font-size: 0.82rem; color: #94a3b8; margin: -14px 0 20px;">This will be visible to all instructors and students.</p>

      <form id="announcementForm" onsubmit="handleSaveAnnouncement(event)">
        <div class="form-group" style="margin-bottom: 18px;">
          <label style="font-size: 0.88rem; color: #cbd5e1;">Announcement Title:</label>
          <input 
            type="text" 
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
            <input type="checkbox" id="annPinCheckbox" style="accent-color: #22c55e; width: 18px; height: 18px;">
            <span>Pin this announcement</span>
          </label>
        </div>

        <div style="display: flex; justify-content: flex-end;">
          <button type="submit" class="btn btn-orange" style="padding: 10px 36px;">Save</button>
        </div>
      </form>
    </div>
  </div>

  <script src="<?= base_url('assets/js/main.js') ?>"></script>
  <script>
    function togglePin(btn) {
      if (btn.style.color === 'rgb(56, 189, 248)' || btn.style.color === 'var(--cpc-cyan)') {
        btn.style.color = '#64748b';
        showToast('Announcement Unpinned', 'info');
      } else {
        btn.style.color = 'var(--cpc-cyan)';
        showToast('Announcement Pinned to Top', 'success');
      }
    }

    function deleteAnnouncement(cardId) {
      if (confirm('Delete this announcement?')) {
        document.getElementById(cardId)?.remove();
        showToast('Announcement deleted', 'error');
      }
    }

    function openEditAnnouncement(title, body, isPinned) {
      document.getElementById('annModalTitle').textContent = 'Edit Announcement';
      document.getElementById('annTitleInput').value = title;
      document.getElementById('annMessageInput').value = body;
      document.getElementById('annPinCheckbox').checked = isPinned;
      openModal(document.getElementById('addAnnouncementModal'));
    }

    function handleSaveAnnouncement(e) {
      e.preventDefault();
      const title = document.getElementById('annTitleInput').value;
      const msg = document.getElementById('annMessageInput').value;
      const isPinned = document.getElementById('annPinCheckbox').checked;

      const list = document.getElementById('announcementsList');
      const newCard = document.createElement('div');
      newCard.className = 'stat-card';
      newCard.style.cssText = 'padding: 18px 24px; display: flex; flex-direction: row; justify-content: space-between; align-items: center;' + (isPinned ? ' border: 1.5px solid var(--cpc-cyan); background: rgba(21, 28, 77, 0.9);' : '');
      
      newCard.innerHTML = `
        <div>
          <h3 style="font-size: 1.05rem; font-weight: 700; color: #ffffff;" class="ann-title">${title}</h3>
          <p style="font-size: 0.85rem; color: #94a3b8; font-style: italic; margin-top: 4px;" class="ann-body">${msg}</p>
        </div>
        <div style="display: flex; gap: 14px; align-items: center;">
          <button type="button" class="profile-edit-btn" style="color: ${isPinned ? 'var(--cpc-cyan)' : '#64748b'};" onclick="togglePin(this)">
            <svg width="20" height="20" viewBox="0 0 24 24" fill="currentColor"><path d="M16 12V4h1V2H7v2h1v8l-2 2v2h5.2v6h1.6v-6H18v-2l-2-2z"></path></svg>
          </button>
          <button type="button" class="profile-edit-btn" onclick="openEditAnnouncement('${title.replace(/'/g, "\\'")}', '${msg.replace(/'/g, "\\'")}', ${isPinned})">
            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M12 20h9"></path><path d="M16.5 3.5a2.121 2.121 0 0 1 3 3L7 19l-4 1 1-4L16.5 3.5z"></path></svg>
          </button>
          <button type="button" class="profile-edit-btn" style="color: #ef4444;" onclick="this.closest('.stat-card').remove(); showToast('Announcement deleted', 'error');">
            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polyline points="3 6 5 6 21 6"></polyline><path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2"></path></svg>
          </button>
        </div>
      `;

      list.prepend(newCard);
      closeModal(document.getElementById('addAnnouncementModal'));
      showToast('Announcement saved!', 'success');
      e.target.reset();
    }
  </script>
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-YvpcrYf0tY3lHB60NNkmXc4s9bIOgUxi8T/jzmgXzuez/pHXEzlN77IfH8MRQ=" crossorigin="anonymous"></script>
</body>
</html>
