<?php
/**
 * @package    framework
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Hubzero\Cache\Tests\Storage;

/**
 * ApcTest
 */
class ApcTest extends AbstractCacheTest
{
	/**
	 * Test setup
	 *
	 * @return  void
	 */
	public function setUp()
	{
		if (!extension_loaded('apcu'))
		{
			$this->markTestSkipped(
				'The APCu extension is not available.'
			);
		}
		if (!ini_get('apc.enable_cli'))
		{
			$this->markTestSkipped(
				'You need to enable apc.enable_cli'
			);
		}

		parent::setup();

		$this->cache->setDefaultDriver('apc');
	}
}
