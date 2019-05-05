<?php
/**
 * @package    hubzero-cms
 * @copyright  Copyright 2005-2019 HUBzero Foundation, LLC.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Components\Installer\Admin\Controllers;

use Hubzero\Component\AdminController;
use Hubzero\Utility\Composer as ComposerHelper;
use Components\Installer\Admin\Helpers\Cli;
use Request;
use Config;
use Notify;
use Route;
use App;

include_once dirname(__DIR__) . '/helpers/cli.php';

/**
 * Packages Controller
 */
class Packages extends AdminController
{
	/**
	 * Execute a task
	 *
	 * @return  void
	 */
	public function execute()
	{
		if (!is_file(PATH_APP . '/composer.json'))
		{
			$view = new \Hubzero\Component\View(array(
				'base_path' => dirname(__DIR__),
				'name'      => 'warnings',
				'layout'    => 'composer'
			));
			$view->display();
			return;
		}

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
			$packages = ComposerHelper::getLocalPackages();
		}
		catch (\Exception $e)
		{
			Notify::error($e->getMessage());
			$packages = array();
		}

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
	 * @return  void
	 */
	public function editTask()
	{
		if (!User::authorise('core.edit', $this->_option)
		&& !User::authorise('core.create', $this->_option))
		{
			App::abort(403, "Unauthorized");
		}

		Request::setVar('hidemainmenu', 1);

		$packageName = Request::getString('packageName', '');

		try
		{
			$versions = ComposerHelper::findRemotePackages($packageName, '*');
			$installedPackage = ComposerHelper::findLocalPackage($packageName);
		}
		catch (\Exception $e)
		{
			$versions = array();
			$installedPackage = null;
			$this->setError($e->getMessage());
		}

		$this->view
			->set('packageName', $packageName)
			->set('installedPackage', $installedPackage)
			->set('versions', $versions)
			->setErrors($this->getErrors())
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

		$packageName    = Request::getString('packageName', null);
		$packageVersion = Request::getString('packageVersion', null);

		Cli::installPackage($packageName, $packageVersion);

		// Set the redirect
		$this->cancelTask();
	}

	/**
	 * Add a package to track
	 * 
	 * @return  void
	 */
	public function addTask()
	{
		Request::setVar('hidemainmenu', 1);

		try
		{
			$availablePackages = ComposerHelper::getAvailablePackages();
		}
		catch (\Exception $e)
		{
			$availablePackages = array();

			$this->setError($e->getMessage());
		}

		$this->view
			->set('availablePackages', $availablePackages)
			->setErrors($this->getErrors())
			->display();
	}

	/**
	 * Remove a package
	 * 
	 * @return  void
	 */
	public function removeTask()
	{
		Request::checkToken();

		$packages = Request::getArray('packages', array());

		foreach ($packages as $package)
		{
			Cli::removePackage($package);
		}

		$this->cancelTask();
	}
}
