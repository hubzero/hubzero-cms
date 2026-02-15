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
     * Map of built-in driver names to class names.
     *
     * @return array<string, string>
     */
    public static function driverClassMap(): array
    {
        return [
            'mysql'    => Driver\Mysql::class,
            'mariadb'  => Driver\Mariadb::class,
            'percona'  => Driver\Mysql::class,
            'cubrid'   => Driver\Cubrid::class,
            'sqlite'   => Driver\Sqlite::class,
            'mock'     => Driver\Mock::class,
            'pgsql'    => Driver\Pgsql::class,
            'firebird' => Driver\Firebird::class,
            'informix' => Driver\Informix::class,
            'oci'      => Driver\Oci::class,
            'sqlsrv'   => Driver\Sqlsrv::class,
            'db2'      => Driver\Db2::class,
            'ase'      => Driver\Ase::class,
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
            'mysql'    => '\\Hubzero\\Database\\Syntax\\Mysql',
            'mariadb'  => '\\Hubzero\\Database\\Syntax\\Mariadb',
            'percona'  => '\\Hubzero\\Database\\Syntax\\Percona',
            'cubrid'   => '\\Hubzero\\Database\\Syntax\\Cubrid',
            'pgsql'    => '\\Hubzero\\Database\\Syntax\\Pgsql',
            'sqlite'   => '\\Hubzero\\Database\\Syntax\\Sqlite',
            'firebird' => '\\Hubzero\\Database\\Syntax\\Firebird',
            'informix' => '\\Hubzero\\Database\\Syntax\\Informix',
            'sqlsrv'   => '\\Hubzero\\Database\\Syntax\\Sqlsrv',
            'db2'      => '\\Hubzero\\Database\\Syntax\\Db2',
            'oci'      => '\\Hubzero\\Database\\Syntax\\Oci',
            'ase'      => '\\Hubzero\\Database\\Syntax\\Ase',
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
        $key = strtolower(trim($name));
        if ($key === '') {
            return null;
        }

        $map = self::driverClassMap();
        return $map[$key] ?? null;
    }

    /**
     * Resolve a syntax class by syntax name with alias support.
     *
     * @param  string $name
     * @return string|null
     */
    public static function syntaxClassFor(string $name): ?string
    {
        $key = strtolower(trim($name));
        if ($key === '') {
            return null;
        }

        $aliases = self::syntaxAliases();
        if (isset($aliases[$key])) {
            $key = $aliases[$key];
        }

        $map = self::syntaxClassMap();
        return $map[$key] ?? null;
    }
}
