<?php
/**
 * HUBzero CMS
 *
 * Copyright 2005-2015 Purdue University. All rights reserved.
 *
 * This file is part of: The HUBzero(R) Platform for Scientific Collaboration
 *
 * The HUBzero(R) Platform for Scientific Collaboration (HUBzero) is free
 * software: you can redistribute it and/or modify it under the terms of
 * the GNU Lesser General Public License as published by the Free Software
 * Foundation, either version 3 of the License, or (at your option) any
 * later version.
 *
 * HUBzero is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU Lesser General Public License for more details.
 *
 * You should have received a copy of the GNU Lesser General Public License
 * along with this program.  If not, see <http://www.gnu.org/licenses/>.
 *
 * HUBzero is a registered trademark of Purdue University.
 *
 * @package   hubzero-cms
 * @author    Shawn Rice <zooley@purdue.edu>
 * @copyright Copyright 2005-2015 Purdue University. All rights reserved.
 * @license   http://www.gnu.org/licenses/lgpl-3.0.html LGPLv3
 */

namespace Components\Installer\Admin\Controllers;

use Hubzero\Component\AdminController;
use Components\Installer\Admin\Models;
use Route;
use App;

include_once(dirname(__DIR__) . DS . 'models' . DS . 'discover.php');

/**
 * Controller for discovering extensions
 */
class Discover extends AdminController
{
	/**
	 * Display a list of uninstalled extensions
	 *
	 * @return  void
	 */
	public function displayTask()
	{
		$model = new Models\Discover();

		$this->view->ftp = \JClientHelper::setCredentialsFromRequest('ftp');

		$this->view->state      = $model->getState();
		$this->view->items      = $model->getItems();
		$this->view->pagination = $model->getPagination();

		$showMessage = false;
		if (is_object($this->view->state))
		{
			$message1    = $this->view->state->get('message');
			$message2    = $this->view->state->get('extension_message');
			$showMessage = ($message1 || $message2);
		}
		$this->view->showMessage = $showMessage;

		$this->view->display();
	}

	/**
	 * Refreshes the cache of discovered extensions.
	 *
	 * @return  void
	 */
	public function refreshTask()
	{
		$model = new Models\Discover();
		$model->discover();

		App::redirect(
			Route::url('index.php?option=com_installer&controller=discover', false)
		);
	}

	/**
	 * Install a discovered extension.
	 *
	 * @return  void
	 */
	public function installTask()
	{
		$model = new Models\Discover();
		$model->discover_install();

		App::redirect(
			Route::url('index.php?option=com_installer&controller=discover', false)
		);
	}

	/**
	 * Clean out the discovered extension cache.
	 *
	 * @return  void
	 */
	public function purgeTask()
	{
		$model = new Models\Discover();
		$model->purge();

		App::redirect(
			Route::url('index.php?option=com_installer&controller=discover', false),
			$model->_message
		);
	}
}
