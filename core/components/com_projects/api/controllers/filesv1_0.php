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
 * @author    Alissa Nedossekina <alisa@purdue.edu>
 * @copyright Copyright 2011-2015 HUBzero Foundation, LLC.
 * @license   http://opensource.org/licenses/MIT MIT
 */

namespace Components\Projects\Api\Controllers;

use Components\Projects\Models\Project;
use Components\Projects\Helpers;
use Hubzero\Component\ApiController;
use Hubzero\Utility\Date;
use Hubzero\Filesystem\Entity;
use Hubzero\Filesystem\Collection;
use Exception;
use stdClass;
use Request;
use Route;
use Lang;

require_once(dirname(dirname(__DIR__)) . DS . 'models' . DS . 'project.php');
require_once(dirname(dirname(__DIR__)) . DS . 'models' . DS . 'orm' . DS . 'project.php');
require_once(dirname(dirname(__DIR__)) . DS . 'models' . DS . 'orm' . DS . 'connection.php');
require_once(dirname(dirname(__DIR__)) . DS . 'models' . DS . 'orm' . DS . 'provider.php');

/**
 * API controller for the projects files
 */
class Filesv1_0 extends ApiController
{
	/**
	 * Execute a request
	 *
	 * @return  void
	 */
	public function execute()
	{
		$this->registerTask('files', 'list');
		$this->registerTask('update', 'save');
		$this->registerTask('insert', 'save');
		$this->_task = Request::getWord('task', 'list');

		// Load component language file
		Lang::load('com_projects') || Lang::load('com_projects', PATH_CORE . DS . 'components' . DS . 'com_projects' . DS . 'site');

		// Incoming
		$id = Request::getVar('id', '');

		$this->model = new Project($id);

		// Project did not load?
		if (!$this->model->exists())
		{
			throw new Exception(Lang::txt('COM_PROJECTS_PROJECT_CANNOT_LOAD'), 404);
		}

		$contentTasks = array('insert', 'update', 'delete', 'move', 'rename', 'makedirectory');

		//tasks specific to adapters
		$connectionTasks = array('upload','download','getmetadata','setmetadata');

		// Check authorization
		if ((in_array($this->_task, $contentTasks) && !$this->model->access('content'))
			|| (in_array($this->_task, $connectionTasks) && !$this->model->access('content'))
			|| !$this->model->access('member'))
		{
			throw new Exception(Lang::txt('ALERTNOTAUTH'), 401);
		}

		//connection ID
		$this->cid = Request::getVar('cid', '');

		if ((in_array($this->_task, $connectionTasks)) && !$this->cid)
		{
			throw new Exception("This action is only supported by connection adapters", 401);
		}

		//if task involves connections, get the ORM Project object
		//if there is a connection id, get an ORM Connection object as well
		if ($this->cid || ($this->_task == 'connections'))
		{
			$this->ormproj = \Components\Projects\Models\Orm\Project::oneOrFail($id);
			if ($this->cid)
			{
				$this->ormconn = \Components\Projects\Models\Orm\Connection::oneOrFail($this->cid);
			}
		}


		// Check for local repo if no connection has been specified
		if (!$this->cid && !$this->model->repo()->exists())
		{
			throw new Exception(Lang::txt('COM_PROJECTS_FILES_ERROR_NO_LOCAL_REPO'), 404);
		}

		parent::execute();
	}

	/**
	 * Get a list of project files
	 *
	 * @apiMethod GET
	 * @apiUri    /projects/{id}/files
	 * @apiParameter {
	 * 		"name":        "id",
	 * 		"description": "Project identifier (numeric ID or alias)",
	 * 		"type":        "string",
	 * 		"required":    true,
	 * 		"default":     null
	 * }
	 * @apiParameter {
	 * 		"name":          "limit",
	 * 		"description":   "Number of result to return.",
	 * 		"type":          "integer",
	 * 		"required":      false,
	 * 		"default":       25
	 * }
	 * @apiParameter {
	 * 		"name":          "limitstart",
	 * 		"description":   "Number of where to start returning results.",
	 * 		"type":          "integer",
	 * 		"required":      false,
	 * 		"default":       0
	 * }
	 * @apiParameter {
	 * 		"name":          "filter",
	 * 		"description":   "A word or phrase to search for.",
	 * 		"type":          "string",
	 * 		"required":      false,
	 * 		"default":       ""
	 * }
	 * @apiParameter {
	 * 		"name":          "subdir",
	 * 		"description":   "Directory path within project repo, if not already included in the asset file path.",
	 * 		"type":          "string",
	 * 		"required":      false,
	 *      "default":       "",
	 * 		"allowedValues": ""
	 * }
	 * @return  void
	 */
	public function listTask()
	{
		$response = new stdClass;

		if ($this->cid) //connection specific listing
		{
			$dir   = Entity::fromPath(Request::getVar('subdir','','post'), $this->ormconn->adapter());
			try
			{
				$files = $dir->listContents();
			}
			catch (Exception $e)
			{
				$files = array();
			}

			$response->results = $this->_parseFlysystemListing($files);
		}
		else
		{
			$files = $this->model->repo()->filelist(array(
				'subdir'           => Request::getVar('subdir', '', 'post'),
				'filter'           => Request::getVar('filter', '', 'post'),
				'limit'            => Request::getInt('limit', 0, 'post'),
				'start'            => Request::getInt('limitstart', 0, 'post'),
				'sortby'           => 'localpath',
				'showFullMetadata' => true,
				'getParents'       => true,
				'getChildren'      => true
				)
			);

			$response->results = $this->_parseResults($files);
		}

		$response->count   = count($files);

		if ($this->model->repo()->getError())
		{
			$response->error = $this->model->repo()->getError();
		}

		$this->send($response);
	}

	/**
	 * Get file(s) metadata
	 *
	 * @apiMethod GET
	 * @apiUri    /projects/{id}/files/get
	 * @apiParameter {
	 * 		"name":        "id",
	 * 		"description": "Project identifier (numeric ID or alias)",
	 * 		"type":        "string",
	 * 		"required":    true,
	 * 		"default":     null
	 * }
	 * @apiParameter {
	 * 		"name":          "asset",
	 * 		"description":   "Array of file/folder paths to get metadata for.",
	 * 		"type":          "array",
	 * 		"required":      true,
	 * 		"default":       ""
	 * }
	 * @apiParameter {
	 * 		"name":          "subdir",
	 * 		"description":   "Directory path within project repo, if not already included in the asset file path.",
	 * 		"type":          "string",
	 * 		"required":      false,
	 *      "default":       "",
	 * 		"allowedValues": ""
	 * }
	 * @return  void
	 */
	public function getTask()
	{
		// Incoming
		$files = Request::getVar( 'asset', array() );

		if (empty($files))
		{
			throw new Exception(Lang::txt('No asset path given'), 404);
		}

		$response = new stdClass;

		if ($this->cid) //connection specific listing
		{
			$entities = array();
			foreach ($files as $file)
			{
				try {
					$entities[] = Entity::fromPath(Request::getVar('subdir','','post') . DS . $file, $this->ormconn->adapter());
				}
				catch (Exception $e) {}
			}
			$response->results = $this->_parseFlysystemListing($entities);
		}
		else
		{
			$files = $this->model->repo()->filelist(array(
				'subdir'           => Request::getVar('subdir', '', 'post'),
				'files'            => $files,
				'showFullMetadata' => true,
				'getParents'       => true,
				'getChildren'      => true
				)
			);
			$response->results = $this->_parseResults($files);
		}
		$this->send($response);
	}

	/**
	 * Create a folder in project local repo
	 *
	 * @apiMethod GET
	 * @apiUri    /projects/{id}/files/makedirectory
	 * @apiParameter {
	 * 		"name":        "id",
	 * 		"description": "Project identifier (numeric ID or alias)",
	 * 		"type":        "string",
	 * 		"required":    true,
	 * 		"default":     null
	 * }
	 * @apiParameter {
	 * 		"name":          "directory",
	 * 		"description":   "Directory path",
	 * 		"type":          "string",
	 * 		"required":      true,
	 * 		"default":       ""
	 * }
	 * @apiParameter {
	 * 		"name":          "subdir",
	 * 		"description":   "Directory path within project repo, if not already included in the asset file path.",
	 * 		"type":          "string",
	 * 		"required":      false,
	 *      "default":       "",
	 * 		"allowedValues": ""
	 * }
	 * @return  void
	 */
	public function makedirectoryTask()
	{
		$directory = trim(Request::getVar('directory', ''));

		if (empty($directory))
		{
			throw new Exception(Lang::txt('No directory path given'), 404);
		}
		$response = new stdClass;

		if ($this->cid) //connection specific operation
		{
			$entity  = Entity::fromPath(Request::getVar('subdir','','post') . DS . $directory, $this->ormconn->adapter());
			if (!$entity->create())
			{
				$response->error = Lang::txt('Error creating directory');
			}
			else
			{
				$response->success = 1;
			}

		}
		else
		{
			// Set params
			$params = array(
				'subdir' => Request::getVar('subdir', '', 'post'),
				'newDir' => urldecode($directory)
			);

			if ($this->model->repo()->makeDirectory($params))
			{
				$response->success = 1;
			}
			if ($this->model->repo()->getError())
			{
				$response->error = $this->model->repo()->getError();
			}
		}

		$this->send($response);
	}

	/**
	 * Delete file or folder from project
	 *
	 * @apiMethod GET
	 * @apiUri    /projects/{id}/files/delete
	 * @apiParameter {
	 * 		"name":        "id",
	 * 		"description": "Project identifier (numeric ID or alias)",
	 * 		"type":        "string",
	 * 		"required":    true,
	 * 		"default":     null
	 * }
	 * @apiParameter {
	 * 		"name":          "asset",
	 * 		"description":   "Array of file paths.",
	 * 		"type":          "array",
	 * 		"required":      true,
	 * 		"default":       ""
	 * }
	 * @apiParameter {
	 * 		"name":          "folder",
	 * 		"description":   "Array of folder paths.",
	 * 		"type":          "array",
	 * 		"required":      false,
	 * 		"default":       ""
	 * }
	 * @apiParameter {
	 * 		"name":          "subdir",
	 * 		"description":   "Directory path within project repo, if not already included in the asset file path.",
	 * 		"type":          "string",
	 * 		"required":      false,
	 *      "default":       "",
	 * 		"allowedValues": ""
	 * }
	 * @return  void
	 */
	public function deleteTask()
	{
		// Incoming
		$items = $this->_sortIncoming();

		if (empty($items))
		{
			throw new Exception(Lang::txt('No asset/folder path(s) given'), 404);
		}

		$response = new stdClass;
		$deleted  = 0;

		foreach ($items as $element)
		{
			foreach ($element as $type => $item)
			{
				// Get type and item name
				break;
			}

			// Must have a name
			if (trim($item) == '')
			{
				continue;
			}


			if ($this->cid) //connection specific operation
			{
				$entity  = Entity::fromPath(Request::getVar('subdir','','post') . DS . $item, $this->ormconn->adapter());
				try
				{
					if ($entity->delete())
					{
						$deleted++;
					}
				}
				catch (Exception $e) {}
			}
			else
			{

				$params = array(
					'type'   => $type,
					'item'   => $item,
					'subdir' => Request::getVar('subdir', '', 'post')
				);

				if ($this->model->repo()->deleteItem($params))
				{
					$deleted++;
				}
			}
		}
		$response->total   = count($items);
		$response->deleted = $deleted;

		if (!$this->cid && $this->model->repo()->getError())
		{
			$response->error = $this->model->repo()->getError();
		}

		$this->send($response);
	}

	/**
	 * Move file or folder in project
	 *
	 * @apiMethod GET
	 * @apiUri    /projects/{id}/files/move
	 * @apiParameter {
	 * 		"name":        "id",
	 * 		"description": "Project identifier (numeric ID or alias)",
	 * 		"type":        "string",
	 * 		"required":    true,
	 * 		"default":     null
	 * }
	 * @apiParameter {
	 * 		"name":          "target",
	 * 		"description":   "Target directory path within project repo",
	 * 		"type":          "string",
	 * 		"required":      true,
	 *      "default":       "",
	 * 		"allowedValues": ""
	 * }
	 * @apiParameter {
	 * 		"name":          "asset",
	 * 		"description":   "Array of file paths to move.",
	 * 		"type":          "array",
	 * 		"required":      true,
	 * 		"default":       ""
	 * }
	 * @apiParameter {
	 * 		"name":          "folder",
	 * 		"description":   "Array of folder paths to move.",
	 * 		"type":          "array",
	 * 		"required":      false,
	 * 		"default":       ""
	 * }
	 * @return  void
	 */
	public function moveTask()
	{
		// Incoming
		$items = $this->_sortIncoming();

		if (empty($items))
		{
			throw new Exception(Lang::txt('No asset/folder path(s) given'), 404);
		}

		// Incoming
		$target = trim(urldecode(Request::getVar('target', '')), DS);

		$response = new stdClass;
		$moved  = 0;

		foreach ($items as $element)
		{
			foreach ($element as $type => $item)
			{
				// Get type and item name
				break;
			}

			// Must have a name
			if (trim($item) == '')
			{
				continue;
			}

			if ($this->cid)
			{
				$entity   = Entity::fromPath($item, $this->ormconn->adapter());
				try
				{
					if ($entity->move($target))
					{
						$moved++;
					}
				}
				catch (Exception $e) {}
			}
			else
			{
				$params = array(
					'type'            => $type,
					'item'            => $item,
					'targetDir'       => $target,
					'createTargetDir' => true // allow new directories
				);

				if ($this->model->repo()->moveItem($params))
				{
					$moved++;
				}
			}
		}
		$response->total = count($items);
		$response->moved = $moved;

		if (!$this->cid && $this->model->repo()->getError())
		{
			$response->error = $this->model->repo()->getError();
		}

		$this->send($response);
	}

	/**
	 * Move file or folder in project
	 *
	 * @apiMethod GET
	 * @apiUri    /projects/{id}/files/rename
	 * @apiParameter {
	 * 		"name":        "id",
	 * 		"description": "Project identifier (numeric ID or alias)",
	 * 		"type":        "string",
	 * 		"required":    true,
	 * 		"default":     null
	 * }
	 * @apiParameter {
	 * 		"name":          "type",
	 * 		"description":   "File or folder.",
	 * 		"type":          "string",
	 * 		"required":      true,
	 *      "default":       "file",
	 * 		"allowedValues": "file, folder"
	 * }
	 * @apiParameter {
	 * 		"name":          "from",
	 * 		"description":   "Name of file/folder to rename (do not include local path - use subdir param).",
	 * 		"type":          "string",
	 * 		"required":      true,
	 *      "default":       "",
	 * 		"allowedValues": ""
	 * }
	 * @apiParameter {
	 * 		"name":          "to",
	 * 		"description":   "New name for file/folder (do not include local path - use subdir param).",
	 * 		"type":          "string",
	 * 		"required":      true,
	 *      "default":       "",
	 * 		"allowedValues": ""
	 * }
	 * @apiParameter {
	 * 		"name":          "subdir",
	 * 		"description":   "Directory path within project repo.",
	 * 		"type":          "string",
	 * 		"required":      false,
	 *      "default":       "",
	 * 		"allowedValues": ""
	 * }
	 * @return  void
	 */
	public function renameTask()
	{
		$response = new stdClass;
		$response->success = 0;

		if ($this->cid)
		{
			$entity   = Entity::fromPath(Request::getVar('subdir','','post') . DS . Request::getVar('from',''), $this->ormconn->adapter());
			try
			{
				if ($entity->rename(Request::getVar('to','')))
				{
					$response->success = 1;
				}
			}
			catch (Exception $e)
			{
				$response->error = "Error renaming entity";
			}
		}
		else
		{

			// Set params
			$params = array(
				'subdir'  => Request::getVar('subdir', ''),
				'from'    => Request::getVar( 'from', ''),
				'to'      => Request::getVar( 'to', ''),
				'type'    => Request::getVar( 'type', 'file')
			);

			if ($this->model->repo()->rename($params))
			{
				$response->success = 1;
			}

			if ($this->model->repo()->getError())
			{
				$response->error = $this->model->repo()->getError();
			}
		}

		$this->send($response);
	}

	/**
	 * upload/replace a project file (only for non-default connection providers)
	 *
	 * @apiMethod POST
	 * @apiUri    /projects/{id}/files/connections/{cid}/upload
	 * @apiParameter {
	 * 		"name":        "id",
	 * 		"description": "Project identifier (numeric ID or alias)",
	 * 		"type":        "string",
	 * 		"required":    true,
	 * 		"default":     null
	 * }
	 * @apiParameter {
	 * 		"name":        "cid",
	 * 		"description": "Connection identifier (numeric ID)",
	 * 		"type":        "string",
	 * 		"required":    true,
	 * 		"default":     null
	 * }
	 * @apiParameter {
	 * 		"name":          "subdir",
	 * 		"description":   "Directory path within project filespace",
	 * 		"type":          "string",
	 * 		"required":      false,
	 *              "default":       "",
	 * 		"allowedValues": ""
	 * }
	 * @apiParameter {
	 * 		"name":          "file",
	 * 		"description":   "File contents to upload",
	 * 		"type":          "binary",
	 * 		"required":      true,
	 *              "default":       "",
	 * 		"allowedValues": ""
	 * }
	 * @return  void
	 */
	public function uploadTask()
	{
		$response = new stdClass;

		if ($this->cid) //connection specific operation
		{

			if (is_uploaded_file($_FILES["file"]["tmp_name"]))
			{
				$updateType = 'uploaded';
				$file  = Entity::fromPath(Request::getVar('subdir','','post') . DS . $_FILES["file"]["name"], $this->ormconn->adapter());
				if ($file->exists())
				{
					$updateType = 'updated';
				}
				$file->contents = file_get_contents($_FILES["file"]["tmp_name"]);
				$file->size = (int) $_FILES["file"]["size"];
				if ($file->save())
				{

					$parsedResults = array();

					// Get metadata
					$parsedResults[] = $this->ormconn->adapter()->getMetadata($file->getPath());

					$response->results = $parsedResults;
				}
				else
				{
					$response->error = "Error uploading file";
				}

			}
			else
			{
				$response->error = "No uploaded file found";
			}
		}
		else
		{
			$response->error = "Connection not specified";
		}

		$this->send($response);

	}

	/**
	 * Download file or folder from project (non-default connection providers only)
	 *
	 * @apiMethod GET
	 * @apiUri    /projects/{id}/files/connections/{cid}/download
	 * @apiParameter {
	 * 		"name":        "id",
	 * 		"description": "Project identifier (numeric ID or alias)",
	 * 		"type":        "string",
	 * 		"required":    true,
	 * 		"default":     null
	 * }
	 * @apiParameter {
	 * 		"name":        "cid",
	 * 		"description": "Connection identifier (numeric ID)",
	 * 		"type":        "string",
	 * 		"required":    true,
	 * 		"default":     null
	 * }
	 * @apiParameter {
	 * 		"name":          "asset",
	 * 		"description":   "Array of file paths.",
	 * 		"type":          "array",
	 * 		"required":      true,
	 * 		"default":       ""
	 * }
	 * @apiParameter {
	 * 		"name":          "folder",
	 * 		"description":   "Array of folder paths.",
	 * 		"type":          "array",
	 * 		"required":      false,
	 * 		"default":       ""
	 * }
	 * @apiParameter {
	 * 		"name":          "subdir",
	 * 		"description":   "Directory path within project repo, if not already included in the asset file path.",
	 * 		"type":          "string",
	 * 		"required":      false,
	 *              "default":       "",
	 * 		"allowedValues": ""
	 * }
	 * @return  void
	 */
	public function downloadTask()
	{
		$items = $this->_getCollection();

		// Check items
		if (!$items || count($items) == 0)
		{
			$this->setError(Lang::txt('PLG_PROJECTS_FILES_ERROR_NO_FILES_TO_SHOW_HISTORY'));
			return;
		}

		if (count($items) > 1)
		{
			$archive = $items->compress();
			$result  = $archive->serve('project_files_'
					. \Components\Projects\Helpers\Html::generateCode(6, 6, 0, 1, 1)
					. '.zip');

			// Delete the tmp file for serving
			$archive->delete();
		}
		else
		{
			$result = $items->first()->serve();
		}

		if (!$result)
		{
			// Should only get here on error
			throw new Exception(Lang::txt('PLG_PROJECTS_FILES_SERVER_ERROR'), 404);
		}
		else
		{
			exit;
		}

	}

	/**
	 * Insert/update a project file
	 *
	 * @apiMethod GET
	 * @apiUri    /projects/{id}/files/insert OR /projects/{id}/files/update
	 * @apiParameter {
	 * 		"name":        "id",
	 * 		"description": "Project identifier (numeric ID or alias)",
	 * 		"type":        "string",
	 * 		"required":    true,
	 * 		"default":     null
	 * }
	 * @apiParameter {
	 * 		"name":          "data_path",
	 * 		"description":   "Path to local or remote file.",
	 * 		"type":          "string",
	 * 		"required":      true,
	 * 		"default":       25
	 * }
	 * @apiParameter {
	 * 		"name":          "subdir",
	 * 		"description":   "Directory path within project repo, if not already included in the asset file path.",
	 * 		"type":          "string",
	 * 		"required":      false,
	 *      "default":       "",
	 * 		"allowedValues": ""
	 * }
	 * @return  void
	 */
	public function saveTask()
	{
		// Incoming
		$dataPath = Request::getVar( 'data_path', '', 'POST' );

		if (empty($dataPath))
		{
			throw new Exception(Lang::txt('No data path given'), 404);
		}

		// Insert file
		$response = new stdClass;
		$response->results     = $this->model->repo()->insert(
			array(
				'dataPath'    => $dataPath,
				'allowReplace'=> $this->_task == 'insert' ? false : true,
				'update'      => $this->_task == 'insert' ? false : true,
				'subdir'      => Request::getVar('subdir', '')
			)
		);

		// Parse results
		if (!empty($response->results))
		{
			$parsedResults = array();
			$names = NULL;
			foreach ($response->results as $updateType => $files)
			{
				foreach ($files as $file)
				{
					if ($updateType == 'uploaded' || $updateType == 'updated')
					{
						// Get metadata
						$parsedResults[] = $this->model->repo()->getMetadata($file, 'file');
						$names .= $names ? ', ' . $file : $file;
					}
				}
			}

			// Register event with the project
			if (!empty($parsedResults))
			{
				$updateType = $this->_task == 'insert' ? 'uploaded' : 'updated';
				// Plugin params
				$plugin_params = array(
					$this->model,
					array($updateType => $names)
				);

				Event::trigger( 'projects.onAfterUpdate', $plugin_params);
			}

			$response->results = $parsedResults;
		}
		elseif ($this->model->repo()->getError())
		{
			$response->error = $this->model->repo()->getError();
		}

		$this->send($response);
	}

	/**
	 * Get file annotation
	 *
	 * @apiMethod GET
	 * @apiUri    /projects/{id}/files/connections/{cid}/getmetadata
	 * @apiParameter {
	 * 		"name":        "id",
	 * 		"description": "Project identifier (numeric ID or alias)",
	 * 		"type":        "string",
	 * 		"required":    true,
	 * 		"default":     null
	 * }
	 * @apiParameter {
	 * 		"name":        "cid",
	 * 		"description": "Connection identifier (numeric ID or alias)",
	 * 		"type":        "string",
	 * 		"required":    true,
	 * 		"default":     null
	 * }
	 * @apiParameter {
	 * 		"name":          "asset",
	 * 		"description":   "Array of files (do not include local path - use subdir param).",
	 * 		"type":          "array",
	 * 		"required":      true,
	 * 		"default":       "",
	 * 		"allowedValues": ""
	 * }
	 * @apiParameter {
	 * 		"name":          "fields",
	 * 		"description":   "Fields to get metadata for (if empty, return all fields).",
	 * 		"type":          "array",
	 * 		"required":      false,
	 * 		"default":       "",
	 * 		"allowedValues": ""
	 * }
	 * @apiParameter {
	 * 		"name":          "subdir",
	 * 		"description":   "Directory path within project repo.",
	 * 		"type":          "string",
	 * 		"required":      false,
	 * 		"default":       "",
	 * 		"allowedValues": ""
	 * }
	 * @return  void
	 */
	public function getmetadataTask()
	{
		$response = new stdClass;
		$response->success = 0;

		$files = Request::getVar('asset',array());
		$fields   = Request::getVar( 'fields', array() );
		$response->metadata = array();

		if (is_array($files))
		{
			foreach ($files as $file)
			{
				$entity   = Entity::fromPath(Request::getVar('subdir','','post')
							. DS . $file, $this->ormconn->adapter());

				if ($entity->exists())
				{

					try
					{
						$metadata = Event::trigger('metadata.onMetadataGet', [$entity]);
						if (empty($fields))
						{
							error_log('metadata returned to API : '.print_r($metadata,true));
							$response->metadata[$file] = $metadata[0];
						}
						else
						{
							$response->metadata[$file] = $this->_filter($metadata[0],$fields);
						}
						$response->success = 1;
					}
					catch (Exception $e)
					{
						$response->error .= " Error fetching entity metadata for ".$file;
					}
				}
				else
				{
					$response->error .= " Entity ".$file." does not exist";
				}

			}
		}
		else
		{
			$response->error = "asset parameter needs to be an array of file names";
		}

		$this->send($response);
	}

	/**
	 * Set file annotation
	 *
	 * @apiMethod GET
	 * @apiUri    /projects/{id}/files/connections/{cid}/setmetadata
	 * @apiParameter {
	 * 		"name":        "id",
	 * 		"description": "Project identifier (numeric ID or alias)",
	 * 		"type":        "string",
	 * 		"required":    true,
	 * 		"default":     null
	 * }
	 * @apiParameter {
	 * 		"name":        "cid",
	 * 		"description": "Connection identifier (numeric ID or alias)",
	 * 		"type":        "string",
	 * 		"required":    true,
	 * 		"default":     null
	 * }
	 * @apiParameter {
	 * 		"name":          "asset",
	 * 		"description":   "Array of files (do not include local path - use subdir param).",
	 * 		"type":          "string",
	 * 		"required":      true,
	 * 		"default":       "",
	 * 		"allowedValues": ""
	 * }
	 * @apiParameter {
	 * 		"name":          "metadata",
	 * 		"description":   "Associative array of metadata to update.",
	 * 		"type":          "array",
	 * 		"required":      false,
	 * 		"default":       "",
	 * 		"allowedValues": ""
	 * }
	 * @apiParameter {
	 * 		"name":          "subdir",
	 * 		"description":   "Directory path within project repo.",
	 * 		"type":          "string",
	 * 		"required":      false,
	 * 		"default":       "",
	 * 		"allowedValues": ""
	 * }
	 * @return  void
	 */
	public function setmetadataTask()
	{
		$response = new stdClass;
		$response->success = 0;

		$files = Request::getVar('asset',array());
		$metadata   = Request::getVar( 'metadata', array() );

		if (is_array($files))
		{
			if (is_array($metadata))
			{
				foreach ($files as $file)
				{
					$entity   = Entity::fromPath(Request::getVar('subdir','','post')
								. DS . $file, $this->ormconn->adapter());

					if ($entity->exists())
					{

						try
						{
							$tmpoldmetadata = Event::trigger('metadata.onMetadataGet',[$entity]);
							$oldmetadata = $this->_packMetadata($tmpoldmetadata[0]);
							$error = Event::trigger('metadata.onMetadataSave',
												[$entity,array_merge($oldmetadata,$metadata)]);
							if (empty($error))
							{
								$response->success = 1;
							}
							else
							{
								$response->success = 0;
								$response->error .= " Error setting metadata for ".$file;
							}
						}
						catch (Exception $e)
						{
							$response->error .= " Error setting metadata for ".$file;
						}
					}
					else
					{
						$response->error .= " Entity ".$file." does not exist";
					}

				}
			}
			else
			{
				$response->error = "metadata parameter needs to be an associative array of fields and values";
			}
		}
		else
		{
			$response->error = "asset parameter needs to be an array of file names";
		}

		$this->send($response);
	}

	/**
	 * Get file(s) metadata for Flysystem adapters
	 *
	 * @param   array $files
	 * @return  array
	 */
	protected function _parseFlysystemListing($files)
	{
		// Get array of file metadata
		if (!empty($files))
		{
			$results = array();
			foreach ($files as $result)
			{
				// Access file metadata
				$results[] = $this->ormconn->adapter()->getMetadata($result->getPath());
			}
			return $results;
		}

		return array();
	}

	/**
	 * Get file(s) metadata
	 *
	 * @param   array $files
	 * @return  array
	 */
	protected function _parseResults($files)
	{
		// Get array of file metadata
		if (!empty($files))
		{
			$results = array();
			foreach ($files as $result)
			{
				// Access private _data container
				$results[] = $result->getData();
			}
			return $results;
		}

		return array();
	}

	/**
	 * Filter an array by another array of field names
	 *
	 * @param   array $source
	 * @param   array $filter
	 * @return  array
	 */
	protected function _filter($source,$filter)
	{
		$result = array();
		foreach ($source as $key => $value)
		{
			if (in_array($key,$filter))
			{
				$result[$key] = $value;
			}
		}

		return $result;
	}

	/**
	 * Sorts incoming file/folder data
	 *
	 * @return  array
	 */
	protected function _getCollection()
	{
		// Incoming
		$files       = $this->_prune((array) Request::getVar('asset', []));
		$directories = $this->_prune((array) Request::getVar('folder', []));
		$collection  = new Collection;

		$entities = array_merge($files, $directories);

		if (!empty($entities) && is_array($entities))
		{
			foreach ($entities as $entity)
			{
				$path = trim(Request::getVar('subdir',''), '/') . '/' . urldecode($entity);
				$collection->add(Entity::fromPath($path, $this->ormconn->adapter()));
			}
		}

		return $collection;
	}

	/**
	 * Trims vars, unsetting if empty
	 *
	 * @param   array  $vars  the variables to trim
	 * @return  array
	 **/
	private function _prune($vars)
	{
		foreach ($vars as $k => $v)
		{
			if (trim($v) == '')
			{
				unset($vars[$k]);
			}
			else
			{
				$vars[$k] = $v;
			}
		}

		return $vars;
	}

	/**
	 * Sort incoming file/folder data
	 *
	 * @return     array
	 */
	protected function _sortIncoming()
	{
		// Incoming
		$checked = Request::getVar( 'asset', array() );
		$folders = Request::getVar( 'folder', array() );

		$combined = array();
		if (!empty($checked) && is_array($checked))
		{
			foreach ($checked as $ch)
			{
				if (trim($ch) != '')
				{
					$combined[] = array('file' => urldecode($ch));
				}
			}
		}
		elseif ($file = Request::getVar( 'asset', ''))
		{
			$combined[] = array('file' => urldecode($file));
		}
		if (!empty($folders) && is_array($folders))
		{
			foreach ($folders as $f)
			{
				if (trim($f) != '')
				{
					$combined[] = array('folder' => urldecode($f));
				}
			}
		}
		elseif ($folder = Request::getVar( 'folder', ''))
		{
			$combined[] = array('folder' => urldecode($folder));
		}

		return $combined;
	}

	/**
	 * Pack metadata from onMetadataGet into form usable by onMetadataSave
	 *
	 * @param      array  $metadata  incoming metadata
	 * @return     array
	 */
	protected function _packMetadata($metadata)
	{
		$packedMetadata = array();
		if (!empty($metadata) && is_array($metadata))
		{
			foreach ($metadata as $key => $avu)
			{
				if (trim($avu['units']) != '')
				{
					$packedMetadata[$key.':'.$avu['units']] = $avu['value'];
				}
				else
				{
					$packedMetadata[$key] = $avu['value'];
				}
			}
		}

		return $packedMetadata;
	}

	/**
	 * Get a list of project files connections
	 *
	 * @apiMethod GET
	 * @apiUri    /projects/{id}/files/connections
	 * @apiParameter {
	 *              "name":        "id",
	 *              "description": "Project identifier (numeric ID or alias)",
	 *              "type":        "string",
	 *              "required":    true,
	 *              "default":     null
	 * }
	 * @return  void
	 */
	public function connectionsTask()
	{
		$response = new stdClass;
		$connections = $this->ormproj->connections()->thatICanView();
		$response->count   = count($connections);
		$response->connections = array();

		foreach ($connections as $connection)
		{
			$obj = new stdClass;
			$obj->id = $connection->get('id');
			$obj->name = $connection->get('name');
			$response->connections[] = $obj;
		}

		$this->send($response);
	}

}
