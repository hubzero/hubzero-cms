<?php
/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

// No direct access
defined('_HZEXEC_') or die();

/**
 * Update plugin for handling/cleaning cached data
 */
class plgUpdateCache extends \Hubzero\Plugin\Plugin
{
	/**
	 * Trash all expired cache data
	 *
	 * @return  void
	 */
	public function onAfterRepositoryUpdate()
	{
		if (!Config::get('caching'))
		{
			return;
		}

		Cache::gc();
	}
}
