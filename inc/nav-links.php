<?php
/**
 * inc/nav-links.php
 * The site's nav <li> items, shared between the inline desktop navbar
 * and the mobile offcanvas panel (both include this file — nothing here
 * should assume which one it's rendering inside).
 * Expects $active to already be set by the including page (via header.php).
 */
?>
<li class="nav-item"><a class="<?php echo itr_nav_class('rent', $active); ?>" href="rent-tax-credit.php">RENT TAX CREDIT</a></li>
<li class="nav-item"><a class="<?php echo itr_nav_class('top', $active); ?>" href="top-rebates.php">TOP REBATES</a></li>
<li class="nav-item"><a class="<?php echo itr_nav_class('medical', $active); ?>" href="medical-expenses.php">MEDICAL EXPENSES</a></li>
<li class="nav-item"><a class="<?php echo itr_nav_class('flat', $active); ?>" href="flat-rate-expenses.php">FLAT RATE EXPENSES</a></li>
<li class="nav-item"><a class="<?php echo itr_nav_class('about', $active); ?>" href="about-us.php">ABOUT US</a></li>
<li class="nav-item"><a class="<?php echo itr_nav_class('faqs', $active); ?>" href="faqs.php">FAQS</a></li>
<li class="nav-item"><a class="<?php echo itr_nav_class('tips', $active); ?>" href="tax-tips.php">TAX TIPS</a></li>
