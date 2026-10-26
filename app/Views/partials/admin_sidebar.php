<?php
/** @var string $activeNav */
$activeNav = $activeNav ?? 'overview';
?>
<aside class="cpc-sidebar" id="adminSidebar">
  <a class="sidebar-brand" href="<?= base_url('admin/overview') ?>">
    <img src="<?= base_url('assets/img/door-orange.svg') ?>" style="width: 24px; height: 24px;" alt="Logo">
    <span>CPC RoomFinder</span>
  </a>

  <div class="sidebar-profile">
    <div class="profile-avatar">AD</div>
    <div class="profile-info">
      <div class="profile-name">Admin</div>
      <div class="profile-role">Administrator</div>
    </div>
    <button type="button" class="profile-edit-btn" title="Edit Admin Profile" aria-label="Edit Profile">
      <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
        <path d="M12 20h9"></path>
        <path d="M16.5 3.5a2.121 2.121 0 0 1 3 3L7 19l-4 1 1-4L16.5 3.5z"></path>
      </svg>
    </button>
  </div>

  <nav class="sidebar-nav">
    <a href="<?= base_url('admin/overview') ?>" class="nav-item <?= $activeNav === 'overview' ? 'active' : '' ?>">
      <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
        <rect x="3" y="3" width="7" height="9"></rect>
        <rect x="14" y="3" width="7" height="5"></rect>
        <rect x="14" y="12" width="7" height="9"></rect>
        <rect x="3" y="16" width="7" height="5"></rect>
      </svg>
      <span>Overview</span>
    </a>

    <a href="<?= base_url('admin/announcements') ?>" class="nav-item <?= $activeNav === 'announcements' ? 'active' : '' ?>">
      <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
        <path d="M18 8A6 6 0 0 0 6 8c0 7-3 9-3 9h18s-3-2-3-9"></path>
        <path d="M13.73 21a2 2 0 0 1-3.46 0"></path>
      </svg>
      <span>Announcements</span>
    </a>

    <a href="<?= base_url('admin/rooms') ?>" class="nav-item <?= $activeNav === 'rooms' ? 'active' : '' ?>">
      <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
        <rect x="4" y="3" width="16" height="18" rx="1"></rect>
        <line x1="16" y1="12" x2="16" y2="12.01" stroke-width="3"></line>
      </svg>
      <span>Manage Rooms</span>
    </a>

    <a href="<?= base_url('admin/users') ?>" class="nav-item <?= $activeNav === 'users' ? 'active' : '' ?>">
      <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
        <path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"></path>
        <circle cx="9" cy="7" r="4"></circle>
        <path d="M23 21v-2a4 4 0 0 0-3-3.87"></path>
        <path d="M16 3.13a4 4 0 0 1 0 7.75"></path>
      </svg>
      <span>Manage Users</span>
    </a>

    <a href="<?= base_url('admin/bookings') ?>" class="nav-item <?= $activeNav === 'bookings' ? 'active' : '' ?>">
      <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
        <rect x="3" y="4" width="18" height="16" rx="2"></rect>
        <circle cx="12" cy="10" r="3"></circle>
        <path d="M7 17a5 5 0 0 1 10 0"></path>
      </svg>
      <span>Manage Bookings</span>
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
