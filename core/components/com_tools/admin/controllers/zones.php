<?php
/**
 * @package    hubzero-cms
 * @copyright  Copyright 2005-2019 HUBzero Foundation, LLC.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Components\Tools\Admin\Controllers;

use Components\Tools\Models\Middleware;
use Components\Tools\Helpers\Utils;
use Hubzero\Component\AdminController;
use Hubzero\Config\Registry;
use Filesystem;
use Request;
use Config;
use Notify;
use Route;
use Lang;
use App;

include_once dirname(dirname(__DIR__)) . DS . 'models' . DS . 'middleware.php';

/**
 * Administrative tools controller for zones
 */
class Zones extends AdminController
{
	/**
	 * Determines task being called and attempts to execute it
	 *
	 * @return	void
	 */
	public function execute()
	{
		if (!$this->config->get('zones'))
		{
			App::redirect(Route::url('index.php?option=' . $this->_option, false));
			return;
		}

		$this->registerTask('add', 'edit');
		$this->registerTask('apply', 'save');

		parent::execute();
	}

	/**
	 * Display a list of hosts
	 *
	 * @return  void
	 */
	public function displayTask()
	{
		// Get filters
		$this->view->filters = array(
			'zone' => urldecode(Request::getState(
				$this->_option . '.' . $this->_controller . '.zone',
				'zone',
				''
			)),
			'master' => urldecode(Request::getState(
				$this->_option . '.' . $this->_controller . '.master',
				'master',
				''
			)),
			// Sorting
			'sort' => Request::getState(
				$this->_option . '.' . $this->_controller . '.sort',
				'filter_order',
				'zone'
			),
			'sort_Dir' => Request::getState(
				$this->_option . '.' . $this->_controller . '.sortdir',
				'filter_order_Dir',
				'ASC'
			),
			// Get paging variables
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

		// In case limit has been changed, adjust limitstart accordingly
		$this->view->filters['start'] = ($this->view->filters['limit'] != 0 ? (floor($this->view->filters['start'] / $this->view->filters['limit']) * $this->view->filters['limit']) : 0);

		// Get the middleware
		$model = new Middleware();

		$this->view->total = $model->zones('count', $this->view->filters);

		$this->view->rows  = $model->zones('list', $this->view->filters);

		// Set any errors
		foreach ($this->getErrors() as $error)
		{
			$this->view->setError($error);
		}

		// Display results
		$this->view->display();
	}

	/**
	 * Edit a record
	 *
	 * @param   mixed  $row
	 * @return  void
	 */
	public function editTask($row=null)
	{
		Request::setVar('hidemainmenu', 1);

		// Get the middleware database
		$mwdb = Utils::getMWDBO();

		$mw = new Middleware($mwdb);

		if (!is_object($row))
		{
			// Incoming
			$id = Request::getInt('id', 0);

			$row = new Middleware\Zone($id);
		}

		$this->view->row = $row;

		if (!$this->view->row->exists())
		{
			$this->view->row->set('state', 'down');
		}

		$this->view->row->params = new Registry($this->view->row->get('params'));

		// Set any errors
		foreach ($this->getErrors() as $error)
		{
			$this->view->setError($error);
		}

		// Display results
		$this->view
			->setLayout('edit')
			->display();
	}

	/**
	 * Save changes to a record
	 *
	 * @return  void
	 */
	public function saveTask()
	{
		// Check for request forgeries
		Request::checkToken();

		// Incoming
		$fields = Request::getArray('fields', array(), 'post');
		$params = Request::getArray('zoneparams', array(), 'post');
		$row    = new Middleware\Zone($fields['id']);

		$fields['params'] = json_encode($params);

		$row = new Middleware\Zone($fields['id']);
		if (!$row->bind($fields))
		{
			Notify::error($row->getError());
			return $this->editTask($row);
		}

		// Store new content
		if (!$row->store(true))
		{
			Notify::error($row->getError());
			return $this->editTask($row);
		}

		/*$vl = new \Components\Tools\Tables\ZoneLocations($mwdb);
		$vl->deleteByZone($row->id);

		$locations = Request::getArray('locations', array(), 'post');
		foreach ($locations as $location)
		{
			$vl = new \Components\Tools\Tables\ZoneLocations($mwdb);
			$vl->zone_id = $row->id;
			$vl->location = $location;
			if (!$vl->check())
			{
				Notify::error($vl->getError());
				return $this->editTask($row);
			}
			if (!$vl->store())
			{
				Notify::error($vl->getError());
				return $this->editTask($row);
			}
		}*/
		Notify::success(Lang::txt('COM_TOOLS_ITEM_SAVED'));

		if ($this->getTask() == 'apply')
		{
			return $this->editTask($row);
		}

		App::redirect(
			Route::url('index.php?option=' . $this->_option . '&controller=' . $this->_controller, false)
		);
	}

	/**
	 * Toggle a zone's state
	 *
	 * @return     void
	 */
	public function stateTask()
	{
		// Check for request forgeries
		Request::checkToken('get');

		// Incoming
		$id = Request::getInt('id', 0);
		$state = strtolower(Request::getWord('state', 'up'));

		if ($state != 'up' && $state != 'down')
		{
			App::redirect(
				Route::url('index.php?option=' . $this->_option . '&controller=' . $this->_controller, false)
			);
			return;
		}

		// Get the middleware database
		$mwdb = Utils::getMWDBO();

		$row = new \Components\Tools\Tables\Zones($mwdb);
		if ($row->load($id))
		{
			$row->state = $state;
			if (!$row->store())
			{
				Notify::error(Lang::txt('COM_TOOLS_ERROR_STATE_UPDATE_FAILED'));
			}
		}

		App::redirect(
			Route::url('index.php?option=' . $this->_option . '&controller=' . $this->_controller, false)
		);
	}

	/**
	 * Delete one or more records
	 *
	 * @return     void
	 */
	public function removeTask()
	{
		// Check for request forgeries
		Request::checkToken();

		// Incoming
		$ids = Request::getArray('id', array());

		$mwdb = Utils::getMWDBO();

		if (count($ids) > 0)
		{
			$row = new \Components\Tools\Tables\Zones($mwdb);

			// Loop through each ID
			foreach ($ids as $id)
			{
				if (!$row->delete(intval($id)))
				{
					throw new \Exception($row->getError(), 500);
				}
			}
		}

		App::redirect(
			Route::url('index.php?option=' . $this->_option . '&controller=' . $this->_controller, false),
			Lang::txt('COM_TOOLS_ITEM_DELETED'),
			'message'
		);
	}

	/**
	 * Method to set the default property for a zone
	 *
	 * @return     void
	 */
	public function defaultTask()
	{
		// Get item to default from request
		$id = Request::getArray('id', [], '', 'array');

		if (empty($id))
		{
			App::abort(404, Lang::txt('COM_TOOLS_ERROR_MISSING_ID'));
		}

		// Get the middleware database
		$mwdb = Utils::getMWDBO();
		$row  = new \Components\Tools\Tables\Zones($mwdb);

		if ($row->load($id[0]))
		{
			// Get rid of the current default
			$default = new \Components\Tools\Tables\Zones($mwdb);
			$default->load(['is_default' => 1]);
			$default->is_default = 0;
			if (!$default->store())
			{
				App::abort(500, Lang::txt('COM_TOOLS_ERROR_DEFAULT_UPDATE_FAILED'));
			}

			// Set a new default
			$row->is_default = 1;
			if (!$row->store())
			{
				App::abort(500, Lang::txt('COM_TOOLS_ERROR_DEFAULT_UPDATE_FAILED'));
			}
		}

		App::redirect(Route::url('index.php?option=' . $this->_option . '&controller=' . $this->_controller, false));
	}

	/**
	 * Upload a file to the wiki via AJAX
	 *
	 * @return  string
	 */
	public function ajaxUploadTask()
	{
		// Check for request forgeries
		Request::checkToken(['get', 'post']);

		// Ensure we have an ID to work with
		$id = Request::getInt('id', 0);
		if (!$id)
		{
			echo json_encode(array('error' => Lang::txt('COM_TOOLS_ERROR_MISSING_ID')));
			return;
		}

		$zone = Middleware\Zone::getInstance($id);

		// Build the path
		$path = $zone->logo('path');

		if (!$path)
		{
			echo json_encode(array('error' => $this->getError()));
			return;
		}

		// allowed extensions for uplaod
		$allowedExtensions = array('png','jpeg','jpg','gif');

		// max upload size
		$sizeLimit = $this->config->get('maxAllowed', 40000000);

		// get the file
		if (isset($_GET['qqfile']))
		{
			$stream = true;
			$file = $_GET['qqfile'];
			$size = (int) $_SERVER["CONTENT_LENGTH"];
		}
		elseif (isset($_FILES['qqfile']))
		{
			$stream = false;
			$file = $_FILES['qqfile']['name'];
			$size = (int) $_FILES['qqfile']['size'];
		}
		else
		{
			echo json_encode(array('error' => Lang::txt('COM_TOOLS_ERROR_NO_FILE')));
			return;
		}

		if (!is_dir($path))
		{
			if (!Filesystem::makeDirectory($path))
			{
				echo json_encode(array('error' => Lang::txt('COM_TOOLS_ERROR_UNABLE_TO_CREATE_UPLOAD_PATH')));
				return;
			}
		}

		if (!is_writable($path))
		{
			echo json_encode(array('error' => Lang::txt('COM_TOOLS_ERROR_DIRECTORY_NOT_WRITABLE')));
			return;
		}

		// check to make sure we have a file and its not too big
		if ($size == 0)
		{
			echo json_encode(array('error' => Lang::txt('COM_TOOLS_ERROR_EMPTY_FILE')));
			return;
		}
		if ($size > $sizeLimit)
		{
			$max = preg_replace('/<abbr \w+=\\"\w+\\">(\w{1,3})<\\/abbr>/', '$1', \Hubzero\Utility\Number::formatBytes($sizeLimit));
			echo json_encode(array('error' => Lang::txt('COM_TOOLS_ERROR_FILE_TOO_LARGE', $max)));
			return;
		}

		// don't overwrite previous files that were uploaded
		$pathinfo = pathinfo($file);
		$filename = $pathinfo['filename'];

		// Make the filename safe
		$filename = urldecode($filename);
		$filename = Filesystem::clean($filename);
		$filename = str_replace(' ', '_', $filename);

		$ext = $pathinfo['extension'];
		if (!in_array(strtolower($ext), $allowedExtensions))
		{
			echo json_encode(array('error' => Lang::txt('COM_TOOLS_ERROR_INVALID_FILE_TYPE')));
			return;
		}

		$file = $path . DS . $filename . '.' . $ext;

		if ($stream)
		{
			//read the php input stream to upload file
			$input = fopen("php://input", "r");
			$temp = tmpfile();
			$realSize = stream_copy_to_stream($input, $temp);
			fclose($input);

			//move from temp location to target location which is user folder
			$target = fopen($file, "w");
			fseek($temp, 0, SEEK_SET);
			stream_copy_to_stream($temp, $target);
			fclose($target);
		}
		else
		{
			move_uploaded_file($_FILES['qqfile']['tmp_name'], $file);
		}

		// Do we have an old file we're replacing?
		if ($curfile = $zone->get('picture'))
		{
			// Remove old image
			if (file_exists($path . DS . $curfile))
			{
				if (!Filesystem::delete($path . DS . $curfile))
				{
					echo json_encode(array('error' => Lang::txt('COM_TOOLS_ERROR_UNABLE_TO_DELETE_FILE')));
					return;
				}
			}
		}

		$zone->set('picture', $filename . '.' . $ext);
		if (!$zone->store())
		{
			echo json_encode(array('error' => $zone->getError()));
			return;
		}

		$this_size = filesize($file);
		list($width, $height, $type, $attr) = getimagesize($file);

		//echo result
		echo json_encode(array(
			'success'   => true,
			'file'      => $filename . '.' . $ext,
			'directory' => str_replace(PATH_APP, '', $path),
			'id'        => $id,
			'size'      => \Hubzero\Utility\Number::formatBytes($this_size),
			'width'     => $width,
			'height'    => $height
		));
	}

	/**
	 * Upload a file
	 *
	 * @return  void
	 */
	public function uploadTask()
	{
		if (Request::getInt('no_html', 0))
		{
			return $this->ajaxUploadTask();
		}

		// Check for request forgeries
		Request::checkToken();

		// Incoming
		$id = Request::getInt('id', 0);
		if (!$id)
		{
			$this->setError(Lang::txt('COM_COURSES_NO_ID'));
			$this->pictureTask('', $id);
			return;
		}

		$zone = Middleware\Zone::getInstance($id);

		// Build the path
		$path = $zone->logo('path');

		if (!$path)
		{
			$this->pictureTask('', $id);
			return;
		}

		// Incoming file
		$file = Request::getArray('upload', '', 'files');
		if (!$file['name'])
		{
			$this->setError(Lang::txt('COM_TOOLS_ERROR_NO_FILE'));
			$this->pictureTask('', $id);
			return;
		}
		$curfile = Request::getString('curfile', '');

		if (!is_dir($path))
		{
			if (!Filesystem::makeDirectory($path))
			{
				$this->setError(Lang::txt('COM_TOOLS_ERROR_UNABLE_TO_CREATE_UPLOAD_PATH'));
				$this->pictureTask('', $id);
				return;
			}
		}

		// Make the filename safe
		$file['name'] = Filesystem::clean($file['name']);
		$file['name'] = str_replace(' ', '_', $file['name']);

		// Perform the upload
		if (!Filesystem::upload($file['tmp_name'], $path . DS . $file['name']))
		{
			$this->setError(Lang::txt('COM_TOOLS_ERROR_UPLOADING'));
			$file = $curfile;
		}
		else
		{
			// Do we have an old file we're replacing?
			if ($curfile = $zone->get('picture'))
			{
				// Remove old image
				if (file_exists($path . DS . $curfile))
				{
					if (!Filesystem::delete($path . DS . $curfile))
					{
						$this->setError(Lang::txt('COM_TOOLS_ERROR_UNABLE_TO_DELETE_FILE'));
						$this->pictureTask($file['name'], $id);
						return;
					}
				}
			}

			$zone->set('picture', $file['name']);
			if (!$zone->store())
			{
				$this->setError($zone->getError());
			}

			$file = $file['name'];
		}

		// Push through to the image view
		$this->pictureTask($file, $id);
	}

	/**
	 * Upload a file to the wiki via AJAX
	 *
	 * @return  string
	 */
	public function ajaxRemoveTask()
	{
		// Check for request forgeries
		Request::checkToken(['get', 'post']);

		// Ensure we have an ID to work with
		$id = Request::getInt('id', 0);
		if (!$id)
		{
			echo json_encode(array('error' => Lang::txt('COM_TOOLS_ERROR_MISSING_ID')));
			return;
		}

		$zone = Middleware\Zone::getInstance($id);

		// Build the path
		$path = $zone->logo('path');
		if (!$path)
		{
			echo json_encode(array('error' => $this->getError()));
			return;
		}

		$file = $zone->get('picture');

		if (!file_exists($path . DS . $file) or !$file)
		{
			$this->setError(Lang::txt('COM_TOOLS_ERROR_FILE_NOT_FOUND'));
		}
		else
		{
			// Attempt to delete the file
			if (!Filesystem::delete($path . DS . $file))
			{
				echo json_encode(array('error' => Lang::txt('COM_TOOLS_ERROR_UNABLE_TO_DELETE_FILE')));
				return;
			}
		}

		// Instantiate a model, change some info and save
		$zone->set('picture', '');
		if (!$zone->store())
		{
			echo json_encode(array('error' => $zone->getError()));
			return;
		}

		//echo result
		echo json_encode(array(
			'success'   => true,
			'file'      => '',
			'directory' => str_replace(PATH_APP, '', $path),
			'id'        => $id,
			'size'      => 0,
			'width'     => 0,
			'height'    => 0
		));
	}

	/**
	 * Delete a file
	 *
	 * @return  void
	 */
	public function removefileTask()
	{
		if (Request::getInt('no_html', 0))
		{
			return $this->ajaxRemoveTask();
		}

		// Check for request forgeries
		Request::checkToken('get');

		// Incoming member ID
		$id = Request::getInt('id', 0);
		if (!$id)
		{
			$this->setError(Lang::txt('COM_TOOLS_ERROR_MISSING_ID'));
			$this->pictureTask('', $id);
			return;
		}

		$zone = Middleware\Zone::getInstance($id);

		// Build the file path
		$path = $zone->logo('path');
		$file = $zone->get('picture');

		if (!file_exists($path . DS . $file) or !$file)
		{
			$this->setError(Lang::txt('COM_TOOLS_ERROR_FILE_NOT_FOUND'));
		}
		else
		{
			// Attempt to delete the file
			if (!Filesystem::delete($path . DS . $file))
			{
				$this->setError(Lang::txt('COM_TOOLS_ERROR_UNABLE_TO_DELETE_FILE'));
				$this->pictureTask($file, $id);
				return;
			}

			$zone->set('picture', '');
			if (!$zone->store())
			{
				$this->setError($zone->getError());
			}

			$file = '';
		}

		$this->pictureTask($file, $id);
	}

	/**
	 * Display a file and its info
	 *
	 * @param   string   $file  File name
	 * @param   integer  $id    User ID
	 * @return  void
	 */
	public function pictureTask($file='', $id=0)
	{
		// Incoming
		$id = $id ?: Request::getInt('id', 0);

		$this->view->zone = Middleware\Zone::getInstance($id);

		// Set any errors
		foreach ($this->getErrors() as $error)
		{
			$this->view->setError($error);
		}

		// Output the HTML
		$this->view
			->set('config', $this->config)
			->setLayout('picture')
			->display();
	}
}
