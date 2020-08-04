<?php
/**
 * @package    framework
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Hubzero\Base\Tests;

use Hubzero\Test\Basic;
use Hubzero\Base\ClientManager;

/**
 * ClientManager test
 */
class ClientManagerTest extends Basic
{
	/**
	 * Sample data
	 *
	 * @var  array
	 */
	protected $data = array(
		'one'   => 'for the money',
		'two'   => 'for the show',
		'three' => 'to get ready',
		'four'  => 'to go'
	);

	/**
	 * Test reset() and all()
	 *
	 * @covers  \Hubzero\Base\ClientManager::reset
	 * @covers  \Hubzero\Base\ClientManager::all
	 * @return  void
	 **/
	public function testReset()
	{
		ClientManager::reset();

		$all = ClientManager::all();

		$this->assertEquals($all, null);
	}

	/**
	 * Test client()
	 *
	 * @covers  \Hubzero\Base\ClientManager::client
	 * @return  void
	 **/
	public function testClient()
	{
		$clients = ClientManager::client();

		$this->assertCount(7, $clients);

		$client = ClientManager::client(1);

		$this->assertTrue(is_object($client));
		$this->assertEquals($client->name, 'administrator');

		$client = ClientManager::client('api', true);

		$this->assertTrue(is_object($client));
		$this->assertEquals($client->name, 'api');
		$this->assertEquals($client->id, 4);

		$client = ClientManager::client('site');

		$this->assertFalse(is_object($client));
	}

	/**
	 * Test modify()
	 *
	 * @covers  \Hubzero\Base\ClientManager::modify
	 * @return  void
	 **/
	public function testModify()
	{
		ClientManager::modify(1, 'name', 'adminstuff');

		$client = ClientManager::client(1);

		$this->assertTrue(is_object($client));
		$this->assertEquals($client->name, 'adminstuff');

		ClientManager::modify(1, 'name', 'administrator');
	}

	/**
	 * Test append()
	 *
	 * @covers  \Hubzero\Base\ClientManager::append
	 * @return  void
	 **/
	public function testAppend()
	{
		$clients = ClientManager::client();

		$foo = array(
			'id' => 9,
			'name' => 'foo',
			'url' => 'foo'
		);

		$bar = new \stdClass;
		$bar->id = 10;
		$bar->name = 'bar';
		$bar->url = 'bar';

		$tur = new \stdClass;
		$tur->name = 'tur';
		$tur->url = 'tur';

		$glu = 'foobar';

		ClientManager::append($tur);
		ClientManager::append($foo);
		ClientManager::append($bar);

		$this->assertFalse(ClientManager::append($glu));

		$client = ClientManager::client('tur', true);

		$this->assertTrue(is_object($client));
		$this->assertEquals($client->name, 'tur');
		$this->assertEquals($client->id, count($clients));

		$client = ClientManager::client(9);

		$this->assertTrue(is_object($client));
		$this->assertEquals($client->name, 'foo');

		$client = ClientManager::client(10);

		$this->assertTrue(is_object($client));
		$this->assertEquals($client->name, 'bar');

		$client = ClientManager::client('foo', true);

		$this->assertTrue(is_object($client));
		$this->assertEquals($client->name, 'foo');
		$this->assertEquals($client->id, 9);
	}
}
