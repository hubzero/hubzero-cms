<?php
/**
 * @package    framework
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Hubzero\Base\Tests;

use Hubzero\Test\Basic;

/**
 * Helpers test
 */
class HelpersTest extends Basic
{
	/**
	 * Test app()
	 *
	 * @covers  \app()
	 * @return  void
	 **/
	public function testApp()
	{
		$app = app();

		$this->assertInstanceOf('Hubzero\\Base\\Application', $app);

		$config = app('config');

		$this->assertInstanceOf('Hubzero\\Config\\Repository', $config);
	}

	/**
	 * Test config()
	 *
	 * @covers  \config()
	 * @return  void
	 **/
	public function testConfig()
	{
		$config = config();

		$this->assertInstanceOf('Hubzero\\Config\\Repository', $config);

		$val = config('application_env');

		$this->assertEquals($val, 'testing');

		$val = config('bar', 'foo');

		$this->assertEquals($val, 'foo');
	}

	/**
	 * Test with()
	 *
	 * @covers  \with()
	 * @return  void
	 **/
	public function testWith()
	{
		$obj = with(new \stdClass);

		$this->assertInstanceOf('stdClass', $obj);

		$obj = with(new \Hubzero\Base\Obj(array('foo' => 'bar')));

		$this->assertInstanceOf('Hubzero\\Base\\Obj', $obj);
		$this->assertEquals($obj->get('foo'), 'bar');
	}

	/**
	 * Test classExists()
	 *
	 * @covers  \classExists()
	 * @return  void
	 **/
	public function testClassExists()
	{
		$this->assertFalse(classExists('Hubzero\\Foo\\Bar'));

		$this->assertTrue(classExists('Hubzero\\Base\\Obj'));
	}
}
