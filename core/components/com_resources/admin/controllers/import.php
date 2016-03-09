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
 * @author    Shawn Rice <zooley@purdue.edu>
 * @copyright Copyright 2005-2015 HUBzero Foundation, LLC.
 * @license   http://opensource.org/licenses/MIT MIT
 */

namespace Components\Resources\Admin\Controllers;

use Components\Resources\Models;
use Components\Resources\Import\Importer;
use Hubzero\Component\AdminController;
use Hubzero\User\Group;
use Filesystem;
use Session;
use Request;
use Route;
use Lang;
use User;
use Date;
use App;

/**
 * Resource importer
 */
class Import extends AdminController
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
	 * Display imports
	 *
	 * @return  void
	 */
	public function displayTask()
	{
		// get all imports from archive
		$archive = Models\Import\Archive::getInstance();

		$imports = $archive->imports('list', array(
			'state'   => array(1),
			'orderby' => 'created_at DESC'
		));

		// Output the HTML
		$this->view
			->set('imports', $imports)
			->setLayout('display')
			->display();
	}

	/**
	 * Edit an Import
	 *
	 * @return  void
	 */
	public function editTask()
	{
		Request::setVar('hidemainmenu', 1);

		// get request vars
		$ids = Request::getVar('id', array());
		$id  = (isset($ids[0])) ? $ids[0] : 0;

		// get the import object
		$import = new Models\Import($id);

		// import params
		$params = new \Hubzero\Config\Registry($import->get('params'));

		// get all files in import filespace
		$files = Filesystem::files($import->fileSpacePath(), '.');

		// get all imports from archive
		$hooksArchive = Models\Import\Hook\Archive::getInstance();
		$hooks = $hooksArchive->hooks('list', array(
			'state' => array(1)
		));

		// Get groups
		$groups = Group::find(array(
			'authorized' => 'admin',
			'fields'     => array('cn','description','published','gidNumber','type'),
			'type'       => array(1,3),
			'sortby'     => 'description'
		));

		// Output the HTML
		$this->view
			->set('import', $import)
			->set('params', $params)
			->set('files', $files)
			->set('hooks', $hooks)
			->set('groups', $groups)
			->setErrors($this->getErrors())
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
		Session::checkToken();

		// get request vars
		$import = Request::getVar('import', array());
		$hooks  = Request::getVar('hooks', array());
		$params = Request::getVar('params', array());
		$file   = Request::getVar('file', array(), 'FILES');

		// create import model object
		$this->import = new Models\Import();

		// set our hooks
		$this->import->set('hooks', json_encode($hooks));

		// load current params
		$iparams = new \Hubzero\Config\Registry($this->import->get('params'));

		// bind incoming params
		$iparams->parse($params);

		// set params on import object
		$this->import->set('params', $iparams->toString());

		// bind input to model
		if (!$this->import->bind($import))
		{
			$this->setError($this->import->getError());
			return $this->editTask();
		}

		// is this a new import
		$isNew = false;
		if (!$this->import->get('id'))
		{
			$isNew = true;

			// set the created by/at
			$this->import->set('created_by', User::get('id'));
			$this->import->set('created_at', Date::toSql());
		}

		// do we have a data file
		if ($this->import->get('file'))
		{
			// get record count
			$importImporter = new Importer();
			$count = $importImporter->count($this->import);
			$this->import->set('count', $count);
		}

		// attempt to save
		if (!$this->import->store(true))
		{
			$this->setError($this->import->getError());
			return $this->editTask();
		}

		// is this a new import
		if ($isNew)
		{
			// create folder for files
			$this->_createImportFilespace($this->import);
		}

		// if we have a file
		if (is_array($file) && $file['size'] > 0 && $file['error'] == 0)
		{
			$ext = strtolower(Filesystem::extension($file['name']));

			if (!in_array($ext, array('csv', 'xml')))
			{
				$this->setError(Lang::txt('COM_RESOURCES_IMPORT_UNSUPPORTED_FILE_TYPE'));
				return $this->editTask();
			}

			if (!is_dir($this->import->fileSpacePath()))
			{
				Filesystem::makeDirectory($this->import->fileSpacePath());
			}

			move_uploaded_file($file['tmp_name'], $this->import->fileSpacePath() . DS . $file['name']);

			$this->import->set('file', $file['name']);
		}

		// do we have a data file
		if ($this->import->get('file'))
		{
			// get record count
			$importImporter = new Importer();
			$count = $importImporter->count($this->import);
			$this->import->set('count', $count);
		}

		// save again with import count
		if (!$this->import->store(true))
		{
			$this->setError($this->import->getError());
			return $this->editTask();
		}

		//inform user & redirect
		App::redirect(
			Route::url('index.php?option=' . $this->_option . '&controller=' . $this->_controller . '&task=display', false),
			Lang::txt('COM_RESOURCES_IMPORT_CREATED'),
			'passed'
		);
	}

	/**
	 * Delete Import
	 *
	 * @return  void
	 */
	public function removeTask()
	{
		// check token
		Session::checkToken();

		// get request vars
		$ids = Request::getVar('id', array());

		// loop through all ids posted
		foreach ($ids as $id)
		{
			// make sure we have an object
			if (!$resourceImport = new Models\Import($id))
			{
				continue;
			}

			// attempt to delete import
			if (!$resourceImport->delete())
			{
				App::redirect(
					Route::url('index.php?option=' . $this->_option . '&controller=' . $this->_controller . '&task=display', false),
					$resourceImport->getError(),
					'error'
				);
				return;
			}
		}

		// inform user & redirect
		App::redirect(
			Route::url('index.php?option=' . $this->_option . '&controller=' . $this->_controller . '&task=display', false),
			Lang::txt('COM_RESOURCES_IMPORT_REMOVED'),
			'passed'
		);
	}

	/**
	 * Run Import as Dry Run
	 *
	 * @return  void
	 */
	public function runTestTask()
	{
		$this->runTask(1);
	}

	/**
	 * Run Import
	 *
	 * @param   integer  $dryRun  Run in test mode?
	 * @return  void
	 */
	public function runTask($dryRun = 0)
	{
		// get request vars
		$ids = Request::getVar('id', array());
		$id  = (isset($ids[0])) ? $ids[0] : 0;

		// create import model object
		$import = new Models\Import($id);

		// force layout
		$this->view
			->set('dryRun', $dryRun)
			->set('import', $import)
			->setErrors($this->getErrors())
			->setLayout('run')
			->display();
	}

	/**
	 * Actually Run Import
	 * 
	 * @return  string  JSON encoded records that just got inserted or would be
	 */
	public function doRunTask()
	{
		// check token
		Session::checkToken();

		// start of import
		$start = microtime(true);

		// get request vars
		$id = Request::getInt('id', 0);

		// test mode
		$dryRun = Request::getBool('dryrun', 0);

		// create import model object
		$import = new Models\Import($id);

		// make import importer
		$importImporter = Importer::getInstance();

		// run process task on importer
		// passed the import model, array or callbacks, and test mode flag
		$resourceData = $importImporter->process($import, array(
			'postparse'   => $this->_hooks('postparse', $import),
			'postmap'     => $this->_hooks('postmap', $import),
			'postconvert' => $this->_hooks('postconvert', $import)
		), $dryRun);

		// calculate execution time
		$end  = microtime(true);
		$time = round($end - $start, 3);

		// outputted with html entities to allow browser json formatter
		if (Request::getInt('format', 0) == 1)
		{
			echo htmlentities(json_encode(array(
				'import'  => 'success',
				'time'    => $time,
				'records' => $resourceData
			)));
			exit();
		}

		// return results to user
		echo json_encode(array(
			'import'  => 'success',
			'time'    => $time,
			'records' => $resourceData
		));
		exit();
	}

	/**
	 * Get progress of import task
	 * 
	 * @return  string  JSON encoded total and position
	 */
	public function progressTask()
	{
		// get request vars
		$id = Request::getInt('id', 0);

		// create import model object
		$import = new Models\Import($id);

		// get the lastest run
		$run = $import->runs('current');

		// build array of data to return
		$data = array(
			'processed' => (int) $run->get('processed'),
			'total'     => (int) $run->get('count')
		);

		// return progress update
		echo json_encode($data);
		exit();
	}

	/**
	 * Return Hook for Post Parsing or Post Convert
	 *
	 * @param   string   $type    Hook we want
	 * @param   object   $import  Resource Import Model
	 * @return  Closure
	 */
	private function _hooks($type, $import)
	{
		// array to hold callbacks
		$callbacks = array();

		// get hooks on import
		$hooks = json_decode($import->get('hooks'));

		// make sure we have this type of hook
		if (!isset($hooks->$type))
		{
			return $callbacks;
		}

		// loop through each hook
		foreach ($hooks->$type as $hook)
		{
			// load hook object
			$importHook = new Models\Import\Hook($hook);

			// make sure we have an object
			if (!$importHook)
			{
				continue;
			}

			// build path to script
			$hookFile = $importHook->fileSpacePath() . DS . $importHook->get('file');

			// make sure we have a file
			if (!is_file($hookFile))
			{
				continue;
			}

			// add callback
			$callbacks[] = function($data, $dryRun) use ($hookFile)
			{
				return include $hookFile;
			};
		}

		// return closures as callbacks
		return $callbacks;
	}

	/**
	 * Method to create import filespace if needed
	 *
	 * @param   object   $import  Models\Import
	 * @return  boolean
	 */
	private function _createImportFilespace(Models\Import $import)
	{
		// upload path
		$uploadPath = $import->fileSpacePath();

		// if we dont have a filespace, create it
		if (!is_dir($uploadPath))
		{
			Filesystem::makeDirectory($uploadPath, 0775);
		}

		// all set
		return true;
	}
}
