<?php
/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Modules\Title;

use Hubzero\Module\Module;
use App;

/**
 * Module class for displaying component title
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
		// Get the component title div
		if (App::has('ComponentTitle'))
		{
			$this->title = App::get('ComponentTitle');
		}

		// Get the view
		parent::display();
	}
}
