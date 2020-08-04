<?php
/**
 * @package    framework
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Hubzero\Cache\Tests\Storage;

/**
 * CacheLiteTest
 */
class CacheLiteTest extends AbstractCacheTest
{
	/**
	 * Test setup
	 *
	 * @return  void
	 */
	public function setUp()
	{
		@include_once 'Cache' . DS . 'Lite.php';

		if (!class_exists('Cache_Lite'))
		{
			$this->markTestSkipped(
				'The CacheLite library is not available.'
			);
		}

		parent::setup();

		$this->cache->setDefaultDriver('cachelite');
	}
}
