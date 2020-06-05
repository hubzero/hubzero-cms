<?php
/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Components\Usage\Admin\Controllers;

use Hubzero\Component\AdminController;

/**
 * Controller class for usage
 */
class Data extends AdminController
{
	/**
	 * Display primary page
	 *
	 * @return  void
	 */
	public function displayTask()
	{
		// Output the HTML
		$this->view->display();
	}
}
