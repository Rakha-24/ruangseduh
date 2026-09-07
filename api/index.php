<?php

/**
 * Titik masuk serverless untuk deployment di Vercel.
 * Semua request dirutekan ke sini, lalu diteruskan ke front controller Laravel.
 */

// Bila request menunjuk file yang benar-benar ada di public/ (mis. aset build),
// serahkan ke PHP built-in server tanpa memboots Laravel.
$uri = urldecode((string) parse_url($_SERVER['REQUEST_URI'] ?? '/', PHP_URL_PATH));

if ($uri !== '/' && is_file(__DIR__.'/../public'.$uri)) {
    return false;
}

require __DIR__.'/../public/index.php';