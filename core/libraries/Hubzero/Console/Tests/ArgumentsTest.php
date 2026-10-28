<?php

/**
 * @package    framework
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Hubzero\Console\Tests;

use Hubzero\Test\Basic;
use Hubzero\Console\Arguments;

/**
 * Base relational model tests
 */
class ArgumentsTest extends Basic
{
    /**
     * The home directory Config reads its .muse from
     *
     * @var  string
     **/
    private static $home = null;

    /**
     * The real one, to put back
     *
     * @var  string
     **/
    private static $realHome = null;

    /**
     * Give Console\Config a .muse of our own to read
     *
     * Not a Mockery "alias:" mock: that declares Hubzero\Console\Config for
     * the rest of the process, and every later test that reached Output --
     * which asks that class whether to colour -- got the mock instead.
     *
     * @return  void
     **/
    public static function setUpBeforeClass(): void
    {
        parent::setUpBeforeClass();

        self::$realHome = getenv('HOME');
        self::$home     = sys_get_temp_dir() . DS . 'muse_' . bin2hex(random_bytes(6));

        mkdir(self::$home, 0700, true);
        file_put_contents(self::$home . DS . '.muse', json_encode(array(
            'aliases' => array(
                'env'  => 'environment',
                't'    => 'test::run',
                'inst' => 'repository:package::install'
            )
        )));

        putenv('HOME=' . self::$home);
    }

    /**
     * Put the real home back
     *
     * @return  void
     **/
    public static function tearDownAfterClass(): void
    {
        if (self::$home && is_dir(self::$home)) {
            @unlink(self::$home . DS . '.muse');
            @rmdir(self::$home);
        }

        if (self::$realHome !== false && self::$realHome !== null) {
            putenv('HOME=' . self::$realHome);
        }

        parent::tearDownAfterClass();
    }


    /**
     * Tests to make sure we can parse for a basic command and task
     *
     * @return  void
     **/
    public function testParseBasicCommandAndTask()
    {
        $args = [
            'muse',
            'repository',
            'update'
        ];

        $arguments = new Arguments($args);
        $arguments->parse();

        $this->assertEquals(
            'Hubzero\Console\Command\Repository',
            $arguments->get('class'),
            'Arguments parser failed to find the proper command'
        );
        $this->assertEquals('update', $arguments->get('task'), 'Arguments parser failed to find the proper task');
    }

    /**
     * Tests to make sure we can parse for a basic command and task within an alternate namespace
     *
     * @return  void
     **/
    public function testParseBasicCommandAndTaskFromAlternateLocation()
    {
        $args = [
            'muse',
            'alternative:subcommand'
        ];

        Arguments::registerNamespace('Hubzero\Console\Tests\Mock\{$1}\Cli\Commands');

        $arguments = new Arguments($args);
        $arguments->parse();

        $this->assertEquals(
            'Hubzero\Console\Tests\Mock\Alternative\Cli\Commands\Subcommand',
            $arguments->get('class'),
            'Arguments parser failed to find the proper command'
        );
        $this->assertEquals('execute', $arguments->get('task'), 'Arguments parser failed to find the proper task');
    }

    /**
     * Tests to make sure we can parse for a command and task within a nested command
     *
     * @return  void
     **/
    public function testParseCommandAndTaskWithNamespace()
    {
        $args = [
            'muse',
            'repository:package',
            'install'
        ];

        $arguments = new Arguments($args);
        $arguments->parse();

        $this->assertEquals(
            'Hubzero\Console\Command\Repository\Package',
            $arguments->get('class'),
            'Arguments parser failed to find the proper command'
        );
        $this->assertEquals('install', $arguments->get('task'), 'Arguments parser failed to find the proper task');
    }

    /**
     * Tests to make sure we can parse for a command and task from an alias
     *
     * @return  void
     **/
    public function testParseCommandAndTaskFromAlias()
    {
        $args = [
            'muse',
            'env'
        ];

        $arguments = new Arguments($args);
        $arguments->parse();

        $this->assertEquals(
            'Hubzero\Console\Command\Environment',
            $arguments->get('class'),
            'Arguments parser failed to find the proper command'
        );
        $this->assertEquals('execute', $arguments->get('task'), 'Arguments parser failed to find the proper task');
    }

    /**
     * Tests to make sure we can parse for a command and task from an alias with task embedded
     *
     * @return  void
     **/
    public function testParseCommandAndTaskFromAliasWithTaskEmbedded()
    {
        $args = [
            'muse',
            't'
        ];

        $arguments = new Arguments($args);
        $arguments->parse();

        $this->assertEquals(
            'Hubzero\Console\Command\Test',
            $arguments->get('class'),
            'Arguments parser failed to find the proper command'
        );
        $this->assertEquals('run', $arguments->get('task'), 'Arguments parser failed to find the proper task');
    }

    /**
     * Tests to make sure we can parse for a command and task from an alias with task embedded and a namespace
     *
     * @return  void
     **/
    public function testParseCommandAndTaskFromAliasWithTaskEmbeddedAndNamespace()
    {
        $args = [
            'muse',
            'inst'
        ];

        $arguments = new Arguments($args);
        $arguments->parse();

        $this->assertEquals(
            'Hubzero\Console\Command\Repository\Package',
            $arguments->get('class'),
            'Arguments parser failed to find the proper command'
        );
        $this->assertEquals('install', $arguments->get('task'), 'Arguments parser failed to find the proper task');
    }

    /**
     * Tests to make sure we get an exception for a bad/unknown command
     *
     * @expectedException  \Hubzero\Console\Exception\UnsupportedCommandException
     * @return  void
     **/
    public function testParseNonExistentCommand()
    {
        $args = [
            'muse',
            'blah'
        ];

        $arguments = new Arguments($args);

        $this->expectException(\Hubzero\Console\Exception\UnsupportedCommandException::class);

        $arguments->parse();
    }

    /**
     * Tests to make sure we can parse options
     *
     * @return  void
     **/
    public function testParseOptions()
    {
        $args = [
            'muse',
            'repository',
            'update',
            '-a',
            '--b',
            '--c=foo',
            '--d=1',
            '--d=2',
            '-ef',
            '-g=bar',
            '-h=1',
            '-h=2',
            '--i-j'
        ];

        $arguments = new Arguments($args);
        $arguments->parse();

        $this->assertTrue($arguments->getOpt('a'), 'Failed to detect parameter "a" as true');
        $this->assertTrue($arguments->getOpt('b'), 'Failed to detect parameter "b" as true');
        $this->assertEquals('foo', $arguments->getOpt('c'), 'Failed to get proper option value for "c"');
        $this->assertEquals(['1', '2'], $arguments->getOpt('d'), 'Failed to get proper option value for "d"');
        $this->assertTrue($arguments->getOpt('e'), 'Failed to detect parameter "e" as true');
        $this->assertTrue($arguments->getOpt('f'), 'Failed to detect parameter "f" as true');
        $this->assertEquals('bar', $arguments->getOpt('g'), 'Failed to get proper option value for "g"');
        $this->assertEquals(['1', '2'], $arguments->getOpt('h'), 'Failed to get proper option value for "h"');
        $this->assertTrue($arguments->getOpt('i-j'), 'Failed to detect parameter "i-j" as true');

        $arguments->setOpt('i', 'foobar');
        $this->assertEquals('foobar', $arguments->getOpt('i'), 'Failed to get proper option value for "i"');

        $arguments->deleteOpt('i');
        $this->assertFalse($arguments->getOpt('i'), 'Failed to properly delete option "i"');

        $this->assertEquals([
            'a'   => true,
            'b'   => true,
            'c'   => 'foo',
            'd'   => ['1', '2'],
            'e'   => true,
            'f'   => true,
            'g'   => 'bar',
            'h'   => ['1', '2'],
            'i-j' => true
        ], $arguments->getOpts(), 'Failed to properly fetch all options');
    }
}
