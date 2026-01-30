<?php

/**
 * Windows OS Error Page
 *
 * Shared between bootstrap installer and main web installer.
 * HUBzero does not support Windows.
 *
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2025 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

// phpcs:disable Generic.Files.LineLength
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Unsupported Operating System - HUBzero</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body {
            font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, "Helvetica Neue", Arial, sans-serif;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 20px;
        }
        .error-container {
            background: white;
            border-radius: 12px;
            box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.25);
            max-width: 600px;
            width: 100%;
            overflow: hidden;
        }
        .error-header {
            background: #dc2626;
            color: white;
            padding: 30px;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 15px;
        }
        .error-header h1 {
            font-size: 1.5rem;
            font-weight: 600;
            margin: 0;
        }
        .error-header .icon {
            font-size: 2.5rem;
            line-height: 1;
        }
        .error-body {
            padding: 30px;
        }
        .error-body p {
            color: #374151;
            line-height: 1.6;
            margin-bottom: 20px;
        }
        .error-body ul {
            color: #374151;
            margin: 0 0 20px 20px;
        }
        .error-body li {
            margin-bottom: 8px;
        }
        .info-box {
            background: #fef3c7;
            border: 1px solid #f59e0b;
            border-radius: 8px;
            padding: 15px;
            margin-top: 20px;
        }
        .info-box h3 {
            color: #92400e;
            font-size: 0.9rem;
            margin-bottom: 8px;
        }
        .info-box p {
            color: #92400e;
            font-size: 0.85rem;
            margin: 0;
        }
    </style>
</head>
<body>
    <div class="error-container">
        <div class="error-header">
            <div class="icon">&#9888;</div>
            <h1>Unsupported Operating System</h1>
        </div>
        <div class="error-body">
            <p><strong>HUBzero cannot be installed on Windows.</strong></p>
            <p>HUBzero is designed exclusively for Unix-like operating systems and relies on features that are not available on Windows, including:</p>
            <ul>
                <li>POSIX file permissions and ownership</li>
                <li>Unix process management</li>
                <li>Symbolic links and Unix filesystem semantics</li>
                <li>Shell commands and utilities (bash, grep, etc.)</li>
            </ul>
            <p>To install HUBzero, please use one of the following supported platforms:</p>
            <ul>
                <li><strong>Linux</strong> (Ubuntu, CentOS, Rocky Linux, etc.)</li>
                <li><strong>macOS</strong> (for development only)</li>
            </ul>
            <div class="info-box">
                <h3>Using Windows for Development?</h3>
                <p>Consider using Windows Subsystem for Linux (WSL2), Docker, or a virtual machine running Linux to develop and test HUBzero.</p>
            </div>
        </div>
    </div>
</body>
</html>
