<?php
require __DIR__ . '/includes/config.php';
require_login();

// Every dataset below comes straight from includes/queries.php — edit the
// SQL there to change what any chart on this page shows.
$weeklyTrend = get_weekly_applications_vs_rebates();
$typeTotals = get_rebate_totals_by_type();
$countyBreakdown = get_applications_by_county();

$pageTitle = 'Charts & Reports';
$activeMenu = 'charts';
require __DIR__ . '/includes/header.php';
require __DIR__ . '/includes/sidebar.php';
?>
<div class="main-col">
  <?php require __DIR__ . '/includes/navbar.php'; ?>

  <div class="content-area">
    <div class="page-header">
      <div>
        <div class="breadcrumb-eyebrow">Analytics</div>
        <h1>Charts &amp; Reports</h1>
        <p class="text-muted small mb-0">Every chart below queries the <code>applications</code> table directly — see <code>includes/queries.php</code> to adjust.</p>
      </div>
    </div>

    <div class="row g-3 mb-3">
      <div class="col-xl-8">
        <div class="card h-100">
          <div class="card-header"><i class="bi bi-graph-up me-1 text-teal"></i>Rebates Issued vs Applications Received</div>
          <div class="card-body"><canvas id="lineChart" height="100"></canvas></div>
        </div>
      </div>
      <div class="col-xl-4">
        <div class="card h-100">
          <div class="card-header"><i class="bi bi-pie-chart-fill me-1 text-teal"></i>Rebate Value by Type</div>
          <div class="card-body"><canvas id="doughnutChart" height="180"></canvas></div>
          <div class="card-footer text-muted small">"Unclassified" = new applications not yet reviewed by an agent.</div>
        </div>
      </div>
    </div>

    <div class="row g-3">
      <div class="col-xl-6">
        <div class="card h-100">
          <div class="card-header"><i class="bi bi-bar-chart-fill me-1 text-teal"></i>Applications by County</div>
          <div class="card-body"><canvas id="countyChart" height="140"></canvas></div>
        </div>
      </div>
      <div class="col-xl-6">
        <div class="card h-100">
          <div class="card-header"><i class="bi bi-hexagon-fill me-1 text-teal"></i>Service Score Card</div>
          <div class="card-body"><canvas id="radarChart" height="140"></canvas></div>
          <div class="card-footer text-muted small">
            Editorial scores set by the team — edit the array in this page's script block to update.
          </div>
        </div>
      </div>
    </div>
  </div>
</div>

<?php
$extraScripts = '<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.4/dist/chart.umd.min.js"></script>
<script>
const brand = { teal: "#0b6e63", green: "#3fae5b", orange: "#f4821f", navy: "#263449" };

new Chart(document.getElementById("lineChart"), {
  type: "line",
  data: {
    labels: ' . json_encode($weeklyTrend['labels']) . ',
    datasets: [
      { label: "Applications received", data: ' . json_encode($weeklyTrend['applications']) . ', borderColor: brand.navy, backgroundColor: "transparent", tension: .35 },
      { label: "Rebates issued (€00s)", data: ' . json_encode($weeklyTrend['rebates']) . ', borderColor: brand.teal, backgroundColor: "rgba(11,110,99,.12)", fill: true, tension: .35 }
    ]
  },
  options: { plugins: { legend: { position: "bottom" } } }
});

new Chart(document.getElementById("doughnutChart"), {
  type: "doughnut",
  data: {
    labels: ' . json_encode($typeTotals['labels']) . ',
    datasets: [{ data: ' . json_encode($typeTotals['values']) . ', backgroundColor: ["#0b6e63","#3fae5b","#f4821f","#263449","#8aa1c1","#c2b8a3"] }]
  },
  options: { plugins: { legend: { position: "bottom", labels: { boxWidth: 10, font: { size: 10 } } } } }
});

new Chart(document.getElementById("countyChart"), {
  type: "bar",
  data: {
    labels: ' . json_encode($countyBreakdown['labels']) . ',
    datasets: [{ label: "Applications", data: ' . json_encode($countyBreakdown['values']) . ', backgroundColor: brand.green, borderRadius: 6 }]
  },
  options: { indexAxis: "y", plugins: { legend: { display: false } } }
});

// Static editorial scorecard — not tied to a table, tweak the data array directly.
new Chart(document.getElementById("radarChart"), {
  type: "radar",
  data: {
    labels: ["Speed","Rebate size","Trust","Support","Simplicity"],
    datasets: [{ label: "EIRE Tax Refunds", data: [9,8,9,7,9], borderColor: brand.teal, backgroundColor: "rgba(11,110,99,.2)" }]
  },
  options: { scales: { r: { beginAtZero: true, max: 10 } } }
});
</script>';
require __DIR__ . '/includes/footer.php';
