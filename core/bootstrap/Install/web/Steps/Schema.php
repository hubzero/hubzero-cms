<?php

/**
 * Schema Loading Step
 *
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2025 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Bootstrap\Install\Web\Steps;

use Hubzero\Database\SqlParser;
use PDOException;

class Schema implements StepInterface
{
    private $installer;
    private $status = [];

    public function __construct($installer)
    {
        $this->installer = $installer;
        $this->checkStatus();
    }

    public function getTitle()
    {
        return 'Database Schema';
    }

    public function render()
    {
        $installer = $this->installer;
        $status = $this->status;
        // Check if schema/data were just loaded and we should start migrations
        $startMigrations = $this->installer->getSessionData('schema_data_loaded', false)
            && !$status['migrations_run'];
        // Clear the flag
        if ($startMigrations) {
            $this->installer->setSessionData('schema_data_loaded', false);
        }
        ob_start();
        include __DIR__ . '/../views/steps/schema.php';
        return ob_get_clean();
    }

    public function process($data)
    {
        // Validate CSRF token
        if (!$this->installer->validateCsrf($data)) {
            return false;
        }

        $loadSample = !empty($data['load_sample']);

        // Check if this is a "continue after migrations" request
        if (!empty($data['migrations_complete'])) {
            // Re-check status to verify migrations actually ran
            $this->checkStatus();
            if ($this->status['migrations_run']) {
                $this->installer->markStepComplete('schema');
                return true;
            }
            // Migrations didn't actually complete - stay on page
            return false;
        }

        // Load schema
        if (!$this->status['schema_loaded']) {
            if (!$this->loadSchema()) {
                $this->installer->addMessage('Failed to load database schema.', 'error');
                return false;
            }
        }

        // Load base data
        if (!$this->status['data_loaded']) {
            if (!$this->loadData()) {
                $this->installer->addMessage('Failed to load base data.', 'error');
                return false;
            }
        }

        // Load sample data if requested
        if ($loadSample && !$this->status['sample_loaded']) {
            if (!$this->loadSampleData()) {
                $this->installer->addMessage('Failed to load sample data.', 'warning');
                // Continue anyway - sample data is optional
            }
        }

        // Re-check status after loading schema/data
        $this->checkStatus();

        // If migrations already done, complete the step
        if ($this->status['migrations_run']) {
            $this->installer->markStepComplete('schema');
            return true;
        }

        // Otherwise, stay on page - migrations will be run via SSE
        // Store that schema/data are loaded so the view knows to start migrations
        $this->installer->setSessionData('schema_data_loaded', true);
        return false;
    }

    /**
     * Check current database status
     */
    private function checkStatus()
    {
        $this->status = [
            'schema_loaded' => false,
            'data_loaded' => false,
            'sample_loaded' => false,
            'migrations_run' => false,
            'table_count' => 0,
            'pending_migrations' => 0,
        ];

        // Load database config directly (no framework available in web installer)
        $configPath = PATH_APP . '/config/database.php';
        $dbConfig = file_exists($configPath) ? include $configPath : null;
        if (!$dbConfig || !is_array($dbConfig)) {
            return;
        }

        $pdo = $this->installer->connectToDatabase($dbConfig);
        if (!$pdo) {
            return;
        }

        $prefix = $dbConfig['dbprefix'] ?? 'jos_';

        // Count tables
        try {
            $stmt = $pdo->prepare(
                "SELECT COUNT(*) FROM INFORMATION_SCHEMA.TABLES
                 WHERE TABLE_SCHEMA = DATABASE()
                 AND TABLE_NAME LIKE :prefix"
            );
            $stmt->execute(['prefix' => $prefix . '%']);
            $this->status['table_count'] = (int) $stmt->fetchColumn();
            $this->status['schema_loaded'] = $this->status['table_count'] > 0;
        } catch (PDOException $e) {
            // Ignore
        }

        // Check for base data
        if ($this->status['schema_loaded']) {
            try {
                $stmt = $pdo->prepare(
                    "SELECT COUNT(*) FROM `{$prefix}extensions`
                     WHERE element IN ('com_content', 'com_users', 'com_menus')"
                );
                $stmt->execute();
                $this->status['data_loaded'] = (int) $stmt->fetchColumn() >= 3;
            } catch (PDOException $e) {
                // Ignore
            }
        }

        // Check for sample data
        if ($this->status['data_loaded']) {
            try {
                $stmt = $pdo->prepare("SELECT COUNT(*) FROM `{$prefix}content` WHERE state >= 0");
                $stmt->execute();
                $this->status['sample_loaded'] = (int) $stmt->fetchColumn() > 0;
            } catch (PDOException $e) {
                // Ignore
            }
        }

        // Check migrations status
        if ($this->status['data_loaded']) {
            $this->checkMigrationStatus($pdo, $prefix);
        }
    }

    /**
     * Check migration status
     */
    private function checkMigrationStatus($pdo, $prefix)
    {
        // Check if migrations table exists
        try {
            $stmt = $pdo->prepare(
                "SELECT COUNT(*) FROM INFORMATION_SCHEMA.TABLES
                 WHERE TABLE_SCHEMA = DATABASE()
                 AND TABLE_NAME IN ('migrations', :prefixed)"
            );
            $stmt->execute(['prefixed' => $prefix . 'migrations']);
            $migTableExists = (int) $stmt->fetchColumn() > 0;
        } catch (PDOException $e) {
            return;
        }

        if (!$migTableExists) {
            // No migrations table means migrations haven't been run yet
            // Count available migration files
            $this->status['pending_migrations'] = $this->countMigrationFiles();
            return;
        }

        // Count migrations that have been run (direction = 'up' and status = 'success')
        try {
            // Try prefixed table first
            $tableName = $prefix . 'migrations';
            $stmt = $pdo->prepare("SELECT COUNT(DISTINCT file) FROM `{$tableName}` WHERE direction = 'up'");
            $stmt->execute();
            $runCount = (int) $stmt->fetchColumn();
        } catch (PDOException $e) {
            try {
                // Try unprefixed table
                $stmt = $pdo->prepare("SELECT COUNT(DISTINCT file) FROM `migrations` WHERE direction = 'up'");
                $stmt->execute();
                $runCount = (int) $stmt->fetchColumn();
            } catch (PDOException $e2) {
                return;
            }
        }

        // Count total migration files
        $totalMigrations = $this->countMigrationFiles();
        $this->status['pending_migrations'] = max(0, $totalMigrations - $runCount);
        $this->status['migrations_run'] = ($this->status['pending_migrations'] === 0);
    }

    /**
     * Count migration files in the codebase
     */
    private function countMigrationFiles()
    {
        $count = 0;
        $searchPaths = [
            PATH_CORE . '/migrations',
        ];

        // Add component migrations
        $componentDirs = [PATH_CORE . '/components'];
        foreach ($componentDirs as $base) {
            if (!is_dir($base)) {
                continue;
            }
            foreach (scandir($base) as $dir) {
                if ($dir[0] === '.') {
                    continue;
                }
                $migPath = $base . '/' . $dir . '/migrations';
                if (is_dir($migPath)) {
                    $searchPaths[] = $migPath;
                }
            }
        }

        // Add plugin migrations
        $pluginBase = PATH_CORE . '/plugins';
        if (is_dir($pluginBase)) {
            foreach (scandir($pluginBase) as $group) {
                if ($group[0] === '.') {
                    continue;
                }
                $groupPath = $pluginBase . '/' . $group;
                if (!is_dir($groupPath)) {
                    continue;
                }
                foreach (scandir($groupPath) as $plugin) {
                    if ($plugin[0] === '.') {
                        continue;
                    }
                    $migPath = $groupPath . '/' . $plugin . '/migrations';
                    if (is_dir($migPath)) {
                        $searchPaths[] = $migPath;
                    }
                }
            }
        }

        // Count migration files
        foreach ($searchPaths as $path) {
            if (!is_dir($path)) {
                continue;
            }
            foreach (scandir($path) as $file) {
                if (preg_match('/^Migration[0-9]{14}[[:alnum:]]+\.php$/', $file)) {
                    $count++;
                }
            }
        }

        return $count;
    }

    /**
     * Load schema
     */
    private function loadSchema()
    {
        // Load database config directly (no framework available in web installer)
        $configPath = PATH_APP . '/config/database.php';
        $dbConfig = file_exists($configPath) ? include $configPath : null;
        if (!$dbConfig || !is_array($dbConfig)) {
            return false;
        }

        $pdo = $this->installer->connectToDatabase($dbConfig);
        if (!$pdo) {
            return false;
        }

        $prefix = $dbConfig['dbprefix'] ?? 'jos_';
        $schemaPath = INSTALL_ROOT . '/sql/mysql/schema.sql';

        return $this->executeSqlFile($pdo, $schemaPath, $prefix);
    }

    /**
     * Load base data
     */
    private function loadData()
    {
        // Load database config directly (no framework available in web installer)
        $configPath = PATH_APP . '/config/database.php';
        $dbConfig = file_exists($configPath) ? include $configPath : null;
        if (!$dbConfig || !is_array($dbConfig)) {
            return false;
        }

        $pdo = $this->installer->connectToDatabase($dbConfig);
        if (!$pdo) {
            return false;
        }

        $prefix = $dbConfig['dbprefix'] ?? 'jos_';
        $dataPath = INSTALL_ROOT . '/sql/mysql/data.sql';

        return $this->executeSqlFile($pdo, $dataPath, $prefix);
    }

    /**
     * Load sample data
     */
    private function loadSampleData()
    {
        // Load database config directly (no framework available in web installer)
        $configPath = PATH_APP . '/config/database.php';
        $dbConfig = file_exists($configPath) ? include $configPath : null;
        if (!$dbConfig || !is_array($dbConfig)) {
            return false;
        }

        $pdo = $this->installer->connectToDatabase($dbConfig);
        if (!$pdo) {
            return false;
        }

        $prefix = $dbConfig['dbprefix'] ?? 'jos_';
        $samplePath = INSTALL_ROOT . '/sql/mysql/sample.sql';

        if (!file_exists($samplePath)) {
            return true; // No sample file is OK
        }

        return $this->executeSqlFile($pdo, $samplePath, $prefix);
    }

    /**
     * Execute a SQL file
     */
    private function executeSqlFile($pdo, $path, $prefix)
    {
        // Use shared SqlParser to load and split the SQL file
        $statements = SqlParser::loadFile($path, $prefix);
        if ($statements === false) {
            return false;
        }

        foreach ($statements as $statement) {
            $statement = trim($statement);
            if (empty($statement)) {
                continue;
            }

            try {
                $pdo->exec($statement);
            } catch (PDOException $e) {
                // Log but continue - some errors like "table already exists" are OK
                error_log("SQL Warning: " . $e->getMessage());
            }
        }

        return true;
    }

    /**
     * Run database migrations via muse
     *
     * @return bool True on success, false on failure
     */
    private function runMigrations()
    {
        $musePath = PATH_CORE . '/bin/muse';

        if (!file_exists($musePath)) {
            $this->installer->addMessage('Muse executable not found.', 'error');
            return false;
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

        // Execute the command
        $output = [];
        $returnCode = 0;
        exec($command, $output, $returnCode);

        // Log output for debugging
        $outputText = implode("\n", $output);
        if (!empty($outputText)) {
            error_log("Migration output: " . $outputText);
        }

        if ($returnCode !== 0) {
            $this->installer->addMessage(
                'Migration failed. Check the logs for details.',
                'error'
            );
            return false;
        }

        return true;
    }
}
