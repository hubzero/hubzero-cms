<?php
/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

// No direct access
defined('_HZEXEC_') or die();

/**
 * System plugin to force .rss and .atom URLs to raw document mode
 */
class plgSystemXFeed extends \Hubzero\Plugin\Plugin
{
	/**
	 * Perform actions after initialization
	 *
	 * @return  void
	 */
	public function onAfterInitialise()
	{
		if (!App::isSite())
		{
			return;
		}

		$uri = Request::getString('REQUEST_URI', null, 'server');
		$bits = explode('?', $uri);
		$bit = $bits[0];

		if (!strpos($bit, '.'))
		{
			return;
		}

		$bi = explode('.', $bit);
		$b = end($bi);
		$b = strtolower($b);

		if ($b == 'rss' || $b == 'atom')
		{
			Request::setVar('no_html', 1);
			Request::setVar('format', 'raw');
		}
	}
}
