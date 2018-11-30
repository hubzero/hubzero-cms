<?php
/**
 * @package    hubzero-cms
 * @copyright  Copyright 2005-2019 HUBzero Foundation, LLC.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Modules\GoogleAnalytics;

use Hubzero\Module\Module;
use Config;

/**
 * Module class for adding Google Analytics script to a page
 */
class Helper extends Module
{
	/**
	 * Display module
	 *
	 * @return  void
	 */
	public function display()
	{
		if (substr(strtolower(Config::get('application_env', 'production')), 0, 10) != 'production')
		{
			return;
		}

		if (!$this->params->get('key'))
		{
			return;
		}

		require $this->getLayoutPath($this->params->get('layout', 'default'));
	}
}
