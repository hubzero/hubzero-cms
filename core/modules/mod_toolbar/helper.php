<?php
/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
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
