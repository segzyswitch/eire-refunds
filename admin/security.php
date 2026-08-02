<?php
require __DIR__ . '/includes/config.php';
require_login();

$stmt = itr_db()->prepare('SELECT * FROM users WHERE id = :id LIMIT 1');
$stmt->execute(['id' => $_SESSION['admin']['id']]);
$currentUser = $stmt->fetch();

if (!$currentUser) {
    // Session points at a user that no longer exists — force re-login.
    header('Location: ' . BASE_URL . '/logout.php');
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $formType = $_POST['form_type'] ?? '';

    if ($formType === 'profile') {
        $newUsername = trim($_POST['username'] ?? '');
        $newName = trim($_POST['name'] ?? '');
        $newEmail = trim($_POST['email'] ?? '');

        $check = itr_db()->prepare('SELECT COUNT(*) FROM users WHERE username = :username AND id != :id');
        $check->execute(['username' => $newUsername, 'id' => $currentUser['id']]);
        $taken = (bool) $check->fetchColumn();

        if ($newUsername === '' || $newName === '' || $newEmail === '') {
            set_flash('error', 'Please fill in username, name and email.');
        } elseif ($taken) {
            set_flash('error', 'That username is already in use.');
        } else {
            $update = itr_db()->prepare('UPDATE users SET username = :username, name = :name, email = :email WHERE id = :id');
            $update->execute(['username' => $newUsername, 'name' => $newName, 'email' => $newEmail, 'id' => $currentUser['id']]);

            $_SESSION['admin_user'] = $newUsername;
            $_SESSION['admin']['name'] = $newName;
            $_SESSION['admin']['email'] = $newEmail;
            set_flash('success', 'Profile details updated successfully.');
        }
    }

    if ($formType === 'password') {
        $current = $_POST['current_password'] ?? '';
        $new = $_POST['new_password'] ?? '';
        $confirm = $_POST['confirm_password'] ?? '';

        if (!password_verify($current, $currentUser['password_hash'])) {
            set_flash('error', 'Your current password is incorrect.');
        } elseif (strlen($new) < 8) {
            set_flash('error', 'New password must be at least 8 characters long.');
        } elseif ($new !== $confirm) {
            set_flash('error', 'New password and confirmation do not match.');
        } else {
            $update = itr_db()->prepare('UPDATE users SET password_hash = :hash WHERE id = :id');
            $update->execute(['hash' => password_hash($new, PASSWORD_BCRYPT), 'id' => $currentUser['id']]);
            set_flash('success', 'Password changed successfully.');
        }
    }

    if ($formType === 'two_factor') {
        $update = itr_db()->prepare('UPDATE users SET two_factor = :tf WHERE id = :id');
        $update->execute(['tf' => isset($_POST['two_factor']) ? 1 : 0, 'id' => $currentUser['id']]);
        set_flash('success', 'Two-factor authentication preference saved.');
    }

    header('Location: ' . BASE_URL . '/security.php');
    exit;
}

$loginActivity = itr_db()->prepare('SELECT * FROM login_activity WHERE user_id = :id ORDER BY created_at DESC LIMIT 5');
$loginActivity->execute(['id' => $currentUser['id']]);
$loginActivity = $loginActivity->fetchAll();

$pageTitle = 'Security';
$activeMenu = 'security';
require __DIR__ . '/includes/header.php';
require __DIR__ . '/includes/sidebar.php';
?>
<div class="main-col">
  <?php require __DIR__ . '/includes/navbar.php'; ?>

  <div class="content-area">
    <div class="page-header">
      <div>
        <div class="breadcrumb-eyebrow">Account</div>
        <h1>Security</h1>
      </div>
    </div>

    <div class="row g-3">
      <div class="col-lg-6">
        <div class="card mb-3">
          <div class="card-header"><i class="bi bi-person-badge me-1 text-teal"></i>Profile &amp; Username</div>
          <div class="card-body">
            <form method="post">
              <input type="hidden" name="form_type" value="profile">
              <div class="mb-3">
                <label class="form-label">Full name</label>
                <input type="text" name="name" class="form-control" value="<?= h($currentUser['name']) ?>" required>
              </div>
              <div class="mb-3">
                <label class="form-label">Username</label>
                <div class="input-group">
                  <span class="input-group-text"><i class="bi bi-at"></i></span>
                  <input type="text" name="username" class="form-control" value="<?= h($currentUser['username']) ?>" required>
                </div>
              </div>
              <div class="mb-3">
                <label class="form-label">Email address</label>
                <input type="email" name="email" class="form-control" value="<?= h($currentUser['email']) ?>" required>
              </div>
              <button type="submit" class="btn btn-brand"><i class="bi bi-save me-1"></i>Save Profile</button>
            </form>
          </div>
        </div>

        <div class="card">
          <div class="card-header"><i class="bi bi-shield-check me-1 text-teal"></i>Two-Factor Authentication</div>
          <div class="card-body">
            <form method="post">
              <input type="hidden" name="form_type" value="two_factor">
              <div class="form-check form-switch mb-2">
                <input class="form-check-input" type="checkbox" role="switch" id="twoFactorSwitch" name="two_factor" onchange="this.form.requestSubmit()" <?= !empty($currentUser['two_factor']) ? 'checked' : '' ?>>
                <label class="form-check-label" for="twoFactorSwitch">Require a one-time code by email when logging in</label>
              </div>
              <p class="text-muted small mb-0">Adds an extra verification step. We recommend keeping this on for any account that can edit site content.</p>
            </form>
          </div>
        </div>
      </div>

      <div class="col-lg-6">
        <div class="card mb-3">
          <div class="card-header"><i class="bi bi-key me-1 text-teal"></i>Change Password</div>
          <div class="card-body">
            <form method="post">
              <input type="hidden" name="form_type" value="password">
              <div class="mb-3">
                <label class="form-label">Current password</label>
                <div class="input-group">
                  <input type="password" name="current_password" id="current_password" class="form-control" required>
                  <button class="btn btn-outline-secondary" type="button" data-toggle-password="current_password"><i class="bi bi-eye"></i></button>
                </div>
              </div>
              <div class="mb-2">
                <label class="form-label">New password</label>
                <div class="input-group">
                  <input type="password" name="new_password" id="new_password" class="form-control" required minlength="8">
                  <button class="btn btn-outline-secondary" type="button" data-toggle-password="new_password"><i class="bi bi-eye"></i></button>
                </div>
              </div>
              <div class="mb-3">
                <div class="progress" style="height:6px;">
                  <div class="progress-bar" id="passwordStrengthBar" role="progressbar" style="width:0%;"></div>
                </div>
                <div class="form-text" id="passwordStrengthLabel"></div>
              </div>
              <div class="mb-3">
                <label class="form-label">Confirm new password</label>
                <input type="password" name="confirm_password" class="form-control" required minlength="8">
              </div>
              <ul class="text-muted small mb-3 ps-3">
                <li>At least 8 characters</li>
                <li>Mix of upper/lowercase, numbers and symbols recommended</li>
              </ul>
              <button type="submit" class="btn btn-teal"><i class="bi bi-shield-lock me-1"></i>Update Password</button>
            </form>
          </div>
        </div>

        <div class="card">
          <div class="card-header"><i class="bi bi-clock-history me-1 text-teal"></i>Recent Login Activity</div>
          <ul class="list-group list-group-flush">
            <li class="list-group-item d-flex justify-content-between align-items-center small">
              <span><i class="bi bi-laptop me-2 text-muted"></i>Current session (<?= h($currentUser['username']) ?>)</span>
              <span class="text-success">Active now</span>
            </li>
            <?php foreach ($loginActivity as $log): ?>
            <li class="list-group-item d-flex justify-content-between align-items-center small">
              <span><i class="bi bi-clock-history me-2 text-muted"></i><?= h($log['device']) ?> — <?= h($log['location']) ?></span>
              <span class="text-muted"><?= date('d M, H:i', strtotime($log['created_at'])) ?></span>
            </li>
            <?php endforeach; ?>
            <?php if (empty($loginActivity)): ?>
            <li class="list-group-item text-muted small">No previous login activity recorded yet.</li>
            <?php endif; ?>
          </ul>
        </div>
      </div>
    </div>
  </div>
</div>
<?php require __DIR__ . '/includes/footer.php'; ?>
