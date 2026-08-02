<?php
require __DIR__ . '/includes/config.php';
require_login();

$pageTitle = 'Form Elements';
$activeMenu = 'forms';
require __DIR__ . '/includes/header.php';
require __DIR__ . '/includes/sidebar.php';
?>
<div class="main-col">
  <?php require __DIR__ . '/includes/navbar.php'; ?>

  <div class="content-area">
    <div class="page-header">
      <div>
        <div class="breadcrumb-eyebrow">UI Components</div>
        <h1>Form Elements</h1>
      </div>
    </div>

    <div class="row g-3">
      <!-- Switches -->
      <div class="col-lg-6">
        <div class="card h-100">
          <div class="card-header"><i class="bi bi-toggle2-on me-1 text-teal"></i>Switches</div>
          <div class="card-body">
            <div class="form-check form-switch mb-3">
              <input class="form-check-input" type="checkbox" role="switch" id="switchEmail" checked>
              <label class="form-check-label" for="switchEmail">Email notifications for new applications</label>
            </div>
            <div class="form-check form-switch mb-3">
              <input class="form-check-input" type="checkbox" role="switch" id="switchSms">
              <label class="form-check-label" for="switchSms">SMS alerts for payment confirmations</label>
            </div>
            <div class="form-check form-switch mb-3">
              <input class="form-check-input" type="checkbox" role="switch" id="switchMaintenance">
              <label class="form-check-label" for="switchMaintenance">Maintenance mode for public site</label>
            </div>
            <div class="form-check form-switch">
              <input class="form-check-input" type="checkbox" role="switch" id="switchDisabled" disabled>
              <label class="form-check-label text-muted" for="switchDisabled">Two-factor login (disabled — set in Security)</label>
            </div>
          </div>
        </div>
      </div>

      <!-- Custom checkboxes / radios -->
      <div class="col-lg-6">
        <div class="card h-100">
          <div class="card-header"><i class="bi bi-check2-square me-1 text-teal"></i>Checkboxes &amp; Radios</div>
          <div class="card-body">
            <label class="form-label">Rebate categories to feature on homepage</label>
            <div class="form-check">
              <input class="form-check-input" type="checkbox" id="catPaye" checked>
              <label class="form-check-label" for="catPaye">PAYE Review</label>
            </div>
            <div class="form-check">
              <input class="form-check-input" type="checkbox" id="catMedical" checked>
              <label class="form-check-label" for="catMedical">Medical Expenses</label>
            </div>
            <div class="form-check">
              <input class="form-check-input" type="checkbox" id="catFlat">
              <label class="form-check-label" for="catFlat">Flat Rate Expenses</label>
            </div>

            <hr class="section-divider">

            <label class="form-label">Default rebate calculation method</label>
            <div class="form-check">
              <input class="form-check-input" type="radio" name="calcMethod" id="calcAuto" checked>
              <label class="form-check-label" for="calcAuto">Automatic (Revenue data)</label>
            </div>
            <div class="form-check">
              <input class="form-check-input" type="radio" name="calcMethod" id="calcManual">
              <label class="form-check-label" for="calcManual">Manual review by agent</label>
            </div>
          </div>
        </div>
      </div>

      <!-- Floating labels & input groups -->
      <div class="col-lg-6">
        <div class="card h-100">
          <div class="card-header"><i class="bi bi-input-cursor-text me-1 text-teal"></i>Floating Labels &amp; Input Groups</div>
          <div class="card-body">
            <div class="form-floating mb-3">
              <input type="text" class="form-control" id="floatCompany" placeholder="Company name" value="EIRE Tax Refunds">
              <label for="floatCompany">Company name</label>
            </div>
            <div class="form-floating mb-3">
              <select class="form-select" id="floatCounty">
                <option>Kildare</option>
                <option>Dublin</option>
                <option>Cork</option>
              </select>
              <label for="floatCounty">County</label>
            </div>
            <label class="form-label">Minimum rebate fee</label>
            <div class="input-group mb-3">
              <span class="input-group-text">€</span>
              <input type="number" class="form-control" value="25">
              <span class="input-group-text">.00</span>
            </div>
            <label class="form-label">Website URL</label>
            <div class="input-group">
              <span class="input-group-text"><i class="bi bi-globe"></i></span>
              <input type="text" class="form-control" value="irishtaxrebates.ie">
            </div>
          </div>
        </div>
      </div>

      <!-- Range, file upload, validation -->
      <div class="col-lg-6">
        <div class="card h-100">
          <div class="card-header"><i class="bi bi-sliders me-1 text-teal"></i>Range, Upload &amp; Validation</div>
          <div class="card-body">
            <label class="form-label d-flex justify-content-between">
              <span>Maximum agent fee (% + VAT)</span><span class="text-teal fw-semibold" id="feeRangeValue">10%</span>
            </label>
            <input type="range" class="form-range mb-3" min="0" max="25" value="10" oninput="document.getElementById('feeRangeValue').textContent = this.value + '%'">

            <label class="form-label">Upload homepage slider image</label>
            <input type="file" class="form-control mb-3">

            <form class="needs-validation" novalidate>
              <label class="form-label">Support email <span class="text-danger">*</span></label>
              <input type="email" class="form-control mb-1" required value="info@irishtaxrebates.ie">
              <div class="valid-feedback d-block small text-success"><i class="bi bi-check-circle me-1"></i>Looks good.</div>
            </form>
          </div>
        </div>
      </div>

      <!-- Full profile-style form -->
      <div class="col-12">
        <div class="card">
          <div class="card-header"><i class="bi bi-card-checklist me-1 text-teal"></i>Sample Settings Form</div>
          <div class="card-body">
            <form class="row g-3">
              <div class="col-md-4">
                <label class="form-label">Business name</label>
                <input type="text" class="form-control" value="EIRE Tax Refunds">
              </div>
              <div class="col-md-4">
                <label class="form-label">Tax agent number</label>
                <input type="text" class="form-control" value="66436K">
              </div>
              <div class="col-md-4">
                <label class="form-label">Turnaround time (days)</label>
                <input type="number" class="form-control" value="12">
              </div>
              <div class="col-md-8">
                <label class="form-label">Registered address</label>
                <input type="text" class="form-control" value="1 Leinster St., Athy, Co. Kildare, R14 K226">
              </div>
              <div class="col-md-4">
                <label class="form-label">Status</label>
                <select class="form-select">
                  <option selected>Active</option>
                  <option>Paused</option>
                </select>
              </div>
              <div class="col-12">
                <label class="form-label">Internal notes</label>
                <textarea class="form-control" rows="3" maxlength="300" data-char-count="#notesCount" placeholder="Notes visible only to admins…"></textarea>
                <div class="form-text text-end"><span id="notesCount">0</span>/300</div>
              </div>
              <div class="col-12 d-flex justify-content-end gap-2">
                <button type="button" class="btn btn-light">Cancel</button>
                <button type="submit" class="btn btn-brand">Save Settings</button>
              </div>
            </form>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>
<?php require __DIR__ . '/includes/footer.php'; ?>
