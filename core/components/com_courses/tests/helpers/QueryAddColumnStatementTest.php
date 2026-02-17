<?php

/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Components\Courses\Tests\Helpers;

use Hubzero\Test\Basic;
use Components\Courses\Helpers\QueryAddColumnStatement;
use Generator;
use PHPUnit\Framework\Attributes\DataProvider;

/**
 * QueryAddColumnStatement tests
 */
class QueryAddColumnStatementTest extends Basic
{
    /**
     * Data provider for toString tests
     *
     * @return  Generator
     */
    public static function seedColumnData(): Generator
    {
        yield 'name and type only' => [
            'columnData' => [
                'name' => 'test',
                'type' => 'varchar(255)',
            ],
            'expected' => 'ADD COLUMN test varchar(255)',
        ];

        yield 'with NOT NULL restriction' => [
            'columnData' => [
                'name' => 'test',
                'type' => 'varchar(255)',
                'restriction' => 'NOT NULL',
            ],
            'expected' => 'ADD COLUMN test varchar(255) NOT NULL',
        ];

        yield 'with string default' => [
            'columnData' => [
                'name' => 'test',
                'type' => 'varchar(255)',
                'default' => "'foo'",
            ],
            'expected' => "ADD COLUMN test varchar(255) DEFAULT 'foo'",
        ];

        yield 'with restriction and string default' => [
            'columnData' => [
                'name' => 'test',
                'type' => 'varchar(255)',
                'restriction' => 'NOT NULL',
                'default' => "'foo'",
            ],
            'expected' => "ADD COLUMN test varchar(255) NOT NULL DEFAULT 'foo'",
        ];

        yield 'with restriction and zero default' => [
            'columnData' => [
                'name' => 'test',
                'type' => 'varchar(255)',
                'restriction' => 'NOT NULL',
                'default' => 0,
            ],
            'expected' => 'ADD COLUMN test varchar(255) NOT NULL DEFAULT 0',
        ];

        yield 'integer column type' => [
            'columnData' => [
                'name' => 'count',
                'type' => 'int(11)',
            ],
            'expected' => 'ADD COLUMN count int(11)',
        ];

        yield 'text column with nullable' => [
            'columnData' => [
                'name' => 'description',
                'type' => 'text',
                'restriction' => 'NULL',
            ],
            'expected' => 'ADD COLUMN description text NULL',
        ];
    }

    #[DataProvider('seedColumnData')]
    public function testToStringReturnsCorrectSql(array $columnData, string $expected): void
    {
        $statement = new QueryAddColumnStatement($columnData);

        $this->assertEquals($expected, $statement->toString());
    }
}
