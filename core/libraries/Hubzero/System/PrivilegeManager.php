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
     * The account the process switched to for good, if it did
     */
    private ?string $switchedTo = null;

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
            // false when SUDO_USER names an account the system cannot resolve
            $this->sudoUserInfo = posix_getpwnam($this->sudoUser) ?: null;
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
        if (!$this->hasPosix || !function_exists('posix_seteuid') || $this->switchedTo !== null) {
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

        if (!posix_seteuid(self::ROOT_UID)) {
            return false;
        }

        $this->elevated = true;

        return posix_setegid($this->originalGid);
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
            // Load the user's supplementary groups before giving up root.
            // Setting only the primary group leaves the process holding root's
            // group list, so anything reachable through a secondary group --
            // which is how deployment permissions are usually granted -- comes
            // back as permission denied. initgroups() needs root itself, so it
            // has to run before the euid changes.
            if (
                function_exists('posix_initgroups')
                && posix_geteuid() === self::ROOT_UID
                && $this->sudoUser !== null
            ) {
                posix_initgroups($this->sudoUser, $this->sudoUserInfo['gid']);
            }

            $gidChanged = posix_setegid($this->sudoUserInfo['gid']);
            $uidChanged = posix_seteuid($this->sudoUserInfo['uid']);

            if ($uidChanged) {
                $this->elevated = false;
            }

            return $gidChanged && $uidChanged;
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
     * Become, for good, the account that owns a directory
     *
     * Files a hub's command line writes have to belong to the account that
     * owns the hub, or the web server cannot write them afterwards. Run as
     * root, muse switches to that account before it does anything, so
     * `sudo php core/bin/muse ...` works without naming it.
     *
     * The switch is complete: real, effective and saved IDs, and the
     * account's own supplementary groups. Changing only the effective ID is
     * not enough, because anything started through a shell (Composer, git)
     * goes back to the real ID, root, and leaves root's files behind. The
     * price is that the process cannot become root again.
     *
     * Nothing changes unless the process is root and the directory belongs
     * to another account that the system can name. A directory owned by root,
     * or by an account with no entry in the user database, is left alone:
     * without the account's groups, the process would keep root's.
     *
     * @param   string  $path  The directory whose owner to become
     * @return  string|null  The account switched to, or null if nothing changed
     */
    public function switchToOwnerOf(string $path): ?string
    {
        if (
            !$this->hasPosix
            || $this->originalUid !== self::ROOT_UID
            || $this->switchedTo !== null
            || !function_exists('posix_setuid')
            || !function_exists('posix_initgroups')
        ) {
            return null;
        }

        $uid = @fileowner($path);

        if ($uid === false || $uid === self::ROOT_UID) {
            return null;
        }

        $account = posix_getpwuid($uid);

        if (!$account) {
            return null;
        }

        // Groups first, then the group ID, then the user ID: once the user ID
        // changes, there is no permission left to change the others
        if (
            !posix_initgroups($account['name'], $account['gid'])
            || !posix_setgid($account['gid'])
            || !posix_setuid($uid)
        ) {
            return null;
        }

        // What the account's own programs expect to find, Composer's cache
        // among them
        putenv('HOME=' . $account['dir']);
        putenv('USER=' . $account['name']);
        putenv('LOGNAME=' . $account['name']);
        $_SERVER['HOME'] = $account['dir'];

        $this->elevated = false;
        $this->switchedTo = $account['name'];

        return $this->switchedTo;
    }

    /**
     * The account switchToOwnerOf() switched to, if it did
     *
     * @return string|null
     */
    public function getSwitchedTo(): ?string
    {
        return $this->switchedTo;
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
