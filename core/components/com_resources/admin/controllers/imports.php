<?php
/**
 * @package    hubzero-cms
 * @copyright  Copyright 2005-2019 HUBzero Foundation, LLC.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Components\Resources\Admin\Controllers;

use Components\Resources\Models\Import;
use Components\Resources\Models\Import\Hook;
use Components\Resources\Models\Import\Run;
use Components\Resources\Import\Importer;
use Hubzero\Component\AdminController;
use Hubzero\User\Group;
use Filesystem;
use Session;
use Request;
use Notify;
use Lang;
use User;
use Date;
use App;

require_once dirname(dirname(__DIR__)) . '/models/import.php';

/**
 * Resource importer
 */
class Imports extends AdminController
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
		// Incoming
		$filters = array(
			'sort' => Request::getState(
				$this->_option . '.' . $this->_controller . '.sort',
				'filter_order',
				'created_at'
			),
			'sort_Dir' => Request::getState(
				$this->_option . '.' . $this->_controller . '.sortdir',
				'filter_order_Dir',
				'desc'
			)
		);

		$query = Import::all();

		$i = $query->getTableName();
		$r = Run::blank()->getTableName();

		$query->select($i . '.*');

		if ($filters['sort'] == 'ran_at')
		{
			$query->select("(SELECT MAX(" . $r . ".ran_at) FROM " . $r . " WHERE " . $r . ".import_id=" . $i . ".`id` AND " . $r . ".dry_run=0)", 'ran_at');
		}

		// get all imports from archive
		$imports = $query
			->order($filters['sort'], $filters['sort_Dir'])
			->paginated('limitstart', 'limit')
			->rows();

		// Output the HTML
		$this->view
			->set('imports', $imports)
			->set('filters', $filters)
			->setLayout('display')
			->display();
	}

	/**
	 * Edit an Import
	 *
	 * @param   object  $import
	 * @return  void
	 */
	public function editTask($import = null)
	{
		if (!User::authorise('core.edit', $this->_option)
		 && !User::authorise('core.create', $this->_option))
		{
			App::abort(403, Lang::txt('JERROR_ALERTNOAUTHOR'));
		}

		Request::setVar('hidemainmenu', 1);

		if (!($import instanceof Import))
		{
			// get request vars
			$ids = Request::getArray('id', array());
			$id  = (isset($ids[0])) ? $ids[0] : 0;

			// get the import object
			$import = Import::oneOrNew($id);
		}

		// import params
		$params = new \Hubzero\Config\Registry($import->get('params'));

		// get all files in import filespace
		$files = Filesystem::files($import->fileSpacePath(), '.');

		// get all imports from archive
		$hooks = Hook::all()
			->whereIn('state', array(1))
			->rows();

		// Get groups
		$groups = Group::find(array(
			'authorized' => 'admin',
			'fields'     => array('cn', 'description', 'published', 'gidNumber', 'type'),
			'type'       => array(1, 3),
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

		if (!User::authorise('core.edit', $this->_option)
		 && !User::authorise('core.create', $this->_option))
		{
			App::abort(403, Lang::txt('JERROR_ALERTNOAUTHOR'));
		}

		// get request vars
		$fields = Request::getArray('import', array());
		$hooks  = Request::getArray('hooks', array());
		$params = Request::getArray('params', array());
		$file   = Request::getArray('file', array(), 'FILES');

		// create import model object
		$import = Import::oneOrNew($fields['id'])->set($fields);

		// set our hooks
		$import->set('hooks', json_encode($hooks));

		// load current params
		$iparams = new \Hubzero\Config\Registry($import->get('params'));

		// bind incoming params
		$iparams->parse($params);

		// set params on import object
		$import->set('params', $iparams->toString());

		// is this a new import
		$isNew = false;
		if (!$import->get('id'))
		{
			$isNew = true;

			// set the created by/at
			$import->set('created_by', User::get('id'));
			$import->set('created_at', Date::toSql());
		}

		// do we have a data file
		/*if ($import->get('file'))
		{
			// get record count
			$importImporter = new Importer();
			$count = $importImporter->count($import);
			$import->set('count', $count);
		}*/

		// attempt to save
		if (!$import->save())
		{
			Notify::error($import->getError());
			return $this->editTask($import);
		}

		// is this a new import
		if ($isNew)
		{
			// create folder for files
			$this->_createImportFilespace($import);
		}

		// if we have a file
		if (is_array($file) && $file['size'] > 0 && $file['error'] == 0)
		{
			$ext = strtolower(Filesystem::extension($file['name']));

			if (!in_array($ext, array('csv', 'xml')))
			{
				Notify::error(Lang::txt('COM_RESOURCES_IMPORT_UNSUPPORTED_FILE_TYPE'));
				return $this->editTask();
			}

			if (!is_dir($import->fileSpacePath()))
			{
				Filesystem::makeDirectory($import->fileSpacePath());
			}

			move_uploaded_file($file['tmp_name'], $import->fileSpacePath() . DS . $file['name']);

			$import->set('file', $file['name']);
		}

		// do we have a data file
		if ($import->get('file'))
		{
			// get record count
			$importImporter = new Importer();
			$count = $importImporter->count($import);
			$import->set('count', $count);
		}

		// save again with import count
		if (!$import->save())
		{
			Notify::error($import->getError());
			return $this->editTask($import);
		}

		// Inform user & redirect
		Notify::success(Lang::txt('COM_RESOURCES_IMPORT_CREATED'));

		$this->cancelTask();
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

		if (!User::authorise('core.delete', $this->_option))
		{
			App::abort(403, Lang::txt('JERROR_ALERTNOAUTHOR'));
		}

		// get request vars
		$ids = Request::getArray('id', array());

		// loop through all ids posted
		$success = 0;
		foreach ($ids as $id)
		{
			$resourceImport = Import::oneOrFail($id);

			// attempt to delete import
			if (!$resourceImport->destroy())
			{
				Notify::error($resourceImport->getError());
				continue;
			}

			$success++;
		}

		// inform user & redirect
		if ($success)
		{
			Notify::success(Lang::txt('COM_RESOURCES_IMPORT_REMOVED'));
		}

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
	 * @param   integer  $dryRun  Run in test mode?
	 * @return  void
	 */
	public function runTask($dryRun = 0)
	{
		// get request vars
		$ids = Request::getArray('id', array());
		$id  = (isset($ids[0])) ? $ids[0] : 0;

		// create import model object
		$import = Import::oneOrFail($id);

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
	 * @return  void  echos JSON encoded records that just got inserted or would be
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
		$import = Import::oneOrFail($id);

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
	 * @return  void  echos JSON encoded total and position
	 */
	public function progressTask()
	{
		// get request vars
		$id = Request::getInt('id', 0);

		// create import model object
		$import = Import::oneOrFail($id);

		// get the lastest run
		$run = $import->runs()
			->order('id', 'desc')
			->rows()
			->first();

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
	 * @param   string  $type    Hook we want
	 * @param   object  $import  Resource Import Model
	 * @return  array
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
			$importHook = Hook::one($hook);

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
	private function _createImportFilespace(\Components\Resources\Models\Import $import)
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
