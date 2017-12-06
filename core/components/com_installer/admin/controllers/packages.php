<?php
/**
 * HUBzero CMS
 *
 * Copyright 2005-2015 HUBzero Foundation, LLC.
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
 * @author    Shawn Rice <zooley@purdue.edu>
 * @copyright Copyright 2005-2015 HUBzero Foundation, LLC.
 * @license   http://www.gnu.org/licenses/gpl-2.0.html GPLv2
 */

namespace Components\Installer\Admin\Controllers;

use Hubzero\Component\AdminController;
use Components\Installer\Admin\Helpers\Cli;
use Components\Installer\Admin\Helpers\ComposerHelper;
use Request;
use Config;
use Route;
use App;

include_once dirname(__DIR__) . '/helpers/cli.php';
include_once dirname(__DIR__) . '/helpers/composer.php';


/**
 * Migrations Controller
 */
class Packages extends AdminController
{
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

		$packages = ComposerHelper::getLocalPackages();
		// Output the HTML
		$this->view
			->set('filters', $filters)
			->set('total', count($packages))
			->set('packages', $packages)
			->display();
	}

	/**
	 * Edit or create a new package
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

		$packageName = Request::getVar('packageName', '');
		$versions = ComposerHelper::findRemotePackages($packageName, '*');
		$installedPackage = ComposerHelper::findLocalPackage($packageName);

		$this->view
			->set('packageName', $packageName)
			->set('installedPackage', $installedPackage)
			->set('versions', $versions)
			->display();
	}

	/**
	 * Perform install
	 *
	 * @return  void
	 */
	public function installTask()
	{
		// Check for request forgeries
		Request::checkToken();

		$packageName    = Request::getVar('packageName', null);
		$packageVersion = Request::getVar('packageVersion', null);

		ComposerHelper::installPackage($packageName, $packageVersion);

		// Set the redirect
		$this->cancelTask();
	}

	/**
	 * Add a package to track
	 * 
	 * @return void
	 */
	public function addTask()
	{
		$availablePackages = ComposerHelper::getAvailablePackages();
		$this->view
			->set('availablePackages', $availablePackages)
			->display();
	}
}
