<?php
/**
 * inc/header.php
 * Expects (optional):
 *   $page_title  - <title> text
 *   $active      - which nav key is highlighted: home|rent|top|medical|flat|about|faqs|tips
 */
require_once __DIR__ . '/site-data.php'; // itr_setting(), itr_sliders(), itr_faqs_grouped(), etc.

if (!isset($page_title)) { $page_title = 'EIRE Tax Refunds | Claim Your Tax Back'; }
if (!isset($active)) { $active = ''; }

function itr_nav_class($key, $active) {
  return 'nav-link' . ($key === $active ? ' active' : '');
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title><?php echo htmlspecialchars($page_title); ?></title>

<!-- Bootstrap 5 -->
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css">
<!-- Bootstrap Icons (used for the small inline icons throughout the site) -->
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css">
<!-- Site font: Inter — compact, clean, reads well at small sizes (nav, buttons) -->
<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
<!-- Site theme -->
<link rel="stylesheet" href="assets/css/style.css">
<!-- X-con -->
<link rel="icon" type="image/x-icon" href="assets/images/icon.png">

</head>
<body style="padding-top:65px;">

<header>
  <nav class="navbar navbar-expand-xl navbar-light bg-white itr-navbar fixed-top">
    <div class="container">
      <a class="navbar-brand p-0" href="index.php">
        <img src="assets/images/logo.png" height="45" alt="EIRE Tax Refunds" />
      </a>
      <button class="navbar-toggler border-0" type="button" data-bs-toggle="offcanvas" data-bs-target="#itrOffcanvas" aria-controls="itrOffcanvas" aria-label="Toggle navigation">
        <span class="navbar-toggler-icon"></span>
      </button>

      <div class="offcanvas offcanvas-start offcanvas-xl itr-offcanvas" tabindex="-1" id="itrOffcanvas" aria-labelledby="itrOffcanvasLabel" style="max-width: 80%;">
        <div class="offcanvas-header">
          <a href="index.php">
            <img src="assets/images/logo.png" height="45" alt="EIRE Tax Refunds" />
          </a>
          <!-- <button type="button" class="btn-close" data-bs-dismiss="offcanvas" data-bs-target="#itrOffcanvas" aria-label="Close"></button> -->
        </div>
        <div class="offcanvas-body overflow-hidden">
          <ul class="navbar-nav mx-xl-auto">
            <?php include 'inc/nav-links.php'; ?>
          </ul>
          <a href="index.php#apply" class="btn btn-itr-primary d-xl-none w-100 text-center mt-3">Apply Now</a>
        </div>
      </div>

      <a href="index.php#apply" class="btn btn-itr-primary d-none d-xl-inline-block">Apply Now</a>
    </div>
  </nav>
</header>
