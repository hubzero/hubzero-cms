<?php
/**
 * @package    hubzero-cms
 * @copyright  Copyright 2005-2019 HUBzero Foundation, LLC.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Modules\ApplicationEnv;

use Hubzero\Module\Module;
use Config;

/**
 * Module class for displaying current system environment
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
		$this->environment = strtolower(Config::get('application_env', 'production'));
		if (substr($this->environment, 0, 10) == 'production')
		{
			return;
		}

		require $this->getLayoutPath($this->params->get('layout', 'default'));
	}
}
