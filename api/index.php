<?php

use Illuminate\Http\Request;

define('LARAVEL_START', microtime(true));

if (isset($_GET['debug_vercel'])) {
    header('Content-Type: application/json');
    $manifestSrcPhp = __DIR__ . '/manifest_data.php';
    echo json_encode([
        '__DIR__' => __DIR__,
        'manifest_data_php_exists' => file_exists($manifestSrcPhp),
        'manifest_data_php_size' => file_exists($manifestSrcPhp) ? filesize($manifestSrcPhp) : null,
        'tmp_manifest_exists' => file_exists('/tmp/public/build/manifest.json'),
        'tmp_manifest_size' => file_exists('/tmp/public/build/manifest.json') ? filesize('/tmp/public/build/manifest.json') : null,
        'files_in_api' => scandir(__DIR__),
        'files_in_tmp' => file_exists('/tmp') ? scandir('/tmp') : null,
    ]);
    exit;
}

// [Vercel] Ensure Vite manifest is available in writable /tmp for serverless.
// The manifest is compiled into a PHP file (api/manifest_data.php) during build,
// then written to /tmp/public/build/ so Laravel can find it via public_path().
$manifestDst = '/tmp/public/build/manifest.json';
if (!file_exists($manifestDst)) {
    $manifestSrcPhp = __DIR__ . '/manifest_data.php';
    if (file_exists($manifestSrcPhp)) {
        $manifestJson = require $manifestSrcPhp;
        @mkdir('/tmp/public/build', 0777, true);
        @file_put_contents($manifestDst, $manifestJson);
    }
}

// Serve static build assets directly (CSS, JS, images from Vite)
$uri = urldecode(parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH));
$publicPath = __DIR__ . '/../public' . $uri;

// [Vercel] Serve uploaded profile pictures from writable /tmp
if (strpos($uri, '/profile/') === 0) {
    $tmpProfilePath = '/tmp/public' . $uri;
    if (file_exists($tmpProfilePath) && !is_dir($tmpProfilePath)) {
        $publicPath = $tmpProfilePath;
    }
}

if ($uri !== '/' && file_exists($publicPath) && !is_dir($publicPath)) {
    $mimeTypes = [
        'css'  => 'text/css',
        'js'   => 'application/javascript',
        'json' => 'application/json',
        'png'  => 'image/png',
        'jpg'  => 'image/jpeg',
        'jpeg' => 'image/jpeg',
        'gif'  => 'image/gif',
        'svg'  => 'image/svg+xml',
        'ico'  => 'image/x-icon',
        'woff' => 'font/woff',
        'woff2'=> 'font/woff2',
        'ttf'  => 'font/ttf',
        'eot'  => 'application/vnd.ms-fontobject',
    ];
    $ext = pathinfo($publicPath, PATHINFO_EXTENSION);
    $mime = $mimeTypes[$ext] ?? 'application/octet-stream';
    header('Content-Type: ' . $mime);
    header('Cache-Control: public, max-age=31536000, immutable');
    readfile($publicPath);
    exit;
}

// Determine if the application is in maintenance mode...
if (file_exists($maintenance = __DIR__.'/../storage/framework/maintenance.php')) {
    require $maintenance;
}

// Register the Composer autoloader...
require __DIR__.'/../vendor/autoload.php';

// Bootstrap Laravel and handle the request...
$app = require_once __DIR__.'/../bootstrap/app.php';

// [Vercel] Override public path so Laravel finds Vite manifest in /tmp
if (file_exists('/tmp/public/build/manifest.json')) {
    $app->usePublicPath('/tmp/public');
}

$app->handleRequest(Request::capture());
