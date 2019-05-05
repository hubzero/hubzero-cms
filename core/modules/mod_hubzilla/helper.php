<?php
/**
 * @package    hubzero-cms
 * @copyright  Copyright 2005-2019 HUBzero Foundation, LLC.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Modules\Hubzilla;

use Hubzero\Module\Module;

/**
 * Module class for displaying the king of hubs
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
		require $this->getLayoutPath($this->params->get('layout', 'default'));
	}
}
