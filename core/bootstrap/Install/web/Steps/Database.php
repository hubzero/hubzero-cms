<?php

/**
 * Database Configuration Step
 *
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2025 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Bootstrap\Install\Web\Steps;

use Hubzero\Database\ConnectionInterface;
use PDOException;

class Database implements StepInterface
{
    private $installer;
    private $errors = [];
    private $existingConfig = [];

    public function __construct($installer)
    {
        $this->installer = $installer;
        $this->loadExistingConfig();
    }

    public function getTitle()
    {
        return 'Database Configuration';
    }

    public function render()
    {
        $installer = $this->installer;
        $config = $this->existingConfig;
        $errors = $this->errors;
        ob_start();
        include __DIR__ . '/../views/steps/database.php';
        return ob_get_clean();
    }

    public function process($data)
    {
        // Validate CSRF token
        if (!$this->installer->validateCsrf($data)) {
            return false;
        }

        // Get connection type
        $connectionType = $data['connection_type'] ?? '';

        // Validate connection type
        if (!in_array($connectionType, ['localhost', 'socket', 'custom'])) {
            $this->installer->addMessage('Please select a connection method.', 'error');
            return false;
        }

        // Validate required fields based on connection type
        if (empty($data['db'])) {
            $this->errors['db'] = 'Database name is required';
        }
        if (empty($data['user'])) {
            $this->errors['user'] = 'Username is required';
        }

        if ($connectionType === 'socket') {
            if (empty($data['socket'])) {
                $this->errors['socket'] = 'Socket path is required';
            }
        } elseif ($connectionType === 'custom') {
            if (empty($data['host'])) {
                $this->errors['host'] = 'Hostname is required';
            }
        }

        if (!empty($this->errors)) {
            return false;
        }

        // Build config array based on connection type
        // Table prefix: use existing config if set, otherwise default to 'jos_'
        $dbprefix = $this->existingConfig['dbprefix'] ?? 'jos_';
        if (empty($dbprefix)) {
            $dbprefix = 'jos_';
        }

        $config = [
            'dbtype' => 'mysql',
            'db' => trim($data['db']),
            'user' => trim($data['user']),
            'password' => $data['password'] ?? '',
            'dbprefix' => $dbprefix,
        ];

        if ($connectionType === 'localhost') {
            $config['host'] = '127.0.0.1';
            $config['port'] = '3306';
            $config['socket'] = '';
        } elseif ($connectionType === 'socket') {
            $config['host'] = 'localhost';
            $config['port'] = '';
            $config['socket'] = trim($data['socket']);
        } else {
            // Custom
            $config['host'] = trim($data['host']);
            $config['port'] = trim($data['port'] ?? '') ?: '3306';
            $config['socket'] = '';
        }

        // Test connection
        $pdo = $this->testConnection($config);
        if ($pdo === null) {
            return false;
        }

        // Save configuration
        if (!$this->saveConfig($config)) {
            $this->installer->addMessage('Failed to save database configuration.', 'error');
            return false;
        }

        // Store in session for later steps
        $this->installer->setSessionData('database', $config);
        $this->installer->markStepComplete('database');

        return true;
    }

    /**
     * Load existing configuration if available
     */
    private function loadExistingConfig()
    {
        $configPath = PATH_APP . '/config/database.php';
        if (file_exists($configPath)) {
            $config = include $configPath;
            if (is_array($config)) {
                $this->existingConfig = $config;
            }
        }

        // Only set minimal defaults - let the view handle empty state
        // This allows the progressive UI to start with nothing selected
        if (empty($this->existingConfig)) {
            $this->existingConfig = [
                'host' => '',
                'db' => '',
                'user' => '',
                'password' => '',
                'dbprefix' => 'jos_',
                'port' => '',
                'socket' => '',
            ];
        }
    }

    /**
     * Test database connection
     */
    private function testConnection($config)
    {
        // First connect without database to check if we need to create it
        $configWithoutDb = $config;
        unset($configWithoutDb['db']);

        try {
            $connection = $this->installer->connectToDatabaseOrFail($configWithoutDb, 10);

            // Check if database exists
            if (!$this->databaseExists($connection, $config['db'])) {
                // Try to create database
                if (!$this->createDatabase($connection, $config['db'])) {
                    $this->installer->addMessage(
                        "Database '{$config['db']}' does not exist and could not be created. "
                        . "Please create it manually or use an account with CREATE DATABASE privileges.",
                        'error'
                    );
                    return null;
                }
            }

            // Connect to the actual database
            return $this->installer->connectToDatabaseOrFail($config, 10);
        } catch (PDOException $e) {
            $this->installer->addMessage('Database connection failed: ' . $e->getMessage(), 'error');
            return null;
        }
    }

    /**
     * Check if database exists
     */
    private function databaseExists(ConnectionInterface $connection, string $name): bool
    {
        try {
            $stmt = $connection->prepare(
                "SELECT SCHEMA_NAME FROM INFORMATION_SCHEMA.SCHEMATA WHERE SCHEMA_NAME = ?"
            );
            $connection->bind($stmt, [$name]);
            $connection->execute($stmt);
            return $connection->fetchAssoc($stmt) !== null;
        } catch (PDOException $e) {
            return false;
        }
    }

    /**
     * Create database
     */
    private function createDatabase(ConnectionInterface $connection, string $name): bool
    {
        try {
            if (!preg_match('/^[a-zA-Z0-9_]+$/', $name)) {
                return false;
            }

            $connection->exec(
                "CREATE DATABASE `{$name}` CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci"
            );
            return true;
        } catch (PDOException $e) {
            return false;
        }
    }

    /**
     * Save database configuration
     */
    private function saveConfig($config)
    {
        $configDir = PATH_APP . '/config';

        // Create config directory if needed
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
}
