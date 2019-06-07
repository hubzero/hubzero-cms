<?php
/**
 * @package    hubzero-cms
 * @copyright  Copyright 2005-2019 HUBzero Foundation, LLC.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Modules\Version;

use Hubzero\Module\Module;
use App;

/**
 * Module class for displaying CMS version
 */
class Helper extends Module
{
	/**
	 * Display module contents
	 *
	 * @return  void
	 */
	public function display()
	{
		$version = '';

		if ($this->params->get('product', 0))
		{
			$version .= 'HUBzero CMS';
		}

		if ($this->params->get('format', 'short') == 'short')
		{
			$parts = explode('-', HVERSION);
			$version .= ' ' . array_shift($parts);
		}
		else
		{
			$version .= ' ' . HVERSION;
		}

		// Get the view
		require $this->getLayoutPath($this->params->get('layout', 'default'));
	}
}
