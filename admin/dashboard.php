<?php
require __DIR__ . '/includes/config.php';
require_login();

$kpis = get_kpi_totals();
$weekly = get_weekly_rebate_totals();
$statusBreakdown = get_status_breakdown();

$recentApplications = itr_db()->query('SELECT * FROM applications ORDER BY submitted_at DESC LIMIT 6')->fetchAll();
$sliderCounts = itr_db()->query("SELECT COUNT(*) AS total, SUM(status = 'Published') AS published FROM sliders")->fetch();
$faqCounts = itr_db()->query("SELECT COUNT(*) AS total, SUM(status = 'Published') AS published FROM faqs")->fetch();

$statusClasses = [
    'Paid' => 'status-paid',
    'Processing' => 'status-processing',
    'Awaiting Agent Link' => 'status-awaiting',
    'Not Due' => 'status-not-due',
    'New' => 'status-draft',
];

$pageTitle = 'Dashboard';
$activeMenu = 'dashboard';
require __DIR__ . '/includes/header.php';
require __DIR__ . '/includes/sidebar.php';
?>
<div class="main-col">
  <?php require __DIR__ . '/includes/navbar.php'; ?>

  <div class="content-area">
    <div class="page-header">
      <div>
        <div class="breadcrumb-eyebrow">Overview</div>
        <h1>Welcome back, <?= h(explode(' ', $_SESSION['admin']['name'])[0]) ?> 👋</h1>
      </div>
      <div class="d-flex gap-2">
        <a href="sliders.php?action=add" class="btn btn-outline-teal btn-sm"><i class="bi bi-plus-lg me-1"></i>Add Slider</a>
        <a href="tables.php" class="btn btn-brand btn-sm"><i class="bi bi-table me-1"></i>View Applications</a>
      </div>
    </div>

    <!-- KPI row -->
    <div class="row g-3 mb-3">
      <div class="col-sm-6 col-xl-3">
        <div class="card kpi-card h-100">
          <div class="kpi-icon teal"><i class="bi bi-people-fill"></i></div>
          <div>
            <div class="kpi-value"><?= (int) $kpis['total_applications'] ?></div>
            <div class="kpi-label">Total Applications</div>
          </div>
        </div>
      </div>
      <div class="col-sm-6 col-xl-3">
        <div class="card kpi-card h-100">
          <div class="kpi-icon green"><i class="bi bi-cash-coin"></i></div>
          <div>
            <div class="kpi-value">€<?= number_format((float) $kpis['total_rebate'], 0) ?></div>
            <div class="kpi-label">Total Rebates Issued</div>
          </div>
        </div>
      </div>
      <div class="col-sm-6 col-xl-3">
        <div class="card kpi-card h-100">
          <div class="kpi-icon orange"><i class="bi bi-hourglass-split"></i></div>
          <div>
            <div class="kpi-value"><?= (int) $kpis['processing_count'] ?></div>
            <div class="kpi-label">New / In Progress</div>
          </div>
        </div>
      </div>
      <div class="col-sm-6 col-xl-3">
        <div class="card kpi-card h-100">
          <div class="kpi-icon navy"><i class="bi bi-graph-up-arrow"></i></div>
          <div>
            <div class="kpi-value">€<?= number_format((float) $kpis['avg_rebate'], 0) ?></div>
            <div class="kpi-label">Average Rebate (Paid)</div>
          </div>
        </div>
      </div>
    </div>

    <div class="row g-3 mb-3">
      <div class="col-xl-8">
        <div class="card h-100">
          <div class="card-header d-flex justify-content-between align-items-center">
            <span><i class="bi bi-bar-chart-line me-1 text-teal"></i>Rebates Issued — Live from Database</span>
          </div>
          <div class="card-body">
            <canvas id="dashboardTrendChart" height="110"></canvas>
          </div>
        </div>
      </div>
      <div class="col-xl-4">
        <div class="card h-100">
          <div class="card-header"><i class="bi bi-pie-chart me-1 text-teal"></i>Applications by Status</div>
          <div class="card-body d-flex align-items-center justify-content-center">
            <canvas id="dashboardStatusChart" height="220"></canvas>
          </div>
        </div>
      </div>
    </div>

    <div class="row g-3">
      <div class="col-xl-8">
        <div class="card">
          <div class="card-header d-flex justify-content-between align-items-center">
            <span><i class="bi bi-clock-history me-1 text-teal"></i>Recent Applications</span>
            <a href="tables.php" class="small">View all <i class="bi bi-arrow-right"></i></a>
          </div>
          <div class="table-responsive">
            <table class="table mb-0">
              <thead>
                <tr><th>Applicant</th><th>County</th><th>Rebate</th><th>Status</th><th>Submitted</th></tr>
              </thead>
              <tbody>
                <?php foreach ($recentApplications as $a): ?>
                <tr>
                  <td>
                    <div class="fw-semibold"><?= h($a['first_name'] . ' ' . $a['last_name']) ?></div>
                    <div class="text-muted small"><?= h($a['email']) ?></div>
                  </td>
                  <td><?= h($a['county']) ?></td>
                  <td>€<?= number_format((float) $a['rebate_amount'], 2) ?></td>
                  <td>
                    <?php $cls = $statusClasses[$a['status']] ?? 'status-draft'; ?>
                    <span class="status-pill <?= $cls ?>"><?= h($a['status']) ?></span>
                  </td>
                  <td class="text-muted small"><?= date('d M Y', strtotime($a['submitted_at'])) ?></td>
                </tr>
                <?php endforeach; ?>
              </tbody>
            </table>
          </div>
        </div>
      </div>

      <div class="col-xl-4">
        <div class="card mb-3">
          <div class="card-header"><i class="bi bi-images me-1 text-teal"></i>Home Sliders</div>
          <div class="card-body">
            <div class="d-flex justify-content-between align-items-center mb-2">
              <span class="text-muted small">Published</span>
              <span class="fw-semibold"><?= (int) $sliderCounts['published'] ?> / <?= (int) $sliderCounts['total'] ?></span>
            </div>
            <a href="sliders.php" class="btn btn-outline-teal btn-sm w-100">Manage Sliders</a>
          </div>
        </div>
        <div class="card">
          <div class="card-header"><i class="bi bi-question-circle me-1 text-teal"></i>FAQs</div>
          <div class="card-body">
            <div class="d-flex justify-content-between align-items-center mb-2">
              <span class="text-muted small">Published</span>
              <span class="fw-semibold"><?= (int) $faqCounts['published'] ?> / <?= (int) $faqCounts['total'] ?></span>
            </div>
            <a href="faqs.php" class="btn btn-outline-teal btn-sm w-100">Manage FAQs</a>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>

<?php
$extraScripts = '<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.4/dist/chart.umd.min.js"></script>
<script>
new Chart(document.getElementById("dashboardTrendChart"), {
  type: "bar",
  data: {
    labels: ' . json_encode($weekly['labels']) . ',
    datasets: [{
      label: "Rebates issued (€)",
      data: ' . json_encode($weekly['values']) . ',
      backgroundColor: "#0b6e63",
      borderRadius: 6,
      maxBarThickness: 46
    }]
  },
  options: { plugins: { legend: { display: false } }, scales: { y: { beginAtZero: true } } }
});

new Chart(document.getElementById("dashboardStatusChart"), {
  type: "doughnut",
  data: {
    labels: ' . json_encode($statusBreakdown['labels']) . ',
    datasets: [{
      data: ' . json_encode($statusBreakdown['values']) . ',
      backgroundColor: ["#8aa1c1", "#3fae5b", "#f4b942", "#263449", "#e57373"]
    }]
  },
  options: { plugins: { legend: { position: "bottom", labels: { boxWidth: 10, font: { size: 11 } } } } }
});
</script>';
require __DIR__ . '/includes/footer.php';
