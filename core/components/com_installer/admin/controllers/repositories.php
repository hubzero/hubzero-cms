<?php
/**
 * @package    hubzero-cms
 * @copyright  Copyright 2005-2019 HUBzero Foundation, LLC.
 * @license    http://opensource.org/licenses/MIT MIT
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
