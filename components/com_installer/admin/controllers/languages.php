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
use Component;
use Request;
use Notify;
use Route;
use Lang;
use App;

include_once(dirname(__DIR__) . DS . 'models' . DS . 'languages.php');

/**
 * Languages Installer Controller
 */
class Languages extends AdminController
{
	/**
	 * Display a list of uninstalled extensions
	 *
	 * @return  void
	 */
	public function displayTask()
	{
		$model = new Models\Languages();

		$this->view->ftp = \JClientHelper::setCredentialsFromRequest('ftp');

		$this->view->state      = $model->getState();
		$this->view->items      = $model->getItems();
		$this->view->pagination = $model->getPagination();

		// Check for errors.
		if (count($errors = $model->getErrors()))
		{
			App::abort(500, implode("\n", $errors));
		}

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
	 * Finds new Languages.
	 *
	 * @return  void
	 */
	public function findTask()
	{
		// Check for request forgeries
		Request::checkToken() or exit(Lang::txt('JINVALID_TOKEN'));

		// Get the caching duration
		$cache_timeout = Component::params('com_installer')->get('cachetimeout', 6, 'int');
		$cache_timeout = 3600 * $cache_timeout;

		// Find updates
		$model = new Models\Languages();
		$model->findLanguages($cache_timeout);

		App::redirect(Route::url('index.php?option=com_installer&view=languages', false));

	}

	/**
	 * Purgue the updates list.
	 *
	 * @return  void
	 */
	public function purgeTask()
	{
		// Check for request forgeries
		Request::checkToken() or exit(Lang::txt('JINVALID_TOKEN'));

		// Purge updates
		$model = new Models\Languages();
		$model->purge();
		$model->enableSites();

		App::redirect(Route::url('index.php?option=com_installer&view=languages', false), $model->_message);
	}

	/**
	 * Install languages.
	 *
	 * @return void
	 */
	public function installTask()
	{
		$model = new Models\Languages();

		// Get array of selected languages
		$lids = Request::getVar('cid', array(), '', 'array');
		\Hubzero\Utility\Arr::toInteger($lids, array());

		if (!$lids)
		{
			// No languages have been selected
			Notify::warning(Lang::txt('COM_INSTALLER_MSG_DISCOVER_NOEXTENSIONSELECTED'));
		}
		else
		{
			// Install selected languages
			$model->install($lids);
		}

		App::redirect(Route::url('index.php?option=com_installer&view=languages', false));
	}
}
