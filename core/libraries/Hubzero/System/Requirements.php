<?php

/**
 * @package    framework
 * @copyright  Copyright (c) 2005-2025 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Hubzero\System;

/**
 * System Requirements utility class
 *
 * Provides common system requirement definitions and check methods.
 * Used by installers to verify the system meets HUBzero's requirements.
 *
 * This class is designed to work both with the HUBzero framework autoloading
 * and standalone during installation (before autoloading is available).
 */
class Requirements
{
    /**
     * Minimum required PHP version
     */
    public const MIN_PHP_VERSION = '8.2.0';

    /**
     * Required PHP extensions with descriptions
     */
    public const REQUIRED_EXTENSIONS = [
        'pdo'       => 'Database abstraction layer',
        'pdo_mysql' => 'MySQL database driver',
        'json'      => 'JSON data handling',
        'mbstring'  => 'Multibyte string support',
        'openssl'   => 'Encryption and SSL support',
        'curl'      => 'HTTP client for APIs',
        'gd'        => 'Image processing',
        'fileinfo'  => 'MIME type detection',
        'zip'       => 'Archive handling',
    ];

    /**
     * Check if the current PHP version meets the minimum requirement
     *
     * @return  array  Result with keys: passed, required, current
     */
    public static function checkPhpVersion(): array
    {
        $passed = version_compare(PHP_VERSION, self::MIN_PHP_VERSION, '>=');

        return [
            'name' => 'PHP Version',
            'required' => self::MIN_PHP_VERSION . '+',
            'current' => PHP_VERSION,
            'passed' => $passed,
        ];
    }

    /**
     * Check if all required PHP extensions are loaded
     *
     * @return  array  Result with keys: passed, required, current, missing, installed
     */
    public static function checkExtensions(): array
    {
        $missing = [];
        $installed = [];

        foreach (self::REQUIRED_EXTENSIONS as $ext => $description) {
            if (extension_loaded($ext)) {
                $installed[] = $ext;
            } else {
                $missing[] = $ext;
            }
        }

        $allPassed = empty($missing);
        $total = count(self::REQUIRED_EXTENSIONS);

        return [
            'name' => 'PHP Extensions',
            'required' => $total . ' required',
            'current' => $allPassed
                ? 'All ' . $total . ' installed'
                : count($missing) . ' missing: ' . implode(', ', $missing),
            'passed' => $allPassed,
            'missing' => $missing,
            'installed' => $installed,
        ];
    }

    /**
     * Check if the unzip command is available
     *
     * @return  array  Result with keys: passed, required, current
     */
    public static function checkUnzip(): array
    {
        $unzipPath = trim(shell_exec('which unzip 2>/dev/null') ?? '');
        $found = !empty($unzipPath);

        return [
            'name' => 'unzip command',
            'required' => 'Required',
            'current' => $found ? 'Found' : 'Missing',
            'passed' => $found,
            'description' => 'Required for extracting packages',
        ];
    }

    /**
     * Check if the operating system is supported
     *
     * @return  array  Result with keys: passed, os, message
     */
    public static function checkOperatingSystem(): array
    {
        $isWindows = strtoupper(substr(PHP_OS, 0, 3)) === 'WIN';

        return [
            'name' => 'Operating System',
            'required' => 'Unix/Linux/macOS',
            'current' => PHP_OS,
            'passed' => !$isWindows,
            'message' => $isWindows
                ? 'HUBzero does not support Windows'
                : 'Supported operating system',
        ];
    }

    /**
     * Check if a directory is writable
     *
     * @param   string  $path  Path to check
     * @return  array  Result with keys: passed, path, message
     */
    public static function checkWritable(string $path): array
    {
        $writable = is_writable($path);

        return [
            'name' => 'Directory Writable',
            'required' => 'Writable',
            'current' => $writable ? 'Writable' : 'Not writable',
            'passed' => $writable,
            'path' => $path,
        ];
    }

    /**
     * Check if Composer dependencies are installed
     *
     * @param   string  $corePath  Path to the core directory
     * @return  array  Result with keys: passed, required, current
     */
    public static function checkVendor(string $corePath): array
    {
        $vendorPath = $corePath . '/vendor/autoload.php';
        $exists = file_exists($vendorPath);

        return [
            'name' => 'Composer dependencies',
            'required' => 'Installed',
            'current' => $exists ? 'Installed' : 'Not installed',
            'passed' => $exists,
            'description' => $exists ? '' : 'Run: cd core && composer install',
        ];
    }

    /**
     * Run all standard checks and return combined results
     *
     * @param   string|null  $corePath  Path to core directory (for vendor check)
     * @param   string|null  $appPath   Path to app directory (for writable check)
     * @return  array  Array of check results, plus 'all_passed' key
     */
    public static function checkAll(?string $corePath = null, ?string $appPath = null): array
    {
        $results = [
            'php_version' => self::checkPhpVersion(),
            'extensions' => self::checkExtensions(),
            'os' => self::checkOperatingSystem(),
            'unzip' => self::checkUnzip(),
        ];

        if ($corePath !== null) {
            $results['vendor'] = self::checkVendor($corePath);
        }

        if ($appPath !== null) {
            $results['app_writable'] = self::checkWritable($appPath);
        }

        // Check if all passed
        $allPassed = true;
        foreach ($results as $result) {
            if (!$result['passed']) {
                $allPassed = false;
                break;
            }
        }
        $results['all_passed'] = $allPassed;

        return $results;
    }
}
