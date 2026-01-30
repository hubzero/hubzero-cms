<?php

/**
 * @package    framework
 * @copyright  Copyright (c) 2005-2025 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Hubzero\System;

/**
 * Privilege Manager for Unix/Linux environments
 *
 * Handles detection and management of user privileges, including:
 * - Detecting if running as root or via sudo
 * - Escalating to root privileges when needed
 * - Dropping back to unprivileged user
 *
 * This class is designed to work before autoloading is available,
 * so it has no external dependencies.
 *
 * Usage:
 *   $pm = PrivilegeManager::getInstance();
 *   if ($pm->canEscalate()) {
 *       $pm->escalate();
 *       // do privileged work
 *       $pm->drop();
 *   }
 *
 * Or with callback:
 *   $pm->withElevatedPrivileges(function() {
 *       // do privileged work
 *   });
 */
class PrivilegeManager
{
    /**
     * Root user ID
     */
    private const ROOT_UID = 0;

    /**
     * Singleton instance
     */
    private static ?PrivilegeManager $instance = null;

    /**
     * Original UID when muse was started
     */
    private int $originalUid;

    /**
     * Original GID when muse was started
     */
    private int $originalGid;

    /**
     * The sudo user if running via sudo
     */
    private ?string $sudoUser;

    /**
     * User info for sudo user (from posix_getpwnam)
     */
    private ?array $sudoUserInfo = null;

    /**
     * Whether we're currently elevated
     */
    private bool $elevated = false;

    /**
     * Whether POSIX functions are available
     */
    private bool $hasPosix;

    /**
     * Constructor - captures initial privilege state
     */
    public function __construct()
    {
        $this->hasPosix = function_exists('posix_getuid');

        if ($this->hasPosix) {
            $this->originalUid = posix_getuid();
            $this->originalGid = posix_getgid();
        } else {
            $this->originalUid = -1;
            $this->originalGid = -1;
        }

        $this->sudoUser = $this->detectSudoUser();

        if ($this->sudoUser !== null && $this->hasPosix) {
            $this->sudoUserInfo = posix_getpwnam($this->sudoUser);
        }
    }

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
     * Check if running as root (UID 0)
     *
     * @return bool
     */
    public function isRoot(): bool
    {
        return $this->originalUid === self::ROOT_UID;
    }

    /**
     * Check if running via sudo
     *
     * @return bool
     */
    public function isSudo(): bool
    {
        return $this->sudoUser !== null;
    }

    /**
     * Check if we have the ability to escalate privileges
     *
     * This is true when:
     * - We were started as root (real UID = 0), AND
     * - POSIX functions are available
     *
     * @return bool
     */
    public function canEscalate(): bool
    {
        if (!$this->hasPosix || !function_exists('posix_seteuid')) {
            return false;
        }

        return $this->originalUid === self::ROOT_UID;
    }

    /**
     * Check if currently running with elevated privileges
     *
     * @return bool
     */
    public function isElevated(): bool
    {
        return $this->elevated;
    }

    /**
     * Escalate to root privileges
     *
     * @return bool True if successfully escalated, false if not available
     */
    public function escalate(): bool
    {
        if (!$this->canEscalate()) {
            return false;
        }

        if ($this->elevated) {
            return true; // Already elevated
        }

        posix_seteuid(self::ROOT_UID);
        posix_setegid($this->originalGid);
        $this->elevated = true;

        return true;
    }

    /**
     * Drop back to unprivileged user
     *
     * If running via sudo, drops to the original sudo user.
     * Otherwise, this is a no-op.
     *
     * @return bool True if successfully dropped, false if not available
     */
    public function drop(): bool
    {
        if (!$this->hasPosix || !function_exists('posix_seteuid')) {
            return false;
        }

        if ($this->sudoUserInfo !== null) {
            posix_setegid($this->sudoUserInfo['gid']);
            posix_seteuid($this->sudoUserInfo['uid']);
            $this->elevated = false;
            return true;
        }

        return false;
    }

    /**
     * Execute a callback with elevated privileges
     *
     * Automatically escalates before the callback and drops after.
     * If escalation is not available, the callback still runs.
     *
     * @param  callable  $callback  The function to execute
     * @return mixed  The callback's return value
     */
    public function withElevatedPrivileges(callable $callback): mixed
    {
        $wasElevated = $this->elevated;
        $didEscalate = false;

        if ($this->canEscalate() && !$wasElevated) {
            $this->escalate();
            $didEscalate = true;
        }

        try {
            return $callback();
        } finally {
            if ($didEscalate) {
                $this->drop();
            }
        }
    }

    /**
     * Get the sudo user name
     *
     * @return string|null  The username or null if not running via sudo
     */
    public function getSudoUser(): ?string
    {
        return $this->sudoUser;
    }

    /**
     * Get the sudo user info (from posix_getpwnam)
     *
     * @return array|null  User info array or null
     */
    public function getSudoUserInfo(): ?array
    {
        return $this->sudoUserInfo;
    }

    /**
     * Get the original UID
     *
     * @return int
     */
    public function getOriginalUid(): int
    {
        return $this->originalUid;
    }

    /**
     * Get the original GID
     *
     * @return int
     */
    public function getOriginalGid(): int
    {
        return $this->originalGid;
    }

    /**
     * Get the current effective UID
     *
     * @return int
     */
    public function getCurrentUid(): int
    {
        if (!$this->hasPosix) {
            return -1;
        }
        return posix_geteuid();
    }

    /**
     * Check if POSIX functions are available
     *
     * @return bool
     */
    public function hasPosixSupport(): bool
    {
        return $this->hasPosix;
    }

    /**
     * Initialize privilege state for a sudo environment
     *
     * This should be called early in muse startup to drop to the
     * original user while preserving the ability to escalate.
     *
     * @return bool True if privileges were dropped
     */
    public function initializeSudoEnvironment(): bool
    {
        if (!$this->isSudo() || !$this->hasPosix) {
            return false;
        }

        return $this->drop();
    }

    /**
     * Detect the sudo user from environment
     *
     * @return string|null  The sudo user or null
     */
    private function detectSudoUser(): ?string
    {
        $sudoUser = getenv('SUDO_USER');

        if ($sudoUser === false || $sudoUser === '') {
            return null;
        }

        return $sudoUser;
    }
}
