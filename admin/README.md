# EIRE Tax Refunds — Admin Panel (MySQL edition, wired to the real site)

A self-contained PHP admin panel for the `itr-site` landing pages, built on
Bootstrap 5, Bootstrap Icons, Chart.js and DataTables, backed entirely by
**MySQL** via PDO. All seed data — sliders, FAQs, site copy, and demo
applications — has been regenerated directly from the real `itr-site`
source so the panel reflects the actual live content, not placeholders.

## 1. Create the database

```bash
mysql -u root -p < schema.sql
```

This creates the `eire_tax_admin` database and every table with real seed
data:

- **4 sliders** — the exact 4 hero carousel slides from `index.php`
  ("Financially Supporting a Relative?", "You're Owed Tax Back. Claim it!",
  "Paid for Medical Expenses?", "Working from Home?")
- **31 FAQs** — every Q&A from `faqs.php`, across its 4 real categories
  (Registration, Your Tax Review, Your Rebate, General Tax Questions)
- **Site copy** — hero, trust bar, "How it Works" steps, stats band, "Our
  Story", contact details, footer legal info, and the "Get your tax back"
  CTA banner — all pulled verbatim from `index.php` / `inc/*.php`
- **12 demo applications** — shaped exactly like a real submission of the
  site's multi-step form (see below)

## 2. Set your database credentials

Both the admin panel (`includes/db.php`) and the public site's form
handler (`itr-site/request/db.php`) need the same four constants:

```php
const DB_HOST = '127.0.0.1';
const DB_NAME = 'eire_tax_admin';
const DB_USER = 'root';
const DB_PASS = '';
```

They point at the same database, so an application submitted on the public
site shows up in the admin panel immediately.

## 3. Run it

```bash
php -S localhost:8000
```

Log in with **admin** / **admin123**, then change it from **Security**.

## The applications table now matches the real form exactly

`itr-site/inc/multi-form.php` is a real 4-step form (About You → Tax
Profile → Address → Signature) that POSTs JSON to
`itr-site/request/form.php`. The `applications` table's columns are named
to match the form's `name="…"` attributes one-for-one:

| Form step            | Columns                                                                 |
|-----------------------|--------------------------------------------------------------------------|
| 1 — About You         | `first_name`, `last_name`, `maiden_name`, `email`, `phone_number`, `whatsapp_number` |
| 2 — Tax Profile       | `occupation`, `pps_number`, `marital_status`, `date_of_birth`             |
| 3 — Address           | `address_one`, `address_two`, `county`, `eircode`, `promotion_code`       |
| 4 — Signature         | `signature` (base64 PNG data URL, or the typed name)                     |

Three more columns are **admin-managed** — an applicant never picks these
on the public form, your team sets them during review:

- `rebate_type` — nullable; `NULL` means "not yet classified"
- `rebate_amount` — decimal, defaults to `0.00`
- `status` — `New` (fresh submission) → `Awaiting Agent Link` →
  `Processing` → `Paid`, or `Not Due`

`itr-site/request/form.php` validates the required fields, combines
`date_of_birth_day` / `_month` / `_year` into one `DATE`, and inserts the
row with `status = 'New'` and `rebate_type = NULL`, ready for your team to
triage from the **Applications** page.

## Where the CRUD lives

- **tables.php** — Applications: Add / **View** / **Edit** / **Delete**,
  all against the real schema above. The View modal shows every field the
  applicant submitted (including a signature preview) plus the
  admin-managed rebate fields; Edit lets your team correct any field or
  set the rebate type/amount/status.
- **sliders.php** / **faqs.php** — same CRUD pattern as before:
  `save_slider` / `delete_slider` / `toggle_status`, and `save_faq` /
  `delete_faq` / `toggle_status`.
- **security.php** — profile, password (bcrypt via `password_verify()` /
  `password_hash()`), and 2FA toggle, all against the `users` table.
- **site-content.php** — one tab per section, including the new **CTA
  Banner** tab for the "Get your tax back" heading/button text.

## Charts — all live queries, one file to edit

Every KPI and chart comes from `includes/queries.php`:

- `get_kpi_totals()` — dashboard KPI cards
- `get_weekly_rebate_totals()` / `get_weekly_applications_vs_rebates()` —
  trend charts (grouped by week of `submitted_at`)
- `get_status_breakdown()` — status doughnut
- `get_rebate_totals_by_type()` — rebate value by type (groups `NULL`
  rebate_type as "Unclassified")
- `get_applications_by_county()` — new: applications by county bar chart,
  since every real application now has a real Irish county on file

Edit the SQL in any of these functions and both `dashboard.php` and
`charts.php` pick up the change automatically.

## Folder structure

```
admin-panel/
├── schema.sql            MySQL schema + real seed data — import this first
├── includes/
│   ├── db.php             PDO connection (edit your credentials here)
│   ├── config.php         Session bootstrap, require_login(), flash messages, h()
│   ├── queries.php        Centralized SQL for every KPI/chart
│   ├── header.php / sidebar.php / navbar.php / footer.php
├── assets/                Brand CSS + shared JS
├── login.php / logout.php / index.php
├── dashboard.php          KPIs + trend & status charts (live from MySQL)
├── tables.php             Applications: full add/view/edit/delete CRUD, real schema
├── charts.php             Line, doughnut, bar (by county) and radar charts
├── forms-elements.php     Static component showcase (switches, validation, etc.)
├── security.php           Username/password change, 2FA toggle, login history
├── sliders.php            CRUD for the 4 real homepage hero slides
├── faqs.php               CRUD for the 31 real FAQ entries
└── site-content.php       Tabbed editor: hero, trust bar, steps, stats, story,
                           contact, CTA banner, footer

itr-site/                  The public landing pages (unchanged, plus:)
└── request/
    ├── db.php              PDO connection — same database as the admin panel
    ├── form.php            Validates & inserts a real form submission
    └── .htaccess           Blocks direct browser access to db.php
```

## Security notes

- Passwords are hashed with `password_hash()` / verified with
  `password_verify()` — never stored in plain text.
- All queries use PDO prepared statements with bound parameters.
- `request/form.php` validates every required field and the email format
  server-side before touching the database, and never echoes raw
  exception details back to the browser.
