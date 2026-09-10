<?php

/**
 * @package    framework
 * @copyright  Copyright (c) 2005-2025 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Hubzero\Console\Command\Install;

use Hubzero\Database\Connection\PdoConnection;
use Hubzero\Database\Exception\ConnectionFailedException;
use Hubzero\Facades\Config;

/**
 * Admin user creation helper class
 *
 * Creates the initial super administrator account during installation.
 **/
class AdminUser
{
    /**
     * Super Users group ID
     *
     * @var int
     **/
    private const SUPER_USERS_GROUP_ID = 8;

    /**
     * Minimum password length
     *
     * @var int
     **/
    private const MIN_PASSWORD_LENGTH = 8;

    /**
     * Configure admin user interactively
     *
     * @param   bool    $ansi      Whether to use ANSI color output
     * @param   string  $appPath   Path to the app directory
     * @param   array   $defaults  Default values (e.g., email from site settings)
     * @return  array|null  User data array or null if cancelled
     */
    public static function configure($ansi = true, $appPath = null, $defaults = [])
    {
        if ($appPath === null) {
            $appPath = defined('PATH_APP')
                ? PATH_APP
                : dirname(dirname(dirname(dirname(dirname(dirname(__DIR__)))))) . '/app';
        }

        self::output("\n", $ansi);
        self::output("\e[33mAdmin User Setup\e[39m\n", $ansi);
        self::output("----------------\n", $ansi);
        self::output("\n", $ansi);
        self::output("Create the initial administrator account for your site.\n", $ansi);
        self::output("This account will have full administrative privileges.\n", $ansi);
        self::output("\n", $ansi);
        self::output("Press Ctrl+C to cancel at any time.\n", $ansi);

        // Prefer what the database step wrote over the configuration the
        // console read at startup, which a fresh install predates.
        $dbConfig = Database::readConfig($appPath) ?? Config::get('database');
        if (!$dbConfig) {
            self::output("\n", $ansi, true);
            self::output("\e[31mDatabase configuration not found.\e[39m\n", $ansi, true);
            self::output("Please run database configuration first.\n", $ansi, true);
            return null;
        }
        $dbConfig = (array) $dbConfig;

        // Connect to database
        $pdo = self::connectToDatabase($dbConfig, $ansi);
        if ($pdo === null) {
            return null;
        }

        $prefix = $dbConfig['dbprefix'] ?? 'jos_';

        // Check if admin user already exists
        $existingAdmin = self::getExistingAdmin($pdo, $prefix);
        if ($existingAdmin !== null) {
            self::output("\n", $ansi);
            self::output("\e[32m[OK]\e[39m Admin user already exists.\n", $ansi);
            self::output("  Username: \e[32m{$existingAdmin['username']}\e[39m\n", $ansi);
            self::output("  Email:    \e[32m{$existingAdmin['email']}\e[39m\n", $ansi);
            self::output("\n", $ansi);
            self::output("Skipping admin user creation.\n", $ansi);
            return $existingAdmin;
        }

        if (Answers::isUnattended()) {
            return self::createUnattended($pdo, $prefix, $ansi, $defaults);
        }

        // Collect user details
        $userData = [];

        // Full name
        self::output("\n", $ansi);
        $userData['name'] = self::promptInput(
            "Full name",
            $defaults['name'] ?? "Site Administrator",
            $ansi
        );
        if ($userData['name'] === null) {
            return null;
        }

        // Username
        self::output("\n", $ansi);
        $userData['username'] = self::promptUsername($pdo, $prefix, $ansi);
        if ($userData['username'] === null) {
            return null;
        }

        // Email
        self::output("\n", $ansi);
        $userData['email'] = self::promptEmail(
            $pdo,
            $prefix,
            $defaults['email'] ?? '',
            $ansi
        );
        if ($userData['email'] === null) {
            return null;
        }

        // Password
        self::output("\n", $ansi);
        $userData['password'] = self::promptPassword($ansi);
        if ($userData['password'] === null) {
            return null;
        }

        // Create the user
        self::output("\n", $ansi);
        self::output("Creating admin user... ", $ansi);

        $userId = self::createUser($pdo, $prefix, $userData);
        if ($userId === null) {
            self::output("\e[31mFailed.\e[39m\n", $ansi, true);
            return null;
        }

        self::output("\e[32mDone.\e[39m\n", $ansi);

        self::output("\n", $ansi);
        self::output("\e[32mAdmin user created successfully!\e[39m\n", $ansi);
        self::output("\n", $ansi);
        self::output("  Username: \e[32m{$userData['username']}\e[39m\n", $ansi);
        self::output("  Email:    \e[32m{$userData['email']}\e[39m\n", $ansi);

        return $userData;
    }

    /**
     * Create the admin account from the answers given up front
     *
     * @param   \PDO    $pdo       PDO connection
     * @param   string  $prefix    Table prefix
     * @param   bool    $ansi      Whether to use ANSI color output
     * @param   array   $defaults  Values carried over from the site settings
     * @return  array|null  User data array, or null if an answer was unusable
     */
    private static function createUnattended($pdo, $prefix, $ansi, $defaults)
    {
        $answers = Answers::section('admin');

        $userData = [
            'name'     => $answers['name'] ?? $defaults['name'] ?? 'Site Administrator',
            'username' => $answers['username'] ?? 'admin',
            'email'    => $answers['email'] ?? $defaults['email'] ?? '',
            'password' => $answers['password'] ?? '',
        ];

        self::output("\n", $ansi);

        if (!preg_match('/^[a-zA-Z][a-zA-Z0-9_]{2,29}$/', $userData['username'])) {
            $msg = "\e[31mNot a usable username: {$userData['username']}\e[39m\n";
            self::output($msg, $ansi, true);
            self::output("Must start with a letter, 3-30 chars, alphanumeric/underscore only.\n", $ansi, true);
            return null;
        }

        if (!preg_match('/^[^@]+@[^@]+$/', $userData['email'])) {
            self::output("\e[31mNot a valid admin email address: {$userData['email']}\e[39m\n", $ansi, true);
            return null;
        }

        if (strlen($userData['password']) < self::MIN_PASSWORD_LENGTH) {
            $minLen = self::MIN_PASSWORD_LENGTH;
            self::output("\e[31mThe admin password must be at least {$minLen} characters.\e[39m\n", $ansi, true);
            return null;
        }

        self::output("Creating admin user... ", $ansi);

        if (self::createUser($pdo, $prefix, $userData) === null) {
            self::output("\e[31mFailed.\e[39m\n", $ansi, true);
            return null;
        }

        self::output("\e[32mDone.\e[39m\n", $ansi);
        self::output("\n", $ansi);
        self::output("  Username: \e[32m{$userData['username']}\e[39m\n", $ansi);
        self::output("  Email:    \e[32m{$userData['email']}\e[39m\n", $ansi);

        return $userData;
    }

    /**
     * Connect to the database using configuration
     *
     * @param   array  $config  Database configuration
     * @param   bool   $ansi    Whether to use ANSI colors
     * @return  \PDO|null  PDO connection or null on failure
     **/
    private static function connectToDatabase($config, $ansi)
    {
        try {
            return self::connectWithPdoConnector($config);
        } catch (\PDOException $e) {
            self::output("\e[31mDatabase connection failed.\e[39m\n", $ansi, true);
            self::output("Error: {$e->getMessage()}\n", $ansi, true);
            return null;
        }
    }

    /**
     * Connect using Hubzero standalone PDO connector and return native PDO
     *
     * @param   array  $config  Database configuration
     * @return  \PDO
     * @throws  \PDOException
     */
    private static function connectWithPdoConnector(array $config): \PDO
    {
        self::resolveInstallerDriver($config);

        $dsn = 'mysql:';

        if (!empty($config['socket'])) {
            $dsn .= 'unix_socket=' . $config['socket'];
        } else {
            $dsn .= 'host=' . ($config['host'] ?? 'localhost');
            if (!empty($config['port'])) {
                $dsn .= ';port=' . $config['port'];
            }
        }

        if (!empty($config['db'])) {
            $dsn .= ';dbname=' . $config['db'];
        } elseif (!empty($config['database'])) {
            $dsn .= ';dbname=' . $config['database'];
        }

        $dsn .= ';charset=utf8mb4';

        try {
            $connection = new PdoConnection(
                $dsn,
                (string) ($config['user'] ?? ''),
                (string) ($config['password'] ?? ''),
                []
            );

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
     * Resolve CLI installer driver/connection combo using test-suite naming conventions
     *
     * @param   array   $config  Database configuration
     * @return  string  Normalized driver name
     * @throws  \PDOException
     */
    private static function resolveInstallerDriver(array $config): string
    {
        $requested = strtolower((string) ($config['dbtype'] ?? $config['driver'] ?? 'mysql'));

        if (in_array($requested, ['mysql', 'mariadb', 'percona', 'pdo'], true)) {
            return 'mysql';
        }

        if (in_array($requested, ['pgsql', 'sqlite', 'firebird', 'informix'], true)) {
            throw new \PDOException(
                "CLI installer currently supports MySQL-family drivers only"
                . " (mysql/mariadb/percona). Requested: {$requested}"
            );
        }

        throw new \PDOException("Unsupported database driver: {$requested}");
    }

    /**
     * Check if an admin user already exists
     *
     * @param   \PDO    $pdo     PDO connection
     * @param   string  $prefix  Table prefix
     * @return  array|null  Existing admin user data or null
     **/
    private static function getExistingAdmin($pdo, $prefix)
    {
        try {
            // Check for users in the Super Users group
            $stmt = $pdo->prepare(
                "SELECT u.id, u.name, u.username, u.email
                 FROM `{$prefix}users` u
                 INNER JOIN `{$prefix}user_usergroup_map` m ON u.id = m.user_id
                 WHERE m.group_id = :group_id
                 AND u.block = 0
                 LIMIT 1"
            );
            $stmt->execute(['group_id' => self::SUPER_USERS_GROUP_ID]);
            $result = $stmt->fetch();

            return $result ?: null;
        } catch (\PDOException $e) {
            return null;
        }
    }

    /**
     * Prompt for username with validation
     *
     * @param   \PDO    $pdo     PDO connection
     * @param   string  $prefix  Table prefix
     * @param   bool    $ansi    Whether to use ANSI colors
     * @return  string|null  Username or null if cancelled
     **/
    private static function promptUsername($pdo, $prefix, $ansi)
    {
        self::output("Username must be 3-30 characters, alphanumeric with underscores.\n", $ansi);

        while (true) {
            echo "Username [admin]: ";

            $input = self::readInput();
            if ($input === null) {
                return null;
            }

            $input = trim($input);

            if ($input === '') {
                $input = 'admin';
            }

            // Validate format
            if (!preg_match('/^[a-zA-Z][a-zA-Z0-9_]{2,29}$/', $input)) {
                $msg = "  \e[31mInvalid username. "
                    . "Must start with a letter, 3-30 chars, alphanumeric/underscore only.\e[39m\n";
                self::output($msg, $ansi, true);
                continue;
            }

            // Check if username exists
            try {
                $stmt = $pdo->prepare("SELECT id FROM `{$prefix}users` WHERE username = :username");
                $stmt->execute(['username' => $input]);
                if ($stmt->fetch()) {
                    self::output("  \e[31mUsername already exists. Please choose another.\e[39m\n", $ansi, true);
                    continue;
                }
            } catch (\PDOException $e) {
                // Table might not exist yet, that's ok
            }

            return $input;
        }
    }

    /**
     * Prompt for email with validation
     *
     * @param   \PDO    $pdo      PDO connection
     * @param   string  $prefix   Table prefix
     * @param   string  $default  Default email value
     * @param   bool    $ansi     Whether to use ANSI colors
     * @return  string|null  Email or null if cancelled
     **/
    private static function promptEmail($pdo, $prefix, $default, $ansi)
    {
        $defaultHint = $default !== '' ? " [{$default}]" : '';

        while (true) {
            echo "Email address{$defaultHint}: ";

            $input = self::readInput();
            if ($input === null) {
                return null;
            }

            $input = trim($input);

            if ($input === '' && $default !== '') {
                $input = $default;
            }

            if ($input === '') {
                self::output("  \e[31mEmail is required.\e[39m\n", $ansi, true);
                continue;
            }

            // Validate email format (allow localhost for dev)
            if (!filter_var($input, FILTER_VALIDATE_EMAIL)) {
                // Also allow simple format like admin@localhost
                if (!preg_match('/^[^@]+@[^@]+$/', $input)) {
                    self::output("  \e[31mInvalid email format.\e[39m\n", $ansi, true);
                    continue;
                }
            }

            // Check if email exists
            try {
                $stmt = $pdo->prepare("SELECT id FROM `{$prefix}users` WHERE email = :email");
                $stmt->execute(['email' => $input]);
                if ($stmt->fetch()) {
                    self::output("  \e[31mEmail already registered. Please use another.\e[39m\n", $ansi, true);
                    continue;
                }
            } catch (\PDOException $e) {
                // Table might not exist yet, that's ok
            }

            return $input;
        }
    }

    /**
     * Prompt for password with confirmation
     *
     * @param   bool    $ansi  Whether to use ANSI colors
     * @return  string|null  Password or null if cancelled
     **/
    private static function promptPassword($ansi)
    {
        $minLen = self::MIN_PASSWORD_LENGTH;
        self::output("Password must be at least {$minLen} characters.\n", $ansi);

        while (true) {
            // Read password (hidden if possible)
            echo "Password: ";
            $password = self::readPassword();
            echo "\n";

            if ($password === null) {
                return null;
            }

            if (strlen($password) < $minLen) {
                self::output("  \e[31mPassword must be at least {$minLen} characters.\e[39m\n", $ansi, true);
                continue;
            }

            // Confirm password
            echo "Confirm password: ";
            $confirm = self::readPassword();
            echo "\n";

            if ($confirm === null) {
                return null;
            }

            if ($password !== $confirm) {
                self::output("  \e[31mPasswords do not match. Please try again.\e[39m\n", $ansi, true);
                continue;
            }

            return $password;
        }
    }

    /**
     * Read password input (hidden if possible)
     *
     * @return  string|null  Password or null if cancelled
     **/
    private static function readPassword()
    {
        // Try to hide input on Unix-like systems
        if (function_exists('shell_exec') && strncasecmp(PHP_OS, 'WIN', 3) !== 0) {
            $oldStyle = shell_exec('stty -g 2>/dev/null');
            if ($oldStyle !== null) {
                shell_exec('stty -echo 2>/dev/null');
                $password = self::readInput();
                shell_exec('stty ' . trim($oldStyle) . ' 2>/dev/null');
                return $password;
            }
        }

        // Fall back to visible input
        return self::readInput();
    }

    /**
     * Create the admin user in the database
     *
     * @param   \PDO    $pdo       PDO connection
     * @param   string  $prefix    Table prefix
     * @param   array   $userData  User data (name, username, email, password)
     * @return  int|null  User ID or null on failure
     **/
    private static function createUser($pdo, $prefix, $userData)
    {
        try {
            $pdo->beginTransaction();

            // Generate password hash (using CRYPT_SHA512 format)
            $passhash = self::hashPassword($userData['password']);

            $values = [
                'name'         => $userData['name'],
                'username'     => $userData['username'],
                'email'        => $userData['email'],
                'password'     => $passhash,
                'usertype'     => 'Super Administrator',
                'block'        => 0,
                'approved'     => 2,
                'sendEmail'    => 1,
                'registerDate' => date('Y-m-d H:i:s'),
                'params'       => '',
            ];

            // A hub keeps the parts of a name as well as the whole of it, and
            // asks for none of them, or the shell fields, to be null. Which of
            // them the table has depends on how far the migrations have run.
            $parts = preg_split('/\s+/', trim($userData['name']), -1, PREG_SPLIT_NO_EMPTY) ?: [];

            $optional = [
                'givenName'     => array_shift($parts) ?? '',
                'surname'       => array_pop($parts) ?? '',
                'middleName'    => implode(' ', $parts),
                'homeDirectory' => '',
                'loginShell'    => '',
                'ftpShell'      => '',
            ];

            $present = self::getColumns($pdo, $prefix . 'users');

            foreach ($optional as $column => $value) {
                if (in_array($column, $present, true)) {
                    $values[$column] = $value;
                }
            }

            $columns      = array_keys($values);
            $placeholders = [];

            foreach ($columns as $column) {
                $placeholders[] = ':' . $column;
            }

            // Insert into users table
            $stmt = $pdo->prepare(
                "INSERT INTO `{$prefix}users` (`" . implode('`, `', $columns) . "`)"
                . ' VALUES (' . implode(', ', $placeholders) . ')'
            );
            $stmt->execute($values);

            $userId = $pdo->lastInsertId();

            // Insert into users_password table
            $stmt = $pdo->prepare(
                "INSERT INTO `{$prefix}users_password`
                 (user_id, passhash, shadowLastChange)
                 VALUES
                 (:user_id, :passhash, :shadowLastChange)"
            );
            $stmt->execute([
                'user_id' => $userId,
                'passhash' => $passhash,
                'shadowLastChange' => floor(time() / 86400), // Days since epoch
            ]);

            // Add to Super Users group
            $stmt = $pdo->prepare(
                "INSERT INTO `{$prefix}user_usergroup_map`
                 (user_id, group_id)
                 VALUES
                 (:user_id, :group_id)"
            );
            $stmt->execute([
                'user_id' => $userId,
                'group_id' => self::SUPER_USERS_GROUP_ID,
            ]);

            $pdo->commit();

            return $userId;
        } catch (\PDOException $e) {
            $pdo->rollBack();
            return null;
        }
    }

    /**
     * The columns a table has
     *
     * @param   \PDO    $pdo    PDO connection
     * @param   string  $table  The table to describe
     * @return  array
     **/
    private static function getColumns($pdo, $table)
    {
        try {
            $stmt = $pdo->query('SHOW COLUMNS FROM `' . $table . '`');

            return $stmt ? $stmt->fetchAll(\PDO::FETCH_COLUMN, 0) : [];
        } catch (\PDOException $e) {
            return [];
        }
    }

    /**
     * Hash a password using CRYPT_SHA512
     *
     * @param   string  $password  Plain text password
     * @return  string  Hashed password in {CRYPT} format
     **/
    private static function hashPassword($password)
    {
        $salt = self::generateSalt(16);
        $encrypted = crypt($password, '$6$' . $salt . '$');
        return '{CRYPT}' . $encrypted;
    }

    /**
     * Generate a random salt
     *
     * @param   int  $length  Salt length
     * @return  string  Random salt
     **/
    private static function generateSalt($length)
    {
        $chars = 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789./';
        $salt = '';

        if (function_exists('random_bytes')) {
            $bytes = random_bytes($length);
            for ($i = 0; $i < $length; $i++) {
                $salt .= $chars[ord($bytes[$i]) % strlen($chars)];
            }
        } else {
            for ($i = 0; $i < $length; $i++) {
                $salt .= $chars[mt_rand(0, strlen($chars) - 1)];
            }
        }

        return $salt;
    }

    /**
     * Prompt for simple input
     *
     * @param   string  $label    Input label
     * @param   string  $default  Default value
     * @param   bool    $ansi     Whether to use ANSI colors
     * @return  string|null  Input value or null if cancelled
     **/
    private static function promptInput($label, $default, $ansi)
    {
        $defaultHint = $default !== '' ? " [{$default}]" : '';

        while (true) {
            echo "{$label}{$defaultHint}: ";

            $input = self::readInput();
            if ($input === null) {
                return null;
            }

            $input = trim($input);

            if ($input === '' && $default !== '') {
                return $default;
            }

            if ($input === '') {
                self::output("  \e[31m{$label} is required.\e[39m\n", $ansi, true);
                continue;
            }

            return $input;
        }
    }

    /**
     * Read a line of input from STDIN
     *
     * @return  string|null  Input or null if EOF/error
     **/
    private static function readInput()
    {
        $input = fgets(STDIN);
        if ($input === false) {
            return null;
        }
        return rtrim($input, "\r\n");
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
