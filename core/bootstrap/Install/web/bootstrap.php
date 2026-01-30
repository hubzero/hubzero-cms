<?php

/**
 * HUBzero Bootstrap Installer
 *
 * This minimal installer runs when vendor dependencies are missing.
 * It performs preflight checks and can run composer install.
 *
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2025 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Bootstrap\Install\Web;

// phpcs:disable PSR1.Files.SideEffects

// Prevent direct access except through index.php
if (!defined('PATH_ROOT')) {
    die('Direct access not permitted');
}

// Load shared classes (used by both bootstrap and main installer)
require_once __DIR__ . '/StorageCheck.php';
require_once __DIR__ . '/SecurityGuard.php';
require_once dirname(dirname(dirname(__DIR__))) . '/libraries/Hubzero/System/Requirements.php';

use Hubzero\System\Requirements;

/**
 * Bootstrap Installer class
 *
 * Handles pre-vendor installation checks and composer install
 */
class BootstrapInstaller
{
    /**
     * Minimum extensions needed for bootstrap (subset of full requirements)
     * Uses Requirements::MIN_PHP_VERSION for PHP version
     */
    private const REQUIRED_EXTENSIONS = [
        'openssl'   => 'Required for secure downloads',
        'curl'      => 'Required for downloading packages',
    ];

    // phpcs:disable Generic.Files.LineLength
    private const HELP_TEXT = [
        'php' => [
            'title' => 'PHP Version',
            'debian' => 'sudo apt update && sudo apt install php8.3',
            'rhel' => 'sudo dnf install php',
            'mac' => 'brew install php@8.3',
        ],
        'extensions' => [
            'title' => 'PHP Extensions',
            'debian' => 'sudo apt install php-openssl php-curl',
            'rhel' => 'sudo dnf install php-openssl php-curl',
            'mac' => 'brew install php (extensions included)',
        ],
        'unzip' => [
            'title' => 'Unzip Command',
            'debian' => 'sudo apt install unzip',
            'rhel' => 'sudo dnf install unzip',
            'mac' => 'brew install unzip (or use built-in)',
        ],
        'composer' => [
            'title' => 'Composer',
            'debian' => 'sudo apt install composer\n# Or: curl -sS https://getcomposer.org/installer | php',
            'rhel' => 'sudo dnf install composer\n# Or: curl -sS https://getcomposer.org/installer | php',
            'mac' => 'brew install composer',
        ],
        'writable' => [
            'title' => 'Directory Permissions',
            'debian' => 'sudo chown -R www-data:www-data /path/to/core\nsudo chmod -R 755 /path/to/core',
            'rhel' => 'sudo chown -R apache:apache /path/to/core\nsudo chmod -R 755 /path/to/core',
            'mac' => 'sudo chown -R _www:_www /path/to/core\nsudo chmod -R 755 /path/to/core',
        ],
    ];
    // phpcs:enable Generic.Files.LineLength

    private $corePath;
    private $checks = [];
    private $allPassed = true;
    private $security = null;

    public function __construct()
    {
        $this->corePath = PATH_CORE;
    }

    /**
     * Run the bootstrap installer
     */
    public function run(): void
    {
        // Check for Windows first - HUBzero does not support Windows
        if (strtoupper(substr(PHP_OS, 0, 3)) === 'WIN') {
            http_response_code(500);
            include __DIR__ . '/views/windows-error.php';
            exit;
        }

        // Check if secure storage is available (same check as main installer)
        $storageCheck = new StorageCheck(PATH_ROOT);
        $storageResult = $storageCheck->check();
        if (!$storageResult['available']) {
            $tried = $storageResult['tried'];
            $docRoot = $storageResult['docRoot'];
            include __DIR__ . '/views/storage-error.php';
            exit;
        }

        // Start session for verification (use same name as main installer to share state)
        if (session_status() === PHP_SESSION_NONE) {
            session_name('hubzero_install');
            session_start();
        }

        // Initialize security guard for verification
        $this->security = new SecurityGuard(PATH_ROOT);
        $security = $this->security;

        // Handle verification step (same security as main installer)
        if ($security->isVerificationRequired()) {
            $this->handleVerification($security);
            return;
        }

        // Validate client hasn't changed since verification
        $validation = $security->validateClient();
        if (!$validation['valid']) {
            $security->logSecurityEvent('CLIENT_VALIDATION_FAIL', $validation['error']);
            $errorMessage = $validation['error'];
            include __DIR__ . '/views/bootstrap-verify.php';
            return;
        }

        // Handle SSE streaming for composer install (requires verification + hardened security)
        if (isset($_GET['action']) && $_GET['action'] === 'stream_composer') {
            // Use hardened validation for SSE streaming (sensitive operation)
            $sseValidation = $security->validateAjaxRequest('stream_composer', false);
            if (!$sseValidation['valid']) {
                header('Content-Type: text/event-stream');
                header('Cache-Control: no-cache');
                echo "event: error\ndata: " . ($sseValidation['error'] ?? 'Security verification failed') . "\n\n";
                echo "event: done\ndata: " . json_encode(['success' => false, 'security_error' => true]) . "\n\n";
                exit;
            }
            $this->streamComposerInstall();
            exit;
        }

        // Run preflight checks
        $this->runChecks();

        // Render the page
        $this->render();
    }

    /**
     * Handle verification step
     *
     * @param  SecurityGuard  $security
     * @return void
     */
    private function handleVerification(SecurityGuard $security): void
    {
        $errorMessage = null;

        // Handle form submission
        if ($_SERVER['REQUEST_METHOD'] === 'POST' && ($_POST['action'] ?? '') === 'verify') {
            // Validate CSRF token
            if (!$security->validateCsrfToken($_POST['csrf_token'] ?? null)) {
                $security->logSecurityEvent('CSRF_FAIL', 'Invalid CSRF token on bootstrap verify');
                $errorMessage = 'Security validation failed. Please try again.';
            } elseif ($security->isLockedOut()) {
                // Check lockout
                $remaining = ceil($security->getLockoutRemaining() / 60);
                $security->logSecurityEvent('LOCKOUT_ATTEMPT', 'Attempt during lockout');
                $errorMessage = "Too many failed attempts. Please wait {$remaining} minute(s).";
            } else {
                // Verify the ownership file
                $result = $security->verifyOwnershipFile();

                if (!$result['valid']) {
                    $security->logSecurityEvent('VERIFY_FAIL', $result['error']);
                    $errorMessage = $result['error'];
                } else {
                    // File verified - bind this client to the session
                    if (!$security->bindClient()) {
                        $security->logSecurityEvent('BIND_FAIL', 'Failed to bind client');
                        $errorMessage = 'Failed to complete verification. Please check file permissions.';
                    } else {
                        // Success! Redirect to main bootstrap page
                        $security->logSecurityEvent(
                            'VERIFY_SUCCESS',
                            'Bootstrap installation authorized from IP: ' . $security->getClientIP()
                        );
                        header('Location: ' . $this->getSelfUrl());
                        exit;
                    }
                }
            }
        }

        // Show verification page
        include __DIR__ . '/views/bootstrap-verify.php';
    }

    /**
     * Run all preflight checks
     */
    private function runChecks(): void
    {
        // Note: Operating system check is handled at the start of run() method
        // Windows users see a dedicated error page before reaching this point

        // PHP Version (using shared Requirements class)
        $phpCheck = Requirements::checkPhpVersion();
        $this->checks['php'] = $phpCheck;
        if (!$phpCheck['passed']) {
            $this->allPassed = false;
        }

        // PHP Extensions (combined into single check like main installer)
        $missing = [];
        $installed = [];
        foreach (self::REQUIRED_EXTENSIONS as $ext => $description) {
            if (extension_loaded($ext)) {
                $installed[] = $ext;
            } else {
                $missing[] = $ext;
            }
        }
        $allExtPassed = empty($missing);
        $total = count(self::REQUIRED_EXTENSIONS);
        $this->checks['extensions'] = [
            'name'     => 'PHP Extensions',
            'required' => $total . ' required',
            'current'  => $allExtPassed
                ? 'All ' . $total . ' installed'
                : count($missing) . ' missing: ' . implode(', ', $missing),
            'passed'   => $allExtPassed,
            'missing'  => $missing,
        ];
        if (!$allExtPassed) {
            $this->allPassed = false;
        }

        // Unzip command (using shared Requirements class)
        $unzipCheck = Requirements::checkUnzip();
        $this->checks['unzip'] = $unzipCheck;
        if (!$unzipCheck['passed']) {
            $this->allPassed = false;
        }

        // Composer
        $composerPath = $this->findComposer();
        $composerFound = !empty($composerPath);
        $this->checks['composer'] = [
            'name'     => 'Composer',
            'required' => 'Required',
            'current'  => $composerFound ? 'Found' : 'Missing',
            'passed'   => $composerFound,
            'note'     => 'PHP dependency manager',
        ];
        if (!$composerFound) {
            $this->allPassed = false;
        }

        // Core directory writable (needed for vendor)
        $coreWritable = is_writable($this->corePath);
        $this->checks['writable'] = [
            'name'     => 'Core directory',
            'required' => 'Writable',
            'current'  => $coreWritable ? 'Writable' : 'Not writable',
            'passed'   => $coreWritable,
            'note'     => 'Required for vendor installation',
        ];
        if (!$coreWritable) {
            $this->allPassed = false;
        }
    }

    /**
     * Find composer executable
     */
    private function findComposer(): string
    {
        // Check for bundled composer in core/bin first
        $bundledComposer = $this->corePath . '/bin/composer';
        if (file_exists($bundledComposer)) {
            return escapeshellarg($bundledComposer);
        }

        // Check system composer via which
        $composerPath = trim(shell_exec('which composer 2>/dev/null') ?? '');
        if (!empty($composerPath)) {
            return escapeshellarg($composerPath);
        }

        // Try common system paths
        $commonPaths = [
            '/usr/local/bin/composer',
            '/usr/bin/composer',
        ];
        foreach ($commonPaths as $path) {
            if (file_exists($path) && is_executable($path)) {
                return escapeshellarg($path);
            }
        }

        return '';
    }

    /**
     * Stream composer install output via Server-Sent Events
     */
    private function streamComposerInstall(): void
    {
        // Set headers for SSE
        header('Content-Type: text/event-stream');
        header('Cache-Control: no-cache');
        header('Connection: keep-alive');
        header('X-Accel-Buffering: no'); // Disable nginx buffering

        // Disable output buffering
        while (ob_get_level()) {
            ob_end_flush();
        }

        $composer = $this->findComposer();
        if (empty($composer)) {
            $this->sendSSE('error', 'Composer not found');
            $this->sendSSE('done', json_encode(['success' => false]));
            return;
        }

        // Build the command
        $command = sprintf(
            'cd %s && %s install --no-interaction --no-ansi 2>&1',
            escapeshellarg($this->corePath),
            $composer
        );

        // Open process with pipes
        $descriptors = [
            0 => ['pipe', 'r'],  // stdin
            1 => ['pipe', 'w'],  // stdout
            2 => ['pipe', 'w'],  // stderr
        ];

        $process = proc_open($command, $descriptors, $pipes);

        if (!is_resource($process)) {
            $this->sendSSE('error', 'Failed to start composer process');
            $this->sendSSE('done', json_encode(['success' => false]));
            return;
        }

        // Close stdin
        fclose($pipes[0]);

        // Set stdout to non-blocking
        stream_set_blocking($pipes[1], false);

        // Read output in real-time
        while (!feof($pipes[1])) {
            $line = fgets($pipes[1]);
            if ($line !== false) {
                $this->sendSSE('output', rtrim($line));
            }
            // Small delay to prevent CPU spinning
            usleep(10000);
        }

        fclose($pipes[1]);
        fclose($pipes[2]);

        $returnCode = proc_close($process);
        $success = ($returnCode === 0);

        // Check if vendor now exists
        $vendorExists = file_exists($this->corePath . '/vendor/autoload.php');

        $this->sendSSE('done', json_encode([
            'success' => $success && $vendorExists,
            'returnCode' => $returnCode
        ]));
    }

    /**
     * Send an SSE event
     */
    private function sendSSE(string $event, string $data): void
    {
        // Filter out unwanted lines
        if ($event === 'output') {
            if (strpos($data, 'Use the `composer fund` command') !== false) {
                return;
            }
            if (strpos($data, 'vendor/bin/phpcs" --config-set installed_paths') !== false) {
                return;
            }
            if (strpos($data, 'Using config file:') !== false && strpos($data, 'CodeSniffer.conf') !== false) {
                return;
            }
        }

        echo "event: {$event}\n";
        echo "data: {$data}\n\n";
        flush();
    }

    /**
     * Get the current URL
     */
    private function getSelfUrl(): string
    {
        $protocol = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') ? 'https' : 'http';
        $host = $_SERVER['HTTP_HOST'] ?? 'localhost';
        $uri = strtok($_SERVER['REQUEST_URI'] ?? '/', '?');
        return $protocol . '://' . $host . $uri;
    }

    // phpcs:disable Generic.Files.LineLength
    /**
     * Render the bootstrap installer page
     */
    private function render(): void
    {
        ?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>HUBzero Bootstrap Setup</title>
    <style>
:root {
    --primary-color: #2563eb;
    --primary-dark: #1d4ed8;
    --success-color: #16a34a;
    --warning-color: #ca8a04;
    --error-color: #dc2626;
    --gray-50: #f9fafb;
    --gray-100: #f3f4f6;
    --gray-200: #e5e7eb;
    --gray-300: #d1d5db;
    --gray-500: #6b7280;
    --gray-700: #374151;
    --gray-900: #111827;
}

* {
    box-sizing: border-box;
    margin: 0;
    padding: 0;
}

body {
    font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
    font-size: 16px;
    line-height: 1.6;
    color: var(--gray-700);
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    min-height: 100vh;
    padding: 2rem 1rem;
}

.container {
    max-width: 800px;
    margin: 0 auto;
}

.header {
    text-align: center;
    color: white;
    margin-bottom: 2rem;
}

.header h1 {
    font-size: 2rem;
    font-weight: 300;
    margin-bottom: 0.5rem;
}

.header p {
    opacity: 0.9;
}

.card {
    background: white;
    border-radius: 1rem;
    box-shadow: 0 20px 40px rgba(0,0,0,0.2);
    padding: 2rem;
    margin-bottom: 1.5rem;
}

.card h2 {
    font-size: 1.25rem;
    color: var(--gray-900);
    margin-bottom: 1rem;
    padding-bottom: 0.75rem;
    border-bottom: 1px solid var(--gray-200);
}

.info-box {
    padding: 1rem;
    border-radius: 0.5rem;
    margin-bottom: 1.5rem;
}

.info-box.warning {
    background: #fffbeb;
    border: 1px solid #fde68a;
    color: #92400e;
}

.info-box.error {
    background: #fef2f2;
    border: 1px solid #fecaca;
    color: var(--error-color);
}

.info-box.success {
    background: #f0fdf4;
    border: 1px solid #bbf7d0;
    color: var(--success-color);
}

table {
    width: 100%;
    border-collapse: collapse;
    margin-bottom: 1rem;
}

th, td {
    padding: 0.75rem;
    text-align: left;
    border-bottom: 1px solid var(--gray-200);
}

th {
    background: var(--gray-50);
    font-weight: 500;
}

tr.passed { background: #f0fdf4; }
tr.failed { background: #fef2f2; }

.status-pass { color: var(--success-color); font-weight: bold; }
.status-fail { color: var(--error-color); font-weight: bold; }

.btn {
    display: inline-flex;
    align-items: center;
    justify-content: center;
    padding: 0.75rem 1.5rem;
    font-size: 1rem;
    font-weight: 500;
    border-radius: 0.5rem;
    border: none;
    cursor: pointer;
    text-decoration: none;
    transition: all 0.2s;
}

.btn-primary {
    background: var(--primary-color);
    color: white;
}

.btn-primary:hover {
    background: var(--primary-dark);
}

.btn-primary:disabled {
    background: var(--gray-300);
    cursor: not-allowed;
}

.btn-secondary {
    background: var(--gray-100);
    color: var(--gray-700);
}

.actions {
    display: flex;
    gap: 1rem;
    margin-top: 1.5rem;
}

.output {
    background: var(--gray-900);
    color: #10b981;
    padding: 1rem;
    border-radius: 0.5rem;
    font-family: monospace;
    font-size: 0.875rem;
    max-height: 300px;
    overflow-y: auto;
    white-space: pre-wrap;
    word-break: break-all;
}

.footer {
    text-align: center;
    color: rgba(255,255,255,0.6);
    margin-top: 2rem;
    font-size: 0.875rem;
}

/* Help icon */
.help-icon {
    display: inline-flex;
    align-items: center;
    justify-content: center;
    width: 1.25rem;
    height: 1.25rem;
    border-radius: 50%;
    background: var(--gray-200);
    color: var(--gray-500);
    font-size: 0.75rem;
    font-weight: bold;
    cursor: pointer;
    margin-right: 0.5rem;
    transition: all 0.2s;
    vertical-align: middle;
}

.help-icon:hover {
    background: var(--primary-color);
    color: white;
}

/* Modal */
.modal-overlay {
    position: fixed;
    top: 0;
    left: 0;
    right: 0;
    bottom: 0;
    background: rgba(0,0,0,0.5);
    display: none;
    align-items: center;
    justify-content: center;
    z-index: 1000;
    padding: 1rem;
}

.modal-overlay.active {
    display: flex;
}

.modal {
    background: white;
    border-radius: 1rem;
    max-width: 600px;
    width: 100%;
    max-height: 90vh;
    overflow-y: auto;
    box-shadow: 0 20px 40px rgba(0,0,0,0.3);
}

.modal-header {
    padding: 1.25rem 1.5rem;
    border-bottom: 1px solid var(--gray-200);
    display: flex;
    justify-content: space-between;
    align-items: center;
}

.modal-header h3 {
    margin: 0;
    font-size: 1.125rem;
    color: var(--gray-900);
}

.modal-close {
    background: none;
    border: none;
    font-size: 1.5rem;
    cursor: pointer;
    color: var(--gray-500);
    line-height: 1;
}

.modal-close:hover {
    color: var(--gray-900);
}

.modal-body {
    padding: 1.5rem;
}

.os-section {
    margin-bottom: 1.25rem;
}

.os-section:last-child {
    margin-bottom: 0;
}

.os-label {
    font-weight: 600;
    color: var(--gray-700);
    margin-bottom: 0.5rem;
    display: flex;
    align-items: center;
    gap: 0.5rem;
}

.os-label .os-icon {
    font-size: 1rem;
}

.os-command {
    background: var(--gray-900);
    color: #10b981;
    padding: 0.75rem 1rem;
    border-radius: 0.375rem;
    font-family: monospace;
    font-size: 0.875rem;
    white-space: pre-wrap;
    word-break: break-all;
}
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>HUBzero Bootstrap Setup</h1>
            <p>Preparing your environment for installation</p>
        </div>

        <div class="card">
            <h2>Missing Dependencies</h2>
            <div class="info-box warning">
                <strong>Composer dependencies not installed.</strong><br>
                The vendor directory is missing. We need to run <code>composer install</code> before
                the main installer can run.
            </div>

            <h2>System Requirements</h2>
            <table>
                <thead>
                    <tr>
                        <th>Requirement</th>
                        <th>Required</th>
                        <th>Current</th>
                        <th>Status</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($this->checks as $key => $check) : ?>
                        <?php $rowClass = $check['passed'] ? 'passed' : 'failed'; ?>
                    <tr class="<?php echo $rowClass; ?>">
                        <td>
                            <?php if (isset(self::HELP_TEXT[$key])) : ?>
                            <span class="help-icon" onclick="showHelp('<?php echo $key; ?>')" title="How to install">?</span>
                            <?php endif; ?>
                            <?php echo htmlspecialchars($check['name']); ?>
                            <?php if (!empty($check['note']) && !$check['passed']) : ?>
                                <br><small><?php echo htmlspecialchars($check['note']); ?></small>
                            <?php endif; ?>
                        </td>
                        <td><?php echo htmlspecialchars($check['required']); ?></td>
                        <td><?php echo htmlspecialchars($check['current']); ?></td>
                        <td>
                            <?php if ($check['passed']) : ?>
                                <span class="status-pass">&#10004;</span>
                            <?php else : ?>
                                <span class="status-fail">&#10008;</span>
                            <?php endif; ?>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>

            <div id="composer-output-section" style="display: none;">
                <h2>Composer Output</h2>
                <div id="composer-status"></div>
                <div class="output" id="composer-output"></div>
            </div>

            <!-- CSRF token for AJAX security -->
            <input type="hidden" name="csrf_token" id="csrf_token" value="<?php echo htmlspecialchars($this->security->generateCsrfToken()); ?>">

            <div class="actions">
                <button type="button" class="btn btn-primary" id="run-composer-btn" onclick="runComposer()" <?php echo $this->allPassed ? '' : 'disabled'; ?>>
                    Run Composer
                </button>
                <a href="?" class="btn btn-secondary" id="refresh-btn">Refresh</a>
                <a href="?" class="btn btn-primary" id="continue-btn" style="display: none;">Continue</a>
            </div>
        </div>

        <div class="footer">
            <p>&copy; <?php echo date('Y'); ?> HUBzero. Released under the MIT License.</p>
        </div>
    </div>

    <!-- Help Modal -->
    <div class="modal-overlay" id="helpModal" onclick="closeHelpModal(event)">
        <div class="modal" onclick="event.stopPropagation()">
            <div class="modal-header">
                <h3 id="modalTitle">Installation Help</h3>
                <button class="modal-close" onclick="closeHelpModal()">&times;</button>
            </div>
            <div class="modal-body" id="modalBody"></div>
        </div>
    </div>

    <script>
    var helpData = <?php echo json_encode(self::HELP_TEXT); ?>;

    function showHelp(key) {
        var data = helpData[key];
        if (!data) return;

        document.getElementById('modalTitle').textContent = 'How to install: ' + data.title;

        var html = '';
        html += '<div class="os-section">';
        html += '<div class="os-label"><span class="os-icon">&#128039;</span> Debian / Ubuntu</div>';
        html += '<div class="os-command">' + escapeHtml(data.debian).replace(/\\n/g, '\n') + '</div>';
        html += '</div>';

        html += '<div class="os-section">';
        html += '<div class="os-label"><span class="os-icon">&#127744;</span> RHEL / CentOS / Rocky / Oracle</div>';
        html += '<div class="os-command">' + escapeHtml(data.rhel).replace(/\\n/g, '\n') + '</div>';
        html += '</div>';

        html += '<div class="os-section">';
        html += '<div class="os-label"><span class="os-icon">&#127822;</span> macOS</div>';
        html += '<div class="os-command">' + escapeHtml(data.mac).replace(/\\n/g, '\n') + '</div>';
        html += '</div>';

        document.getElementById('modalBody').innerHTML = html;
        document.getElementById('helpModal').classList.add('active');
    }

    function closeHelpModal(event) {
        if (!event || event.target === document.getElementById('helpModal')) {
            document.getElementById('helpModal').classList.remove('active');
        }
    }

    function escapeHtml(text) {
        var div = document.createElement('div');
        div.textContent = text;
        return div.innerHTML;
    }

    document.addEventListener('keydown', function(e) {
        if (e.key === 'Escape') closeHelpModal();
    });

    // Composer streaming
    function runComposer() {
        var btn = document.getElementById('run-composer-btn');
        var outputSection = document.getElementById('composer-output-section');
        var outputEl = document.getElementById('composer-output');
        var statusEl = document.getElementById('composer-status');

        // Disable button and show output section
        btn.disabled = true;
        btn.textContent = 'Running...';
        outputSection.style.display = 'block';
        outputEl.textContent = '';
        statusEl.innerHTML = '<div class="info-box warning">Running composer install...</div>';

        // Scroll to make output visible
        outputSection.scrollIntoView({ behavior: 'smooth', block: 'start' });

        // Start SSE connection with CSRF token for security
        var csrfToken = document.getElementById('csrf_token').value;
        var eventSource = new EventSource('?action=stream_composer&csrf_token=' + encodeURIComponent(csrfToken));

        eventSource.addEventListener('output', function(e) {
            outputEl.textContent += e.data + '\n';
            outputEl.scrollTop = outputEl.scrollHeight;
            // Keep page scrolled to bottom as output grows
            window.scrollTo(0, document.body.scrollHeight);
        });

        eventSource.addEventListener('error', function(e) {
            outputEl.textContent += 'Error: ' + e.data + '\n';
        });

        eventSource.addEventListener('done', function(e) {
            eventSource.close();
            var result = JSON.parse(e.data);
            var actionsEl = document.querySelector('.actions');

            if (result.success) {
                statusEl.innerHTML = '<div class="info-box success"><strong>Success!</strong> Dependencies installed.</div>';
                btn.style.display = 'none';
                document.getElementById('refresh-btn').style.display = 'none';
                document.getElementById('continue-btn').style.display = 'inline-flex';
            } else if (result.security_error) {
                // Security validation failed - reload to get new token
                statusEl.innerHTML = '<div class="info-box error"><strong>Security validation failed.</strong> Reloading page...</div>';
                setTimeout(function() { window.location.reload(); }, 2000);
            } else {
                statusEl.innerHTML = '<div class="info-box error"><strong>Installation failed.</strong> Check the output above for details.</div>';
                btn.disabled = false;
                btn.textContent = 'Run Composer';
            }

            // Scroll to bottom of page
            window.scrollTo({ top: document.body.scrollHeight, behavior: 'smooth' });
        });

        eventSource.onerror = function() {
            eventSource.close();
            btn.disabled = false;
            btn.textContent = 'Run Composer';
            statusEl.innerHTML = '<div class="info-box error"><strong>Connection error.</strong> The composer process may still be running. Try refreshing the page.</div>';
        };
    }
    </script>
</body>
</html>
        <?php
    }
    // phpcs:enable Generic.Files.LineLength
}

// Run the bootstrap installer
$bootstrap = new BootstrapInstaller();
$bootstrap->run();
