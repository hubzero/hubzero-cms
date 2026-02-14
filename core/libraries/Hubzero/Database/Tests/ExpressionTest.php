<?php

/**
 * @package    framework
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Hubzero\Database\Tests;

use PHPUnit\Framework\TestCase;
use Hubzero\Database\Expression;

/**
 * Expression builder test
 *
 * Tests the Expression class for building database-agnostic SQL expressions.
 */
class ExpressionTest extends TestCase
{
    // =========================================================================
    // Factory Method Tests
    // =========================================================================

    /**
     * Test creating a raw expression
     *
     * @return void
     */
    public function testRawExpression()
    {
        $expr = Expression::raw('1 + 1');

        $this->assertEquals(Expression::TYPE_RAW, $expr->getType());
        $this->assertEquals(['1 + 1'], $expr->getArguments());
    }

    /**
     * Test creating a hex literal expression
     *
     * @return void
     */
    public function testHexLiteralExpression()
    {
        $expr = Expression::hexLiteral('c2ad');

        $this->assertEquals(Expression::TYPE_RAW, $expr->getType());
        $this->assertEquals(['0xc2ad'], $expr->getArguments());
    }

    /**
     * Test creating a hex literal expression strips prefix
     *
     * @return void
     */
    public function testHexLiteralExpressionStripsPrefix()
    {
        $expr = Expression::hexLiteral('0xC2AD');

        $this->assertEquals(Expression::TYPE_RAW, $expr->getType());
        $this->assertEquals(['0xc2ad'], $expr->getArguments());
    }

    /**
     * Test hex literal expression rejects invalid values
     *
     * @return void
     */
    public function testHexLiteralExpressionRejectsInvalid()
    {
        $this->expectException(\InvalidArgumentException::class);
        Expression::hexLiteral('zz');
    }

    /**
     * Test creating a subquery expression from SQL
     *
     * @return void
     */
    public function testSubqueryExpressionFromString()
    {
        $expr = Expression::subquery('select 1');

        $this->assertEquals(Expression::TYPE_RAW, $expr->getType());
        $this->assertEquals(['(select 1)'], $expr->getArguments());
    }

    /**
     * Test subquery expression rejects empty SQL
     *
     * @return void
     */
    public function testSubqueryExpressionRejectsEmpty()
    {
        $this->expectException(\InvalidArgumentException::class);
        Expression::subquery('   ');
    }

    /**
     * Test creating a column expression
     *
     * @return void
     */
    public function testColumnExpression()
    {
        $expr = Expression::column('users.name');

        $this->assertEquals(Expression::TYPE_COLUMN, $expr->getType());
        $this->assertEquals(['users.name'], $expr->getArguments());
    }

    /**
     * Test creating a literal expression
     *
     * @return void
     */
    public function testLiteralExpression()
    {
        $expr = Expression::literal('hello world');

        $this->assertEquals(Expression::TYPE_LITERAL, $expr->getType());
        $this->assertEquals(['hello world'], $expr->getArguments());
        $this->assertEquals(['hello world'], $expr->getBindings());
    }

    // =========================================================================
    // Aggregate Function Tests
    // =========================================================================

    /**
     * Test SUM expression
     *
     * @return void
     */
    public function testSumExpression()
    {
        $expr = Expression::sum('amount');

        $this->assertEquals(Expression::TYPE_FUNCTION, $expr->getType());
        $this->assertEquals('SUM', $expr->getFunction());
        $this->assertEquals(['amount'], $expr->getArguments());
    }

    /**
     * Test COUNT expression
     *
     * @return void
     */
    public function testCountExpression()
    {
        $expr = Expression::count();

        $this->assertEquals('COUNT', $expr->getFunction());
        $this->assertEquals(['*'], $expr->getArguments());
    }

    /**
     * Test COUNT with specific column
     *
     * @return void
     */
    public function testCountColumnExpression()
    {
        $expr = Expression::count('id');

        $this->assertEquals('COUNT', $expr->getFunction());
        $this->assertEquals(['id'], $expr->getArguments());
    }

    /**
     * Test COUNT DISTINCT expression
     *
     * @return void
     */
    public function testCountDistinctExpression()
    {
        $expr = Expression::countDistinct('user_id');

        $this->assertEquals('COUNT_DISTINCT', $expr->getFunction());
        $this->assertEquals(['user_id'], $expr->getArguments());
    }

    /**
     * Test CURRENT_TIMESTAMP expression
     *
     * @return void
     */
    public function testCurrentTimestampExpression()
    {
        $expr = Expression::currentTimestamp();

        $this->assertEquals(Expression::TYPE_FUNCTION, $expr->getType());
        $this->assertEquals('CURRENT_TIMESTAMP', $expr->getFunction());
        $this->assertEquals([], $expr->getArguments());
    }

    /**
     * Test percent expression
     *
     * @return void
     */
    public function testPercentExpression()
    {
        $expr = Expression::percent(Expression::count('pfa.id'), Expression::count('pfr2.id'));

        $this->assertEquals(Expression::TYPE_OPERATOR, $expr->getType());
        $this->assertEquals('/', $expr->getFunction());

        $args = $expr->getArguments();
        $this->assertCount(2, $args);
        $this->assertInstanceOf(Expression::class, $args[0]);
        $this->assertInstanceOf(Expression::class, $args[1]);
    }

    /**
     * Test AVG expression
     *
     * @return void
     */
    public function testAvgExpression()
    {
        $expr = Expression::avg('price');

        $this->assertEquals('AVG', $expr->getFunction());
        $this->assertEquals(['price'], $expr->getArguments());
    }

    /**
     * Test MIN expression
     *
     * @return void
     */
    public function testMinExpression()
    {
        $expr = Expression::min('created_at');

        $this->assertEquals('MIN', $expr->getFunction());
        $this->assertEquals(['created_at'], $expr->getArguments());
    }

    /**
     * Test MAX expression
     *
     * @return void
     */
    public function testMaxExpression()
    {
        $expr = Expression::max('updated_at');

        $this->assertEquals('MAX', $expr->getFunction());
        $this->assertEquals(['updated_at'], $expr->getArguments());
    }

    // =========================================================================
    // String Function Tests
    // =========================================================================

    /**
     * Test CONCAT expression
     *
     * @return void
     */
    public function testConcatExpression()
    {
        $expr = Expression::concat('first_name', Expression::literal(' '), 'last_name');

        $this->assertEquals('CONCAT', $expr->getFunction());
        $this->assertCount(3, $expr->getArguments());
    }

    /**
     * Test UPPER expression
     *
     * @return void
     */
    public function testUpperExpression()
    {
        $expr = Expression::upper('name');

        $this->assertEquals('UPPER', $expr->getFunction());
        $this->assertEquals(['name'], $expr->getArguments());
    }

    /**
     * Test LOWER expression
     *
     * @return void
     */
    public function testLowerExpression()
    {
        $expr = Expression::lower('email');

        $this->assertEquals('LOWER', $expr->getFunction());
        $this->assertEquals(['email'], $expr->getArguments());
    }

    /**
     * Test LENGTH expression
     *
     * @return void
     */
    public function testLengthExpression()
    {
        $expr = Expression::length('name');

        $this->assertEquals('LENGTH', $expr->getFunction());
        $this->assertEquals(['name'], $expr->getArguments());
    }

    /**
     * Test TRIM expression
     *
     * @return void
     */
    public function testTrimExpression()
    {
        $expr = Expression::trim('input');

        $this->assertEquals('TRIM', $expr->getFunction());
        $this->assertEquals(['input'], $expr->getArguments());
    }

    /**
     * Test SUBSTRING expression
     *
     * @return void
     */
    public function testSubstringExpression()
    {
        $expr = Expression::substring('title', 1, 10);

        $this->assertEquals('SUBSTRING', $expr->getFunction());
        $this->assertEquals(['title', 1, 10], $expr->getArguments());
    }

    /**
     * Test REPLACE expression
     *
     * @return void
     */
    public function testReplaceExpression()
    {
        $expr = Expression::replace('content', 'old', 'new');

        $this->assertEquals('REPLACE', $expr->getFunction());
        $this->assertCount(3, $expr->getArguments());
        // Check bindings for search and replace strings
        $this->assertContains('old', $expr->getBindings());
        $this->assertContains('new', $expr->getBindings());
    }

    // =========================================================================
    // Conditional Function Tests
    // =========================================================================

    /**
     * Test COALESCE expression
     *
     * @return void
     */
    public function testCoalesceExpression()
    {
        $expr = Expression::coalesce('nickname', 'name', Expression::literal('Anonymous'));

        $this->assertEquals('COALESCE', $expr->getFunction());
        $this->assertCount(3, $expr->getArguments());
    }

    /**
     * Test IFNULL expression
     *
     * @return void
     */
    public function testIfNullExpression()
    {
        $expr = Expression::ifNull('deleted_at', Expression::literal('active'));

        $this->assertEquals('IFNULL', $expr->getFunction());
        $this->assertCount(2, $expr->getArguments());
    }

    /**
     * Test NULLIF expression
     *
     * @return void
     */
    public function testNullIfExpression()
    {
        $expr = Expression::nullIf('value', 0);

        $this->assertEquals('NULLIF', $expr->getFunction());
        $this->assertCount(2, $expr->getArguments());
    }

    /**
     * Test CASE expression
     *
     * @return void
     */
    public function testCaseExpression()
    {
        $expr = Expression::caseWhen('status', [
            'active' => 'Active',
            'inactive' => 'Inactive'
        ], 'Unknown');

        $this->assertEquals('CASE', $expr->getFunction());
        $this->assertCount(3, $expr->getArguments());
    }

    // =========================================================================
    // Math Function Tests
    // =========================================================================

    /**
     * Test ROUND expression
     *
     * @return void
     */
    public function testRoundExpression()
    {
        $expr = Expression::round('price', 2);

        $this->assertEquals('ROUND', $expr->getFunction());
        $this->assertEquals(['price', 2], $expr->getArguments());
    }

    /**
     * Test FLOOR expression
     *
     * @return void
     */
    public function testFloorExpression()
    {
        $expr = Expression::floor('rating');

        $this->assertEquals('FLOOR', $expr->getFunction());
        $this->assertEquals(['rating'], $expr->getArguments());
    }

    /**
     * Test CEIL expression
     *
     * @return void
     */
    public function testCeilExpression()
    {
        $expr = Expression::ceil('value');

        $this->assertEquals('CEIL', $expr->getFunction());
        $this->assertEquals(['value'], $expr->getArguments());
    }

    /**
     * Test ABS expression
     *
     * @return void
     */
    public function testAbsExpression()
    {
        $expr = Expression::abs('difference');

        $this->assertEquals('ABS', $expr->getFunction());
        $this->assertEquals(['difference'], $expr->getArguments());
    }

    /**
     * Test random ORDER BY expression
     *
     * @return void
     */
    public function testRandomOrderExpression()
    {
        $expr = Expression::randomOrder();

        $this->assertEquals(Expression::TYPE_FUNCTION, $expr->getType());
        $this->assertEquals('RANDOM_ORDER', $expr->getFunction());
        $this->assertSame([], $expr->getArguments());
    }

    // =========================================================================
    // Date/Time Function Tests
    // =========================================================================

    /**
     * Test NOW expression
     *
     * @return void
     */
    public function testNowExpression()
    {
        $expr = Expression::now();

        $this->assertEquals('NOW', $expr->getFunction());
        $this->assertEmpty($expr->getArguments());
    }

    /**
     * Test CURRENT_DATE expression
     *
     * @return void
     */
    public function testCurrentDateExpression()
    {
        $expr = Expression::currentDate();

        $this->assertEquals('CURRENT_DATE', $expr->getFunction());
    }

    /**
     * Test CURRENT_TIME expression
     *
     * @return void
     */
    public function testCurrentTimeExpression()
    {
        $expr = Expression::currentTime();

        $this->assertEquals('CURRENT_TIME', $expr->getFunction());
    }

    /**
     * Test DATE expression
     *
     * @return void
     */
    public function testDateExpression()
    {
        $expr = Expression::date('created_at');

        $this->assertEquals('DATE', $expr->getFunction());
        $this->assertEquals(['created_at'], $expr->getArguments());
    }

    /**
     * Test TIME expression
     *
     * @return void
     */
    public function testTimeExpression()
    {
        $expr = Expression::time('event_time');

        $this->assertEquals('TIME', $expr->getFunction());
        $this->assertEquals(['event_time'], $expr->getArguments());
    }

    /**
     * Test YEAR expression
     *
     * @return void
     */
    public function testYearExpression()
    {
        $expr = Expression::year('birthday');

        $this->assertEquals('YEAR', $expr->getFunction());
        $this->assertEquals(['birthday'], $expr->getArguments());
    }

    /**
     * Test MONTH expression
     *
     * @return void
     */
    public function testMonthExpression()
    {
        $expr = Expression::month('created_at');

        $this->assertEquals('MONTH', $expr->getFunction());
    }

    /**
     * Test DAY expression
     *
     * @return void
     */
    public function testDayExpression()
    {
        $expr = Expression::day('created_at');

        $this->assertEquals('DAY', $expr->getFunction());
    }

    /**
     * Test HOUR expression
     *
     * @return void
     */
    public function testHourExpression()
    {
        $expr = Expression::hour('event_time');

        $this->assertEquals('HOUR', $expr->getFunction());
    }

    /**
     * Test MINUTE expression
     *
     * @return void
     */
    public function testMinuteExpression()
    {
        $expr = Expression::minute('event_time');

        $this->assertEquals('MINUTE', $expr->getFunction());
    }

    /**
     * Test SECOND expression
     *
     * @return void
     */
    public function testSecondExpression()
    {
        $expr = Expression::second('event_time');

        $this->assertEquals('SECOND', $expr->getFunction());
    }

    /**
     * Test DATE_FORMAT expression
     *
     * @return void
     */
    public function testDateFormatExpression()
    {
        $expr = Expression::dateFormat('created_at', '%Y-%m-%d');

        $this->assertEquals('DATE_FORMAT', $expr->getFunction());
        $this->assertCount(2, $expr->getArguments());
    }

    // =========================================================================
    // Alias Tests
    // =========================================================================

    /**
     * Test setting an alias
     *
     * @return void
     */
    public function testExpressionAlias()
    {
        $expr = Expression::sum('amount')->as('total_amount');

        $this->assertEquals('total_amount', $expr->getAlias());
    }

    /**
     * Test that alias returns a clone (immutability)
     *
     * @return void
     */
    public function testAliasReturnsClone()
    {
        $original = Expression::count();
        $aliased = $original->as('total');

        $this->assertNull($original->getAlias());
        $this->assertEquals('total', $aliased->getAlias());
        $this->assertNotSame($original, $aliased);
    }

    // =========================================================================
    // Nested Expression Tests
    // =========================================================================

    /**
     * Test nesting expressions
     *
     * @return void
     */
    public function testNestedExpressions()
    {
        // UPPER(COALESCE(nickname, name))
        $expr = Expression::upper(Expression::coalesce('nickname', 'name'));

        $this->assertEquals('UPPER', $expr->getFunction());
        $this->assertInstanceOf(Expression::class, $expr->getArguments()[0]);
    }

    /**
     * Test collecting bindings from nested expressions
     *
     * @return void
     */
    public function testNestedExpressionBindings()
    {
        // CONCAT(first_name, ' ', COALESCE(nickname, 'Unknown'))
        $expr = Expression::concat(
            'first_name',
            Expression::literal(' '),
            Expression::coalesce('nickname', Expression::literal('Unknown'))
        );

        $bindings = $expr->getBindings();

        // Should have ' ' from the first literal and 'Unknown' from the nested one
        $this->assertContains(' ', $bindings);
        $this->assertContains('Unknown', $bindings);
    }

    // =========================================================================
    // toString Tests
    // =========================================================================

    /**
     * Test string casting for debugging
     *
     * @return void
     */
    public function testToString()
    {
        $expr = Expression::sum('amount')->as('total');

        $string = (string) $expr;

        $this->assertStringContainsString('Expression', $string);
        $this->assertStringContainsString('function', $string);
        $this->assertStringContainsString('SUM', $string);
        $this->assertStringContainsString('total', $string);
    }
}
