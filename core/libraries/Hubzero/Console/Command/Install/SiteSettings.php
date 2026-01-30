<?php

/**
 * @package    framework
 * @copyright  Copyright (c) 2005-2025 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Hubzero\Console\Command\Install;

/**
 * Site settings helper class
 *
 * This class handles prompting for and configuring site settings during installation.
 * It can be used both by minimal muse (before autoloading) and full muse.
 **/
class SiteSettings
{
    /**
     * Common timezones for selection
     *
     * @var array
     **/
    private const COMMON_TIMEZONES = [
        'UTC'                    => 'UTC (Coordinated Universal Time)',
        'America/New_York'       => 'Eastern Time (US & Canada)',
        'America/Chicago'        => 'Central Time (US & Canada)',
        'America/Denver'         => 'Mountain Time (US & Canada)',
        'America/Los_Angeles'    => 'Pacific Time (US & Canada)',
        'America/Anchorage'      => 'Alaska',
        'Pacific/Honolulu'       => 'Hawaii',
        'Europe/London'          => 'London',
        'Europe/Paris'           => 'Paris, Berlin, Amsterdam',
        'Europe/Moscow'          => 'Moscow',
        'Asia/Tokyo'             => 'Tokyo',
        'Asia/Shanghai'          => 'Beijing, Shanghai',
        'Asia/Kolkata'           => 'Mumbai, New Delhi',
        'Australia/Sydney'       => 'Sydney',
    ];

    /**
     * Whether terminal has been modified (for cleanup)
     *
     * @var bool
     **/
    private static $terminalModified = false;

    /**
     * Load existing configuration from config files
     *
     * @param   string  $appPath  Path to the app directory
     * @return  array   Existing configuration values (empty array if none)
     */
    public static function loadExistingConfig($appPath)
    {
        $existing = [];

        // Load from app.php
        $appConfig = $appPath . '/config/app.php';
        if (file_exists($appConfig)) {
            $config = include $appConfig;
            if (is_array($config)) {
                if (!empty($config['sitename'])) {
                    $existing['sitename'] = $config['sitename'];
                }
                if (!empty($config['offset'])) {
                    $existing['offset'] = $config['offset'];
                }
                if (isset($config['live_site'])) {
                    $existing['live_site'] = $config['live_site'];
                }
            }
        }

        // Load from mail.php
        $mailConfig = $appPath . '/config/mail.php';
        if (file_exists($mailConfig)) {
            $config = include $mailConfig;
            if (is_array($config) && !empty($config['mailfrom'])) {
                $existing['mailfrom'] = $config['mailfrom'];
            }
        }

        // Load from meta.php
        $metaConfig = $appPath . '/config/meta.php';
        if (file_exists($metaConfig)) {
            $config = include $metaConfig;
            if (is_array($config) && !empty($config['MetaDesc'])) {
                $existing['MetaDesc'] = $config['MetaDesc'];
            }
        }

        return $existing;
    }

    /**
     * Configure site settings interactively
     *
     * @param   bool    $ansi      Whether to use ANSI color output
     * @param   string  $appPath   Path to the app directory
     * @param   array   $existing  Existing configuration to use as defaults
     * @return  array|null  Configuration array or null if cancelled
     */
    public static function configure($ansi = true, $appPath = null, $existing = null)
    {
        if ($appPath === null) {
            $appPath = defined('PATH_APP')
                ? PATH_APP
                : dirname(dirname(dirname(dirname(dirname(dirname(__DIR__)))))) . '/app';
        }

        // Load existing config if not provided
        if ($existing === null) {
            $existing = self::loadExistingConfig($appPath);
        }

        self::output("\n", $ansi);
        self::output("\e[33mSite Settings\e[39m\n", $ansi);
        self::output("-------------\n", $ansi);
        self::output("\n", $ansi);
        if (!empty($existing)) {
            self::output("Existing configuration found. Press Enter to keep current values.\n", $ansi);
        } else {
            self::output("Please provide the following information for your site.\n", $ansi);
        }
        self::output("Press Ctrl+C to cancel at any time.\n", $ansi);

        // Collect settings
        $settings = [];

        // Site name - use existing or default to "myhub"
        self::output("\n", $ansi);
        $settings['sitename'] = self::promptInput(
            "Site name",
            $existing['sitename'] ?? "myhub",
            $ansi
        );
        if ($settings['sitename'] === null) {
            return null;
        }

        // Admin email - use existing or default to "admin@localhost"
        self::output("\n", $ansi);
        $settings['mailfrom'] = self::promptEmail(
            "Admin email address",
            $existing['mailfrom'] ?? "admin@localhost",
            $ansi
        );
        if ($settings['mailfrom'] === null) {
            return null;
        }

        // Site URL - use existing or empty
        self::output("\n", $ansi);
        self::output("Enter the full URL where this site will be accessed.\n", $ansi);
        self::output("Leave blank to auto-detect (recommended for most setups).\n", $ansi);
        $settings['live_site'] = self::promptInput(
            "Site URL",
            $existing['live_site'] ?? "",
            $ansi,
            true  // Allow empty
        );
        if ($settings['live_site'] === null) {
            return null;
        }

        // Timezone - use existing or default to first option (UTC)
        self::output("\n", $ansi);
        $settings['offset'] = self::promptTimezone($ansi, $existing['offset'] ?? null);
        if ($settings['offset'] === null) {
            return null;
        }

        // Meta description - use existing or generate from sitename
        self::output("\n", $ansi);
        $defaultDesc = $existing['MetaDesc']
            ?? ($settings['sitename'] . ' - A HUBzero-powered hub for research and collaboration.');
        $settings['MetaDesc'] = self::promptInput(
            "Site description (for search engines)",
            $defaultDesc,
            $ansi
        );
        if ($settings['MetaDesc'] === null) {
            return null;
        }

        // Confirm settings
        self::output("\n", $ansi);
        self::output("\e[33mConfiguration Summary\e[39m\n", $ansi);
        self::output("---------------------\n", $ansi);
        self::output("  Site name:    " . $settings['sitename'] . "\n", $ansi);
        self::output("  Admin email:  " . $settings['mailfrom'] . "\n", $ansi);
        self::output("  Site URL:     " . ($settings['live_site'] ?: '(auto-detect)') . "\n", $ansi);
        self::output("  Timezone:     " . $settings['offset'] . "\n", $ansi);
        self::output("  Description:  " . self::truncate($settings['MetaDesc'], 50) . "\n", $ansi);
        self::output("\n", $ansi);

        if (!self::promptConfirm("Save these settings?", $ansi)) {
            self::output("\n", $ansi);
            self::output("Configuration cancelled.\n", $ansi);
            return null;
        }

        // Write configuration files
        self::output("\n", $ansi);

        // Write app.php
        if (!AppDirectory::writeAppConfig($ansi, $appPath, $settings)) {
            return null;
        }

        // Write other config files
        if (!AppDirectory::writeDefaultConfigs($ansi, $appPath, $settings)) {
            return null;
        }

        self::output("\n", $ansi);
        self::output("\e[32mSite settings saved successfully!\e[39m\n", $ansi);

        return $settings;
    }

    /**
     * Prompt for text input
     *
     * @param   string  $prompt      The prompt message
     * @param   string  $default     Default value
     * @param   bool    $ansi        Whether to use ANSI colors
     * @param   bool    $allowEmpty  Whether to allow empty input
     * @return  string|null  User input or null if cancelled
     **/
    private static function promptInput($prompt, $default = '', $ansi = true, $allowEmpty = false)
    {
        $defaultHint = $default !== '' ? " [{$default}]" : '';

        while (true) {
            echo "{$prompt}{$defaultHint}: ";

            $input = self::readInput();
            if ($input === null) {
                return null; // Ctrl+C
            }

            $input = trim($input);

            if ($input === '' && $default !== '') {
                return $default;
            }

            if ($input === '' && !$allowEmpty) {
                self::output("  \e[31mThis field is required.\e[39m\n", $ansi, true);
                continue;
            }

            return $input;
        }
    }

    /**
     * Prompt for email input with validation
     *
     * @param   string  $prompt   The prompt message
     * @param   string  $default  Default value
     * @param   bool    $ansi     Whether to use ANSI colors
     * @return  string|null  Valid email or null if cancelled
     **/
    private static function promptEmail($prompt, $default = '', $ansi = true)
    {
        $defaultHint = $default !== '' ? " [{$default}]" : '';

        while (true) {
            echo "{$prompt}{$defaultHint}: ";

            $input = self::readInput();
            if ($input === null) {
                return null; // Ctrl+C
            }

            $input = trim($input);

            if ($input === '' && $default !== '') {
                return $default;
            }

            if ($input === '') {
                self::output("  \e[31mEmail address is required.\e[39m\n", $ansi, true);
                continue;
            }

            // Email validation - allow localhost as a valid domain
            if (!self::isValidEmail($input)) {
                self::output("  \e[31mPlease enter a valid email address.\e[39m\n", $ansi, true);
                continue;
            }

            return $input;
        }
    }

    /**
     * Validate email address, allowing localhost as a valid domain
     *
     * @param   string  $email  Email address to validate
     * @return  bool    True if valid
     **/
    private static function isValidEmail($email)
    {
        // Standard email validation
        if (filter_var($email, FILTER_VALIDATE_EMAIL)) {
            return true;
        }

        // Also accept user@localhost format
        if (preg_match('/^[a-zA-Z0-9._%+-]+@localhost$/i', $email)) {
            return true;
        }

        return false;
    }

    /**
     * Prompt for timezone selection
     *
     * @param   bool         $ansi            Whether to use ANSI colors
     * @param   string|null  $existingTimezone  Existing timezone to pre-select
     * @return  string|null  Timezone identifier or null if cancelled
     **/
    private static function promptTimezone($ansi = true, $existingTimezone = null)
    {
        self::output("Select your timezone:\n", $ansi);
        self::output("\n", $ansi);

        // Build options array from timezones
        $timezones = self::COMMON_TIMEZONES;
        $keys = array_keys($timezones);
        $options = array_values($timezones);
        $options[] = 'Other (enter timezone manually)';

        // Find default selection index
        $defaultIndex = 0;
        if ($existingTimezone !== null) {
            $foundIndex = array_search($existingTimezone, $keys);
            if ($foundIndex !== false) {
                $defaultIndex = $foundIndex;
            } else {
                // Timezone not in common list - it's a custom one
                // Pre-select "Other" and we'll handle it
                $defaultIndex = count($keys); // "Other" option
            }
        }

        $selected = self::interactiveMenu($options, $defaultIndex, $ansi, false);

        if ($selected === -1) {
            return null; // Cancelled
        }

        // "Other" selected (last option)
        if ($selected === count($keys)) {
            return self::promptCustomTimezone($ansi, $existingTimezone);
        }

        $tz = $keys[$selected];
        self::output("\n", $ansi);
        self::output("  Selected: {$tz}\n", $ansi);
        return $tz;
    }

    /**
     * Prompt for custom timezone input
     *
     * @param   bool         $ansi              Whether to use ANSI colors
     * @param   string|null  $existingTimezone  Existing timezone to use as default
     * @return  string|null  Timezone identifier or null if cancelled
     **/
    private static function promptCustomTimezone($ansi = true, $existingTimezone = null)
    {
        self::output("\n", $ansi);
        self::output("Enter a valid PHP timezone identifier.\n", $ansi);
        self::output("See: https://www.php.net/manual/en/timezones.php\n", $ansi);
        self::output("\n", $ansi);

        // Only use existing as default if it's a valid timezone not in the common list
        $default = '';
        if ($existingTimezone !== null) {
            $validTimezones = timezone_identifiers_list();
            if (
                in_array($existingTimezone, $validTimezones)
                && !array_key_exists($existingTimezone, self::COMMON_TIMEZONES)
            ) {
                $default = $existingTimezone;
            }
        }

        $defaultHint = $default !== '' ? " [{$default}]" : '';

        while (true) {
            echo "Timezone{$defaultHint}: ";

            $input = self::readInput();
            if ($input === null) {
                return null;
            }

            $input = trim($input);

            if ($input === '' && $default !== '') {
                return $default;
            }

            if ($input === '') {
                self::output("  \e[31mTimezone is required.\e[39m\n", $ansi, true);
                continue;
            }

            // Validate timezone
            $validTimezones = timezone_identifiers_list();
            if (!in_array($input, $validTimezones)) {
                self::output(
                    "  \e[31mInvalid timezone. Please enter a valid PHP timezone identifier.\e[39m\n",
                    $ansi,
                    true
                );
                continue;
            }

            return $input;
        }
    }

    /**
     * Prompt for yes/no confirmation (default yes)
     *
     * @param   string  $prompt  The prompt message
     * @param   bool    $ansi    Whether to use ANSI colors
     * @return  bool    True for yes, false for no
     **/
    private static function promptConfirm($prompt, $ansi = true)
    {
        while (true) {
            echo "{$prompt} [Y/n] ";

            $input = self::readInput();
            if ($input === null) {
                return false; // Ctrl+C
            }

            $input = strtolower(trim($input));

            if ($input === '' || $input === 'y' || $input === 'yes') {
                return true;
            }

            if ($input === 'n' || $input === 'no') {
                return false;
            }

            self::output("  Please enter y/yes or n/no.\n", $ansi);
        }
    }

    /**
     * Read input from STDIN with Ctrl+C handling
     *
     * @return  string|null  User input or null if cancelled
     **/
    private static function readInput()
    {
        $input = fgets(STDIN);

        if ($input === false) {
            return null;
        }

        return rtrim($input, "\n\r");
    }

    /**
     * Truncate string for display
     *
     * @param   string  $text    Text to truncate
     * @param   int     $length  Maximum length
     * @return  string  Truncated text
     **/
    private static function truncate($text, $length = 50)
    {
        if (strlen($text) <= $length) {
            return $text;
        }

        return substr($text, 0, $length - 3) . '...';
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

    /**
     * Display an interactive menu with arrow key navigation
     *
     * @param   array  $options        Array of option labels
     * @param   int    $default        Default selected index
     * @param   bool   $ansi           Whether to use ANSI colors
     * @param   bool   $showBackspace  Whether to show backspace hint
     * @return  int    Selected index, or -1 if cancelled
     **/
    private static function interactiveMenu(array $options, $default = 0, $ansi = true, $showBackspace = true)
    {
        // Try interactive mode first
        if ($ansi && self::enableRawMode()) {
            return self::runInteractiveMenu($options, $default, $showBackspace);
        }

        // Fallback to numbered input
        return self::runNumberedMenu($options, $default, $ansi);
    }

    /**
     * Run the interactive arrow-key menu
     *
     * @param   array  $options        Array of option labels
     * @param   int    $default        Default selected index
     * @param   bool   $showBackspace  Whether to show backspace hint
     * @return  int    Selected index, or -1 if backspace/cancelled
     **/
    private static function runInteractiveMenu(array $options, $default = 0, $showBackspace = true)
    {
        $selected = $default;
        $count = count($options);

        // Hide cursor during menu selection
        echo "\033[?25l";

        // Draw initial menu
        self::drawMenu($options, $selected, $showBackspace);

        while (true) {
            $key = self::readKey();

            if ($key === 'up') {
                $selected = ($selected - 1 + $count) % $count;
                self::redrawMenu($options, $selected, $showBackspace);
            } elseif ($key === 'down') {
                $selected = ($selected + 1) % $count;
                self::redrawMenu($options, $selected, $showBackspace);
            } elseif ($key === 'enter') {
                echo "\r\033[K";
                echo "\033[?25h";
                self::restoreTerminal();
                return $selected;
            } elseif ($key === 'escape') {
                echo "\r\033[K";
                echo "\033[?25h";
                self::restoreTerminal();
                return -1;
            } elseif ($key === 'backspace' && $showBackspace) {
                echo "\r\033[K";
                echo "\033[?25h";
                self::restoreTerminal();
                return -1;
            }
        }
    }

    /**
     * Draw the menu options
     *
     * @param   array  $options        Array of option labels
     * @param   int    $selected       Currently selected index
     * @param   bool   $showBackspace  Whether to show backspace hint
     * @return  void
     **/
    private static function drawMenu(array $options, $selected, $showBackspace = true)
    {
        foreach ($options as $i => $label) {
            if ($i === $selected) {
                echo "  \033[32m❯ {$label}\033[39m\n";
            } else {
                echo "    {$label}\n";
            }
        }

        if ($showBackspace) {
            echo "\n  \033[90m↑/↓ navigate, Enter select, Backspace back, Esc cancel\033[39m";
        } else {
            echo "\n  \033[90m↑/↓ navigate, Enter select, Esc cancel\033[39m";
        }
    }

    /**
     * Redraw the menu (move cursor up and redraw)
     *
     * @param   array  $options        Array of option labels
     * @param   int    $selected       Currently selected index
     * @param   bool   $showBackspace  Whether to show backspace hint
     * @return  void
     **/
    private static function redrawMenu(array $options, $selected, $showBackspace = true)
    {
        echo "\r";
        $lines = count($options) + 1;
        echo "\033[{$lines}A";
        echo "\033[J";
        self::drawMenu($options, $selected, $showBackspace);
    }

    /**
     * Run the numbered fallback menu
     *
     * @param   array  $options  Array of option labels
     * @param   int    $default  Default selected index
     * @param   bool   $ansi     Whether to use ANSI colors
     * @return  int    Selected index
     **/
    private static function runNumberedMenu(array $options, $default = 0, $ansi = true)
    {
        foreach ($options as $i => $label) {
            $num = $i + 1;
            self::output("  [{$num}] {$label}\n", $ansi);
        }

        self::output("\n", $ansi);
        $defaultNum = $default + 1;

        while (true) {
            self::output("Select option [{$defaultNum}]: ", $ansi);
            $input = trim(fgets(STDIN));

            if ($input === '') {
                return $default;
            }

            $choice = (int) $input;

            if ($choice >= 1 && $choice <= count($options)) {
                return $choice - 1;
            }

            self::output("Invalid selection. Please enter 1-" . count($options) . ".\n\n", $ansi);
        }
    }

    /**
     * Try to enable raw terminal mode for interactive input
     *
     * @return  bool  True if successful, false otherwise
     **/
    private static function enableRawMode()
    {
        if (PHP_OS_FAMILY === 'Windows') {
            return false;
        }

        if (!function_exists('posix_isatty') || !posix_isatty(STDIN)) {
            return false;
        }

        $result = @system('stty -icanon -echo -isig 2>/dev/null', $retval);

        if ($retval !== 0) {
            return false;
        }

        self::$terminalModified = true;
        register_shutdown_function([self::class, 'restoreTerminal']);

        return true;
    }

    /**
     * Restore terminal to normal mode
     *
     * @return  void
     **/
    public static function restoreTerminal()
    {
        if (self::$terminalModified) {
            echo "\033[?25h";
            @system('stty sane 2>/dev/null');
            self::$terminalModified = false;
        }
    }

    /**
     * Read a single keypress and return action
     *
     * @return  string  'up', 'down', 'enter', 'escape', 'backspace', or 'other'
     **/
    private static function readKey()
    {
        $char = fread(STDIN, 1);

        if ($char === "\033") {
            $read = [STDIN];
            $write = null;
            $except = null;
            if (stream_select($read, $write, $except, 0, 50000) > 0) {
                $char .= fread(STDIN, 2);

                if ($char === "\033[A") {
                    return 'up';
                }
                if ($char === "\033[B") {
                    return 'down';
                }

                return 'other';
            }

            return 'escape';
        }

        if ($char === "\n" || $char === "\r") {
            return 'enter';
        }

        if ($char === "\x7f" || $char === "\x08") {
            return 'backspace';
        }

        if ($char === "\003") {
            return 'escape';
        }

        if ($char === 'j') {
            return 'down';
        }
        if ($char === 'k') {
            return 'up';
        }

        return 'other';
    }
}
