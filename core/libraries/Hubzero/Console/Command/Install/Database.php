<?php

/**
 * @package    framework
 * @copyright  Copyright (c) 2005-2025 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Hubzero\Console\Command\Install;

use Hubzero\Database\Connection\PdoConnection;
use Hubzero\Database\Exception\ConnectionFailedException;

/**
 * Database configuration helper class
 *
 * This class handles database setup during installation.
 * It can be used both by minimal muse (before autoloading) and full muse.
 **/
class Database
{
    /**
     * Supported database types
     *
     * @var array
     **/
    private const SUPPORTED_TYPES = [
        'mysql' => 'MySQL (PDO)',
    ];

    /**
     * Planned but not yet supported database types
     *
     * @var array
     **/
    private const UNSUPPORTED_TYPES = [
        'pgsql'  => 'PostgreSQL',
        'sqlite' => 'SQLite',
    ];

    /**
     * Track if terminal has been modified (for cleanup)
     *
     * @var bool
     **/
    private static $terminalModified = false;

    /**
     * MySQL connection type options
     *
     * @var array
     **/
    private const MYSQL_CONNECTION_TYPES = [
        1 => [
            'label'  => 'localhost',
            'dsn'    => 'mysql:host=127.0.0.1;port=3306',
            'host'   => '127.0.0.1',
            'port'   => 3306,
            'socket' => null,
        ],
        2 => [
            'label'  => 'unix socket',
            'dsn'    => 'mysql:unix_socket=/var/run/mysqld/mysqld.sock',
            'host'   => 'localhost',
            'port'   => null,
            'socket' => '/var/run/mysqld/mysqld.sock',
        ],
        3 => [
            'label'  => 'custom',
            'dsn'    => null,
            'host'   => null,
            'port'   => null,
            'socket' => null,
        ],
        4 => [
            'label'  => 'exit',
            'dsn'    => null,
            'host'   => null,
            'port'   => null,
            'socket' => null,
        ],
    ];

    /**
     * Load existing database configuration from config file
     *
     * @param   string  $appPath  Path to the app directory
     * @return  array   Existing configuration values (empty array if none)
     */
    public static function loadExistingConfig($appPath)
    {
        $existing = [];

        $dbConfig = $appPath . '/config/database.php';
        if (file_exists($dbConfig)) {
            $config = include $dbConfig;
            if (is_array($config)) {
                if (!empty($config['host'])) {
                    $existing['host'] = $config['host'];
                }
                if (!empty($config['user'])) {
                    $existing['username'] = $config['user'];
                }
                if (isset($config['password'])) {
                    $existing['password'] = $config['password'];
                }
                if (!empty($config['db'])) {
                    $existing['database'] = $config['db'];
                }
                if (!empty($config['socket'])) {
                    $existing['socket'] = $config['socket'];
                }
                if (!empty($config['port'])) {
                    $existing['port'] = $config['port'];
                }
            }
        }

        return $existing;
    }

    /**
     * Test existing database configuration
     *
     * @param   array  $existing  Existing configuration
     * @param   bool   $ansi      Whether to use ANSI colors
     * @return  bool   True if connection works
     */
    public static function testExistingConfig($existing, $ansi = true)
    {
        if (empty($existing['database']) || empty($existing['username'])) {
            return false;
        }

        try {
            // Build DSN
            if (!empty($existing['socket'])) {
                $dsn = "mysql:unix_socket={$existing['socket']};dbname={$existing['database']}";
            } else {
                $host = $existing['host'] ?? 'localhost';
                $port = $existing['port'] ?? 3306;
                $dsn = "mysql:host={$host};port={$port};dbname={$existing['database']}";
            }

            $pdo = self::connectWithPdoConnectorFromDsn(
                $dsn,
                $existing['username'],
                $existing['password'] ?? '',
                []
            );

            // Quick test query
            $pdo->query('SELECT 1');
            return true;
        } catch (\PDOException $e) {
            return false;
        }
    }

    /**
     * Configure the database connection
     *
     * @param   bool    $ansi      Whether to use ANSI color output
     * @param   string  $appPath   Path to the app directory
     * @param   array   $existing  Existing configuration to use as defaults
     * @return  bool|null  True on success, false on failure, null if user exited
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
        self::output("\e[33mConfigure Database\e[39m\n", $ansi);
        self::output("------------------\n", $ansi);
        self::output("\n", $ansi);

        // If we have existing config, test it first
        if (!empty($existing)) {
            self::output("Testing existing database configuration... ", $ansi);
            if (self::testExistingConfig($existing, $ansi)) {
                self::output("\e[32mConnected!\e[39m\n", $ansi);
                self::output("\n", $ansi);
                self::output("Existing configuration:\n", $ansi);
                self::output("  Database: \e[32m{$existing['database']}\e[39m\n", $ansi);
                self::output("  User:     \e[32m{$existing['username']}\e[39m\n", $ansi);
                self::output("  Host:     \e[32m" . ($existing['host'] ?? 'localhost') . "\e[39m\n", $ansi);
                if (!empty($existing['socket'])) {
                    self::output("  Socket:   \e[32m{$existing['socket']}\e[39m\n", $ansi);
                }
                self::output("\n", $ansi);

                // Ask if they want to keep existing config
                self::output("Keep existing database configuration? [Y/n] ", $ansi);
                $response = strtolower(trim(fgets(STDIN)));
                if ($response === '' || $response === 'y' || $response === 'yes') {
                    self::output("\n", $ansi);
                    self::output("\e[32mUsing existing database configuration.\e[39m\n", $ansi);
                    return true;
                }
                self::output("\n", $ansi);
            } else {
                self::output("\e[31mFailed.\e[39m\n", $ansi);
                self::output("\n", $ansi);
                self::output("Existing configuration could not connect. Let's reconfigure.\n", $ansi);
                self::output("\n", $ansi);
            }
        }

        // Select database type
        $dbType = self::selectDatabaseType($ansi);

        if ($dbType === false) {
            return false;
        }

        self::output("\n", $ansi);
        self::output("\e[32mUsing " . self::SUPPORTED_TYPES[$dbType] . ".\e[39m\n", $ansi);

        // Select connection type (MySQL specific)
        if ($dbType === 'mysql') {
            $connection = self::selectMySqlConnection($ansi, $existing);

            if ($connection === null) {
                return null; // User chose to exit
            }

            if ($connection === false) {
                return false; // Actual failure
            }
        }

        // Write the database configuration file
        self::output("\n", $ansi);
        self::output("\e[33mWriting Database Configuration\e[39m\n", $ansi);
        self::output("------------------------------\n", $ansi);

        $dbConfig = [
            'dbtype'   => 'mysql',
            'host'     => $connection['host'] ?? 'localhost',
            'user'     => $connection['username'] ?? '',
            'password' => $connection['password'] ?? '',
            'db'       => $connection['database'] ?? '',
            'dbprefix' => 'jos_',
        ];

        // Add socket if using socket connection
        if (!empty($connection['socket'])) {
            $dbConfig['socket'] = $connection['socket'];
        }

        // Add port if not default
        if (!empty($connection['port']) && $connection['port'] != 3306) {
            $dbConfig['port'] = $connection['port'];
        }

        if (!AppDirectory::writeDatabaseConfig($ansi, $appPath, $dbConfig)) {
            return false;
        }

        self::output("\n", $ansi);
        self::output("\e[32mDatabase configuration complete!\e[39m\n", $ansi);
        self::output("\n", $ansi);
        self::output("  Database: \e[32m{$dbConfig['db']}\e[39m\n", $ansi);
        self::output("  User:     \e[32m{$dbConfig['user']}\e[39m\n", $ansi);
        self::output("  Host:     \e[32m{$dbConfig['host']}\e[39m\n", $ansi);
        self::output("  Config:   \e[32m{$appPath}/config/database.php\e[39m\n", $ansi);

        return true;
    }

    /**
     * Select database type
     *
     * @param   bool  $ansi  Whether to use ANSI colors
     * @return  string|false  Database type or false on failure
     **/
    private static function selectDatabaseType($ansi)
    {
        self::output("\e[33mDatabase Type\e[39m\n", $ansi);

        // Show supported types
        foreach (self::SUPPORTED_TYPES as $type => $label) {
            self::output("  \e[32m[X]\e[39m {$label}\n", $ansi);
        }

        // Show unsupported types
        foreach (self::UNSUPPORTED_TYPES as $type => $label) {
            self::output("  [ ] {$label} (not yet supported)\n", $ansi);
        }

        // For now, MySQL is the only option
        return 'mysql';
    }

    /**
     * Select MySQL connection type
     *
     * @param   bool   $ansi      Whether to use ANSI colors
     * @param   array  $existing  Existing configuration to use as defaults
     * @return  array|null|false  Connection configuration, null if user exited, false on failure
     **/
    private static function selectMySqlConnection($ansi, $existing = [])
    {
        self::output("\n", $ansi);
        self::output("\e[33mMySQL Connection\e[39m\n", $ansi);
        self::output("\n", $ansi);

        // Build options array for interactive menu with DSN info
        $options = [];
        $keys = [];

        // Filter connection types - skip Unix socket on Windows
        $connectionTypes = [];
        foreach (self::MYSQL_CONNECTION_TYPES as $key => $config) {
            // Skip Unix socket option on Windows
            if ($key === 2 && PHP_OS_FAMILY === 'Windows') {
                continue;
            }
            $connectionTypes[$key] = $config;
        }

        // Calculate max label length for alignment
        $maxLen = 0;
        foreach ($connectionTypes as $config) {
            $maxLen = max($maxLen, strlen($config['label']));
        }

        // Determine default selection based on existing config
        $defaultIndex = 0;
        if (!empty($existing)) {
            if (!empty($existing['socket'])) {
                // Socket connection - find index of unix socket option
                $socketIndex = array_search(2, array_keys($connectionTypes));
                if ($socketIndex !== false) {
                    $defaultIndex = $socketIndex;
                }
            } elseif (
                !empty($existing['host'])
                && $existing['host'] !== 'localhost'
                && $existing['host'] !== '127.0.0.1'
            ) {
                // Custom host - find index of custom option
                $customIndex = array_search(3, array_keys($connectionTypes));
                if ($customIndex !== false) {
                    $defaultIndex = $customIndex;
                }
            }
            // Otherwise default to localhost (index 0)
        }

        foreach ($connectionTypes as $key => $config) {
            if ($config['dsn']) {
                $padding = str_repeat(' ', $maxLen - strlen($config['label']));
                $options[] = $config['label'] . $padding . "  \e[90m" . $config['dsn'] . "\e[39m";
            } else {
                $options[] = $config['label'];
            }
            $keys[] = $key;
        }

        // Get user selection via interactive menu
        // Pass false for showBackspace since this is the top-level menu (backspace disabled)
        $selectedIndex = self::interactiveMenu($options, $defaultIndex, $ansi, false);

        $selectedKey = $keys[$selectedIndex];
        $connection = self::MYSQL_CONNECTION_TYPES[$selectedKey];

        // Handle exit option
        if ($connection['label'] === 'exit') {
            self::output("\n", $ansi);
            self::output("Exiting...\n", $ansi);
            self::output("\n", $ansi);
            return null;
        }

        // Handle custom connection option
        if ($connection['label'] === 'custom') {
            $connection = self::promptCustomConnection($ansi);

            // User chose to go back
            if ($connection === null) {
                return self::selectMySqlConnection($ansi, $existing);
            }

            // User chose to exit
            if ($connection === false) {
                return null;
            }
        }

        self::output("\n", $ansi);
        self::output("\e[32mUsing {$connection['label']} connection.\e[39m\n", $ansi);

        // Test the connection (loop for retry)
        while (true) {
            $testResult = self::testConnection($connection['dsn'], $ansi);

            if ($testResult === 'reachable') {
                // MySQL is reachable, now authenticate
                $authResult = self::authenticateConnection($connection, $ansi, $existing);

                if ($authResult === null) {
                    // User chose to go back
                    return self::selectMySqlConnection($ansi, $existing);
                }

                if ($authResult === false) {
                    // User chose to exit
                    return null;
                }

                // Return connection with credentials
                return $authResult;
            }

            // MySQL not reachable - show options menu
            self::output("\n", $ansi);
            self::output("\e[33mWhat would you like to do?\e[39m\n", $ansi);
            self::output("\n", $ansi);

            $options = ['Try the connection again...', 'Try a new MySQL connection...', 'Back', 'Exit'];
            $selected = self::interactiveMenu($options, 0, $ansi, true);

            // Handle backspace (go back)
            if ($selected === -1 || $options[$selected] === 'Back') {
                return self::selectMySqlConnection($ansi, $existing);
            }

            if ($options[$selected] === 'Try the connection again...') {
                // Loop will retry the test
                continue;
            }

            if ($options[$selected] === 'Try a new MySQL connection...') {
                return self::selectMySqlConnection($ansi, $existing);
            }

            if ($options[$selected] === 'Exit') {
                self::output("\n", $ansi);
                self::output("Exiting...\n", $ansi);
                self::output("\n", $ansi);
                return null;
            }
        }
    }

    /**
     * Test if MySQL is reachable at the given DSN
     *
     * Attempts a connection without credentials to see if MySQL responds.
     * "Access denied" means MySQL is there; connection errors mean it's not.
     *
     * @param   string  $dsn   The DSN to test
     * @param   bool    $ansi  Whether to use ANSI colors
     * @return  string  'reachable', 'unreachable', or 'error'
     **/
    private static function testConnection($dsn, $ansi)
    {
        self::output("\n", $ansi);
        self::output("Testing connection to {$dsn} ... ", $ansi);

        try {
            // Attempt connection with no credentials and short timeout
            $options = [];

            // This will likely fail, but HOW it fails tells us if MySQL is there
            self::connectWithPdoConnectorFromDsn($dsn, '', '', $options);

            // If we get here, connection succeeded (anonymous access enabled)
            self::output("\e[32mMySQL is reachable.\e[39m\n", $ansi);
            return 'reachable';
        } catch (\PDOException $e) {
            $code = $e->getCode();
            $message = $e->getMessage();

            // Error codes that indicate MySQL IS running and reachable:
            // 1045 = Access denied (needs credentials)
            // 1044 = Access denied for user to database
            // 1049 = Unknown database
            // 1698 = Access denied (auth plugin issue)
            if (
                in_array($code, [1045, 1044, 1049, 1698]) ||
                stripos($message, 'Access denied') !== false
            ) {
                self::output("\e[32mMySQL is reachable.\e[39m\n", $ansi);
                return 'reachable';
            }

            // Error codes that indicate MySQL is NOT reachable:
            // 2002 = Can't connect to local MySQL server through socket
            // 2003 = Can't connect to MySQL server
            // 2005 = Unknown MySQL server host
            // 2006 = MySQL server has gone away
            // 2013 = Lost connection to MySQL server
            self::output("\e[31mFailed.\e[39m\n", $ansi);
            self::output("\n", $ansi);

            // Provide helpful error message based on the connection type
            if (stripos($dsn, 'unix_socket') !== false) {
                self::output("\e[31mCould not connect via Unix socket.\e[39m\n", $ansi);
                self::output("Check that MySQL is running and the socket path is correct.\n", $ansi);
            } elseif (
                stripos($message, 'Connection refused') !== false ||
                      in_array($code, [2002, 2003])
            ) {
                self::output("\e[31mConnection refused.\e[39m\n", $ansi);
                self::output("Check that MySQL is running and accepting connections.\n", $ansi);
            } elseif (stripos($message, 'Unknown') !== false || $code == 2005) {
                self::output("\e[31mHost not found.\e[39m\n", $ansi);
                self::output("Check the hostname is correct.\n", $ansi);
            } else {
                self::output("\e[31mConnection error:\e[39m {$message}\n", $ansi);
            }

            return 'unreachable';
        }
    }

    /**
     * Prompt for custom connection details
     *
     * @param   bool  $ansi  Whether to use ANSI colors
     * @return  array|null|false  Connection config, null to go back, false to exit
     **/
    private static function promptCustomConnection($ansi)
    {
        self::output("\n", $ansi);
        self::output("\e[33mConnection Method\e[39m\n", $ansi);
        self::output("\n", $ansi);

        // Build options - skip socket on Windows
        $options = ['hostname'];
        if (PHP_OS_FAMILY !== 'Windows') {
            $options[] = 'unix socket';
        }
        $options[] = 'back';
        $options[] = 'exit';

        $selected = self::interactiveMenu($options, 0, $ansi);

        // Handle backspace (go back)
        if ($selected === -1) {
            return null;
        }

        // Handle "back" option
        if ($options[$selected] === 'back') {
            return null;
        }

        // Handle "exit" option
        if ($options[$selected] === 'exit') {
            self::output("\n", $ansi);
            self::output("Exiting...\n", $ansi);
            self::output("\n", $ansi);
            return false;
        }

        self::output("\n", $ansi);

        if ($options[$selected] === 'hostname') {
            return self::promptHostnameConnection($ansi);
        } else {
            return self::promptSocketConnection($ansi);
        }
    }

    /**
     * Prompt for hostname-based connection details
     *
     * @param   bool  $ansi  Whether to use ANSI colors
     * @return  array  Connection configuration
     **/
    private static function promptHostnameConnection($ansi)
    {
        // Host
        self::output("Hostname [localhost]: ", $ansi);
        $host = trim(fgets(STDIN));
        if ($host === '') {
            $host = 'localhost';
        }

        // Port
        self::output("Port [3306]: ", $ansi);
        $portInput = trim(fgets(STDIN));
        $port = ($portInput === '') ? 3306 : (int) $portInput;

        // Build DSN
        $dsn = "mysql:host={$host}";
        if ($port !== 3306) {
            $dsn .= ";port={$port}";
        }

        return [
            'label'  => "({$dsn})",
            'dsn'    => $dsn,
            'host'   => $host,
            'port'   => $port,
            'socket' => null,
        ];
    }

    /**
     * Prompt for socket-based connection details
     *
     * @param   bool  $ansi  Whether to use ANSI colors
     * @return  array  Connection configuration
     **/
    private static function promptSocketConnection($ansi)
    {
        self::output("Socket path [/var/run/mysqld/mysqld.sock]: ", $ansi);
        $socket = trim(fgets(STDIN));
        if ($socket === '') {
            $socket = '/var/run/mysqld/mysqld.sock';
        }

        // Build DSN
        $dsn = "mysql:unix_socket={$socket}";

        return [
            'label'  => "({$dsn})",
            'dsn'    => $dsn,
            'host'   => 'localhost',
            'port'   => null,
            'socket' => $socket,
        ];
    }

    /**
     * Authenticate with the database and set up credentials
     *
     * First attempts to connect without credentials to check for automatic admin access.
     * If that works and has admin privileges, offers to create a dedicated database user.
     * Otherwise, offers a menu to either enter admin credentials or use existing credentials.
     *
     * @param   array  $connection  Connection configuration
     * @param   bool   $ansi        Whether to use ANSI colors
     * @param   array  $existing    Existing configuration to use as defaults
     * @return  array|null|false    Connection with credentials, null to go back, false to exit
     **/
    private static function authenticateConnection($connection, $ansi, $existing = [])
    {
        self::output("\n", $ansi);
        self::output("\e[33mChecking for elevated database credentials\e[39m\n", $ansi);
        self::output("\n", $ansi);

        // Always try passwordless admin access first
        // If running with sudo, escalate to potentially benefit from unix socket auth
        $adminResult = self::tryAdminConnection($connection, $ansi, $existing);

        if ($adminResult !== null) {
            // Got credentials (either created or user chose to enter existing)
            return $adminResult;
        }

        // No automatic admin access - offer options
        return self::promptCredentialOptions($connection, $ansi, $existing);
    }

    /**
     * Offer options for database credentials when no automatic admin access
     *
     * @param   array  $connection  Connection configuration
     * @param   bool   $ansi        Whether to use ANSI colors
     * @param   array  $existing    Existing configuration to use as defaults
     * @return  array|null|false    Connection with credentials, null to go back, false to exit
     **/
    private static function promptCredentialOptions($connection, $ansi, $existing = [])
    {
        self::output("Choose how to set up database credentials:\n", $ansi);
        self::output("\n", $ansi);

        $options = [
            'Enter MySQL admin credentials to create a new user',
            'Enter credentials for an existing MySQL account',
            'Back',
            'Exit'
        ];

        // If we have existing credentials, default to "existing account" option
        $defaultIndex = !empty($existing['username']) ? 1 : 0;

        $selected = self::interactiveMenu($options, $defaultIndex, $ansi, true);

        // Handle backspace (go back)
        if ($selected === -1 || $options[$selected] === 'Back') {
            return null;
        }

        if ($options[$selected] === 'Exit') {
            self::output("\n", $ansi);
            self::output("Exiting...\n", $ansi);
            self::output("\n", $ansi);
            return false;
        }

        if ($options[$selected] === 'Enter credentials for an existing MySQL account') {
            self::output("\n", $ansi);
            return self::promptCredentials($connection, $ansi, $existing);
        }

        // Enter admin credentials to create a new user
        return self::promptAdminCredentials($connection, $ansi, $existing);
    }

    /**
     * Prompt for MySQL admin credentials to create a new user
     *
     * @param   array  $connection  Connection configuration
     * @param   bool   $ansi        Whether to use ANSI colors
     * @param   array  $existing    Existing configuration to use as defaults
     * @return  array|null|false    Connection with credentials, null to go back, false to exit
     **/
    private static function promptAdminCredentials($connection, $ansi, $existing = [])
    {
        self::output("\n", $ansi);
        self::output("\e[33mMySQL Administrator Credentials\e[39m\n", $ansi);
        self::output("\n", $ansi);
        self::output("Enter credentials for a MySQL account with CREATE USER privileges.\n", $ansi);
        self::output("\n", $ansi);

        // Prompt for admin username
        self::output("Admin username [root]: ", $ansi);
        $adminUser = trim(fgets(STDIN));
        if ($adminUser === '') {
            $adminUser = 'root';
        }

        // Prompt for admin password
        $adminPass = self::promptPassword("Admin password: ", $ansi);

        self::output("\n", $ansi);
        self::output("Testing admin credentials... ", $ansi);

        // Try to connect with admin credentials
        while (true) {
            try {
                $options = [];

                $adminPdo = self::connectWithPdoConnectorFromDsn($connection['dsn'], $adminUser, $adminPass, $options);

                // Check who we connected as
                $stmt = $adminPdo->query('SELECT CURRENT_USER()');
                $currentUser = $stmt->fetchColumn();

                // Verify this user has admin privileges
                if (!self::checkAdminPrivileges($adminPdo, $ansi)) {
                    self::output("\e[33mConnected but lacks admin privileges.\e[39m\n", $ansi);
                    self::output("\n", $ansi);
                    $msg = "The user '{$adminUser}' does not have CREATE USER, "
                        . "GRANT, or CREATE DATABASE privileges.\n";
                    self::output($msg, $ansi);
                    self::output("\n", $ansi);

                    // Show options menu
                    self::output("\e[33mWhat would you like to do?\e[39m\n", $ansi);
                    self::output("\n", $ansi);

                    $options = [
                        'Try a different admin account',
                        'Use this account for HUBzero instead',
                        'Back',
                        'Exit'
                    ];
                    $selected = self::interactiveMenu($options, 0, $ansi, true);

                    if ($selected === -1 || $options[$selected] === 'Back') {
                        return null;
                    }

                    if ($options[$selected] === 'Exit') {
                        self::output("\n", $ansi);
                        self::output("Exiting...\n", $ansi);
                        self::output("\n", $ansi);
                        return false;
                    }

                    if ($options[$selected] === 'Use this account for HUBzero instead') {
                        // Use these credentials directly
                        $connection['username'] = $adminUser;
                        $connection['password'] = $adminPass;
                        $connection['pdo'] = $adminPdo;

                        self::output("\n", $ansi);
                        self::output("Connected as: \e[32m{$currentUser}\e[39m\n", $ansi);

                        return $connection;
                    }

                    // Try a different admin account - re-prompt
                    self::output("\n", $ansi);

                    self::output("Admin username [{$adminUser}]: ", $ansi);
                    $newUser = trim(fgets(STDIN));
                    if ($newUser !== '') {
                        $adminUser = $newUser;
                    }

                    $adminPass = self::promptPassword("Admin password: ", $ansi);

                    self::output("\n", $ansi);
                    self::output("Testing admin credentials... ", $ansi);
                    continue;
                }

                self::output("\e[32mConnected as {$currentUser}\e[39m\n", $ansi);

                // Now offer to create a user
                return self::offerUserCreation($connection, $adminPdo, $ansi, $existing);
            } catch (\PDOException $e) {
                $code = $e->getCode();

                self::output("\e[31mFailed.\e[39m\n", $ansi);
                self::output("\n", $ansi);

                // Access denied - wrong credentials
                if (
                    in_array($code, [1045, 1044, 1698]) ||
                    stripos($e->getMessage(), 'Access denied') !== false
                ) {
                    self::output("\e[31mAccess denied.\e[39m The username or password is incorrect.\n", $ansi);
                } else {
                    self::output("\e[31mConnection error:\e[39m {$e->getMessage()}\n", $ansi);
                }

                // Show options menu
                self::output("\n", $ansi);
                self::output("\e[33mWhat would you like to do?\e[39m\n", $ansi);
                self::output("\n", $ansi);

                $options = ['Try again with different credentials', 'Use existing account instead', 'Back', 'Exit'];
                $selected = self::interactiveMenu($options, 0, $ansi, true);

                // Handle backspace (go back)
                if ($selected === -1 || $options[$selected] === 'Back') {
                    return null;
                }

                if ($options[$selected] === 'Exit') {
                    self::output("\n", $ansi);
                    self::output("Exiting...\n", $ansi);
                    self::output("\n", $ansi);
                    return false;
                }

                if ($options[$selected] === 'Use existing account instead') {
                    self::output("\n", $ansi);
                    return self::promptCredentials($connection, $ansi);
                }

                // Try again - prompt for new credentials
                self::output("\n", $ansi);

                self::output("Admin username [{$adminUser}]: ", $ansi);
                $newUser = trim(fgets(STDIN));
                if ($newUser !== '') {
                    $adminUser = $newUser;
                }

                $adminPass = self::promptPassword("Admin password: ", $ansi);

                self::output("\n", $ansi);
                self::output("Testing admin credentials... ", $ansi);
            }
        }
    }

    /**
     * Try to connect as MySQL admin and create a user
     *
     * Attempts to connect without credentials and checks if the connected user
     * has privileges to CREATE USER, GRANT, and CREATE DATABASE.
     *
     * @param   array  $connection  Connection configuration
     * @param   bool   $ansi        Whether to use ANSI colors
     * @param   array  $existing    Existing configuration to use as defaults
     * @return  array|null|false    Connection with credentials, null if should prompt instead, false to exit
     **/
    private static function tryAdminConnection($connection, $ansi, $existing = [])
    {
        self::output("Checking for MySQL administrative access...\n", $ansi);

        $options = [];

        $adminPdo = null;
        $escalated = false;
        $lastError = null;

        // Determine what the user's requested connection targets
        $requestedHost = $connection['host'] ?? 'localhost';
        $requestedPort = $connection['port'] ?? 3306;
        $requestedSocket = $connection['socket'] ?? null;
        $isLocalConnection = self::isLocalHost($requestedHost);

        // Strategy 1: Try credentials from current user's ~/.my.cnf file
        // Use POSIX to look up the actual home directory based on effective user ID
        // rather than relying on environment variables which may be stale (e.g., from sudo)
        $homeDir = null;
        if (function_exists('posix_getpwuid') && function_exists('posix_geteuid')) {
            $userInfo = posix_getpwuid(posix_geteuid());
            if ($userInfo && !empty($userInfo['dir'])) {
                $homeDir = $userInfo['dir'];
            }
        }
        // Fallback to environment variables for systems without POSIX extensions
        if ($homeDir === null) {
            $homeDir = getenv('HOME') ?: (getenv('USERPROFILE') ?: null);
        }
        if ($homeDir !== null) {
            $myCnfPath = $homeDir . '/.my.cnf';
            if (!file_exists($myCnfPath)) {
                self::output("  Trying {$myCnfPath} ... \e[90mnot found\e[39m\n", $ansi);
            } else {
                $myCnf = self::parseMyCnf($myCnfPath);
                if ($myCnf === null) {
                    self::output("  Trying {$myCnfPath} ... \e[90mno [client] credentials\e[39m\n", $ansi);
                } else {
                    $myCnfHost = $myCnf['host'] ?? 'localhost';
                    $myCnfPort = (int) ($myCnf['port'] ?? 3306);
                    $myCnfIsLocal = self::isLocalHost($myCnfHost);

                    // Check compatibility:
                    // - If both are local connections, they're compatible
                    // - If both target the same remote host:port, they're compatible
                    $compatible = false;
                    if ($isLocalConnection && $myCnfIsLocal) {
                        // Both local - compatible (socket or localhost TCP)
                        $compatible = true;
                    } elseif (!$isLocalConnection && !$myCnfIsLocal) {
                        // Both remote - check host and port match
                        if (
                            strtolower($requestedHost) === strtolower($myCnfHost) &&
                            $requestedPort === $myCnfPort
                        ) {
                            $compatible = true;
                        }
                    }

                    if ($compatible) {
                        $adminDsn = self::buildDsnFromMyCnf($myCnf);
                        $adminUser = $myCnf['user'] ?? '';
                        $adminPass = $myCnf['password'] ?? '';
                        // Build display DSN with credentials included
                        $displayDsn = $adminDsn;
                        if ($adminUser !== '') {
                            $displayDsn .= ";user={$adminUser}";
                            if ($adminPass !== '') {
                                $displayDsn .= ";password=" . str_repeat('*', strlen($adminPass));
                            }
                        }
                        self::output("  Trying {$displayDsn} (from {$myCnfPath}) ... ", $ansi);
                        try {
                            $adminPdo = self::connectWithPdoConnectorFromDsn(
                                $adminDsn,
                                $adminUser,
                                $adminPass,
                                $options
                            );
                            self::output("\e[32mOK\e[39m\n", $ansi);
                            // Check privileges immediately - if lacking, try next strategy
                            if (!self::checkAdminPrivileges($adminPdo, $ansi)) {
                                self::output(
                                    "  \e[33mConnection succeeded but lacks required "
                                        . "privileges, trying next...\e[39m\n",
                                    $ansi
                                );
                                $adminPdo = null;
                            }
                        } catch (\PDOException $e) {
                            self::output("\e[31mFAIL\e[39m\n", $ansi);
                            $lastError = $e;
                            $adminPdo = null;
                        }
                    }
                }
            }
        }

        // Strategy 2: Try the selected connection DSN with no credentials
        // This catches cases where MySQL allows anonymous access or passwordless root
        if ($adminPdo === null) {
            self::output("  Trying {$connection['dsn']} ... ", $ansi);
            try {
                $adminPdo = self::connectWithPdoConnectorFromDsn($connection['dsn'], '', '', $options);
                self::output("\e[32mOK\e[39m\n", $ansi);
                // Check privileges immediately - if lacking, try next strategy
                if (!self::checkAdminPrivileges($adminPdo, $ansi)) {
                    self::output(
                        "  \e[33mConnection succeeded but lacks required privileges, trying next...\e[39m\n",
                        $ansi
                    );
                    $adminPdo = null;
                }
            } catch (\PDOException $e) {
                self::output("\e[31mFAIL\e[39m\n", $ansi);
                $lastError = $e;
                $adminPdo = null;
            }
        }

        // Strategy 3: Try socket auth with current Unix user (auth_socket plugin)
        // Only for socket connections - MySQL auth_socket plugin authenticates based on Unix user
        if ($adminPdo === null && $requestedSocket !== null) {
            // Prefer POSIX functions over environment variables since they reflect the actual
            // effective user after privilege manipulation (e.g., after dropping from sudo)
            $currentUser = null;
            if (function_exists('posix_getpwuid') && function_exists('posix_geteuid')) {
                $userInfo = posix_getpwuid(posix_geteuid());
                $currentUser = $userInfo['name'] ?? null;
            }
            if (!$currentUser) {
                $currentUser = getenv('USER') ?: getenv('USERNAME');
            }
            if ($currentUser && file_exists($requestedSocket)) {
                self::output("  Trying {$connection['dsn']};user={$currentUser} ... ", $ansi);
                try {
                    $adminPdo = self::connectWithPdoConnectorFromDsn($connection['dsn'], $currentUser, '', $options);
                    self::output("\e[32mOK\e[39m\n", $ansi);
                    // Check privileges immediately - if lacking, try next strategy
                    if (!self::checkAdminPrivileges($adminPdo, $ansi)) {
                        self::output(
                            "  \e[33mConnection succeeded but lacks required privileges, trying next...\e[39m\n",
                            $ansi
                        );
                        $adminPdo = null;
                    }
                } catch (\PDOException $e) {
                    self::output("\e[31mFAIL\e[39m\n", $ansi);
                    $lastError = $e;
                    $adminPdo = null;
                }
            }
        }

        // If non-escalated strategies failed and we can escalate, try with root privileges
        if ($adminPdo === null && \function_exists('muse_escalate_privileges')) {
            $escalated = \muse_escalate_privileges();

            if ($escalated) {
                // Strategy 1: Check for /root/.my.cnf credentials file (sudo)
                // Only use if the .my.cnf settings are compatible with the requested connection
                $rootMyCnfPath = '/root/.my.cnf';
                if (!file_exists($rootMyCnfPath)) {
                    self::output("  Trying (sudo) {$rootMyCnfPath} ... \e[90mnot found\e[39m\n", $ansi);
                } else {
                    $myCnf = self::parseMyCnf($rootMyCnfPath);
                    if ($myCnf === null) {
                        self::output(
                            "  Trying (sudo) {$rootMyCnfPath} ... \e[90mno [client] credentials\e[39m\n",
                            $ansi
                        );
                    } else {
                        $myCnfHost = $myCnf['host'] ?? 'localhost';
                        $myCnfPort = (int) ($myCnf['port'] ?? 3306);
                        $myCnfSocket = $myCnf['socket'] ?? null;
                        $myCnfIsLocal = self::isLocalHost($myCnfHost);

                        // Check compatibility:
                        // - If both are local connections, they're compatible
                        // - If both target the same remote host:port, they're compatible
                        $compatible = false;
                        if ($isLocalConnection && $myCnfIsLocal) {
                            // Both local - compatible (socket or localhost TCP)
                            $compatible = true;
                        } elseif (!$isLocalConnection && !$myCnfIsLocal) {
                            // Both remote - check host and port match
                            if (
                                strtolower($requestedHost) === strtolower($myCnfHost) &&
                                $requestedPort === $myCnfPort
                            ) {
                                $compatible = true;
                            }
                        }

                        if ($compatible) {
                            $adminDsn = self::buildDsnFromMyCnf($myCnf);
                            $adminUser = $myCnf['user'] ?? 'root';
                            $adminPass = $myCnf['password'] ?? '';
                            // Build display DSN with credentials included
                            $displayDsn = $adminDsn;
                            if ($adminUser !== '') {
                                $displayDsn .= ";user={$adminUser}";
                                if ($adminPass !== '') {
                                    $displayDsn .= ";password=" . str_repeat('*', strlen($adminPass));
                                }
                            }
                            self::output("  Trying (sudo) {$displayDsn} (from {$rootMyCnfPath}) ... ", $ansi);
                            try {
                                $adminPdo = self::connectWithPdoConnectorFromDsn(
                                    $adminDsn,
                                    $adminUser,
                                    $adminPass,
                                    $options
                                );
                                self::output("\e[32mOK\e[39m\n", $ansi);
                                // Check privileges immediately - if lacking, try next strategy
                                if (!self::checkAdminPrivileges($adminPdo, $ansi)) {
                                    self::output(
                                        "  \e[33mConnection succeeded but lacks required "
                                            . "privileges, trying next...\e[39m\n",
                                        $ansi
                                    );
                                    $adminPdo = null;
                                }
                            } catch (\PDOException $e) {
                                self::output("\e[31mFAIL\e[39m\n", $ansi);
                                $lastError = $e;
                                $adminPdo = null;
                            }
                        }
                    }
                }

                // Strategy 2: Try the selected connection DSN with no credentials (sudo)
                if ($adminPdo === null) {
                    self::output("  Trying (sudo) {$connection['dsn']} ... ", $ansi);
                    try {
                        $adminPdo = self::connectWithPdoConnectorFromDsn($connection['dsn'], '', '', $options);
                        self::output("\e[32mOK\e[39m\n", $ansi);
                        // Check privileges immediately - if lacking, try next strategy
                        if (!self::checkAdminPrivileges($adminPdo, $ansi)) {
                            self::output(
                                "  \e[33mConnection succeeded but lacks required privileges, trying next...\e[39m\n",
                                $ansi
                            );
                            $adminPdo = null;
                        }
                    } catch (\PDOException $e) {
                        self::output("\e[31mFAIL\e[39m\n", $ansi);
                        $lastError = $e;
                        $adminPdo = null;
                    }
                }

                // Strategy 3: Try socket auth with current Unix user (auth_socket plugin)
                // Only for socket connections - after escalation, effective user is root
                if ($adminPdo === null && $requestedSocket !== null) {
                    $currentUser = null;
                    if (function_exists('posix_getpwuid') && function_exists('posix_geteuid')) {
                        $userInfo = posix_getpwuid(posix_geteuid());
                        $currentUser = $userInfo['name'] ?? null;
                    }
                    if (!$currentUser) {
                        $currentUser = getenv('USER') ?: getenv('USERNAME');
                    }
                    if ($currentUser && file_exists($requestedSocket)) {
                        self::output("  Trying (sudo) {$connection['dsn']};user={$currentUser} ... ", $ansi);
                        try {
                            $adminPdo = self::connectWithPdoConnectorFromDsn(
                                $connection['dsn'],
                                $currentUser,
                                '',
                                $options
                            );
                            self::output("\e[32mOK\e[39m\n", $ansi);
                            // Check privileges immediately - if lacking, continue (no more strategies)
                            if (!self::checkAdminPrivileges($adminPdo, $ansi)) {
                                self::output(
                                    "  \e[33mConnection succeeded but lacks required privileges.\e[39m\n",
                                    $ansi
                                );
                                $adminPdo = null;
                            }
                        } catch (\PDOException $e) {
                            self::output("\e[31mFAIL\e[39m\n", $ansi);
                            $lastError = $e;
                            $adminPdo = null;
                        }
                    }
                }
            }
        }

        // If we still don't have a connection, handle the error
        if ($adminPdo === null) {
            // Drop back to unprivileged user
            if ($escalated && \function_exists('muse_drop_privileges')) {
                \muse_drop_privileges();
            }

            self::output("\n", $ansi);
            self::output("\e[31mNo elevated access available.\e[39m\n", $ansi);
            self::output("\n", $ansi);
            return null;
        }

        // We have a connection with verified privileges - check who we connected as
        $stmt = $adminPdo->query('SELECT CURRENT_USER()');
        $currentUser = $stmt->fetchColumn();

        self::output("\n", $ansi);
        self::output("\e[32mElevated access confirmed as {$currentUser}\e[39m\n", $ansi);

        // Drop back to unprivileged user
        if ($escalated && \function_exists('muse_drop_privileges')) {
            \muse_drop_privileges();
        }

        // Offer to create a new user or use existing credentials
        return self::offerUserCreation($connection, $adminPdo, $ansi, $existing);
    }

    /**
     * Check if the current MySQL connection has admin privileges
     *
     * Checks for CREATE USER, GRANT, and CREATE DATABASE privileges.
     *
     * @param   \PDO  $pdo   PDO connection to check
     * @param   bool  $ansi  Whether to use ANSI colors (for output)
     * @return  bool  True if has admin privileges, false otherwise
     **/
    private static function checkAdminPrivileges($pdo, $ansi = true)
    {
        try {
            // Check global privileges
            $stmt = $pdo->query("SHOW GRANTS FOR CURRENT_USER()");
            $grants = [];
            while (($grant = $stmt->fetchColumn()) !== false) {
                $grants[] = $grant;
            }

            $hasAllPrivileges = false;
            $hasCreateUser = false;
            $hasGrant = false;
            $hasCreateDb = false;

            foreach ($grants as $grant) {
                $grantUpper = strtoupper($grant);

                // ALL PRIVILEGES includes everything
                if (strpos($grantUpper, 'ALL PRIVILEGES') !== false) {
                    $hasAllPrivileges = true;
                    $hasCreateUser = true;
                    $hasGrant = true;
                    $hasCreateDb = true;
                }

                // Check individual privileges
                if (strpos($grantUpper, 'CREATE USER') !== false) {
                    $hasCreateUser = true;
                }
                if (strpos($grantUpper, 'WITH GRANT OPTION') !== false) {
                    $hasGrant = true;
                }
                if (
                    strpos($grantUpper, 'CREATE') !== false &&
                    strpos($grantUpper, 'ON *.*') !== false
                ) {
                    $hasCreateDb = true;
                }
            }

            // Output results of each privilege check
            self::output("\n", $ansi);
            self::output("  Checking MySQL privileges:\n", $ansi);

            if ($hasAllPrivileges) {
                self::output("    ALL PRIVILEGES ... \e[32mYES\e[39m\n", $ansi);
            } else {
                $createUserStatus = $hasCreateUser ? "\e[32mYES\e[39m" : "\e[31mNO\e[39m";
                $grantStatus = $hasGrant ? "\e[32mYES\e[39m" : "\e[31mNO\e[39m";
                $createDbStatus = $hasCreateDb ? "\e[32mYES\e[39m" : "\e[31mNO\e[39m";

                self::output("    CREATE USER ... {$createUserStatus}\n", $ansi);
                self::output("    GRANT OPTION ... {$grantStatus}\n", $ansi);
                self::output("    CREATE DATABASE ... {$createDbStatus}\n", $ansi);
            }

            // Need all three privileges
            return $hasCreateUser && $hasGrant && $hasCreateDb;
        } catch (\PDOException $e) {
            self::output("\n", $ansi);
            self::output("  \e[31mFailed to check privileges: {$e->getMessage()}\e[39m\n", $ansi);
            return false;
        }
    }

    /**
     * Offer to create a new database user or use existing credentials
     *
     * @param   array  $connection  Connection configuration
     * @param   \PDO   $adminPdo    Admin PDO connection
     * @param   bool   $ansi        Whether to use ANSI colors
     * @param   array  $existing    Existing configuration to use as defaults
     * @return  array|null|false    Connection with credentials, null to go back, false to exit
     **/
    private static function offerUserCreation($connection, $adminPdo, $ansi, $existing = [])
    {
        self::output("\n", $ansi);
        self::output("You have elevated MySQL access. You can:\n", $ansi);
        self::output("\n", $ansi);

        $options = [
            'Create a new database and credentials for this site',
            'Enter existing database name and credentials',
            'Back',
            'Exit'
        ];

        // If we have existing credentials, default to "existing" option
        $defaultIndex = !empty($existing['username']) ? 1 : 0;

        $selected = self::interactiveMenu($options, $defaultIndex, $ansi, true);

        // Handle backspace (go back)
        if ($selected === -1 || $options[$selected] === 'Back') {
            return null;
        }

        if ($options[$selected] === 'Exit') {
            self::output("\n", $ansi);
            self::output("Exiting...\n", $ansi);
            self::output("\n", $ansi);
            return false;
        }

        if ($options[$selected] === 'Enter existing database name and credentials') {
            self::output("\n", $ansi);
            return self::promptCredentials($connection, $ansi, $existing);
        }

        // Create a new user
        return self::createDatabaseUser($connection, $adminPdo, $ansi, $existing);
    }

    /**
     * Create a new database and credentials for this site
     *
     * @param   array  $connection  Connection configuration
     * @param   \PDO   $adminPdo    Admin PDO connection
     * @param   bool   $ansi        Whether to use ANSI colors
     * @param   array  $existing    Existing configuration to use as defaults
     * @return  array|null|false    Connection with credentials, null to go back, false to exit
     **/
    private static function createDatabaseUser($connection, $adminPdo, $ansi, $existing = [])
    {
        self::output("\n", $ansi);
        self::output("\e[33mCreate Database and Credentials\e[39m\n", $ansi);
        self::output("\n", $ansi);
        self::output("\e[90mEsc clears input, Esc when empty goes back, Ctrl-C exits\e[39m\n", $ansi);
        self::output("\n", $ansi);

        // Determine host for the user (localhost for socket, or the connection host)
        $userHost = 'localhost';

        // Use existing values as defaults
        $defaultDb = $existing['database'] ?? 'hubzero';
        $defaultUser = $existing['username'] ?? 'hubzero';

        // Database name loop - Esc when empty goes back to menu
        while (true) {
            $database = self::interactiveInput("Database name", $defaultDb, $ansi);
            if ($database === false) {
                self::output("\n", $ansi);
                self::output("Exiting...\n", $ansi);
                self::output("\n", $ansi);
                return false;
            }
            if ($database === null) {
                return null; // Go back to menu
            }

            // Username loop - Esc when empty goes back to database name
            while (true) {
                $username = self::interactiveInput("Username", $defaultUser, $ansi);
                if ($username === false) {
                    self::output("\n", $ansi);
                    self::output("Exiting...\n", $ansi);
                    self::output("\n", $ansi);
                    return false;
                }
                if ($username === null) {
                    break; // Go back to database name prompt
                }

                // Check if user already exists BEFORE prompting for password
                try {
                    // Escalate privileges for user check
                    if (\function_exists('muse_escalate_privileges')) {
                        \muse_escalate_privileges();
                    }

                    $stmt = $adminPdo->prepare("SELECT COUNT(*) FROM mysql.user WHERE user = ? AND host = ?");
                    $stmt->execute([$username, $userHost]);
                    $userExists = (int) $stmt->fetchColumn() > 0;

                    // Drop privileges
                    if (\function_exists('muse_drop_privileges')) {
                        \muse_drop_privileges();
                    }
                } catch (\PDOException $e) {
                    // Drop privileges on error
                    if (\function_exists('muse_drop_privileges')) {
                        \muse_drop_privileges();
                    }
                    self::output("\n", $ansi);
                    self::output("\e[31mFailed to check if user exists.\e[39m\n", $ansi);
                    self::output("Error: " . $e->getMessage() . "\n", $ansi);
                    return null;
                }

                // If user exists, handle that case
                if ($userExists) {
                    $existingResult = self::handleExistingUser(
                        $connection,
                        $adminPdo,
                        $database,
                        $username,
                        $userHost,
                        $ansi
                    );
                    // null = go back to username, false = exit, array = success
                    if ($existingResult === null) {
                        continue; // Back to username prompt
                    }
                    return $existingResult; // false or success array
                }

                // User doesn't exist - password loop (Esc when empty goes back to username)
                while (true) {
                    $defaultPassword = self::generatePassword(16);
                    $password = self::interactiveInput("Password", $defaultPassword, $ansi);
                    if ($password === false) {
                        self::output("\n", $ansi);
                        self::output("Exiting...\n", $ansi);
                        self::output("\n", $ansi);
                        return false;
                    }
                    if ($password === null) {
                        break; // Go back to username prompt
                    }

                    // Got all three values - proceed with user creation
                    return self::executeUserCreation(
                        $connection,
                        $adminPdo,
                        $database,
                        $username,
                        $password,
                        $userHost,
                        $ansi
                    );
                }
                // Password was null (back), continue to username prompt
            }
            // Username was null (back), continue to database prompt
        }
    }

    /**
     * Handle the case when user already exists
     *
     * @param   array   $connection  Connection configuration
     * @param   \PDO    $adminPdo    Admin PDO connection
     * @param   string  $database    Database name
     * @param   string  $username    Username
     * @param   string  $userHost    User host
     * @param   bool    $ansi        Whether to use ANSI colors
     * @return  array|null|false     Connection with credentials, null to go back, false to exit
     **/
    private static function handleExistingUser($connection, $adminPdo, $database, $username, $userHost, $ansi)
    {
        self::output("\n", $ansi);
        self::output("\e[33mUser '{$username}'@'{$userHost}' already exists.\e[39m\n", $ansi);
        self::output("\n", $ansi);

        self::output("\e[33mWhat would you like to do?\e[39m\n", $ansi);
        self::output("\n", $ansi);

        $options = [
            'Use this user (enter existing password)',
            'Reset password for this user',
            'Choose a different username',
            'Back',
            'Exit'
        ];
        $selected = self::interactiveMenu($options, 0, $ansi, true);

        if ($selected === -1 || $options[$selected] === 'Back') {
            return null; // Go back to username prompt
        }

        if ($options[$selected] === 'Exit') {
            self::output("\n", $ansi);
            self::output("Exiting...\n", $ansi);
            self::output("\n", $ansi);
            return false;
        }

        if ($options[$selected] === 'Choose a different username') {
            return null; // Go back to username prompt
        }

        if ($options[$selected] === 'Reset password for this user') {
            // Prompt for new password
            self::output("\n", $ansi);
            $newPassword = self::promptPassword("New password for '{$username}': ", $ansi);
            self::output("\n", $ansi);

            // Escalate privileges for password change
            if (\function_exists('muse_escalate_privileges')) {
                \muse_escalate_privileges();
            }

            self::output("Resetting password... ", $ansi);

            try {
                $adminPdo->exec("ALTER USER " . $adminPdo->quote($username) . "@" . $adminPdo->quote($userHost) .
                               " IDENTIFIED BY " . $adminPdo->quote($newPassword));
                $adminPdo->exec("FLUSH PRIVILEGES");
                self::output("\e[32mDone.\e[39m\n", $ansi);
            } catch (\PDOException $e) {
                self::output("\e[31mFailed.\e[39m\n", $ansi);
                self::output("Error: " . $e->getMessage() . "\n", $ansi);

                // Drop privileges
                if (\function_exists('muse_drop_privileges')) {
                    \muse_drop_privileges();
                }

                return null; // Go back to username prompt
            }

            // Drop privileges
            if (\function_exists('muse_drop_privileges')) {
                \muse_drop_privileges();
            }

            // Test the new credentials
            self::output("Testing new credentials... ", $ansi);
            $result = self::testAndReturnCredentials($connection, $username, $newPassword, $ansi);

            if ($result !== null) {
                // Create database and grant privileges
                if (!self::createDatabaseAndGrant($adminPdo, $database, $username, $userHost, $ansi)) {
                    return null; // Go back to username prompt
                }
                $result['database'] = $database;
                return $result;
            }

            return null; // Go back to username prompt
        }

        // Use existing user - prompt for password and test in a loop
        self::output("\n", $ansi);
        $password = self::promptPassword("Password for '{$username}': ", $ansi);
        self::output("\n", $ansi);
        self::output("Testing credentials... ", $ansi);

        while (true) {
            $result = self::testAndReturnCredentials($connection, $username, $password, $ansi);
            if ($result !== null) {
                // Create database and grant privileges
                if (!self::createDatabaseAndGrant($adminPdo, $database, $username, $userHost, $ansi)) {
                    return null; // Go back to username prompt
                }
                $result['database'] = $database;
                return $result;
            }

            self::output("\n", $ansi);
            self::output("\e[33mWhat would you like to do?\e[39m\n", $ansi);
            self::output("\n", $ansi);

            $options = [
                'Try a different password',
                'Reset password for this user',
                'Choose a different username',
                'Back',
                'Exit'
            ];
            $selected = self::interactiveMenu($options, 0, $ansi, true);

            if ($selected === -1 || $options[$selected] === 'Back') {
                return null;
            }

            if ($options[$selected] === 'Exit') {
                self::output("\n", $ansi);
                self::output("Exiting...\n", $ansi);
                self::output("\n", $ansi);
                return false;
            }

            if ($options[$selected] === 'Choose a different username') {
                return null; // Go back to username prompt
            }

            if ($options[$selected] === 'Reset password for this user') {
                // Prompt for new password
                self::output("\n", $ansi);
                $newPassword = self::promptPassword("New password for '{$username}': ", $ansi);
                self::output("\n", $ansi);

                // Escalate privileges for password change
                if (\function_exists('muse_escalate_privileges')) {
                    \muse_escalate_privileges();
                }

                self::output("Resetting password... ", $ansi);

                try {
                    $adminPdo->exec("ALTER USER " . $adminPdo->quote($username) . "@" . $adminPdo->quote($userHost) .
                                   " IDENTIFIED BY " . $adminPdo->quote($newPassword));
                    $adminPdo->exec("FLUSH PRIVILEGES");
                    self::output("\e[32mDone.\e[39m\n", $ansi);
                } catch (\PDOException $e) {
                    self::output("\e[31mFailed.\e[39m\n", $ansi);
                    self::output("Error: " . $e->getMessage() . "\n", $ansi);

                    // Drop privileges
                    if (\function_exists('muse_drop_privileges')) {
                        \muse_drop_privileges();
                    }

                    continue; // Try again
                }

                // Drop privileges
                if (\function_exists('muse_drop_privileges')) {
                    \muse_drop_privileges();
                }

                // Test the new credentials
                self::output("Testing new credentials... ", $ansi);
                $result = self::testAndReturnCredentials($connection, $username, $newPassword, $ansi);

                if ($result !== null) {
                    // Create database and grant privileges
                    if (!self::createDatabaseAndGrant($adminPdo, $database, $username, $userHost, $ansi)) {
                        continue; // Try again
                    }
                    $result['database'] = $database;
                    return $result;
                }

                // Test failed, continue the loop to show options again
                continue;
            }

            self::output("\n", $ansi);
            $password = self::promptPassword("Password for '{$username}': ", $ansi);
            self::output("\n", $ansi);
            self::output("Testing credentials... ", $ansi);
        }
    }

    /**
     * Execute the actual user and database creation
     *
     * @param   array   $connection  Connection configuration
     * @param   \PDO    $adminPdo    Admin PDO connection
     * @param   string  $database    Database name
     * @param   string  $username    Username
     * @param   string  $password    Password
     * @param   string  $userHost    User host
     * @param   bool    $ansi        Whether to use ANSI colors
     * @return  array|null|false     Connection with credentials, null to go back, false to exit
     **/
    private static function executeUserCreation(
        $connection,
        $adminPdo,
        $database,
        $username,
        $password,
        $userHost,
        $ansi
    ) {
        self::output("\n", $ansi);
        self::output("Creating user '\e[32m{$username}\e[39m'@'\e[32m{$userHost}\e[39m'... ", $ansi);

        try {
            // Escalate privileges for user creation
            if (\function_exists('muse_escalate_privileges')) {
                \muse_escalate_privileges();
            }

            // Create the user
            $adminPdo->exec("CREATE USER " . $adminPdo->quote($username) . "@" . $adminPdo->quote($userHost) .
                           " IDENTIFIED BY " . $adminPdo->quote($password));

            self::output("\e[32mCreated.\e[39m\n", $ansi);

            // Create the database
            self::output("Creating database '\e[32m{$database}\e[39m'... ", $ansi);
            $adminPdo->exec("CREATE DATABASE IF NOT EXISTS " . self::quoteIdentifier($adminPdo, $database) .
                           " CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci");
            self::output("\e[32mCreated.\e[39m\n", $ansi);

            // Grant privileges
            self::output("Granting privileges... ", $ansi);
            $adminPdo->exec("GRANT ALL PRIVILEGES ON " . self::quoteIdentifier($adminPdo, $database) . ".* TO " .
                           $adminPdo->quote($username) . "@" . $adminPdo->quote($userHost));
            $adminPdo->exec("FLUSH PRIVILEGES");
            self::output("\e[32mDone.\e[39m\n", $ansi);

            // Drop privileges
            if (\function_exists('muse_drop_privileges')) {
                \muse_drop_privileges();
            }

            // Test the new credentials
            self::output("Testing new credentials... ", $ansi);

            $result = self::testAndReturnCredentials($connection, $username, $password, $ansi);

            if ($result !== null) {
                $result['database'] = $database;
                return $result;
            }

            // Test failed - show retry options
            self::output("\n", $ansi);
            self::output("\e[33mWhat would you like to do?\e[39m\n", $ansi);
            self::output("\n", $ansi);

            $options = ['Try again with this user', 'Create a different user', 'Back', 'Exit'];
            $selected = self::interactiveMenu($options, 0, $ansi, true);

            if ($selected === -1 || $options[$selected] === 'Back') {
                return null;
            }

            if ($options[$selected] === 'Exit') {
                self::output("\n", $ansi);
                self::output("Exiting...\n", $ansi);
                self::output("\n", $ansi);
                return false;
            }

            if ($options[$selected] === 'Create a different user') {
                return null; // Go back
            }

            // Try again with same user - prompt for password
            self::output("\n", $ansi);
            $password = self::promptPassword("Password for '{$username}': ", $ansi);
            self::output("\n", $ansi);
            self::output("Testing credentials... ", $ansi);

            // Loop until success or user chooses back/exit
            while (true) {
                $result = self::testAndReturnCredentials($connection, $username, $password, $ansi);
                if ($result !== null) {
                    $result['database'] = $database;
                    return $result;
                }

                self::output("\n", $ansi);
                self::output("\e[33mWhat would you like to do?\e[39m\n", $ansi);
                self::output("\n", $ansi);

                $options = ['Try a different password', 'Back', 'Exit'];
                $selected = self::interactiveMenu($options, 0, $ansi, true);

                if ($selected === -1 || $options[$selected] === 'Back') {
                    return null;
                }

                if ($options[$selected] === 'Exit') {
                    self::output("\n", $ansi);
                    self::output("Exiting...\n", $ansi);
                    self::output("\n", $ansi);
                    return false;
                }

                self::output("\n", $ansi);
                $password = self::promptPassword("Password for '{$username}': ", $ansi);
                self::output("\n", $ansi);
                self::output("Testing credentials... ", $ansi);
            }
        } catch (\PDOException $e) {
            // Drop privileges
            if (\function_exists('muse_drop_privileges')) {
                \muse_drop_privileges();
            }

            self::output("\e[31mFailed.\e[39m\n", $ansi);
            self::output("Error: {$e->getMessage()}\n", $ansi);
            self::output("\n", $ansi);

            // Ask what to do
            self::output("\e[33mWhat would you like to do?\e[39m\n", $ansi);
            self::output("\n", $ansi);

            $options = ['Try again', 'Enter existing credentials', 'Back', 'Exit'];
            $selected = self::interactiveMenu($options, 0, $ansi, true);

            if ($selected === -1 || $options[$selected] === 'Back') {
                return null;
            }

            if ($options[$selected] === 'Exit') {
                self::output("\n", $ansi);
                self::output("Exiting...\n", $ansi);
                self::output("\n", $ansi);
                return false;
            }

            if ($options[$selected] === 'Enter existing credentials') {
                return self::promptCredentials($connection, $ansi);
            }

            // Try again - return null to go back and restart
            return null;
        }
    }

    /**
     * Test credentials and return connection config
     *
     * @param   array   $connection  Connection configuration
     * @param   string  $username    Username to test
     * @param   string  $password    Password to test
     * @param   bool    $ansi        Whether to use ANSI colors
     * @return  array|null           Connection with credentials, or null on failure
     **/
    private static function testAndReturnCredentials($connection, $username, $password, $ansi)
    {
        try {
            $options = [];

            $pdo = self::connectWithPdoConnectorFromDsn($connection['dsn'], $username, $password, $options);

            // Query for the current user
            $stmt = $pdo->query('SELECT CURRENT_USER()');
            $currentUser = $stmt->fetchColumn();

            self::output("\e[32mConnected.\e[39m\n", $ansi);
            self::output("\n", $ansi);
            self::output("Connected as: \e[32m{$currentUser}\e[39m\n", $ansi);

            // Add credentials to connection config
            $connection['username'] = $username;
            $connection['password'] = $password;
            $connection['pdo'] = $pdo;

            return $connection;
        } catch (\PDOException $e) {
            self::output("\e[31mFailed.\e[39m\n", $ansi);
            self::output("Error: {$e->getMessage()}\n", $ansi);
            return null;
        }
    }

    /**
     * Generate a random password
     *
     * @param   int     $length  Password length
     * @return  string  Random password
     **/
    private static function generatePassword($length = 16)
    {
        $chars = 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789!@#$%^&*';
        $password = '';

        for ($i = 0; $i < $length; $i++) {
            $password .= $chars[random_int(0, strlen($chars) - 1)];
        }

        return $password;
    }

    /**
     * Prompt for database credentials
     *
     * @param   array  $connection  Connection configuration
     * @param   bool   $ansi        Whether to use ANSI colors
     * @param   array  $existing    Existing configuration to use as defaults
     * @return  array|null|false    Connection with credentials, null to go back, false to exit
     **/
    private static function promptCredentials($connection, $ansi, $existing = [])
    {
        self::output("\n", $ansi);
        self::output("Enter existing MySQL database name and credentials:\n", $ansi);
        self::output("\n", $ansi);

        // Use existing values as defaults, or fall back to 'hubzero'
        $defaultDb = $existing['database'] ?? 'hubzero';
        $defaultUser = $existing['username'] ?? 'hubzero';

        // Prompt for database name
        self::output("Database name [{$defaultDb}]: ", $ansi);
        $database = trim(fgets(STDIN));
        if ($database === '') {
            $database = $defaultDb;
        }

        // Prompt for username
        self::output("Username [{$defaultUser}]: ", $ansi);
        $username = trim(fgets(STDIN));
        if ($username === '') {
            $username = $defaultUser;
        }

        // Prompt for password (with hidden input if possible)
        // Don't show existing password as default for security
        $password = self::promptPassword("Password: ", $ansi);

        self::output("\n", $ansi);
        self::output("Testing credentials... ", $ansi);

        // Try to connect with provided credentials
        while (true) {
            try {
                $options = [];

                $pdo = self::connectWithPdoConnectorFromDsn($connection['dsn'], $username, $password, $options);

                // Connection succeeded!
                $stmt = $pdo->query('SELECT CURRENT_USER()');
                $currentUser = $stmt->fetchColumn();

                self::output("\e[32mConnected.\e[39m\n", $ansi);
                self::output("\n", $ansi);
                self::output("Connected as: \e[32m{$currentUser}\e[39m\n", $ansi);

                // Verify the database exists
                self::output("Checking database '\e[32m{$database}\e[39m'... ", $ansi);
                try {
                    $pdo->exec("USE " . self::quoteIdentifier($pdo, $database));
                    self::output("\e[32mExists.\e[39m\n", $ansi);
                } catch (\PDOException $dbError) {
                    self::output("\e[31mNot found.\e[39m\n", $ansi);
                    self::output("\n", $ansi);
                    self::output("\e[31mThe database '{$database}' does not exist.\e[39m\n", $ansi);
                    self::output(
                        "Please create it first, or use "
                            . "'Create a new database and credentials' option.\n",
                        $ansi
                    );
                    self::output("\n", $ansi);

                    // Show options menu
                    self::output("\e[33mWhat would you like to do?\e[39m\n", $ansi);
                    self::output("\n", $ansi);

                    $options = ['Try again with different database/credentials', 'Back', 'Exit'];
                    $selected = self::interactiveMenu($options, 0, $ansi, true);

                    if ($selected === -1 || $options[$selected] === 'Back') {
                        return null;
                    }

                    if ($options[$selected] === 'Exit') {
                        self::output("\n", $ansi);
                        self::output("Exiting...\n", $ansi);
                        self::output("\n", $ansi);
                        return false;
                    }

                    // Try again - re-prompt with current values as new defaults
                    $existing['database'] = $database;
                    $existing['username'] = $username;
                    return self::promptCredentials($connection, $ansi, $existing);
                }

                // Add credentials to connection config
                $connection['username'] = $username;
                $connection['password'] = $password;
                $connection['database'] = $database;
                $connection['pdo'] = $pdo;

                return $connection;
            } catch (\PDOException $e) {
                $code = $e->getCode();

                self::output("\e[31mFailed.\e[39m\n", $ansi);
                self::output("\n", $ansi);

                // Access denied - wrong credentials
                if (
                    in_array($code, [1045, 1044, 1698]) ||
                    stripos($e->getMessage(), 'Access denied') !== false
                ) {
                    self::output("\e[31mAccess denied.\e[39m The username or password is incorrect.\n", $ansi);
                } else {
                    self::output("\e[31mConnection error:\e[39m {$e->getMessage()}\n", $ansi);
                }

                // Show options menu
                self::output("\n", $ansi);
                self::output("\e[33mWhat would you like to do?\e[39m\n", $ansi);
                self::output("\n", $ansi);

                $options = ['Try again with different credentials', 'Back', 'Exit'];
                $selected = self::interactiveMenu($options, 0, $ansi, true);

                // Handle backspace (go back)
                if ($selected === -1 || $options[$selected] === 'Back') {
                    return null;
                }

                if ($options[$selected] === 'Exit') {
                    self::output("\n", $ansi);
                    self::output("Exiting...\n", $ansi);
                    self::output("\n", $ansi);
                    return false;
                }

                // Try again - prompt for new credentials
                self::output("\n", $ansi);

                self::output("Database name [{$database}]: ", $ansi);
                $newDatabase = trim(fgets(STDIN));
                if ($newDatabase !== '') {
                    $database = $newDatabase;
                }

                self::output("Username [{$username}]: ", $ansi);
                $newUsername = trim(fgets(STDIN));
                if ($newUsername !== '') {
                    $username = $newUsername;
                }

                $password = self::promptPassword("Password: ", $ansi);

                self::output("\n", $ansi);
                self::output("Testing credentials... ", $ansi);
            }
        }
    }

    /**
     * Prompt for password with hidden input
     *
     * @param   string  $prompt  The prompt text
     * @param   bool    $ansi    Whether to use ANSI colors
     * @return  string  The password
     **/
    private static function promptPassword($prompt, $ansi)
    {
        self::output($prompt, $ansi);

        // Try to hide password input
        if (PHP_OS_FAMILY !== 'Windows' && posix_isatty(STDIN)) {
            // Unix - use stty to disable echo
            system('stty -echo');
            $password = trim(fgets(STDIN));
            system('stty echo');
            self::output("\n", $ansi);
        } else {
            // Windows or non-TTY - just read normally
            $password = trim(fgets(STDIN));
        }

        return $password;
    }

    /**
     * Display an interactive menu with arrow key navigation
     *
     * Falls back to numbered input if interactive mode is unavailable.
     *
     * @param   array   $options        Array of option labels (0-indexed)
     * @param   int     $default        Default selected index (0-indexed)
     * @param   bool    $ansi           Whether to use ANSI colors
     * @param   bool    $showBackspace  Whether to show backspace hint (for submenus)
     * @return  int     Selected index (0-indexed)
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
     * @return  int    Selected index, or -1 if backspace (go back) was pressed
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
                // Clear the hint line, show cursor, and restore terminal
                echo "\r\033[K"; // Clear current line (hint line)
                echo "\033[?25h"; // Show cursor
                self::restoreTerminal();
                return $selected;
            } elseif ($key === 'escape') {
                // Escape pressed - select Exit option (last option)
                echo "\r\033[K"; // Clear current line (hint line)
                echo "\033[?25h"; // Show cursor
                self::restoreTerminal();
                return $count - 1;
            } elseif ($key === 'backspace' && $showBackspace) {
                // Backspace pressed - go back to previous menu (only if enabled)
                echo "\r\033[K"; // Clear current line (hint line)
                echo "\033[?25h"; // Show cursor
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

        // Build hint line based on context
        if ($showBackspace) {
            echo "\n  \033[90m↑/↓ navigate, Enter select, Backspace back, Esc exit\033[39m";
        } else {
            echo "\n  \033[90m↑/↓ navigate, Enter select, Esc exit\033[39m";
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
        // Move to beginning of current line
        echo "\r";

        // Move cursor up: options + blank line + hint line (which we're on)
        $lines = count($options) + 1;
        echo "\033[{$lines}A";

        // Clear from cursor to end of screen
        echo "\033[J";

        // Redraw
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
        // Display numbered options
        foreach ($options as $i => $label) {
            $num = $i + 1;
            self::output("  [{$num}] {$label}\n", $ansi);
        }

        self::output("\n", $ansi);

        $defaultNum = $default + 1;

        while (true) {
            self::output("Select option [{$defaultNum}]: ", $ansi);
            $input = trim(fgets(STDIN));

            // Default selection
            if ($input === '') {
                return $default;
            }

            $choice = (int) $input;

            if ($choice >= 1 && $choice <= count($options)) {
                return $choice - 1; // Convert to 0-indexed
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
        // Only works on Unix-like systems with stty
        if (PHP_OS_FAMILY === 'Windows') {
            return false;
        }

        // Check if we have a TTY
        if (!posix_isatty(STDIN)) {
            return false;
        }

        // Try to enable raw mode
        // -icanon: disable canonical mode (line buffering)
        // -echo: disable echoing of input characters
        // -isig: disable signal generation (Ctrl+C won't send SIGINT)
        $result = @system('stty -icanon -echo -isig 2>/dev/null', $retval);

        if ($retval !== 0) {
            return false;
        }

        self::$terminalModified = true;

        // Register shutdown handler to restore terminal
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
            echo "\033[?25h"; // Ensure cursor is visible
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

        // Handle escape sequences (arrow keys) or standalone Escape
        if ($char === "\033") {
            // Check if more data is available (arrow key sequence)
            $read = [STDIN];
            $write = null;
            $except = null;
            if (stream_select($read, $write, $except, 0, 50000) > 0) {
                // More data available - it's an escape sequence
                $char .= fread(STDIN, 2);

                if ($char === "\033[A") {
                    return 'up';
                }
                if ($char === "\033[B") {
                    return 'down';
                }

                return 'other';
            }

            // No more data - standalone Escape key
            return 'escape';
        }

        // Enter key
        if ($char === "\n" || $char === "\r") {
            return 'enter';
        }

        // Backspace key (DEL = \x7f, BS = \x08)
        if ($char === "\x7f" || $char === "\x08") {
            return 'backspace';
        }

        // Ctrl+C - treat as escape (select Exit option)
        if ($char === "\003") {
            return 'escape';
        }

        // j/k for vim-style navigation
        if ($char === 'j') {
            return 'down';
        }
        if ($char === 'k') {
            return 'up';
        }

        return 'other';
    }

    /**
     * Interactive text input with Esc/Ctrl-C handling
     *
     * Reads text input character by character with special key handling:
     * - Esc with text: clears input and restarts
     * - Esc when empty: returns null (go back)
     * - Ctrl-C: returns false (exit)
     * - Enter: returns input (or default if empty)
     * - Backspace: removes last character
     *
     * @param   string  $prompt   The prompt to display
     * @param   string  $default  Default value if Enter pressed with empty input
     * @param   bool    $ansi     Whether to use ANSI colors
     * @param   bool    $isPassword  Whether to hide input (for passwords)
     * @return  string|null|false  Input string, null to go back, false to exit
     **/
    private static function interactiveInput($prompt, $default = '', $ansi = true, $isPassword = false)
    {
        // Try interactive mode
        if ($ansi && self::enableRawMode()) {
            $input = '';
            $promptWithDefault = $default !== '' ? "{$prompt} [{$default}]: " : "{$prompt}: ";

            // Display initial prompt
            self::output($promptWithDefault, $ansi);

            while (true) {
                $char = fread(STDIN, 1);

                // Handle escape sequences (arrow keys) or standalone Escape
                if ($char === "\033") {
                    // Check if more data is available (arrow key sequence)
                    $read = [STDIN];
                    $write = null;
                    $except = null;
                    if (stream_select($read, $write, $except, 0, 50000) > 0) {
                        // More data available - it's an escape sequence, ignore it
                        fread(STDIN, 2);
                        continue;
                    }

                    // Standalone Escape key
                    if ($input !== '') {
                        // Clear the input and restart
                        // Move cursor back and clear to end of line
                        echo "\r\033[K";
                        self::output($promptWithDefault, $ansi);
                        $input = '';
                    } else {
                        // Empty input - go back
                        echo "\n";
                        self::restoreTerminal();
                        return null;
                    }
                    continue;
                }

                // Ctrl+C - exit entirely
                if ($char === "\003") {
                    echo "\n";
                    self::restoreTerminal();
                    return false;
                }

                // Enter key
                if ($char === "\n" || $char === "\r") {
                    echo "\n";
                    self::restoreTerminal();
                    return $input !== '' ? $input : $default;
                }

                // Backspace key (DEL = \x7f, BS = \x08)
                if ($char === "\x7f" || $char === "\x08") {
                    if ($input !== '') {
                        $input = substr($input, 0, -1);
                        // Move cursor back, print space, move cursor back again
                        echo "\x08 \x08";
                    }
                    continue;
                }

                // Regular printable character
                if (ord($char) >= 32 && ord($char) < 127) {
                    $input .= $char;
                    if ($isPassword) {
                        echo '*';
                    } else {
                        echo $char;
                    }
                }
            }
        }

        // Fallback to simple fgets (no special key handling)
        $promptWithDefault = $default !== '' ? "{$prompt} [{$default}]: " : "{$prompt}: ";
        self::output($promptWithDefault, $ansi);

        if ($isPassword && function_exists('shell_exec') && PHP_OS_FAMILY !== 'Windows') {
            system('stty -echo');
            $input = trim(fgets(STDIN));
            system('stty echo');
            echo "\n";
        } else {
            $input = trim(fgets(STDIN));
        }

        return $input !== '' ? $input : $default;
    }

    /**
     * Parse a MySQL .my.cnf configuration file
     *
     * Reads the [client] section to extract connection settings.
     * Settings can include: user, password, host, port, socket
     *
     * @param   string  $path  Path to the .my.cnf file
     * @return  array|null     Parsed settings or null if file doesn't exist/is unreadable
     **/
    private static function parseMyCnf($path)
    {
        if (!file_exists($path) || !is_readable($path)) {
            return null;
        }

        $contents = @file_get_contents($path);
        if ($contents === false) {
            return null;
        }

        $settings = [];
        $inClientSection = false;

        foreach (explode("\n", $contents) as $line) {
            $line = trim($line);

            // Skip empty lines and comments
            if ($line === '' || $line[0] === '#' || $line[0] === ';') {
                continue;
            }

            // Check for section headers
            if (preg_match('/^\[(.+)\]$/', $line, $matches)) {
                $section = strtolower($matches[1]);
                // [client] or [mysql] sections contain credentials
                $inClientSection = ($section === 'client' || $section === 'mysql');
                continue;
            }

            // Parse key=value pairs in client section
            if ($inClientSection && strpos($line, '=') !== false) {
                list($key, $value) = explode('=', $line, 2);
                $key = trim($key);
                $value = trim($value);

                // Remove quotes from value
                if (
                    (substr($value, 0, 1) === '"' && substr($value, -1) === '"') ||
                    (substr($value, 0, 1) === "'" && substr($value, -1) === "'")
                ) {
                    $value = substr($value, 1, -1);
                }

                $settings[$key] = $value;
            }
        }

        return empty($settings) ? null : $settings;
    }

    /**
     * Check if a hostname refers to localhost
     *
     * @param   string  $host  Hostname to check
     * @return  bool    True if localhost, false otherwise
     **/
    private static function isLocalHost($host)
    {
        $host = strtolower(trim($host));
        return $host === 'localhost' ||
               $host === '127.0.0.1' ||
               $host === '::1' ||
               $host === '';
    }

    /**
     * Quote a MySQL identifier (database name, table name, etc.)
     *
     * MySQL identifiers use backticks, not single quotes like string values.
     *
     * @param   \PDO    $pdo         PDO connection
     * @param   string  $identifier  Identifier to quote
     * @return  string  Quoted identifier
     **/
    private static function quoteIdentifier($pdo, $identifier)
    {
        // Replace any backticks with double backticks (MySQL escaping)
        return '`' . str_replace('`', '``', $identifier) . '`';
    }

    /**
     * Create database and grant privileges to a user
     *
     * @param   \PDO    $adminPdo  Admin PDO connection
     * @param   string  $database  Database name
     * @param   string  $username  Username
     * @param   string  $userHost  User host
     * @param   bool    $ansi      Whether to use ANSI colors
     * @return  bool    True on success, false on failure
     **/
    private static function createDatabaseAndGrant($adminPdo, $database, $username, $userHost, $ansi)
    {
        try {
            // Escalate privileges
            if (\function_exists('muse_escalate_privileges')) {
                \muse_escalate_privileges();
            }

            // Create the database
            self::output("Creating database '\e[32m{$database}\e[39m'... ", $ansi);
            $adminPdo->exec("CREATE DATABASE IF NOT EXISTS " . self::quoteIdentifier($adminPdo, $database) .
                           " CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci");
            self::output("\e[32mCreated.\e[39m\n", $ansi);

            // Grant privileges
            self::output("Granting privileges... ", $ansi);
            $adminPdo->exec("GRANT ALL PRIVILEGES ON " . self::quoteIdentifier($adminPdo, $database) . ".* TO " .
                           $adminPdo->quote($username) . "@" . $adminPdo->quote($userHost));
            $adminPdo->exec("FLUSH PRIVILEGES");
            self::output("\e[32mDone.\e[39m\n", $ansi);

            // Drop privileges
            if (\function_exists('muse_drop_privileges')) {
                \muse_drop_privileges();
            }

            return true;
        } catch (\PDOException $e) {
            // Drop privileges on error
            if (\function_exists('muse_drop_privileges')) {
                \muse_drop_privileges();
            }

            self::output("\e[31mFailed.\e[39m\n", $ansi);
            self::output("Error: {$e->getMessage()}\n", $ansi);
            return false;
        }
    }

    /**
     * Build a PDO DSN string from .my.cnf settings
     *
     * @param   array   $myCnf  Parsed .my.cnf settings
     * @return  string  PDO DSN string
     **/
    private static function buildDsnFromMyCnf(array $myCnf)
    {
        // Prefer socket if specified
        if (!empty($myCnf['socket'])) {
            return "mysql:unix_socket={$myCnf['socket']}";
        }

        // Otherwise use host/port
        $host = $myCnf['host'] ?? 'localhost';
        $port = $myCnf['port'] ?? 3306;

        $dsn = "mysql:host={$host}";
        if ($port != 3306) {
            $dsn .= ";port={$port}";
        }

        return $dsn;
    }

    /**
     * Connect using Hubzero standalone PDO connector and return native PDO
     *
     * @param   string  $dsn       PDO DSN
     * @param   string  $username  Database username
     * @param   string  $password  Database password
     * @param   array   $options   PDO options
     * @return  \PDO
     * @throws  \PDOException
     */
    private static function connectWithPdoConnectorFromDsn(
        string $dsn,
        string $username = '',
        string $password = '',
        array $options = []
    ): \PDO {
        self::resolveInstallerDriverFromDsn($dsn);

        try {
            $connection = new PdoConnection($dsn, $username, $password, $options);
            return $connection->getNativeConnection();
        } catch (ConnectionFailedException $e) {
            $previous = $e->getPrevious();
            if ($previous instanceof \PDOException) {
                throw $previous;
            }

            throw new \PDOException($e->getMessage(), (int) $e->getCode());
        }
    }

    /**
     * Resolve CLI installer driver from DSN using test-suite naming conventions
     *
     * @param   string  $dsn  PDO DSN
     * @return  string  Normalized driver name
     * @throws  \PDOException
     */
    private static function resolveInstallerDriverFromDsn(string $dsn): string
    {
        $driver = strtolower((string) strstr($dsn, ':', true));

        if (in_array($driver, ['mysql', 'mariadb', 'percona', 'pdo'], true)) {
            return 'mysql';
        }

        if (in_array($driver, ['pgsql', 'sqlite', 'firebird', 'informix'], true)) {
            throw new \PDOException(
                "CLI installer currently supports MySQL-family drivers only"
                . " (mysql/mariadb/percona). Requested DSN driver: {$driver}"
            );
        }

        throw new \PDOException("Unsupported database driver in DSN: {$driver}");
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
}
