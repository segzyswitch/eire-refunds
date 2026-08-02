<?php
require __DIR__ . '/includes/config.php';
require_login();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $formType = $_POST['form_type'] ?? '';

    if ($formType === 'save_faq') {
        $id = (int) ($_POST['id'] ?? 0);
        $data = [
            'category' => trim($_POST['category'] ?? 'General'),
            'question' => trim($_POST['question'] ?? ''),
            'answer' => trim($_POST['answer'] ?? ''),
            'status' => $_POST['status'] ?? 'Draft',
            'sort_order' => (int) ($_POST['sort_order'] ?? 1),
        ];

        if ($data['question'] === '' || $data['answer'] === '') {
            set_flash('error', 'Both a question and an answer are required.');
        } elseif ($id > 0) {
            $stmt = itr_db()->prepare(
                'UPDATE faqs SET category=:category, question=:question, answer=:answer, status=:status, sort_order=:sort_order WHERE id=:id'
            );
            $stmt->execute($data + ['id' => $id]);
            set_flash('success', 'FAQ updated successfully.');
        } else {
            $stmt = itr_db()->prepare(
                'INSERT INTO faqs (category, question, answer, status, sort_order) VALUES (:category, :question, :answer, :status, :sort_order)'
            );
            $stmt->execute($data);
            set_flash('success', 'New FAQ added successfully.');
        }
    }

    if ($formType === 'delete_faq') {
        $stmt = itr_db()->prepare('DELETE FROM faqs WHERE id = :id');
        $stmt->execute(['id' => (int) ($_POST['id'] ?? 0)]);
        set_flash('success', 'FAQ deleted.');
    }

    if ($formType === 'toggle_status') {
        $stmt = itr_db()->prepare(
            "UPDATE faqs SET status = IF(status = 'Published', 'Draft', 'Published') WHERE id = :id"
        );
        $stmt->execute(['id' => (int) ($_POST['id'] ?? 0)]);
        set_flash('success', 'FAQ status updated.');
    }

    header('Location: ' . BASE_URL . '/faqs.php');
    exit;
}

$faqs = itr_db()->query('SELECT * FROM faqs ORDER BY sort_order ASC')->fetchAll();
$categories = itr_db()->query('SELECT DISTINCT category FROM faqs ORDER BY category')->fetchAll(PDO::FETCH_COLUMN);

$pageTitle = 'FAQs';
$activeMenu = 'faqs';
require __DIR__ . '/includes/header.php';
require __DIR__ . '/includes/sidebar.php';
?>
<div class="main-col">
  <?php require __DIR__ . '/includes/navbar.php'; ?>

  <div class="content-area">
    <div class="page-header">
      <div>
        <div class="breadcrumb-eyebrow">Website Content</div>
        <h1>Frequently Asked Questions</h1>
        <p class="text-muted small mb-0">Manage the Q&amp;A shown on the public FAQs page.</p>
      </div>
      <button class="btn btn-brand btn-sm" data-bs-toggle="modal" data-bs-target="#faqModal" onclick="openFaqModal()">
        <i class="bi bi-plus-lg me-1"></i>Add FAQ
      </button>
    </div>

    <div class="accordion" id="faqAccordion">
      <?php if (empty($faqs)): ?>
        <div class="card"><div class="card-body text-center text-muted py-4">No FAQs yet — click "Add FAQ" to create your first entry.</div></div>
      <?php endif; ?>
      <?php foreach ($faqs as $f): ?>
      <div class="accordion-item mb-2 border rounded-3 overflow-hidden">
        <h2 class="accordion-header d-flex align-items-stretch">
          <button class="accordion-button collapsed flex-grow-1" type="button" data-bs-toggle="collapse" data-bs-target="#faqCollapse<?= $f['id'] ?>">
            <span class="badge text-bg-light border me-2"><?= h($f['category']) ?></span>
            <?= h($f['question']) ?>
          </button>
          <div class="d-flex align-items-center gap-1 px-3 bg-white">
            <span class="status-pill <?= $f['status'] === 'Published' ? 'status-published' : 'status-draft' ?>"><?= h($f['status']) ?></span>
            <button class="btn btn-sm btn-light" title="Edit" data-bs-toggle="modal" data-bs-target="#faqModal"
              onclick='openFaqModal(<?= json_encode($f, JSON_HEX_APOS | JSON_HEX_QUOT) ?>)'><i class="bi bi-pencil"></i></button>
            <button class="btn btn-sm btn-light text-danger" title="Delete"
              data-confirm-delete data-action="faqs.php" data-name="<?= h(mb_strimwidth($f['question'], 0, 40, '…')) ?>"
              onclick="document.getElementById('deleteFaqIdField').value='<?= h($f['id']) ?>'">
              <i class="bi bi-trash"></i>
            </button>
          </div>
        </h2>
        <div id="faqCollapse<?= $f['id'] ?>" class="accordion-collapse collapse" data-bs-parent="#faqAccordion">
          <div class="accordion-body text-muted">
            <?= nl2br(h($f['answer'])) ?>
            <div class="small mt-2">Updated <?= date('d M Y, H:i', strtotime($f['updated_at'])) ?></div>
          </div>
        </div>
      </div>
      <?php endforeach; ?>
    </div>
  </div>
</div>

<!-- Add/Edit FAQ modal -->
<div class="modal fade" id="faqModal" tabindex="-1">
  <div class="modal-dialog modal-lg">
    <form class="modal-content" method="post">
      <div class="modal-header">
        <h5 class="modal-title" id="faqModalTitle"><i class="bi bi-question-circle me-2 text-teal"></i>Add FAQ</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
      </div>
      <div class="modal-body">
        <input type="hidden" name="form_type" value="save_faq">
        <input type="hidden" name="id" id="faq_id" value="0">
        <div class="row g-3">
          <div class="col-md-4">
            <label class="form-label">Category</label>
            <input type="text" name="category" id="faq_category" class="form-control" list="faqCategoryList" placeholder="General">
            <datalist id="faqCategoryList">
              <?php foreach ($categories as $c): ?><option value="<?= h($c) ?>"><?php endforeach; ?>
            </datalist>
          </div>
          <div class="col-md-4">
            <label class="form-label">Status</label>
            <select name="status" id="faq_status" class="form-select">
              <option>Published</option>
              <option>Draft</option>
            </select>
          </div>
          <div class="col-md-4">
            <label class="form-label">Sort order</label>
            <input type="number" name="sort_order" id="faq_sort_order" class="form-control" min="1" value="1">
          </div>
          <div class="col-12">
            <label class="form-label">Question</label>
            <input type="text" name="question" id="faq_question" class="form-control" required placeholder="e.g. How far back can I claim tax back?">
          </div>
          <div class="col-12">
            <label class="form-label">Answer</label>
            <textarea name="answer" id="faq_answer" class="form-control" rows="4" required></textarea>
          </div>
        </div>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-light" data-bs-dismiss="modal">Cancel</button>
        <button type="submit" class="btn btn-brand"><i class="bi bi-save me-1"></i>Save FAQ</button>
      </div>
    </form>
  </div>
</div>

<!-- Shared delete confirmation modal -->
<div class="modal fade" id="confirmDeleteModal" tabindex="-1">
  <div class="modal-dialog">
    <form class="modal-content" method="post">
      <div class="modal-header">
        <h5 class="modal-title"><i class="bi bi-exclamation-triangle text-danger me-2"></i>Delete FAQ?</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
      </div>
      <div class="modal-body">
        <input type="hidden" name="form_type" value="delete_faq">
        <input type="hidden" name="id" id="deleteFaqIdField" value="0">
        Are you sure you want to delete <strong data-confirm-name>this FAQ</strong>? This cannot be undone.
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
function openFaqModal(data) {
  data = data || {};
  document.getElementById("faqModalTitle").innerHTML = data.id
    ? \'<i class="bi bi-question-circle me-2 text-teal"></i>Edit FAQ\'
    : \'<i class="bi bi-question-circle me-2 text-teal"></i>Add FAQ\';
  document.getElementById("faq_id").value = data.id || 0;
  document.getElementById("faq_category").value = data.category || "General";
  document.getElementById("faq_status").value = data.status || "Published";
  document.getElementById("faq_sort_order").value = data.sort_order || 1;
  document.getElementById("faq_question").value = data.question || "";
  document.getElementById("faq_answer").value = data.answer || "";
}
</script>';
require __DIR__ . '/includes/footer.php';
