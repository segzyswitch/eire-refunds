<?php
/**
 * includes/header.php
 * Expects (optionally) from the calling page:
 *   $pageTitle   string  — browser tab title
 *   $activeMenu  string  — key matching a sidebar item, used to highlight it
 */
$pageTitle = $pageTitle ?? 'Dashboard';
$activeMenu = $activeMenu ?? '';
?>
<!doctype html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title><?= h($pageTitle) ?> · EIRE Tax Refunds Admin</title>

  <!-- Bootstrap 5 -->
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css">
  <!-- Bootstrap Icons -->
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css">
  <!-- DataTables (Bootstrap 5 theme) -->
  <link rel="stylesheet" href="https://cdn.datatables.net/1.13.8/css/dataTables.bootstrap5.min.css">
  <!-- Google font for a slightly less "default Bootstrap" feel -->
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">

  <link rel="stylesheet" href="<?= BASE_URL ?>/assets/css/style.css">
  <!-- X-con -->
  <link rel="icon" type="image/x-icon" href="assets/icon.png">
  <?php if (!empty($extraHead)) echo $extraHead; ?>
</head>
<body class="<?= isset($_COOKIE['sidebar_collapsed']) && $_COOKIE['sidebar_collapsed'] === '1' ? 'sidebar-collapsed' : '' ?>">
<div class="app-shell">
  <div class="sidebar-backdrop"></div>
