<?php
/**
 * HUBzero CMS
 *
 * Copyright 2005-2018 HUBzero Foundation, LLC.
 *
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 2 of the License, or
 * (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program.  If not, see <http://www.gnu.org/licenses/>.
 *
 * HUBzero is a registered trademark of Purdue University.
 *
 * @package   hubzero-cms
 * @author    Zach Weidner <zweidner@purdue.edu>
 * @copyright Copyright 2005-2018 HUBzero Foundation, LLC.
 * @license   http://www.gnu.org/licenses/gpl-2.0.html GPLv2
 */

namespace Components\Installer\Admin\Controllers;

use Hubzero\Component\AdminController;
use Hubzero\Utility\Composer as ComposerHelper;
use Request;
use Config;
use Notify;
use StdClass;
use Route;
use App;

/**
 * Repositories Controller
 */
class Repositories extends AdminController
{
	/**
	 * Execute a task
	 *
	 * @return  void
	 */
	public function execute()
	{
		$this->registerTask('add', 'edit');

		parent::execute();
	}

	/**
	 * Display a list of uninstalled extensions
	 *
	 * @return  void
	 */
	public function displayTask()
	{
		// Paging
		$filters = array(
			'limit' => Request::getState(
				$this->_option . '.' . $this->_controller . '.limit',
				'limit',
				Config::get('list_limit'),
				'int'
			),
			'start' => Request::getState(
				$this->_option . '.' . $this->_controller . '.limitstart',
				'limitstart',
				0,
				'int'
			)
		);

		try
		{
			$repositories = ComposerHelper::getRepositoryConfigs();
		}
		catch (\Exception $e)
		{
			Notify::error($e->getMessage());
			$repositories = array();
		}

		// Output the HTML
		$this->view
			->set('filters', $filters)
			->set('total', count($repositories))
			->set('repositories', $repositories)
			->display();
	}

	/**
	 * Edit or create a new repository
	 * 
	 * @return void
	 */
	public function editTask()
	{
		if (!User::authorise('core.edit', $this->_option)
		 && !User::authorise('core.create', $this->_option))
		{
			App::abort(403, "Unauthorized");
		}

		Request::setVar('hidemainmenu', 1);

		$alias = Request::getString('alias', null);

		// If no alias is given, assume we came in via addTask
		$isNew = false;
		if (empty($alias))
		{
			$isNew = true;
		}

		try
		{
			$config = ComposerHelper::getRepositoryConfigByAlias($alias);
		}
		catch (\Exception $e)
		{
			$config = null;
			$this->setError($e->getMessage());
		}

		$this->view
			->set('config', $config)
			->set('alias', $alias)
			->set('isNew', $isNew)
			->setErrors($this->getErrors())
			->display();
	}

	/**
	 * Save repository
	 *
	 * @return  void
	 */
	public function saveTask()
	{
		// Check for request forgeries
		Request::checkToken();

		$temp = new StdClass;
		$alias             = Request::getString('alias', null);
		$oldAlias          = Request::getString('oldAlias', null);
		$isNew             = Request::getString('isNew', null);
		$temp->name        = Request::getString('name', null);
		$temp->description = Request::getString('description', null);
		$temp->url         = Request::getString('url', null);
		$temp->type        = Request::getString('type', null);

		$json = json_encode($temp);

		// Check if alias was changed - remove old repository config
		if (!$isNew && $oldAlias != $alias)
		{
			ComposerHelper::removeRepository($oldAlias);
		}

		// Add the repository
		ComposerHelper::addRepository($alias, $json);

		Notify::success('Success');

		// Set the redirect
		$this->cancelTask();
	}

	/**
	 * Delete a repository
	 * 
	 * @return void
	 */
	public function removeTask()
	{
		$alias = Request::getString('alias', null);

		ComposerHelper::removeRepository($alias);

		Notify::success('Successfully deleted repository, packages will remain until uninstalled.');

		$this->cancelTask();
	}
}
