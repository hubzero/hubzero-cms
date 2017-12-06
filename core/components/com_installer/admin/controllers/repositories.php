<?php
/**
 * HUBzero CMS
 *
 * Copyright 2005-2017 HUBzero Foundation, LLC.
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
 * @copyright Copyright 2005-2017 HUBzero Foundation, LLC.
 * @license   http://www.gnu.org/licenses/gpl-2.0.html GPLv2
 */

namespace Components\Installer\Admin\Controllers;

use Hubzero\Component\AdminController;
use Components\Installer\Admin\Helpers\ComposerHelper;
use Request;
use Config;
use StdClass;
use Route;
use App;

include_once dirname(__DIR__) . '/helpers/composer.php';


/**
 * Migrations Controller
 */
class Repositories extends AdminController
{
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
		//************************************************/
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
		//************************************************/
		$repositories = ComposerHelper::getRepositoryConfigs();
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

	function editTask()
	{
		if (!User::authorise('core.edit', $this->_option)
		&& !User::authorise('core.create', $this->_option))
		{
			App::abort(403, "Unauthorized");
		}

		Request::setVar('hidemainmenu', 1);

		$alias = Request::getVar('alias', null);
		// If no alias is given, assume we came in via addTask
		$isNew = false;
		if (is_null($alias))
		{
			$isNew = true;
		}
		$config = ComposerHelper::getRepositoryConfigByAlias($alias);

		$this->view
			->set('config', $config)
			->set('alias', $alias)
			->set('isNew', $isNew)
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
		//Request::checkToken();

		$temp = new StdClass;
		$alias             = Request::getVar('alias', null);
		$oldAlias          = Request::getVar('oldAlias', null);
		$isNew             = Request::getVar('isNew', null);
		$temp->name        = Request::getVar('name', null);
		$temp->description = Request::getVar('description', null);
		$temp->url         = Request::getVar('url', null);
		$temp->type        = Request::getVar('type', null);

		$json = json_encode($temp);

		// Check if alias was changed - remove old repository config
		if (!$isNew && $oldAlias != $alias)
		{
			ComposerHelper::removeRepository($oldAlias);
		}

		// Add the repository
		ComposerHelper::addRepository($alias, $json);

		Notify::success("Success");

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
		$alias = Request::getVar('alias', null);
		ComposerHelper::removeRepository($alias);
		Notify::success("Successfully deleted repository, packages will remain until uninstalled");
		$this->cancelTask();
	}
}
