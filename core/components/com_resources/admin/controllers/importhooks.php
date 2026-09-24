<?php
/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Components\Resources\Admin\Controllers;

use Components\Resources\Models\Import\Hook;
use Components\Resources\Import\Importer;
use Hubzero\Component\AdminController;
use Request;
use Notify;
use User;
use Date;
use Lang;
use App;

require_once dirname(dirname(__DIR__)) . DS . 'models' . DS . 'import' . DS . 'hook.php';

/**
 * Resource importer hooks
 */
class ImportHooks extends AdminController
{
	/**
	 * Executes a task
	 *
	 * @return  void
	 */
	public function execute()
	{
		$this->registerTask('add', 'edit');
		$this->registerTask('apply', 'save');

		parent::execute();
	}

	/**
	 * Display imports
	 *
	 * @return  void
	 */
	public function displayTask()
	{
		// Incoming
		$filters = array(
			'sort' => Request::getState(
				$this->_option . '.hooks.sort',
				'filter_order',
				'type'
			),
			'sort_Dir' => Request::getState(
				$this->_option . '.hooks.sortdir',
				'filter_order_Dir',
				'ASC'
			)
		);

		// get all imports from archive
		$hooks = Hook::all()
			->ordered('filter_order', 'filter_order_Dir')
			->paginated('limitstart', 'limit')
			->rows();

		// Output the HTML
		$this->view
			->set('hooks', $hooks)
			->set('filters', $filters)
			->setLayout('display')
			->display();
	}

	/**
	 * Edit an Import
	 *
	 * @param   object  $hook
	 * @return  void
	 */
	public function editTask($hook = null)
	{
		if (!User::authorise('core.edit', $this->_option)
		 && !User::authorise('core.create', $this->_option))
		{
			App::abort(403, Lang::txt('JERROR_ALERTNOAUTHOR'));
		}

		Request::setVar('hidemainmenu', 1);

		if (!($hook instanceof Hook))
		{
			// get request vars
			$id = Request::getArray('id', array(0));
			if (is_array($id))
			{
				$id = (!empty($id)) ? $id[0] : 0;
			}

			// get the import object
			$hook = Hook::oneOrNew($id);
		}

		// Output the HTML
		$this->view
			->set('hook', $hook)
			->setLayout('edit')
			->display();
	}

	/**
	 * Save an Import
	 *
	 * @return  void
	 */
	public function saveTask()
	{
		// check token
		Request::checkToken();

		if (!User::authorise('core.edit', $this->_option)
		 && !User::authorise('core.create', $this->_option))
		{
			App::abort(403, Lang::txt('JERROR_ALERTNOAUTHOR'));
		}

		// get request vars
		$data = Request::getArray('hook', array());
		$file = Request::getArray('file', array(), 'FILES');

		// The stored file name is written by the upload branch below and nowhere
		// else. Binding it from the request let a POST with no upload at all set
		// hook[file] to a traversing path, which rawTask() then reads and prints.
		unset($data['file']);

		// The id decides the upload DIRECTORY: fileSpacePath() concatenates it
		// onto PATH_APP/site/resources/import/hooks, and Request::getArray()
		// filters nothing. hook[id]=../../../../config walked the
		// move_uploaded_file() target below out of the hook filespace and into
		// PATH_APP/config, and save() did not stand in the way -- a non-numeric
		// id leaves isNew() false, so the UPDATE matched no rows and still
		// returned success. basename() on the uploaded name does not help here,
		// because the traversal is in the directory rather than the name.
		// is_array first: PHP casts a non-empty array to int(1) with no diagnostic,
		// so hook[id][]=99 would silently rewrite hook #1.
		$data['id'] = (isset($data['id']) && !is_array($data['id'])) ? (int) $data['id'] : 0;

		// create hook model object
		$hook = Hook::oneOrNew($data['id'])->set($data);

		// is this a new import
		$isNew = false;
		if (!$hook->get('id'))
		{
			$isNew = true;

			// set the created by/at
			$hook->set('created_by', User::get('id'));
			$hook->set('created', Date::toSql());
		}

		// attempt to save
		if (!$hook->save())
		{
			Notify::error($hook->getError());
			return $this->editTask($hook);
		}

		// is this a new import
		if ($isNew)
		{
			// create folder for files
			$uploadPath = $hook->fileSpacePath();

			// if we dont have a filespace, create it
			if (!is_dir($uploadPath))
			{
				\Filesystem::makeDirectory($uploadPath, 0775);
			}
		}

		// if we have a file
		if (!empty($file['size']) && isset($file['error']) && $file['error'] == 0)
		{
			// Keep the upload inside the hook's filespace - strip any path parts
			$file['name'] = basename($file['name']);

			move_uploaded_file($file['tmp_name'], $hook->fileSpacePath() . DS . $file['name']);

			$hook->set('file', $file['name']);
			$hook->save();
		}

		// Inform user & redirect
		Notify::success(Lang::txt('COM_RESOURCES_IMPORTHOOK_CREATED'));

		$this->cancelTask();
	}

	/**
	 * Show Raw immport hook file
	 *
	 * @return  void
	 */
	public function rawTask()
	{
		if (!User::authorise('core.edit', $this->_option)
		 && !User::authorise('core.create', $this->_option))
		{
			App::abort(403, Lang::txt('JERROR_ALERTNOAUTHOR'));
		}

		// get request vars
		$id = Request::getArray('id', array());
		if (is_array($id))
		{
			$id = (!empty($id)) ? $id[0] : 0;
		}

		// create hook model object
		$hook = Hook::oneOrFail($id);

		// get path to file -- basename again here, so a row stored before this
		// change cannot still walk out of the hook's filespace
		$file = $hook->fileSpacePath() . DS . basename((string) $hook->get('file'));

		// default contents
		$contents = '';

		// if we have a file
		if (file_exists($file))
		{
			// get contents of file
			$contents = file_get_contents($file);
		}

		// output contents of hook file
		highlight_string($contents);
		exit();
	}

	/**
	 * Delete Import
	 *
	 * @return  void
	 */
	public function removeTask()
	{
		// check token
		Request::checkToken();

		if (!User::authorise('core.delete', $this->_option))
		{
			App::abort(403, Lang::txt('JERROR_ALERTNOAUTHOR'));
		}

		// get request vars
		$ids = Request::getArray('id', array());
		$ids = (!is_array($ids) ? array($ids) : $ids);

		// loop through all ids posted
		$removed = 0;
		foreach ($ids as $id)
		{
			// make sure we have an object
			$hook = Hook::oneOrFail($id);

			if (!$hook->destroy())
			{
				Notify::error($hook->getError());
				continue;
			}

			$removed++;
		}

		if ($removed)
		{
			Notify::success(Lang::txt('COM_RESOURCES_IMPORTHOOK_REMOVED'));
		}

		// inform user & redirect
		$this->cancelTask();
	}
}
