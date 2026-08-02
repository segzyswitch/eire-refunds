<?php
require __DIR__ . '/includes/config.php';
require_login();

$maritalStatuses = ['Married', 'Single', 'Civil Partnership', 'Separated', 'Divorced', 'Widowed', 'Single Parent'];
$rebateTypes = ['PAYE Review', 'Medical Expenses', 'Flat Rate Expenses', 'Marriage Tax Credit', 'Remote Working Relief', 'Rent Tax Credit', 'Dependent Relative Tax Credit'];
$statuses = ['New', 'Awaiting Agent Link', 'Processing', 'Paid', 'Not Due'];
$statusClasses = [
    'Paid' => 'status-paid', 'Processing' => 'status-processing', 'Awaiting Agent Link' => 'status-awaiting',
    'Not Due' => 'status-not-due', 'New' => 'status-draft',
];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $formType = $_POST['form_type'] ?? '';

    if ($formType === 'save_application') {
        $id = (int) ($_POST['id'] ?? 0);

        $dob = trim($_POST['date_of_birth'] ?? '');
        $data = [
            'first_name' => trim($_POST['first_name'] ?? ''),
            'last_name' => trim($_POST['last_name'] ?? ''),
            'maiden_name' => trim($_POST['maiden_name'] ?? '') ?: null,
            'email' => trim($_POST['email'] ?? ''),
            'phone_number' => trim($_POST['phone_number'] ?? ''),
            'whatsapp_number' => trim($_POST['whatsapp_number'] ?? ''),
            'occupation' => trim($_POST['occupation'] ?? ''),
            'pps_number' => trim($_POST['pps_number'] ?? ''),
            'marital_status' => $_POST['marital_status'] ?? $maritalStatuses[0],
            'date_of_birth' => $dob !== '' ? $dob : null,
            'address_one' => trim($_POST['address_one'] ?? ''),
            'address_two' => trim($_POST['address_two'] ?? ''),
            'county' => trim($_POST['county'] ?? ''),
            'eircode' => trim($_POST['eircode'] ?? '') ?: null,
            'promotion_code' => trim($_POST['promotion_code'] ?? '') ?: null,
            'rebate_type' => trim($_POST['rebate_type'] ?? '') ?: null,
            'rebate_amount' => (float) ($_POST['rebate_amount'] ?? 0),
            'status' => $_POST['status'] ?? $statuses[0],
        ];

        if ($data['first_name'] === '' || $data['last_name'] === '' || $data['email'] === '') {
            set_flash('error', 'First name, last name and email are required.');
        } elseif ($id > 0) {
            $stmt = itr_db()->prepare(
                'UPDATE applications SET first_name=:first_name, last_name=:last_name, maiden_name=:maiden_name,
                 email=:email, phone_number=:phone_number, whatsapp_number=:whatsapp_number, occupation=:occupation,
                 pps_number=:pps_number, marital_status=:marital_status, date_of_birth=:date_of_birth,
                 address_one=:address_one, address_two=:address_two, county=:county, eircode=:eircode,
                 promotion_code=:promotion_code, rebate_type=:rebate_type, rebate_amount=:rebate_amount, status=:status
                 WHERE id=:id'
            );
            $stmt->execute($data + ['id' => $id]);
            set_flash('success', 'Application updated successfully.');
        } else {
            $data['submitted_at'] = date('Y-m-d H:i:s');
            $stmt = itr_db()->prepare(
                'INSERT INTO applications (first_name, last_name, maiden_name, email, phone_number, whatsapp_number,
                 occupation, pps_number, marital_status, date_of_birth, address_one, address_two, county, eircode,
                 promotion_code, rebate_type, rebate_amount, status, submitted_at)
                 VALUES (:first_name, :last_name, :maiden_name, :email, :phone_number, :whatsapp_number, :occupation,
                 :pps_number, :marital_status, :date_of_birth, :address_one, :address_two, :county, :eircode,
                 :promotion_code, :rebate_type, :rebate_amount, :status, :submitted_at)'
            );
            $stmt->execute($data);
            set_flash('success', 'New application added successfully.');
        }
    }

    if ($formType === 'delete_application') {
        $stmt = itr_db()->prepare('DELETE FROM applications WHERE id = :id');
        $stmt->execute(['id' => (int) ($_POST['id'] ?? 0)]);
        set_flash('success', 'Application deleted.');
    }

    header('Location: ' . BASE_URL . '/tables.php');
    exit;
}

$applications = itr_db()->query('SELECT * FROM applications ORDER BY submitted_at DESC')->fetchAll();
$counts = itr_db()->query(
    "SELECT COUNT(*) AS total,
            SUM(status = 'Paid') AS paid,
            SUM(status IN ('New','Processing','Awaiting Agent Link')) AS in_progress
     FROM applications"
)->fetch();

$pageTitle = 'Applications Table';
$activeMenu = 'tables';
require __DIR__ . '/includes/header.php';
require __DIR__ . '/includes/sidebar.php';
?>
<div class="main-col">
  <?php require __DIR__ . '/includes/navbar.php'; ?>

  <div class="content-area">
    <div class="page-header">
      <div>
        <div class="breadcrumb-eyebrow">Analytics</div>
        <h1>Rebate Applications</h1>
        <p class="text-muted small mb-0">Every row here is a real submission of the 60-second form on the public site.</p>
      </div>
      <div class="d-flex gap-2">
        <button class="btn btn-outline-teal btn-sm"><i class="bi bi-filetype-csv me-1"></i>Export CSV</button>
        <button class="btn btn-brand btn-sm" data-bs-toggle="modal" data-bs-target="#applicationModal" onclick="openApplicationModal()">
          <i class="bi bi-plus-lg me-1"></i>New Application
        </button>
      </div>
    </div>

    <div class="row g-3 mb-3">
      <div class="col-sm-4">
        <div class="card kpi-card"><div class="kpi-icon teal"><i class="bi bi-people-fill"></i></div>
          <div><div class="kpi-value"><?= (int) $counts['total'] ?></div><div class="kpi-label">Total Applications</div></div></div>
      </div>
      <div class="col-sm-4">
        <div class="card kpi-card"><div class="kpi-icon green"><i class="bi bi-check-circle"></i></div>
          <div><div class="kpi-value"><?= (int) $counts['paid'] ?></div><div class="kpi-label">Paid</div></div></div>
      </div>
      <div class="col-sm-4">
        <div class="card kpi-card"><div class="kpi-icon orange"><i class="bi bi-hourglass-split"></i></div>
          <div><div class="kpi-value"><?= (int) $counts['in_progress'] ?></div><div class="kpi-label">New / In Progress</div></div></div>
      </div>
    </div>

    <div class="card">
      <div class="card-body">
        <div class="table-responsive">
          <table id="applicationsTable" class="table table-hover align-middle w-100">
            <thead>
              <tr>
                <th>ID</th>
                <th>Applicant</th>
                <th>County</th>
                <th>Rebate Type</th>
                <th>Amount</th>
                <th>Status</th>
                <th>Submitted</th>
                <th class="text-end">Actions</th>
              </tr>
            </thead>
            <tbody>
              <?php foreach ($applications as $a): ?>
              <?php $cls = $statusClasses[$a['status']] ?? 'status-draft'; ?>
              <tr>
                <td class="text-muted">#<?= h($a['id']) ?></td>
                <td>
                  <div class="fw-semibold"><?= h($a['first_name'] . ' ' . $a['last_name']) ?></div>
                  <div class="text-muted small"><?= h($a['email']) ?></div>
                </td>
                <td><?= h($a['county']) ?></td>
                <td><?= h($a['rebate_type'] ?: 'Unclassified') ?></td>
                <td>€<?= number_format((float) $a['rebate_amount'], 2) ?></td>
                <td><span class="status-pill <?= $cls ?>"><?= h($a['status']) ?></span></td>
                <td data-order="<?= h($a['submitted_at']) ?>"><?= date('d M Y', strtotime($a['submitted_at'])) ?></td>
                <td class="text-end">
                  <button class="btn btn-sm btn-light" title="View" data-bs-toggle="modal" data-bs-target="#viewApplicationModal"
                    onclick='openViewModal(<?= json_encode($a, JSON_HEX_APOS | JSON_HEX_QUOT) ?>)'>
                    <i class="bi bi-eye"></i>
                  </button>
                  <button class="btn btn-sm btn-light" title="Edit" data-bs-toggle="modal" data-bs-target="#applicationModal"
                    onclick='openApplicationModal(<?= json_encode($a, JSON_HEX_APOS | JSON_HEX_QUOT) ?>)'>
                    <i class="bi bi-pencil"></i>
                  </button>
                  <button class="btn btn-sm btn-light text-danger" title="Delete"
                    data-confirm-delete data-action="tables.php" data-name="<?= h($a['first_name'] . ' ' . $a['last_name']) ?>"
                    onclick="document.getElementById('deleteApplicationIdField').value='<?= h($a['id']) ?>'">
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
</div>

<!-- Add/Edit application modal -->
<div class="modal fade" id="applicationModal" tabindex="-1">
  <div class="modal-dialog modal-lg">
    <form class="modal-content" method="post">
      <div class="modal-header">
        <h5 class="modal-title" id="applicationModalTitle"><i class="bi bi-person-plus me-2 text-teal"></i>New Application</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
      </div>
      <div class="modal-body">
        <input type="hidden" name="form_type" value="save_application">
        <input type="hidden" name="id" id="app_id" value="0">

        <h6 class="text-muted text-uppercase small mb-3" style="letter-spacing:.05em;">About the Applicant</h6>
        <div class="row g-3 mb-3">
          <div class="col-md-4"><label class="form-label">First name</label><input type="text" name="first_name" id="app_first_name" class="form-control" required></div>
          <div class="col-md-4"><label class="form-label">Last name</label><input type="text" name="last_name" id="app_last_name" class="form-control" required></div>
          <div class="col-md-4"><label class="form-label">Maiden name</label><input type="text" name="maiden_name" id="app_maiden_name" class="form-control"></div>
          <div class="col-md-4"><label class="form-label">Email</label><input type="email" name="email" id="app_email" class="form-control" required></div>
          <div class="col-md-4"><label class="form-label">Phone number</label><input type="text" name="phone_number" id="app_phone_number" class="form-control"></div>
          <div class="col-md-4"><label class="form-label">WhatsApp number</label><input type="text" name="whatsapp_number" id="app_whatsapp_number" class="form-control"></div>
          <div class="col-md-4"><label class="form-label">Occupation</label><input type="text" name="occupation" id="app_occupation" class="form-control"></div>
          <div class="col-md-4"><label class="form-label">PPS number</label><input type="text" name="pps_number" id="app_pps_number" class="form-control"></div>
          <div class="col-md-4">
            <label class="form-label">Marital status</label>
            <select name="marital_status" id="app_marital_status" class="form-select">
              <?php foreach ($maritalStatuses as $m): ?><option value="<?= h($m) ?>"><?= h($m) ?></option><?php endforeach; ?>
            </select>
          </div>
          <div class="col-md-4"><label class="form-label">Date of birth</label><input type="date" name="date_of_birth" id="app_date_of_birth" class="form-control"></div>
        </div>

        <h6 class="text-muted text-uppercase small mb-3" style="letter-spacing:.05em;">Address</h6>
        <div class="row g-3 mb-3">
          <div class="col-md-6"><label class="form-label">Street address</label><input type="text" name="address_one" id="app_address_one" class="form-control"></div>
          <div class="col-md-6"><label class="form-label">Town/City</label><input type="text" name="address_two" id="app_address_two" class="form-control"></div>
          <div class="col-md-4"><label class="form-label">County</label><input type="text" name="county" id="app_county" class="form-control"></div>
          <div class="col-md-4"><label class="form-label">Eircode</label><input type="text" name="eircode" id="app_eircode" class="form-control"></div>
          <div class="col-md-4"><label class="form-label">Promotion code</label><input type="text" name="promotion_code" id="app_promotion_code" class="form-control"></div>
        </div>

        <h6 class="text-muted text-uppercase small mb-3" style="letter-spacing:.05em;">Review &amp; Rebate <span class="normal-case text-muted" style="text-transform:none;">(set by your team)</span></h6>
        <div class="row g-3">
          <div class="col-md-4">
            <label class="form-label">Rebate type</label>
            <select name="rebate_type" id="app_rebate_type" class="form-select">
              <option value="">Unclassified</option>
              <?php foreach ($rebateTypes as $t): ?><option value="<?= h($t) ?>"><?= h($t) ?></option><?php endforeach; ?>
            </select>
          </div>
          <div class="col-md-4"><label class="form-label">Rebate amount (€)</label><input type="number" step="0.01" min="0" name="rebate_amount" id="app_rebate_amount" class="form-control" value="0.00"></div>
          <div class="col-md-4">
            <label class="form-label">Status</label>
            <select name="status" id="app_status" class="form-select">
              <?php foreach ($statuses as $s): ?><option value="<?= h($s) ?>"><?= h($s) ?></option><?php endforeach; ?>
            </select>
          </div>
        </div>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-light" data-bs-dismiss="modal">Cancel</button>
        <button type="submit" class="btn btn-brand"><i class="bi bi-save me-1"></i>Save Application</button>
      </div>
    </form>
  </div>
</div>

<!-- View application modal (read-only) -->
<div class="modal fade" id="viewApplicationModal" tabindex="-1">
  <div class="modal-dialog modal-lg">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title"><i class="bi bi-eye me-2 text-teal"></i>Application Details</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
      </div>
      <div class="modal-body">
        <div class="row">
          <div class="col-md-6">
            <h6 class="text-muted text-uppercase small mb-2" style="letter-spacing:.05em;">Applicant</h6>
            <dl class="row small mb-4">
              <dt class="col-5 text-muted">Name</dt><dd class="col-7" id="view_name"></dd>
              <dt class="col-5 text-muted">Maiden name</dt><dd class="col-7" id="view_maiden_name"></dd>
              <dt class="col-5 text-muted">Email</dt><dd class="col-7" id="view_email"></dd>
              <dt class="col-5 text-muted">Phone</dt><dd class="col-7" id="view_phone_number"></dd>
              <dt class="col-5 text-muted">WhatsApp</dt><dd class="col-7" id="view_whatsapp_number"></dd>
              <dt class="col-5 text-muted">Occupation</dt><dd class="col-7" id="view_occupation"></dd>
              <dt class="col-5 text-muted">PPS number</dt><dd class="col-7" id="view_pps_number"></dd>
              <dt class="col-5 text-muted">Marital status</dt><dd class="col-7" id="view_marital_status"></dd>
              <dt class="col-5 text-muted">Date of birth</dt><dd class="col-7" id="view_date_of_birth"></dd>
            </dl>
          </div>
          <div class="col-md-6">
            <h6 class="text-muted text-uppercase small mb-2" style="letter-spacing:.05em;">Address &amp; Rebate</h6>
            <dl class="row small mb-3">
              <dt class="col-5 text-muted">Street</dt><dd class="col-7" id="view_address_one"></dd>
              <dt class="col-5 text-muted">Town/City</dt><dd class="col-7" id="view_address_two"></dd>
              <dt class="col-5 text-muted">County</dt><dd class="col-7" id="view_county"></dd>
              <dt class="col-5 text-muted">Eircode</dt><dd class="col-7" id="view_eircode"></dd>
              <dt class="col-5 text-muted">Promo code</dt><dd class="col-7" id="view_promotion_code"></dd>
              <dt class="col-5 text-muted">Rebate type</dt><dd class="col-7" id="view_rebate_type"></dd>
              <dt class="col-5 text-muted">Rebate amount</dt><dd class="col-7" id="view_rebate_amount"></dd>
              <dt class="col-5 text-muted">Status</dt><dd class="col-7" id="view_status"></dd>
              <dt class="col-5 text-muted">Submitted</dt><dd class="col-7" id="view_submitted_at"></dd>
            </dl>
            <h6 class="text-muted text-uppercase small mb-2" style="letter-spacing:.05em;">Signature</h6>
            <div id="view_signature_wrap" class="border rounded-3 p-2 text-center bg-light"></div>
          </div>
        </div>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-light" data-bs-dismiss="modal">Close</button>
      </div>
    </div>
  </div>
</div>

<!-- Shared delete confirmation modal -->
<div class="modal fade" id="confirmDeleteModal" tabindex="-1">
  <div class="modal-dialog">
    <form class="modal-content" method="post">
      <div class="modal-header">
        <h5 class="modal-title"><i class="bi bi-exclamation-triangle text-danger me-2"></i>Delete Application?</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
      </div>
      <div class="modal-body">
        <input type="hidden" name="form_type" value="delete_application">
        <input type="hidden" name="id" id="deleteApplicationIdField" value="0">
        Are you sure you want to delete the application for <strong data-confirm-name>this applicant</strong>? This cannot be undone.
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-light" data-bs-dismiss="modal">Cancel</button>
        <button type="submit" class="btn btn-danger">Yes, Delete</button>
      </div>
    </form>
  </div>
</div>

<?php
$extraScripts = '<script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
<script src="https://cdn.datatables.net/1.13.8/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/1.13.8/js/dataTables.bootstrap5.min.js"></script>
<script>
$(function () {
  $("#applicationsTable").DataTable({
    pageLength: 8,
    lengthMenu: [5, 8, 25, 50],
    order: [[6, "desc"]],
    language: { search: "", searchPlaceholder: "Search applications…" }
  });
});

function openApplicationModal(data) {
  data = data || {};
  document.getElementById("applicationModalTitle").innerHTML = data.id
    ? \'<i class="bi bi-pencil me-2 text-teal"></i>Edit Application\'
    : \'<i class="bi bi-person-plus me-2 text-teal"></i>New Application\';
  document.getElementById("app_id").value = data.id || 0;
  document.getElementById("app_first_name").value = data.first_name || "";
  document.getElementById("app_last_name").value = data.last_name || "";
  document.getElementById("app_maiden_name").value = data.maiden_name || "";
  document.getElementById("app_email").value = data.email || "";
  document.getElementById("app_phone_number").value = data.phone_number || "";
  document.getElementById("app_whatsapp_number").value = data.whatsapp_number || "";
  document.getElementById("app_occupation").value = data.occupation || "";
  document.getElementById("app_pps_number").value = data.pps_number || "";
  document.getElementById("app_marital_status").value = data.marital_status || "Single";
  document.getElementById("app_date_of_birth").value = data.date_of_birth || "";
  document.getElementById("app_address_one").value = data.address_one || "";
  document.getElementById("app_address_two").value = data.address_two || "";
  document.getElementById("app_county").value = data.county || "";
  document.getElementById("app_eircode").value = data.eircode || "";
  document.getElementById("app_promotion_code").value = data.promotion_code || "";
  document.getElementById("app_rebate_type").value = data.rebate_type || "";
  document.getElementById("app_rebate_amount").value = data.rebate_amount || "0.00";
  document.getElementById("app_status").value = data.status || "New";
}

function openViewModal(data) {
  document.getElementById("view_name").textContent = (data.first_name + " " + data.last_name).trim();
  document.getElementById("view_maiden_name").textContent = data.maiden_name || "—";
  document.getElementById("view_email").textContent = data.email;
  document.getElementById("view_phone_number").textContent = data.phone_number || "—";
  document.getElementById("view_whatsapp_number").textContent = data.whatsapp_number || "—";
  document.getElementById("view_occupation").textContent = data.occupation || "—";
  document.getElementById("view_pps_number").textContent = data.pps_number || "—";
  document.getElementById("view_marital_status").textContent = data.marital_status || "—";
  document.getElementById("view_date_of_birth").textContent = data.date_of_birth || "—";
  document.getElementById("view_address_one").textContent = data.address_one || "—";
  document.getElementById("view_address_two").textContent = data.address_two || "—";
  document.getElementById("view_county").textContent = data.county || "—";
  document.getElementById("view_eircode").textContent = data.eircode || "—";
  document.getElementById("view_promotion_code").textContent = data.promotion_code || "—";
  document.getElementById("view_rebate_type").textContent = data.rebate_type || "Unclassified";
  document.getElementById("view_rebate_amount").textContent = "€" + parseFloat(data.rebate_amount || 0).toFixed(2);
  document.getElementById("view_status").textContent = data.status;
  document.getElementById("view_submitted_at").textContent = data.submitted_at;

  const wrap = document.getElementById("view_signature_wrap");
  if (data.signature && data.signature.indexOf("data:image") === 0) {
    wrap.innerHTML = \'<img src="\' + data.signature + \'" style="max-height:70px;">\';
  } else if (data.signature) {
    wrap.innerHTML = \'<span class="fst-italic">\' + data.signature + \'</span>\';
  } else {
    wrap.innerHTML = \'<span class="text-muted small">No signature on file</span>\';
  }
}
</script>';
require __DIR__ . '/includes/footer.php';
