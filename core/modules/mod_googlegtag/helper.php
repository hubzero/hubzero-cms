<?php
/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2022 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Modules\GoogleGtag;

use Hubzero\Module\Module;
use Config;

/**
 * Module class for adding Google Gtag script to a page
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

		if (!$this->params->get('trackingID'))
		{
			return;
		}

		require $this->getLayoutPath($this->params->get('layout', 'default'));
	}
}
