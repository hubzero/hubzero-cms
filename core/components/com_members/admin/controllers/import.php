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

namespace Components\Members\Admin\Controllers;

use Components\Members\Helpers\Permissions;
use Hubzero\Component\AdminController;
use Filesystem;
use Request;
use Config;
use Route;
use User;
use Date;
use Lang;
use App;

// No direct access
defined('_HZEXEC_') or die();

include_once (dirname(dirname(__DIR__)) . DS . 'models' . DS . 'import.php');

/**
 * Member importer
 */
class Import extends AdminController
{
	/**
	 * Determine task and execute it
	 *
	 * @return  void
	 */
	public function execute()
	{
		if (!Permissions::getActions('component')->get('core.admin'))
		{
			App::redirect(
				Route::url('index.php?option=com_members', false),
				Lang::txt('Not authorized'),
				'warning'
			);
		}

		parent::execute();
	}

	/**
	 * Display imports
	 *
	 * @return  void
	 */
	public function displayTask()
	{
		// Get filters
		$this->view->filters = array(
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
			),
			'state'    => array(1),
			'sort'     => 'created_at',
			'sort_Dir' => 'DESC',
			'type'     => 'members'
		);

		// get all imports from archive
		$archive = \Hubzero\Content\Import\Model\Archive::getInstance();

		$this->view->total   = $archive->imports('count', $this->view->filters);
		$this->view->imports = $archive->imports('list', $this->view->filters);

		// Output the HTML
		$this->view
			->setLayout('display')
			->display();
	}

	/**
	 * Add an Import
	 *
	 * @return  void
	 */
	public function addTask()
	{
		$this->editTask();
	}

	/**
	 * Edit an Import
	 *
	 * @param   object  $row  \Members\Models\Import
	 * @return  void
	 */
	public function editTask($row=null)
	{
		Request::setVar('hidemainmenu', 1);

		// get the import object
		if (!($row instanceof \Components\Members\Models\Import))
		{
			// get request vars
			$id = Request::getVar('id', array(0));
			if (is_array($id))
			{
				$id = (isset($id[0]) ? $id[0] : 0);
			}

			$row = new \Components\Members\Models\Import($id);
		}

		$this->view->import = $row;

		// import params
		$this->view->params = new \Hubzero\Config\Registry($this->view->import->get('params'));

		// get all files in import filespace
		if ($this->view->import->exists())
		{
			if ($this->_createImportFilespace($this->view->import))
			{
				$this->view->files = Filesystem::files($this->view->import->fileSpacePath(), '.');
			}
		}

		// get all imports from archive
		$hooksArchive = \Hubzero\Content\Import\Model\Hook\Archive::getInstance();
		$this->view->hooks = $hooksArchive->hooks('list', array(
			'state' => array(1)
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
	 * @param   boolean  $redirect  Redirect after save?
	 * @return  void
	 */
	public function applyTask()
	{
		$this->saveTask(false);
	}

	/**
	 * Save an Import
	 *
	 * @return  void
	 */
	public function saveTask($redirect=true)
	{
		// check token
		Session::checkToken();

		// Get request vars
		$import = Request::getVar('import', array());
		$hooks  = Request::getVar('hooks', array());
		$params = Request::getVar('params', array());
		$fields = Request::getVar('mapping', array());
		$file   = Request::getVar('file', array(), 'FILES');

		// Create import model object
		$this->import = new \Components\Members\Models\Import(isset($import['id']) ? $import['id'] : null);

		// Set our hooks
		$this->import->set('hooks', json_encode($hooks));

		// Set our fields
		$this->import->set('fields', json_encode($fields));

		// Load current params
		$iparams = new \Hubzero\Config\Registry($this->import->get('params'));

		// Bind incoming params
		$iparams->parse($params);

		// Set params on import object
		$this->import->set('params', $iparams->toString());

		// Bind input to model
		if (!$this->import->bind($import))
		{
			$this->setError($this->import->getError());
			return $this->editTask($this->import);
		}

		// Is this a new import?
		$isNew = false;
		if (!$this->import->exists())
		{
			$isNew = true;

			// Set the created by/at
			$this->import->set('created_by', User::get('id'));
			$this->import->set('created_at', Date::toSql());
		}

		// Do we have a data file
		/*if ($this->import->get('file'))
		{
			// Get record count
			$importImporter = new \Hubzero\Content\Importer();
			$count = $importImporter->count($this->import);
			$this->import->set('count', $count);
		}*/

		// Attempt to save
		if (!$this->import->store(true))
		{
			$this->setError($this->import->getError());
			return $this->editTask();
		}

		// create folder for files
		if (!$this->_createImportFilespace($this->import))
		{
			return $this->editTask();
		}

		// If we have a file
		if (is_array($file) && !empty($file) && $file['size'] > 0 && $file['error'] == 0)
		{
			move_uploaded_file($file['tmp_name'], $this->import->fileSpacePath() . DS . $file['name']);
			$this->import->set('file', $file['name']);
			$this->import->set('fields', '');

			// Force into the field map view
			$isNew = true;
		}

		// Do we have a data file?
		if ($this->import->get('file'))
		{
			// get record count
			$importImporter = new \Hubzero\Content\Importer();
			$count = $importImporter->count($this->import);
			$this->import->set('count', $count);
		}

		// Save again with import count
		if (!$this->import->store(true))
		{
			$this->setError($this->import->getError());
			return $this->editTask($this->import);
		}

		// Inform user & redirect
		if ($redirect)
		{
			if ($isNew)
			{
				$this->view
					->set('import', $this->import)
					->setLayout('fields')
					->display();
				return;
			}

			App::redirect(
				Route::url('index.php?option=' . $this->_option . '&controller=' . $this->_controller, false),
				Lang::txt('COM_MEMBERS_IMPORT_CREATED'),
				'passed'
			);
			return;
		}

		$this->editTask($this->import);
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
		$ids = (!is_array($ids) ? array($ids) : $ids);

		// loop through all ids posted
		foreach ($ids as $id)
		{
			// make sure we have an object
			if (!$resourceImport = new \Components\Members\Models\Import($id))
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
			Lang::txt('COM_MEMBERS_IMPORT_REMOVED'),
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
	 * @param   integer  $dryRun
	 * @return  void
	 */
	public function runTask($dryRun = 0)
	{
		// Get request vars
		$id = Request::getVar('id', array(0));
		$id = (is_array($id) ? $id[0] : $id);

		// Are we test mode
		$this->view->dryRun = $dryRun;

		// Create import model object
		$this->view->import = new \Components\Members\Models\Import($id);

		if (!$this->view->import->exists())
		{
			return $this->cancelTask();
		}

		// Set any errors
		foreach ($this->getErrors() as $error)
		{
			$this->view->setError($error);
		}

		// Output the HTML
		$this->view
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
		// Check token
		//Session::checkToken();

		// Start of import
		$start = microtime(true);

		// Get request vars
		$id = Request::getInt('id', 0);

		// Test mode
		$dryRun = Request::getBool('dryrun', 0);

		// Create import model object
		$import = new \Components\Members\Models\Import($id);

		// Make import importer
		$importImporter = \Hubzero\Content\Importer::getInstance();

		// Run process task on importer
		// passed the import model, array or callbacks, and test mode flag
		$resourceData = $importImporter->process($import, array(
			'postparse'   => $this->_hooks('postparse',   $import),
			'postmap'     => $this->_hooks('postmap',     $import),
			'postconvert' => $this->_hooks('postconvert', $import)
		), $dryRun);

		// Calculate execution time
		$end  = microtime(true);
		$time = round($end - $start, 3);

		// Outputted with html entities to allow browser json formatter
		if (Request::getInt('format', 0) == 1)
		{
			echo htmlentities(json_encode(array(
				'import'  => 'success',
				'time'    => $time,
				'records' => $resourceData
			)));
			exit();
		}

		// Return results to user
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
		$import = new \Components\Members\Models\Import($id);

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
	 * @param   string  $event   Hook we want
	 * @param   object  $import  Import Model
	 * @return  object  Closure
	 */
	private function _hooks($event, $import)
	{
		// Array to hold callbacks
		$callbacks = array();

		// Get hooks on import
		$hooks = json_decode($import->get('hooks'));

		// Make sure we have this type of hook
		if (!isset($hooks->$event))
		{
			return $callbacks;
		}

		// Loop through each hook
		foreach ($hooks->$event as $hook)
		{
			// Load hook object
			$importHook = new \Hubzero\Content\Import\Model\Hook($hook);

			// Make sure we have an object
			if (!$importHook)
			{
				continue;
			}

			// Build path to script
			$hookFile = $importHook->fileSpacePath() . DS . $importHook->get('file');

			// Make sure we have a file
			if (!is_file($hookFile))
			{
				continue;
			}

			// Add callback
			$callbacks[] = function($data, $dryRun) use ($hookFile)
			{
				return include $hookFile;
			};
		}

		// Return closures as callbacks
		return $callbacks;
	}

	/**
	 * Method to create import filespace if needed
	 *
	 * @param   object  $import  \Hubzero\Content\Import\Model\Import
	 * @return  boolean
	 */
	private function _createImportFilespace(\Hubzero\Content\Import\Model\Import $import)
	{
		// Upload path
		$uploadPath = $import->fileSpacePath();

		// If we dont have a filespace, create it
		if (!is_dir($uploadPath))
		{
			if (!Filesystem::makeDirectory($uploadPath))
			{
				$this->setError(Lang::txt('Failed to create target upload path "%s".', $uploadPath));
				return false;
			}
		}

		return true;
	}

	/**
	 * Quote a value for a CSV file
	 *
	 * @param   string $val
	 * @return  string
	 */
	public static function quoteCsv($val)
	{
		if (!isset($val))
		{
			return '';
		}

		if (strpos($val, "\n") !== false || strpos($val, ',') !== false)
		{
			return '"' . str_replace(array('\\', '"'), array('\\\\', '""'), $val) . '"';
		}

		return $val;
	}

	/**
	 * Quote a CSV row
	 *
	 * @param   array  $vals
	 * @return  string
	 */
	public function quoteCsvRow($vals)
	{
		return implode(',', array_map(array($this, 'quoteCsv'), $vals)) . "\n";
	}

	/**
	 * Serve up an example CSV file
	 *
	 * @return  void
	 */
	public function sampleTask()
	{
		$profile = new \Components\Members\Tables\Profile($this->database);

		$skip = array('gid', 'gidnumber', 'regIP', 'regHost', 'modifiedDate', 'proxypassword', 'loginshell', 'ftpshell', 'shadowexpire', 'params', 'proxyuidnumber');

		$fields = array();
		$row    = array();

		foreach ($profile->getProperties() as $key => $val)
		{
			if (in_array(strtolower($key), $skip))
			{
				continue;
			}
			array_push($fields, $key);
			array_push($row, 'Example');
		}

		$multi = array('interests', 'race', 'disability');

		foreach ($multi as $key)
		{
			array_push($fields, $key);
			array_push($row, 'example;example;example');
		}

		// Output header
		@ob_end_clean();

		header("Pragma: public");
		header("Cache-Control: must-revalidate, post-check=0, pre-check=0");
		header("Expires: 0");

		header("Content-Transfer-Encoding: binary");
		header('Content-type: text/comma-separated-values');
		header('Content-disposition: attachment; filename="members.csv"');

		echo $this->quoteCsvRow($fields); //array_map('ucfirst', $fields));
		echo $this->quoteCsvRow($row);
		exit;
	}
}
