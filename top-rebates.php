<?php
$page_title = 'Top Rebates | EIRE Tax Refunds';
$active = 'top';
include 'inc/header.php';
?>

<section class="py-5">
  <div class="container">
    <div class="row align-items-center gy-4">
      <div class="col-lg-6">
        <h1 class="fw-bold">Are you due tax back from the <span style="color:var(--itr-primary); text-decoration:underline;">Top Additional</span> Rebates?</h1>
        <p class="fw-bold mt-3">Claim it now, hassle-free!</p>
        <p>Most people in Ireland miss out on further tax rebates including medical expenses, working from home tax rebate and dependent relative tax credit. These rebates are <strong>in addition</strong> to your initial tax rebate worth on average &euro;<?php echo number_format((float) itr_setting('hero_average_rebate', '1092'), 0); ?>!</p>
        <p>Check out the most common extra rebates below, that people often do not claim, to see if you could be due more money.</p>
        <div class="d-flex flex-wrap gap-2 mt-3">
          <a href="rent-tax-credit.php" class="btn btn-itr-outline"><i class="bi bi-house-door me-1"></i>Rent Tax Credit</a>
          <a href="medical-expenses.php" class="btn btn-itr-outline"><i class="bi bi-heart-pulse me-1"></i>Medical</a>
          <a href="#working-from-home" class="btn btn-itr-outline"><i class="bi bi-laptop me-1"></i>Working From Home</a>
          <a href="#dependent-relative" class="btn btn-itr-outline"><i class="bi bi-people me-1"></i>Dependent Relative</a>
        </div>
      </div>
      <div class="col-lg-6">
        <img src="https://picsum.photos/seed/top-rebates/700/500" class="img-fluid rounded" alt="Reviewing tax rebate paperwork">
      </div>
    </div>

    <h2 class="fw-bold mt-5 mb-4">Top 4 Additional Tax Rebates you could be entitled to claim</h2>

    <!-- Rent Tax Credit -->
    <div class="itr-rebate-card row g-0 mb-4" id="rent-tax-credit">
      <div class="col-md-7 p-4">
        <h3 class="fw-bold"><i class="bi bi-house-door itr-rebate-icon me-2"></i>Rent Tax Credit</h3>
        <p>The Rent Tax Credit has been introduced for the years 2022-2025. If you have rented private accommodation since in any of the claimable years (2022 - 2025) you can now claim this tax relief. This tax credit is worth up to &euro;500 for 2022 and 2023 for single taxed individuals. The credit is also worth up to &euro;1000 per year for a jointly assessed couple. As announced in the 2025 Budget, the value of this credit has increased to &euro;1000 per person for 2024 and 2025, and &euro;2000 per year for a jointly assessed couple.</p>
        <a href="rent-tax-credit.php" class="btn btn-itr-primary">Apply for Rent Tax Credit</a>
      </div>
      <div class="col-md-5">
        <img src="https://picsum.photos/seed/rent-house/500/350" alt="Model house with coins">
      </div>
    </div>

    <!-- Medical Expenses -->
    <div class="itr-rebate-card row g-0 mb-4" id="medical">
      <div class="col-md-5 order-md-1">
        <img src="https://picsum.photos/seed/medical-consult/500/350" alt="Doctor consultation">
      </div>
      <div class="col-md-7 order-md-2 p-4">
        <h3 class="fw-bold"><i class="bi bi-heart-pulse-fill itr-rebate-icon me-2"></i>Medical Expenses Tax Rebate</h3>
        <p>Every PAYE employee in Ireland can claim tax relief on both day-to-day and special medical expenses such as you and your family's doctor's visits, prescriptions, dental and many more.</p>
        <p>Yet, less than half of the Irish workforce claim this additional tax rebate. EIRE Tax Refunds can help you to maximise your medical expenses tax rebate, hassle-free. Applicants for the Medical Expense tax rebate claim back on average &euro;480. All PAYE employees are entitled to claim back up to 20% on medical costs and 40% for costs of nursing home care or in-home professional care for you or a dependent.</p>
        <a href="medical-expenses.php" class="btn btn-itr-primary">Apply For Additional Rebate</a>
      </div>
    </div>

    <!-- Working From Home -->
    <div class="itr-rebate-card row g-0 mb-4" id="working-from-home">
      <div class="col-md-7 p-4">
        <h3 class="fw-bold"><i class="bi bi-laptop itr-rebate-icon me-2"></i>'Working From Home' Tax Rebate</h3>
        <p>There are more Irish taxpayers than ever who are now working from home and eligible to claim a special tax allowance each year to claim money back on expenses such as Broadband, light and heating utilities and costs that will be greater if your work is now substantially from home, either on a full or part time basis (*Please note that Broadband costs are only eligible from the tax year 2020 onwards).</p>
        <a href="index.php#apply" class="btn btn-itr-primary">Apply For Additional Rebate</a>
      </div>
      <div class="col-md-5">
        <img src="https://picsum.photos/seed/wfh-laptop/500/350" alt="Working from home at a laptop">
      </div>
    </div>

    <!-- Dependent Relative -->
    <div class="itr-rebate-card row g-0 mb-5" id="dependent-relative">
      <div class="col-md-5 order-md-1">
        <img src="https://picsum.photos/seed/dependent-relative/500/350" alt="Supporting a dependent relative">
      </div>
      <div class="col-md-7 order-md-2 p-4">
        <h3 class="fw-bold"><i class="bi bi-people-fill itr-rebate-icon me-2"></i>Dependent Relative Tax Rebate</h3>
        <p>Thousands of people in Ireland have dependent relatives but do not claim the annual Dependent Relative Tax allowance. You are entitled to claim for any elderly dependent relatives whether they reside in Ireland or elsewhere in the world. The value of this credit is &euro;305 per individual, from the 1st of January 2025.</p>
        <a href="index.php#apply" class="btn btn-itr-primary">Apply For Additional Rebate</a>
      </div>
    </div>

    <!-- Testimonial -->
    <div class="itr-testimonial">
      <p>"Simple, fast, and accurate. Takes all of the hassle out of the refund procedure. They know what they are doing!"</p>
      <cite>Aoife O'Gorman<br>Co. Laois</cite>
    </div>
  </div>
</section>

<?php include 'inc/footer.php'; ?>
