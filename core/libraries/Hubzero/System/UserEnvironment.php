<?php

/**
 * @package    framework
 * @copyright  Copyright (c) 2005-2025 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Hubzero\System;

/**
 * User Environment detection utility
 *
 * Provides information about the current operating system,
 * user environment, and system capabilities.
 *
 * This class is designed to work before autoloading is available,
 * so it has no external dependencies.
 */
class UserEnvironment
{
    /**
     * Windows OS prefix
     */
    private const WINDOWS_PREFIX = 'WIN';

    /**
     * Singleton instance
     */
    private static ?UserEnvironment $instance = null;

    /**
     * Cached OS name
     */
    private ?string $osName = null;

    /**
     * Get singleton instance
     *
     * @return self
     */
    public static function getInstance(): self
    {
        if (self::$instance === null) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    /**
     * Reset singleton instance (primarily for testing)
     *
     * @return void
     */
    public static function resetInstance(): void
    {
        self::$instance = null;
    }

    /**
     * Get the operating system name
     *
     * @return string  The PHP_OS value
     */
    public function getOperatingSystem(): string
    {
        if ($this->osName === null) {
            $this->osName = PHP_OS;
        }
        return $this->osName;
    }

    /**
     * Check if running on Windows
     *
     * @return bool
     */
    public function isWindows(): bool
    {
        return strtoupper(substr($this->getOperatingSystem(), 0, 3)) === self::WINDOWS_PREFIX;
    }

    /**
     * Check if running on a Unix-like system (Linux, macOS, BSD, etc.)
     *
     * @return bool
     */
    public function isUnix(): bool
    {
        return !$this->isWindows();
    }

    /**
     * Check if running on Linux
     *
     * @return bool
     */
    public function isLinux(): bool
    {
        return strtoupper(substr($this->getOperatingSystem(), 0, 5)) === 'LINUX';
    }

    /**
     * Check if running on macOS
     *
     * @return bool
     */
    public function isMacOS(): bool
    {
        return strtoupper(substr($this->getOperatingSystem(), 0, 6)) === 'DARWIN';
    }

    /**
     * Check if POSIX functions are available
     *
     * @return bool
     */
    public function hasPosixSupport(): bool
    {
        return function_exists('posix_getuid');
    }

    /**
     * Check if the system is supported for HUBzero
     *
     * @return array  Result with 'supported' and 'message' keys
     */
    public function checkSupported(): array
    {
        if ($this->isWindows()) {
            return [
                'supported' => false,
                'os' => $this->getOperatingSystem(),
                'message' => 'HUBzero does not support Windows. Please use Linux or macOS.',
                'suggestions' => [
                    'Use Windows Subsystem for Linux (WSL)',
                    'Install on a Linux server (Ubuntu, Debian, RHEL, Rocky Linux)',
                    'Use a Linux virtual machine',
                ],
            ];
        }

        return [
            'supported' => true,
            'os' => $this->getOperatingSystem(),
            'message' => 'Operating system is supported.',
        ];
    }

    /**
     * Get current user information
     *
     * @return array|null  User info from posix_getpwuid or null if unavailable
     */
    public function getCurrentUser(): ?array
    {
        if (!$this->hasPosixSupport()) {
            return null;
        }

        $uid = posix_geteuid();
        return posix_getpwuid($uid) ?: null;
    }

    /**
     * Get the current username
     *
     * @return string|null  Username or null if unavailable
     */
    public function getCurrentUsername(): ?string
    {
        $user = $this->getCurrentUser();
        return $user['name'] ?? null;
    }

    /**
     * Get user information by username
     *
     * @param  string  $username  The username to look up
     * @return array|null  User info or null if not found
     */
    public function getUserByName(string $username): ?array
    {
        if (!$this->hasPosixSupport()) {
            return null;
        }

        return posix_getpwnam($username) ?: null;
    }

    /**
     * Check if running in a CLI environment
     *
     * @return bool
     */
    public function isCli(): bool
    {
        return php_sapi_name() === 'cli' || defined('STDIN');
    }

    /**
     * Check if running interactively (with a TTY)
     *
     * @return bool
     */
    public function isInteractive(): bool
    {
        if (!$this->isCli()) {
            return false;
        }

        // Check if STDIN is a TTY
        if (function_exists('posix_isatty')) {
            return posix_isatty(STDIN);
        }

        // Fallback: check if STDIN is defined
        return defined('STDIN');
    }

    /**
     * Get the home directory for the current user
     *
     * @return string|null  Home directory path or null if unavailable
     */
    public function getHomeDirectory(): ?string
    {
        // Try environment variable first
        $home = getenv('HOME');
        if ($home !== false && $home !== '') {
            return $home;
        }

        // Try POSIX
        $user = $this->getCurrentUser();
        if ($user !== null && isset($user['dir'])) {
            return $user['dir'];
        }

        return null;
    }

    /**
     * Render an error message for unsupported OS
     *
     * @param  bool  $ansi  Whether to use ANSI colors
     * @return string  The formatted error message
     */
    public function renderUnsupportedOsError(bool $ansi = true): string
    {
        $check = $this->checkSupported();

        if ($check['supported']) {
            return '';
        }

        $output = "\n";

        if ($ansi) {
            $output .= "\033[31m" . str_repeat('=', 60) . "\033[39m\n";
            $output .= "\033[31m  UNSUPPORTED OPERATING SYSTEM\033[39m\n";
            $output .= "\033[31m" . str_repeat('=', 60) . "\033[39m\n";
        } else {
            $output .= str_repeat('=', 60) . "\n";
            $output .= "  UNSUPPORTED OPERATING SYSTEM\n";
            $output .= str_repeat('=', 60) . "\n";
        }

        $output .= "\n";
        $output .= "HUBzero CMS is designed for Unix-like operating systems\n";

        if ($ansi) {
            $output .= "and \033[1mcannot run on Windows\033[0m.\n";
        } else {
            $output .= "and cannot run on Windows.\n";
        }

        $output .= "\n";
        $output .= "The platform relies on:\n";
        $output .= "  - POSIX features (posix_getuid, posix_seteuid, etc.)\n";
        $output .= "  - Unix file permissions and ownership\n";
        $output .= "  - System utilities not available on Windows\n";
        $output .= "\n";

        if ($ansi) {
            $output .= "\033[33mSupported Operating Systems:\033[39m\n";
        } else {
            $output .= "Supported Operating Systems:\n";
        }

        $output .= "  - Linux (Ubuntu, Debian, RHEL, CentOS, Rocky Linux)\n";
        $output .= "  - macOS (for development environments)\n";
        $output .= "\n";
        $output .= "Please install HUBzero on a supported Linux server\n";
        $output .= "or use Windows Subsystem for Linux (WSL).\n";
        $output .= "\n";
        $output .= "Detected OS: " . $check['os'] . "\n";
        $output .= "\n";

        return $output;
    }
}
