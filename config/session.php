<?php

/**
 * @package    hubzero-cms
 * @copyright  Copyright © 2026 Purdue University. All Rights Reserved.
 * @license    http://opensource.org/licenses/MIT MIT
 */

// Compute the legacy HubZero session cookie name: md5(md5($secret . $client))
// This ensures Laravel and the legacy entry point share the same session cookie.
$hubzeroApp = file_exists(base_path('app/config/app.php'))
    ? (array) require base_path('app/config/app.php')
    : [];
$hubzeroSession = file_exists(base_path('app/config/session.php'))
    ? (array) require base_path('app/config/session.php')
    : [];

$secret = $hubzeroApp['secret'] ?? '';
$cookieName = $secret ? md5(md5($secret . 'site')) : 'hubzero_session';

return [
    'driver' => env('SESSION_DRIVER', 'hubzero'),
    'lifetime' => env('SESSION_LIFETIME', $hubzeroSession['lifetime'] ?? 120),
    'expire_on_close' => env('SESSION_EXPIRE_ON_CLOSE', false),
    'encrypt' => env('SESSION_ENCRYPT', false),
    'files' => storage_path('framework/sessions'),
    'connection' => env('SESSION_CONNECTION'),
    'table' => env('SESSION_TABLE', 'sessions'),
    'store' => env('SESSION_STORE'),
    'lottery' => [2, 100],
    'cookie' => env('SESSION_COOKIE', $cookieName),
    'path' => env('SESSION_PATH', $hubzeroSession['cookie_path'] ?: '/'),
    'domain' => env('SESSION_DOMAIN', $hubzeroSession['cookie_domain'] ?: null),
    'secure' => env('SESSION_SECURE_COOKIE'),
    'http_only' => env('SESSION_HTTP_ONLY', true),
    'same_site' => env('SESSION_SAME_SITE', 'lax'),
    'partitioned' => env('SESSION_PARTITIONED_COOKIE', false),
];
