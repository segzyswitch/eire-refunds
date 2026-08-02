<?php
/**
 * includes/navbar.php
 * Expects $_SESSION['admin'] (set at login) with name/email/role.
 */
$currentAdmin = $_SESSION['admin'] ?? ['name' => 'Admin', 'email' => '', 'role' => 'Administrator'];
$initials = '';
foreach (explode(' ', trim($currentAdmin['name'])) as $part) { $initials .= strtoupper(substr($part, 0, 1)); }
$initials = substr($initials, 0, 2) ?: 'A';
?>
<header class="topbar">
  <div class="d-flex align-items-center gap-2">
    <button class="btn-toggle d-none d-lg-inline-flex" id="sidebarCollapseBtn" title="Toggle sidebar">
      <i class="bi bi-layout-sidebar"></i>
    </button>
    <button class="btn-toggle d-lg-none" id="sidebarMobileBtn" title="Menu">
      <i class="bi bi-list"></i>
    </button>

    <div class="search-box d-none d-md-block ms-2">
      <div class="input-group input-group-sm">
        <span class="input-group-text bg-transparent border-end-0"><i class="bi bi-search text-muted"></i></span>
        <input type="search" class="form-control border-start-0" placeholder="Search applications, FAQs, sliders…">
      </div>
    </div>
  </div>

  <div class="d-flex align-items-center gap-2">
    <a href="https://eiretaxrefund.com" class="btn btn-sm btn-outline-teal d-none d-md-inline-flex align-items-center gap-1" target="_blank">
      <i class="bi bi-box-arrow-up-right"></i> View Site
    </a>

    <div class="icon-btn" title="Notifications" data-bs-toggle="dropdown" role="button">
      <i class="bi bi-bell"></i>
      <span class="dot"></span>
    </div>
    <ul class="dropdown-menu dropdown-menu-end shadow-sm" style="min-width:280px;">
      <li><h6 class="dropdown-header">Notifications</h6></li>
      <li><a class="dropdown-item small" href="#"><i class="bi bi-person-plus text-success me-2"></i>New application from Conor Ryan</a></li>
      <li><a class="dropdown-item small" href="#"><i class="bi bi-cash-coin text-warning me-2"></i>Rebate payment issued — €675.00</a></li>
      <li><a class="dropdown-item small" href="#"><i class="bi bi-chat-left-text text-teal me-2"></i>New FAQ suggestion submitted</a></li>
      <li><hr class="dropdown-divider"></li>
      <li><a class="dropdown-item small text-center" href="#">View all</a></li>
    </ul>

    <div class="dropdown">
      <div class="d-flex align-items-center gap-2" role="button" data-bs-toggle="dropdown">
        <div class="avatar-circle"><?= h($initials) ?></div>
        <div class="d-none d-md-block lh-1">
          <div class="fw-semibold small"><?= h($currentAdmin['name']) ?></div>
          <div class="text-muted" style="font-size:.72rem;"><?= h($currentAdmin['role']) ?></div>
        </div>
        <i class="bi bi-chevron-down small text-muted d-none d-md-block"></i>
      </div>
      <ul class="dropdown-menu dropdown-menu-end shadow-sm">
        <li><a class="dropdown-item" href="<?= BASE_URL ?>/security.php"><i class="bi bi-person-circle me-2"></i>My Profile</a></li>
        <li><a class="dropdown-item" href="<?= BASE_URL ?>/security.php"><i class="bi bi-shield-lock me-2"></i>Security</a></li>
        <li><hr class="dropdown-divider"></li>
        <li><a class="dropdown-item text-danger" href="<?= BASE_URL ?>/logout.php"><i class="bi bi-box-arrow-right me-2"></i>Log Out</a></li>
      </ul>
    </div>
  </div>
</header>
