<?php

/**
 * @package    framework
 * @copyright  Copyright (c) 2005-2025 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Hubzero\Database;

/**
 * SQL Parser utility class
 *
 * Provides methods for parsing and splitting SQL content into individual statements.
 * Handles multi-line statements, quoted strings, and SQL comments correctly.
 *
 * This class is designed to work both with the HUBzero framework autoloading
 * and standalone during installation (before autoloading is available).
 */
class SqlParser
{
    /**
     * Split SQL content into individual statements
     *
     * Handles:
     * - Multi-line statements
     * - Quoted strings (single and double quotes)
     * - Escaped characters within strings
     * - Full-line comments (-- at start of line)
     * - Inline comments (-- within a line, outside strings)
     *
     * @param   string  $sql  SQL content to split
     * @return  array   Array of individual SQL statements
     */
    public static function splitStatements(string $sql): array
    {
        $statements = [];
        $current = '';
        $inString = false;
        $stringChar = '';
        $escaped = false;

        $lines = explode("\n", $sql);

        foreach ($lines as $line) {
            $trimmed = trim($line);

            // Skip empty lines and full-line comments
            if ($trimmed === '' || strpos($trimmed, '--') === 0) {
                continue;
            }

            // Process character by character to handle strings properly
            $len = strlen($line);
            for ($i = 0; $i < $len; $i++) {
                $char = $line[$i];

                if ($escaped) {
                    $current .= $char;
                    $escaped = false;
                    continue;
                }

                if ($char === '\\') {
                    $current .= $char;
                    $escaped = true;
                    continue;
                }

                if ($inString) {
                    $current .= $char;
                    if ($char === $stringChar) {
                        $inString = false;
                    }
                    continue;
                }

                // Check for string start
                if ($char === "'" || $char === '"') {
                    $inString = true;
                    $stringChar = $char;
                    $current .= $char;
                    continue;
                }

                // Check for inline comment
                if ($char === '-' && $i + 1 < $len && $line[$i + 1] === '-') {
                    // Rest of line is comment, skip
                    break;
                }

                // Statement delimiter
                if ($char === ';') {
                    $current = trim($current);
                    if (!empty($current)) {
                        $statements[] = $current;
                    }
                    $current = '';
                    continue;
                }

                $current .= $char;
            }

            // Add space for line continuation
            if (!empty($current)) {
                $current .= ' ';
            }
        }

        // Add final statement if any (handles statements without trailing semicolon)
        $current = trim($current);
        if (!empty($current)) {
            $statements[] = $current;
        }

        return $statements;
    }

    /**
     * Replace table prefix placeholder in SQL content
     *
     * Replaces the #__ placeholder with the actual table prefix.
     *
     * @param   string  $sql     SQL content
     * @param   string  $prefix  Table prefix to use
     * @return  string  SQL with replaced prefix
     */
    public static function replacePrefix(string $sql, string $prefix): string
    {
        return str_replace('#__', $prefix, $sql);
    }

    /**
     * Load and parse a SQL file
     *
     * Reads a SQL file, optionally replaces the table prefix,
     * and returns an array of individual statements.
     *
     * @param   string       $path    Path to the SQL file
     * @param   string|null  $prefix  Table prefix (if null, no replacement is done)
     * @return  array|false  Array of statements, or false if file cannot be read
     */
    public static function loadFile(string $path, ?string $prefix = null)
    {
        if (!file_exists($path)) {
            return false;
        }

        $sql = file_get_contents($path);
        if ($sql === false) {
            return false;
        }

        if ($prefix !== null) {
            $sql = self::replacePrefix($sql, $prefix);
        }

        return self::splitStatements($sql);
    }
}
