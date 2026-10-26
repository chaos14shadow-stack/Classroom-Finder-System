<?php
/** @var string $activeNav */
$activeNav = $activeNav ?? 'bookings';
?>
<aside class="cpc-sidebar" id="instructorSidebar">
  <a class="sidebar-brand" href="<?= base_url('instructor/bookings') ?>">
    <img src="<?= base_url('assets/img/door-orange.svg') ?>" style="width: 24px; height: 24px;" alt="Logo">
    <span>CPC RoomFinder</span>
  </a>

  <div class="sidebar-profile">
    <div class="profile-avatar">JD</div>
    <div class="profile-info">
      <div class="profile-name">Prof. Doe</div>
      <div class="profile-role">Instructor</div>
    </div>
    <button type="button" class="profile-edit-btn" title="Edit Profile" aria-label="Edit Profile">
      <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
        <path d="M12 20h9"></path>
        <path d="M16.5 3.5a2.121 2.121 0 0 1 3 3L7 19l-4 1 1-4L16.5 3.5z"></path>
      </svg>
    </button>
  </div>

  <nav class="sidebar-nav">
    <a href="<?= base_url('instructor/dashboard') ?>" class="nav-item <?= $activeNav === 'home' ? 'active' : '' ?>">
      <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
        <path d="M3 9l9-7 9 7v11a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2z"></path>
        <polyline points="9 22 9 12 15 12 15 22"></polyline>
      </svg>
      <span>Home</span>
    </a>

    <a href="<?= base_url('instructor/book') ?>" class="nav-item <?= $activeNav === 'book' ? 'active' : '' ?>">
      <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
        <circle cx="11" cy="11" r="8"></circle>
        <line x1="21" y1="21" x2="16.65" y2="16.65"></line>
      </svg>
      <span>Book a room</span>
    </a>

    <a href="<?= base_url('instructor/announcements') ?>" class="nav-item <?= $activeNav === 'announcements' ? 'active' : '' ?>">
      <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
        <path d="M18 8A6 6 0 0 0 6 8c0 7-3 9-3 9h18s-3-2-3-9"></path>
        <path d="M13.73 21a2 2 0 0 1-3.46 0"></path>
      </svg>
      <span>Announcements</span>
    </a>

    <a href="<?= base_url('instructor/bookings') ?>" class="nav-item <?= $activeNav === 'bookings' ? 'active' : '' ?>">
      <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
        <rect x="3" y="4" width="18" height="16" rx="2"></rect>
        <circle cx="12" cy="10" r="3"></circle>
        <path d="M7 17a5 5 0 0 1 10 0"></path>
      </svg>
      <span>My Bookings</span>
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
