<?php

/**
 * Storage Availability Check
 *
 * Checks if a secure storage directory can be created outside the document root.
 * Used by both the bootstrap installer and the main web installer.
 *
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2025 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Bootstrap\Install\Web;

/**
 * Storage availability checker
 *
 * Verifies that a secure directory can be created outside the document root
 * for storing sensitive installation data.
 */
class StorageCheck
{
    /**
     * Server-side storage directory name
     */
    private const STORAGE_DIR = '.hubzero-install-security';

    /**
     * Document root path
     *
     * @var string
     */
    private $docRoot;

    /**
     * Constructor
     *
     * @param  string|null  $docRoot  Document root path
     */
    public function __construct(?string $docRoot = null)
    {
        $this->docRoot = $docRoot ?? $this->detectDocRoot();
    }

    /**
     * Detect the document root
     *
     * @return string
     */
    private function detectDocRoot(): string
    {
        if (defined('PATH_ROOT')) {
            return PATH_ROOT;
        }
        return dirname($_SERVER['DOCUMENT_ROOT'] ?? getcwd());
    }

    /**
     * Check if secure storage is available
     *
     * This method checks if a secure storage directory can be created
     * WITHOUT throwing an exception. Use this to display a friendly error
     * if storage is not available.
     *
     * @return array ['available' => bool, 'path' => string|null, 'tried' => array, 'docRoot' => string]
     */
    public function check(): array
    {
        $triedPaths = [];

        // Priority 1: Home directory of PHP process owner
        $homeDir = $this->getProcessOwnerHomeDir();
        if ($homeDir !== null) {
            $homePath = $homeDir . '/' . self::STORAGE_DIR;

            // SECURITY: Verify home dir is not under document root
            if ($this->isUnderDocRoot($homeDir)) {
                $triedPaths[] = [
                    'location' => 'Home directory',
                    'path' => $homeDir,
                    'status' => 'rejected',
                    'reason' => 'Path is under the document root (security risk)'
                ];
            } elseif ($this->tryCreateDir($homePath)) {
                return [
                    'available' => true,
                    'path' => $homePath,
                    'tried' => $triedPaths,
                    'docRoot' => $this->docRoot
                ];
            } else {
                $triedPaths[] = [
                    'location' => 'Home directory',
                    'path' => $homeDir,
                    'status' => 'failed',
                    'reason' => 'Directory is not writable by the web server'
                ];
            }
        } else {
            $triedPaths[] = [
                'location' => 'Home directory',
                'path' => null,
                'status' => 'unavailable',
                'reason' => 'Could not determine PHP process owner\'s home directory'
            ];
        }

        // Priority 2: PHP session save path
        $sessionPath = session_save_path();
        if (!empty($sessionPath) && is_dir($sessionPath)) {
            // SECURITY: Verify session path is not under document root
            if ($this->isUnderDocRoot($sessionPath)) {
                $triedPaths[] = [
                    'location' => 'Session path',
                    'path' => $sessionPath,
                    'status' => 'rejected',
                    'reason' => 'Path is under the document root (security risk)'
                ];
            } else {
                $sessionDir = $sessionPath . '/' . self::STORAGE_DIR;
                if ($this->tryCreateDir($sessionDir)) {
                    return [
                        'available' => true,
                        'path' => $sessionDir,
                        'tried' => $triedPaths,
                        'docRoot' => $this->docRoot
                    ];
                } else {
                    $triedPaths[] = [
                        'location' => 'Session path',
                        'path' => $sessionPath,
                        'status' => 'failed',
                        'reason' => 'Directory is not writable by the web server'
                    ];
                }
            }
        } else {
            $triedPaths[] = [
                'location' => 'Session path',
                'path' => $sessionPath ?: null,
                'status' => 'unavailable',
                'reason' => empty($sessionPath)
                    ? 'PHP session.save_path is not configured'
                    : 'Path does not exist or is not a directory'
            ];
        }

        // No secure location available
        return [
            'available' => false,
            'path' => null,
            'tried' => $triedPaths,
            'docRoot' => $this->docRoot
        ];
    }

    /**
     * Get the home directory of the PHP process owner
     *
     * @return string|null
     */
    private function getProcessOwnerHomeDir(): ?string
    {
        // Try posix functions first (most reliable on Unix)
        if (function_exists('posix_getpwuid') && function_exists('posix_geteuid')) {
            $userInfo = posix_getpwuid(posix_geteuid());
            if ($userInfo && !empty($userInfo['dir']) && is_dir($userInfo['dir'])) {
                return $userInfo['dir'];
            }
        }

        // Try HOME environment variable
        $home = getenv('HOME');
        if ($home && is_dir($home)) {
            return $home;
        }

        // Try USERPROFILE for Windows (though Windows is not supported)
        $userProfile = getenv('USERPROFILE');
        if ($userProfile && is_dir($userProfile)) {
            return $userProfile;
        }

        return null;
    }

    /**
     * Check if a path is under the document root
     *
     * Uses realpath to resolve symlinks and ensure accurate comparison.
     *
     * @param  string  $path
     * @return bool
     */
    private function isUnderDocRoot(string $path): bool
    {
        // Resolve both paths to handle symlinks
        $realDocRoot = realpath($this->docRoot);
        $realPath = realpath($path);

        // If either path doesn't exist, try to resolve parent directories
        if ($realDocRoot === false) {
            $realDocRoot = $this->docRoot;
        }
        if ($realPath === false) {
            // Path doesn't exist yet, check parent
            $realPath = realpath(dirname($path));
            if ($realPath === false) {
                $realPath = $path;
            }
        }

        // Normalize paths (remove trailing slashes, ensure consistent format)
        $realDocRoot = rtrim(str_replace('\\', '/', $realDocRoot), '/');
        $realPath = rtrim(str_replace('\\', '/', $realPath), '/');

        // Check if path starts with docroot
        // Must match exactly or be followed by a slash to avoid false positives
        // e.g., /var/www should not match /var/www-backup
        if ($realPath === $realDocRoot) {
            return true;
        }

        return strpos($realPath, $realDocRoot . '/') === 0;
    }

    /**
     * Try to create a directory
     *
     * @param  string  $dir
     * @return bool
     */
    private function tryCreateDir(string $dir): bool
    {
        if (is_dir($dir) && is_writable($dir)) {
            return true;
        }

        $parent = dirname($dir);
        if (!is_writable($parent)) {
            return false;
        }

        return @mkdir($dir, 0700, true);
    }

    /**
     * Get the document root
     *
     * @return string
     */
    public function getDocRoot(): string
    {
        return $this->docRoot;
    }
}
