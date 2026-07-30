<?php
/**
 * inc/site-data.php
 * Every helper here reads from the `eire_tax_admin` database (the same one
 * the admin panel writes to) and every helper fails *safely*: if the DB is
 * unreachable or a table is empty, the page still renders using sensible
 * fallback content instead of a fatal error. Edit content from the admin
 * panel (Home Sliders, FAQs, Site Info & Copy) — these pages never need
 * touching again.
 */

require_once __DIR__ . '/db.php';

/** All site_settings rows as a flat [key => value] array, cached per-request. */
function itr_settings(): array
{
    static $cache = null;
    if ($cache !== null) {
        return $cache;
    }
    try {
        $rows = itr_db()->query('SELECT setting_key, setting_value FROM site_settings')->fetchAll();
        $cache = [];
        foreach ($rows as $row) {
            $cache[$row['setting_key']] = $row['setting_value'];
        }
    } catch (Throwable $e) {
        error_log('[itr-site] site_settings fetch failed: ' . $e->getMessage());
        $cache = [];
    }
    return $cache;
}

/** A single settings value, with a fallback if the DB is empty/unreachable or the key doesn't exist yet. */
function itr_setting(string $key, string $default = ''): string
{
    $settings = itr_settings();
    return ($settings[$key] ?? '') !== '' ? $settings[$key] : $default;
}

/** Published homepage hero slides, in display order. */
function itr_sliders(): array
{
    try {
        return itr_db()->query(
            "SELECT * FROM sliders WHERE status = 'Published' ORDER BY sort_order ASC"
        )->fetchAll();
    } catch (Throwable $e) {
        error_log('[itr-site] sliders fetch failed: ' . $e->getMessage());
        return [];
    }
}

/** The "How it Works" numbered steps, in display order. */
function itr_how_it_works_steps(): array
{
    try {
        return itr_db()->query('SELECT * FROM how_it_works_steps ORDER BY sort_order ASC')->fetchAll();
    } catch (Throwable $e) {
        error_log('[itr-site] how_it_works_steps fetch failed: ' . $e->getMessage());
        return [];
    }
}

/** The 3 tiles under "The market leading tax rebate service". */
function itr_stats_items(): array
{
    try {
        return itr_db()->query('SELECT * FROM stats_items ORDER BY sort_order ASC')->fetchAll();
    } catch (Throwable $e) {
        error_log('[itr-site] stats_items fetch failed: ' . $e->getMessage());
        return [];
    }
}

/**
 * Published FAQs grouped by category, preserving both the order categories
 * were first seen in (by sort_order) and the order of questions within
 * each category. Shape: ['Registration' => [ ['question'=>…, 'answer'=>…], … ], …]
 */
function itr_faqs_grouped(): array
{
    try {
        $rows = itr_db()->query(
            "SELECT * FROM faqs WHERE status = 'Published' ORDER BY sort_order ASC"
        )->fetchAll();
    } catch (Throwable $e) {
        error_log('[itr-site] faqs fetch failed: ' . $e->getMessage());
        return [];
    }

    $grouped = [];
    foreach ($rows as $row) {
        $grouped[$row['category']][] = $row;
    }
    return $grouped;
}
