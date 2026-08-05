<?php
/**
 * EIRE Tax Refunds — Admin Panel
 * Core configuration + session bootstrap. All data now lives in MySQL —
 * see includes/db.php for the connection and schema.sql for the schema.
 */

session_start();

define('BASE_PATH', dirname(__DIR__));
define('BASE_URL', rtrim(str_replace('\\', '/', dirname($_SERVER['SCRIPT_NAME'] ?? '')), '/'));

// The admin panel lives in /admin at the site root, alongside /public —
// the same folder the public site serves uploaded images/files from.
// define('PUBLIC_UPLOAD_DIR', dirname(BASE_PATH) . '/public'); // local
define('PUBLIC_UPLOAD_DIR', dirname(BASE_PATH) . 'https://iristaxrefund.ie/public'); // live

date_default_timezone_set('Europe/Dublin');

require '../inc/db.php'; // provides itr_db() (PDO instance)
require __DIR__ . '/queries.php'; // centralized dashboard/chart SQL

/** Require the user to be logged in — redirect to login.php otherwise. */
function require_login(): void
{
    if (empty($_SESSION['admin_user'])) {
        header('Location: ' . BASE_URL . '/login.php');
        exit;
    }
}

/** Flash message helpers (one-shot session messages shown after redirects). */
function set_flash(string $type, string $message): void
{
    $_SESSION['flash'] = ['type' => $type, 'message' => $message];
}

function get_flash(): ?array
{
    if (empty($_SESSION['flash'])) {
        return null;
    }
    $flash = $_SESSION['flash'];
    unset($_SESSION['flash']);
    return $flash;
}

/** Escape helper for output. */
function h($value): string
{
    return htmlspecialchars((string) $value, ENT_QUOTES, 'UTF-8');
}

/**
 * Resolves an image path stored in the database for display *inside the
 * admin panel*. Full URLs (https://...) are used as-is. Root-relative
 * paths like "public/slider_xyz.jpg" are what the public site (at the
 * site root) uses directly — but the admin panel lives one level down in
 * /admin, so its own preview thumbnails need a "../" prefix to reach the
 * same /public folder.
 */
function public_asset_url(string $path): string
{
    if ($path === '') {
        return '';
    }
    if (preg_match('#^https?://#i', $path)) {
        return $path;
    }
    return '../' . ltrim($path, '/');
}

/**
 * Handles a single uploaded image file (from an <input type="file"> with
 * the given field name), saving it into /public at the site root and
 * returning the path to store in the database (e.g. "public/abc123.jpg").
 *
 * Returns null if no file was uploaded for that field (not an error —
 * callers should fall back to whatever the person typed in a URL field
 * instead). Throws a RuntimeException on validation/upload failure so the
 * caller can show a friendly error via set_flash().
 */
function handle_image_upload(string $fieldName): ?string
{
    if (empty($_FILES[$fieldName]) || $_FILES[$fieldName]['error'] === UPLOAD_ERR_NO_FILE) {
        return null; // Nothing was chosen — not an error.
    }

    $file = $_FILES[$fieldName];

    if ($file['error'] !== UPLOAD_ERR_OK) {
        throw new RuntimeException('The image failed to upload (error code ' . $file['error'] . ').');
    }

    $maxBytes = 5 * 1024 * 1024; // 5MB
    if ($file['size'] > $maxBytes) {
        throw new RuntimeException('Images must be smaller than 5MB.');
    }

    $allowed = ['jpg' => 'image/jpeg', 'jpeg' => 'image/jpeg', 'png' => 'image/png', 'gif' => 'image/gif', 'webp' => 'image/webp'];
    $ext = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
    if (!isset($allowed[$ext])) {
        throw new RuntimeException('Please upload a JPG, PNG, GIF or WEBP image.');
    }

    if (!is_dir(PUBLIC_UPLOAD_DIR) && !mkdir(PUBLIC_UPLOAD_DIR, 0755, true) && !is_dir(PUBLIC_UPLOAD_DIR)) {
        throw new RuntimeException('Could not create the /public uploads folder — check its permissions.');
    }

    $filename = uniqid('slider_', true) . '.' . $ext;
    $destination = PUBLIC_UPLOAD_DIR . '/' . $filename;

    if (!move_uploaded_file($file['tmp_name'], $destination)) {
        throw new RuntimeException('Could not save the uploaded image to /public.');
    }

    // Stored/returned as a root-relative path so it works whether the
    // public site links to it as "public/slider_xyz.jpg" or you later
    // swap it for a full https:// URL from a CDN.
    return 'public/' . $filename;
}

/**
 * Fetch every row from site_settings as a flat [setting_key => setting_value]
 * array. Add a new row to the table any time you need a new field — no
 * code change required to read it back.
 */
function get_site_settings(): array
{
    $rows = itr_db()->query('SELECT setting_key, setting_value FROM site_settings')->fetchAll();
    $settings = [];
    foreach ($rows as $row) {
        $settings[$row['setting_key']] = $row['setting_value'];
    }
    return $settings;
}

/** Upsert a single site_settings key/value pair. */
function save_site_setting(string $key, string $value): void
{
    $stmt = itr_db()->prepare(
        'INSERT INTO site_settings (setting_key, setting_value) VALUES (:key, :value)
         ON DUPLICATE KEY UPDATE setting_value = VALUES(setting_value)'
    );
    $stmt->execute(['key' => $key, 'value' => $value]);
}
