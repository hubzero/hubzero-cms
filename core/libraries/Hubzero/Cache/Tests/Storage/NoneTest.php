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
 * NoneTest
 */
class NoneTest extends Basic
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
	public function setUp()
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

		$this->cache->setDefaultDriver('none');
	}

	/**
	 * Test if an item exists in the cache
	 *
	 * @return  void
	 */
	public function testHas()
	{
		$this->cache->put('key', 'value', 15);
		$this->assertFalse($this->cache->has('key'));
	}

	/**
	 * Test adding item to cache, returning FALSE if it already exists
	 *
	 * @return  void
	 */
	public function testAdd()
	{
		$this->cache->put('key', 'value', 15);
		$this->assertFalse($this->cache->add('key', 'value', 15));
	}

	/**
	 * Test retrieving item from cache
	 *
	 * @return  void
	 */
	public function testGet()
	{
		$this->cache->put('key', 'value', 15);
		$this->assertNull($this->cache->get('key'));
	}

	/**
	 * Test puting something into the cache
	 *
	 * @return  void
	 */
	public function testPut()
	{
		$this->assertFalse($this->cache->put('key', 'value', 15));
	}

	/**
	 * Test removing item from cache
	 *
	 * @return  void
	 */
	public function testForget()
	{
		$this->assertTrue($this->cache->forget('key'));
	}
}
