<?php

/**
 * includes/db.php
 * One place to configure the database connection. Update these four
 * constants to match your environment (or better, load them from
 * environment variables) and every page in the panel picks it up via itr_db().
 */

const DB_HOST = '127.0.0.1';
const DB_NAME = 'eire_tax_admin';
const DB_USER = 'root';
const DB_PASS = '';
const DB_CHARSET = 'utf8mb4';

$dsn = 'mysql:host=' . DB_HOST . ';dbname=' . DB_NAME . ';charset=' . DB_CHARSET;

$options = [
	PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
	PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
	PDO::ATTR_EMULATE_PREPARES   => false,
];

try {
	$pdo = new PDO($dsn, DB_USER, DB_PASS, $options);
} catch (PDOException $e) {
	// Fail loudly but safely — no credentials or stack trace shown to visitors.
	http_response_code(500);
	die('<div style="font-family:sans-serif;max-width:560px;margin:80px auto;padding:24px;' .
		'border:1px solid #f1c0c0;background:#fff5f5;border-radius:8px;color:#7a1f1f;">' .
		'<h2 style="margin-top:0;">Database connection failed</h2>' .
		'<p>Could not connect to <code>' . htmlspecialchars(DB_NAME) . '</code> on <code>' . htmlspecialchars(DB_HOST) . '</code>.</p>' .
		'<p>Check the credentials in <code>includes/db.php</code> and make sure you have imported <code>schema.sql</code>.</p>' .
		'</div>');
}
