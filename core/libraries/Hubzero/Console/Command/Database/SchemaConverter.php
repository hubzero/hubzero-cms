<?php

/**
 * @package    framework
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Hubzero\Console\Command\Database;

use Hubzero\Console\Output;
use Hubzero\Console\Arguments;

/**
 * Database schema converter
 *
 * Converts database schema files from one database type to another.
 * Currently supports MySQL to SQLite conversion.
 */
class SchemaConverter
{
    /**
     * Output handler
     *
     * @var Output
     */
    protected $output;

    /**
     * Arguments handler
     *
     * @var Arguments
     */
    protected $arguments;

    /**
     * Supported source database types
     *
     * @var array
     */
    protected $supportedFrom = ['mysql'];

    /**
     * Supported target database types
     *
     * @var array
     */
    protected $supportedTo = ['sqlite'];

    /**
     * Constructor
     *
     * @param  Output     $output
     * @param  Arguments  $arguments
     */
    public function __construct(Output $output, Arguments $arguments)
    {
        $this->output = $output;
        $this->arguments = $arguments;
    }

    /**
     * Run the schema conversion
     *
     * @return void
     */
    public function convert(): void
    {
        // Get source and target types
        $from = $this->arguments->getOpt('from');
        $to = $this->arguments->getOpt('to');

        if (!$from || !$to) {
            $this->output->error('Error: You must specify both --from and --to arguments');
            return;
        }

        $from = strtolower($from);
        $to = strtolower($to);

        if (!in_array($from, $this->supportedFrom)) {
            $this->output->error(
                "Error: Unsupported source database type '$from'. Supported: "
                . implode(', ', $this->supportedFrom)
            );
            return;
        }

        if (!in_array($to, $this->supportedTo)) {
            $this->output->error(
                "Error: Unsupported target database type '$to'. Supported: "
                . implode(', ', $this->supportedTo)
            );
            return;
        }

        // Get input/output files
        $basePath = PATH_CORE . '/bootstrap/Install/sql';
        $inputFile = $this->arguments->getOpt('input') ?: "$basePath/$from/schema.sql";
        $outputFile = $this->arguments->getOpt('output') ?: "$basePath/$to/schema.sql";

        if (!file_exists($inputFile)) {
            $this->output->error("Error: Input file not found: $inputFile");
            return;
        }

        // Ensure output directory exists
        $outputDir = dirname($outputFile);
        if (!is_dir($outputDir)) {
            if (!mkdir($outputDir, 0755, true)) {
                $this->output->error("Error: Could not create output directory: $outputDir");
                return;
            }
        }

        $this->output->addLine("Converting schema from $from to $to...");
        $this->output->addLine("  Input:  $inputFile");
        $this->output->addLine("  Output: $outputFile");

        // Dispatch to appropriate converter
        $method = "convert" . ucfirst($from) . "To" . ucfirst($to);
        if (method_exists($this, $method)) {
            $result = $this->$method($inputFile, $outputFile);
            if ($result) {
                $this->output->addLine("Schema conversion complete!", 'success');
            }
        } else {
            $this->output->error("Error: No converter available for $from to $to");
        }
    }

    /**
     * Convert MySQL schema to SQLite
     *
     * @param  string  $inputFile
     * @param  string  $outputFile
     * @return bool
     */
    protected function convertMysqlToSqlite(string $inputFile, string $outputFile): bool
    {
        $content = file_get_contents($inputFile);

        // Remove MySQL session settings
        $content = preg_replace("/^SET NAMES.*$/m", '', $content);
        $content = preg_replace("/^SET @@SESSION.*$/m", '', $content);

        // Remove VIEW definitions from content (we'll handle them separately at the end)
        $contentNoViews = preg_replace('/CREATE VIEW[^;]+;/s', '', $content);
        $contentNoViews = preg_replace('/CREATE ALGORITHM[^;]+;/s', '', $contentNoViews);
        $contentNoViews = preg_replace('/DROP VIEW[^;]+;/s', '', $contentNoViews);

        // Process tables
        $output = [];
        $output[] = '--';
        $output[] = '-- SQLite Schema for HubZero CMS';
        $output[] = '-- Auto-generated from mysql/schema.sql';
        $output[] = '-- ';
        $output[] = '-- This schema is compatible with SQLite 3.35.0+';
        $output[] = '--';
        $output[] = '';
        $output[] = 'PRAGMA foreign_keys = ON;';
        $output[] = '';

        // Split by CREATE TABLE
        $tables = preg_split('/(CREATE TABLE)/', $contentNoViews, -1, PREG_SPLIT_DELIM_CAPTURE);
        $tableCount = 0;

        for ($i = 1; $i < count($tables); $i += 2) {
            if (!isset($tables[$i + 1])) {
                continue;
            }

            $tableDef = $tables[$i] . $tables[$i + 1];

            // Get table name
            if (!preg_match('/CREATE TABLE\s+`([^`]+)`/', $tableDef, $matches)) {
                continue;
            }
            $tableName = $matches[1];

            // Skip if not a valid table definition
            if (strpos($tableDef, '(') === false) {
                continue;
            }

            $tableCount++;

            // Convert the table definition
            $converted = $this->convertMysqlTable($tableDef);

            if ($converted) {
                $output[] = '--';
                $output[] = "-- Table: `$tableName`";
                $output[] = '--';
                $output[] = '';
                $output[] = $converted;
                $output[] = '';
            }
        }

        // Now handle views
        $viewCount = 0;
        $viewDefs = [];

        // Extract all view definitions
        preg_match_all(
            '/DROP VIEW IF EXISTS\s+`([^`]+)`;\s*CREATE\s+(?:ALGORITHM=\S+\s+)?'
            . '(?:DEFINER=\S+\s+)?(?:SQL SECURITY \w+\s+)?VIEW\s+`\1`\s+AS\s+'
            . '(.+?)(?=;\s*(?:--|DROP VIEW|$))/si',
            $content,
            $viewMatches,
            PREG_SET_ORDER
        );

        foreach ($viewMatches as $match) {
            $viewName = $match[1];
            $viewDef = trim($match[2]);
            $viewDef = rtrim($viewDef, ';');
            $viewDefs[$viewName] = $viewDef;
        }

        // Order views by dependencies (simple topological sort)
        $orderedViews = [];
        $remaining = $viewDefs;
        $maxIterations = count($viewDefs) + 1;
        $iteration = 0;

        while (!empty($remaining) && $iteration < $maxIterations) {
            $iteration++;
            foreach ($remaining as $viewName => $viewDef) {
                $hasDep = false;
                foreach (array_keys($remaining) as $otherView) {
                    if ($otherView !== $viewName && strpos($viewDef, "`$otherView`") !== false) {
                        $hasDep = true;
                        break;
                    }
                }
                if (!$hasDep) {
                    $orderedViews[$viewName] = $viewDef;
                    unset($remaining[$viewName]);
                }
            }
        }

        // Add any remaining views (circular dependencies)
        foreach ($remaining as $viewName => $viewDef) {
            $orderedViews[$viewName] = $viewDef;
        }

        // Add views to output in order
        foreach ($orderedViews as $viewName => $viewDef) {
            $viewCount++;
            $output[] = '--';
            $output[] = "-- View: `$viewName`";
            $output[] = '--';
            $output[] = '';
            $output[] = "DROP VIEW IF EXISTS `$viewName`;";
            $output[] = '';
            $output[] = "CREATE VIEW `$viewName` AS";
            $output[] = "  $viewDef;";
            $output[] = '';
        }

        file_put_contents($outputFile, implode("\n", $output));

        $this->output->addLine("  Converted $tableCount tables and $viewCount views");

        return true;
    }

    /**
     * Convert a single MySQL table definition to SQLite
     *
     * @param  string  $sql
     * @return string
     */
    protected function convertMysqlTable(string $sql): string
    {
        // Remove MySQL comments (-- Table structure for...)
        $sql = preg_replace('/--\s*\n--\s*Table structure for table.*?\n--\s*\n/s', '', $sql);

        // Remove ENGINE, DEFAULT CHARSET, COLLATE
        $sql = preg_replace('/\)\s*ENGINE=\S+[^;]*;/', ');', $sql);

        // Remove COLLATE on individual columns
        $sql = preg_replace('/\s+COLLATE\s+\S+/', '', $sql);

        // Remove CHARACTER SET on individual columns
        $sql = preg_replace('/\s+CHARACTER SET\s+\S+/', '', $sql);

        // Remove COMMENT strings
        $sql = preg_replace("/\s+COMMENT\s+'[^']*'/", '', $sql);

        // Handle AUTO_INCREMENT columns
        $hasAutoIncrement = preg_match('/`(\w+)`[^,]+AUTO_INCREMENT/i', $sql, $aiMatch);
        $autoIncrementColumn = $hasAutoIncrement ? $aiMatch[1] : null;

        // Check if PRIMARY KEY is composite (has commas inside parentheses)
        $hasCompositePK = preg_match('/PRIMARY KEY\s*\([^)]*,[^)]*\)/', $sql);

        // Convert integer types with size to just INTEGER
        $sql = preg_replace('/\b(tiny|small|medium|big)?int\(\d+\)/i', 'INTEGER', $sql);

        // unsigned is not supported in SQLite
        $sql = preg_replace('/\s+unsigned/i', '', $sql);

        // Convert AUTO_INCREMENT to INTEGER PRIMARY KEY (only if not composite PK)
        if ($autoIncrementColumn && !$hasCompositePK) {
            $sql = preg_replace(
                '/`' . $autoIncrementColumn . '`\s+INTEGER\s+NOT NULL\s+AUTO_INCREMENT/',
                '`' . $autoIncrementColumn . '` INTEGER PRIMARY KEY AUTOINCREMENT',
                $sql
            );
            $sql = preg_replace(
                '/`' . $autoIncrementColumn . '`\s+INTEGER\s+AUTO_INCREMENT/',
                '`' . $autoIncrementColumn . '` INTEGER PRIMARY KEY AUTOINCREMENT',
                $sql
            );

            // Remove the separate PRIMARY KEY declaration for this column
            $sql = preg_replace('/,\s*PRIMARY KEY\s+\(`' . $autoIncrementColumn . '`\)/', '', $sql);
        } else {
            // Just remove AUTO_INCREMENT for composite PKs
            $sql = preg_replace('/\s+AUTO_INCREMENT/i', '', $sql);
        }

        // Fallback: catch any remaining AUTO_INCREMENT patterns
        if (preg_match('/`(\w+)`\s+INTEGER\s+(?:NOT NULL\s+)?AUTO_INCREMENT/i', $sql, $fallbackMatch)) {
            $sql = preg_replace(
                '/`' . $fallbackMatch[1] . '`\s+INTEGER\s+NOT NULL\s+AUTO_INCREMENT/i',
                '`' . $fallbackMatch[1] . '` INTEGER PRIMARY KEY AUTOINCREMENT',
                $sql
            );
            $sql = preg_replace(
                '/`' . $fallbackMatch[1] . '`\s+INTEGER\s+AUTO_INCREMENT/i',
                '`' . $fallbackMatch[1] . '` INTEGER PRIMARY KEY AUTOINCREMENT',
                $sql
            );
            $sql = preg_replace('/,\s*PRIMARY KEY\s+\(`' . $fallbackMatch[1] . '`\)/', '', $sql);
        }

        // Convert MySQL datetime zero default
        $sql = preg_replace("/DEFAULT '0000-00-00 00:00:00'/", "DEFAULT NULL", $sql);
        $sql = preg_replace("/DEFAULT '0000-00-00'/", "DEFAULT NULL", $sql);

        // Convert current_timestamp()
        $sql = preg_replace('/current_timestamp\(\)/i', 'CURRENT_TIMESTAMP', $sql);

        // Remove ON UPDATE CURRENT_TIMESTAMP
        $sql = preg_replace('/\s+ON UPDATE CURRENT_TIMESTAMP/i', '', $sql);

        // Remove FULLTEXT KEY definitions
        $sql = preg_replace('/,\s*FULLTEXT KEY\s+`[^`]+`\s*\([^)]+\)/', '', $sql);

        // Remove USING BTREE/HASH from index definitions
        $sql = preg_replace('/\s+USING\s+(BTREE|HASH)/i', '', $sql);

        // Remove MySQL prefix length from index columns
        $sql = preg_replace('/`([^`]+)`\(\d+\)/', '`$1`', $sql);

        // Extract and convert indexes
        $indexes = [];

        // Extract UNIQUE KEY
        preg_match_all('/,\s*UNIQUE KEY\s+`([^`]+)`\s*\(([^)]+)\)/', $sql, $ukMatches, PREG_SET_ORDER);
        foreach ($ukMatches as $match) {
            $indexName = $match[1];
            $columns = $match[2];
            preg_match('/CREATE TABLE\s+`([^`]+)`/', $sql, $tableMatch);
            $tableName = $tableMatch[1] ?? 'unknown';
            $indexes[] = "CREATE UNIQUE INDEX IF NOT EXISTS `{$indexName}` ON `{$tableName}` ({$columns});";
        }
        $sql = preg_replace('/,\s*UNIQUE KEY\s+`[^`]+`\s*\([^)]+\)/', '', $sql);

        // Extract regular KEY (indexes)
        preg_match_all('/,\s*KEY\s+`([^`]+)`\s*\(([^)]+)\)/', $sql, $kMatches, PREG_SET_ORDER);
        foreach ($kMatches as $match) {
            $indexName = $match[1];
            $columns = $match[2];
            preg_match('/CREATE TABLE\s+`([^`]+)`/', $sql, $tableMatch);
            $tableName = $tableMatch[1] ?? 'unknown';
            $indexes[] = "CREATE INDEX IF NOT EXISTS `{$indexName}` ON `{$tableName}` ({$columns});";
        }
        $sql = preg_replace('/,\s*KEY\s+`[^`]+`\s*\([^)]+\)/', '', $sql);

        // Clean up any double commas or trailing commas
        $sql = preg_replace('/,(\s*,)+/', ',', $sql);
        $sql = preg_replace('/,\s*\)/', "\n)", $sql);

        // Clean up extra whitespace
        $sql = preg_replace('/\n\s*\n/', "\n", $sql);

        // Add index statements
        if (!empty($indexes)) {
            $sql .= "\n" . implode("\n", $indexes);
        }

        return trim($sql);
    }
}
