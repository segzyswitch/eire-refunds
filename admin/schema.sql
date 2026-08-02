-- ============================================================================
-- EIRE Tax Refunds — Admin Panel database schema + seed data
-- Regenerated to match the real itr-site landing page & application form
-- (sliders = actual hero carousel, FAQs = actual 31 Q&As, applications
-- table = actual multi-step form fields from inc/multi-form.php).
--
-- Import with:  mysql -u root -p < schema.sql
-- (or paste into phpMyAdmin / Adminer's SQL tab)
-- ============================================================================

CREATE DATABASE IF NOT EXISTS `eire_tax_admin`
  CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;

USE `eire_tax_admin`;

-- ----------------------------------------------------------------------------
-- users — admin panel logins
-- ----------------------------------------------------------------------------
CREATE TABLE `users` (
  `id`            INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  `username`      VARCHAR(60)  NOT NULL UNIQUE,
  `password_hash` VARCHAR(255) NOT NULL,
  `name`          VARCHAR(120) NOT NULL,
  `email`         VARCHAR(160) NOT NULL,
  `role`          VARCHAR(60)  NOT NULL DEFAULT 'Administrator',
  `two_factor`    TINYINT(1)   NOT NULL DEFAULT 0,
  `created_at`    TIMESTAMP    NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at`    TIMESTAMP    NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB;

-- Demo login — username: admin / password: admin123
-- (hashed with PHP password_hash(), fully compatible with password_verify())
INSERT INTO `users` (`username`, `password_hash`, `name`, `email`, `role`, `two_factor`) VALUES
('admin', '$2b$10$NBdj1jPU0i74umGIyLKWROHc4gML3sK7Vb/h1YJRCY52jvT83A06C', 'Admin User', 'admin@irishtaxrebates.ie', 'Administrator', 0);

-- ----------------------------------------------------------------------------
-- login_activity — powers the "Recent Login Activity" list on Security
-- ----------------------------------------------------------------------------
CREATE TABLE `login_activity` (
  `id`         INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  `user_id`    INT UNSIGNED NOT NULL,
  `device`     VARCHAR(160) NOT NULL,
  `location`   VARCHAR(160) NOT NULL,
  `ip_address` VARCHAR(45)  NULL,
  `created_at` TIMESTAMP    NOT NULL DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (`user_id`) REFERENCES `users`(`id`) ON DELETE CASCADE
) ENGINE=InnoDB;

INSERT INTO `login_activity` (`user_id`, `device`, `location`, `ip_address`, `created_at`) VALUES
(1, 'Safari on iPhone', 'Athy, IE', '86.42.11.20', DATE_SUB(NOW(), INTERVAL 1 DAY)),
(1, 'Edge on Windows', 'Cork, IE', '86.42.9.117', DATE_SUB(NOW(), INTERVAL 4 DAY));

-- ----------------------------------------------------------------------------
-- applications — one row per submission of the real tax rebate application
-- form (inc/multi-form.php, steps 1–4). Column names mirror the form's
-- `name="…"` attributes exactly so request/form.php can INSERT the posted
-- JSON straight across with no relabeling. `rebate_type`, `rebate_amount`
-- and `status` are admin-managed fields filled in later during review —
-- an applicant never selects a rebate type themselves on the public form.
-- ----------------------------------------------------------------------------
CREATE TABLE `applications` (
  `id`               INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,

  -- Step 1 — About You (contact basics)
  `first_name`       VARCHAR(80)  NOT NULL,
  `last_name`        VARCHAR(80)  NOT NULL,
  `maiden_name`      VARCHAR(80)  NULL,
  `email`            VARCHAR(160) NOT NULL,
  `phone_number`     VARCHAR(30)  NOT NULL,
  `whatsapp_number`  VARCHAR(30)  NOT NULL,

  -- Step 2 — About You (tax profile)
  `occupation`       VARCHAR(100) NOT NULL,
  `pps_number`       VARCHAR(20)  NOT NULL,
  `marital_status`   ENUM('Married','Single','Civil Partnership','Separated','Divorced','Widowed','Single Parent') NOT NULL,
  `date_of_birth`    DATE NULL,

  -- Step 3 — Contact Details
  `address_one`      VARCHAR(160) NOT NULL,
  `address_two`      VARCHAR(120) NOT NULL,
  `county`           VARCHAR(60)  NOT NULL,
  `eircode`          VARCHAR(12)  NULL,
  `promotion_code`   VARCHAR(40)  NULL,

  -- Step 4 — Signature (base64 PNG data URL, or the typed name if "Type In Signature" was used)
  `signature`        LONGTEXT NULL,

  -- Admin-managed review fields (set from the Applications page, not by the applicant)
  `rebate_type`      VARCHAR(80)  NULL,
  `rebate_amount`    DECIMAL(10,2) NOT NULL DEFAULT 0.00,
  `status`           ENUM('New','Awaiting Agent Link','Processing','Paid','Not Due') NOT NULL DEFAULT 'New',

  `submitted_at`     DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at`       TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB;

-- Realistic demo submissions shaped exactly like a real form post, so the
-- Applications table, Dashboard and Charts all have something to show.
INSERT INTO `applications`
  (`first_name`, `last_name`, `maiden_name`, `email`, `phone_number`, `whatsapp_number`,
   `occupation`, `pps_number`, `marital_status`, `date_of_birth`,
   `address_one`, `address_two`, `county`, `eircode`, `promotion_code`,
   `signature`, `rebate_type`, `rebate_amount`, `status`, `submitted_at`)
VALUES
('Aoife', 'Byrne', NULL, 'aoife.byrne@example.com', '0862345671', '0862345671',
 'Staff Nurse', '1234567TA', 'Single', '1994-03-11',
 '12 Rowan Grove', 'Naas', 'Kildare', 'W91K2P4', NULL,
 'Aoife Byrne', 'PAYE Review', 842.50, 'Paid', '2026-07-02 09:14:00'),

('Sean', 'O''Donnell', NULL, 'sean.odonnell@example.com', '0871234562', '0871234562',
 'Radiographer', '2345671TB', 'Married', '1988-07-24',
 '4 Elm Court', 'Cork City', 'Cork', 'T12X4F9', 'SUMMER10',
 'Sean O''Donnell', 'Medical Expenses', 210.00, 'Processing', '2026-07-05 11:40:00'),

('Niamh', 'Kelly', 'Fitzgerald', 'niamh.kelly@example.com', '0851239873', '0851239873',
 'Retail Manager', '3456712TC', 'Married', '1990-01-30',
 '9 The Paddocks', 'Athy', 'Kildare', 'R14P223', NULL,
 NULL, 'Flat Rate Expenses', 156.75, 'Awaiting Agent Link', '2026-07-09 14:02:00'),

('Cian', 'Murphy', NULL, 'cian.murphy@example.com', '0834567891', '0834567891',
 'Software Engineer', '4567123TD', 'Married', '1985-11-02',
 '21 Riverside Walk', 'Dublin 8', 'Dublin', 'D08YV21', NULL,
 'Cian Murphy', 'Marriage Tax Credit', 1320.00, 'Paid', '2026-07-11 16:25:00'),

('Roisin', 'Walsh', NULL, 'roisin.walsh@example.com', '0867891234', '0867891234',
 'Primary School Teacher', '5671234TE', 'Single', '1996-05-19',
 '15 Castle View', 'Wexford Town', 'Wexford', 'Y35H8C2', NULL,
 'Roisin Walsh', NULL, 0.00, 'Not Due', '2026-07-14 10:08:00'),

('Darragh', 'Fitzgerald', NULL, 'darragh.fitzgerald@example.com', '0851122334', '0851122334',
 'Marketing Executive', '6712345TF', 'Single', '1992-09-08',
 '3 Oakwood Drive', 'Galway City', 'Galway', 'H91R6T3', 'REMOTE24',
 'Darragh Fitzgerald', 'Remote Working Relief', 486.20, 'Processing', '2026-07-17 13:55:00'),

('Grace', 'Doyle', NULL, 'grace.doyle@example.com', '0899988776', '0899988776',
 'Physiotherapist', '7123456TG', 'Civil Partnership', '1991-12-14',
 '27 Sli na hAbhann', 'Limerick City', 'Limerick', 'V94E2K5', NULL,
 'Grace Doyle', 'Medical Expenses', 95.40, 'Paid', '2026-07-19 09:31:00'),

('Conor', 'Ryan', NULL, 'conor.ryan@example.com', '0838765432', '0838765432',
 'Electrician', '8123457TH', 'Married', '1983-04-27',
 '6 Chapel Lane', 'Athlone', 'Westmeath', 'N37X8D4', NULL,
 NULL, NULL, 675.00, 'Awaiting Agent Link', '2026-07-21 15:44:00'),

('Emer', 'Nolan', NULL, 'emer.nolan@example.com', '0851239900', '0851239900',
 'Hairdresser', '9123458TI', 'Single', '1997-02-05',
 '18 Priory Court', 'Tullamore', 'Offaly', 'R35T1A9', 'SUMMER10',
 'Emer Nolan', 'Flat Rate Expenses', 143.10, 'Paid', '2026-07-23 08:20:00'),

('Padraig', 'Quinn', NULL, 'padraig.quinn@example.com', '0873344556', '0873344556',
 'Civil Servant', '1123459TJ', 'Married', '1980-06-16',
 '11 Harbour Row', 'Dun Laoghaire', 'Dublin', 'A96K7P2', NULL,
 'Padraig Quinn', 'Marriage Tax Credit', 990.00, 'Processing', '2026-07-25 12:10:00'),

('Sinead', 'Brennan', NULL, 'sinead.brennan@example.com', '0866677889', '0866677889',
 'Occupational Therapist', '2123460TK', 'Widowed', '1975-10-22',
 '2 Millbrook Avenue', 'Kilkenny City', 'Kilkenny', 'R95F4W8', NULL,
 'Sinead Brennan', 'PAYE Review', 320.60, 'Paid', '2026-07-27 10:47:00'),

('Eoin', 'Carroll', NULL, 'eoin.carroll@example.com', '0851567890', '0851567890',
 'Warehouse Operative', '3123461TL', 'Single Parent', '1998-08-30',
 '30 Meadow Close', 'Sligo Town', 'Sligo', 'F91Y2C6', NULL,
 NULL, NULL, 0.00, 'New', '2026-07-28 17:02:00');

-- ----------------------------------------------------------------------------
-- sliders — homepage hero carousel (matches the 4 real slides in index.php's
-- #heroCarousel exactly: heading, copy, photo, and rebate badge per slide)
-- ----------------------------------------------------------------------------
CREATE TABLE `sliders` (
  `id`          INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  `title`       VARCHAR(160) NOT NULL,
  `subtitle`    TEXT NULL,
  `badge_text`  VARCHAR(80)  NOT NULL DEFAULT 'Average Rebate',
  `badge_value` VARCHAR(40)  NULL,
  `image`       VARCHAR(400) NULL,
  `cta_label`   VARCHAR(120) NOT NULL DEFAULT 'Apply Now',
  `cta_url`     VARCHAR(255) NOT NULL DEFAULT '/apply',
  `status`      ENUM('Published','Draft') NOT NULL DEFAULT 'Draft',
  `sort_order`  INT UNSIGNED NOT NULL DEFAULT 1,
  `updated_at`  TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB;

INSERT INTO `sliders` (`title`, `subtitle`, `badge_text`, `badge_value`, `image`, `cta_label`, `cta_url`, `status`, `sort_order`) VALUES
('Financially Supporting a Relative?', 'There''s a tax rebate for that. We guarantee the highest possible tax rebate in Ireland. EIRE Tax Refunds will find and fight for every cent, with the most comprehensive tax review in the market.', 'Average Rebate', '1,092', 'https://picsum.photos/seed/itr-relative/560/560', 'Apply For My Rebate Now', '#apply', 'Published', 1),
('You''re Owed Tax Back. Claim it!', 'We guarantee the highest possible tax rebate in Ireland. EIRE Tax Refunds will find and fight for every cent, with the most comprehensive tax review in the market.', 'Average Rebate', '1,092', 'https://picsum.photos/seed/itr-owed/560/560', 'Apply For My Rebate Now', '#apply', 'Published', 2),
('Paid for Medical Expenses?', 'There''s a tax rebate for that. We guarantee the highest possible tax rebate in Ireland. EIRE Tax Refunds will find and fight for every cent, with the most comprehensive tax review in the market.', 'Average Rebate', '1,092', 'https://picsum.photos/seed/itr-medical/560/560', 'Apply For My Rebate Now', '#apply', 'Published', 3),
('Working from Home?', 'There''s a tax rebate for that. We guarantee the highest possible tax rebate in Ireland. EIRE Tax Refunds will find and fight for every cent, with the most comprehensive tax review in the market.', 'Average Rebate', '1,092', 'https://picsum.photos/seed/itr-wfh/560/560', 'Apply For My Rebate Now', '#apply', 'Published', 4);

-- ----------------------------------------------------------------------------
-- faqs — public FAQ accordion (all 31 real Q&As from faqs.php, across the
-- site's 4 real categories: Registration, Your Tax Review, Your Rebate,
-- General Tax Questions)
-- ----------------------------------------------------------------------------
CREATE TABLE `faqs` (
  `id`         INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  `category`   VARCHAR(60)  NOT NULL DEFAULT 'General',
  `question`   VARCHAR(255) NOT NULL,
  `answer`     TEXT NOT NULL,
  `status`     ENUM('Published','Draft') NOT NULL DEFAULT 'Draft',
  `sort_order` INT UNSIGNED NOT NULL DEFAULT 1,
  `updated_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB;

INSERT INTO `faqs` (`category`, `question`, `answer`, `status`, `sort_order`) VALUES
('Registration', 'Why do I have to register?', 'Registering lets us securely link to your Revenue record as your appointed tax agent, so we can review your tax history and identify any credits or reliefs you''ve missed. Without this authorisation, we can''t access your record or submit a claim on your behalf.', 'Published', 1),
('Registration', 'How do I register?', 'Simply complete our 60-second online application form with your personal details. Once submitted, we send an agent-link request to Revenue, which you then approve yourself through your Revenue myAccount.', 'Published', 2),
('Registration', 'I am jointly assessed with my spouse, do we both need to register?', 'If you''re jointly assessed, credits and reliefs are calculated across the joint assessment, so it''s best for both spouses to register. This lets us review the full picture and make sure nothing is missed on either side.', 'Published', 3),
('Registration', 'How does it work if I recently got married?', 'Getting married can open up additional credits, including the option to be jointly assessed, which often works out more favourably. Let us know your marital status and the date of marriage on the form and we''ll review the most beneficial tax treatment for you.', 'Published', 4),
('Registration', 'I provided some incorrect information on my authorisation form. What shall I do?', 'Get in touch with us as soon as possible so we can correct it. Incorrect details — particularly your PPS number or address — can delay Revenue approving the agent-link request.', 'Published', 5),
('Registration', 'How long does the process take?', 'Once you approve the agent-link request in your Revenue myAccount, our review typically takes a matter of days. If a rebate is due, it''s usually paid out by Revenue within an average of 12 working days from when your claim is submitted.', 'Published', 6),
('Registration', 'Do I need to register each year?', 'No. Once you''re registered, our agent link stays in place, so we can review your tax position automatically each year without you needing to sign up again.', 'Published', 7),
('Registration', 'How much will it cost me?', 'Nothing upfront, and nothing at all if you''re not due a rebate — it''s no rebate, no fee. If a rebate is found, our fee is a maximum of 10% of the rebate plus VAT, with a minimum charge of €25, and it''s only ever taken after your money has come through.', 'Published', 8),
('Your Tax Review', 'What documentation do I need to send in to support my tax rebate claim?', 'For most standard credits, none — the information is already available on your Revenue record once we''re linked as your agent. For specific reliefs like medical expenses, we may ask you to send in receipts or a summary of costs to support the claim.', 'Published', 9),
('Your Tax Review', 'What if I think that there is additional information relevant to my review?', 'Just let us know. Email us or contact our support team with the details and we''ll factor it into your review before anything is submitted.', 'Published', 10),
('Your Tax Review', 'How will I know if I am due a rebate?', 'We''ll contact you directly, by email or phone, once your review is complete — whether or not a rebate is due, so you''re never left wondering.', 'Published', 11),
('Your Tax Review', 'My wife has received an update on her review, but I haven''t heard anything.', 'Even where a couple is jointly assessed, each person''s review is processed as an individual case and can complete at different times. Get in touch with us directly and we''ll check the status of your own application.', 'Published', 12),
('Your Tax Review', 'How do I receive my money back?', 'Any rebate due is paid directly to you by Revenue — usually straight into the bank account linked to your Revenue profile, or by cheque if no bank details are on file.', 'Published', 13),
('Your Tax Review', 'I don''t have a bank account. What can I do?', 'That''s not a problem. Revenue can issue your rebate by cheque instead. Let us know and we can also point you toward updating your Revenue profile if you''d prefer a bank transfer in future.', 'Published', 14),
('Your Rebate', 'I think I should be due a rebate, but I am not sure.', 'That''s exactly what our review is for. There''s no cost to find out — apply and we''ll check your last four years of tax for anything you may be owed, with no fee unless we find a rebate.', 'Published', 15),
('Your Rebate', 'What can I claim back?', 'Common areas include overpaid PAYE income tax, medical and dental expenses, flat rate (uniform/tools) expenses, marriage and family tax credits, the Rent Tax Credit, the Dependent Relative Tax Credit, and Working from Home relief, among others.', 'Published', 16),
('Your Rebate', 'My colleagues said I should have a Uniform Allowance, what is this?', 'This is a flat rate expense credit available to certain occupations that are required to purchase and maintain their own uniform, tools or equipment — for example nurses, tradespeople and various other roles. The amount varies depending on your specific occupation.', 'Published', 17),
('Your Rebate', 'Who is eligible for tax back?', 'Any PAYE worker who has paid income tax in Ireland, either currently or within the last four tax years, may be eligible, depending on their individual circumstances and entitlements.', 'Published', 18),
('Your Rebate', 'How much of a tax rebate could I get back?', 'It varies a lot from person to person depending on your circumstances, but our average rebate across customers is around €1,092. The only way to know your own figure is through a full review of your last four years.', 'Published', 19),
('Your Rebate', 'How many years can you claim tax back for?', 'You can claim for the current tax year plus the previous four years, in line with Revenue''s statutory time limit for backdated claims.', 'Published', 20),
('Your Rebate', 'I think my taxes were incorrect more than 4 years ago?', 'Unfortunately Revenue''s four-year rule is a hard limit, so years outside that window generally can''t be reclaimed. We''d still recommend a review to make sure you''re not missing out on anything within the claimable years.', 'Published', 21),
('Your Rebate', 'I am unemployed now, could I still be due a rebate?', 'Yes. You can still claim for any tax year in which you were employed and paid tax, regardless of your current employment status.', 'Published', 22),
('Your Rebate', 'Can I make a tax claim if I''ve left Ireland?', 'Yes — if you paid PAYE income tax while living or working in Ireland, you can still submit a claim for those years after you''ve left the country.', 'Published', 23),
('General Tax Questions', 'I am leaving Ireland to go and work abroad?', 'Let us know before or as soon as you leave. We can review your final year of Irish employment, as you may be due a rebate for the portion of the year you worked here.', 'Published', 24),
('General Tax Questions', 'I am constantly travelling outside the country with my job. Could I be due a tax rebate?', 'Possibly — this can depend on allowable travel and subsistence expenses, or whether emergency tax was applied at any point. It''s worth having your record reviewed to check.', 'Published', 25),
('General Tax Questions', 'I am paying for my childs third level education. Is there any relief available for this?', 'Tax relief is available on qualifying third-level tuition fees above a set annual threshold, subject to certain conditions. Let us know the details and we can check what applies in your case.', 'Published', 26),
('General Tax Questions', 'Who can I claim medical expenses for?', 'You can generally claim for medical expenses you paid for yourself, your spouse or civil partner, your children, and in some cases other individuals such as a dependent relative, even where they aren''t a tax dependent in the traditional sense.', 'Published', 27),
('General Tax Questions', 'I cannot find all my receipts, and I know that there are a lot missing. What can I do?', 'Send us what you do have. Revenue will sometimes accept alternative supporting evidence, and we can advise on the best way to document expenses where original receipts aren''t available.', 'Published', 28),
('General Tax Questions', 'My husband and I just got married, does this change the way in which we are taxed?', 'It can. Married couples can choose to be assessed jointly, separately, or as single individuals, and joint assessment often makes the most of combined tax bands and credits. We can review which option suits you best.', 'Published', 29),
('General Tax Questions', 'What is emergency tax?', 'Emergency tax is a higher, non-personalised rate of tax applied when Revenue or your employer doesn''t have your correct tax details on file — usually when starting a new job without a completed tax registration. It''s generally refundable in full once your record is corrected.', 'Published', 30),
('General Tax Questions', 'What can''t I claim for?', 'Certain items are excluded, such as most cosmetic procedures, routine and general dental or eye care not related to a specific qualifying treatment, and any expenses already reimbursed by insurance or another source. If you''re unsure whether something qualifies, just ask us.', 'Published', 31);

-- ----------------------------------------------------------------------------
-- site_settings — simple key/value store for one-off text blocks
-- (hero, trust bar, our story, contact, footer, CTA banner). Add a new row
-- any time you need a new editable field — no migration required.
-- ----------------------------------------------------------------------------
CREATE TABLE `site_settings` (
  `setting_key`   VARCHAR(100) PRIMARY KEY,
  `setting_value` TEXT NULL,
  `updated_at`    TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB;

INSERT INTO `site_settings` (`setting_key`, `setting_value`) VALUES
('hero_heading',        'Financially Supporting a Relative?'),
('hero_body',            'There''s a tax rebate for that. We guarantee the highest possible tax rebate in Ireland. EIRE Tax Refunds will find and fight for every cent, with the most comprehensive tax review in the market.'),
('hero_average_rebate', '1092'),

('trust_badge_label',  'Irish Tax Agent'),
('trust_badge_number', '66436K'),
('trust_message',      'Over 20 years experience in offering independent and confidential tax advice'),

('how_it_works_heading', 'Highest Rebate. We work for you. No Rebate, No Fee.'),
('how_it_works_intro',   'EIRE Tax Refunds ensure you get back all the tax that is owed to you, for this year and the previous four. Our comprehensive review checks overpaid income tax, medical expenses, flat rate expenses, employment expenses, marriage & family tax credits and many more — and it''s no rebate, no fee. If you''re not entitled to any tax back, we don''t charge a fee. If there is a rebate due, our maximum fee is just 10% plus VAT, with a minimum charge of €25. It''s time to claim what you''re owed, hassle-free. Apply online within 60 seconds and receive your rebate within 12 working days, issued directly by Revenue.'),

('stats_heading', 'The market leading tax rebate service'),

('story_eyebrow', 'Our Story'),
('story_heading', 'Helping people claim tax back'),
('story_body',    'We started out more than 20 years ago in an attempt to counteract the issue of PAYE workers overpaying their taxes. It wasn''t clear to many that this was happening, or how to claim it back. We wanted to help, and EIRE Tax Refunds was born. Since launching our smart online form with eSignature to make the process even easier, we''ve grown to become a leading provider of tax back services in Ireland.'),

('contact_phone_1', '059-8634 794'),
('contact_phone_2', '01-6755 010'),
('contact_email',   'info@irishtaxrebates.ie'),
('contact_address', 'EIRE Tax Refunds, MB Tax Group, 1 Leinster St., Athy, Co. Kildare, Ireland, R14 K226'),

('footer_cro',       '473739'),
('footer_vat',       '9717017R'),
('footer_copyright', '© 2026 EIRE Tax Refunds, part of the MB Tax Group. All rights reserved.'),

('cta_heading',      'Get your tax back'),
('cta_button_text',  'Fill out our 60-second tax application form');

-- ----------------------------------------------------------------------------
-- how_it_works_steps — the 5 numbered steps under "How it Works"
-- ----------------------------------------------------------------------------
CREATE TABLE `how_it_works_steps` (
  `id`          INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  `step_number` INT UNSIGNED NOT NULL,
  `title`       VARCHAR(160) NOT NULL,
  `description` TEXT NULL,
  `sort_order`  INT UNSIGNED NOT NULL DEFAULT 1
) ENGINE=InnoDB;

INSERT INTO `how_it_works_steps` (`step_number`, `title`, `description`, `sort_order`) VALUES
(1, 'Complete the quick online form',        'It just takes 60 seconds', 1),
(2, 'Approve the agent link on Revenue',     'Simply log in to Revenue MyAccount and authorise EIRE Tax Refunds to act on your behalf by approving the agent link request', 2),
(3, 'We review your taxes',                  'Our team reviews your taxes for this year and the past 4 years and identifies any tax rebates due to you', 3),
(4, 'You receive your tax rebate',           'Any refund due will be paid directly into your bank account by Revenue, or in the absence of a bank account on your Revenue profile, by cheque.', 4),
(5, 'You pay our fee',                       'Our fee is only charged after you receive your rebate, we also operate a ''no refund, no fee'' basis, so we do not charge a fee if you are not due a rebate.', 5);

-- ----------------------------------------------------------------------------
-- stats_items — the 3 tiles under "The market leading tax rebate service"
-- ----------------------------------------------------------------------------
CREATE TABLE `stats_items` (
  `id`          INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  `icon`        VARCHAR(60)  NOT NULL DEFAULT 'award',
  `value`       VARCHAR(160) NOT NULL,
  `description` TEXT NULL,
  `sort_order`  INT UNSIGNED NOT NULL DEFAULT 1
) ENGINE=InnoDB;

INSERT INTO `stats_items` (`icon`, `value`, `description`, `sort_order`) VALUES
('speedometer2',     'Fastest Service',        'Apply in just 60 seconds and receive a rebate in an average of 12 working days.', 1),
('currency-exchange', 'Highest Rebate',        'Our average rebate is a market-leading €1,092', 2),
('award',            '20+ Years Experience',   'A dedicated team of experienced, professional accountants.', 3);
