<?php
$page_title = 'Tax Tips | EIRE Tax Refunds';
$active = 'tips';
include 'inc/header.php';

$posts = [
  ['title' => 'Am I Due a Tax Refund? 10 Signs You Might Be Owed Money', 'excerpt' => 'Tax rebates: Am I due a tax refund in Ireland? Tax rebates are one of the most commonly missed financial entitlements in Ireland. Every year, thousands of PAYE workers unknowingly overpay tax and never claim the money back. If you\'ve ever asked yourself "am I due a tax refund?", the answer is often yes. In this guide, we\'ll walk you through...'],
  ['title' => 'Marriage Tax Credits: All You Need to Know', 'excerpt' => 'Thinking about marriage or a civil partnership affects your tax? You\'re not alone. The way you\'re assessed as a couple can make a big difference to what you pay each year, with potential savings of up to &euro;3,875. This guide walks you through the options, explains the key tax credits and shows you how to make sure you\'re not...'],
  ['title' => 'How You Will Receive Your Tax Rebate From 2026 Onwards', 'excerpt' => 'Your Tax Rebate Will Now Be Paid Directly to You (From 2025) Last updated: 20 October 2025 From January 2025, Revenue will begin paying approved tax rebates directly to taxpayers\' bank accounts, rather than routing the funds to your tax agent (e.g. EIRE Tax Refunds). This process will streamline the rebate process and allow you to receive your tax rebate...'],
  ['title' => 'Budget 2026: Highlights and What It Means for PAYE Workers', 'excerpt' => 'Budget 2026 Highlights for PAYE workers. No change in the standard rate of income tax cut off point for the 40% tax band, the entry point will remain at &euro;44,000. The Rent Tax Credit will be extended to 2028 remaining at &euro;1,000 for single taxpayers and &euro;2,000 for married couples. Changes made again to the upper rate of the second...'],
  ['title' => 'What is Income Tax in Ireland?', 'excerpt' => 'Understanding Income Tax. Income tax and the Irish tax system in general can be confusing for many, but fear not — here at EIRE Tax Refunds we are here to simplify it for you. Whether you\'re curious about how to calculate income tax, want to understand the various income tax bands and rates, or seek clarity on special...'],
  ['title' => 'Local Property Tax in Ireland: What It Is &amp; How to Pay It Online', 'excerpt' => 'Local Property Tax (LPT) is a yearly self-assessed tax based on the market value of residential properties in Ireland. It applies to all owners of residential properties, including any buildings or parts used as accommodation. LPT has been in effect since 1 July 2013 and is collected by the Revenue Commissioners. The revenue generated from local property tax in Ireland...'],
  ['title' => 'Your guide to claiming income protection tax relief in Ireland', 'excerpt' => 'What if you couldn\'t work due to illness or injury? Income protection provides vital security, and here in Ireland, you can get tax relief on your income protection, making this important cover more affordable. At EIRE Tax Refunds, we cut through the noise to help you understand and claim what\'s yours. We\'ve answered your top questions about income protection tax...'],
  ['title' => 'How to Claim Tax Credits (With Examples)', 'excerpt' => 'Many people mistakenly believe that tax credits are automatically applied by Revenue. Unfortunately for them, this is not always the case, which means they might not receive tax back that they are owed for medical procedures or tuition fees etc. Are tax credits automatically applied? The most common tax credits that Revenue automatically applies are the basic personal tax credit...'],
  ['title' => '5 Important Tax Credits for Medical Professionals', 'excerpt' => 'Are you a healthcare worker? If so, this one\'s for you! Every year, thousands of tax credits go unclaimed. During our tax reviews, we have found this to be especially true for medical and healthcare professionals. Our tax rebate customers in the healthcare sector include doctors, nurses, medical assistants, occupational therapists, laboratory technicians, home carers and many others. That\'s why...'],
  ['title' => 'Everything you need to know about civil service subsistence rates in Ireland', 'excerpt' => 'If you\'re a public sector worker in Ireland, understanding civil service subsistence rates is key to ensuring you\'re reimbursed fairly for your work-related travel expenses. Whether you\'re travelling overnight for a meeting or using your vehicle for work trips, there are allowances available to help cover costs like accommodation, meals, and mileage. In this blog post, we walk you through...'],
];

$categories = [
  'Education' => 5, 'Employment' => 29, 'General Tax' => 50,
  'Irish Tax Advice' => 20, 'Marriage &amp; Family' => 22, 'Tax Updates' => 12,
  'Medical &amp; Dental' => 10, 'Uncategorised' => 21, 'Uncategorized' => 5,
];
?>

<section class="itr-page-banner">
  <div class="container">
    <h1 class="fw-bold">Tax Tips</h1>
    <p class="mb-0">EIRE Tax Refunds' Tax Tips is the leading online resource to help keep you up-to-date with all of the latest developments in tax. With a team that has over 20 year's professional tax experience, we aim to offer you the best practical tips, advice and information to help you claim back any rebate owed. Simple. Easy. Fast</p>
  </div>
</section>

<section class="py-5">
  <div class="container">

    <!-- Featured post -->
    <div class="row g-4 align-items-center mb-5 pb-4 border-bottom">
      <div class="col-md-5">
        <div class="itr-blog-thumb"></div>
      </div>
      <div class="col-md-7">
        <h2 class="fw-bold h4">How Tax Rebates Work in Ireland for PAYE Workers</h2>
        <p class="text-muted">Many PAYE workers in Ireland pay more tax than they need to, often without ever realising it. In many cases it's because an overpayment of tax, but in most cases it is because certain tax credits or reliefs were never applied. So how do rebates actually work in Ireland? Revenue releases a statement of liability confirming the amount of...</p>
        <a href="#" class="itr-read-more">Read More</a>
      </div>
    </div>

    <!-- Grid of remaining posts -->
    <div class="row g-4">
      <?php foreach ($posts as $post): ?>
        <div class="col-md-6 itr-blog-card">
          <div class="itr-blog-thumb mb-3"></div>
          <h3 class="fw-bold"><?php echo $post['title']; ?></h3>
          <p><?php echo $post['excerpt']; ?></p>
          <a href="#" class="itr-read-more">Read More</a>
        </div>
      <?php endforeach; ?>
    </div>

    <div class="text-center my-5">
      <a href="#" class="btn btn-outline-dark rounded-0 px-4">More Tax Tips</a>
    </div>

    <h2 class="fw-bold" style="color:var(--itr-primary);">Categories</h2>
    <div class="row">
      <?php $i = 0; foreach ($categories as $cat => $count): $i++; ?>
        <div class="col-md-4 mb-2"><a href="#" class="text-dark text-decoration-none"><?php echo $cat; ?> (<?php echo $count; ?>)</a></div>
        <?php if ($i % 3 === 0) echo '<div class="w-100"></div>'; ?>
      <?php endforeach; ?>
    </div>
  </div>
</section>

<?php include 'inc/cta-banner.php'; ?>
<?php include 'inc/footer.php'; ?>
