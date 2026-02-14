<?php

/**
 * @package    framework
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Hubzero\Database\Schema;

use Hubzero\Database\Driver;
use Hubzero\Database\Schema\Diff\SchemaDiff;
use Hubzero\Database\Schema\Diff\TableDiff;

/**
 * Migration Generator
 *
 * Generates migration PHP files from schema diffs.
 *
 * Usage:
 * ```php
 * $generator = new MigrationGenerator($driver);
 *
 * // Generate from a table diff
 * $content = $generator->generateFromTableDiff($diff, 'ComUsers');
 *
 * // Generate from a schema diff
 * $content = $generator->generateFromSchemaDiff($diff, 'Core');
 *
 * // Write to file
 * $generator->writeToFile($content, '/path/to/migrations/', 'Core');
 *
 * // Or all at once
 * $path = $generator->generateAndWrite($diff, '/path/to/migrations/', 'Core');
 * ```
 */
class MigrationGenerator
{
    /**
     * Database driver
     *
     * @var Driver
     */
    protected $driver;

    /**
     * SQL generator for diffs
     *
     * @var DiffSqlGenerator
     */
    protected $sqlGenerator;

    /**
     * Custom template content
     *
     * @var string|null
     */
    protected $template = null;

    /**
     * Safe mode flags for SQL generation
     *
     * @var int
     */
    protected $safeFlags = 0;

    /**
     * Whether to include table existence checks
     *
     * @var bool
     */
    protected $includeExistenceChecks = true;

    /**
     * Whether blank migration generation should reject TODO placeholders.
     *
     * @var bool
     */
    protected $strictBlankMigrations = false;

    /**
     * Copyright year for generated files
     *
     * @var string
     */
    protected $copyrightYear;

    /**
     * Create a new MigrationGenerator
     *
     * @param Driver $driver
     */
    public function __construct(Driver $driver)
    {
        $this->driver = $driver;
        $this->sqlGenerator = new DiffSqlGenerator($driver);
        $this->copyrightYear = date('Y');
    }

    /**
     * Set a custom template for migration files
     *
     * Available placeholders:
     * - <namespace> : The namespace (Migrations)
     * - <className> : The migration class name
     * - <description> : A description of the migration
     * - <year> : Copyright year
     * - <upCode> : The up() method body
     * - <downCode> : The down() method body
     *
     * @param  string  $template
     * @return self
     */
    public function setTemplate(string $template): self
    {
        $this->template = $template;
        return $this;
    }

    /**
     * Set safe mode flags for SQL generation
     *
     * @param  int  $flags  Bitmask of DiffSqlGenerator::SAFE_* constants
     * @return self
     */
    public function setSafeFlags(int $flags): self
    {
        $this->safeFlags = $flags;
        return $this;
    }

    /**
     * Set whether to include table/column existence checks
     *
     * @param  bool  $include
     * @return self
     */
    public function setIncludeExistenceChecks(bool $include): self
    {
        $this->includeExistenceChecks = $include;
        return $this;
    }

    /**
     * Set strict mode for blank migration generation.
     *
     * When enabled, generateBlank() throws if it would emit TODO stubs.
     * Use generateBlankWithBodies() to provide explicit method bodies.
     *
     * @param  bool  $strict
     * @return self
     */
    public function setStrictBlankMigrations(bool $strict): self
    {
        $this->strictBlankMigrations = $strict;
        return $this;
    }

    /**
     * Set copyright year
     *
     * @param  string  $year
     * @return self
     */
    public function setCopyrightYear(string $year): self
    {
        $this->copyrightYear = $year;
        return $this;
    }

    /**
     * Generate migration content from a table diff
     *
     * @param  TableDiff  $diff
     * @param  string     $component  Component name (e.g., 'ComUsers', 'Core')
     * @param  string     $description  Optional description
     * @return string  The migration file content
     */
    public function generateFromTableDiff(TableDiff $diff, string $component = 'Core', string $description = ''): string
    {
        $upSql = $this->sqlGenerator->generateUp($diff, $this->safeFlags);
        $downSql = $this->sqlGenerator->generateDown($diff, $this->safeFlags);

        $tableName = $diff->getFromTable()->getName();

        if (empty($description)) {
            $description = $this->generateTableDiffDescription($diff);
        }

        $upCode = $this->generateUpCode($upSql, $tableName);
        $downCode = $this->generateDownCode($downSql, $tableName);

        return $this->generateMigrationContent($component, $description, $upCode, $downCode);
    }

    /**
     * Generate migration content from a schema diff
     *
     * @param  SchemaDiff  $diff
     * @param  string      $component  Component name (e.g., 'ComUsers', 'Core')
     * @param  string      $description  Optional description
     * @return string  The migration file content
     */
    public function generateFromSchemaDiff(
        SchemaDiff $diff,
        string $component = 'Core',
        string $description = ''
    ): string {
        $upSql = $this->sqlGenerator->generateSchemaUp($diff, $this->safeFlags);
        $downSql = $this->sqlGenerator->generateSchemaDown($diff, $this->safeFlags);

        if (empty($description)) {
            $description = $this->generateSchemaDiffDescription($diff);
        }

        $upCode = $this->generateSchemaUpCode($diff, $upSql);
        $downCode = $this->generateSchemaDownCode($diff, $downSql);

        return $this->generateMigrationContent($component, $description, $upCode, $downCode);
    }

    /**
     * Generate a blank migration stub
     *
     * @param  string  $component  Component name
     * @param  string  $description  Description
     * @return string
     */
    public function generateBlank(string $component = 'Core', string $description = 'Migration script'): string
    {
        if ($this->strictBlankMigrations) {
            throw new \RuntimeException(
                'Strict blank migration mode is enabled; provide explicit up/down bodies.'
            );
        }

        $upCode = "        // TODO: Implement migration\n";
        $downCode = "        // TODO: Implement rollback\n";

        return $this->generateMigrationContent($component, $description, $upCode, $downCode);
    }

    /**
     * Generate a migration stub with explicit up/down bodies.
     *
     * @param  string  $upCode
     * @param  string  $downCode
     * @param  string  $component
     * @param  string  $description
     * @return string
     */
    public function generateBlankWithBodies(
        string $upCode,
        string $downCode,
        string $component = 'Core',
        string $description = 'Migration script'
    ): string {
        $trimmedUp = trim($upCode);
        $trimmedDown = trim($downCode);

        if ($trimmedUp === '' || $trimmedDown === '') {
            throw new \InvalidArgumentException('Blank migration bodies cannot be empty.');
        }

        if (
            $this->strictBlankMigrations
            && (
                stripos($trimmedUp, 'TODO: Implement migration') !== false
                || stripos($trimmedDown, 'TODO: Implement rollback') !== false
            )
        ) {
            throw new \RuntimeException(
                'Strict blank migration mode rejects TODO placeholder bodies.'
            );
        }

        $upBody = $this->normalizeMethodBodyIndentation($upCode);
        $downBody = $this->normalizeMethodBodyIndentation($downCode);

        return $this->generateMigrationContent($component, $description, $upBody, $downBody);
    }

    /**
     * Write migration content to a file
     *
     * @param  string  $content    The migration file content
     * @param  string  $directory  Directory to write to
     * @param  string  $component  Component name for filename
     * @param  string|null  $timestamp  Optional timestamp (YYYYMMDDHHMMSS), auto-generated if null
     * @return string  The full path to the written file
     */
    public function writeToFile(
        string $content,
        string $directory,
        string $component,
        ?string $timestamp = null
    ): string {
        if ($timestamp === null) {
            $timestamp = date('YmdHis');
        }

        $className = "Migration{$timestamp}{$component}";
        $filename = "{$className}.php";
        $filepath = rtrim($directory, '/') . '/' . $filename;

        // Replace className placeholder if present
        $content = str_replace('<className>', $className, $content);

        file_put_contents($filepath, $content);

        return $filepath;
    }

    /**
     * Generate and write migration file in one step
     *
     * @param  TableDiff|SchemaDiff  $diff
     * @param  string                $directory
     * @param  string                $component
     * @param  string                $description
     * @return string  The full path to the written file
     */
    public function generateAndWrite(
        $diff,
        string $directory,
        string $component = 'Core',
        string $description = ''
    ): string {
        if ($diff instanceof SchemaDiff) {
            $content = $this->generateFromSchemaDiff($diff, $component, $description);
        } elseif ($diff instanceof TableDiff) {
            $content = $this->generateFromTableDiff($diff, $component, $description);
        } else {
            throw new \InvalidArgumentException('Expected TableDiff or SchemaDiff');
        }

        return $this->writeToFile($content, $directory, $component);
    }

    /**
     * Get the class name for a migration
     *
     * @param  string       $component
     * @param  string|null  $timestamp
     * @return string
     */
    public function getClassName(string $component, ?string $timestamp = null): string
    {
        if ($timestamp === null) {
            $timestamp = date('YmdHis');
        }

        return "Migration{$timestamp}{$component}";
    }

    /**
     * Generate the migration file content
     *
     * @param  string  $component
     * @param  string  $description
     * @param  string  $upCode
     * @param  string  $downCode
     * @return string
     */
    protected function generateMigrationContent(
        string $component,
        string $description,
        string $upCode,
        string $downCode
    ): string {
        if ($this->template !== null) {
            return $this->applyTemplate($component, $description, $upCode, $downCode);
        }

        return $this->getDefaultTemplate($component, $description, $upCode, $downCode);
    }

    /**
     * Apply a custom template
     *
     * @param  string  $component
     * @param  string  $description
     * @param  string  $upCode
     * @param  string  $downCode
     * @return string
     */
    protected function applyTemplate(string $component, string $description, string $upCode, string $downCode): string
    {
        $timestamp = date('YmdHis');
        $className = "Migration{$timestamp}{$component}";

        return str_replace(
            ['<namespace>', '<className>', '<description>', '<year>', '<upCode>', '<downCode>'],
            ['Migrations', $className, $description, $this->copyrightYear, $upCode, $downCode],
            $this->template
        );
    }

    /**
     * Get the default migration template
     *
     * @param  string  $component
     * @param  string  $description
     * @param  string  $upCode
     * @param  string  $downCode
     * @return string
     */
    protected function getDefaultTemplate(
        string $component,
        string $description,
        string $upCode,
        string $downCode
    ): string {
        $timestamp = date('YmdHis');
        $className = "Migration{$timestamp}{$component}";

        return <<<PHP
<?php

/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-{$this->copyrightYear} The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Migrations;

use Hubzero\Content\Migration\Base;

/**
 * {$description}
 */
class {$className} extends Base
{
    /**
     * Up
     */
    public function up()
    {
{$upCode}
    }

    /**
     * Down
     */
    public function down()
    {
{$downCode}
    }
}

PHP;
    }

    /**
     * Generate up() method code from SQL statements
     *
     * @param  array   $sql
     * @param  string  $tableName
     * @return string
     */
    protected function generateUpCode(array $sql, string $tableName): string
    {
        if (empty($sql)) {
            return "        // No changes to apply\n";
        }

        $code = '';

        if ($this->includeExistenceChecks) {
            $code .= "        \$schema = \$this->db->schema();\n\n";
            $code .= "        if (\$schema->tableExists('{$tableName}')) {\n";
            $indent = '            ';
        } else {
            $indent = '        ';
        }

        foreach ($sql as $statement) {
            $escaped = $this->escapePhpString($statement);
            $code .= "{$indent}\$this->db->setQuery(\"{$escaped}\")->execute();\n";
        }

        if ($this->includeExistenceChecks) {
            $code .= "        }\n";
        }

        return $code;
    }

    /**
     * Generate down() method code from SQL statements
     *
     * @param  array   $sql
     * @param  string  $tableName
     * @return string
     */
    protected function generateDownCode(array $sql, string $tableName): string
    {
        if (empty($sql)) {
            return "        // No changes to revert\n";
        }

        $code = '';

        if ($this->includeExistenceChecks) {
            $code .= "        \$schema = \$this->db->schema();\n\n";
            $code .= "        if (\$schema->tableExists('{$tableName}')) {\n";
            $indent = '            ';
        } else {
            $indent = '        ';
        }

        foreach ($sql as $statement) {
            $escaped = $this->escapePhpString($statement);
            $code .= "{$indent}\$this->db->setQuery(\"{$escaped}\")->execute();\n";
        }

        if ($this->includeExistenceChecks) {
            $code .= "        }\n";
        }

        return $code;
    }

    /**
     * Generate up() code for schema-level diff
     *
     * @param  SchemaDiff  $diff
     * @param  array       $sql
     * @return string
     */
    protected function generateSchemaUpCode(SchemaDiff $diff, array $sql): string
    {
        if (empty($sql)) {
            return "        // No changes to apply\n";
        }

        $code = "        \$schema = \$this->db->schema();\n\n";

        // Group by operation type for clarity
        $addedTables = $diff->getAddedTables();
        $removedTables = $diff->getRemovedTables();
        $changedTables = $diff->getChangedTables();

        // Comment sections
        if (count($addedTables) > 0) {
            $code .= "        // Create new tables\n";
        }

        if (count($removedTables) > 0) {
            $code .= "        // Drop removed tables\n";
        }

        if (count($changedTables) > 0) {
            $code .= "        // Alter existing tables\n";
        }

        $code .= "\n";

        // Generate the SQL execution
        foreach ($sql as $statement) {
            $escaped = $this->escapePhpString($statement);
            $code .= "        \$this->db->setQuery(\"{$escaped}\")->execute();\n";
        }

        return $code;
    }

    /**
     * Generate down() code for schema-level diff
     *
     * @param  SchemaDiff  $diff
     * @param  array       $sql
     * @return string
     */
    protected function generateSchemaDownCode(SchemaDiff $diff, array $sql): string
    {
        if (empty($sql)) {
            return "        // No changes to revert\n";
        }

        $code = "        \$schema = \$this->db->schema();\n\n";
        $code .= "        // Reverse the schema changes\n\n";

        foreach ($sql as $statement) {
            $escaped = $this->escapePhpString($statement);
            $code .= "        \$this->db->setQuery(\"{$escaped}\")->execute();\n";
        }

        return $code;
    }

    /**
     * Generate a description for a table diff
     *
     * @param  TableDiff  $diff
     * @return string
     */
    protected function generateTableDiffDescription(TableDiff $diff): string
    {
        $parts = [];
        $tableName = $diff->getFromTable()->getName();

        $added = count($diff->getAddedColumns());
        $removed = count($diff->getRemovedColumns());
        $changed = count($diff->getChangedColumns());
        $renamed = count($diff->getRenamedColumns());

        if ($added > 0) {
            $parts[] = "add {$added} column(s)";
        }
        if ($removed > 0) {
            $parts[] = "remove {$removed} column(s)";
        }
        if ($changed > 0) {
            $parts[] = "modify {$changed} column(s)";
        }
        if ($renamed > 0) {
            $parts[] = "rename {$renamed} column(s)";
        }

        if (empty($parts)) {
            return "Migration script for {$tableName}";
        }

        return "Migration script to " . implode(', ', $parts) . " in {$tableName}";
    }

    /**
     * Generate a description for a schema diff
     *
     * @param  SchemaDiff  $diff
     * @return string
     */
    protected function generateSchemaDiffDescription(SchemaDiff $diff): string
    {
        $parts = [];

        $added = count($diff->getAddedTables());
        $removed = count($diff->getRemovedTables());
        $changed = count($diff->getChangedTables());
        $renamed = count($diff->getRenamedTables());

        if ($added > 0) {
            $parts[] = "add {$added} table(s)";
        }
        if ($removed > 0) {
            $parts[] = "remove {$removed} table(s)";
        }
        if ($changed > 0) {
            $parts[] = "modify {$changed} table(s)";
        }
        if ($renamed > 0) {
            $parts[] = "rename {$renamed} table(s)";
        }

        if (empty($parts)) {
            return "Schema migration script";
        }

        return "Migration script to " . implode(', ', $parts);
    }

    /**
     * Escape a string for use in PHP double-quoted string
     *
     * @param  string  $str
     * @return string
     */
    protected function escapePhpString(string $str): string
    {
        return addcslashes($str, "\\\"\$\n\r\t");
    }

    /**
     * Normalize a method body string to 8-space indentation with trailing newline.
     *
     * @param  string  $body
     * @return string
     */
    protected function normalizeMethodBodyIndentation(string $body): string
    {
        $body = str_replace("\r\n", "\n", $body);
        $body = rtrim($body, "\n");
        $lines = explode("\n", $body);

        foreach ($lines as &$line) {
            $line = '        ' . ltrim($line);
        }
        unset($line);

        return implode("\n", $lines) . "\n";
    }
}
