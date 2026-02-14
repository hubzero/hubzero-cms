<?php

/**
 * @package    framework
 * @copyright  Copyright (c) 2005-2026 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Hubzero\Database\Tests;

use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\Test;
use Hubzero\Database\Driver;
use Hubzero\Database\Schema\AlterTableBuilder;
use Hubzero\Database\Schema\Grammar;
use Hubzero\Database\Schema\TableDefinition;

/**
 * Unit tests for schema grammar base behavior.
 */
class SchemaGrammarTest extends AbstractDriverTestCase
{
    protected static function getTestTables(): array
    {
        return [];
    }

    protected static function setUpDatabase(Driver $driver): void
    {
        // No persistent database fixtures required for these grammar contract tests.
    }

    /**
     * compileCreateTableFromDefinition() defaults to passthrough stub.
     */
    #[Test]
    #[DataProvider('databaseProvider')]
    public function compileCreateTableFromDefinitionIsPassthroughByDefault(string $dbName, Driver $driver): void
    {
        $grammar = new class ($driver) extends Grammar {
            public function compileCreate(TableDefinition $blueprint): array
            {
                return [];
            }

            public function compileInlineIndex(string $name, array $columns): ?string
            {
                return null;
            }

            public function compileInlineUniqueIndex(string $name, array $columns): ?string
            {
                return null;
            }

            public function compileInlineFulltextIndex(string $name, array $columns): ?string
            {
                return null;
            }

            public function compileAlterAdd(TableDefinition $blueprint): array
            {
                return [];
            }

            public function compileAlterModify(TableDefinition $blueprint): array
            {
                return [];
            }

            public function compileAlterTable(AlterTableBuilder $builder): array
            {
                return [];
            }

            protected function getColumnType(\Hubzero\Database\Schema\Column $column): string
            {
                return 'TEXT';
            }
        };

        $definition = [
            'table' => 'sg_items',
            'ifNotExists' => true,
            'options' => [
                'engine' => 'InnoDB',
                'charset' => 'utf8',
                'collation' => 'utf8_general_ci',
            ],
            'columns' => [
                'id' => [
                    'type' => 'INT(11) UNSIGNED',
                    'modifiers' => ['autoIncrement' => true, 'nullable' => false],
                ],
            ],
            'primaryKey' => ['id'],
            'indexes' => [],
            'uniqueIndexes' => [],
            'fulltextIndexes' => [],
            'foreignKeys' => [],
        ];

        $this->assertSame($definition, $grammar->compileCreateTableFromDefinition($definition));
    }

    /**
     * The default configured grammar override returns concrete SQL statements.
     */
    #[Test]
    #[DataProvider('databaseProvider')]
    public function compileCreateTableFromDefinitionConfiguredOverrideReturnsSqlList(
        string $dbName,
        Driver $driver
    ): void {
        $grammar = $driver->getSchemaGrammar();

        $definition = [
            'table' => 'sg_items',
            'ifNotExists' => true,
            'options' => [
                'engine' => 'InnoDB',
                'charset' => 'utf8',
                'collation' => 'utf8_general_ci',
            ],
            'columns' => [
                'id' => [
                    'type' => 'INT(11) UNSIGNED',
                    'modifiers' => ['autoIncrement' => true, 'nullable' => false],
                ],
            ],
            'primaryKey' => ['id'],
            'indexes' => [],
            'uniqueIndexes' => [],
            'fulltextIndexes' => [],
            'foreignKeys' => [],
        ];

        $compiled = $grammar->compileCreateTableFromDefinition($definition);

        $this->assertIsArray($compiled);
        $this->assertNotEmpty($compiled);
        $this->assertIsString($compiled[0]);
        $this->assertStringStartsWith('CREATE TABLE', $compiled[0]);
    }

    /**
     * All concrete schema grammars should override and return SQL-list shape.
     */
    #[Test]
    #[DataProvider('databaseProvider')]
    public function concreteGrammarsOverrideCreateFromDefinitionAndReturnSqlList(string $dbName, Driver $driver): void
    {
        $classes = [
            get_class($driver->getSchemaGrammar()),
        ];

        $definition = [
            'table' => 'sg_cov_items',
            'ifNotExists' => true,
            'options' => [
                'engine' => 'InnoDB',
                'charset' => 'utf8',
                'collation' => 'utf8_general_ci',
            ],
            'columns' => [
                'id' => [
                    'type' => 'INT(11) UNSIGNED',
                    'modifiers' => ['autoIncrement' => true, 'nullable' => false],
                ],
            ],
            'primaryKey' => ['id'],
            'indexes' => [],
            'uniqueIndexes' => [],
            'fulltextIndexes' => [],
            'foreignKeys' => [],
        ];

        foreach ($classes as $class) {
            $method = new \ReflectionMethod($class, 'compileCreateTableFromDefinition');
            $this->assertSame($class, $method->getDeclaringClass()->getName(), $class);

            $grammar = new $class($driver);
            $compiled = $grammar->compileCreateTableFromDefinition($definition);

            $this->assertNotEmpty($compiled, $class);
            $this->assertTrue(array_is_list($compiled), $class);
            $this->assertIsString($compiled[0], $class);
            $this->assertStringStartsWith('CREATE TABLE', $compiled[0], $class);
        }
    }
}
