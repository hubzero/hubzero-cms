<?php
/**
 * @package    framework
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Hubzero\Cache\Tests\Storage;

use Hubzero\Test\Basic;
use Hubzero\Base\Application;
use Hubzero\Config\Registry;
use Hubzero\Cache\Manager;

/**
 * AbstractCacheTest
 */
abstract class AbstractCacheTest extends Basic
{
	/**
	 * Cache manager
	 *
	 * @var  object
	 */
	protected $cache;

	/**
	 * Test setup
	 *
	 * @return  void
	 */
	public function setup()
	{
		$configurationFile = dirname(__DIR__) . DIRECTORY_SEPARATOR . 'Fixtures' . DIRECTORY_SEPARATOR . 'config.json';

		if (!is_file($configurationFile))
		{
			throw new \Exception('Configuration file not found in "' . $configurationFile . '"');
		}

		$config = json_decode(file_get_contents($configurationFile), true);

		$app = new Application;
		$app['config'] = new Registry();
		foreach ($config as $key => $value)
		{
			$app['config']->set($key, $value);
		}

		$this->cache = new Manager($app);
	}

	/**
	 * @return  array
	 */
	public function dataProvider()
	{
		return [
			['key1', 'value1', 1],
			['key2', 'value2', 100],
			['key3', 'value3', null],
			['key4', true, null],
			['key5', false, null],
			['key6', array(), null],
			['key7', new \DateTime('now', new \DateTimeZone('UTC')), null],
		];
	}

	/**
	 * Test if an item exists int he cache
	 *
	 * @dataProvider dataProvider
	 *
	 * @param   string    $key
	 * @param   mixed     $value
	 * @param   int|null  $ttl
	 * @return  void
	 */
	public function testHas($key, $value, $ttl)
	{
		$this->assertTrue($this->cache->forget($key));
		$this->assertFalse($this->cache->has($key));
		$this->assertTrue($this->cache->put($key, $value, $ttl));
		$this->assertTrue($this->cache->has($key));
	}

	/**
	 * Test adding item to cache, returning FALSE if it already exists
	 *
	 * @dataProvider dataProvider
	 *
	 * @param   string    $key
	 * @param   mixed     $value
	 * @param   int|null  $ttl
	 * @return  void
	 */
	public function testAdd($key, $value, $ttl)
	{
		$this->cache->put($key, $value, $ttl);
		$this->assertFalse($this->cache->add($key, $value, $ttl));
	}

	/**
	 * Test retrieving item from cache
	 *
	 * @dataProvider dataProvider
	 *
	 * @param   string    $key
	 * @param   mixed     $value
	 * @param   int|null  $ttl
	 * @return  void
	 */
	public function testGet($key, $value, $ttl)
	{
		$this->cache->put($key, $value, $ttl);
		$this->assertEquals($value, $this->cache->get($key));
	}

	/**
	 * Test removing item from cache
	 *
	 * @dataProvider dataProvider
	 *
	 * @param   string    $key
	 * @param   mixed     $value
	 * @param   int|null  $ttl
	 * @return  void
	 */
	public function testForget($key, $value, $ttl)
	{
		$this->cache->put($key, $value, $ttl);
		$this->assertTrue($this->cache->forget($key));
		$this->assertFalse($this->cache->has($key));
	}

	/**
	 * Test has() with expired data
	 *
	 * @return  void
	 */
	public function testHasWithTtlExpired()
	{
		$this->cache->put('key1', 'value1', (1 / 60));
		sleep(2);
		$this->assertFalse($this->cache->has('key1'));
	}
}
