<?php

/**
 * HUBzero Web Installation Wizard Controller
 *
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2025 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Bootstrap\Install\Web;

use Bootstrap\Install\Web\Steps\StepInterface;
use Bootstrap\Install\Web\Steps\Verify;
use Bootstrap\Install\Web\Steps\Welcome;
use Bootstrap\Install\Web\Steps\Requirements;
use Bootstrap\Install\Web\Steps\Database;
use Bootstrap\Install\Web\Steps\Settings;
use Bootstrap\Install\Web\Steps\Schema;
use Bootstrap\Install\Web\Steps\Admin;
use Bootstrap\Install\Web\Steps\Complete;
use Exception;
use Hubzero\Database\MysqlDatabaseConnection;
use PDO;
use PDOException;

// phpcs:disable PSR1.Files.SideEffects
// Load security, step classes, and shared utilities (intentional side effects - no autoloader available)
require_once __DIR__ . '/StorageCheck.php';
require_once __DIR__ . '/SecurityGuard.php';
require_once dirname(dirname(dirname(__DIR__))) . '/libraries/Hubzero/Database/MysqlDatabaseConnection.php';
require_once __DIR__ . '/Steps/StepInterface.php';
require_once __DIR__ . '/Steps/Verify.php';
require_once __DIR__ . '/Steps/Welcome.php';
require_once __DIR__ . '/Steps/Requirements.php';
require_once __DIR__ . '/Steps/Database.php';
require_once __DIR__ . '/Steps/Settings.php';
require_once __DIR__ . '/Steps/Schema.php';
require_once __DIR__ . '/Steps/Admin.php';
require_once __DIR__ . '/Steps/Complete.php';

/**
 * Main installer controller
 */
class Installer
{
    /**
     * Available steps in order
     *
     * @var array
     */
    private $steps = [
        'verify'       => Verify::class,
        'welcome'      => Welcome::class,
        'requirements' => Requirements::class,
        'database'     => Database::class,
        'settings'     => Settings::class,
        'schema'       => Schema::class,
        'admin'        => Admin::class,
        'complete'     => Complete::class,
    ];

    /**
     * Step display names
     *
     * @var array
     */
    private $stepNames = [
        'verify'       => 'Verify',
        'welcome'      => 'Welcome',
        'requirements' => 'Requirements',
        'database'     => 'Database',
        'settings'     => 'Settings',
        'schema'       => 'Schema',
        'admin'        => 'Admin',
        'complete'     => 'Complete',
    ];

    /**
     * Security guard instance
     *
     * @var SecurityGuard
     */
    private $security;

    /**
     * Current step
     *
     * @var string
     */
    private $currentStep;

    /**
     * Step instance
     *
     * @var StepInterface
     */
    private $stepInstance;

    /**
     * Flash messages
     *
     * @var array
     */
    private $messages = [];

    /**
     * Run the installer
     *
     * @return void
     */
    public function run(): void
    {
        // Initialize security guard
        $this->security = new SecurityGuard(defined('PATH_ROOT') ? PATH_ROOT : null);

        // Check if secure storage is available before anything else
        // This prevents cryptic fatal errors if storage can't be created
        $storageCheck = $this->security->checkStorageAvailability();
        if (!$storageCheck['available']) {
            $this->renderStorageError($storageCheck);
            return;
        }

        // Handle SSE streaming requests (before AJAX check)
        if (isset($_GET['action']) && $_GET['action'] === 'stream_migrations') {
            // Use hardened validation for SSE streaming (sensitive operation)
            $validation = $this->security->validateAjaxRequest('stream_migrations', false);
            if (!$validation['valid']) {
                header('Content-Type: text/event-stream');
                header('Cache-Control: no-cache');
                echo "event: error\ndata: " . ($validation['error'] ?? 'Security verification failed') . "\n\n";
                echo "event: done\ndata: " . json_encode(['success' => false, 'security_error' => true]) . "\n\n";
                return;
            }
            $this->streamMigrations();
            return;
        }

        // Handle AJAX requests
        if ($this->isAjax()) {
            // Basic verification check - detailed validation happens in handleAjax()
            if (!$this->verifySecurityForRequest()) {
                header('Content-Type: application/json');
                http_response_code(403);
                echo json_encode([
                    'error' => 'Security verification failed',
                    'redirect' => '?step=verify',
                    'security_error' => true
                ]);
                exit;
            }
            $this->handleAjax();
            return;
        }

        // Get current step based on system state and request
        // This ensures you can't skip ahead of incomplete steps
        $this->currentStep = $this->getCurrentStep();

        // If requested step differs from actual step, redirect
        $requestedStep = $_GET['step'] ?? null;
        if ($requestedStep && $requestedStep !== $this->currentStep) {
            $this->redirect($this->currentStep);
            return;
        }

        // For steps after verify, validate client hasn't changed
        if ($this->currentStep !== 'verify' && $this->currentStep !== 'complete') {
            $validation = $this->security->validateClient();
            if (!$validation['valid']) {
                $this->security->logSecurityEvent('CLIENT_VALIDATION_FAIL', $validation['error']);
                $this->addMessage($validation['error'], 'error');
                $this->currentStep = 'verify';
                $_SESSION['install_step'] = 'verify';
            }
        }

        // Update session with current step
        $_SESSION['install_step'] = $this->currentStep;

        // Load step class
        $this->stepInstance = $this->loadStep($this->currentStep);

        // Handle form submission
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $this->handlePost();
            return;
        }

        // Display current step
        $this->render();
    }

    /**
     * Verify security for AJAX/SSE requests (basic check)
     *
     * @return bool
     * @deprecated Use validateAjaxSecurity() for hardened validation
     */
    private function verifySecurityForRequest(): bool
    {
        // Skip verification check if not yet verified (verify step itself)
        if ($this->security->isVerificationRequired()) {
            return false;
        }

        $validation = $this->security->validateClient();
        return $validation['valid'];
    }

    /**
     * Validate AJAX request with full security hardening
     *
     * @param  string  $action       The action being performed
     * @param  bool    $requirePost  Whether POST method is required
     * @return array ['valid' => bool, 'error' => string|null]
     */
    private function validateAjaxSecurity(string $action, bool $requirePost = false): array
    {
        return $this->security->validateAjaxRequest($action, $requirePost);
    }

    /**
     * Check if this is an AJAX request
     *
     * @return bool
     */
    private function isAjax(): bool
    {
        return !empty($_SERVER['HTTP_X_REQUESTED_WITH'])
            && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) === 'xmlhttprequest';
    }

    /**
     * Handle AJAX requests with security hardening
     *
     * @return void
     */
    private function handleAjax(): void
    {
        header('Content-Type: application/json');

        $action = $_GET['action'] ?? '';

        // Define which actions require POST and which are sensitive (need stricter validation)
        $postRequiredActions = ['load_schema', 'create_database'];
        $sensitiveActions = ['load_schema', 'create_database', 'test_admin'];

        // Validate security for sensitive actions
        if (in_array($action, $sensitiveActions)) {
            $requirePost = in_array($action, $postRequiredActions);
            $validation = $this->validateAjaxSecurity($action, $requirePost);

            if (!$validation['valid']) {
                http_response_code(403);
                echo json_encode([
                    'success' => false,
                    'error' => $validation['error'],
                    'security_error' => true
                ]);
                exit;
            }
        }

        switch ($action) {
            case 'schema_progress':
                echo json_encode($this->getSchemaProgress());
                break;

            case 'load_schema':
                echo json_encode($this->loadSchemaAjax());
                break;

            case 'test_database':
                echo json_encode($this->testDatabaseAjax());
                break;

            case 'test_server':
                echo json_encode($this->testServerAjax());
                break;

            case 'test_admin':
                echo json_encode($this->testAdminAjax());
                break;

            case 'create_database':
                echo json_encode($this->createDatabaseAjax());
                break;

            default:
                echo json_encode(['error' => 'Unknown action']);
        }
        exit;
    }

    /**
     * Get current step from request or session
     *
     * @return string
     */
    private function getCurrentStep(): string
    {
        $requestedStep = $_GET['step'] ?? $_SESSION['install_step'] ?? null;

        // Get the actual step based on system state
        $actualStep = $this->getActualStep();

        // If no step requested, or invalid step, use actual step
        if (!$requestedStep || !isset($this->steps[$requestedStep])) {
            return $actualStep;
        }

        // Check if the requested step is accessible
        if (!$this->isStepAccessible($requestedStep)) {
            // Can't skip ahead - redirect to actual step
            return $actualStep;
        }

        return $requestedStep;
    }

    /**
     * Load a step class
     *
     * @param  string  $step  Step identifier
     * @return StepInterface
     */
    private function loadStep(string $step): StepInterface
    {
        $className = $this->steps[$step];
        return new $className($this);
    }

    /**
     * Handle POST form submission
     *
     * @return void
     */
    private function handlePost(): void
    {
        $result = $this->stepInstance->process($_POST);

        if ($result === true) {
            // Step completed successfully, move to next
            $nextStep = $this->getNextStep();
            $_SESSION['install_step'] = $nextStep;
            $this->redirect($nextStep);
        } elseif (is_string($result)) {
            // Error message
            $this->addMessage($result, 'error');
            $this->render();
        } else {
            // Stay on current step (validation errors handled by step)
            $this->render();
        }
    }

    /**
     * Get the next step
     *
     * @return string
     */
    private function getNextStep(): string
    {
        $keys = array_keys($this->steps);
        $index = array_search($this->currentStep, $keys);

        if ($index !== false && isset($keys[$index + 1])) {
            return $keys[$index + 1];
        }

        return 'complete';
    }

    /**
     * Get the previous step
     *
     * @return string|null
     */
    public function getPreviousStep(): ?string
    {
        $keys = array_keys($this->steps);
        $index = array_search($this->currentStep, $keys);

        if ($index !== false && $index > 0) {
            return $keys[$index - 1];
        }

        return null;
    }

    /**
     * Check if installation is complete
     *
     * @return bool
     */
    private function isInstalled(): bool
    {
        return $this->getActualStep() === 'complete';
    }

    /**
     * Determine the actual current step based on system state
     *
     * This checks real system state (files, database, etc.) to determine
     * which step the installer should be on, regardless of session state.
     *
     * @return string The step that should be shown
     */
    private function getActualStep(): string
    {
        // Step 1: Verify - check if security verification passed
        if ($this->security->isVerificationRequired()) {
            return 'verify';
        }

        // Step 2: Welcome - informational, always pass if verified
        // (no system state to check)

        // Step 3: Requirements - check PHP requirements
        // For now, assume requirements are met if we got past verify
        // A more thorough check could re-run the requirements checks

        // Step 4: Database - check if database.php config exists and connects
        $dbConfigPath = PATH_APP . '/config/database.php';
        if (!file_exists($dbConfigPath)) {
            return 'database';
        }

        try {
            $dbConfig = include $dbConfigPath;
            if (empty($dbConfig['host']) || empty($dbConfig['db']) || empty($dbConfig['user'])) {
                return 'database';
            }
            $pdo = $this->connectToDatabase($dbConfig);
            if (!$pdo) {
                return 'database';
            }
        } catch (Exception $e) {
            return 'database';
        }

        // Step 5: Settings - check if app.php config exists
        if (!file_exists(PATH_APP . '/config/app.php')) {
            return 'settings';
        }

        // Step 6: Schema - check if database tables exist
        try {
            $prefix = $dbConfig['dbprefix'] ?? 'jos_';
            $stmt = $pdo->query("SHOW TABLES LIKE '{$prefix}users'");
            if (!$stmt || $stmt->rowCount() === 0) {
                return 'schema';
            }
        } catch (Exception $e) {
            return 'schema';
        }

        // Step 7: Admin - check if admin user exists
        try {
            $stmt = $pdo->query("SELECT COUNT(*) FROM `{$prefix}users`");
            if (!$stmt || $stmt->fetchColumn() == 0) {
                return 'admin';
            }
        } catch (Exception $e) {
            return 'admin';
        }

        // All steps complete
        return 'complete';
    }

    /**
     * Check if a requested step is accessible
     *
     * A step is accessible if all previous steps are complete.
     *
     * @param  string  $requestedStep  The step being requested
     * @return bool
     */
    private function isStepAccessible(string $requestedStep): bool
    {
        $actualStep = $this->getActualStep();
        $stepOrder = array_keys($this->steps);

        $actualIndex = array_search($actualStep, $stepOrder);
        $requestedIndex = array_search($requestedStep, $stepOrder);

        if ($actualIndex === false || $requestedIndex === false) {
            return false;
        }

        // Can access the actual step or any step before it (to review)
        // But not steps after the actual step
        return $requestedIndex <= $actualIndex;
    }

    /**
     * Redirect to a step
     *
     * @param  string  $step  Step identifier
     * @return void
     */
    public function redirect(string $step): void
    {
        $url = $this->getBaseUrl() . '?step=' . $step;
        header('Location: ' . $url);
        exit;
    }

    /**
     * Get base URL for installer
     *
     * @return string
     */
    public function getBaseUrl(): string
    {
        $protocol = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') ? 'https' : 'http';
        $host = $_SERVER['HTTP_HOST'] ?? 'localhost';

        // When run through framework (index.php), use /install path
        // When run standalone, use the script directory
        if (defined('_HZEXEC_') || strpos($_SERVER['SCRIPT_NAME'], '/index.php') !== false) {
            return $protocol . '://' . $host . '/install';
        }

        $path = dirname($_SERVER['SCRIPT_NAME']);
        $path = rtrim($path, '/');
        return $protocol . '://' . $host . $path;
    }

    /**
     * Add a flash message
     *
     * @param  string  $message  Message text
     * @param  string  $type     Message type (success, error, warning, info)
     * @return void
     */
    public function addMessage(string $message, string $type = 'info'): void
    {
        $this->messages[] = ['text' => $message, 'type' => $type];
    }

    /**
     * Get all messages
     *
     * @return array
     */
    public function getMessages(): array
    {
        return $this->messages;
    }

    /**
     * Get session data
     *
     * @param  string  $key      Key name
     * @param  mixed   $default  Default value
     * @return mixed
     */
    public function getSessionData(string $key, $default = null)
    {
        return $_SESSION['install_data'][$key] ?? $default;
    }

    /**
     * Set session data
     *
     * @param  string  $key    Key name
     * @param  mixed   $value  Value
     * @return void
     */
    public function setSessionData(string $key, $value): void
    {
        $_SESSION['install_data'][$key] = $value;
    }

    /**
     * Get all steps with display names
     *
     * @return array
     */
    public function getSteps(): array
    {
        return $this->stepNames;
    }

    /**
     * Get current step identifier
     *
     * @return string
     */
    public function getCurrentStepId(): string
    {
        return $this->currentStep;
    }

    /**
     * Check if a step is complete
     *
     * @param  string  $step  Step identifier
     * @return bool
     */
    public function isStepComplete(string $step): bool
    {
        $completed = $_SESSION['completed_steps'] ?? [];
        return in_array($step, $completed);
    }

    /**
     * Mark a step as complete
     *
     * @param  string  $step  Step identifier
     * @return void
     */
    public function markStepComplete(string $step): void
    {
        if (!isset($_SESSION['completed_steps'])) {
            $_SESSION['completed_steps'] = [];
        }
        if (!in_array($step, $_SESSION['completed_steps'])) {
            $_SESSION['completed_steps'][] = $step;
        }
    }

    /**
     * Connect to database
     *
     * @param  array  $config  Database configuration
     * @return PDO|null
     */
    public function connectToDatabase(array $config): ?PDO
    {
        return MysqlDatabaseConnection::connect($config);
    }

    /**
     * Test database connection (AJAX)
     *
     * @return array
     */
    private function testDatabaseAjax(): array
    {
        $config = [
            'host' => $_POST['host'] ?? 'localhost',
            'db' => $_POST['db'] ?? '',
            'user' => $_POST['user'] ?? '',
            'password' => $_POST['password'] ?? '',
            'port' => $_POST['port'] ?? '',
            'socket' => $_POST['socket'] ?? '',
        ];

        try {
            $pdo = $this->connectToDatabase($config);
            if ($pdo) {
                return ['success' => true, 'message' => 'Connection successful!'];
            }
            return ['success' => false, 'message' => 'Connection failed'];
        } catch (Exception $e) {
            return ['success' => false, 'message' => $e->getMessage()];
        }
    }

    /**
     * Test server availability (AJAX) - checks if MySQL is reachable without credentials
     *
     * @return array
     */
    private function testServerAjax(): array
    {
        $connectionType = $_POST['connection_type'] ?? '';
        $host = $_POST['host'] ?? '127.0.0.1';
        $port = $_POST['port'] ?? '3306';
        $socket = $_POST['socket'] ?? '';

        try {
            $dsn = 'mysql:';
            if ($connectionType === 'socket' && !empty($socket)) {
                $dsn .= 'unix_socket=' . $socket;
            } else {
                $dsn .= 'host=' . $host;
                if (!empty($port)) {
                    $dsn .= ';port=' . $port;
                }
            }

            $options = [
                PDO::ATTR_TIMEOUT => 5,
                PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            ];

            // Try to connect - this will fail with access denied if server is up
            // but that's OK - we just want to know the server is reachable
            $pdo = new PDO($dsn, '', '', $options);
            return ['success' => true, 'message' => 'Server is available'];
        } catch (PDOException $e) {
            $code = $e->getCode();
            $msg = $e->getMessage();

            // Error 1045 = Access denied - server IS reachable, just need credentials
            // Error 1044 = Access denied for user - server IS reachable
            if ($code == 1045 || $code == 1044 || strpos($msg, 'Access denied') !== false) {
                return ['success' => true, 'message' => 'Server is available'];
            }

            // Error 2002 = Can't connect to local MySQL server through socket
            if ($code == 2002 || strpos($msg, 'No such file') !== false) {
                if ($connectionType === 'socket') {
                    return ['success' => false, 'message' => 'Socket file not found or MySQL is not running'];
                }
                return ['success' => false, 'message' => 'Cannot connect - MySQL may not be running'];
            }

            // Error 2003 = Can't connect to MySQL server
            if ($code == 2003) {
                return ['success' => false, 'message' => 'Cannot connect to MySQL server at this address'];
            }

            // Connection refused
            if (strpos($msg, 'Connection refused') !== false) {
                return ['success' => false, 'message' => 'Connection refused - MySQL may not be running on this port'];
            }

            return ['success' => false, 'message' => 'Cannot reach MySQL server: ' . $msg];
        }
    }

    /**
     * Test admin credentials (AJAX) - checks if admin can connect with privileges
     *
     * @return array
     */
    private function testAdminAjax(): array
    {
        $connectionType = $_POST['connection_type'] ?? '';
        $host = $_POST['host'] ?? '127.0.0.1';
        $port = $_POST['port'] ?? '3306';
        $socket = $_POST['socket'] ?? '';
        $adminUser = $_POST['admin_user'] ?? '';
        $adminPass = $_POST['admin_password'] ?? '';

        if (empty($adminUser)) {
            return ['success' => false, 'message' => 'Admin username is required'];
        }

        try {
            $dsn = 'mysql:';
            if ($connectionType === 'socket' && !empty($socket)) {
                $dsn .= 'unix_socket=' . $socket;
            } else {
                $dsn .= 'host=' . $host;
                if (!empty($port)) {
                    $dsn .= ';port=' . $port;
                }
            }
            $dsn .= ';charset=utf8mb4';

            $options = [
                PDO::ATTR_TIMEOUT => 10,
                PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            ];

            $pdo = new PDO($dsn, $adminUser, $adminPass, $options);

            // Check if user has CREATE DATABASE privilege
            $stmt = $pdo->query("SHOW GRANTS FOR CURRENT_USER()");
            $grants = $stmt->fetchAll(PDO::FETCH_COLUMN);
            $hasPrivileges = false;

            foreach ($grants as $grant) {
                if (
                    stripos($grant, 'ALL PRIVILEGES') !== false ||
                    stripos($grant, 'CREATE') !== false
                ) {
                    $hasPrivileges = true;
                    break;
                }
            }

            if (!$hasPrivileges) {
                return [
                    'success' => false,
                    'message' => 'User does not have CREATE DATABASE privileges'
                ];
            }

            return ['success' => true, 'message' => 'Admin connection successful with CREATE privileges'];
        } catch (PDOException $e) {
            $msg = $e->getMessage();

            if (strpos($msg, 'Access denied') !== false) {
                return ['success' => false, 'message' => 'Access denied - check username and password'];
            }

            return ['success' => false, 'message' => 'Connection failed: ' . $msg];
        }
    }

    /**
     * Create database and user (AJAX)
     *
     * @return array
     */
    private function createDatabaseAjax(): array
    {
        $connectionType = $_POST['connection_type'] ?? '';
        $host = $_POST['host'] ?? '127.0.0.1';
        $port = $_POST['port'] ?? '3306';
        $socket = $_POST['socket'] ?? '';
        $adminUser = $_POST['admin_user'] ?? '';
        $adminPass = $_POST['admin_password'] ?? '';
        $newDb = $_POST['new_db'] ?? '';
        $newUser = $_POST['new_user'] ?? '';
        $newPass = $_POST['new_password'] ?? '';

        // Table prefix: use existing config if set, otherwise default to 'jos_'
        $newPrefix = 'jos_';
        $configPath = PATH_APP . '/config/database.php';
        if (file_exists($configPath)) {
            $existingConfig = include $configPath;
            if (is_array($existingConfig) && !empty($existingConfig['dbprefix'])) {
                $newPrefix = $existingConfig['dbprefix'];
            }
        }

        if (empty($newDb) || empty($newUser)) {
            return ['success' => false, 'message' => 'Database name and username are required'];
        }

        try {
            $dsn = 'mysql:';
            if ($connectionType === 'socket' && !empty($socket)) {
                $dsn .= 'unix_socket=' . $socket;
            } else {
                $dsn .= 'host=' . $host;
                if (!empty($port)) {
                    $dsn .= ';port=' . $port;
                }
            }
            $dsn .= ';charset=utf8mb4';

            $options = [
                PDO::ATTR_TIMEOUT => 30,
                PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            ];

            $pdo = new PDO($dsn, $adminUser, $adminPass, $options);

            // Create database
            $dbName = preg_replace('/[^a-zA-Z0-9_]/', '', $newDb);
            $pdo->exec("CREATE DATABASE IF NOT EXISTS `{$dbName}` CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci");

            // Create user and grant privileges
            $userName = preg_replace('/[^a-zA-Z0-9_]/', '', $newUser);

            // Determine host for user (localhost for socket/localhost connections)
            $userHost = ($connectionType === 'socket' || $host === '127.0.0.1' || $host === 'localhost')
                ? 'localhost'
                : '%';

            // Drop user if exists (ignore errors)
            try {
                $pdo->exec("DROP USER IF EXISTS '{$userName}'@'{$userHost}'");
            } catch (PDOException $e) {
                // Ignore - user might not exist
            }

            // Create user with password
            $escapedPass = $pdo->quote($newPass);
            $pdo->exec("CREATE USER '{$userName}'@'{$userHost}' IDENTIFIED BY {$escapedPass}");

            // Grant privileges
            $pdo->exec("GRANT ALL PRIVILEGES ON `{$dbName}`.* TO '{$userName}'@'{$userHost}'");
            $pdo->exec("FLUSH PRIVILEGES");

            // Save configuration
            $config = [
                'dbtype' => 'mysql',
                'host' => ($connectionType === 'socket') ? 'localhost' : $host,
                'port' => ($connectionType === 'socket') ? '' : ($port ?: '3306'),
                'socket' => ($connectionType === 'socket') ? $socket : '',
                'db' => $dbName,
                'user' => $userName,
                'password' => $newPass,
                'dbprefix' => $newPrefix,
            ];

            $this->saveDatabaseConfig($config);

            // Store in session for later steps
            $this->setSessionData('database', $config);
            $this->markStepComplete('database');

            return [
                'success' => true,
                'message' => "Database '{$dbName}' and user '{$userName}' created successfully!"
            ];
        } catch (PDOException $e) {
            $msg = $e->getMessage();

            if (strpos($msg, 'Access denied') !== false) {
                return ['success' => false, 'message' => 'Access denied - admin user lacks privileges'];
            }

            return ['success' => false, 'message' => 'Failed to create database: ' . $msg];
        }
    }

    /**
     * Save database configuration file
     *
     * @param  array  $config  Database configuration
     * @return bool
     */
    private function saveDatabaseConfig(array $config): bool
    {
        $configDir = PATH_APP . '/config';

        if (!is_dir($configDir)) {
            if (!mkdir($configDir, 0755, true)) {
                return false;
            }
        }

        $content = "<?php\n\n";
        $content .= "/**\n";
        $content .= " * Database configuration\n";
        $content .= " * Generated by HUBzero installer on " . date('Y-m-d H:i:s') . "\n";
        $content .= " */\n\n";
        $content .= "return array(\n";
        $content .= "    'dbtype'   => 'mysql',\n";
        $content .= "    'host'     => " . var_export($config['host'], true) . ",\n";
        $content .= "    'user'     => " . var_export($config['user'], true) . ",\n";
        $content .= "    'password' => " . var_export($config['password'], true) . ",\n";
        $content .= "    'db'       => " . var_export($config['db'], true) . ",\n";
        $content .= "    'dbprefix' => " . var_export($config['dbprefix'], true) . ",\n";

        if (!empty($config['port'])) {
            $content .= "    'port'     => " . var_export($config['port'], true) . ",\n";
        }
        if (!empty($config['socket'])) {
            $content .= "    'socket'   => " . var_export($config['socket'], true) . ",\n";
        }

        $content .= ");\n";

        return file_put_contents($configDir . '/database.php', $content) !== false;
    }

    /**
     * Get schema loading progress
     *
     * @return array
     */
    private function getSchemaProgress(): array
    {
        return [
            'progress' => $_SESSION['schema_progress'] ?? 0,
            'status' => $_SESSION['schema_status'] ?? 'pending',
            'message' => $_SESSION['schema_message'] ?? '',
        ];
    }

    /**
     * Load schema via AJAX
     *
     * @return array
     */
    private function loadSchemaAjax(): array
    {
        $type = $_POST['type'] ?? 'schema';

        // Load the CLI Schema helper
        require_once PATH_CORE . '/libraries/Hubzero/Console/Command/Install/Schema.php';

        $_SESSION['schema_status'] = 'running';
        $_SESSION['schema_progress'] = 0;

        try {
            $result = false;
            switch ($type) {
                case 'schema':
                    $result = \Hubzero\Console\Command\Install\Schema::load(false, PATH_APP, PATH_CORE);
                    break;
                case 'data':
                    $result = \Hubzero\Console\Command\Install\Schema::loadData(false, PATH_APP, PATH_CORE);
                    break;
                case 'sample':
                    $result = \Hubzero\Console\Command\Install\Schema::loadSampleData(false, PATH_APP, PATH_CORE);
                    break;
            }

            $_SESSION['schema_status'] = $result ? 'complete' : 'error';
            $_SESSION['schema_progress'] = 100;

            return [
                'success' => $result,
                'message' => $result ? ucfirst($type) . ' loaded successfully' : 'Failed to load ' . $type,
            ];
        } catch (Exception $e) {
            $_SESSION['schema_status'] = 'error';
            return [
                'success' => false,
                'message' => $e->getMessage(),
            ];
        }
    }

    /**
     * Stream migration output via Server-Sent Events
     *
     * @return void
     */
    private function streamMigrations(): void
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

        $musePath = PATH_CORE . '/bin/muse';

        if (!file_exists($musePath)) {
            $this->sendSSE('error', 'Muse executable not found');
            $this->sendSSE('done', json_encode(['success' => false]));
            return;
        }

        // Build the migration command
        // -f = full run (not dry run)
        // --no-ansi = no color codes
        $command = sprintf(
            'cd %s && %s %s migration run -f --no-ansi 2>&1',
            escapeshellarg(PATH_ROOT),
            escapeshellarg(PHP_BINARY),
            escapeshellarg($musePath)
        );

        // Open process with pipes
        $descriptors = [
            0 => ['pipe', 'r'],  // stdin
            1 => ['pipe', 'w'],  // stdout
            2 => ['pipe', 'w'],  // stderr
        ];

        $process = proc_open($command, $descriptors, $pipes);

        if (!is_resource($process)) {
            $this->sendSSE('error', 'Failed to start migration process');
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

        $this->sendSSE('done', json_encode([
            'success' => $success,
            'returnCode' => $returnCode
        ]));
    }

    /**
     * Send an SSE event
     *
     * @param  string  $event  Event name
     * @param  string  $data   Event data
     * @return void
     */
    private function sendSSE(string $event, string $data): void
    {
        // Filter out unwanted lines (similar to bootstrap composer output)
        if ($event === 'output') {
            // Skip empty lines
            if (trim($data) === '') {
                return;
            }
        }

        echo "event: {$event}\n";
        echo "data: {$data}\n\n";
        flush();
    }

    /**
     * Render the current step
     *
     * @return void
     */
    private function render(): void
    {
        $step = $this->stepInstance;
        $installer = $this;
        $content = $step->render();
        $title = $step->getTitle();
        $stepId = $this->currentStep;
        $messages = $this->messages;

        include __DIR__ . '/views/layout.php';
    }

    /**
     * Render the storage error page
     *
     * Displays a friendly error when secure storage cannot be created.
     *
     * @param  array  $storageCheck  Result from SecurityGuard::checkStorageAvailability()
     * @return void
     */
    private function renderStorageError(array $storageCheck): void
    {
        $tried = $storageCheck['tried'];
        $docRoot = $storageCheck['docRoot'];
        $installer = $this;

        include __DIR__ . '/views/storage-error.php';
    }

    /**
     * Get the security guard instance
     *
     * @return SecurityGuard
     */
    public function getSecurityGuard(): SecurityGuard
    {
        if (!$this->security) {
            $this->security = new SecurityGuard(defined('PATH_ROOT') ? PATH_ROOT : null);
        }
        return $this->security;
    }

    /**
     * Perform cleanup after installation is complete
     *
     * This removes the verification file and clears all session data.
     *
     * @return bool
     */
    public function cleanup(): bool
    {
        $this->security->logSecurityEvent('INSTALL_COMPLETE', 'Installation completed successfully');
        return $this->security->cleanup();
    }

    /**
     * Validate CSRF token from form submission
     *
     * @param  array  $data  POST data containing csrf_token
     * @return bool
     */
    public function validateCsrf(array $data): bool
    {
        $token = $data['csrf_token'] ?? null;
        $valid = $this->getSecurityGuard()->validateCsrfToken($token);

        if (!$valid) {
            $this->getSecurityGuard()->logSecurityEvent('CSRF_FAIL', 'Invalid CSRF token');
            $this->addMessage('Security validation failed. Please try again.', 'error');
        }

        return $valid;
    }
}
