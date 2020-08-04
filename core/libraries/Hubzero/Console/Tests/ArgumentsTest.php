<?php
/**
 * @package    framework
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Hubzero\Console\Tests;

use Hubzero\Test\Basic;
use Hubzero\Console\Arguments;
use \Mockery as m;

/**
 * Base relational model tests
 */
class ArgumentsTest extends Basic
{
	/**
	 * Sets up the testing environment
	 *
	 * @return  void
	 **/
	public function setup()
	{
		$config = m::mock('alias:\Hubzero\Console\Config');

		$configuration = [];
		$aliases       = new \stdClass();
		$aliases->env  = 'environment';
		$aliases->t    = 'test::run';
		$aliases->inst = 'repository:package::install';
		$configuration['aliases'] = $aliases;

		$config->shouldReceive('get')->andReturnUsing(function ($key, $default = null) use ($configuration) {
			return (isset($configuration[$key])) ? $configuration[$key] : $default;
		});
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

		$this->assertEquals('Hubzero\Console\Command\Repository', $arguments->get('class'), 'Arguments parser failed to find the proper command');
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

		$this->assertEquals('Hubzero\Console\Tests\Mock\Alternative\Cli\Commands\Subcommand', $arguments->get('class'), 'Arguments parser failed to find the proper command');
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

		$this->assertEquals('Hubzero\Console\Command\Repository\Package', $arguments->get('class'), 'Arguments parser failed to find the proper command');
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

		$this->assertEquals('Hubzero\Console\Command\Environment', $arguments->get('class'), 'Arguments parser failed to find the proper command');
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

		$this->assertEquals('Hubzero\Console\Command\Test', $arguments->get('class'), 'Arguments parser failed to find the proper command');
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

		$this->assertEquals('Hubzero\Console\Command\Repository\Package', $arguments->get('class'), 'Arguments parser failed to find the proper command');
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
