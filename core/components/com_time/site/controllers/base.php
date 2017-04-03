<?php
/**
 * HUBzero CMS
 *
 * Copyright 2005-2015 HUBzero Foundation, LLC.
 *
 * Permission is hereby granted, free of charge, to any person obtaining a copy
 * of this software and associated documentation files (the "Software"), to deal
 * in the Software without restriction, including without limitation the rights
 * to use, copy, modify, merge, publish, distribute, sublicense, and/or sell
 * copies of the Software, and to permit persons to whom the Software is
 * furnished to do so, subject to the following conditions:
 *
 * The above copyright notice and this permission notice shall be included in
 * all copies or substantial portions of the Software.
 *
 * THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
 * IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
 * FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
 * AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
 * LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
 * OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN
 * THE SOFTWARE.
 *
 * HUBzero is a registered trademark of Purdue University.
 *
 * @package   hubzero-cms
 * @author    Sam Wilson <samwilson@purdue.edu>
 * @copyright Copyright 2005-2015 HUBzero Foundation, LLC.
 * @license   http://opensource.org/licenses/MIT MIT
 */

namespace Components\Time\Site\Controllers;

use Hubzero\Component\SiteController;
use Components\Time\Models\Permissions;
use Pathway;
use Route;
use Lang;
use App;

/**
 * Base time controller (extends \Hubzero\Component\SiteController)
 */
class Base extends SiteController
{
	/**
	 * Execute function
	 *
	 * @return void
	 */
	public function execute()
	{
		// Force login if user isn't already
		if (User::isGuest())
		{
			$task = (isset($this->_task) && !empty($this->_task)) ? '&task=' . $this->_task : '';
			// Set the redirect
			App::redirect(
				Route::url('index.php?option=com_users&view=login&return='
					. base64_encode(Route::url('index.php?option=' . $this->_option . '&controller=' . $this->_controller . $task))),
				Lang::txt('COM_TIME_ERROR_LOGIN_REQUIRED'),
				'warning'
			);
			return;
		}

		// Set up permissions model
		$this->permissions = new Permissions($this->_option);

		// Execute the task
		parent::execute();
	}

	/**
	 * Set up a few things for all views and do permissions check
	 *
	 * @return void
	 **/
	protected function _onBeforeDoTask()
	{
		// Set action
		$action = (($this->_task) ? $this->_task : 'view') . '.' . $this->_controller;

		// Make sure action can be performed
		if (!$this->permissions->can($action))
		{
			// Users not submitting time only need to see reports and may not have acess to overview, therefore this redirects to the reports controller to check access there.
			if ($action == 'view.overview')
			{
				App::redirect(
					Route::url('index.php?option=' . $this->_option . '&controller=reports')
				);
			}
			App::abort(401, Lang::txt('COM_TIME_ERROR_NOT_AUTHORIZED'));
			return;
		}

		// Pass permissions model to our view
		$this->view->permissions = $this->permissions;

		// Build pathway
		$this->_buildPathway();

		// Set title
		$this->view->title = $this->_buildTitle();
		$this->view->base  = $this->base = 'index.php?option=' . $this->_option . '&controller=' . $this->_controller;
	}

	/**
	 * Build the "trail"
	 *
	 * @return void
	 */
	protected function _buildPathway()
	{
		if (Pathway::count() <= 0)
		{
			// Base option
			Pathway::append(
				Lang::txt(strtoupper($this->_option)),
				'index.php?option=' . $this->_option
			);
			// Controller
			Pathway::append(
				Lang::txt(strtoupper($this->_option) . '_' . strtoupper($this->_controller)),
				'index.php?option=' . $this->_option . '&controller=' . $this->_controller
			);
			// Task
			if (isset($this->_task) && !empty($this->_task))
			{
				Pathway::append(
					Lang::txt(strtoupper($this->_option) . '_' . strtoupper($this->_controller) . '_' . strtoupper($this->_task)),
					'index.php?option=' . $this->_option . '&controller=' . $this->_controller . '&task=' . $this->_task
				);
			}
		}
	}

	/**
	 * Build the title for the view
	 *
	 * @return (string) $title
	 */
	protected function _buildTitle()
	{
		// Set the title
		$title  = Lang::txt(strtoupper($this->_option) . '_' . strtoupper($this->_controller));

		// Set the title of the browser window
		App::get('document')->setTitle($title);

		return $title;
	}

	/**
	 * Build start query param
	 *
	 * @return string
	 **/
	protected function start($model)
	{
		return $model->getState('start') ? '&start=' . $model->getState('start') : '';
	}
}
