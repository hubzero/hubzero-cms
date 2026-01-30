<?php

/**
 * HUBzero Web Installation Wizard
 *
 * This file can be used as a standalone entry point for fresh installations
 * OR loaded through the HUBzero framework's Install client.
 *
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2025 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

// phpcs:disable PSR1.Files.SideEffects

// Prevent direct access except through proper entry points
if (!defined('HUBZERO_INSTALL') && !defined('_HZEXEC_')) {
    // Allow direct access for development/testing
    define('HUBZERO_INSTALL', 1);
}

// Error reporting for install
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Check for Windows - HUBzero does not support Windows
if (strtoupper(substr(PHP_OS, 0, 3)) === 'WIN') {
    http_response_code(500);
    ?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>HUBzero - Unsupported Operating System</title>
    <style>
        * { box-sizing: border-box; margin: 0; padding: 0; }
        body {
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 2rem;
        }
        .error-box {
            background: white;
            border-radius: 1rem;
            box-shadow: 0 20px 40px rgba(0,0,0,0.2);
            padding: 3rem;
            max-width: 600px;
            text-align: center;
        }
        .error-icon {
            font-size: 4rem;
            margin-bottom: 1rem;
        }
        h1 {
            color: #dc2626;
            margin-bottom: 1rem;
            font-size: 1.5rem;
        }
        p {
            color: #374151;
            line-height: 1.6;
            margin-bottom: 1rem;
        }
        .supported-os {
            background: #f3f4f6;
            border-radius: 0.5rem;
            padding: 1rem 1.5rem;
            margin: 1.5rem 0;
            text-align: left;
        }
        .supported-os h3 {
            color: #111827;
            font-size: 1rem;
            margin-bottom: 0.5rem;
        }
        .supported-os ul {
            margin-left: 1.5rem;
            color: #4b5563;
        }
        .supported-os li {
            margin: 0.25rem 0;
        }
        .detected {
            background: #fef2f2;
            border: 1px solid #fecaca;
            border-radius: 0.5rem;
            padding: 0.75rem 1rem;
            color: #dc2626;
            font-family: monospace;
            margin-top: 1rem;
        }
    </style>
</head>
<body>
    <div class="error-box">
        <div class="error-icon">&#10060;</div>
        <h1>Unsupported Operating System</h1>
        <p>HUBzero CMS is designed for Unix-like operating systems and <strong>cannot run on Windows</strong>.</p>
        <p>The platform relies on POSIX features, Unix file permissions, and system utilities
            that are not available on Windows.</p>
        <div class="supported-os">
            <h3>Supported Operating Systems:</h3>
            <ul>
                <li><strong>Linux</strong> - Ubuntu, Debian, RHEL, CentOS, Rocky Linux</li>
                <li><strong>macOS</strong> - For development environments</li>
            </ul>
        </div>
        <p>Please install HUBzero on a supported Linux server or use a Linux virtual machine.</p>
        <div class="detected">Detected: <?php echo PHP_OS; ?></div>
    </div>
</body>
</html>
    <?php
    exit(1);
}

// Start session for wizard state if not already started
if (session_status() === PHP_SESSION_NONE) {
    session_name('hubzero_install');
    session_start();
}

// Define paths only if not already defined (framework may have set them)
if (!defined('INSTALL_ROOT')) {
    define('INSTALL_ROOT', dirname(__DIR__));
}
if (!defined('PATH_CORE')) {
    define('PATH_CORE', dirname(dirname(INSTALL_ROOT)));
}
if (!defined('PATH_ROOT')) {
    define('PATH_ROOT', dirname(PATH_CORE));
}
if (!defined('PATH_APP')) {
    define('PATH_APP', PATH_ROOT . '/app');
}

// Load the installer
require_once __DIR__ . '/Installer.php';

// Run the installer (only if called directly, not through framework)
if (!defined('_HZEXEC_')) {
    $installer = new \Bootstrap\Install\Web\Installer();
    $installer->run();
}
