<?php

/**
 * @package    framework
 * @copyright  Copyright (c) 2005-2025 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Hubzero\Console\Command\Install;

use Hubzero\Content\Migration;

/**
 * Migrations helper class for installation
 *
 * This class handles running database migrations during installation.
 * It uses the Hubzero\Content\Migration class directly when possible,
 * or falls back to shelling out when the application isn't bootstrapped.
 **/
class Migrations
{
    /**
     * Run all pending database migrations
     *
     * @param   bool    $ansi      Whether to use ANSI color output
     * @param   string  $appPath   Path to the app directory
     * @param   string  $corePath  Path to the core directory
     * @param   string  $rootPath  Path to the root directory
     * @return  bool    True on success, false on failure
     */
    public static function run($ansi = true, $appPath = null, $corePath = null, $rootPath = null)
    {
        if ($appPath === null) {
            $appPath = defined('PATH_APP')
                ? PATH_APP
                : dirname(dirname(dirname(dirname(dirname(dirname(__DIR__)))))) . '/app';
        }

        if ($corePath === null) {
            $corePath = defined('PATH_CORE')
                ? PATH_CORE
                : dirname(dirname(dirname(dirname(dirname(dirname(__DIR__))))));
        }

        if ($rootPath === null) {
            $rootPath = defined('PATH_ROOT') ? PATH_ROOT : dirname($corePath);
        }

        self::output("\n", $ansi);
        self::output("\e[33mRunning Database Migrations\e[39m\n", $ansi);
        self::output("---------------------------\n", $ansi);

        // Check for database config
        $configPath = $appPath . '/config/database.php';
        if (!file_exists($configPath)) {
            self::output("\n", $ansi, true);
            self::output("\e[31mDatabase configuration not found.\e[39m\n", $ansi, true);
            self::output("Please run database configuration first.\n", $ansi, true);
            return false;
        }

        // Try to use the Migration class directly if the app is bootstrapped
        if (class_exists('\\App') && is_callable(['\\App', 'get'])) {
            return self::runWithMigrationClass($ansi);
        }

        // Fall back to shelling out
        return self::runViaShell($ansi, $corePath, $rootPath);
    }

    /**
     * Run migrations using the Hubzero\Content\Migration class directly
     *
     * @param   bool  $ansi  Whether to use ANSI color output
     * @return  bool  True on success, false on failure
     */
    private static function runWithMigrationClass($ansi)
    {
        self::output("\n", $ansi);
        self::output("Running migrations...\n", $ansi);
        self::output("\n", $ansi);

        try {
            // Create migration instance
            $migration = new Migration();

            // Register callback for output
            $callback = function ($message, $type = null) use ($ansi) {
                self::output($message . "\n", $ansi, $type === 'error');
            };
            $migration->registerCallback('message', $callback);

            // Find all migration files
            // Note: errors are output via the registered callback
            if ($migration->find() === false) {
                self::output("\e[31mMigration find failed.\e[39m\n", $ansi, true);
                return false;
            }

            // Run migrations (direction=up, force=false, dryrun=false, listAll=false, logOnly=false)
            // Note: errors are output via the registered callback, so we don't need to
            // iterate over the log again here
            $result = $migration->migrate('up', false, false, false, false);

            if (!$result) {
                self::output("\e[31mMigration failed.\e[39m\n", $ansi, true);
                return false;
            }

            self::output("\n", $ansi);
            self::output("\e[32mDatabase migrations completed successfully!\e[39m\n", $ansi);
            return true;
        } catch (\Exception $e) {
            self::output("\e[31mMigration error: " . $e->getMessage() . "\e[39m\n", $ansi, true);
            return false;
        }
    }

    /**
     * Run migrations by shelling out to muse
     * Used when the application isn't fully bootstrapped (e.g., web installer)
     *
     * @param   bool    $ansi      Whether to use ANSI color output
     * @param   string  $corePath  Path to the core directory
     * @param   string  $rootPath  Path to the root directory
     * @return  bool    True on success, false on failure
     */
    private static function runViaShell($ansi, $corePath, $rootPath)
    {
        // Check for muse
        $musePath = $corePath . '/bin/muse';
        if (!file_exists($musePath)) {
            self::output("\n", $ansi, true);
            self::output("\e[31mMuse executable not found at {$musePath}\e[39m\n", $ansi, true);
            return false;
        }

        // Check for vendor autoloader (required for muse)
        $vendorAutoload = $corePath . '/vendor/autoload.php';
        if (!file_exists($vendorAutoload)) {
            self::output("\n", $ansi, true);
            self::output("\e[31mVendor autoloader not found.\e[39m\n", $ansi, true);
            self::output("Please run 'muse install vendor' first.\n", $ansi, true);
            return false;
        }

        self::output("\n", $ansi);
        self::output("Running migrations...\n", $ansi);
        self::output("\n", $ansi);

        // Build the migration command
        // -f = full run (not dry run)
        $ansiFlag = $ansi ? '' : ' --no-ansi';
        $command = sprintf(
            'cd %s && %s %s migration run -f%s 2>&1',
            escapeshellarg($rootPath),
            escapeshellarg(PHP_BINARY),
            escapeshellarg($musePath),
            $ansiFlag
        );

        // Open process with pipes for streaming output
        $descriptors = [
            0 => ['pipe', 'r'],  // stdin
            1 => ['pipe', 'w'],  // stdout
            2 => ['pipe', 'w'],  // stderr
        ];

        $process = proc_open($command, $descriptors, $pipes);

        if (!is_resource($process)) {
            self::output("\e[31mFailed to start migration process.\e[39m\n", $ansi, true);
            return false;
        }

        // Close stdin
        fclose($pipes[0]);

        // Read and display output in real-time
        stream_set_blocking($pipes[1], false);

        while (!feof($pipes[1])) {
            $line = fgets($pipes[1]);
            if ($line !== false) {
                echo $line;
            }
            usleep(10000);
        }

        fclose($pipes[1]);
        fclose($pipes[2]);

        $returnCode = proc_close($process);

        self::output("\n", $ansi);

        if ($returnCode === 0) {
            self::output("\e[32mDatabase migrations completed successfully!\e[39m\n", $ansi);
            return true;
        } else {
            self::output("\e[31mMigration process exited with code {$returnCode}.\e[39m\n", $ansi, true);
            self::output("Some migrations may have failed. Check the output above.\n", $ansi, true);
            return false;
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
