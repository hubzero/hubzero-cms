<?php

/**
 * @package    framework
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Hubzero\Database\Tests;

use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\Attributes\DataProvider;
use Hubzero\Database\Driver;
use Hubzero\Database\Query;

/**
 * Tests for database transactions
 *
 * Tests transaction callbacks, nested transactions (savepoints),
 * transaction state methods, and deadlock detection.
 */
class TransactionTest extends AbstractDriverTestCase
{
    protected static function getTestTables(): array
    {
        return ['test_transactions'];
    }

    protected static function setUpDatabase(Driver $driver): void
    {
        // Table is created per-test by setupTable() helper
    }

    // =========================================================================
    // inTransaction() Tests
    // =========================================================================

    /**
     * Test inTransaction returns false when not in transaction
     */
    #[Test]
    #[DataProvider('databaseProvider')]
    public function inTransactionReturnsFalseWhenNotInTransaction(string $dbName, Driver $driver)
    {
        $this->assertFalse($driver->inTransaction());
    }

    /**
     * Test inTransaction returns true when in transaction
     */
    #[Test]
    #[DataProvider('databaseProvider')]
    public function inTransactionReturnsTrueWhenInTransaction(string $dbName, Driver $driver)
    {
        $driver->transactionStart();

        $this->assertTrue($driver->inTransaction());

        $driver->transactionRollback();
    }

    /**
     * Test inTransaction returns false after commit
     */
    #[Test]
    #[DataProvider('databaseProvider')]
    public function inTransactionReturnsFalseAfterCommit(string $dbName, Driver $driver)
    {
        $driver->transactionStart();
        $driver->transactionCommit();

        $this->assertFalse($driver->inTransaction());
    }

    /**
     * Test inTransaction returns false after rollback
     */
    #[Test]
    #[DataProvider('databaseProvider')]
    public function inTransactionReturnsFalseAfterRollback(string $dbName, Driver $driver)
    {
        $driver->transactionStart();
        $driver->transactionRollback();

        $this->assertFalse($driver->inTransaction());
    }

    // =========================================================================
    // getTransactionLevel() Tests
    // =========================================================================

    /**
     * Test getTransactionLevel returns 0 when not in transaction
     */
    #[Test]
    #[DataProvider('databaseProvider')]
    public function getTransactionLevelReturnsZeroWhenNotInTransaction(string $dbName, Driver $driver)
    {
        $this->assertEquals(0, $driver->getTransactionLevel());
    }

    /**
     * Test getTransactionLevel returns 1 after single transactionStart
     */
    #[Test]
    #[DataProvider('databaseProvider')]
    public function getTransactionLevelReturnsOneInTransaction(string $dbName, Driver $driver)
    {
        $driver->transactionStart();

        $this->assertEquals(1, $driver->getTransactionLevel());

        $driver->transactionRollback();
    }

    /**
     * Test nested transactions increment level
     */
    #[Test]
    #[DataProvider('databaseProvider')]
    public function nestedTransactionsIncrementLevel(string $dbName, Driver $driver)
    {
        $driver->transactionStart();
        $this->assertEquals(1, $driver->getTransactionLevel());

        $driver->transactionStart(); // Nested (savepoint)
        $this->assertEquals(2, $driver->getTransactionLevel());

        $driver->transactionStart(); // Another nested
        $this->assertEquals(3, $driver->getTransactionLevel());

        // Cleanup
        $driver->transactionRollback();
        $driver->transactionRollback();
        $driver->transactionRollback();
    }

    /**
     * Test commit decrements level
     */
    #[Test]
    #[DataProvider('databaseProvider')]
    public function commitDecrementsLevel(string $dbName, Driver $driver)
    {
        $driver->transactionStart();
        $driver->transactionStart();
        $this->assertEquals(2, $driver->getTransactionLevel());

        $driver->transactionCommit();
        $this->assertEquals(1, $driver->getTransactionLevel());

        $driver->transactionCommit();
        $this->assertEquals(0, $driver->getTransactionLevel());
    }

    /**
     * Test rollback decrements level
     */
    #[Test]
    #[DataProvider('databaseProvider')]
    public function rollbackDecrementsLevel(string $dbName, Driver $driver)
    {
        $driver->transactionStart();
        $driver->transactionStart();
        $this->assertEquals(2, $driver->getTransactionLevel());

        $driver->transactionRollback();
        $this->assertEquals(1, $driver->getTransactionLevel());

        $driver->transactionRollback();
        $this->assertEquals(0, $driver->getTransactionLevel());
    }

    // =========================================================================
    // transaction() Callback Tests
    // =========================================================================

    /**
     * Test transaction commits on success
     */
    #[Test]
    #[DataProvider('databaseProvider')]
    public function transactionCommitsOnSuccess(string $dbName, Driver $driver)
    {
        $this->setupTable($driver);

        $driver->transaction(function ($db) {
            $query = new Query($db);
            $query->insert('test_transactions')->values(['value' => 'test'])->execute();
        });

        // Verify the data was committed
        $query = new Query($driver);
        $result = $query->from('test_transactions')
                       ->select('COUNT(*) as cnt')
                       ->fetch('row');

        $this->assertEquals(1, $result->cnt);

        // Cleanup
        $driver->dropTable('test_transactions', true);
    }

    /**
     * Test transaction rolls back on exception
     */
    #[Test]
    #[DataProvider('databaseProvider')]
    public function transactionRollsBackOnException(string $dbName, Driver $driver)
    {
        $this->setupTable($driver);

        try {
            $driver->transaction(function ($db) {
                $query = new Query($db);
                $query->insert('test_transactions')->values(['value' => 'test'])->execute();

                throw new \RuntimeException('Intentional failure');
            });
        } catch (\RuntimeException $e) {
            // Expected
        }

        // Verify the data was rolled back
        $query = new Query($driver);
        $result = $query->from('test_transactions')
                       ->select('COUNT(*) as cnt')
                       ->fetch('row');

        $this->assertEquals(0, $result->cnt);

        // Cleanup
        $driver->dropTable('test_transactions', true);
    }

    /**
     * Test transaction returns callback result
     */
    #[Test]
    #[DataProvider('databaseProvider')]
    public function transactionReturnsCallbackResult(string $dbName, Driver $driver)
    {
        $this->setupTable($driver);

        $result = $driver->transaction(function ($db) {
            $query = new Query($db);
            $query->insert('test_transactions')->values(['value' => 'test'])->execute();

            return 'success';
        });

        $this->assertEquals('success', $result);

        // Cleanup
        $driver->dropTable('test_transactions', true);
    }

    /**
     * Test transaction rethrows exception after rollback
     */
    #[Test]
    #[DataProvider('databaseProvider')]
    public function transactionRethrowsException(string $dbName, Driver $driver)
    {
        $this->expectException(\RuntimeException::class);
        $this->expectExceptionMessage('Test exception');

        $driver->transaction(function ($db) {
            throw new \RuntimeException('Test exception');
        });
    }

    /**
     * Test transaction receives driver as argument
     */
    #[Test]
    #[DataProvider('databaseProvider')]
    public function transactionReceivesDriverAsArgument(string $dbName, Driver $driver)
    {
        $receivedDriver = null;

        $driver->transaction(function ($db) use (&$receivedDriver) {
            $receivedDriver = $db;
        });

        $this->assertSame($driver, $receivedDriver);
    }

    /**
     * Test transaction not in transaction after success
     */
    #[Test]
    #[DataProvider('databaseProvider')]
    public function notInTransactionAfterSuccess(string $dbName, Driver $driver)
    {
        $driver->transaction(function ($db) {
            // Empty transaction
        });

        $this->assertFalse($driver->inTransaction());
    }

    /**
     * Test transaction not in transaction after failure
     */
    #[Test]
    #[DataProvider('databaseProvider')]
    public function notInTransactionAfterFailure(string $dbName, Driver $driver)
    {
        try {
            $driver->transaction(function ($db) {
                throw new \RuntimeException('Failure');
            });
        } catch (\RuntimeException $e) {
            // Expected
        }

        $this->assertFalse($driver->inTransaction());
    }

    // =========================================================================
    // Nested Transaction (Savepoint) Tests
    // =========================================================================

    /**
     * Test nested transaction commit
     */
    #[Test]
    #[DataProvider('databaseProvider')]
    public function nestedTransactionCommit(string $dbName, Driver $driver)
    {
        $this->setupTable($driver);

        $driver->transactionStart();
        $query = new Query($driver);
        $query->insert('test_transactions')->values(['value' => 'outer'])->execute();

        // Nested transaction
        $driver->transactionStart();
        $query = new Query($driver);
        $query->insert('test_transactions')->values(['value' => 'inner'])->execute();
        $driver->transactionCommit(); // Commit inner

        $driver->transactionCommit(); // Commit outer

        // Both should be committed
        $query = new Query($driver);
        $result = $query->from('test_transactions')
                       ->select('COUNT(*) as cnt')
                       ->fetch('row');

        $this->assertEquals(2, $result->cnt);

        // Cleanup
        $driver->dropTable('test_transactions', true);
    }

    /**
     * Test nested transaction rollback only affects inner
     */
    #[Test]
    #[DataProvider('databaseProvider')]
    public function nestedTransactionRollbackOnlyAffectsInner(string $dbName, Driver $driver)
    {
        $this->setupTable($driver);

        $driver->transactionStart();
        $query = new Query($driver);
        $query->insert('test_transactions')->values(['value' => 'outer'])->execute();

        // Nested transaction
        $driver->transactionStart();
        $query = new Query($driver);
        $query->insert('test_transactions')->values(['value' => 'inner'])->execute();
        $driver->transactionRollback(); // Rollback inner only

        $driver->transactionCommit(); // Commit outer

        // Only outer should be committed
        $query = new Query($driver);
        $result = $query->from('test_transactions')
                       ->select('COUNT(*) as cnt')
                       ->fetch('row');

        $this->assertEquals(1, $result->cnt);

        // Verify it's the outer value
        $query = new Query($driver);
        $result = $query->from('test_transactions')
                       ->select('value')
                       ->fetch('row');

        $this->assertEquals('outer', $result->value);

        // Cleanup
        $driver->dropTable('test_transactions', true);
    }

    /**
     * Test outer rollback rolls back everything
     */
    #[Test]
    #[DataProvider('databaseProvider')]
    public function outerRollbackRollsBackEverything(string $dbName, Driver $driver)
    {
        $this->setupTable($driver);

        $driver->transactionStart();
        $query = new Query($driver);
        $query->insert('test_transactions')->values(['value' => 'outer'])->execute();

        // Nested transaction
        $driver->transactionStart();
        $query = new Query($driver);
        $query->insert('test_transactions')->values(['value' => 'inner'])->execute();
        $driver->transactionCommit(); // Commit inner (to savepoint)

        $driver->transactionRollback(); // Rollback outer (rolls back everything)

        // Nothing should be committed
        $query = new Query($driver);
        $result = $query->from('test_transactions')
                       ->select('COUNT(*) as cnt')
                       ->fetch('row');

        $this->assertEquals(0, $result->cnt);

        // Cleanup
        $driver->dropTable('test_transactions', true);
    }

    // =========================================================================
    // Deadlock Detection Tests
    // =========================================================================

    /**
     * Test isDeadlockException detects deadlock in message
     */
    #[Test]
    #[DataProvider('databaseProvider')]
    public function isDeadlockExceptionDetectsDeadlock(string $dbName, Driver $driver)
    {
        $reflection = new \ReflectionClass(Driver::class);
        $method = $reflection->getMethod('isDeadlockException');
        $method->setAccessible(true);

        $this->assertTrue($method->invoke($driver, new \RuntimeException('Deadlock found')));
        $this->assertTrue($method->invoke($driver, new \RuntimeException('DEADLOCK DETECTED')));
    }

    /**
     * Test isDeadlockException detects lock wait timeout
     */
    #[Test]
    #[DataProvider('databaseProvider')]
    public function isDeadlockExceptionDetectsLockTimeout(string $dbName, Driver $driver)
    {
        $reflection = new \ReflectionClass(Driver::class);
        $method = $reflection->getMethod('isDeadlockException');
        $method->setAccessible(true);

        $this->assertTrue($method->invoke($driver, new \RuntimeException('Lock wait timeout exceeded')));
    }

    /**
     * Test isDeadlockException detects serialization failure
     */
    #[Test]
    #[DataProvider('databaseProvider')]
    public function isDeadlockExceptionDetectsSerializationFailure(string $dbName, Driver $driver)
    {
        $reflection = new \ReflectionClass(Driver::class);
        $method = $reflection->getMethod('isDeadlockException');
        $method->setAccessible(true);

        $this->assertTrue($method->invoke($driver, new \RuntimeException('serialization failure')));
    }

    /**
     * Test isDeadlockException returns false for other exceptions
     */
    #[Test]
    #[DataProvider('databaseProvider')]
    public function isDeadlockExceptionReturnsFalseForOtherExceptions(string $dbName, Driver $driver)
    {
        $reflection = new \ReflectionClass(Driver::class);
        $method = $reflection->getMethod('isDeadlockException');
        $method->setAccessible(true);

        $this->assertFalse($method->invoke($driver, new \RuntimeException('Some other error')));
        $this->assertFalse($method->invoke($driver, new \RuntimeException('Connection refused')));
    }

    // =========================================================================
    // Edge Case Tests
    // =========================================================================

    /**
     * Test transaction with zero attempts uses 1
     */
    #[Test]
    #[DataProvider('databaseProvider')]
    public function transactionWithZeroAttemptsUsesOne(string $dbName, Driver $driver)
    {
        $count = 0;

        $driver->transaction(function ($db) use (&$count) {
            $count++;
        }, 0);

        $this->assertEquals(1, $count);
    }

    /**
     * Test transaction with negative attempts uses 1
     */
    #[Test]
    #[DataProvider('databaseProvider')]
    public function transactionWithNegativeAttemptsUsesOne(string $dbName, Driver $driver)
    {
        $count = 0;

        $driver->transaction(function ($db) use (&$count) {
            $count++;
        }, -5);

        $this->assertEquals(1, $count);
    }

    /**
     * Test transactionCommit when not in transaction is safe
     */
    #[Test]
    #[DataProvider('databaseProvider')]
    public function commitWhenNotInTransactionIsSafe(string $dbName, Driver $driver)
    {
        // Should not throw
        $driver->transactionCommit();

        $this->assertEquals(0, $driver->getTransactionLevel());
    }

    /**
     * Test transactionRollback when not in transaction is safe
     */
    #[Test]
    #[DataProvider('databaseProvider')]
    public function rollbackWhenNotInTransactionIsSafe(string $dbName, Driver $driver)
    {
        // Should not throw
        $driver->transactionRollback();

        $this->assertEquals(0, $driver->getTransactionLevel());
    }

    // =========================================================================
    // Helper Methods
    // =========================================================================

    /**
     * Setup test table
     */
    private function setupTable(Driver $driver): void
    {
        $driver->dropTable('test_transactions', true);

        $schema = $driver->schema();
        $schema->createTable('test_transactions')
            ->id()
            ->string('value', 100)
            ->execute();
    }
}
