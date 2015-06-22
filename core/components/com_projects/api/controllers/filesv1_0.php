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

		// Incoming
		$id = Request::getVar('id', '');

		$this->model = new Project($id);

		// Project did not load?
		if (!$this->model->exists())
		{
			throw new Exception(Lang::txt('COM_PROJECTS_PROJECT_CANNOT_LOAD'), 404);
		}

		// Check authorization
		if (($this->_task == 'insert' && !$this->model->access('content')) || !$this->model->access('view'))
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
	 * @apiUri    /projects/{id}/list
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
		$this->send($response);
	}

	/**
	 * Insert/update a project file
	 *
	 * @apiMethod GET
	 * @apiUri    /projects/{id}/list
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
		// Insert file
		$response->results     = $this->model->repo()->insert(
			array(
				'dataPath'    => Request::getVar( 'data_path', '' ),
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
}
