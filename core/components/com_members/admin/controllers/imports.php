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

use Components\Members\Helpers\Admin;
use Components\Members\Models\Import;
use Components\Members\Models\Member;
use Components\Members\Models\Profile\Field;
use Hubzero\Component\AdminController;
use Hubzero\Content\Import\Model\Hook;
use Hubzero\Content\Importer;
use Hubzero\Config\Registry;
use Filesystem;
use Request;
use Config;
use Route;
use User;
use Date;
use Lang;
use App;


include_once (dirname(dirname(__DIR__)) . DS . 'models' . DS . 'member.php');
include_once (dirname(dirname(__DIR__)) . DS . 'models' . DS . 'profile' . DS . 'field.php');
include_once (dirname(dirname(__DIR__)) . DS . 'models' . DS . 'import.php');

/**
 * Member importer
 */
class Imports extends AdminController
{
	/**
	 * Determine task and execute it
	 *
	 * @return  void
	 */
	public function execute()
	{
		if (!Admin::getActions('component')->get('core.admin'))
		{
			App::redirect(
				Route::url('index.php?option=com_members', false),
				Lang::txt('Not authorized'),
				'warning'
			);
		}

		Lang::load($this->_option . '.import', dirname(__DIR__));

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
			),
			'state'    => array(1),
			'sort'     => 'created_at',
			'sort_Dir' => 'DESC',
			'type'     => 'members'
		);

		$model = Import::all();

		if (isset($filters['state']) && $filters['state'])
		{
			if (!is_array($filters['state']))
			{
				$filters['state'] = array($filters['state']);
			}
			$filters['state'] = array_map('intval', $filters['state']);

			$model->whereIn('state', $filters['state']);
		}

		if (isset($filters['type']) && $filters['type'])
		{
			$model->whereEquals('type', $filters['type']);
		}

		$imports = $model->ordered()
			->paginated('limitstart', 'limit')
			->rows();

		// Output the HTML
		$this->view
			->set('filters', $filters)
			->set('imports', $imports)
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
	 * @param   object  $import
	 * @return  void
	 */
	public function editTask($import = null)
	{
		Request::setVar('hidemainmenu', 1);

		// get the import object
		if (!($import instanceof Import))
		{
			// get request vars
			$id = Request::getVar('id', array(0));
			if (is_array($id))
			{
				$id = (isset($id[0]) ? $id[0] : 0);
			}

			$import = Import::oneOrNew($id);
		}

		// import params
		$params = new Registry($import->get('params'));

		// get all files in import filespace
		$files = array();

		if ($import->get('id'))
		{
			if ($this->_createImportFilespace($import))
			{
				$files = Filesystem::files($import->fileSpacePath(), '.');
			}
		}

		// get all import hooks
		$hooks = Hook::all()
			->whereIn('state', array(1))
			->rows();

		// Set any errors
		foreach ($this->getErrors() as $error)
		{
			$this->view->setError($error);
		}

		// Output the HTML
		$this->view
			->set('import', $import)
			->set('params', $params)
			->set('files', $files)
			->set('hooks', $hooks)
			->setLayout('edit')
			->display();
	}

	/**
	 * Save an Import and display edit form
	 *
	 * @return  void
	 */
	public function applyTask()
	{
		$this->saveTask(false);
	}

	/**
	 * Save an Import
	 *
	 * @param   boolean  $redirect  Redirect after save?
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
		$model = Import::oneOrNew(isset($import['id']) ? $import['id'] : 0);

		// Set our hooks
		$model->set('hooks', json_encode($hooks));

		// Set our fields
		$model->set('fields', json_encode($fields));

		// Load current params
		$iparams = new Registry($model->get('params'));

		// Bind incoming params
		$iparams->parse($params);

		// Set params on import object
		$model->set('params', $iparams->toString());

		// Bind input to model
		if (!$model->set($import))
		{
			$this->setError($model->getError());
			return $this->editTask($model);
		}

		// Is this a new import?
		$isNew = false;
		if ($model->isNew())
		{
			$isNew = true;

			// Set the created by/at
			$model->set('created_by', User::get('id'));
			$model->set('created_at', Date::toSql());
		}

		// Attempt to save
		if (!$model->save())
		{
			$this->setError($model->getError());
			return $this->editTask();
		}

		// create folder for files
		if (!$this->_createImportFilespace($model))
		{
			return $this->editTask();
		}

		// If we have a file
		if (is_array($file) && !empty($file) && $file['size'] > 0 && $file['error'] == 0)
		{
			move_uploaded_file($file['tmp_name'], $model->fileSpacePath() . DS . $file['name']);
			$model->set('file', $file['name']);
			$model->set('fields', '');

			// Force into the field map view
			$isNew = true;
		}

		// Do we have a data file?
		if ($model->get('file'))
		{
			// get record count
			$importImporter = new Importer();
			$model->set('count', $importImporter->count($model));
		}

		// Save again with import count
		if (!$model->save())
		{
			$this->setError($model->getError());
			return $this->editTask($model);
		}

		Notify::success(Lang::txt('COM_MEMBERS_IMPORT_CREATED'));

		// Inform user & redirect
		if ($redirect)
		{
			if ($isNew)
			{
				$this->view
					->set('import', $model)
					->setLayout('fields')
					->display();
				return;
			}

			return $this->cancelTask();
		}

		$this->editTask($model);
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
		$i = 0;
		foreach ($ids as $id)
		{
			// make sure we have an object
			$import = Import::oneOrFail($id);

			if (!$import->get('id'))
			{
				continue;
			}

			// attempt to delete import
			if (!$import->destroy())
			{
				Notify::error($import->getError());
				continue;
			}

			$i++;
		}

		if ($i)
		{
			Notify::success(Lang::txt('COM_MEMBERS_IMPORT_REMOVED'));
		}

		//inform user & redirect
		$this->cancelTask();
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


		// Create import model object
		$import = Import::oneOrFail($id);

		if (!$import->get('id'))
		{
			return $this->cancelTask();
		}

		// Output the HTML
		$this->view
			->set('dryRun', $dryRun)
			->set('import', $import)
			->setLayout('run')
			->setErrors($this->getErrors())
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
		$import = Import::oneOrFail($id);

		// Make import importer
		$importImporter = Importer::getInstance();

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
		$import = Import::oneOrFail($id);

		// get the lastest run
		$run = $import->runs()
			->whereEquals('import_id', $import->get('id'))
			->ordered()
			->row();

		// build array of data to return
		$data = array(
			'processed' => $run->get('processed', 0),
			'total'     => $run->get('count', 0)
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
			$importHook = Hook::oneOrNew($hook);

			// Make sure we have an object
			if (!$importHook->get('id'))
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
	 * @param   string  $val
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
		$skip = array('gid', 'gidnumber', 'regIP', 'regHost', 'modifiedDate', 'proxypassword', 'loginshell', 'ftpshell', 'shadowexpire', 'params', 'proxyuidnumber');

		$fields = array();
		$row    = array();

		$member = Member::blank();
		$attribs = $member->getStructure()->getTableColumns($member->getTableName());

		foreach ($attribs as $key => $desc)
		{
			if (in_array(strtolower($key), $skip))
			{
				continue;
			}

			$example = 'Example';
			$desc = preg_replace('/\(.*\)/', '', $desc);

			if (in_array($desc, array('int', 'tinyint', 'float')))
			{
				$example = '1';
			}

			array_push($row, $example);
			array_push($fields, $key);
		}

		$attribs = Field::all()
			->including(['options', function ($option){
				$option
					->select('*');
			}])
			->ordered()
			->rows();

		foreach ($attribs as $field)
		{
			$key = $field->get('name');

			if (in_array(strtolower($key), $skip))
			{
				continue;
			}

			$example = 'Example';
			if ($field->options->count() || in_array($field->get('type'), array('select', 'dropdown', 'list', 'radio', 'radios', 'checkbox', 'checkboxes')))
			{
				$example = 'example;example;example';
			}

			array_push($row, $example);
			array_push($fields, $key);
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
