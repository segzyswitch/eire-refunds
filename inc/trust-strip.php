<?php /* inc/trust-strip.php — red "Irish Tax Agent" strip under the hero on the home & flat-rate pages */ ?>
<section class="itr-trust-strip">
  <div class="container d-flex align-items-center flex-wrap gap-3">
    <span class="badge-shield"><i class="bi bi-shield-check fs-5"></i></span>
    <div>
      <div class="fw-bold"><?php echo htmlspecialchars(itr_setting('trust_badge_label', 'Irish Tax Agent')); ?></div>
      <div>No. <?php echo htmlspecialchars(itr_setting('trust_badge_number', '66436K')); ?></div>
    </div>
    <div class="ms-lg-4"><?php echo htmlspecialchars(itr_setting('trust_message', 'Over 20 years experience in offering independent and confidential tax advice')); ?></div>
  </div>
</section>
