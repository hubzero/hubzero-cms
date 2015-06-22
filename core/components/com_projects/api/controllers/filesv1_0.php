<?php
/**
 * HUBzero CMS
 *
 * Copyright 2005-2011 Purdue University. All rights reserved.
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
 * @author    Alissa Nedossekina <alisa@purdue.edu>
 * @copyright Copyright 2011 Purdue University. All rights reserved.
 * @license   http://www.gnu.org/licenses/lgpl-3.0.html LGPLv3
 */

namespace Components\Projects\Api\Controllers;

use Components\Projects\Models\Project;
use Components\Projects\Helpers;
use Hubzero\Component\ApiController;
use Hubzero\Utility\Date;
use Exception;
use stdClass;
use Request;
use Route;
use Lang;

require_once(dirname(dirname(__DIR__)) . DS . 'models' . DS . 'project.php');

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

		// Check authorization
		if ((in_array($this->_task, $contentTasks) && !$this->model->access('content'))
			|| !$this->model->access('member'))
		{
			throw new Exception(Lang::txt('ALERTNOTAUTH'), 401);
		}

		// Check for local repo
		if (!$this->model->repo()->exists())
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
		$response->count   = count($files);
		$response->results = $this->_parseResults($files);

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
		$files = $this->model->repo()->filelist(array(
			'subdir'           => Request::getVar('subdir', '', 'post'),
			'files'            => $files,
			'showFullMetadata' => true,
			'getParents'       => true,
			'getChildren'      => true
			)
		);
		$response->results = $this->_parseResults($files);
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
		$response->total   = count($items);
		$response->deleted = $deleted;

		if ($this->model->repo()->getError())
		{
			$response->error = $this->model->repo()->getError();
		}

		$this->send($response);
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
		if (!empty($checked))
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
		if (!empty($folders))
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
}
