<?php
$page_title = 'EIRE Tax Refunds | Claim Your Tax Back';
$active = 'home';
include 'inc/header.php';

$sliders = itr_sliders();
if (empty($sliders)) {
    // DB unreachable or no slides published yet — fall back to the hero
    // settings so the homepage never renders with an empty carousel.
    $sliders = [[
        'title' => itr_setting('hero_heading', 'Financially Supporting a Relative?'),
        'subtitle' => itr_setting('hero_body', "There's a tax rebate for that. We guarantee the highest possible tax rebate in Ireland. EIRE Tax Refunds will find and fight for every cent, with the most comprehensive tax review in the market."),
        'badge_text' => 'Average Rebate',
        'badge_value' => itr_setting('hero_average_rebate', '1,092'),
        'image' => 'https://picsum.photos/seed/itr-relative/560/560',
        'cta_label' => 'Apply For My Rebate Now',
        'cta_url' => '#apply',
    ]];
}

$stepsHeading = itr_setting('how_it_works_heading', 'Highest Rebate. We work for you. No Rebate, No Fee.');
$stepsIntro = itr_setting('how_it_works_intro');
$steps = itr_how_it_works_steps();

$statsHeading = itr_setting('stats_heading', 'The market leading tax rebate service');
$statsItems = itr_stats_items();

$storyEyebrow = itr_setting('story_eyebrow', 'Our Story');
$storyHeading = itr_setting('story_heading', 'Helping people claim tax back');
$storyBody = itr_setting('story_body');
?>

<!-- ================= HERO (slider) + FORM ================= -->
<section class="itr-hero-section" id="apply" style="background:#f4f4f4;">
  <div class="container">
    <div class="row align-items-center gy-4">

      <!-- Text + photo travel together as one slide -->
      <div class="col-lg-8">
        <div id="heroCarousel" class="carousel slide carousel-fade" data-bs-ride="carousel" data-bs-interval="4500">
          <div class="carousel-inner">

            <?php foreach ($sliders as $i => $slide):
              // Split the heading roughly in half onto two lines, matching the original design's <br><span> treatment.
              $words = explode(' ', $slide['title']);
              $mid = (int) ceil(count($words) / 2);
              $line1 = implode(' ', array_slice($words, 0, $mid));
              $line2 = implode(' ', array_slice($words, $mid));
            ?>
            <div class="carousel-item<?php echo $i === 0 ? ' active' : ''; ?>">
              <div class="row align-items-center gy-4">
                <div class="col-md-6 order-2 order-sm-1 pt-3 pt-sm-0">
                  <h1 class="itr-hero-heading"><?php echo htmlspecialchars($line1); ?><?php if ($line2 !== ''): ?><br><span class="itr-hero-underline"><?php echo htmlspecialchars($line2); ?></span><?php endif; ?></h1>
                  <p class="itr-hero-copy"><?php echo $slide['subtitle']; ?></p>
                </div>
                <div class="col-md-6 px-sm-0 pt-3 pt-sm-0 order-1 order-sm-2">
                  <div class="itr-hero-photo-wrap">
                    <!-- <div class="itr-hero-photo-deco"></div> -->
                    <img src="assets/images/slider-graphic.png" class="itr-slider-graphic" />
                    <img src="<?php echo htmlspecialchars($slide['image']); ?>" class="itr-hero-photo" alt="<?php echo htmlspecialchars($slide['title']); ?>">
                    <div class="itr-hero-badge"><span><?php echo htmlspecialchars($slide['badge_text']); ?></span><strong>&euro;<?php echo htmlspecialchars($slide['badge_value']); ?></strong></div>
                  </div>
                </div>
              </div>
            </div>
            <?php endforeach; ?>

          </div>
        </div>
      </div>

      <!-- Form: static, doesn't change between slides -->
      <div class="col-lg-4 ps-sm-0 position-relative mb-auto">
        <div class="itr-form-embed mx-auto" style="max-width:420px;">
          <?php include 'inc/multi-form.php'; ?>
        </div>
      </div>

    </div>
  </div>
</section>

<?php include 'inc/trust-strip.php'; ?>

<!-- ================= HIGHEST REBATE COPY ================= -->
<section class="py-5" style="background:#f4f4f4;">
  <div class="container">
    <h2 class="fw-bold mb-4"><?php echo htmlspecialchars($stepsHeading); ?></h2>
    <p><?php echo $stepsIntro; ?></p>

    <!-- ================= HOW IT WORKS ================= -->
    <div class="itr-steps-panel mt-5" id="how-it-works">
      <div class="panel-title">How it Works</div>
      <div class="row g-0">
        <?php foreach ($steps as $step): ?>
        <div class="col-md itr-step">
          <div class="step-num"><?php echo (int) $step['step_number']; ?></div>
          <p class="fw-bold mb-1"><?php echo htmlspecialchars($step['title']); ?></p>
          <p><?php echo htmlspecialchars($step['description']); ?></p>
        </div>
        <?php endforeach; ?>
      </div>
    </div>

    <a href="#apply" class="btn btn-itr-primary mt-4">Apply For Your Tax Rebate</a>
  </div>
</section>

<!-- ================= STATS ================= -->
<section class="itr-stats-section text-center">
  <div class="container">
    <h2 class="fw-bold mb-2"><?php echo htmlspecialchars($statsHeading); ?></h2>
    <hr style="width:60px;opacity:.6;" class="mx-auto my-4">
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

<!-- ================= OUR STORY ================= -->
<section class="py-0">
  <div class="row g-0 align-items-stretch">
    <div class="col-lg-5">
      <img src="https://picsum.photos/seed/itr-story/700/700" class="w-100 h-100" style="object-fit:cover;" alt="EIRE Tax Refunds team member">
    </div>
    <div class="col-lg-7 d-flex align-items-center">
      <div class="p-5">
        <div class="itr-eyebrow mb-2"><?php echo htmlspecialchars($storyEyebrow); ?></div>
        <h2 class="fw-bold mb-3"><?php echo htmlspecialchars($storyHeading); ?></h2>
        <p><?php echo htmlspecialchars($storyBody); ?></p>
      </div>
    </div>
  </div>
</section>

<?php include 'inc/cta-banner.php'; ?>
<?php include 'inc/footer.php'; ?>
