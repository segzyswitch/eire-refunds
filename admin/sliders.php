<?php
require __DIR__ . '/includes/config.php';
require_login();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  $formType = $_POST['form_type'] ?? '';

  if ($formType === 'save_slider') {
    $id = (int) ($_POST['id'] ?? 0);
    $data = [
      'title' => trim($_POST['title'] ?? ''),
      'subtitle' => trim($_POST['subtitle'] ?? ''),
      'badge_text' => trim($_POST['badge_text'] ?? 'Average Rebate'),
      'badge_value' => trim($_POST['badge_value'] ?? ''),
      'image' => trim($_POST['image'] ?? ''),
      'cta_label' => trim($_POST['cta_label'] ?? 'Apply Now'),
      'cta_url' => trim($_POST['cta_url'] ?? '#apply'),
      'status' => $_POST['status'] ?? 'Draft',
      'sort_order' => (int) ($_POST['sort_order'] ?? 1),
    ];

    try {
      // An uploaded file always wins over whatever's typed in the URL field.
      $uploadedPath = handle_image_upload('image_upload');
      if ($uploadedPath !== null) {
        $data['image'] = $uploadedPath;
      }
    } catch (RuntimeException $e) {
      set_flash('error', $e->getMessage());
      header('Location: ' . BASE_URL . '/sliders.php');
      exit;
    }

    if ($data['title'] === '') {
      set_flash('error', 'Slider title is required.');
    } elseif ($id > 0) {
      $stmt = itr_db()->prepare(
        'UPDATE sliders SET title=:title, subtitle=:subtitle, badge_text=:badge_text, badge_value=:badge_value,
                 image=:image, cta_label=:cta_label, cta_url=:cta_url, status=:status, sort_order=:sort_order WHERE id=:id'
      );
      $stmt->execute($data + ['id' => $id]);
      set_flash('success', 'Slider updated successfully.');
    } else {
      $stmt = itr_db()->prepare(
        'INSERT INTO sliders (title, subtitle, badge_text, badge_value, image, cta_label, cta_url, status, sort_order)
                 VALUES (:title, :subtitle, :badge_text, :badge_value, :image, :cta_label, :cta_url, :status, :sort_order)'
      );
      $stmt->execute($data);
      set_flash('success', 'New slider added successfully.');
    }
  }

  if ($formType === 'delete_slider') {
    $stmt = itr_db()->prepare('DELETE FROM sliders WHERE id = :id');
    $stmt->execute(['id' => (int) ($_POST['id'] ?? 0)]);
    set_flash('success', 'Slider deleted.');
  }

  if ($formType === 'toggle_status') {
    $stmt = itr_db()->prepare(
      "UPDATE sliders SET status = IF(status = 'Published', 'Draft', 'Published') WHERE id = :id"
    );
    $stmt->execute(['id' => (int) ($_POST['id'] ?? 0)]);
    set_flash('success', 'Slider status updated.');
  }

  header('Location: ' . BASE_URL . '/sliders.php');
  exit;
}

$sliders = itr_db()->query('SELECT * FROM sliders ORDER BY sort_order ASC')->fetchAll();

$pageTitle = 'Home Sliders';
$activeMenu = 'sliders';
require __DIR__ . '/includes/header.php';
require __DIR__ . '/includes/sidebar.php';
?>
<div class="main-col">
  <?php require __DIR__ . '/includes/navbar.php'; ?>

  <div class="content-area">
    <div class="page-header">
      <div>
        <div class="breadcrumb-eyebrow">Website Content</div>
        <h1>Home Page Sliders</h1>
        <p class="text-muted small mb-0">These feed the hero banner rotation on the public homepage, like the "Financially Supporting a Relative?" panel.</p>
      </div>
      <button class="btn btn-brand btn-sm" data-bs-toggle="modal" data-bs-target="#sliderModal" onclick="openSliderModal()">
        <i class="bi bi-plus-lg me-1"></i>Add Slider
      </button>
    </div>

    <div class="card">
      <div class="table-responsive">
        <table class="table align-middle mb-0">
          <thead>
            <tr>
              <th style="width:56px;">#</th>
              <th>Preview</th>
              <th>Heading</th>
              <th>Rebate Badge</th>
              <th>Status</th>
              <th>Updated</th>
              <th class="text-end">Actions</th>
            </tr>
          </thead>
          <tbody>
            <?php if (empty($sliders)): ?>
              <tr>
                <td colspan="7" class="text-center text-muted py-4">No sliders yet — click "Add Slider" to create your first homepage banner.</td>
              </tr>
            <?php endif; ?>
            <?php foreach ($sliders as $s): ?>
              <tr>
                <td class="text-muted"><?= h($s['sort_order']) ?></td>
                <td><img src="<?= h(public_asset_url($s['image'])) ?>" class="slider-thumb" alt="" onerror="this.src='https://placehold.co/90x60/e6f2f0/0b6e63?text=No+Image'"></td>
                <td>
                  <div class="fw-semibold"><?= h($s['title']) ?></div>
                  <div class="text-muted small text-truncate" style="max-width:280px;"><?= h($s['subtitle']) ?></div>
                </td>
                <td><span class="badge rounded-pill text-bg-light border">€<?= h($s['badge_value']) ?></span></td>
                <td>
                  <form method="post" class="d-inline">
                    <input type="hidden" name="form_type" value="toggle_status">
                    <input type="hidden" name="id" value="<?= h($s['id']) ?>">
                    <button type="submit" class="btn btn-sm border-0 p-0 status-pill <?= $s['status'] === 'Published' ? 'status-published' : 'status-draft' ?>">
                      <?= h($s['status']) ?>
                    </button>
                  </form>
                </td>
                <td class="text-muted small"><?= date('d M Y', strtotime($s['updated_at'])) ?></td>
                <td class="text-end">
                  <button class="btn btn-sm btn-light" title="Edit" data-bs-toggle="modal" data-bs-target="#sliderModal"
                    onclick='openSliderModal(<?= json_encode($s, JSON_HEX_APOS | JSON_HEX_QUOT) ?>)'>
                    <i class="bi bi-pencil"></i>
                  </button>
                  <button class="btn btn-sm btn-light text-danger" title="Delete"
                    data-confirm-delete data-action="sliders.php" data-name="<?= h($s['title']) ?>"
                    onclick="document.getElementById('deleteIdField').value='<?= h($s['id']) ?>'">
                    <i class="bi bi-trash"></i>
                  </button>
                </td>
              </tr>
            <?php endforeach; ?>
          </tbody>
        </table>
      </div>
    </div>
  </div>
</div>

<!-- Add/Edit slider modal -->
<div class="modal fade" id="sliderModal" tabindex="-1">
  <div class="modal-dialog modal-lg">
    <form class="modal-content" method="post" enctype="multipart/form-data">
      <div class="modal-header">
        <h5 class="modal-title" id="sliderModalTitle"><i class="bi bi-images me-2 text-teal"></i>Add Slider</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
      </div>
      <div class="modal-body">
        <input type="hidden" name="form_type" value="save_slider">
        <input type="hidden" name="id" id="slider_id" value="0">
        <div class="row g-3">
          <div class="col-md-7">
            <label class="form-label">Heading</label>
            <input type="text" name="title" id="slider_title" class="form-control" required placeholder="e.g. Financially Supporting a Relative?">
          </div>
          <div class="col-md-5">
            <label class="form-label">Status</label>
            <select name="status" id="slider_status" class="form-select">
              <option>Published</option>
              <option>Draft</option>
            </select>
          </div>
          <div class="col-12">
            <label class="form-label">Subtitle / supporting text</label>
            <textarea name="subtitle" id="slider_subtitle" class="form-control" rows="2"></textarea>
          </div>
          <div class="col-md-4">
            <label class="form-label">Badge label</label>
            <input type="text" name="badge_text" id="slider_badge_text" class="form-control" value="Average Rebate">
          </div>
          <div class="col-md-4">
            <label class="form-label">Rebate value (€)</label>
            <input type="text" name="badge_value" id="slider_badge_value" class="form-control" placeholder="1,092">
          </div>
          <div class="col-md-4">
            <label class="form-label">Sort order</label>
            <input type="number" name="sort_order" id="slider_sort_order" class="form-control" min="1" value="1">
          </div>
          <div class="col-md-8">
            <label class="form-label">Image URL</label>
            <input type="text" name="image" id="slider_image" class="form-control" placeholder="https://…">
          </div>
          <div class="col-md-4">
            <label class="form-label">Or upload image</label>
            <input type="file" name="image_upload" id="slider_image_upload" class="form-control" accept="image/jpeg,image/png,image/gif,image/webp">
            <div class="form-text">Uploading a file here saves it to <code>/public</code> and overrides the URL above.</div>
          </div>
          <div class="col-md-6">
            <label class="form-label">Button label</label>
            <input type="text" name="cta_label" id="slider_cta_label" class="form-control" value="Apply For My Rebate Now">
          </div>
          <div class="col-md-6">
            <label class="form-label">Button link</label>
            <input type="text" name="cta_url" id="slider_cta_url" class="form-control" value="#apply">
          </div>
        </div>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-light" data-bs-dismiss="modal">Cancel</button>
        <button type="submit" class="btn btn-brand"><i class="bi bi-save me-1"></i>Save Slider</button>
      </div>
    </form>
  </div>
</div>

<!-- Shared delete confirmation modal -->
<div class="modal fade" id="confirmDeleteModal" tabindex="-1">
  <div class="modal-dialog">
    <form class="modal-content" method="post">
      <div class="modal-header">
        <h5 class="modal-title"><i class="bi bi-exclamation-triangle text-danger me-2"></i>Delete Slider?</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
      </div>
      <div class="modal-body">
        <input type="hidden" name="form_type" value="delete_slider">
        <input type="hidden" name="id" id="deleteIdField" value="0">
        Are you sure you want to delete <strong data-confirm-name>this slider</strong>? This cannot be undone.
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-light" data-bs-dismiss="modal">Cancel</button>
        <button type="submit" class="btn btn-danger">Yes, Delete</button>
      </div>
    </form>
  </div>
</div>

<?php
$extraScripts = '<script>
function openSliderModal(data) {
  data = data || {};
  document.getElementById("sliderModalTitle").innerHTML = data.id
    ? \'<i class="bi bi-images me-2 text-teal"></i>Edit Slider\'
    : \'<i class="bi bi-images me-2 text-teal"></i>Add Slider\';
  document.getElementById("slider_id").value = data.id || 0;
  document.getElementById("slider_title").value = data.title || "";
  document.getElementById("slider_subtitle").value = data.subtitle || "";
  document.getElementById("slider_badge_text").value = data.badge_text || "Average Rebate";
  document.getElementById("slider_badge_value").value = data.badge_value || "";
  document.getElementById("slider_sort_order").value = data.sort_order || 1;
  document.getElementById("slider_image").value = data.image || "";
  document.getElementById("slider_cta_label").value = data.cta_label || "Apply For My Rebate Now";
  document.getElementById("slider_cta_url").value = data.cta_url || "#apply";
  document.getElementById("slider_status").value = data.status || "Published";
  document.getElementById("slider_image_upload").value = "";
}
</script>';
require __DIR__ . '/includes/footer.php';
