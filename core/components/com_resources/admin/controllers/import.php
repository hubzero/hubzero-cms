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

namespace Components\Resources\Admin\Controllers;

use Components\Resources\Models;
use Components\Resources\Import\Importer;
use Hubzero\Component\AdminController;
use Hubzero\User\Group;
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
		// Set any errors
		foreach ($this->getErrors() as $error)
		{
			$this->view->setError($error);
		}

		// get all imports from archive
		$importArchive = Models\Import\Archive::getInstance();
		$imports = $importArchive->imports('list', array(
			'state'   => array(1),
			'orderby' => 'created_at DESC'
		));

		// pass vars to view
		$this->view->imports = $imports;

		// Output the HTML
		$this->view
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
		$id  = (isset($ids[0])) ? $ids[0] : null;

		// get the import object
		$this->view->import = new Models\Import($id);

		// import params
		$this->view->params = new \Hubzero\Config\Registry($this->view->import->get('params'));

		// get all files in import filespace
		$this->view->files = \Filesystem::files($this->view->import->fileSpacePath(), '.');

		// get all imports from archive
		$hooksArchive = Models\Import\Hook\Archive::getInstance();
		$this->view->hooks = $hooksArchive->hooks('list', array(
			'state' => array(1)
		));

		// Get groups
		$this->view->groups = Group::find(array(
			'authorized' => 'admin',
			'fields'     => array('cn','description','published','gidNumber','type'),
			'type'       => array(1,3),
			'sortby'     => 'description'
		));

		// Set any errors
		foreach ($this->getErrors() as $error)
		{
			$this->view->setError($error);
		}

		// Output the HTML
		$this->view
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

		//inform user & redirect
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
	 * @param   integer $dryRun
	 * @return  void
	 */
	public function runTask($dryRun = 0)
	{
		// get request vars
		$ids = Request::getVar('id', array());
		$id  = (isset($ids[0])) ? $ids[0] : null;

		// are we test mode
		$this->view->dryRun = $dryRun;

		// create import model object
		$this->view->import = new Models\Import($id);

		// Set any errors
		foreach ($this->getErrors() as $error)
		{
			$this->view->setError($error);
		}

		// force layout
		$this->view
			->setLayout('run')
			->display();
	}

	/**
	 * Actually Run Import
	 * 
	 * @return   string  JSON encoded records that just got inserted or would be
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
			'processed' => $run->get('processed'),
			'total'     => $run->get('count')
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
			\Filesystem::makeDirectory($uploadPath, 0775);
		}

		// all set
		return true;
	}
}
