<?php
$page_title = 'About Us | EIRE Tax Refunds';
$active = 'about';
include 'inc/header.php';

$statsHeading = itr_setting('stats_heading', 'The market leading tax rebate service');
$statsItems = itr_stats_items();
$trustNumber = itr_setting('trust_badge_number', '66436K');
$trustMessage = itr_setting('trust_message', 'Over 20 years experience in offering independent and confidential tax advice');
?>

<section class="itr-hero" style="background-image:url('https://picsum.photos/seed/about-us-hero/1600/450');">
  <div class="itr-hero-overlay"></div>
  <div class="container py-5 text-center">
    <p class="text-uppercase small mb-3">— About Us —</p>
    <h1 class="fw-bold display-6">The market leader in tax rebates</h1>
  </div>
</section>

<section class="py-5">
  <div class="container">
    <div class="row gy-4">
      <div class="col-lg-8">
        <h2 class="fw-bold" style="color:var(--itr-primary);">Many years of tax rebate expertise</h2>
        <p class="mt-3">Be it tax credits, tax reliefs or Universal Social Charge, PAYE workers regularly go about their lives without realising they could be owed thousands in tax rebates from Revenue. That's where we come in. Our purpose is clear: help people check for overpaid tax with Revenue, in the easiest possible way, and see what rebate they could be due back. We created our 60-second application form to make this process as simple, fast and successful as possible, and today we're a market leader in securing tax rebates for PAYE workers across Ireland.</p>
      </div>
      <div class="col-lg-4">
        <div class="d-flex align-items-center gap-3 border rounded p-3">
          <div class="text-white rounded d-flex flex-column align-items-center justify-content-center p-2" style="width:80px;height:80px;background:var(--itr-primary);">
            <!-- <div class="small">TAX AGENT NUMBER</div> -->
            <div class="small">TAN</div>
            <div class="fw-bold"><?php echo htmlspecialchars($trustNumber); ?></div>
          </div>
          <div class="small"><?php echo htmlspecialchars($trustMessage); ?></div>
        </div>
      </div>
    </div>
  </div>
</section>

<section class="itr-stats-section text-center">
  <div class="container">
    <h2 class="fw-bold mb-3">Benefits</h2>
    <p class="mb-4">EIRE Tax Refunds is trusted by Irish tax payers every year. We carry out full and thorough reviews - we check back through up to 4 years of taxes to give you the best possible chance of receiving a rebate.</p>
    <hr style="width:60px;opacity:.6;" class="mx-auto mb-4">
    <div class="row g-4">
      <?php foreach ($statsItems as $stat): ?>
      <div class="col-md-4">
        <div class="itr-stat-icon"><i class="bi bi-<?php echo htmlspecialchars($stat['icon']); ?>"></i></div>
        <h3 class="fw-bold"><?php echo htmlspecialchars($stat['value']); ?></h3>
        <p><?php echo htmlspecialchars($stat['description']); ?></p>
      </div>
      <?php endforeach; ?>
    </div>
  </div>
</section>

<div class="text-center py-5">
  <a href="faqs.php" class="btn btn-outline-dark rounded-0 px-4">View our FAQs</a>
</div>

<?php include 'inc/cta-banner.php'; ?>
<?php include 'inc/footer.php'; ?>
