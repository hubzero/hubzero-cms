<?php

/**
 * @package    framework
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Hubzero\Database;

/**
 * Shared backend metadata registry
 *
 * This centralizes driver and syntax class mappings that were previously
 * duplicated in DatabaseManager and Query.
 *
 * This is the canonical source for built-in driver and syntax metadata.
 */
final class BackendRegistry
{
    /**
     * Map of built-in driver names to backend tokens.
     *
     * @return array<string, string>
     */
    public static function driverBackendMap(): array
    {
        return [
            'mysql'    => 'mysql',
            'mariadb'  => 'mariadb',
            'percona'  => 'percona',
            'cubrid'   => 'cubrid',
            'sqlite'   => 'sqlite',
            'mock'     => 'mock',
            'pgsql'    => 'pgsql',
            'firebird' => 'firebird',
            'informix' => 'informix',
            'oci'      => 'oci',
            'sqlsrv'   => 'sqlsrv',
            'db2'      => 'db2',
            'ase'      => 'ase',
        ];
    }

    /**
     * Map of syntax names to backend tokens.
     *
     * @return array<string, string>
     */
    public static function syntaxBackendMap(): array
    {
        return [
            'mysql'    => 'mysql',
            'mariadb'  => 'mariadb',
            'percona'  => 'percona',
            'cubrid'   => 'cubrid',
            'pgsql'    => 'pgsql',
            'sqlite'   => 'sqlite',
            'firebird' => 'firebird',
            'informix' => 'informix',
            'sqlsrv'   => 'sqlsrv',
            'db2'      => 'db2',
            'oci'      => 'oci',
            'ase'      => 'ase',
        ];
    }

    /**
     * Map of built-in driver names to class names.
     *
     * @return array<string, string>
     */
    public static function driverClassMap(): array
    {
        return [
            'mysql'    => '\\Hubzero\\Database\\Drivers\\Mysql\\MysqlDriver',
            'mariadb'  => '\\Hubzero\\Database\\Drivers\\Mariadb\\MariadbDriver',
            'percona'  => '\\Hubzero\\Database\\Drivers\\Percona\\PerconaDriver',
            'cubrid'   => '\\Hubzero\\Database\\Drivers\\Cubrid\\CubridDriver',
            'sqlite'   => '\\Hubzero\\Database\\Drivers\\Sqlite\\SqliteDriver',
            'mock'     => '\\Hubzero\\Database\\Drivers\\Mock\\MockDriver',
            'pgsql'    => '\\Hubzero\\Database\\Drivers\\Pgsql\\PgsqlDriver',
            'firebird' => '\\Hubzero\\Database\\Drivers\\Firebird\\FirebirdDriver',
            'informix' => '\\Hubzero\\Database\\Drivers\\Informix\\InformixDriver',
            'oci'      => '\\Hubzero\\Database\\Drivers\\Oci\\OciDriver',
            'sqlsrv'   => '\\Hubzero\\Database\\Drivers\\Sqlsrv\\SqlsrvDriver',
            'db2'      => '\\Hubzero\\Database\\Drivers\\Db2\\Db2Driver',
            'ase'      => '\\Hubzero\\Database\\Drivers\\Ase\\AseDriver',
        ];
    }

    /**
     * Map of syntax names to class names.
     *
     * @return array<string, string>
     */
    public static function syntaxClassMap(): array
    {
        return [
            'mysql'    => '\\Hubzero\\Database\\Drivers\\Mysql\\MysqlSyntax',
            'mariadb'  => '\\Hubzero\\Database\\Drivers\\Mariadb\\MariadbSyntax',
            'percona'  => '\\Hubzero\\Database\\Drivers\\Percona\\PerconaSyntax',
            'cubrid'   => '\\Hubzero\\Database\\Drivers\\Cubrid\\CubridSyntax',
            'pgsql'    => '\\Hubzero\\Database\\Drivers\\Pgsql\\PgsqlSyntax',
            'sqlite'   => '\\Hubzero\\Database\\Drivers\\Sqlite\\SqliteSyntax',
            'firebird' => '\\Hubzero\\Database\\Drivers\\Firebird\\FirebirdSyntax',
            'informix' => '\\Hubzero\\Database\\Drivers\\Informix\\InformixSyntax',
            'sqlsrv'   => '\\Hubzero\\Database\\Drivers\\Sqlsrv\\SqlsrvSyntax',
            'db2'      => '\\Hubzero\\Database\\Drivers\\Db2\\Db2Syntax',
            'oci'      => '\\Hubzero\\Database\\Drivers\\Oci\\OciSyntax',
            'ase'      => '\\Hubzero\\Database\\Drivers\\Ase\\AseSyntax',
        ];
    }

    /**
     * Map of grammar names to class names.
     *
     * @return array<string, string>
     */
    public static function grammarClassMap(): array
    {
        return [
            'mysql'    => '\\Hubzero\\Database\\Drivers\\Mysql\\MysqlGrammar',
            'mariadb'  => '\\Hubzero\\Database\\Drivers\\Mariadb\\MariadbGrammar',
            'percona'  => '\\Hubzero\\Database\\Drivers\\Percona\\PerconaGrammar',
            'cubrid'   => '\\Hubzero\\Database\\Drivers\\Cubrid\\CubridGrammar',
            'sqlite'   => '\\Hubzero\\Database\\Drivers\\Sqlite\\SqliteGrammar',
            'mock'     => '\\Hubzero\\Database\\Drivers\\Mock\\MockGrammar',
            'pgsql'    => '\\Hubzero\\Database\\Drivers\\Pgsql\\PgsqlGrammar',
            'firebird' => '\\Hubzero\\Database\\Drivers\\Firebird\\FirebirdGrammar',
            'informix' => '\\Hubzero\\Database\\Drivers\\Informix\\InformixGrammar',
            'sqlsrv'   => '\\Hubzero\\Database\\Drivers\\Sqlsrv\\SqlsrvGrammar',
            'db2'      => '\\Hubzero\\Database\\Drivers\\Db2\\Db2Grammar',
            'oci'      => '\\Hubzero\\Database\\Drivers\\Oci\\OciGrammar',
            'ase'      => '\\Hubzero\\Database\\Drivers\\Ase\\AseGrammar',
        ];
    }

    /**
     * Map of syntax aliases.
     *
     * @return array<string, string>
     */
    public static function syntaxAliases(): array
    {
        return [
            'ibm' => 'db2',
        ];
    }

    /**
     * Resolve a built-in driver class by name.
     *
     * @param  string $name
     * @return string|null
     */
    public static function driverClassFor(string $name): ?string
    {
        $key = self::normalizeDriverKey($name);
        if ($key === null) {
            return null;
        }

        $map = self::driverClassMap();
        return $map[$key] ?? null;
    }

    /**
     * Resolve a canonical co-located driver class name for a backend key.
     *
     * @param  string $name
     * @return string|null
     */
    public static function canonicalDriverClassFor(string $name): ?string
    {
        $key = self::normalizeDriverKey($name);
        if ($key === null) {
            return null;
        }

        $backendMap = self::driverBackendMap();
        if (!isset($backendMap[$key])) {
            return null;
        }

        return self::canonicalBackendClassName($backendMap[$key], 'Driver');
    }

    /**
     * Resolve a syntax class by syntax name with alias support.
     *
     * @param  string $name
     * @return string|null
     */
    public static function syntaxClassFor(string $name): ?string
    {
        $key = self::normalizeSyntaxKey($name);
        if ($key === null) {
            return null;
        }

        $map = self::syntaxClassMap();
        return $map[$key] ?? null;
    }

    /**
     * Resolve a canonical co-located syntax class name for a syntax key.
     *
     * @param  string $name
     * @return string|null
     */
    public static function canonicalSyntaxClassFor(string $name): ?string
    {
        $key = self::normalizeSyntaxKey($name);
        if ($key === null) {
            return null;
        }

        $backendMap = self::syntaxBackendMap();
        if (!isset($backendMap[$key])) {
            return null;
        }

        return self::canonicalBackendClassName($backendMap[$key], 'Syntax');
    }

    /**
     * Resolve a canonical built-in driver class by backend name.
     *
     * @param  string $name
     * @return string|null
     */
    public static function resolveDriverClassFor(string $name): ?string
    {
        return self::classIfExists(self::driverClassFor($name));
    }

    /**
     * Convention candidates for custom driver class resolution.
     *
     * Order:
     * 1) Hubzero\Database\Drivers\<Name>\<Name>Driver
     *
     * @param  string $name
     * @return array<int, string>
     */
    public static function conventionDriverClassCandidates(string $name): array
    {
        $key = self::normalizeDriverKey($name);
        if ($key === null) {
            return [];
        }

        if (self::backendStudlyName($key) === '') {
            return [];
        }

        return [self::canonicalBackendClassName($key, 'Driver')];
    }

    /**
     * Resolve a canonical syntax class by syntax/backend name.
     *
     * @param  string $name
     * @return string|null
     */
    public static function resolveSyntaxClassFor(string $name): ?string
    {
        return self::classIfExists(self::syntaxClassFor($name));
    }

    /**
     * Resolve a canonical grammar class by syntax/backend name.
     *
     * @param  string $name
     * @return string|null
     */
    public static function resolveGrammarClassFor(string $name): ?string
    {
        $key = self::normalizeSyntaxKey($name);
        if ($key === null) {
            return null;
        }

        $class = self::classIfExists(self::grammarClassMap()[$key] ?? null);
        return $class;
    }

    /**
     * Convention candidates for custom syntax class resolution.
     *
     * Order:
     * 1) Hubzero\Database\Drivers\<Name>\<Name>Syntax
     *
     * @param  string $name
     * @return array<int, string>
     */
    public static function conventionSyntaxClassCandidates(string $name): array
    {
        $key = self::normalizeSyntaxKey($name);
        if ($key === null) {
            return [];
        }

        if (self::backendStudlyName($key) === '') {
            return [];
        }

        return [self::canonicalBackendClassName($key, 'Syntax')];
    }

    /**
     * Return the first existing class from a convention candidate list.
     *
     * @param  array<int, string> $candidates
     * @return string|null
     */
    public static function firstExistingClassCandidate(array $candidates): ?string
    {
        foreach ($candidates as $candidate) {
            if (!is_string($candidate)) {
                continue;
            }

            if (class_exists($candidate)) {
                return $candidate;
            }
        }

        return null;
    }

    /**
     * Normalize a syntax-like backend key and apply syntax aliases.
     *
     * @param  string $name
     * @return string|null
     */
    private static function normalizeSyntaxKey(string $name): ?string
    {
        return self::normalizeBackendKey($name, true);
    }

    /**
     * Normalize a driver-like backend key.
     *
     * @param  string $name
     * @return string|null
     */
    private static function normalizeDriverKey(string $name): ?string
    {
        return self::normalizeBackendKey($name, false);
    }

    /**
     * Return class name when it exists; otherwise null.
     *
     * @param  string|null $class
     * @return string|null
     */
    private static function classIfExists(?string $class): ?string
    {
        return ($class !== null && class_exists($class)) ? $class : null;
    }

    /**
     * Normalize a backend key and optionally apply syntax aliases.
     *
     * @param  string $name
     * @param  bool   $applySyntaxAliases
     * @return string|null
     */
    private static function normalizeBackendKey(string $name, bool $applySyntaxAliases): ?string
    {
        $key = strtolower(trim($name));
        if ($key === '') {
            return null;
        }

        if ($applySyntaxAliases) {
            $aliases = self::syntaxAliases();
            if (isset($aliases[$key])) {
                $key = $aliases[$key];
            }
        }

        return $key;
    }

    /**
     * Build canonical class name under Drivers/<Backend>/<Backend><Suffix>.
     *
     * @param  string $backend
     * @param  string $suffix
     * @return string
     */
    private static function canonicalBackendClassName(string $backend, string $suffix): string
    {
        $studly = self::backendStudlyName($backend);

        return '\\Hubzero\\Database\\Drivers\\' . $studly . '\\' . $studly . $suffix;
    }

    /**
     * Build a StudlyCase backend token for class-name generation.
     *
     * @param  string $backend
     * @return string
     */
    private static function backendStudlyName(string $backend): string
    {
        $parts = preg_split('/[^a-z0-9]+/i', $backend) ?: [];
        $parts = array_filter($parts, static fn($part) => $part !== '');

        if (empty($parts)) {
            return '';
        }

        return implode('', array_map(static fn($part) => ucfirst(strtolower($part)), $parts));
    }
}
