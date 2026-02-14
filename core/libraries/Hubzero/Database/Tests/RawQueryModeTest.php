<?php

/**
 * @package    framework
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Hubzero\Database\Tests;

use Hubzero\Database\Driver;
use Hubzero\Database\Query;
use Hubzero\Test\ExternalCallerHelper;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\Test;

/**
 * Tests for the raw query mode feature on Driver
 *
 * Verifies that setQuery() can be configured to log or reject
 * raw SQL calls from outside the Database framework.
 */
class RawQueryModeTest extends AbstractDriverTestCase
{
    /**
     * @return array
     */
    protected static function getTestTables(): array
    {
        return ['rqm_test'];
    }

    /**
     * @param Driver $driver
     */
    protected static function setUpDatabase(Driver $driver): void
    {
        $schema = $driver->schema();

        try {
            $connection = $driver->getConnection();
            if ($connection && $connection->inTransaction()) {
                $connection->commit();
            }
        } catch (\Exception $e) {
            // Ignore
        }

        if ($driver->tableExists('rqm_test')) {
            $query = new Query($driver);
            try {
                $query->delete('rqm_test')->execute();
            } catch (\Exception $e) {
                $driver->dropTable('rqm_test', true);
            }
        }

        if (!$driver->tableExists('rqm_test')) {
            $schema->table('rqm_test')->create()
                ->id('id')
                ->string('name', 255)
                ->execute();
        }

        $query = new Query($driver);
        $query->insertMany('rqm_test', [
            ['name' => 'Alice'],
            ['name' => 'Bob'],
        ]);
    }

    // -------------------------------------------------------------
    // Getter / Setter tests
    // -------------------------------------------------------------

    #[Test]
    #[DataProvider('databaseProvider')]
    public function testDefaultModeIsPermissive(
        string $dbName,
        Driver $driver
    ): void {
        $this->assertSame(
            'permissive',
            $driver->getRawQueryMode()
        );
    }

    #[Test]
    #[DataProvider('databaseProvider')]
    public function testSetAndGetMode(
        string $dbName,
        Driver $driver
    ): void {
        $original = $driver->getRawQueryMode();

        $driver->setRawQueryMode('log');
        $this->assertSame('log', $driver->getRawQueryMode());

        $driver->setRawQueryMode('strict');
        $this->assertSame(
            'strict',
            $driver->getRawQueryMode()
        );

        $driver->setRawQueryMode('permissive');
        $this->assertSame(
            'permissive',
            $driver->getRawQueryMode()
        );

        $driver->setRawQueryMode($original);
    }

    #[Test]
    #[DataProvider('databaseProvider')]
    public function testSetModeReturnsSelf(
        string $dbName,
        Driver $driver
    ): void {
        $original = $driver->getRawQueryMode();

        $result = $driver->setRawQueryMode('permissive');
        $this->assertSame($driver, $result);

        $driver->setRawQueryMode($original);
    }

    #[Test]
    #[DataProvider('databaseProvider')]
    public function testInvalidModeThrows(
        string $dbName,
        Driver $driver
    ): void {
        $this->expectException(
            \InvalidArgumentException::class
        );
        $this->expectExceptionMessage(
            "Invalid raw query mode 'invalid'"
        );

        $driver->setRawQueryMode('invalid');
    }

    // -------------------------------------------------------------
    // Permissive mode (default behavior)
    // -------------------------------------------------------------

    #[Test]
    #[DataProvider('databaseProvider')]
    public function testPermissiveModeAllowsSetQuery(
        string $dbName,
        Driver $driver
    ): void {
        $driver->setRawQueryMode('permissive');

        $driver->setQuery(
            'SELECT COUNT(*) FROM '
            . $driver->quoteName('rqm_test')
        );
        $count = $driver->loadResult();

        $this->assertEquals(2, $count);
    }

    // -------------------------------------------------------------
    // Internal framework calls pass in all modes
    // -------------------------------------------------------------

    #[Test]
    #[DataProvider('databaseProvider')]
    public function testStrictModeAllowsQueryBuilder(
        string $dbName,
        Driver $driver
    ): void {
        $original = $driver->getRawQueryMode();
        $driver->setRawQueryMode('strict');

        // Query builder calls setQuery() internally
        $query = new Query($driver);
        $results = $query->select('*')
            ->from('rqm_test')
            ->order('id', 'asc')
            ->fetch('rows');

        $this->assertCount(2, $results);
        $this->assertEquals(
            'Alice',
            $results[0]->name
        );

        $driver->setRawQueryMode($original);
    }

    #[Test]
    #[DataProvider('databaseProvider')]
    public function testStrictModeAllowsInsertViaQueryBuilder(
        string $dbName,
        Driver $driver
    ): void {
        $original = $driver->getRawQueryMode();
        $driver->setRawQueryMode('strict');

        $query = new Query($driver);
        $query->push(
            'rqm_test',
            ['name' => 'Charlie']
        );

        $query2 = new Query($driver);
        $count = $query2->select('COUNT(*)', 'cnt')
            ->from('rqm_test')
            ->fetch('rows');

        $this->assertGreaterThanOrEqual(
            3,
            (int) $count[0]->cnt
        );

        $driver->setRawQueryMode($original);
    }

    #[Test]
    #[DataProvider('databaseProvider')]
    public function testLogModeAllowsQueryBuilder(
        string $dbName,
        Driver $driver
    ): void {
        $original = $driver->getRawQueryMode();
        $logger = new SpyLogger();
        $driver->setLogger($logger);
        $driver->setRawQueryMode('log');

        $query = new Query($driver);
        $results = $query->select('*')
            ->from('rqm_test')
            ->fetch('rows');

        $this->assertCount(
            0,
            $logger->notices,
            sprintf(
                '[%s] Query builder calls should not '
                . 'be logged, but got %d notice(s)',
                $dbName,
                count($logger->notices)
            )
        );

        $driver->setRawQueryMode($original);
    }

    // -------------------------------------------------------------
    // Log mode - calls from Database namespace are internal
    // -------------------------------------------------------------

    #[Test]
    #[DataProvider('databaseProvider')]
    public function testLogModeSkipsInternalCalls(
        string $dbName,
        Driver $driver
    ): void {
        $original = $driver->getRawQueryMode();
        $logger = new SpyLogger();
        $driver->setLogger($logger);
        $driver->setRawQueryMode('log');

        // This test class is in Hubzero\Database\Tests
        // namespace — so it's internal
        $driver->setQuery(
            $this->simpleSelectSql($driver)
        );

        $this->assertCount(
            0,
            $logger->notices,
            sprintf(
                '[%s] Calls from Hubzero\\Database\\ '
                . 'namespace should not be logged',
                $dbName
            )
        );

        $driver->setRawQueryMode($original);
    }

    #[Test]
    #[DataProvider('databaseProvider')]
    public function testStrictModeSkipsInternalCalls(
        string $dbName,
        Driver $driver
    ): void {
        $original = $driver->getRawQueryMode();
        $driver->setRawQueryMode('strict');

        // This test class is in Hubzero\Database\Tests
        $driver->setQuery(
            $this->simpleSelectSql($driver)
        );
        $result = $driver->loadResult();

        $this->assertEquals(1, (int) $result);

        $driver->setRawQueryMode($original);
    }

    // -------------------------------------------------------------
    // External caller simulation
    //
    // Since this test class is in the Hubzero\Database\
    // namespace, direct calls from test methods are treated
    // as internal. To test external caller detection, we use
    // a helper defined OUTSIDE the Hubzero\Database namespace
    // (see ExternalCallerHelper.php).
    // -------------------------------------------------------------

    #[Test]
    #[DataProvider('databaseProvider')]
    public function testLogModeLogsExternalCalls(
        string $dbName,
        Driver $driver
    ): void {
        $original = $driver->getRawQueryMode();
        $logger = new SpyLogger();
        $driver->setLogger($logger);
        $driver->setRawQueryMode('log');

        $sql = $this->simpleSelectSql($driver);

        $helper = new ExternalCallerHelper();
        $helper->callSetQuery($driver, $sql);

        $this->assertCount(
            1,
            $logger->notices,
            sprintf(
                '[%s] External call should be logged',
                $dbName
            )
        );

        // Verify context contains useful caller info
        $context = $logger->notices[0]['context'];
        $this->assertArrayHasKey('file', $context);
        $this->assertArrayHasKey('line', $context);
        $this->assertArrayHasKey(
            'sql_preview',
            $context
        );
        $this->assertStringContainsString(
            'SELECT',
            $context['sql_preview']
        );

        $driver->setRawQueryMode($original);
    }

    #[Test]
    #[DataProvider('databaseProvider')]
    public function testStrictModeThrowsForExternalCalls(
        string $dbName,
        Driver $driver
    ): void {
        $original = $driver->getRawQueryMode();
        $driver->setRawQueryMode('strict');

        $sql = $this->simpleSelectSql($driver);

        $this->expectException(\RuntimeException::class);
        $this->expectExceptionMessage('strict mode');

        try {
            $helper = new ExternalCallerHelper();
            $helper->callSetQuery($driver, $sql);
        } finally {
            $driver->setRawQueryMode($original);
        }
    }

    #[Test]
    #[DataProvider('databaseProvider')]
    public function testPermissiveModeDoesNotLogExternalCalls(
        string $dbName,
        Driver $driver
    ): void {
        $logger = new SpyLogger();
        $driver->setLogger($logger);
        $driver->setRawQueryMode('permissive');

        $sql = $this->simpleSelectSql($driver);

        $helper = new ExternalCallerHelper();
        $helper->callSetQuery($driver, $sql);

        $this->assertCount(
            0,
            $logger->notices,
            sprintf(
                '[%s] Permissive mode should not log',
                $dbName
            )
        );
    }

    // -------------------------------------------------------------
    // Constructor option
    // -------------------------------------------------------------

    #[Test]
    public function testConstructorOption(): void
    {
        $backends = static::databaseProvider();
        if (empty($backends)) {
            $this->markTestSkipped(
                'No database backends configured'
            );
        }

        $first = reset($backends);
        $driver = $first[1];
        $original = $driver->getRawQueryMode();

        $driver->setRawQueryMode('log');
        $this->assertSame(
            'log',
            $driver->getRawQueryMode()
        );

        $driver->setRawQueryMode($original);
    }

    // -------------------------------------------------------------
    // SQL preview truncation
    // -------------------------------------------------------------

    #[Test]
    #[DataProvider('databaseProvider')]
    public function testLogModeTruncatesSqlPreview(
        string $dbName,
        Driver $driver
    ): void {
        $original = $driver->getRawQueryMode();
        $logger = new SpyLogger();
        $driver->setLogger($logger);
        $driver->setRawQueryMode('log');

        // Build a SQL string longer than 200 chars
        $longSql = 'SELECT '
            . str_repeat('x', 300)
            . ' FROM '
            . $driver->quoteName('rqm_test');

        $helper = new ExternalCallerHelper();
        try {
            $helper->callSetQuery($driver, $longSql);
        } catch (\Exception $e) {
            // setQuery may fail on prepare — that's
            // fine, we're testing the log output
        }

        $this->assertCount(1, $logger->notices);
        $preview = $logger->notices[0]['context']
            ['sql_preview'];
        $this->assertLessThanOrEqual(
            200,
            strlen($preview)
        );

        $driver->setRawQueryMode($original);
    }

    // -------------------------------------------------------------
    // Helpers
    // -------------------------------------------------------------

    /**
     * Build a simple SELECT that works on any backend.
     */
    private function simpleSelectSql(
        Driver $driver
    ): string {
        return 'SELECT 1 FROM '
            . $driver->quoteName('rqm_test')
            . ' WHERE '
            . $driver->quoteName('id')
            . ' = 1';
    }
}
