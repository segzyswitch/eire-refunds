<?php
require __DIR__ . '/includes/config.php';
header('Location: ' . BASE_URL . '/' . (!empty($_SESSION['admin_user']) ? 'dashboard.php' : 'login.php'));
exit;
