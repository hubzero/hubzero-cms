<?php
/**
 * @package    framework
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Hubzero\Cache\Tests\Storage;

/**
 * WincacheTest
 */
class WincacheTest extends AbstractCacheTest
{
	/**
	 * Test setup
	 *
	 * @return  void
	 */
	public function setUp()
	{
		if (!extension_loaded('wincache') || !function_exists('wincache_ucache_get'))
		{
			$this->markTestSkipped(
				'The wincache library is not available.'
			);
		}
		if (!ini_get('wincache.ucenabled'))
		{
			$this->markTestSkipped(
				'You need to enable wincache.ucenabled'
			);
		}

		parent::setup();

		$this->cache->setDefaultDriver('wincache');
	}
}
