<?php
/**
 * @package    hubzero-cms
 * @copyright  Copyright 2005-2019 HUBzero Foundation, LLC.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Modules\Submenu;

use Hubzero\Module\Module;

/**
 * Module class for rendering a submenu
 */
class Helper extends Module
{
	/**
	 * Get the items of the submenu and display them.
	 *
	 * @return  void
	 */
	public function display()
	{
		if (!\App::isAdmin() || !class_exists('\\Submenu'))
		{
			return;
		}

		// Initialise variables.
		$list = \Submenu::getItems();

		if (!is_array($list) || !count($list))
		{
			return;
		}

		require $this->getLayoutPath($this->params->get('layout', 'default'));
	}
}
