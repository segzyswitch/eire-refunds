<?php
$page_title = 'FAQs | EIRE Tax Refunds';
$active = 'faqs';
include 'inc/header.php';

$faq_categories = itr_faqs_grouped();
?>

<section class="py-5">
  <div class="container">
    <p class="text-muted small">Frequently Asked Questions</p>
    <h1 class="fw-bold" style="color:var(--itr-primary);">Your Tax Rebate - FAQs</h1>
    <p class="mt-3">Here are some of our most frequently asked questions about EIRE Tax Refunds. If you have a question that's not answered here, please use our live chat to speak to our Customer Service team, or email us at <a href="mailto:<?php echo htmlspecialchars(itr_setting('contact_email', 'info@irishtaxrebates.ie')); ?>"><?php echo htmlspecialchars(itr_setting('contact_email', 'info@irishtaxrebates.ie')); ?></a>.</p>

    <?php if (empty($faq_categories)): ?>
      <div class="alert alert-light border mt-4">No FAQs are published yet. Add some from the admin panel's FAQs page.</div>
    <?php endif; ?>

    <?php foreach ($faq_categories as $category => $items):
      $cat_id = 'cat-' . preg_replace('/[^a-z0-9]+/', '-', strtolower($category));
    ?>
      <h2 class="itr-faq-category" id="<?php echo $cat_id; ?>"><?php echo htmlspecialchars($category); ?></h2>
      <div class="accordion itr-faq mb-4" id="accordion-<?php echo $cat_id; ?>">
        <?php foreach ($items as $i => $item): $item_id = $cat_id . '-q' . $i; ?>
          <div class="accordion-item">
            <h3 class="accordion-header">
              <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#<?php echo $item_id; ?>">
                <?php echo htmlspecialchars($item['question']); ?>
              </button>
            </h3>
            <div id="<?php echo $item_id; ?>" class="accordion-collapse collapse" data-bs-parent="#accordion-<?php echo $cat_id; ?>">
              <div class="accordion-body text-muted">
                <?php echo nl2br(htmlspecialchars($item['answer'])); ?>
              </div>
            </div>
          </div>
        <?php endforeach; ?>
      </div>
    <?php endforeach; ?>
  </div>
</section>

<?php include 'inc/cta-banner.php'; ?>
<?php include 'inc/footer.php'; ?>
