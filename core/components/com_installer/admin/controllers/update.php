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
use Session;
use Request;
use Notify;
use Lang;
use Html;
use App;

include_once(dirname(__DIR__) . DS . 'models' . DS . 'update.php');

/**
 * Update Installer Controller
 */
class Update extends AdminController
{
	/**
	 * Display a list of uninstalled extensions
	 *
	 * @return  void
	 */
	public function displayTask()
	{
		$model = new Models\Update();

		$this->view->ftp = \JClientHelper::setCredentialsFromRequest('ftp');

		$this->view->state      = $model->getState();
		$this->view->items      = $model->getItems();
		$this->view->pagination = $model->getPagination();

		$this->view->paths = new \stdClass();
		$this->view->paths->first = '';

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
	 * Update a set of extensions.
	 *
	 * @since	1.6
	 */
	public function update()
	{
		// Check for request forgeries
		Session::checkToken() or exit(Lang::txt('JINVALID_TOKEN'));

		$model = new Models\Update();
		$uid   = Request::getVar('cid', array(), '', 'array');

		\Hubzero\Utility\Arr::toInteger($uid, array());
		if ($model->update($uid))
		{
			App::get('cache')->clean('mod_menu');
		}

		$redirect_url = User::getState('com_installer.redirect_url');
		if (empty($redirect_url))
		{
			$redirect_url = Route::url('index.php?option=com_installer&view=update', false);
		}
		else
		{
			// wipe out the user state when we're going to redirect
			User::setState('com_installer.redirect_url', '');
			User::setState('com_installer.message', '');
			User::setState('com_installer.extension_message', '');
		}

		App::redirect($redirect_url);
	}

	/**
	 * Find new updates.
	 *
	 * @since	1.6
	 */
	public function find()
	{
		// Check for request forgeries
		Session::checkToken() or exit(Lang::txt('JINVALID_TOKEN'));

		// Get the caching duration
		$component = Component::load('com_installer');
		$params = $component->params;
		$cache_timeout = $params->get('cachetimeout', 6, 'int');
		$cache_timeout = 3600 * $cache_timeout;

		// Find updates
		$model  = new Models\Update();
		$result = $model->findUpdates(0, $cache_timeout);

		App::redirect(Route::url('index.php?option=com_installer&view=update', false));
		//$view->display();
	}

	/**
	 * Purges updates.
	 *
	 * @since	1.6
	 */
	public function purge()
	{
		// Purge updates
		// Check for request forgeries
		Session::checkToken() or exit(Lang::txt('JINVALID_TOKEN'));

		$model = new Models\Update();
		$model->purge();
		$model->enableSites();

		App::redirect(Route::url('index.php?option=com_installer&view=update', false), $model->_message);
	}

	/**
	 * Fetch and report updates in JSON format, for AJAX requests
	 *
	 * @return void
	 *
	 * @since 2.5
	 */
	public function ajax()
	{
		// Note: we don't do a token check as we're fetching information
		// asynchronously. This means that between requests the token might
		// change, making it impossible for AJAX to work.

		$eid  = Request::getInt('eid', 0);
		$skip = Request::getVar('skip', array(), 'default', 'array');

		$cache_timeout = Request::getInt('cache_timeout', 0);
		if ($cache_timeout == 0)
		{
			$cache_timeout = Component::params('com_installer')->get('cachetimeout', 6, 'int');
			$cache_timeout = 3600 * $cache_timeout;
		}

		$model  = new Models\Update();
		$result = $model->findUpdates($eid, $cache_timeout);

		$model->setState('list.start', 0);
		$model->setState('list.limit', 0);
		if ($eid != 0)
		{
			$model->setState('filter.extension_id', $eid);
		}
		$updates = $model->getItems();

		if (!empty($skip))
		{
			$unfiltered_updates = $updates;
			$updates = array();

			foreach ($unfiltered_updates as $update)
			{
				if (!in_array($update->extension_id, $skip)) $updates[] = $update;
			}
		}

		echo json_encode($updates);

		App::close();
	}
}
