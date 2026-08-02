<?php
require __DIR__ . '/includes/config.php';

if (!empty($_SESSION['admin_user'])) {
    header('Location: ' . BASE_URL . '/dashboard.php');
    exit;
}

$error = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username'] ?? '');
    $password = $_POST['password'] ?? '';

    $stmt = itr_db()->prepare('SELECT * FROM users WHERE username = :username LIMIT 1');
    $stmt->execute(['username' => $username]);
    $match = $stmt->fetch();

    if ($match && password_verify($password, $match['password_hash'])) {
        $_SESSION['admin_user'] = $match['username'];
        $_SESSION['admin'] = [
            'id' => $match['id'],
            'name' => $match['name'],
            'email' => $match['email'],
            'role' => $match['role'],
        ];

        // Log this login for the Security page's "Recent Login Activity" list.
        $ua = $_SERVER['HTTP_USER_AGENT'] ?? '';
        $device = 'Unknown device';
        if (stripos($ua, 'Chrome') !== false) $device = 'Chrome';
        elseif (stripos($ua, 'Firefox') !== false) $device = 'Firefox';
        elseif (stripos($ua, 'Safari') !== false) $device = 'Safari';
        elseif (stripos($ua, 'Edge') !== false) $device = 'Edge';
        $platform = (stripos($ua, 'Windows') !== false) ? 'Windows'
            : ((stripos($ua, 'Mac') !== false) ? 'Mac' : ((stripos($ua, 'Android') !== false) ? 'Android' : ((stripos($ua, 'iPhone') !== false) ? 'iPhone' : 'a device')));

        $log = itr_db()->prepare('INSERT INTO login_activity (user_id, device, location, ip_address) VALUES (:uid, :device, :location, :ip)');
        $log->execute([
            'uid' => $match['id'],
            'device' => $device . ' on ' . $platform,
            'location' => 'Unknown location',
            'ip' => $_SERVER['REMOTE_ADDR'] ?? null,
        ]);

        header('Location: ' . BASE_URL . '/dashboard.php');
        exit;
    }
    $error = 'That username or password is incorrect. Please try again.';
}
?>
<!doctype html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Log In · EIRE Tax Refunds Admin</title>
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css">
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css">
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="assets/css/style.css">
  <!-- X-con -->
  <link rel="icon" type="image/x-icon" href="../assets/images/icon.png">
</head>
<body>
<div class="login-shell">
  <div class="card login-card">
    <div class="login-brand text-center">
      <div class="d-inline-flex align-items-center justify-content-center rounded-3 mb-3">
        <!-- <i class="bi bi-shield-check fs-3"></i> -->
        <img src="../assets/images/icon.png" alt="EIRE TAX" style="height:40px;">
      </div>
      <h4 class="mb-0 fw-bold">EIRE TAX REFUNDS</h4>
      <div class="small opacity-75">Admin Panel</div>
    </div>
    <div class="card-body p-4">
      <h5 class="fw-bold mb-1">Welcome back</h5>
      <p class="text-muted small mb-4">Sign in to manage rebate applications and site content.</p>

      <?php if ($error): ?>
        <div class="alert alert-danger py-2 small"><i class="bi bi-exclamation-triangle me-1"></i><?= h($error) ?></div>
      <?php endif; ?>

      <form method="post" novalidate class="mb-3">
        <div class="mb-3">
          <label class="form-label">Username</label>
          <div class="input-group">
            <span class="input-group-text"><i class="bi bi-person"></i></span>
            <input type="text" name="username" class="form-control" placeholder="Enter username" required autofocus value="<?= h($_POST['username'] ?? '') ?>">
          </div>
        </div>
        <div class="mb-3">
          <label class="form-label">Password</label>
          <div class="input-group">
            <span class="input-group-text"><i class="bi bi-lock"></i></span>
            <input type="password" name="password" id="loginPassword" class="form-control" placeholder="••••••••" required>
            <button class="btn btn-outline-secondary" type="button" data-toggle-password="loginPassword"><i class="bi bi-eye"></i></button>
          </div>
        </div>
        <div class="d-flex align-items-center justify-content-between mb-3">
          <div class="form-check">
            <input class="form-check-input" type="checkbox" id="rememberMe" name="remember">
            <label class="form-check-label small" for="rememberMe">Remember me</label>
          </div>
          <!-- <a href="#" class="small">Forgot password?</a> -->
        </div>
        <button type="submit" class="btn btn-brand w-100 py-2">Log In <i class="bi bi-arrow-right ms-1"></i></button>
      </form>

      <!-- <div class="text-center small text-muted mt-4 pt-3 border-top">
        Demo credentials: <strong>admin</strong> / <strong>admin123</strong>
      </div> -->
    </div>
  </div>
</div>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<script src="assets/js/script.js"></script>
</body>
</html>
