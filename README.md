# itr-site — now fully wired to MySQL

Every place this site used to have hardcoded copy now reads live from the
same `eire_tax_admin` database the admin panel manages. Edit content from
the admin panel (Home Sliders, FAQs, Site Info & Copy) and it appears here
immediately — no code changes, no redeploy.

## Setup

1. Import `schema.sql` (from the admin panel package) into MySQL — same
   database this site and the admin panel both use.
2. Set your DB credentials in **`inc/db.php`** (the single shared
   connection file for the whole site):
   ```php
   const DB_HOST = '127.0.0.1';
   const DB_NAME = 'eire_tax_admin';
   const DB_USER = 'root';
   const DB_PASS = '';
   ```
3. Serve the site (`php -S localhost:8000`) and visit `index.php`.

## What's connected, and where

| On the page                                   | Comes from                                  | File(s)                          |
|-------------------------------------------------|----------------------------------------------|-----------------------------------|
| Hero carousel (4 slides, headings, copy, badge)  | `sliders` table (`status = 'Published'`)     | `index.php`                       |
| "Irish Tax Agent No. …" trust strip               | `site_settings` (`trust_*`)                   | `inc/trust-strip.php`             |
| "Highest Rebate…" heading + intro paragraph       | `site_settings` (`how_it_works_*`)            | `index.php`                       |
| "How it Works" 5 numbered steps                   | `how_it_works_steps` table                    | `index.php`                       |
| "Market leading tax rebate service" stats band    | `site_settings` (`stats_heading`) + `stats_items` table | `index.php`, `about-us.php` |
| "Our Story" section                               | `site_settings` (`story_*`)                    | `index.php`                       |
| FAQ categories & Q&As                             | `faqs` table (`status = 'Published'`)          | `faqs.php`                        |
| Contact phone/email/address                       | `site_settings` (`contact_*`)                  | `inc/footer.php`, `faqs.php`      |
| Footer CRO/VAT numbers                            | `site_settings` (`footer_*`)                   | `inc/footer.php`                  |
| "Get your tax back" CTA banner                    | `site_settings` (`cta_*`)                      | `inc/cta-banner.php`              |
| "Average rebate €X" figure (badge + inline copy)  | `site_settings` (`hero_average_rebate`)        | `index.php`, `flat-rate-expenses.php`, `top-rebates.php` |
| TAN badge number on About Us                      | `site_settings` (`trust_badge_number`)         | `about-us.php`                    |
| Application form submissions                      | Inserted into `applications` table             | `request/form.php`                |

## How it's wired

- **`inc/db.php`** — one shared PDO connection, used everywhere.
- **`inc/site-data.php`** — small helper functions (`itr_setting()`,
  `itr_sliders()`, `itr_faqs_grouped()`, `itr_how_it_works_steps()`,
  `itr_stats_items()`). Every one of them **fails safely**: if the
  database is unreachable or a table is empty, the function returns an
  empty array / the given default instead of throwing, so a DB hiccup
  degrades the page gracefully instead of crashing it. Errors are logged
  server-side via `error_log()`.
- **`inc/header.php`** loads `inc/site-data.php` once, so every page that
  does `include 'inc/header.php';` (i.e. every page on the site)
  automatically has all of the helper functions available.
- **`request/form.php`** validates and inserts new application submissions
  using the same `inc/db.php` connection — so a submission on the live
  site shows up in the admin panel's Applications table immediately.

## Fallbacks

- If `sliders` has no published rows, the hero carousel falls back to a
  single slide built from the `hero_*` settings, so the homepage never
  renders with an empty carousel.
- If `faqs` has no published rows, the FAQs page shows a friendly "no
  FAQs published yet" message instead of an empty page.
- Every `site_settings` lookup (`itr_setting()`) takes a default value, so
  a missing key never breaks a page — it just shows the default until you
  fill it in from the admin panel.
