<?php
/**
 * @package    framework
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Hubzero\Cache\Tests;

use Hubzero\Cache\Storage\None;
use Hubzero\Cache\Manager;
use Hubzero\Base\Application;
use Hubzero\Config\Registry;

/**
 * ManagerTest
 */
class ManagerTest extends \PHPUnit_Framework_TestCase
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
		$configurationFile = __DIR__ . DIRECTORY_SEPARATOR . 'Fixtures' . DIRECTORY_SEPARATOR . 'config.json';

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
		$app['config']->set('foo', array(
			'hash'      => '',
			'cachebase' => ''
		));

		$this->cache = new Manager($app);
	}

	/**
	 * Test that an exception is thrown when selecting
	 * a nonexistent storage type.
	 *
	 * @expectedException \InvalidArgumentException
	 *
	 * @return  void
	 */
	public function testStorageThrowsException()
	{
		$this->cache->storage('foo');
	}

	/**
	 * Test setting the default storage type
	 *
	 * @return  void
	 */
	public function testSetDefaultDriver()
	{
		$this->cache->setDefaultDriver('memory');

		$this->assertEquals('memory', $this->cache->getDefaultDriver());
	}

	/**
	 * Test adding custom storage type
	 *
	 * @return  void
	 */
	public function testExtend()
	{
		$this->cache->extend('foo', function($config)
		{
			return new None;
		});

		$this->assertInstanceOf('Hubzero\Cache\Storage\None', $this->cache->storage('foo'));
	}
}
