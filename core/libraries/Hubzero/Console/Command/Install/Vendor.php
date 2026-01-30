<?php

/**
 * @package    framework
 * @copyright  Copyright (c) 2005-2025 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Hubzero\Console\Command\Install;

/**
 * Vendor installation helper class
 *
 * This class handles composer dependency installation with formatted output.
 * It can be used both by minimal muse (before autoloading) and full muse.
 **/
class Vendor
{
    /**
     * Install composer vendor dependencies
     *
     * @param   bool    $ansi       Whether to use ANSI color output
     * @param   bool    $quiet      Suppress non-error output
     * @param   string  $corePath   Path to the core directory
     * @return  bool    True on success, false on failure
     */
    public static function install($ansi = true, $quiet = false, $corePath = null)
    {
        if ($corePath === null) {
            $corePath = defined('PATH_CORE') ? PATH_CORE : dirname(dirname(dirname(dirname(dirname(__DIR__)))));
        }

        $binPath = $corePath . '/bin';
        $vendorPath = $corePath . '/vendor/autoload.php';
        $composerPath = $binPath . '/composer';

        // Check for unzip command (required by Composer)
        $unzipPath = trim(shell_exec('which unzip 2>/dev/null') ?? '');
        if (empty($unzipPath)) {
            self::output("\n", $ansi, true);
            self::output("\e[37;41mERROR:\e[39;49m unzip command not found.\n", $ansi, true);
            self::output("The unzip command is required for extracting packages.\n", $ansi, true);
            self::output("Please install unzip and try again.\n", $ansi, true);
            self::output("\n", $ansi, true);
            return false;
        }

        // Check if composer exists
        if (!file_exists($composerPath)) {
            self::output("\n", $ansi, true);
            self::output("\e[37;41mERROR:\e[39;49m {$composerPath}: No such file.\n", $ansi, true);
            self::output(
                "Please install composer dependencies manually: composer install -d {$corePath}\n",
                $ansi,
                true
            );
            self::output("\n", $ansi, true);
            return false;
        }

        // Show installation message
        if (!$quiet) {
            self::output("\n", $ansi);
            if (!file_exists($vendorPath)) {
                self::output("\e[33mComposer dependencies not found. Installing...\e[39m\n", $ansi);
            } else {
                self::output("\e[33mInstalling composer dependencies...\e[39m\n", $ansi);
            }
            self::output("\n", $ansi);
        }

        // Build composer command
        $composerOptions = $ansi ? " --ansi" : " --no-ansi";

        // Run composer and capture output line by line for reformatting
        $cmd = $composerPath . " install" . $composerOptions . " -d " . $corePath . " 2>&1";
        $handle = popen($cmd, 'r');

        if (!$handle) {
            self::output("\n", $ansi, true);
            self::output("\e[37;41mERROR:\e[39;49m Failed to execute composer.\n", $ansi, true);
            self::output("\n", $ansi, true);
            return false;
        }

        while (($line = fgets($handle)) !== false) {
            // Strip ANSI escape sequences for pattern matching
            $plainLine = preg_replace('/\e\[[0-9;]*m/', '', $line);

            // Filter out composer fund advertisement lines
            if (
                strpos($plainLine, 'composer fund') !== false ||
                strpos($plainLine, 'are looking for funding') !== false
            ) {
                continue;
            }

            // Add blank line before certain lines for readability
            if (
                strpos($plainLine, 'Generating autoload') !== false ||
                strpos($plainLine, 'Using config file:') !== false ||
                strpos($plainLine, 'Nothing to install') !== false
            ) {
                echo "\n";
            }

            // Remove "> " prefix from script output lines
            if (strpos($plainLine, '> ') === 0) {
                $line = preg_replace('/^(\e\[[0-9;]*m)?> /', '$1', $line);
            }

            echo $line;

            // Add blank line after "Package operations:" and "Generating autoload" for readability
            if (
                strpos($plainLine, 'Package operations:') !== false ||
                strpos($plainLine, 'Generating autoload') !== false
            ) {
                echo "\n";
            }
        }

        $returnCode = pclose($handle);

        if ($returnCode !== 0) {
            self::output("\n", $ansi, true);
            self::output("\e[37;41mERROR:\e[39;49m Composer install failed.\n", $ansi, true);
            self::output("\n", $ansi, true);
            return false;
        }

        self::output("\n", $ansi);
        self::output("\e[32mComposer dependencies installed successfully.\e[39m\n", $ansi);
        self::output("\n", $ansi);

        return true;
    }

    /**
     * Output helper that handles ANSI stripping
     *
     * @param   string  $text   Text to output
     * @param   bool    $ansi   Whether to use ANSI colors
     * @param   bool    $error  Whether this is an error message (always show)
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
