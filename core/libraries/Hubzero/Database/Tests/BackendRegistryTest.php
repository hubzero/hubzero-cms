<?php

/**
 * @package    framework
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Hubzero\Database\Tests;

use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;
use Hubzero\Database\BackendRegistry;
use Hubzero\Database\Driver;
use Hubzero\Database\Drivers\Base\BaseSchemaGrammar;
use Hubzero\Database\Drivers\Base\BaseSqlSyntax;

/**
 * Contract tests for BackendRegistry metadata consistency.
 */
class BackendRegistryTest extends TestCase
{
    #[Test]
    public function resolvedDriverClassesAreValidDriverImplementations(): void
    {
        foreach (array_keys(BackendRegistry::driverClassMap()) as $backend) {
            $class = BackendRegistry::resolveDriverClassFor($backend);
            $this->assertNotNull($class, $backend);
            $this->assertTrue(class_exists($class), $backend);
            $this->assertTrue(
                is_subclass_of($class, Driver::class),
                $backend
            );
        }
    }

    #[Test]
    public function resolvedSyntaxClassesAreValidSqlSyntaxImplementations(): void
    {
        foreach (array_keys(BackendRegistry::syntaxClassMap()) as $backend) {
            $class = BackendRegistry::resolveSyntaxClassFor($backend);
            $this->assertNotNull($class, $backend);
            $this->assertTrue(class_exists($class), $backend);
            $this->assertTrue(
                is_subclass_of($class, BaseSqlSyntax::class),
                $backend
            );
        }
    }

    #[Test]
    public function resolvedGrammarClassesAreValidSchemaGrammarImplementations(): void
    {
        foreach (array_keys(BackendRegistry::grammarClassMap()) as $backend) {
            $class = BackendRegistry::resolveGrammarClassFor($backend);
            $this->assertNotNull($class, $backend);
            $this->assertTrue(class_exists($class), $backend);
            $this->assertTrue(
                is_subclass_of($class, BaseSchemaGrammar::class),
                $backend
            );
        }
    }

    #[Test]
    public function driverAndGrammarMapsShareTheSameBackends(): void
    {
        $driverBackends = array_keys(BackendRegistry::driverClassMap());
        $grammarBackends = array_keys(BackendRegistry::grammarClassMap());

        sort($driverBackends);
        sort($grammarBackends);

        $this->assertSame($driverBackends, $grammarBackends);
    }

    #[Test]
    public function grammarResolutionAppliesAliases(): void
    {
        $this->assertSame(
            BackendRegistry::resolveGrammarClassFor('db2'),
            BackendRegistry::resolveGrammarClassFor('ibm')
        );
    }

    #[Test]
    public function driverResolutionNormalizesCaseAndWhitespace(): void
    {
        $this->assertSame(
            BackendRegistry::resolveDriverClassFor('mysql'),
            BackendRegistry::resolveDriverClassFor('  MySQL  ')
        );

        $this->assertSame(
            BackendRegistry::canonicalDriverClassFor('percona'),
            BackendRegistry::canonicalDriverClassFor('  PerCona ')
        );

        $this->assertSame(
            BackendRegistry::conventionDriverClassCandidates(' customDriver '),
            BackendRegistry::conventionDriverClassCandidates('CUSTOMDRIVER')
        );
    }

    #[Test]
    public function unknownBackendResolutionReturnsNull(): void
    {
        $this->assertNull(BackendRegistry::resolveDriverClassFor('does-not-exist'));
        $this->assertNull(BackendRegistry::resolveSyntaxClassFor('does-not-exist'));
        $this->assertNull(BackendRegistry::resolveGrammarClassFor('does-not-exist'));

        $this->assertNull(BackendRegistry::resolveDriverClassFor('   '));
        $this->assertNull(BackendRegistry::resolveSyntaxClassFor('   '));
        $this->assertNull(BackendRegistry::resolveGrammarClassFor('   '));
    }

    #[Test]
    public function firstExistingClassCandidateReturnsFirstLoadableClass(): void
    {
        $this->assertSame(
            \Hubzero\Database\Drivers\Mock\MockDriver::class,
            BackendRegistry::firstExistingClassCandidate([
                '\\Hubzero\\Database\\Driver\\Nope',
                \Hubzero\Database\Drivers\Mock\MockDriver::class,
                \Hubzero\Database\Drivers\Sqlite\SqliteDriver::class,
            ])
        );

        $this->assertNull(
            BackendRegistry::firstExistingClassCandidate([
                '\\Hubzero\\Database\\Driver\\Nope',
                '\\Hubzero\\Database\\Syntax\\Nope',
            ])
        );
    }

    #[Test]
    public function canonicalResolutionMatchesConventionFirstCandidates(): void
    {
        $this->assertSame(
            BackendRegistry::canonicalDriverClassFor('mysql'),
            BackendRegistry::conventionDriverClassCandidates('mysql')[0]
        );

        $this->assertSame(
            BackendRegistry::canonicalSyntaxClassFor('pgsql'),
            BackendRegistry::conventionSyntaxClassCandidates('pgsql')[0]
        );

        $this->assertSame(
            BackendRegistry::canonicalSyntaxClassFor('db2'),
            BackendRegistry::conventionSyntaxClassCandidates('ibm')[0]
        );
    }
}
