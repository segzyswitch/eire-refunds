<?php /* inc/cta-banner.php — "Get your tax back" banner used on most inner pages */ ?>
<section class="itr-cta-banner">
  <div class="container">
    <h2 class="mb-4"><?php echo htmlspecialchars(itr_setting('cta_heading', 'Get your tax back')); ?></h2>
    <a href="index.php#apply" class="btn btn-outline-light px-4 py-2">
      <i class="bi bi-arrow-repeat me-2"></i><?php echo htmlspecialchars(itr_setting('cta_button_text', "Fill out our 60-second tax application form")); ?>
    </a>
  </div>
</section>
