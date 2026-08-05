<?php
/**
 * includes/sidebar.php
 * Relies on $activeMenu (set by the calling page) to mark the active link.
 */
function nav_active($key, $activeMenu) { return $key === $activeMenu ? 'active' : ''; }
?>
<nav class="sidebar d-flex flex-column">
  <div class="sidebar-brand">
    <img src="assets/icon.png" alt="EIRE" height="35" />
    <div class="brand-text">
      <strong>EIRE TAX</strong>
      <span>Admin Panel</span>
    </div>
  </div>

  <div class="flex-grow-1 py-2">
    <div class="nav-section-title">Overview</div>
    <ul class="nav flex-column">
      <li class="nav-item">
        <a class="nav-link <?= nav_active('dashboard', $activeMenu) ?>" href="<?= BASE_URL ?>/dashboard.php">
          <i class="bi bi-grid-1x2-fill"></i><span class="nav-label">Dashboard</span>
        </a>
      </li>
      <li class="nav-item">
        <a class="nav-link <?= nav_active('tables', $activeMenu) ?>" href="<?= BASE_URL ?>/tables.php">
          <i class="bi bi-table"></i><span class="nav-label">Applications Table</span>
        </a>
      </li>
      <li class="nav-item">
        <a class="nav-link <?= nav_active('charts', $activeMenu) ?>" href="<?= BASE_URL ?>/charts.php">
          <i class="bi bi-bar-chart-line-fill"></i><span class="nav-label">Charts &amp; Reports</span>
        </a>
      </li>
    </ul>

    <div class="nav-section-title">Website Content</div>
    <ul class="nav flex-column">
      <li class="nav-item">
        <a class="nav-link <?= nav_active('sliders', $activeMenu) ?>" href="<?= BASE_URL ?>/sliders.php">
          <i class="bi bi-images"></i><span class="nav-label">Home Sliders</span>
        </a>
      </li>
      <li class="nav-item">
        <a class="nav-link <?= nav_active('faqs', $activeMenu) ?>" href="<?= BASE_URL ?>/faqs.php">
          <i class="bi bi-question-circle-fill"></i><span class="nav-label">FAQs</span>
        </a>
      </li>
      <li class="nav-item">
        <a class="nav-link <?= nav_active('site-content', $activeMenu) ?>" href="<?= BASE_URL ?>/site-content.php">
          <i class="bi bi-file-earmark-richtext-fill"></i><span class="nav-label">Site Info &amp; Copy</span>
        </a>
      </li>
    </ul>

    <!-- <div class="nav-section-title">UI Components</div>
    <ul class="nav flex-column">
      <li class="nav-item">
        <a class="nav-link <?= nav_active('forms', $activeMenu) ?>" href="<?= BASE_URL ?>/forms-elements.php">
          <i class="bi bi-ui-checks-grid"></i><span class="nav-label">Form Elements</span>
        </a>
      </li>
    </ul> -->

    <div class="nav-section-title">Account</div>
    <ul class="nav flex-column">
      <li class="nav-item">
        <a class="nav-link <?= nav_active('security', $activeMenu) ?>" href="<?= BASE_URL ?>/security.php">
          <i class="bi bi-shield-lock-fill"></i><span class="nav-label">Security</span>
        </a>
      </li>
      <li class="nav-item">
        <a class="nav-link" href="<?= BASE_URL ?>/logout.php">
          <i class="bi bi-box-arrow-right"></i><span class="nav-label">Log Out</span>
        </a>
      </li>
    </ul>
  </div>

  <div class="sidebar-footer">
    <span class="nav-label">Irish Tax Agent No. 66436K</span><br>
    <span class="nav-label">&copy; <?= date('Y') ?> EIRE Tax Refunds</span>
  </div>
</nav>
