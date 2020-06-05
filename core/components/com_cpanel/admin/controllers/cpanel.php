<?php
/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Components\Cpanel\Admin\Controllers;

use Hubzero\Component\AdminController;

/**
 * Cpanel Controller
 */
class Cpanel extends AdminController
{
	/**
	 * Display admin control panel
	 *
	 * @return  void
	 */
	public function displayTask()
	{
		// Set the template - this will display cpanel.php
		// from the selected admin template.
		\Request::setVar('tmpl', 'cpanel');

		$this->view
			->setLayout('default')  // Preserve potential view overrides
			->display();
	}

	/**
	 * Display a specific module
	 *
	 * @return  void
	 */
	public function moduleTask()
	{
		$this->view
			->setLayout('module')  // Preserve potential view overrides
			->display();
	}
}
