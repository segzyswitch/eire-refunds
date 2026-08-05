<?php
require __DIR__ . '/includes/config.php';
require_login();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $formType = $_POST['form_type'] ?? '';

    if ($formType === 'hero') {
        save_site_setting('hero_heading', trim($_POST['heading'] ?? ''));
        save_site_setting('hero_body', trim($_POST['body'] ?? ''));
        save_site_setting('hero_average_rebate', trim($_POST['average_rebate'] ?? ''));
        set_flash('success', 'Hero section updated.');
    }

    if ($formType === 'trust_bar') {
        save_site_setting('trust_badge_label', trim($_POST['badge_label'] ?? ''));
        save_site_setting('trust_badge_number', trim($_POST['badge_number'] ?? ''));
        save_site_setting('trust_message', trim($_POST['message'] ?? ''));
        set_flash('success', 'Trust bar updated.');
    }

    if ($formType === 'how_it_works') {
        save_site_setting('how_it_works_heading', trim($_POST['heading'] ?? ''));
        save_site_setting('how_it_works_intro', trim($_POST['intro'] ?? ''));

        // Simplest way to keep step ordering correct: replace the set inside a transaction.
        itr_db()->beginTransaction();
        itr_db()->exec('DELETE FROM how_it_works_steps');
        $insert = itr_db()->prepare('INSERT INTO how_it_works_steps (step_number, title, description, sort_order) VALUES (:n, :title, :description, :n)');
        foreach ($_POST['step_title'] ?? [] as $i => $title) {
            $insert->execute([
                'n' => $i + 1,
                'title' => trim($title),
                'description' => trim($_POST['step_description'][$i] ?? ''),
            ]);
        }
        itr_db()->commit();
        set_flash('success', '"How it Works" steps updated.');
    }

    if ($formType === 'stats') {
        save_site_setting('stats_heading', trim($_POST['heading'] ?? ''));

        itr_db()->beginTransaction();
        itr_db()->exec('DELETE FROM stats_items');
        $insert = itr_db()->prepare('INSERT INTO stats_items (icon, value, description, sort_order) VALUES (:icon, :value, :description, :sort)');
        foreach ($_POST['stat_value'] ?? [] as $i => $value) {
            $insert->execute([
                'icon' => trim($_POST['stat_icon'][$i] ?? 'award'),
                'value' => trim($value),
                'description' => trim($_POST['stat_description'][$i] ?? ''),
                'sort' => $i + 1,
            ]);
        }
        itr_db()->commit();
        set_flash('success', 'Stats band updated.');
    }

    if ($formType === 'our_story') {
        save_site_setting('story_eyebrow', trim($_POST['eyebrow'] ?? ''));
        save_site_setting('story_heading', trim($_POST['heading'] ?? ''));
        save_site_setting('story_body', trim($_POST['body'] ?? ''));
        set_flash('success', '"Our Story" section updated.');
    }

    if ($formType === 'contact') {
        save_site_setting('contact_phone_1', trim($_POST['phone_1'] ?? ''));
        save_site_setting('contact_phone_2', trim($_POST['phone_2'] ?? ''));
        save_site_setting('contact_email', trim($_POST['email'] ?? ''));
        save_site_setting('contact_address', trim($_POST['address'] ?? ''));
        set_flash('success', 'Contact details updated.');
    }

    if ($formType === 'cta_banner') {
        save_site_setting('cta_heading', trim($_POST['heading'] ?? ''));
        save_site_setting('cta_button_text', trim($_POST['button_text'] ?? ''));
        set_flash('success', '"Get your tax back" banner updated.');
    }

    if ($formType === 'footer') {
        save_site_setting('footer_cro', trim($_POST['cro'] ?? ''));
        save_site_setting('footer_vat', trim($_POST['vat'] ?? ''));
        save_site_setting('footer_copyright', trim($_POST['copyright'] ?? ''));
        set_flash('success', 'Footer details updated.');
    }

    header('Location: ' . BASE_URL . '/site-content.php#' . ($_POST['tab'] ?? 'hero'));
    exit;
}

$settings = get_site_settings();
$steps = itr_db()->query('SELECT * FROM how_it_works_steps ORDER BY sort_order ASC')->fetchAll();
$statItems = itr_db()->query('SELECT * FROM stats_items ORDER BY sort_order ASC')->fetchAll();

$pageTitle = 'Site Info & Copy';
$activeMenu = 'site-content';
require __DIR__ . '/includes/header.php';
require __DIR__ . '/includes/sidebar.php';
?>
<div class="main-col">
  <?php require __DIR__ . '/includes/navbar.php'; ?>

  <div class="content-area">
    <div class="page-header">
      <div>
        <div class="breadcrumb-eyebrow">Website Content</div>
        <h1>Site Info &amp; Copy</h1>
        <p class="text-muted small mb-0">Edit the text blocks that power the public homepage — stored in the <code>site_settings</code>, <code>how_it_works_steps</code> and <code>stats_items</code> tables.</p>
      </div>
    </div>

    <ul class="nav nav-tabs mb-3" id="siteTabs" role="tablist">
      <li class="nav-item"><button class="nav-link active" data-bs-toggle="tab" data-bs-target="#tab-hero" type="button"><i class="bi bi-flag me-1"></i>Hero</button></li>
      <li class="nav-item"><button class="nav-link" data-bs-toggle="tab" data-bs-target="#tab-trust" type="button"><i class="bi bi-patch-check me-1"></i>Trust Bar</button></li>
      <li class="nav-item"><button class="nav-link" data-bs-toggle="tab" data-bs-target="#tab-steps" type="button"><i class="bi bi-list-ol me-1"></i>How it Works</button></li>
      <li class="nav-item"><button class="nav-link" data-bs-toggle="tab" data-bs-target="#tab-stats" type="button"><i class="bi bi-bar-chart me-1"></i>Stats Band</button></li>
      <li class="nav-item"><button class="nav-link" data-bs-toggle="tab" data-bs-target="#tab-story" type="button"><i class="bi bi-book me-1"></i>Our Story</button></li>
      <li class="nav-item"><button class="nav-link" data-bs-toggle="tab" data-bs-target="#tab-contact" type="button"><i class="bi bi-telephone me-1"></i>Contact</button></li>
      <li class="nav-item"><button class="nav-link" data-bs-toggle="tab" data-bs-target="#tab-cta" type="button"><i class="bi bi-megaphone me-1"></i>CTA Banner</button></li>
      <li class="nav-item"><button class="nav-link" data-bs-toggle="tab" data-bs-target="#tab-footer" type="button"><i class="bi bi-layout-text-window-reverse me-1"></i>Footer</button></li>
    </ul>

    <div class="tab-content">

      <!-- HERO -->
      <div class="tab-pane fade show active" id="tab-hero">
        <div class="card">
          <div class="card-header">Hero Section — "Financially Supporting a Relative?"</div>
          <div class="card-body">
            <form method="post" class="row g-3">
              <input type="hidden" name="form_type" value="hero">
              <input type="hidden" name="tab" value="tab-hero">
              <div class="col-md-8">
                <label class="form-label">Heading</label>
                <input type="text" name="heading" class="form-control" value="<?= h($settings['hero_heading'] ?? '') ?>">
              </div>
              <div class="col-md-4">
                <label class="form-label">Average rebate (€)</label>
                <input type="text" name="average_rebate" class="form-control" value="<?= h($settings['hero_average_rebate'] ?? '') ?>">
              </div>
              <div class="col-12">
                <label class="form-label">Body text</label>
                <textarea name="body" class="form-control" rows="3"><?= h($settings['hero_body'] ?? '') ?></textarea>
              </div>
              <div class="col-12 d-flex justify-content-end">
                <button class="btn btn-brand"><i class="bi bi-save me-1"></i>Save Hero Section</button>
              </div>
            </form>
          </div>
        </div>
      </div>

      <!-- TRUST BAR -->
      <div class="tab-pane fade" id="tab-trust">
        <div class="card">
          <div class="card-header">Trust Bar — "Irish Tax Agent No. 66436K"</div>
          <div class="card-body">
            <form method="post" class="row g-3">
              <input type="hidden" name="form_type" value="trust_bar">
              <input type="hidden" name="tab" value="tab-trust">
              <div class="col-md-4">
                <label class="form-label">Badge label</label>
                <input type="text" name="badge_label" class="form-control" value="<?= h($settings['trust_badge_label'] ?? '') ?>">
              </div>
              <div class="col-md-4">
                <label class="form-label">Agent number</label>
                <input type="text" name="badge_number" class="form-control" value="<?= h($settings['trust_badge_number'] ?? '') ?>">
              </div>
              <div class="col-12">
                <label class="form-label">Message</label>
                <input type="text" name="message" class="form-control" value="<?= h($settings['trust_message'] ?? '') ?>">
              </div>
              <div class="col-12 d-flex justify-content-end">
                <button class="btn btn-brand"><i class="bi bi-save me-1"></i>Save Trust Bar</button>
              </div>
            </form>
          </div>
        </div>
      </div>

      <!-- HOW IT WORKS -->
      <div class="tab-pane fade" id="tab-steps">
        <div class="card">
          <div class="card-header">"Highest Rebate. We work for you. No Rebate, No Fee." Section</div>
          <div class="card-body">
            <form method="post">
              <input type="hidden" name="form_type" value="how_it_works">
              <input type="hidden" name="tab" value="tab-steps">
              <div class="row g-3 mb-3">
                <div class="col-12">
                  <label class="form-label">Section heading</label>
                  <input type="text" name="heading" class="form-control" value="<?= h($settings['how_it_works_heading'] ?? '') ?>">
                </div>
                <div class="col-12">
                  <label class="form-label">Intro paragraph</label>
                  <textarea name="intro" class="form-control" rows="3"><?= h($settings['how_it_works_intro'] ?? '') ?></textarea>
                </div>
              </div>
              <hr class="section-divider">
              <label class="form-label">Steps (shown as 1–<?= count($steps) ?> in the "How it Works" band)</label>
              <?php foreach ($steps as $i => $step): ?>
                <div class="d-flex gap-3 align-items-start mb-3 p-3 rounded-3" style="background:var(--etr-bg);">
                  <div class="avatar-circle flex-shrink-0" style="background:var(--etr-navy);"><?= $i + 1 ?></div>
                  <div class="flex-grow-1 row g-2">
                    <div class="col-md-4">
                      <input type="text" name="step_title[]" class="form-control form-control-sm" value="<?= h($step['title']) ?>" placeholder="Step title">
                    </div>
                    <div class="col-md-8">
                      <input type="text" name="step_description[]" class="form-control form-control-sm" value="<?= h($step['description']) ?>" placeholder="Step description">
                    </div>
                  </div>
                </div>
              <?php endforeach; ?>
              <div class="d-flex justify-content-end">
                <button class="btn btn-brand"><i class="bi bi-save me-1"></i>Save Steps</button>
              </div>
            </form>
          </div>
        </div>
      </div>

      <!-- STATS BAND -->
      <div class="tab-pane fade" id="tab-stats">
        <div class="card">
          <div class="card-header">"The Market Leading Tax Rebate Service" Band</div>
          <div class="card-body">
            <form method="post">
              <input type="hidden" name="form_type" value="stats">
              <input type="hidden" name="tab" value="tab-stats">
              <div class="mb-3">
                <label class="form-label">Section heading</label>
                <input type="text" name="heading" class="form-control" value="<?= h($settings['stats_heading'] ?? '') ?>">
              </div>
              <hr class="section-divider">
              <div class="row g-3">
                <?php foreach ($statItems as $item): ?>
                <div class="col-md-4">
                  <div class="border rounded-3 p-3 h-100">
                    <label class="form-label small text-muted">Bootstrap icon name</label>
                    <div class="input-group input-group-sm mb-2">
                      <span class="input-group-text"><i class="bi bi-<?= h($item['icon']) ?>"></i></span>
                      <input type="text" name="stat_icon[]" class="form-control" value="<?= h($item['icon']) ?>">
                    </div>
                    <label class="form-label small text-muted">Headline</label>
                    <input type="text" name="stat_value[]" class="form-control form-control-sm mb-2" value="<?= h($item['value']) ?>">
                    <label class="form-label small text-muted">Description</label>
                    <textarea name="stat_description[]" class="form-control form-control-sm" rows="2"><?= h($item['description']) ?></textarea>
                  </div>
                </div>
                <?php endforeach; ?>
              </div>
              <div class="d-flex justify-content-end mt-3">
                <button class="btn btn-brand"><i class="bi bi-save me-1"></i>Save Stats Band</button>
              </div>
            </form>
          </div>
        </div>
      </div>

      <!-- OUR STORY -->
      <div class="tab-pane fade" id="tab-story">
        <div class="card">
          <div class="card-header">"Helping People Claim Tax Back" Section</div>
          <div class="card-body">
            <form method="post" class="row g-3">
              <input type="hidden" name="form_type" value="our_story">
              <input type="hidden" name="tab" value="tab-story">
              <div class="col-md-4">
                <label class="form-label">Eyebrow text</label>
                <input type="text" name="eyebrow" class="form-control" value="<?= h($settings['story_eyebrow'] ?? '') ?>">
              </div>
              <div class="col-md-8">
                <label class="form-label">Heading</label>
                <input type="text" name="heading" class="form-control" value="<?= h($settings['story_heading'] ?? '') ?>">
              </div>
              <div class="col-12">
                <label class="form-label">Body text</label>
                <textarea name="body" class="form-control" rows="5"><?= h($settings['story_body'] ?? '') ?></textarea>
              </div>
              <div class="col-12 d-flex justify-content-end">
                <button class="btn btn-brand"><i class="bi bi-save me-1"></i>Save Our Story</button>
              </div>
            </form>
          </div>
        </div>
      </div>

      <!-- CONTACT -->
      <div class="tab-pane fade" id="tab-contact">
        <div class="card">
          <div class="card-header">Contact Details</div>
          <div class="card-body">
            <form method="post" class="row g-3">
              <input type="hidden" name="form_type" value="contact">
              <input type="hidden" name="tab" value="tab-contact">
              <div class="col-md-6">
                <label class="form-label">Phone 1</label>
                <input type="text" name="phone_1" class="form-control" value="<?= h($settings['contact_phone_1'] ?? '') ?>">
              </div>
              <div class="col-md-6">
                <label class="form-label">Phone 2</label>
                <input type="text" name="phone_2" class="form-control" value="<?= h($settings['contact_phone_2'] ?? '') ?>">
              </div>
              <div class="col-md-6">
                <label class="form-label">Email</label>
                <input type="email" name="email" class="form-control" value="<?= h($settings['contact_email'] ?? '') ?>">
              </div>
              <div class="col-md-6">
                <label class="form-label">Registered address</label>
                <input type="text" name="address" class="form-control" value="<?= h($settings['contact_address'] ?? '') ?>">
              </div>
              <div class="col-12 d-flex justify-content-end">
                <button class="btn btn-brand"><i class="bi bi-save me-1"></i>Save Contact Details</button>
              </div>
            </form>
          </div>
        </div>
      </div>

      <!-- CTA BANNER -->
      <div class="tab-pane fade" id="tab-cta">
        <div class="card">
          <div class="card-header">"Get your tax back" Banner (shown near the bottom of most pages)</div>
          <div class="card-body">
            <form method="post" class="row g-3">
              <input type="hidden" name="form_type" value="cta_banner">
              <input type="hidden" name="tab" value="tab-cta">
              <div class="col-md-6">
                <label class="form-label">Heading</label>
                <input type="text" name="heading" class="form-control" value="<?= h($settings['cta_heading'] ?? '') ?>">
              </div>
              <div class="col-md-6">
                <label class="form-label">Button text</label>
                <input type="text" name="button_text" class="form-control" value="<?= h($settings['cta_button_text'] ?? '') ?>">
              </div>
              <div class="col-12 d-flex justify-content-end">
                <button class="btn btn-brand"><i class="bi bi-save me-1"></i>Save CTA Banner</button>
              </div>
            </form>
          </div>
        </div>
      </div>

      <!-- FOOTER -->
      <div class="tab-pane fade" id="tab-footer">
        <div class="card">
          <div class="card-header">Footer &amp; Legal</div>
          <div class="card-body">
            <form method="post" class="row g-3">
              <input type="hidden" name="form_type" value="footer">
              <input type="hidden" name="tab" value="tab-footer">
              <div class="col-md-4">
                <label class="form-label">CRO number</label>
                <input type="text" name="cro" class="form-control" value="<?= h($settings['footer_cro'] ?? '') ?>">
              </div>
              <div class="col-md-4">
                <label class="form-label">VAT number</label>
                <input type="text" name="vat" class="form-control" value="<?= h($settings['footer_vat'] ?? '') ?>">
              </div>
              <div class="col-md-4">
                <label class="form-label">Copyright line</label>
                <input type="text" name="copyright" class="form-control" value="<?= h($settings['footer_copyright'] ?? '') ?>">
              </div>
              <div class="col-12 d-flex justify-content-end">
                <button class="btn btn-brand"><i class="bi bi-save me-1"></i>Save Footer</button>
              </div>
            </form>
          </div>
        </div>
      </div>

    </div>
  </div>
</div>
<?php require __DIR__ . '/includes/footer.php'; ?>
