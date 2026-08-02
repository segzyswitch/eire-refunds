<footer class="itr-footer">
  <div class="container">
    <div class="row gy-4">
      <div class="col-md-4">
        <img src="assets/images/logo.png" height="35" class="mb-3" alt="EIRE Tax Refunds" />
        <h5>Explore</h5>
        <ul>
          <li><a href="index.php">Home &amp; Online Form</a></li>
          <li><a href="faqs.php">FAQs</a></li>
          <li><a href="index.php#how-it-works">How it Works</a></li>
          <li><a href="top-rebates.php">Top Rebates</a></li>
          <!-- <li><a href="top-rebates.php#working-from-home">Working From Home Tax Rebate</a></li>
          <li><a href="top-rebates.php#dependent-relative">Dependent Relative Tax Rebate</a></li>
          <li><a href="medical-expenses.php">Medical Expenses</a></li>
          <li><a href="flat-rate-expenses.php">Flat Rate Expenses</a></li>
          <li><a href="rent-tax-credit.php">Rent Tax Credit</a></li> -->
          <li><a href="about-us.php">About Us</a></li>
          <li><a href="tax-tips.php">Tax Tips</a></li>
        </ul>
      </div>

      <div class="col-md-4">
        <h5>Contact Us</h5>
        <p class="mb-2">Phone: <?php echo htmlspecialchars(itr_setting('contact_phone_1', '059-8634 794')); ?><br><?php echo htmlspecialchars(itr_setting('contact_phone_2', '01-6755 010')); ?></p>
        <p class="mb-2">Email: <a href="mailto:<?php echo htmlspecialchars(itr_setting('contact_email', 'info@irishtaxrebates.ie')); ?>"><?php echo htmlspecialchars(itr_setting('contact_email', 'info@irishtaxrebates.ie')); ?></a></p>
        <p><?php echo nl2br(htmlspecialchars(itr_setting('contact_address', 'EIRE Tax Refunds, MB Tax Group, 1 Leinster St., Athy, Co. Kildare, Ireland, R14 K226'))); ?></p>
      </div>

      <div class="col-md-4">
        <h5>Also from the MB Tax Group</h5>
        <p class="mb-3">
          <span class="badge badge-tax-return px-2 py-2" style="border-radius: 0.375rem 0 0 0.375rem;">Tax Return</span><span class="badge badge-plus px-2 py-2" style="border-radius: 0 0.375rem 0.375rem 0;">Plus</span>
        </p>
        <p class="fw-bold text-white mb-1">Tax Returns Made Simple</p>
        <p>Take the stress out of tax returns with our simple, fast, accurate service from just &euro;299.</p>
        <a href="#"><i class="bi bi-box-arrow-up-right me-1"></i>Visit Tax Return Plus</a>
      </div>
    </div>

    <div class="social-icons mt-4">
      <a href="#"><i class="bi bi-facebook"></i></a>
      <a href="#"><i class="bi bi-instagram"></i></a>
      <a href="#"><i class="bi bi-twitter-x"></i></a>
      <a href="#"><i class="bi bi-linkedin"></i></a>
      <a href="#"><i class="bi bi-youtube"></i></a>
    </div>

    <div class="itr-footer-bottom d-flex flex-wrap justify-content-between">
      <div>
        CRO:<?php echo htmlspecialchars(itr_setting('footer_cro', '473739')); ?>&nbsp; &nbsp;VAT:<?php echo htmlspecialchars(itr_setting('footer_vat', '9717017R')); ?>&nbsp; &nbsp;
        <a href="privacy-policy.php">Privacy Policy</a>&nbsp; &nbsp;
        <a href="cookie-policy.php">Cookie Policy</a>&nbsp; &nbsp;
        <a href="terms-conditions.php">Terms &amp; Conditions</a>
      </div>
    </div>
  </div>
</footer>

<!-- jQuery (used for the small progressive-enhancement bits in main.js) -->
<script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
<!-- Bootstrap 5 bundle (Popper + JS) -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<script src="assets/js/main.js"></script>
</body>
</html>
