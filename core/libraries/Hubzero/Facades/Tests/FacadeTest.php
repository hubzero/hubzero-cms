<?php
/**
 * @package    framework
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Hubzero\Facades\Tests;

use Hubzero\Test\Basic;
use Hubzero\Facades\Facade;
use Hubzero\Facades\Tests\Mock\Application;
use Hubzero\Facades\Tests\Mock\Foo;
use Hubzero\Facades\Tests\Mock\Bar;
use Hubzero\Facades\Tests\Mock\FooFacade;
use Hubzero\Facades\Tests\Mock\BadFacade;

/**
 * Facade tests
 */
class FacadeTest extends Basic
{
	public function setUp()
	{
		$this->app = Facade::getApplication();
	}

	public function tearDown()
	{
		Facade::setApplication($this->app);
	}

	/**
	 * Test setting and getting underlying application
	 *
	 * @covers  \Hubzero\Facades\Facade::setApplication
	 * @covers  \Hubzero\Facades\Facade::getApplication
	 * @return  void
	 **/
	public function testSetAndGetApplication()
	{
		$foo = new Foo;

		$app = new Application;
		$app['foo'] = $foo;

		FooFacade::setApplication($app);

		$this->assertEquals($app, FooFacade::getApplication());
	}

	/**
	 * Test getRoot method
	 *
	 * @covers  \Hubzero\Facades\Facade::getRoot
	 * @return  void
	 **/
	public function testGetRoot()
	{
		$foo = new Foo;

		$app = new Application;
		$app['foo'] = $foo;

		FooFacade::setApplication($app);

		$this->assertEquals($foo, FooFacade::getRoot());
	}

	/**
	 * Tests getAccessor() method
	 *
	 * @covers  \Hubzero\Facades\Facade::getAccessor
	 * @return  void
	 **/
	public function testGetAccessor()
	{
		$foo = new Foo;

		$app = new Application;
		$app['foo'] = $foo;

		BadFacade::setApplication($app);

		$this->setExpectedException('RuntimeException');

		BadFacade::bar();
	}

	/**
	 * Test Facade calls the underlying application
	 *
	 * @covers  \Hubzero\Facades\Facade::getAccessor
	 * @covers  \Hubzero\Facades\Facade::__callStatic
	 * @return  void
	 **/
	public function testFacadeCallsUnderlyingApplication()
	{
		$foo = new Foo;

		$app = new Application;
		$app['foo'] = $foo;

		FooFacade::setApplication($app);

		$this->assertEquals('baz', FooFacade::bar());

		$argsCount = FooFacade::multiArg('one');
		$this->assertEquals($argsCount, 1);

		$argsCount = FooFacade::multiArg('one', 'two');
		$this->assertEquals($argsCount, 2);

		$argsCount = FooFacade::multiArg('one', 'two', 'three');
		$this->assertEquals($argsCount, 3);

		$argsCount = FooFacade::multiArg('one', 'two', 'three', 'four');
		$this->assertEquals($argsCount, 4);

		$argsCount = FooFacade::multiArg('one', 'two', 'three', 'four', 'five');
		$this->assertEquals($argsCount, 5);
	}

	/**
	 * Tests swap() method
	 *
	 * @covers  \Hubzero\Facades\Facade::swap
	 * @return  void
	 **/
	public function testSwap()
	{
		$foo = new Foo;

		$app = new Application;
		$app['foo'] = $foo;

		FooFacade::setApplication($app);

		$bar = new Bar;

		FooFacade::swap($bar);

		$this->assertEquals('zab', FooFacade::bar());
		$this->assertEquals($bar, FooFacade::getRoot());
	}
}
