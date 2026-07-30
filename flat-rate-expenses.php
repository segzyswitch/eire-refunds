<?php
$page_title = 'Flat Rate Expenses | EIRE Tax Refunds';
$active = 'flat';
include 'inc/header.php';
?>

<section class="py-5" style="background:#f4f4f4;">
  <div class="container">
    <p class="small text-muted mb-3"><a href="index.php" class="text-muted text-decoration-none">Home</a> / Flat Rate Expenses</p>

    <div class="row align-items-start gy-4">
      <div class="col-lg-6">
        <h1 class="fw-bold">Claim <span style="color:var(--itr-primary);">tax back</span> on flat rate expenses</h1>
        <p class="mt-3">Many employees such as <a href="medical-expenses.php">Medical and Healthcare Professionals</a>, Engineers, Tradespeople, Teachers, Lecturers, Hairdressers, Beauticians, Retail workers and a host of other professions are eligible to claim tax back on a range of flat rate expenses yet are unaware of the extent they can claim back.</p>
        <p>Fill in our 60-second form and we'll help you <a href="#">claim back</a> all that is owed to you.</p>
        <div class="position-relative mt-4">
          <img src="https://picsum.photos/seed/flat-rate-nurse/500/500" class="img-fluid rounded" alt="Healthcare professional checking her phone">
          <span class="badge rounded-circle d-inline-flex flex-column justify-content-center position-absolute" style="width:140px;height:140px;top:-24px;left:-24px;background:var(--itr-primary);padding:10px;line-height:1.3;">
            <span style="font-size:.8rem;">Average Rebate</span>
            <strong style="font-size:1.5rem;">&euro;<?php echo number_format((float) itr_setting('hero_average_rebate', '1092'), 0); ?></strong>
          </span>
        </div>
      </div>
      <div class="col-lg-6">
        <div class="itr-form-embed mx-auto" style="max-width:420px;" id="apply">
          <?php include 'inc/multi-form.php'; ?>
        </div>
      </div>
    </div>
  </div>
</section>

<?php include 'inc/trust-strip.php'; ?>

<section class="py-5">
  <div class="container">
    <p class="itr-eyebrow">What To Claim</p>
    <h1 class="fw-bold" style="color:var(--itr-primary);">Claim Tax Back on Flat Rate Expenses</h1>
    <p class="mt-3">Flat rate expenses refer to costs incurred by an employee on items that are necessary to complete their work such as purchasing <a href="#">uniforms</a>, equipment or tools.</p>
    <p>A wide range of occupations are eligible to claim tax back on flat rate expenses, the most <a href="#">prevalent being Medical and Healthcare professionals</a> as well as Engineers, Tradespeople, Teachers, Lecturers, Hairdressers, Beauticians, Retail workers and more.</p>
    <p>As a registered Irish Tax Agent, we are happy to help maximise your tax rebate. To get started, simply <a href="#">complete our quick online registration form</a> and we'll revert within 1 working day with the next steps towards securing your tax refund.</p>
  </div>
</section>

<?php include 'inc/cta-banner.php'; ?>
<?php include 'inc/footer.php'; ?>
