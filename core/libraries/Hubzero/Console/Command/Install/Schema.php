<?php

/**
 * @package    framework
 * @copyright  Copyright (c) 2005-2025 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Hubzero\Console\Command\Install;

use Hubzero\Database\Connection\PdoConnection;
use Hubzero\Database\Exception\ConnectionFailedException;
use Hubzero\Database\SqlParser;
use Hubzero\Facades\Config;

/**
 * Schema loader helper class
 *
 * This class handles loading the database schema during installation.
 * It can be used both by minimal muse (before autoloading) and full muse.
 **/
class Schema
{
    /**
     * Default table prefix
     *
     * @var string
     **/
    private const DEFAULT_PREFIX = 'jos_';

    /**
     * Path to SQL files relative to core directory
     *
     * @var string
     **/
    private const SQL_PATH = 'bootstrap/Install/sql/mysql';

    /**
     * The set of starting content a hub gets when nothing else is asked for
     *
     * @var  string
     */
    public const DEFAULT_DATA_SET = 'starter';

    /**
     * Load the database schema
     *
     * @param   bool    $ansi      Whether to use ANSI color output
     * @param   string  $appPath   Path to the app directory
     * @param   string  $corePath  Path to the core directory
     * @return  bool    True on success, false on failure
     */
    public static function load($ansi = true, $appPath = null, $corePath = null)
    {
        if ($appPath === null) {
            $appPath = defined('PATH_APP')
                ? PATH_APP
                : dirname(dirname(dirname(dirname(dirname(dirname(__DIR__)))))) . '/app';
        }

        if ($corePath === null) {
            $corePath = defined('PATH_CORE')
                ? PATH_CORE
                : dirname(dirname(dirname(dirname(dirname(dirname(__DIR__))))));
        }

        self::output("\n", $ansi);
        self::output("\e[33mLoading Database Schema\e[39m\n", $ansi);
        self::output("-----------------------\n", $ansi);

        // Prefer what the database step wrote over the configuration the
        // console read at startup, which a fresh install predates.
        $dbConfig = Database::readConfig($appPath) ?? Config::get('database');
        if (!$dbConfig) {
            self::output("\n", $ansi, true);
            self::output("\e[31mDatabase configuration not found.\e[39m\n", $ansi, true);
            self::output("Please run database configuration first.\n", $ansi, true);
            return false;
        }
        $dbConfig = (array) $dbConfig;

        // Connect to database
        $pdo = self::connectToDatabase($dbConfig, $ansi);
        if ($pdo === null) {
            return false;
        }

        // Get table prefix
        $prefix = $dbConfig['dbprefix'] ?? self::DEFAULT_PREFIX;

        // Check if schema is already loaded
        $tableCount = self::countTables($pdo, $prefix);
        if ($tableCount > 0) {
            self::output("\n", $ansi);
            self::output("\e[32m[OK]\e[39m Schema already loaded ({$tableCount} tables found).\n", $ansi);
            self::output("Skipping schema loading.\n", $ansi);
            return true;
        }

        // Load schema.sql
        $schemaPath = $corePath . '/' . self::SQL_PATH . '/schema.sql';
        if (!self::loadSqlFile($pdo, $schemaPath, $prefix, $ansi, 'schema')) {
            return false;
        }

        self::output("\n", $ansi);
        self::output("\e[32mDatabase schema loaded successfully!\e[39m\n", $ansi);

        return true;
    }

    /**
     * Load base data into the database
     *
     * @param   bool    $ansi      Whether to use ANSI color output
     * @param   string  $appPath   Path to the app directory
     * @param   string  $corePath  Path to the core directory
     * @return  bool    True on success, false on failure
     */
    public static function loadData($ansi = true, $appPath = null, $corePath = null)
    {
        if ($appPath === null) {
            $appPath = defined('PATH_APP')
                ? PATH_APP
                : dirname(dirname(dirname(dirname(dirname(dirname(__DIR__)))))) . '/app';
        }

        if ($corePath === null) {
            $corePath = defined('PATH_CORE')
                ? PATH_CORE
                : dirname(dirname(dirname(dirname(dirname(dirname(__DIR__))))));
        }

        self::output("\n", $ansi);
        self::output("\e[33mLoading Base Data\e[39m\n", $ansi);
        self::output("-----------------\n", $ansi);

        // Prefer what the database step wrote over the configuration the
        // console read at startup, which a fresh install predates.
        $dbConfig = Database::readConfig($appPath) ?? Config::get('database');
        if (!$dbConfig) {
            self::output("\n", $ansi, true);
            self::output("\e[31mDatabase configuration not found.\e[39m\n", $ansi, true);
            return false;
        }
        $dbConfig = (array) $dbConfig;

        // Connect to database
        $pdo = self::connectToDatabase($dbConfig, $ansi);
        if ($pdo === null) {
            return false;
        }

        // Get table prefix
        $prefix = $dbConfig['dbprefix'] ?? self::DEFAULT_PREFIX;

        // Check if base data is already loaded
        if (self::isBaseDataLoaded($pdo, $prefix)) {
            self::output("\n", $ansi);
            self::output("\e[32m[OK]\e[39m Base data already loaded.\n", $ansi);
            self::output("Skipping base data loading.\n", $ansi);
            return true;
        }

        // Load data.sql
        $dataPath = $corePath . '/' . self::SQL_PATH . '/data.sql';
        if (!self::loadSqlFile($pdo, $dataPath, $prefix, $ansi, 'data')) {
            return false;
        }

        self::output("\n", $ansi);
        self::output("\e[32mBase data loaded successfully!\e[39m\n", $ansi);

        return true;
    }

    /**
     * Load the default set of starting content
     *
     * Kept for callers that predate named sets, the web installer among them.
     *
     * @param   bool    $ansi      Whether to use ANSI color output
     * @param   string  $appPath   Path to the app directory
     * @param   string  $corePath  Path to the core directory
     * @return  bool    True on success, false on failure
     */
    public static function loadSampleData($ansi = true, $appPath = null, $corePath = null)
    {
        return self::loadDataSet(self::DEFAULT_DATA_SET, $ansi, $appPath, $corePath);
    }

    /**
     * Load a named set of starting content
     *
     * A hub can start empty, on the base data alone, or with a set of content
     * on top of it. Each set is a SQL file named after it.
     *
     * @param   string  $set       The name of the set to load
     * @param   bool    $ansi      Whether to use ANSI color output
     * @param   string  $appPath   Path to the app directory
     * @param   string  $corePath  Path to the core directory
     * @return  bool    True on success, false on failure
     */
    public static function loadDataSet($set, $ansi = true, $appPath = null, $corePath = null)
    {
        if ($appPath === null) {
            $appPath = defined('PATH_APP')
                ? PATH_APP
                : dirname(dirname(dirname(dirname(dirname(dirname(__DIR__)))))) . '/app';
        }

        if ($corePath === null) {
            $corePath = defined('PATH_CORE')
                ? PATH_CORE
                : dirname(dirname(dirname(dirname(dirname(dirname(__DIR__))))));
        }

        self::output("\n", $ansi);
        self::output("\e[33mLoading the " . $set . " Data\e[39m\n", $ansi);
        self::output("--------------" . str_repeat('-', strlen($set)) . "\n", $ansi);

        if (!preg_match('/^[a-z][a-z0-9_-]*$/', (string) $set)) {
            self::output("\n", $ansi, true);
            self::output("\e[31mNot a usable name for a data set: {$set}\e[39m\n", $ansi, true);
            return false;
        }

        // Prefer what the database step wrote over the configuration the
        // console read at startup, which a fresh install predates.
        $dbConfig = Database::readConfig($appPath) ?? Config::get('database');
        if (!$dbConfig) {
            self::output("\n", $ansi, true);
            self::output("\e[31mDatabase configuration not found.\e[39m\n", $ansi, true);
            return false;
        }
        $dbConfig = (array) $dbConfig;

        // Connect to database
        $pdo = self::connectToDatabase($dbConfig, $ansi);
        if ($pdo === null) {
            return false;
        }

        // Get table prefix
        $prefix = $dbConfig['dbprefix'] ?? self::DEFAULT_PREFIX;

        if (self::isDataSetLoaded($pdo, $prefix)) {
            self::output("\n", $ansi);
            self::output("\e[32m[OK]\e[39m A set of content is already loaded.\n", $ansi);
            self::output("Skipping.\n", $ansi);
            return true;
        }

        $path = $corePath . '/' . self::SQL_PATH . '/' . $set . '.sql';

        if (!is_file($path)) {
            self::output("\n", $ansi, true);
            self::output("\e[31mThere is no data set named {$set}.\e[39m\n", $ansi, true);
            self::output("Looked for {$path}\n", $ansi, true);
            return false;
        }

        if (!self::loadSqlFile($pdo, $path, $prefix, $ansi, $set)) {
            return false;
        }

        self::output("\n", $ansi);
        self::output("\e[32mThe {$set} data loaded successfully!\e[39m\n", $ansi);

        return true;
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
        self::output("\n", $ansi);
        self::output("Connecting to database... ", $ansi);

        try {
            $pdo = self::connectWithPdoConnector($config);
            self::output("\e[32mConnected.\e[39m\n", $ansi);
            return $pdo;
        } catch (\PDOException $e) {
            self::output("\e[31mFailed.\e[39m\n", $ansi, true);
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

        $dsn = self::buildMysqlDsn($config);
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
     * Build MySQL DSN from installer config
     *
     * @param   array   $config  Database configuration
     * @return  string
     */
    private static function buildMysqlDsn(array $config): string
    {
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

        return $dsn;
    }

    /**
     * Load and execute a SQL file
     *
     * @param   \PDO    $pdo      PDO connection
     * @param   string  $path     Path to SQL file
     * @param   string  $prefix   Table prefix to use
     * @param   bool    $ansi     Whether to use ANSI colors
     * @param   string  $type     What the file holds, for messaging
     * @return  bool    True on success, false on failure
     **/
    private static function loadSqlFile($pdo, $path, $prefix, $ansi, $type)
    {
        self::output("\n", $ansi);
        self::output("Loading {$type} from " . basename($path) . "... ", $ansi);

        // Use shared SqlParser to load and split the SQL file
        $statements = SqlParser::loadFile($path, $prefix);
        if ($statements === false) {
            self::output("\e[31mFile not found or could not be read.\e[39m\n", $ansi, true);
            return false;
        }

        self::output("\e[32mLoaded.\e[39m\n", $ansi);

        $total = count($statements);
        self::output("Executing {$total} statements...\n", $ansi);
        self::output("\n", $ansi);

        $executed = 0;
        $errors = 0;
        $tablesCreated = 0;
        $lastTableName = '';

        // Hide cursor during progress
        if ($ansi) {
            echo "\033[?25l";
        }

        foreach ($statements as $i => $statement) {
            $statement = trim($statement);
            if (empty($statement)) {
                continue;
            }

            // Track table creation for progress display
            if (preg_match('/CREATE\s+TABLE\s+`?(\w+)`?/i', $statement, $matches)) {
                $lastTableName = $matches[1];
            }

            try {
                $pdo->exec($statement);
                $executed++;

                if (stripos($statement, 'CREATE TABLE') !== false) {
                    $tablesCreated++;
                }

                // Update progress every 10 statements or on table creation
                if ($executed % 10 === 0 || stripos($statement, 'CREATE TABLE') !== false) {
                    $percent = round(($i + 1) / $total * 100);
                    $progress = str_pad($percent . '%', 4, ' ', STR_PAD_LEFT);

                    if ($ansi) {
                        // Move to start of line and clear
                        echo "\r\033[K";
                        echo "  Progress: {$progress} ({$executed}/{$total})";
                        if ($tablesCreated > 0 && $lastTableName) {
                            echo " - {$tablesCreated} tables";
                        }
                    }
                }
            } catch (\PDOException $e) {
                $errors++;

                // Log error but continue (some errors like "table already exists" may be OK)
                if ($ansi) {
                    echo "\n";
                }
                self::output("  \e[33mWarning:\e[39m " . self::truncateError($e->getMessage()) . "\n", $ansi);
            }
        }

        // Show cursor again
        if ($ansi) {
            echo "\033[?25h";
            echo "\n";
        }

        self::output("\n", $ansi);

        if ($tablesCreated > 0) {
            self::output("  \e[32m[OK]\e[39m Created {$tablesCreated} tables\n", $ansi);
        }

        self::output("  \e[32m[OK]\e[39m Executed {$executed} statements\n", $ansi);

        if ($errors > 0) {
            self::output("  \e[33m[WARN]\e[39m {$errors} statements had warnings\n", $ansi);
        }

        return true;
    }

    /**
     * Truncate error message for display
     *
     * @param   string  $message  Error message
     * @param   int     $maxLen   Maximum length
     * @return  string  Truncated message
     **/
    private static function truncateError($message, $maxLen = 100)
    {
        // Remove newlines
        $message = str_replace(["\r", "\n"], ' ', $message);

        if (strlen($message) > $maxLen) {
            return substr($message, 0, $maxLen - 3) . '...';
        }

        return $message;
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

    /**
     * Count tables in the database with the given prefix
     *
     * @param   \PDO    $pdo     PDO connection
     * @param   string  $prefix  Table prefix
     * @return  int     Number of tables found
     **/
    private static function countTables($pdo, $prefix)
    {
        try {
            $stmt = $pdo->prepare(
                "SELECT COUNT(*) FROM INFORMATION_SCHEMA.TABLES
                 WHERE TABLE_SCHEMA = DATABASE()
                 AND TABLE_NAME LIKE :prefix"
            );
            $stmt->execute(['prefix' => $prefix . '%']);
            return (int) $stmt->fetchColumn();
        } catch (\PDOException $e) {
            return 0;
        }
    }

    /**
     * Check if base data has been loaded
     *
     * Checks for essential records that would be present after data.sql is loaded.
     *
     * @param   \PDO    $pdo     PDO connection
     * @param   string  $prefix  Table prefix
     * @return  bool    True if base data appears to be loaded
     **/
    private static function isBaseDataLoaded($pdo, $prefix)
    {
        try {
            // Check for extensions table and essential extension records
            $stmt = $pdo->prepare(
                "SELECT COUNT(*) FROM `{$prefix}extensions`
                 WHERE element IN ('com_content', 'com_users', 'com_menus')"
            );
            $stmt->execute();
            $count = (int) $stmt->fetchColumn();

            // If we have the core extensions, base data is loaded
            return $count >= 3;
        } catch (\PDOException $e) {
            // Table doesn't exist or other error - base data not loaded
            return false;
        }
    }

    /**
     * Check whether a set of starting content has been loaded
     *
     * @param   \PDO    $pdo     PDO connection
     * @param   string  $prefix  Table prefix
     * @return  bool    True if a set appears to be loaded
     **/
    private static function isDataSetLoaded($pdo, $prefix)
    {
        try {
            // Every set brings articles with it, and the base data brings none
            $stmt = $pdo->prepare(
                "SELECT COUNT(*) FROM `{$prefix}content` WHERE state >= 0"
            );
            $stmt->execute();
            return (int) $stmt->fetchColumn() > 0;
        } catch (\PDOException $e) {
            // Table doesn't exist or other error
            return false;
        }
    }
}
