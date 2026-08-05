<?php
/**
 * inc/db.php
 * Single shared PDO connection for the entire itr-site. Every public page
 * pulls its sliders / FAQs / site copy from the `eire_tax_admin` database
 * through this file (via inc/site-data.php), and request/form.php uses it
 * to save new applications. Update these four constants to match your
 * environment — this is the only place credentials live.
 */

// const DB_HOST = '127.0.0.1';
// const DB_NAME = 'eire_tax_admin';
// const DB_USER = 'root';
// const DB_PASS = '';
// const DB_CHARSET = 'utf8mb4';

const DB_HOST = 'localhost';
const DB_NAME = 'eiretaxr_main_db';
const DB_USER = 'eiretaxr_main';
const DB_PASS = 'Segzy2026@';
const DB_CHARSET = 'utf8mb4';

/**
 * Returns a shared PDO instance (one connection per request). Throws on
 * failure — callers in inc/site-data.php catch this and fall back to
 * sensible defaults so a database hiccup never takes the whole site down.
 */
function itr_db(): PDO
{
    static $pdo = null;
    if ($pdo === null) {
        $dsn = 'mysql:host=' . DB_HOST . ';dbname=' . DB_NAME . ';charset=' . DB_CHARSET;
        $pdo = new PDO($dsn, DB_USER, DB_PASS, [
            PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            PDO::ATTR_EMULATE_PREPARES   => false,
        ]);
    }
    return $pdo;
}
