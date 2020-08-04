<?php
/**
 * @package    framework
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Hubzero\Component;

/**
 * Base admin controller for components to extend.
 */
class AdminController extends SiteController
{
	/**
	 * Cancels a task and redirects to default view
	 *
	 * @return  void
	 */
	public function cancelTask()
	{
		// Set the redirect
		\App::redirect(
			\Route::url('index.php?option=' . $this->_option . ($this->_controller ? '&controller=' . $this->_controller : ''), false)
		);
	}
}
