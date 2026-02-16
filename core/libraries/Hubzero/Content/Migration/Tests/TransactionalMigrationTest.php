<?php

/**
 * @package    framework
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Hubzero\Content\Migration\Tests;

use PHPUnit\Framework\TestCase;
use Hubzero\Content\Migration\Base;

/**
 * Tests for transactional migration support
 */
class TransactionalMigrationTest extends TestCase
{
    /**
     * Test that Base migration class has useTransaction property
     */
    public function testBaseHasUseTransactionProperty()
    {
        $this->assertTrue(
            property_exists(Base::class, 'useTransaction'),
            'Base migration class should have useTransaction property'
        );
    }

    /**
     * Test that useTransaction defaults to true
     */
    public function testUseTransactionDefaultsToTrue()
    {
        // Create a mock migration that extends Base
        $migration = new class (null) extends Base {
            public function __construct($db)
            {
                // Skip parent constructor to avoid needing real DB
            }
        };

        $this->assertTrue(
            $migration->useTransaction,
            'useTransaction should default to true'
        );
    }

    /**
     * Test that migrations can opt-out of transactions
     */
    public function testMigrationCanOptOutOfTransactions()
    {
        // Create a mock migration that opts out
        $migration = new class (null) extends Base {
            public $useTransaction = false;

            public function __construct($db)
            {
                // Skip parent constructor
            }
        };

        $this->assertFalse(
            $migration->useTransaction,
            'Migration should be able to opt-out of transactions'
        );
    }

    /**
     * Test that migrations can explicitly enable transactions
     */
    public function testMigrationCanExplicitlyEnableTransactions()
    {
        // Create a mock migration that explicitly enables
        $migration = new class (null) extends Base {
            public $useTransaction = true;

            public function __construct($db)
            {
                // Skip parent constructor
            }
        };

        $this->assertTrue(
            $migration->useTransaction,
            'Migration should be able to explicitly enable transactions'
        );
    }

    /**
     * Test shouldUseTransaction helper method via reflection
     */
    public function testShouldUseTransactionHelper()
    {
        // We can't easily test the Migration class directly without a real DB,
        // but we can verify the logic by testing property detection

        $migrationWithDefault = new class (null) extends Base {
            public function __construct($db)
            {
            }
        };

        $migrationOptOut = new class (null) extends Base {
            public $useTransaction = false;

            public function __construct($db)
            {
            }
        };

        $migrationOptIn = new class (null) extends Base {
            public $useTransaction = true;

            public function __construct($db)
            {
            }
        };

        // Test property exists check
        $this->assertTrue(property_exists($migrationWithDefault, 'useTransaction'));
        $this->assertTrue(property_exists($migrationOptOut, 'useTransaction'));
        $this->assertTrue(property_exists($migrationOptIn, 'useTransaction'));

        // Test values
        $this->assertTrue($migrationWithDefault->useTransaction);
        $this->assertFalse($migrationOptOut->useTransaction);
        $this->assertTrue($migrationOptIn->useTransaction);
    }

    /**
     * Test that useTransaction is a public property
     */
    public function testUseTransactionIsPublic()
    {
        $reflection = new \ReflectionProperty(Base::class, 'useTransaction');

        $this->assertTrue(
            $reflection->isPublic(),
            'useTransaction should be a public property'
        );
    }

    /**
     * Test backwards compatibility - existing migrations without useTransaction work
     */
    public function testBackwardsCompatibilityWithoutProperty()
    {
        // Simulate an old migration that doesn't override useTransaction
        // It should inherit the default true value from Base
        $oldStyleMigration = new class (null) extends Base {
            // No useTransaction property - uses inherited default

            public function __construct($db)
            {
            }

            public function up()
            {
                return true;
            }

            public function down()
            {
                return true;
            }
        };

        // The migration should have useTransaction = true from Base
        $this->assertTrue(
            $oldStyleMigration->useTransaction,
            'Old migrations should inherit useTransaction = true from Base'
        );
    }
}
