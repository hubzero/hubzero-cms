<?php

/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Components\Courses\Tests\Helpers;

use Hubzero\Test\Basic;
use Components\Courses\Helpers\QueryDropColumnStatement;
use Generator;
use PHPUnit\Framework\Attributes\DataProvider;

/**
 * QueryDropColumnStatement tests
 */
class QueryDropColumnStatementTest extends Basic
{
    /**
     * Data provider for toString tests
     *
     * @return  Generator
     */
    public static function seedColumnNames(): Generator
    {
        yield 'simple column name' => ['test', 'DROP COLUMN test'];
        yield 'underscore column name' => ['user_id', 'DROP COLUMN user_id'];
        yield 'camelCase column name' => ['createdAt', 'DROP COLUMN createdAt'];
        yield 'numeric suffix' => ['field1', 'DROP COLUMN field1'];
    }

    #[DataProvider('seedColumnNames')]
    public function testToStringReturnsCorrectSql(string $name, string $expected): void
    {
        $statement = new QueryDropColumnStatement(['name' => $name]);

        $this->assertEquals($expected, $statement->toString());
    }
}
