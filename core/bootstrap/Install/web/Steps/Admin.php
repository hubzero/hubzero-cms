<?php

/**
 * Admin User Creation Step
 *
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2025 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Bootstrap\Install\Web\Steps;

use Hubzero\User\User;
use Hubzero\User\Password;
use Hubzero\Access\Map;
use Hubzero\Base\Application;
use Hubzero\Database\ConnectionInterface;
use PDOException;

class Admin implements StepInterface
{
    private $installer;
    private $errors = [];
    private $existingAdmin = null;

    private const SUPER_USERS_GROUP_ID = 8;
    private const MIN_PASSWORD_LENGTH = 8;
    private const ADMIN_USER_ID = 1000;
    private const ADMIN_USERNAME = 'admin';
    private const SESSION_ERRORS_KEY = 'admin_step_errors';

    public function __construct($installer)
    {
        $this->installer = $installer;
        $this->checkExistingAdmin();

        // Restore errors from session (persists across redirects/reloads)
        if (!empty($_SESSION[self::SESSION_ERRORS_KEY])) {
            $this->errors = $_SESSION[self::SESSION_ERRORS_KEY];
        }
    }

    public function getTitle()
    {
        return 'Admin Account';
    }

    public function render()
    {
        $installer = $this->installer;
        $errors = $this->errors;
        $existingAdmin = $this->existingAdmin;
        $defaultEmail = $this->installer->getSessionData('settings')['mailfrom'] ?? '';
        ob_start();
        include __DIR__ . '/../views/steps/tmpl/admin.php';
        $content = ob_get_clean();

        // Clear errors from session after rendering (they've been displayed)
        unset($_SESSION[self::SESSION_ERRORS_KEY]);

        return $content;
    }

    public function process($data)
    {
        // Validate CSRF token
        if (!$this->installer->validateCsrf($data)) {
            return false;
        }

        // Check if we're updating an existing admin
        $isExistingAdmin = !empty($data['existing_admin']) && $this->existingAdmin;

        if ($isExistingAdmin) {
            return $this->processExistingAdmin($data);
        }

        return $this->processNewAdmin($data);
    }

    /**
     * Process existing admin - update name, email, and optionally password
     */
    private function processExistingAdmin($data)
    {
        // Validate name and email
        if (empty($data['name'])) {
            $this->errors['name'] = 'Full name is required';
        }
        if (empty($data['email'])) {
            $this->errors['email'] = 'Email is required';
        } elseif (
            !filter_var($data['email'], FILTER_VALIDATE_EMAIL)
            && !preg_match('/^[^@]+@[^@]+$/', $data['email'])
        ) {
            $this->errors['email'] = 'Invalid email format';
        }

        // Validate password only if provided
        $changePassword = !empty($data['password']);
        if ($changePassword) {
            if (strlen($data['password']) < self::MIN_PASSWORD_LENGTH) {
                $this->errors['password'] = 'Password must be at least ' . self::MIN_PASSWORD_LENGTH . ' characters';
            }
            if ($data['password'] !== ($data['password_confirm'] ?? '')) {
                $this->errors['password_confirm'] = 'Passwords do not match';
            }
        }

        if (!empty($this->errors)) {
            // Store errors in session so they persist across page reloads
            $_SESSION[self::SESSION_ERRORS_KEY] = $this->errors;
            return false;
        }

        // Initialize database connection for HUBzero ORM
        if (!$this->bootstrapApplication()) {
            return false;
        }

        // Update user using HUBzero User class
        try {
            $user = User::oneOrFail($this->existingAdmin['id']);

            // Parse name into parts
            $fullName = trim($data['name']);
            $nameParts = $this->parseNameParts($fullName);

            $user->set('name', $fullName);
            $user->set('givenName', $nameParts['givenName']);
            $user->set('middleName', $nameParts['middleName']);
            $user->set('surname', $nameParts['surname']);
            $user->set('email', trim($data['email']));

            if (!$user->save()) {
                $this->installer->addMessage('Failed to update admin user: ' . $user->getError(), 'error');
                return false;
            }

            // Update password if provided
            if ($changePassword) {
                if (!Password::changePassword($user->get('id'), $data['password'])) {
                    $this->installer->addMessage('Failed to update password.', 'error');
                    return false;
                }
            }

            $userData = [
                'id' => $user->get('id'),
                'name' => $user->get('name'),
                'email' => $user->get('email'),
                'username' => $user->get('username'),
            ];

            $this->installer->setSessionData('admin', $userData);
            $this->installer->markStepComplete('admin');
            return true;
        } catch (\Exception $e) {
            $this->installer->addMessage('Failed to update admin user: ' . $e->getMessage(), 'error');
            return false;
        }
    }

    /**
     * Process new admin creation
     */
    private function processNewAdmin($data)
    {
        // Validate required fields (username is fixed as 'admin')
        if (empty($data['name'])) {
            $this->errors['name'] = 'Full name is required';
        }
        if (empty($data['email'])) {
            $this->errors['email'] = 'Email is required';
        } elseif (
            !filter_var($data['email'], FILTER_VALIDATE_EMAIL)
            && !preg_match('/^[^@]+@[^@]+$/', $data['email'])
        ) {
            $this->errors['email'] = 'Invalid email format';
        }
        if (empty($data['password'])) {
            $this->errors['password'] = 'Password is required';
        } elseif (strlen($data['password']) < self::MIN_PASSWORD_LENGTH) {
            $this->errors['password'] = 'Password must be at least ' . self::MIN_PASSWORD_LENGTH . ' characters';
        }
        if ($data['password'] !== ($data['password_confirm'] ?? '')) {
            $this->errors['password_confirm'] = 'Passwords do not match';
        }

        if (!empty($this->errors)) {
            // Store errors in session so they persist across page reloads
            $_SESSION[self::SESSION_ERRORS_KEY] = $this->errors;
            return false;
        }

        // Initialize database connection for HUBzero ORM
        if (!$this->bootstrapApplication()) {
            return false;
        }

        // Create user using HUBzero User class
        try {
            $user = User::blank();

            // Parse name into parts
            $fullName = trim($data['name']);
            $nameParts = $this->parseNameParts($fullName);

            // Set fixed id and username
            $user->set('id', self::ADMIN_USER_ID);
            $user->set('name', $fullName);
            $user->set('givenName', $nameParts['givenName']);
            $user->set('middleName', $nameParts['middleName']);
            $user->set('surname', $nameParts['surname']);
            $user->set('username', self::ADMIN_USERNAME);
            $user->set('email', trim($data['email']));
            $user->set('password', ''); // Placeholder - real password set via Password class
            $user->set('usertype', 'Super Administrator');
            $user->set('block', 0);
            $user->set('approved', 2);
            $user->set('sendEmail', 1);
            $user->set('access', 1);
            $user->set('params', '');
            $user->set('registerDate', date('Y-m-d H:i:s'));
            $user->set('registerIP', $_SERVER['REMOTE_ADDR'] ?? '');
            $user->set('lastvisitDate', '0000-00-00 00:00:00');
            $user->set('lastResetTime', '0000-00-00 00:00:00');
            $user->set('activation', 0);
            $user->set('resetCount', 0);
            $user->set('usageAgreement', 0);
            $user->set('homeDirectory', '');
            $user->set('loginShell', '');
            $user->set('ftpShell', '');

            if (!$user->save()) {
                $this->installer->addMessage('Failed to create admin user: ' . $user->getError(), 'error');
                return false;
            }

            // Set password using Password class
            if (!Password::changePassword($user->get('id'), $data['password'])) {
                // Try to clean up the user if password setting failed
                $user->destroy();
                $this->installer->addMessage('Failed to set password for admin user.', 'error');
                return false;
            }

            // Add to Super Users group
            Map::addUserToGroup($user->get('id'), self::SUPER_USERS_GROUP_ID);

            $userData = [
                'id' => $user->get('id'),
                'name' => $user->get('name'),
                'username' => $user->get('username'),
                'email' => $user->get('email'),
            ];

            $this->installer->setSessionData('admin', $userData);
            $this->installer->markStepComplete('admin');
            return true;
        } catch (\Exception $e) {
            $this->installer->addMessage('Failed to create admin user: ' . $e->getMessage(), 'error');
            return false;
        }
    }

    /**
     * Check if admin user already exists
     */
    private function checkExistingAdmin()
    {
        // Load database config directly (no framework available in web installer)
        $configPath = PATH_APP . '/config/database.php';
        $dbConfig = file_exists($configPath) ? include $configPath : null;
        if (!$dbConfig || !is_array($dbConfig)) {
            return;
        }

        $connection = $this->installer->connectToDatabase($dbConfig);
        if (!$connection) {
            return;
        }

        $prefix = $dbConfig['dbprefix'] ?? 'jos_';

        try {
            $stmt = $connection->prepare(
                "SELECT u.id, u.name, u.username, u.email
                 FROM `{$prefix}users` u
                 INNER JOIN `{$prefix}user_usergroup_map` m ON u.id = m.user_id
                 WHERE m.group_id = ?
                 AND u.block = 0
                 LIMIT 1"
            );
            $connection->bind($stmt, [self::SUPER_USERS_GROUP_ID]);
            $connection->execute($stmt);
            $result = $connection->fetchAssoc($stmt);

            if ($result) {
                $this->existingAdmin = $result;
            }
        } catch (PDOException $e) {
            // Ignore
        }
    }

    /**
     * Bootstrap a full HUBzero application
     *
     * This initializes all Site service providers (database, events, etc.)
     * so that User, Password, and Map classes can function properly.
     *
     * @return bool True if application was bootstrapped successfully
     */
    private function bootstrapApplication()
    {
        static $booted = false;

        // Only bootstrap once
        if ($booted) {
            return true;
        }

        try {
            // Suppress deprecation warnings
            $oldErrorReporting = error_reporting();
            error_reporting($oldErrorReporting & ~E_DEPRECATED);

            // Check if an App is already running with database (when accessed via framework)
            if (class_exists('\App') && \Hubzero\Facades\App::has('db')) {
                $booted = true;
                error_reporting($oldErrorReporting);
                return true;
            }

            // Save installer session data before bootstrapping
            $installerSession = $_SESSION;

            // Create and load application with Site client
            $app = new Application();
            $app->load('site');

            // Disable database sessions during installation
            $app['config']->set('session_handler', 'none');

            // Boot the application
            $app->boot();

            // Restore error reporting level
            error_reporting($oldErrorReporting);

            // Restore installer session data
            foreach ($installerSession as $key => $value) {
                $_SESSION[$key] = $value;
            }

            $booted = true;
            return true;
        } catch (\Exception $e) {
            // Restore error reporting level
            error_reporting($oldErrorReporting);

            $this->installer->addMessage(
                'Failed to bootstrap application: ' . $e->getMessage(),
                'error'
            );
            return false;
        }
    }

    /**
     * Parse a full name into givenName, middleName, and surname
     *
     * @param   string  $name  Full name to parse
     * @return  array   Array with givenName, middleName, surname keys
     */
    private function parseNameParts($name)
    {
        $givenName = '';
        $middleName = '';
        $surname = '';

        $words = array_map('trim', explode(' ', $name));
        $count = count($words);

        if ($count == 1) {
            $givenName = $words[0];
        } elseif ($count == 2) {
            $givenName = $words[0];
            $surname = $words[1];
        } elseif ($count == 3) {
            $givenName = $words[0];
            $middleName = $words[1];
            $surname = $words[2];
        } else {
            $givenName = $words[0];
            $surname = $words[$count - 1];
            $middleName = $words[1];
            for ($i = 2; $i < $count - 1; $i++) {
                $middleName .= ' ' . $words[$i];
            }
        }

        return [
            'givenName' => $givenName,
            'middleName' => $middleName,
            'surname' => $surname,
        ];
    }
}
