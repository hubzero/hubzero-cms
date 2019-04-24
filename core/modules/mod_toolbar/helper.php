<?php
/**
 * @package    hubzero-cms
 * @copyright  Copyright 2005-2019 HUBzero Foundation, LLC.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Modules\Toolbar;

use Hubzero\Module\Module;

/**
 * Module class for displaying component toolbar
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
		if (!\App::isAdmin())
		{
			return;
		}

		// Get the toolbar.
		$toolbar = \Toolbar::render('toolbar');

		// Get the view
		require $this->getLayoutPath($this->params->get('layout', 'default'));
	}
}
