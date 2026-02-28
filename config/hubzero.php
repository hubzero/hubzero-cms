<?php

/**
 * @package    hubzero-cms
 * @copyright  Copyright © 2026 Purdue University. All Rights Reserved.
 * @license    http://opensource.org/licenses/MIT MIT
 */

/**
 * Bridge legacy HubZero configuration files into Laravel's config system.
 *
 * Legacy configs live in app/config/*.php (flat arrays). This file loads
 * them and exposes values via Config::get('hubzero.app.secret'), etc.
 *
 * Components can use Laravel's Config facade:
 *   use Illuminate\Support\Facades\Config;
 *   $secret = Config::get('hubzero.app.secret');
 */

$legacyPath = base_path('app/config');

$load = function (string $file) use ($legacyPath): array {
    $path = $legacyPath . '/' . $file . '.php';
    return file_exists($path) ? (array) require $path : [];
};

return [
    'app'      => $load('app'),
    'database' => $load('database'),
    'session'  => $load('session'),
    'mail'     => $load('mail'),
    'cache'    => $load('cache'),
    'ftp'      => $load('ftp'),
    'meta'     => $load('meta'),
    'seo'      => $load('seo'),
    'offline'  => $load('offline'),
];
