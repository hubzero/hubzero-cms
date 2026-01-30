<?php

/**
 * Security Guard for Web Installation Wizard
 *
 * Protects the installer from unauthorized access by requiring proof of
 * file system ownership before allowing installation to proceed.
 *
 * Security model:
 * - Tokens are stored SERVER-SIDE in a secure file, NOT in sessions
 * - Session manipulation cannot bypass verification
 * - Each verification challenge is cryptographically bound to the session
 * - Rate limiting and lockouts are enforced server-side
 *
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2025 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Bootstrap\Install\Web;

/**
 * Security verification for the web installer
 */
class SecurityGuard
{
    /**
     * Characters used for readable tokens (excludes ambiguous: 0/O, 1/l/I, 5/S, 2/Z)
     * Using uppercase only for clarity
     */
    private const READABLE_CHARS = 'ABCDEFGHJKMNPQRTUVWXY346789';

    /**
     * Token length (filename will be hubzero-verify-XXXXXXXX.txt)
     */
    private const TOKEN_LENGTH = 8;

    /**
     * Maximum age of verification file in seconds (10 minutes)
     */
    private const MAX_FILE_AGE = 600;

    /**
     * Maximum failed verification attempts before lockout
     */
    private const MAX_ATTEMPTS = 10;

    /**
     * Lockout duration in seconds (15 minutes)
     */
    private const LOCKOUT_DURATION = 900;

    /**
     * Session key prefix
     */
    private const SESSION_PREFIX = 'security_guard_';

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
     * Server secret (loaded/generated once)
     *
     * @var string|null
     */
    private $serverSecret = null;

    /**
     * Resolved storage directory path
     *
     * @var string|null
     */
    private $storageDirPath = null;

    /**
     * Constructor
     *
     * @param  string|null  $docRoot  Document root path (defaults to PATH_ROOT)
     */
    public function __construct(?string $docRoot = null)
    {
        $this->docRoot = $docRoot ?? (defined('PATH_ROOT') ? PATH_ROOT : dirname($_SERVER['DOCUMENT_ROOT']));
    }

    /**
     * Check if secure storage is available
     *
     * This method checks if a secure storage directory can be created
     * WITHOUT throwing an exception. Use this to display a friendly error
     * before the verify step if storage is not available.
     *
     * Delegates to StorageCheck class (shared with bootstrap installer).
     *
     * @return array ['available' => bool, 'path' => string|null, 'tried' => array, 'docRoot' => string]
     */
    public function checkStorageAvailability(): array
    {
        $checker = new StorageCheck($this->docRoot);
        return $checker->check();
    }

    /**
     * Get the server-side storage directory path
     *
     * SECURITY: Stored OUTSIDE document root to prevent web access even if
     * Apache .htaccess is not honored.
     *
     * Priority:
     * 1. Home directory of PHP process owner (most secure, user-specific)
     * 2. PHP session save path (always outside docroot)
     *
     * @return string
     */
    private function getStorageDir(): string
    {
        if ($this->storageDirPath !== null) {
            return $this->storageDirPath;
        }

        $triedPaths = [];

        // Priority 1: Home directory of PHP process owner
        $homeDir = $this->getProcessOwnerHomeDir();
        if ($homeDir !== null) {
            $homePath = $homeDir . '/' . self::STORAGE_DIR;

            // SECURITY: Verify home dir is not under document root
            if ($this->isUnderDocRoot($homeDir)) {
                $triedPaths[] = "home directory ({$homeDir}) - REJECTED: under document root";
            } elseif ($this->tryCreateDir($homePath)) {
                $this->storageDirPath = $homePath;
                return $this->storageDirPath;
            } else {
                $triedPaths[] = "home directory ({$homeDir}) - not writable";
            }
        } else {
            $triedPaths[] = "home directory - not available";
        }

        // Priority 2: PHP session save path (always outside docroot)
        $sessionPath = session_save_path();
        if (!empty($sessionPath) && is_dir($sessionPath)) {
            // SECURITY: Verify session path is not under document root
            if ($this->isUnderDocRoot($sessionPath)) {
                $triedPaths[] = "session path ({$sessionPath}) - REJECTED: under document root";
            } else {
                $sessionDir = $sessionPath . '/' . self::STORAGE_DIR;
                if ($this->tryCreateDir($sessionDir)) {
                    $this->storageDirPath = $sessionDir;
                    return $this->storageDirPath;
                } else {
                    $triedPaths[] = "session path ({$sessionPath}) - not writable";
                }
            }
        } else {
            $triedPaths[] = "session path - " . (empty($sessionPath) ? "not set" : "not a directory");
        }

        // No secure location available
        throw new \RuntimeException(
            'Unable to create secure storage directory outside document root. ' .
            'Tried: ' . implode('; ', $triedPaths) . '. ' .
            'Document root: ' . $this->docRoot . '. ' .
            'Please ensure the PHP process owner has a writable home directory outside the web root.'
        );
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

        // Try USERPROFILE for Windows
        $userProfile = getenv('USERPROFILE');
        if ($userProfile && is_dir($userProfile)) {
            return $userProfile;
        }

        // Try to construct from HOMEDRIVE + HOMEPATH (Windows)
        $homeDrive = getenv('HOMEDRIVE');
        $homePath = getenv('HOMEPATH');
        if ($homeDrive && $homePath) {
            $winHome = $homeDrive . $homePath;
            if (is_dir($winHome)) {
                return $winHome;
            }
        }

        return null;
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
     * Ensure storage directory exists with proper permissions
     *
     * @return bool
     */
    private function ensureStorageDir(): bool
    {
        $dir = $this->getStorageDir();

        if (!is_dir($dir)) {
            if (!@mkdir($dir, 0700, true)) {
                return false;
            }
        }

        // Add protections if inside docroot
        if (strpos($dir, $this->docRoot) === 0) {
            $this->ensureStorageDirProtected($dir);
        }

        return true;
    }

    /**
     * Add web-access protections to a directory inside docroot
     *
     * @param  string  $dir
     * @return void
     */
    private function ensureStorageDirProtected(string $dir): void
    {
        if (!is_dir($dir)) {
            @mkdir($dir, 0700, true);
        }

        // .htaccess for Apache
        $htaccess = $dir . '/.htaccess';
        if (!file_exists($htaccess)) {
            $content = "# Deny all access\n";
            $content .= "<IfModule mod_authz_core.c>\n";
            $content .= "    Require all denied\n";
            $content .= "</IfModule>\n";
            $content .= "<IfModule !mod_authz_core.c>\n";
            $content .= "    Order deny,allow\n";
            $content .= "    Deny from all\n";
            $content .= "</IfModule>\n";
            file_put_contents($htaccess, $content);
        }

        // index.html to prevent directory listing
        $index = $dir . '/index.html';
        if (!file_exists($index)) {
            file_put_contents($index, '');
        }
    }

    /**
     * Get or create server secret
     *
     * SECURITY: The secret is stored in a PHP file format. Even if accessed
     * via web (misconfigured server), it executes as PHP and returns nothing.
     * The actual secret is never exposed in HTTP responses.
     *
     * @return string
     */
    private function getServerSecret(): string
    {
        if ($this->serverSecret !== null) {
            return $this->serverSecret;
        }

        $this->ensureStorageDir();
        $secretFile = $this->getStorageDir() . '/secret.php';

        // Try to load existing secret
        if (file_exists($secretFile)) {
            $this->serverSecret = $this->loadSecretFromPhpFile($secretFile);
            if ($this->serverSecret !== null && strlen($this->serverSecret) >= 32) {
                return $this->serverSecret;
            }
        }

        // Generate new secret
        $this->serverSecret = bin2hex(random_bytes(32));
        $this->saveSecretToPhpFile($secretFile, $this->serverSecret);

        return $this->serverSecret;
    }

    /**
     * Load secret from PHP file format
     *
     * @param  string  $file
     * @return string|null
     */
    private function loadSecretFromPhpFile(string $file): ?string
    {
        if (!file_exists($file)) {
            return null;
        }

        $content = file_get_contents($file);

        // Extract secret from PHP file format
        // Expected format: return 'SECRET';
        if (preg_match("/return\s+'([a-f0-9]{64})'/", $content, $matches)) {
            return $matches[1];
        }

        return null;
    }

    /**
     * Save secret to PHP file format
     *
     * SECURITY: PHP format ensures that even if web-accessible, the secret
     * cannot be read via HTTP - the PHP just executes and returns nothing
     * to the browser.
     *
     * @param  string  $file
     * @param  string  $secret
     * @return bool
     */
    private function saveSecretToPhpFile(string $file, string $secret): bool
    {
        $content = "<?php\n";
        $content .= "/**\n";
        $content .= " * HUBzero Installation Security Secret\n";
        $content .= " * DO NOT DELETE - Required for installation security\n";
        $content .= " * DO NOT SHARE - This file should never be accessible via web\n";
        $content .= " * Generated: " . date('Y-m-d H:i:s') . "\n";
        $content .= " */\n";
        $content .= "if (php_sapi_name() !== 'cli' && !defined('HUBZERO_INSTALL')) {\n";
        $content .= "    http_response_code(403); exit;\n";
        $content .= "}\n";
        $content .= "return '" . $secret . "';\n";

        $result = file_put_contents($file, $content);

        if ($result !== false) {
            chmod($file, 0600);
        }

        return $result !== false;
    }

    /**
     * Get the challenge ID for the current session
     *
     * This binds the challenge to the specific session in a way that
     * cannot be forged by manipulating session data.
     *
     * @return string
     */
    private function getChallengeId(): string
    {
        $sessionId = session_id();
        $secret = $this->getServerSecret();

        // HMAC ensures this cannot be forged without knowing the server secret
        return hash_hmac('sha256', $sessionId, $secret);
    }

    /**
     * Get the path to the server-side challenge file
     *
     * @return string
     */
    private function getChallengeFilePath(): string
    {
        return $this->getStorageDir() . '/challenge-' . substr($this->getChallengeId(), 0, 16) . '.json';
    }

    /**
     * Load server-side challenge data
     *
     * @return array|null
     */
    private function loadChallengeData(): ?array
    {
        $file = $this->getChallengeFilePath();

        if (!file_exists($file)) {
            return null;
        }

        $data = json_decode(file_get_contents($file), true);

        if (!is_array($data)) {
            return null;
        }

        // Verify the challenge hasn't been tampered with
        if (!isset($data['signature'])) {
            return null;
        }

        $signature = $data['signature'];
        unset($data['signature']);

        $expectedSignature = hash_hmac('sha256', json_encode($data), $this->getServerSecret());

        if (!hash_equals($expectedSignature, $signature)) {
            // Tampered data - delete and return null
            @unlink($file);
            return null;
        }

        return $data;
    }

    /**
     * Save server-side challenge data
     *
     * @param  array  $data
     * @return bool
     */
    private function saveChallengeData(array $data): bool
    {
        $this->ensureStorageDir();

        // Sign the data to detect tampering
        $signature = hash_hmac('sha256', json_encode($data), $this->getServerSecret());
        $data['signature'] = $signature;

        $file = $this->getChallengeFilePath();
        return file_put_contents($file, json_encode($data, JSON_PRETTY_PRINT)) !== false;
    }

    /**
     * Generate a new verification token
     *
     * The token is stored SERVER-SIDE, not in the session.
     *
     * @return string  The token (just the random part)
     */
    public function generateToken(): string
    {
        $token = '';
        $maxIndex = strlen(self::READABLE_CHARS) - 1;

        for ($i = 0; $i < self::TOKEN_LENGTH; $i++) {
            $token .= self::READABLE_CHARS[random_int(0, $maxIndex)];
        }

        // Store token SERVER-SIDE (not in session!)
        $data = $this->loadChallengeData() ?? [];
        $data['token'] = $token;
        $data['token_generated'] = time();
        $data['session_id'] = session_id();
        $this->saveChallengeData($data);

        return $token;
    }

    /**
     * Get the current token, or generate a new one if needed
     *
     * Token is retrieved from SERVER-SIDE storage, not session.
     *
     * @return string
     */
    public function getCurrentToken(): string
    {
        $data = $this->loadChallengeData();

        // Check if we have a valid token
        if ($data && isset($data['token'])) {
            $generated = $data['token_generated'] ?? 0;

            // Verify token belongs to this session
            if (($data['session_id'] ?? '') !== session_id()) {
                // Session mismatch - generate new token
                return $this->generateToken();
            }

            // If token is still fresh (within max age), return it
            if (time() - $generated < self::MAX_FILE_AGE) {
                return $data['token'];
            }
        }

        // Generate a new token
        return $this->generateToken();
    }

    /**
     * Get the full filename for verification
     *
     * @return string
     */
    public function getVerificationFilename(): string
    {
        return 'hubzero-verify-' . $this->getCurrentToken() . '.txt';
    }

    /**
     * Get the full path to the verification file
     *
     * @return string
     */
    public function getVerificationFilePath(): string
    {
        return $this->docRoot . '/' . $this->getVerificationFilename();
    }

    /**
     * Check if the client is currently locked out
     *
     * Lockout is stored SERVER-SIDE to prevent session manipulation bypass.
     *
     * @return bool
     */
    public function isLockedOut(): bool
    {
        $data = $this->loadChallengeData();
        $lockoutUntil = $data['lockout_until'] ?? 0;
        return time() < $lockoutUntil;
    }

    /**
     * Get remaining lockout time in seconds
     *
     * @return int
     */
    public function getLockoutRemaining(): int
    {
        $data = $this->loadChallengeData();
        $lockoutUntil = $data['lockout_until'] ?? 0;
        return max(0, $lockoutUntil - time());
    }

    /**
     * Record a failed verification attempt
     *
     * Stored SERVER-SIDE to prevent bypass via session manipulation.
     *
     * @return void
     */
    private function recordFailedAttempt(): void
    {
        $data = $this->loadChallengeData() ?? [];
        $attempts = ($data['attempts'] ?? 0) + 1;
        $data['attempts'] = $attempts;

        if ($attempts >= self::MAX_ATTEMPTS) {
            $data['lockout_until'] = time() + self::LOCKOUT_DURATION;
            $data['attempts'] = 0;
            $this->logSecurityEvent('LOCKOUT', "Locked out after {$attempts} failed attempts");
        }

        $this->saveChallengeData($data);
    }

    /**
     * Reset failed attempt counter
     *
     * @return void
     */
    private function resetAttempts(): void
    {
        $data = $this->loadChallengeData() ?? [];
        $data['attempts'] = 0;
        $this->saveChallengeData($data);
    }

    /**
     * Verify the ownership file exists and is valid
     *
     * @return array  ['valid' => bool, 'error' => string|null]
     */
    public function verifyOwnershipFile(): array
    {
        // Check lockout first
        if ($this->isLockedOut()) {
            $remaining = ceil($this->getLockoutRemaining() / 60);
            return [
                'valid' => false,
                'error' => "Too many failed attempts. Please wait {$remaining} minute(s) before trying again."
            ];
        }

        $filePath = $this->getVerificationFilePath();

        // Check if file exists
        if (!file_exists($filePath)) {
            $this->recordFailedAttempt();
            return [
                'valid' => false,
                'error' => 'Verification file not found. Please create the file as instructed.'
            ];
        }

        // Check file age
        $fileAge = time() - filemtime($filePath);
        if ($fileAge > self::MAX_FILE_AGE) {
            $this->recordFailedAttempt();
            // Generate new token since this one expired
            $this->generateToken();
            return [
                'valid' => false,
                'error' => 'Verification file has expired. A new filename has been generated.'
            ];
        }

        // File exists and is fresh - verification successful
        $this->resetAttempts();
        return ['valid' => true, 'error' => null];
    }

    /**
     * Get client fingerprint for binding sessions to clients
     *
     * @return string
     */
    private function getClientFingerprint(): string
    {
        $ip = $this->getClientIP();
        $userAgent = $_SERVER['HTTP_USER_AGENT'] ?? 'unknown';
        $sessionId = session_id();

        // Create a hash of the client characteristics
        return hash('sha256', $ip . '|' . $userAgent . '|' . $sessionId);
    }

    /**
     * Get client IP address
     *
     * @return string
     */
    public function getClientIP(): string
    {
        // Check for forwarded IP (behind proxy/load balancer)
        $headers = [
            'HTTP_CF_CONNECTING_IP',     // Cloudflare
            'HTTP_X_FORWARDED_FOR',      // Standard proxy
            'HTTP_X_REAL_IP',            // Nginx proxy
            'REMOTE_ADDR'                // Direct connection
        ];

        foreach ($headers as $header) {
            if (!empty($_SERVER[$header])) {
                // X-Forwarded-For may contain multiple IPs, get the first
                $ip = $_SERVER[$header];
                if (strpos($ip, ',') !== false) {
                    $ip = trim(explode(',', $ip)[0]);
                }
                if (filter_var($ip, FILTER_VALIDATE_IP)) {
                    return $ip;
                }
            }
        }

        return '0.0.0.0';
    }

    /**
     * Bind the verified session to this client
     *
     * Stores verification state SERVER-SIDE to prevent session manipulation.
     *
     * @return bool
     */
    public function bindClient(): bool
    {
        $filePath = $this->getVerificationFilePath();

        if (!file_exists($filePath)) {
            return false;
        }

        $fingerprint = $this->getClientFingerprint();
        $ip = $this->getClientIP();
        $timestamp = date('Y-m-d H:i:s');

        $content = "HUBzero Installation Verification\n";
        $content .= "==================================\n";
        $content .= "Client IP: {$ip}\n";
        $content .= "Verified: {$timestamp}\n";
        $content .= "Fingerprint: {$fingerprint}\n";
        $content .= "Session: " . session_id() . "\n";

        // Write to verification file
        if (file_put_contents($filePath, $content) === false) {
            return false;
        }

        // Store verification state SERVER-SIDE (not in session!)
        $data = $this->loadChallengeData() ?? [];
        $data['verified'] = true;
        $data['fingerprint'] = $fingerprint;
        $data['verified_at'] = time();
        $data['file_path'] = $filePath;
        $data['bound_session_id'] = session_id();

        return $this->saveChallengeData($data);
    }

    /**
     * Check if the current client is verified and still valid
     *
     * All verification state is checked SERVER-SIDE.
     *
     * @return array  ['valid' => bool, 'error' => string|null]
     */
    public function validateClient(): array
    {
        $data = $this->loadChallengeData();

        // Check server-side verification flag
        if (!$data || empty($data['verified'])) {
            return [
                'valid' => false,
                'error' => 'Session not verified. Please complete the verification step.'
            ];
        }

        // Verify the bound session ID matches
        if (($data['bound_session_id'] ?? '') !== session_id()) {
            $this->clearVerification();
            return [
                'valid' => false,
                'error' => 'Session mismatch. Please restart verification.'
            ];
        }

        // Check fingerprint match
        $storedFingerprint = $data['fingerprint'] ?? '';
        $currentFingerprint = $this->getClientFingerprint();

        if (!hash_equals($storedFingerprint, $currentFingerprint)) {
            // Clear verification - fingerprint changed
            $this->clearVerification();
            return [
                'valid' => false,
                'error' => 'Client verification failed. Your connection details have changed.'
            ];
        }

        // Verify the file still exists and matches
        $filePath = $data['file_path'] ?? '';

        if (!file_exists($filePath)) {
            $this->clearVerification();
            return [
                'valid' => false,
                'error' => 'Verification file was removed. Please restart verification.'
            ];
        }

        // Read and verify file contents contain the fingerprint
        $contents = file_get_contents($filePath);
        if (strpos($contents, $storedFingerprint) === false) {
            $this->clearVerification();
            return [
                'valid' => false,
                'error' => 'Verification file was tampered with. Please restart verification.'
            ];
        }

        return ['valid' => true, 'error' => null];
    }

    /**
     * Check if verification is required (first step not yet completed)
     *
     * Checks SERVER-SIDE storage, not session.
     *
     * @return bool
     */
    public function isVerificationRequired(): bool
    {
        $data = $this->loadChallengeData();
        return !$data || empty($data['verified']);
    }

    /**
     * Clear all verification data
     *
     * Clears SERVER-SIDE storage.
     *
     * @return void
     */
    public function clearVerification(): void
    {
        // Remove server-side challenge file
        $challengeFile = $this->getChallengeFilePath();
        if (file_exists($challengeFile)) {
            @unlink($challengeFile);
        }
    }

    /**
     * Clean up verification file (call on install completion)
     *
     * Removes all security files created during installation:
     * - Verification file in document root
     * - Challenge files in storage directory
     * - Server secret (no longer needed after install)
     * - Storage directory itself if empty
     *
     * @return bool
     */
    public function cleanup(): bool
    {
        $data = $this->loadChallengeData();
        $filePath = $data['file_path'] ?? null;

        // Delete the verification file in document root
        if ($filePath && file_exists($filePath)) {
            // Securely delete - overwrite with random data first
            $size = filesize($filePath);
            if ($size > 0) {
                file_put_contents($filePath, random_bytes($size));
            }
            unlink($filePath);
        }

        // Delete the server-side challenge file
        $this->clearVerification();

        // Clean up the entire storage directory (including secret)
        $this->cleanupStorageDirectory();

        // Clear session data
        $_SESSION = [];

        return true;
    }

    /**
     * Clean up the entire storage directory
     *
     * Removes all files and the directory itself after successful installation.
     *
     * @return void
     */
    private function cleanupStorageDirectory(): void
    {
        $dir = $this->getStorageDir();
        if (!is_dir($dir)) {
            return;
        }

        // Remove all files in the directory
        $files = glob($dir . '/*');
        foreach ($files as $file) {
            if (is_file($file)) {
                // Securely delete secret file
                if (basename($file) === 'secret.php') {
                    $size = filesize($file);
                    if ($size > 0) {
                        file_put_contents($file, random_bytes($size));
                    }
                }
                @unlink($file);
            }
        }

        // Remove hidden files (.htaccess, etc.)
        $hiddenFiles = glob($dir . '/.*');
        foreach ($hiddenFiles as $file) {
            if (is_file($file)) {
                @unlink($file);
            }
        }

        // Remove the directory itself
        @rmdir($dir);
    }

    /**
     * Clean up old challenge files
     *
     * Removes challenge files older than 24 hours to prevent accumulation.
     * Called periodically, NOT on install completion.
     *
     * @return void
     */
    private function cleanupOldChallenges(): void
    {
        $dir = $this->getStorageDir();
        if (!is_dir($dir)) {
            return;
        }

        $maxAge = 86400; // 24 hours
        $now = time();

        foreach (glob($dir . '/challenge-*.json') as $file) {
            if ($now - filemtime($file) > $maxAge) {
                @unlink($file);
            }
        }
    }

    /**
     * Get time remaining until token expires (for display)
     *
     * Reads from SERVER-SIDE storage.
     *
     * @return int  Seconds remaining
     */
    public function getTokenTimeRemaining(): int
    {
        $data = $this->loadChallengeData();
        $generated = $data['token_generated'] ?? time();
        $elapsed = time() - $generated;
        return max(0, self::MAX_FILE_AGE - $elapsed);
    }

    /**
     * Format seconds as minutes:seconds
     *
     * @param  int  $seconds
     * @return string
     */
    public function formatTime(int $seconds): string
    {
        $minutes = floor($seconds / 60);
        $secs = $seconds % 60;
        return sprintf('%d:%02d', $minutes, $secs);
    }

    /**
     * Get the document root path (for display to user)
     *
     * @return string
     */
    public function getDocumentRoot(): string
    {
        return $this->docRoot;
    }

    /**
     * Get number of failed attempts
     *
     * Reads from SERVER-SIDE storage.
     *
     * @return int
     */
    public function getFailedAttempts(): int
    {
        $data = $this->loadChallengeData();
        return $data['attempts'] ?? 0;
    }

    /**
     * Get maximum allowed attempts before lockout
     *
     * @return int
     */
    public function getMaxAttempts(): int
    {
        return self::MAX_ATTEMPTS;
    }

    /**
     * Generate CSRF token for forms
     *
     * @return string
     */
    public function generateCsrfToken(): string
    {
        if (empty($_SESSION[self::SESSION_PREFIX . 'csrf_token'])) {
            $_SESSION[self::SESSION_PREFIX . 'csrf_token'] = bin2hex(random_bytes(32));
        }
        return $_SESSION[self::SESSION_PREFIX . 'csrf_token'];
    }

    /**
     * Validate CSRF token from form submission
     *
     * @param  string|null  $token
     * @return bool
     */
    public function validateCsrfToken(?string $token): bool
    {
        $stored = $_SESSION[self::SESSION_PREFIX . 'csrf_token'] ?? '';
        return $token !== null && hash_equals($stored, $token);
    }

    /**
     * Log a security event
     *
     * @param  string  $event   Event type
     * @param  string  $detail  Event details
     * @return void
     */
    public function logSecurityEvent(string $event, string $detail = ''): void
    {
        $logDir = $this->docRoot . '/app/logs';
        if (!is_dir($logDir)) {
            @mkdir($logDir, 0755, true);
        }

        $logFile = $logDir . '/install-security.log';
        $timestamp = date('Y-m-d H:i:s');
        $ip = $this->getClientIP();
        $userAgent = $_SERVER['HTTP_USER_AGENT'] ?? 'unknown';

        $entry = "[{$timestamp}] [{$event}] IP: {$ip} | {$detail} | UA: {$userAgent}\n";

        @file_put_contents($logFile, $entry, FILE_APPEND | LOCK_EX);
    }

    // =========================================================================
    // AJAX/SSE Security Hardening
    // =========================================================================

    /**
     * Rate limit key prefix for AJAX
     */
    private const RATE_LIMIT_PREFIX = 'ajax_rate_';

    /**
     * Maximum AJAX requests per minute per action
     */
    private const AJAX_RATE_LIMIT = 30;

    /**
     * Validate an AJAX request
     *
     * Performs multiple security checks:
     * - Client verification (session binding)
     * - CSRF token validation
     * - Origin/Referer validation
     * - Rate limiting
     *
     * @param  string  $action      The action being performed
     * @param  bool    $requirePost Whether the request must be POST
     * @return array ['valid' => bool, 'error' => string|null]
     */
    public function validateAjaxRequest(string $action, bool $requirePost = false): array
    {
        // Check if client is verified
        if ($this->isVerificationRequired()) {
            $this->logSecurityEvent('AJAX_UNVERIFIED', "Unverified AJAX request: {$action}");
            return ['valid' => false, 'error' => 'Client not verified'];
        }

        // Validate client binding
        $validation = $this->validateClient();
        if (!$validation['valid']) {
            $this->logSecurityEvent('AJAX_CLIENT_FAIL', "Client validation failed: {$action}");
            return ['valid' => false, 'error' => $validation['error']];
        }

        // Check request method if POST required
        if ($requirePost && $_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->logSecurityEvent('AJAX_METHOD_FAIL', "POST required for: {$action}");
            return ['valid' => false, 'error' => 'Invalid request method'];
        }

        // Validate CSRF token (from header or POST data)
        $token = $_SERVER['HTTP_X_CSRF_TOKEN']
            ?? $_POST['csrf_token']
            ?? $_GET['csrf_token']
            ?? null;

        if (!$this->validateCsrfToken($token)) {
            $this->logSecurityEvent('AJAX_CSRF_FAIL', "Invalid CSRF token: {$action}");
            return ['valid' => false, 'error' => 'Invalid security token'];
        }

        // Validate origin/referer
        if (!$this->validateRequestOrigin()) {
            $this->logSecurityEvent('AJAX_ORIGIN_FAIL', "Invalid origin: {$action}");
            return ['valid' => false, 'error' => 'Invalid request origin'];
        }

        // Check rate limiting
        if (!$this->checkRateLimit($action)) {
            $this->logSecurityEvent('AJAX_RATE_LIMIT', "Rate limit exceeded: {$action}");
            return ['valid' => false, 'error' => 'Too many requests. Please wait.'];
        }

        return ['valid' => true, 'error' => null];
    }

    /**
     * Validate request origin matches our host
     *
     * @return bool
     */
    private function validateRequestOrigin(): bool
    {
        // Normalize our host (remove port, lowercase)
        $host = $_SERVER['HTTP_HOST'] ?? '';
        $serverHost = strtolower($this->extractHostname($host));

        if (empty($serverHost)) {
            // Can't validate without knowing our host - allow (defense in depth via other checks)
            return true;
        }

        // Check Origin header first (more reliable)
        $origin = $_SERVER['HTTP_ORIGIN'] ?? '';
        if (!empty($origin)) {
            $originHost = strtolower(parse_url($origin, PHP_URL_HOST) ?? '');
            return $originHost === $serverHost;
        }

        // Fall back to Referer header
        $referer = $_SERVER['HTTP_REFERER'] ?? '';
        if (!empty($referer)) {
            $refererHost = strtolower(parse_url($referer, PHP_URL_HOST) ?? '');
            return $refererHost === $serverHost;
        }

        // For SSE connections, browsers may not send Origin/Referer
        // Allow if it's a same-session request (verified client)
        return true;
    }

    /**
     * Extract hostname from a host string (removes port if present)
     *
     * @param  string  $host  Host string like "localhost:8080" or "example.com"
     * @return string Just the hostname portion
     */
    private function extractHostname(string $host): string
    {
        // Handle IPv6 addresses in brackets [::1]
        if (strpos($host, '[') === 0) {
            $closeBracket = strpos($host, ']');
            if ($closeBracket !== false) {
                return substr($host, 0, $closeBracket + 1);
            }
            return $host;
        }

        // Remove port from hostname
        $colonPos = strrpos($host, ':');
        if ($colonPos !== false) {
            return substr($host, 0, $colonPos);
        }

        return $host;
    }

    /**
     * Check rate limiting for an action
     *
     * @param  string  $action  The action being rate limited
     * @return bool True if within limits, false if exceeded
     */
    private function checkRateLimit(string $action): bool
    {
        $key = self::RATE_LIMIT_PREFIX . $action;
        $data = $this->loadChallengeData() ?? [];

        $rateLimits = $data['rate_limits'] ?? [];
        $now = time();
        $windowStart = $now - 60; // 1 minute window

        // Get requests in current window
        $requests = $rateLimits[$key] ?? [];
        $requests = array_filter($requests, function ($timestamp) use ($windowStart) {
            return $timestamp > $windowStart;
        });

        // Check if over limit
        if (count($requests) >= self::AJAX_RATE_LIMIT) {
            return false;
        }

        // Record this request
        $requests[] = $now;
        $rateLimits[$key] = array_values($requests);
        $data['rate_limits'] = $rateLimits;
        $this->saveChallengeData($data);

        return true;
    }

    /**
     * Generate a one-time nonce for sensitive operations
     *
     * @param  string  $operation  The operation this nonce is for
     * @return string
     */
    public function generateNonce(string $operation): string
    {
        $nonce = bin2hex(random_bytes(16));
        $data = $this->loadChallengeData() ?? [];

        $data['nonces'][$operation] = [
            'value' => $nonce,
            'created' => time(),
            'used' => false,
        ];

        $this->saveChallengeData($data);
        return $nonce;
    }

    /**
     * Validate and consume a one-time nonce
     *
     * @param  string  $operation  The operation this nonce is for
     * @param  string  $nonce      The nonce to validate
     * @return bool
     */
    public function validateNonce(string $operation, string $nonce): bool
    {
        $data = $this->loadChallengeData();
        if (!$data || !isset($data['nonces'][$operation])) {
            return false;
        }

        $stored = $data['nonces'][$operation];

        // Check if already used
        if ($stored['used']) {
            $this->logSecurityEvent('NONCE_REUSE', "Attempted nonce reuse: {$operation}");
            return false;
        }

        // Check if expired (5 minute window)
        if (time() - $stored['created'] > 300) {
            $this->logSecurityEvent('NONCE_EXPIRED', "Expired nonce: {$operation}");
            unset($data['nonces'][$operation]);
            $this->saveChallengeData($data);
            return false;
        }

        // Check if matches
        if (!hash_equals($stored['value'], $nonce)) {
            $this->logSecurityEvent('NONCE_INVALID', "Invalid nonce: {$operation}");
            return false;
        }

        // Mark as used (consume)
        $data['nonces'][$operation]['used'] = true;
        $this->saveChallengeData($data);

        return true;
    }

    /**
     * Clean up expired nonces
     *
     * @return void
     */
    public function cleanupNonces(): void
    {
        $data = $this->loadChallengeData();
        if (!$data || !isset($data['nonces'])) {
            return;
        }

        $now = time();
        $maxAge = 300; // 5 minutes

        foreach ($data['nonces'] as $operation => $nonce) {
            if ($now - $nonce['created'] > $maxAge) {
                unset($data['nonces'][$operation]);
            }
        }

        $this->saveChallengeData($data);
    }
}
