<?php
/**
 * @package    framework
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Hubzero\Container\Tests;

use Hubzero\Container\Container;
use Hubzero\Container\Tests\Mock\Service;
use Hubzero\Test\Basic;

/**
 * Container test
 */
class ContainerTest extends Basic
{
	/**
	 * Test the constructor sets values
	 *
	 * @covers  \Hubzero\Container\Container::__construct
	 * @return  void
	 **/
	public function testConstructor()
	{
		$params = array('param' => 'value');

		$container = new Container($params);

		$this->assertSame($params['param'], $container['param']);
	}

	/**
	 * Test setting and getting a string
	 *
	 * @covers  \Hubzero\Container\Container::set
	 * @covers  \Hubzero\Container\Container::offsetSet
	 * @covers  \Hubzero\Container\Container::get
	 * @covers  \Hubzero\Container\Container::offsetGet
	 * @return  void
	 **/
	public function testWithString()
	{
		$container = new Container();
		$container['param'] = 'value';

		$this->assertEquals('value', $container['param']);

		$this->assertTrue($container->has('param'));

		$container->set('foo', 'bar');

		$this->assertEquals('bar', $container->get('foo'));

		$this->setExpectedException('InvalidArgumentException');

		$container->get('lorem');
	}

	/**
	 * Test setting and getting a string
	 *
	 * @covers  \Hubzero\Container\Container::set
	 * @covers  \Hubzero\Container\Container::offsetSet
	 * @covers  \Hubzero\Container\Container::get
	 * @covers  \Hubzero\Container\Container::offsetGet
	 * @return  void
	 **/
	public function testWithClosure()
	{
		$container = new Container();
		$container['service'] = function ()
		{
			return new Service();
		};

		$this->assertInstanceOf(Service::class, $container['service']);
	}

	/**
	 * Test checking for a parameter being set or not
	 *
	 * @covers  \Hubzero\Container\Container::has
	 * @covers  \Hubzero\Container\Container::offsetExists
	 * @return  void
	 **/
	public function testHas()
	{
		$container = new Container();
		$container['param'] = 'value';

		$this->assertTrue(isset($container['param']));

		$this->assertFalse(isset($container['foo']));

		$container->set('foo', 'bar');

		$this->assertTrue($container->has('foo'));

		$this->assertFalse($container->has('ipsum'));
	}

	/**
	 * Test unsetting a parameter
	 *
	 * @covers  \Hubzero\Container\Container::forget
	 * @covers  \Hubzero\Container\Container::offsetUnset
	 * @return  void
	 **/
	public function testForget()
	{
		$container = new Container();
		$container['param'] = 'value';

		$this->assertTrue(isset($container['param']));

		unset($container['param']);

		$this->assertFalse(isset($container['param']));

		$container->set('foo', 'bar');

		$this->assertTrue($container->has('foo'));

		$container->forget('foo');

		$this->assertFalse($container->has('foo'));
	}

	/**
	 * Test getting defined value names
	 *
	 * @covers  \Hubzero\Container\Container::keys
	 * @return  void
	 **/
	public function testKeys()
	{
		$container = new Container();
		$container->set('foo', 'bar');
		$container->set('bar', 'foo');

		$this->assertEquals(array('foo', 'bar'), $container->keys());
	}

	/**
	 * Test getting raw value
	 *
	 * @covers  \Hubzero\Container\Container::raw
	 * @return  void
	 **/
	public function testRaw()
	{
		$container = new Container();

		$service = function ()
		{
			return 'foo';
		};

		$container['service'] = $service;

		$this->assertSame($service, $container->raw('service'));

		$this->setExpectedException('InvalidArgumentException');

		$container->raw('lorem');
	}

	/**
	 * Test that factory services are different
	 *
	 * @covers  \Hubzero\Container\Container::factory
	 * @return  void
	 **/
	public function testServicesShouldBeDifferent()
	{
		$container = new Container();

		$container['service'] = $container->factory(function () {
			return new Service();
		});

		$serviceOne = $container['service'];

		$this->assertInstanceOf(__NAMESPACE__ . '\Mock\Service', $serviceOne);

		$serviceTwo = $container['service'];

		$this->assertInstanceOf(__NAMESPACE__ . '\Mock\Service', $serviceTwo);

		$this->assertNotSame($serviceOne, $serviceTwo);
	}

	/**
	 * Test that extend() throws an exception when a key is undefined
	 *
	 * @covers  \Hubzero\Container\Container::extend
	 * @return  void
	 **/
	public function testExtendThrowsExceptionWithUndefinedKey()
	{
		$container = new Container();

		$this->setExpectedException('InvalidArgumentException');

		$container->extend(
			'lorem',
			function ()
			{
				return 'ipsum';
			}
		);
	}

	/**
	 * Test that extend() throws an exception when a definition isn't callable
	 *
	 * @covers  \Hubzero\Container\Container::extend
	 * @return  void
	 **/
	public function testExtendThrowsExceptionWithInvalidDefinition()
	{
		$container = new Container();
		$container['param'] = 'value';

		$this->setExpectedException('InvalidArgumentException');

		$container->extend(
			'param',
			function ()
			{
				return 'ipsum';
			}
		);
	}

	/**
	 * Test that extend() throws an exception when an extension isn't callable
	 *
	 * @covers  \Hubzero\Container\Container::extend
	 * @return  void
	 **/
	public function testExtendThrowsExceptionWithUncallableExtension()
	{
		$container = new Container();
		$container['param'] = 'value';

		$this->setExpectedException('InvalidArgumentException');

		$container->extend(
			'param',
			'ipsum'
		);
	}

	/**
	 * Test extending a service
	 *
	 * @covers  \Hubzero\Container\Container::extend
	 * @return  void
	 **/
	public function testExtendingService()
	{
		$container = new Container();
		$container['foo'] = function ()
		{
			return 'foo';
		};

		$container['foo'] = $container->extend('foo', function ($foo, $app)
		{
			return "$foo.bar";
		});

		$container['foo'] = $container->extend('foo', function ($foo, $app)
		{
			return "$foo.baz";
		});

		$this->assertSame('foo.bar.baz', $container['foo']);
	}
}
