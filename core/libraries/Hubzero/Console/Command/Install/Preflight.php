<?php

/**
 * @package    framework
 * @copyright  Copyright (c) 2005-2025 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Hubzero\Console\Command\Install;

/**
 * Preflight check helper class
 *
 * This class handles pre-installation checks.
 * It can be used both by minimal muse (before autoloading) and full muse.
 **/
class Preflight
{
    /**
     * Minimum required PHP version
     *
     * @var string
     **/
    private const MIN_PHP_VERSION = '8.2.0';

    /**
     * Required PHP extensions
     *
     * @var array
     **/
    private const REQUIRED_EXTENSIONS = [
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
     * Run all preflight checks
     *
     * @param   bool    $ansi      Whether to use ANSI color output
     * @param   string  $rootPath  Path to the root directory (parent of core)
     * @return  bool    True if all checks pass, false otherwise
     */
    public static function check($ansi = true, $rootPath = null)
    {
        if ($rootPath === null) {
            $rootPath = defined('PATH_ROOT')
                ? PATH_ROOT
                : dirname(dirname(dirname(dirname(dirname(dirname(__DIR__))))));
        }

        $allPassed = true;
        $appPath = $rootPath . '/app';
        $corePath = $rootPath . '/core';

        self::output("\n", $ansi);
        self::output("\e[33mPre-flight Checks\e[39m\n", $ansi);
        self::output("-----------------\n", $ansi);

        // Check operating system (must be first - Windows is not supported)
        if (!self::checkOperatingSystem($ansi)) {
            $allPassed = false;
        }

        // Check system commands (unzip first)
        if (!self::checkSystemCommands($ansi)) {
            $allPassed = false;
        }

        // Check vendor dependencies
        if (!self::checkVendor($ansi, $corePath)) {
            $allPassed = false;
        }

        // Check PHP version
        if (!self::checkPhpVersion($ansi)) {
            $allPassed = false;
        }

        // Check required extensions
        if (!self::checkExtensions($ansi)) {
            $allPassed = false;
        }

        // Check write permissions
        if (!self::checkWritePermissions($ansi, $appPath)) {
            $allPassed = false;
        }

        if (!$allPassed) {
            self::output("\n", $ansi, true);
            self::output(
                "\e[37;41mERROR:\e[39;49m Pre-flight checks failed. Please resolve the issues above.\n",
                $ansi,
                true
            );
            self::output("\n", $ansi, true);
        } else {
            self::output("\n", $ansi);
            self::output("\e[32mAll preflight checks passed.\e[39m\n", $ansi);
        }

        return $allPassed;
    }

    /**
     * Check operating system is supported (Linux, macOS, BSD - not Windows)
     *
     * @param   bool  $ansi  Whether to use ANSI colors
     * @return  bool
     **/
    private static function checkOperatingSystem($ansi)
    {
        $os = PHP_OS_FAMILY;
        $supported = ['Linux', 'Darwin', 'BSD'];

        if (in_array($os, $supported, true)) {
            self::output("  \e[32m[OK]\e[39m Operating System: {$os}\n", $ansi);
            return true;
        }

        self::output("  \e[31m[FAIL]\e[39m Operating System: {$os}\n", $ansi, true);
        self::output("         HUBzero requires Linux, macOS, or BSD. Windows is not supported.\n", $ansi, true);
        return false;
    }

    /**
     * Check PHP version meets minimum requirement
     *
     * @param   bool  $ansi  Whether to use ANSI colors
     * @return  bool
     **/
    private static function checkPhpVersion($ansi)
    {
        $minVersion = self::MIN_PHP_VERSION;

        if (version_compare(PHP_VERSION, $minVersion, '>=')) {
            self::output("  \e[32m[OK]\e[39m PHP " . PHP_VERSION . "\n", $ansi);
            return true;
        }

        self::output("  \e[31m[FAIL]\e[39m PHP " . PHP_VERSION . " (requires {$minVersion}+)\n", $ansi, true);
        return false;
    }

    /**
     * Check all required PHP extensions are loaded
     *
     * @param   bool  $ansi  Whether to use ANSI colors
     * @return  bool
     **/
    private static function checkExtensions($ansi)
    {
        $allLoaded = true;

        foreach (self::REQUIRED_EXTENSIONS as $ext => $description) {
            if (extension_loaded($ext)) {
                self::output("  \e[32m[OK]\e[39m {$ext} extension\n", $ansi);
            } else {
                self::output("  \e[31m[FAIL]\e[39m {$ext} extension - {$description}\n", $ansi, true);
                $allLoaded = false;
            }
        }

        return $allLoaded;
    }

    /**
     * Check required system commands are available
     *
     * @param   bool  $ansi  Whether to use ANSI colors
     * @return  bool
     **/
    private static function checkSystemCommands($ansi)
    {
        $allFound = true;

        // Check for unzip (required by Composer)
        $unzipPath = trim(shell_exec('which unzip 2>/dev/null') ?? '');
        if (!empty($unzipPath)) {
            self::output("  \e[32m[OK]\e[39m unzip command\n", $ansi);
        } else {
            self::output("  \e[31m[FAIL]\e[39m unzip command not found\n", $ansi, true);
            self::output("         Required for extracting packages\n", $ansi, true);
            $allFound = false;
        }

        return $allFound;
    }

    /**
     * Check composer vendor dependencies are installed
     *
     * @param   bool    $ansi      Whether to use ANSI colors
     * @param   string  $corePath  Path to the core directory
     * @return  bool
     **/
    private static function checkVendor($ansi, $corePath)
    {
        $vendorPath = $corePath . '/vendor/autoload.php';

        if (file_exists($vendorPath)) {
            self::output("  \e[32m[OK]\e[39m Composer dependencies installed\n", $ansi);
            return true;
        }

        self::output("  \e[31m[FAIL]\e[39m Composer dependencies not installed\n", $ansi, true);
        self::output("         Run: muse install vendor\n", $ansi, true);
        return false;
    }

    /**
     * Check required directories are writable
     *
     * @param   bool    $ansi     Whether to use ANSI colors
     * @param   string  $appPath  Path to the app directory
     * @return  bool
     **/
    private static function checkWritePermissions($ansi, $appPath)
    {
        $appParent = dirname($appPath);

        if (is_dir($appPath)) {
            if (is_writable($appPath)) {
                self::output("  \e[32m[OK]\e[39m {$appPath} is writable\n", $ansi);
                return true;
            } else {
                self::output("  \e[31m[FAIL]\e[39m {$appPath} is not writable\n", $ansi, true);
                return false;
            }
        } else {
            if (is_writable($appParent)) {
                self::output("  \e[32m[OK]\e[39m {$appParent} is writable\n", $ansi);
                return true;
            } else {
                self::output("  \e[31m[FAIL]\e[39m {$appParent} is not writable\n", $ansi, true);
                return false;
            }
        }
    }

    /**
     * Output helper that handles ANSI stripping
     *
     * @param   string  $text   Text to output
     * @param   bool    $ansi   Whether to use ANSI colors
     * @param   bool    $error  Whether this is an error message
     * @return  void
     */
    private static function output($text, $ansi = true, $error = false)
    {
        if (!$ansi) {
            $text = preg_replace("/\e\[\d+m/", "", $text);
            $text = preg_replace("/\e\[\d+;\d+m/", "", $text);
        }
        echo $text;
    }
}
