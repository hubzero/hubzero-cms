<?php
/**
 * @package    framework
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Hubzero\Cache\Tests\Storage;

/**
 * XcacheTest
 */
class XcacheTest extends AbstractCacheTest
{
	/**
	 * Test setup
	 *
	 * @return  void
	 */
	public function setUp()
	{
		if (!extension_loaded('xcache'))
		{
			$this->markTestSkipped(
				'The xcache library is not available.'
			);
		}

		parent::setup();

		$this->cache->setDefaultDriver('xcache');
	}
}
