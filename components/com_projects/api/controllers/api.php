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

// no direct access
defined('_JEXEC') or die('Restricted access');

JLoader::import('Hubzero.Api.Controller');

/**
 * API controller for the projects component
 */
class ProjectsControllerApi extends \Hubzero\Component\ApiController
{
	/**
	 * Execute!
	 *
	 * @return void
	 */
	function execute()
	{
		// Import some Joomla libraries
		JLoader::import('joomla.environment.request');
		JLoader::import('joomla.application.component.helper');

		$this->_config   = Component::params('com_projects');
		$this->_database = JFactory::getDBO();
		$this->_action   = $this->segments[1] ? $this->segments[1] : 'list';

		// Get the user id
		$this->user_id = JFactory::getApplication()->getAuthn('user_id');

		// Switch based on entity type and action
		switch ($this->segments[0])
		{
			// Files
			case 'files':
				switch ($this->_action)
				{
					case 'list':
					case 'get':
					case 'insert':
					case 'update':
						$this->_manageFiles();
						break;
					default:
						return $this->_errorMessage(404, Lang::txt('Method unavailable'));
						break;
				}
			break;

			// Project list
			case 'list':
				$this->_projectList();
			break;

			default:
				$this->serviceTask();
			break;
		}
	}

	/**
	 * Displays a available options and parameters the API
	 * for this component offers.
	 *
	 * @return  void
	 */
	private function serviceTask()
	{
		$response = new stdClass();
		$response->component = 'projects';
		$response->tasks = array(
			'list' => array(
				'description' => Lang::txt('Get a list of projects user is a member of'),
				'parameters'  => array(
					'sortby' => array(
						'description' => Lang::txt('Field to sort results by.'),
						'type'        => 'string',
						'default'     => 'created',
						'accepts'     => array('created', 'title', 'alias', 'id', 'publish_up', 'publish_down', 'state')
					),
					'sortdir' => array(
						'description' => Lang::txt('Direction to sort results by.'),
						'type'        => 'string',
						'default'     => 'desc',
						'accepts'     => array('asc', 'desc')
					),
					'limit' => array(
						'description' => Lang::txt('Number of result to return.'),
						'type'        => 'integer',
						'default'     => '0'
					),
					'limitstart' => array(
						'description' => Lang::txt('Number of where to start returning results.'),
						'type'        => 'integer',
						'default'     => '0'
					),
					'verbose' => array(
						'description' => Lang::txt('Receive verbose output for project status, team member role and privacy.'),
						'type'        => 'integer',
						'default'     => '0',
						'accepts'     => array('0', '1')
					),
				),
			),
			'files' => array(
				'list' => array(
					'description' => Lang::txt('Get a list of project files'),
					'parameters'  => array(
						'project_id' => array(
							'description' => Lang::txt('Project alias or numeric id.'),
							'type'        => 'string',
							'default'     => '0',
							'required'    => 'true'
						),
					),
					'subdir' => array(
						'description' => Lang::txt('Directory path within project repo, if not included in the asset file path.'),
						'type'        => 'string'
					),
				),
				'get' => array(
					'description' => Lang::txt('Get project file metadata.'),
					'parameters'  => array(
						'project_id' => array(
							'description' => Lang::txt('Project alias or numeric id.'),
							'type'        => 'string',
							'default'     => '0',
							'required'    => 'true'
						),
						'asset' => array(
							'description' => Lang::txt('Array of file paths.'),
							'type'        => 'array',
							'required'    => 'true'
						),
						'subdir' => array(
							'description' => Lang::txt('Directory path within project repo, if not included in the asset file path.'),
							'type'        => 'string'
						),
					),
				),
				'insert' => array(
					'description' => Lang::txt('Insert a file into project.'),
					'parameters'  => array(
						'project_id' => array(
							'description' => Lang::txt('Project alias or numeric id.'),
							'type'        => 'string',
							'default'     => '0',
							'required'    => 'true'
						),
						'data_path' => array(
							'description' => Lang::txt('Path to local or remote file.'),
							'type'        => 'string',
							'required'    => 'true'
						),
					),
					'subdir' => array(
						'description' => Lang::txt('Directory path within project repo to insert file into.'),
						'type'        => 'string'
					),
				),
				'update' => array(
					'description' => Lang::txt('Insert a file into project.'),
					'parameters'  => array(
						'project_id' => array(
							'description' => Lang::txt('Project alias or numeric id.'),
							'type'        => 'string',
							'default'     => '0',
							'required'    => 'true'
						),
						'data_path' => array(
							'description' => Lang::txt('Path to local or remote file.'),
							'type'        => 'string',
							'required'    => 'true'
						),
					),
				),
			),
		);

		$this->setMessageType(Request::getWord('format', 'json'));
		$this->setMessage($response);
	}

	/**
	 * List projects user has access to
	 *
	 * @return array
	 */
	private function _projectList()
	{
		// get the userid and attempt to load user profile
		$user = \Hubzero\User\Profile::getInstance($this->user_id);

		// make sure we have a user
		if ($user === false)
		{
			return $this->_errorMessage(404, Lang::txt('User not found'));
		}

		include_once(PATH_CORE . DS . 'components' . DS
			. 'com_projects' . DS . 'tables' . DS . 'project.php');
		$objP = new Components\Projects\Tables\Project($this->_database);

		// Set filters
		$filters = array(
			'limit'      => Request::getInt('limit', 0),
			'start'      => Request::getInt('limitstart', 0),
			'sortby'     => Request::getWord('sortby', 'title'),
			'sortdir'    => strtoupper(Request::getWord('sortdir', 'ASC')),
			'getowner'   => 1,
			'updates'    => 1,
			'mine'       => 1
		);

		// Incoming
		$verbose = Request::getInt('verbose', 0);

		$setupComplete = $this->_config->get('confirm_step', 0) ? 3 : 2;

		$response 			= new stdClass;
		$response->projects = array();
		$response->total 	= $objP->getCount($filters, $admin = false, $this->user_id, 0, $setupComplete);
		$response->success 	= true;

		if ($response->total)
		{
			$projects = $objP->getRecords($filters, $admin = false, $this->user_id, 0, $setupComplete);

			$juri = JURI::getInstance();

			// Get config
			$livesite = Config::get('config.live_site')
				? Config::get('config.live_site')
				: trim(preg_replace('/\/administrator/', '', $juri->base()), DS);
			$livesite = trim(preg_replace('/\/api/', '', $juri->base()), DS);

			foreach ($projects as $i => $entry)
			{
				$obj 			= new stdClass;
				$obj->id        = $entry->id;
				$obj->alias     = $entry->alias;
				$obj->title     = $entry->title;
				$obj->state     = $entry->state;
				$obj->inSetup   = ($entry->setup_stage < $setupComplete) ? 1 : 0;
				$obj->author 	= $entry->authorname;
				$obj->created 	= $entry->created;
				$obj->userRole 	= $entry->role;
				$obj->thumbUrl 	= $livesite . DS . 'projects' . DS . $obj->alias . DS . 'thumb';
				$obj->privacy   = $entry->private;
				$obj->provisioned = $entry->provisioned;
				$obj->groupOwnerId = $entry->owned_by_group;
				$obj->userOwnerId = $entry->owned_by_user;

				// Explain what status/role means
				if ($verbose)
				{
					// Project status
					switch ($entry->state)
					{
						case 0:
							$obj->state = ($entry->setup_stage < $setupComplete) ? Lang::txt('setup') : Lang::txt('suspended');
							break;

						case 1:
						default:
							$obj->state = Lang::txt('active');
							break;

						case 2:
							$obj->state = Lang::txt('deleted');
							break;

						case 5:
							$obj->state = Lang::txt('pending approval');
							break;
					}

					// Privacy
					$obj->privacy = $obj->privacy == 1 ? Lang::txt('private') : Lang::txt('public');

					// Team role
					switch ($entry->role)
					{
						case 0:
						default:
							$obj->userRole = Lang::txt('collaborator');
							break;
						case 1:
							$obj->userRole = Lang::txt('manager');
							break;
						case 2:
							$obj->userRole = Lang::txt('author');
							break;
						case 3:
							$obj->userRole = Lang::txt('reviewer');
							break;
					}
				}

				$response->projects[] = $obj;
			}
		}

		$this->setMessageType(Request::getWord('format', 'json'));
		$this->setMessage($response);

		return;
	}

	/**
	 * Manage project files
	 *
	 * @return array
	 */
	private function _manageFiles()
	{
		// get the userid and attempt to load user profile
		$user = \Hubzero\User\Profile::getInstance($this->user_id);

		// make sure we have a user
		if ($user === false)
		{
			return $this->_errorMessage(404, Lang::txt('User not found'));
		}

		// Authorization for project team
		$authorized = $this->_authorize();

		// Missing required param
		if (!$this->project_id)
		{
			// Set the error message
			$this->_errorMessage(
				404,
				Lang::txt('Missing required parameter: project_id.'),
				Request::getWord('format', 'json')
			);
			return;
		}

		// Project did not load?
		if (!$this->project->exists())
		{
			// Set the error message
			$this->_errorMessage(
				404,
				Lang::txt('Project not found.'),
				Request::getWord('format', 'json')
			);
			return;
		}

		// Check authorization
		if (($this->_action == 'insert' && !$authorized['content']) || !$authorized['view'])
		{
			// Set the error message
			$this->_errorMessage(
				401,
				Lang::txt('Unauthorized task.'),
				Request::getWord('format', 'json')
			);
			return;
		}

		// Check for local repo
		if (!$this->project->repo()->exists())
		{
			// Set the error message
			$this->_errorMessage(
				404,
				Lang::txt('Project local repository does not exist'),
				Request::getWord('format', 'json')
			);
			return;
		}

		$response 			= new stdClass;
		$response->task 	= 'files';
		$response->action 	= $this->_action;
		$response->project 	= $this->project_id;

		switch ($this->_action)
		{
			case 'list':
			default:
				$response->results     = $this->project->repo()->filelist(array(
					'subdir'           => Request::getVar('subdir', ''),
					'filter'           => Request::getVar('filter', ''),
					'limit'            => Request::getInt('limit', 0),
					'start'            => Request::getInt('limitstart', 0),
					'sortby'           => 'localpath',
					'showFullMetadata' => true,
					'getParents'       => true,
					'getChildren'      => true
					)
				);
				break;

			case 'get':
				$response->results     = $this->project->repo()->filelist(array(
					'subdir'           => Request::getVar('subdir', ''),
					'files'            => Request::getVar( 'asset', '', 'request', 'array' ),
					'showFullMetadata' => true,
					'getParents'       => true,
					'getChildren'      => true
					)
				);
				break;

			case 'insert':
			case 'update':

				// Project plugin params
				$fileParams = Plugin::params('projects', 'files');

				// Get used space
				$dirsize = $this->project->repo()->call(
					'getDiskUsage',
					$params = array('history' => $fileParams->get('disk_usage'))
				);

				// Get disk quota
				$quota = $this->project->params->get('quota', \Components\Projects\Helpers\Html::convertSize(floatval($this->project->config()->get('defaultQuota', '1')), 'GB', 'b'));

				// Insert file
				$response->results     = $this->project->repo()->insert(
					array(
						'dataPath'    => Request::getVar( 'data_path', '' ),
						'allowReplace'=> $this->_action == 'insert' ? false : true,
						'update'      => $this->_action == 'insert' ? false : true,
						'subdir'      => Request::getVar('subdir', ''),
						'quota'       => $quota,
						'dirsize'     => $dirsize,
						'sizelimit'   => $fileParams->get('maxUpload', '104857600')
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
								$parsedResults[] = $this->project->repo()->getMetadata($file, 'file');
								$names .= $names ? ', ' . $file : $file;
							}
						}
					}

					// Register event with the project
					if (!empty($parsedResults))
					{
						$updateType = $this->_action == 'insert' ? 'uploaded' : 'updated';
						// Plugin params
						$plugin_params = array(
							$this->project,
							array($updateType => $names)
						);

						Event::trigger( 'projects.onAfterUpdate', $plugin_params);
					}

					$response->results = $parsedResults;
				}
				break;
		}

		// Get array of file metadata
		if (!empty($response->results))
		{
			$results = array();
			foreach ($response->results as $result)
			{
				// Access private _data container
				$results[] = $result->getData();
			}
			$response->results = $results;
		}

		if ($this->project->repo()->getError())
		{
			$response->error   = $this->project->repo()->getError();
			$response->success = false;
		}
		else
		{
			$response->success = true;
			$response->error   = NULL;
		}

		$this->setMessage($response);

		$this->setMessageType(Request::getWord('format', 'json'));
		return;
	}

	/**
	 * Helper function to check whether or not someone is using oauth and authorized
	 *
	 * @return bool - true if in group, false otherwise
	 */
	private function _authorize()
	{
		// Get the project id
		$this->project_id      = Request::getWord('project_id', 0);
		$this->project 		   = NULL;

		$authorized            = array();
		$authorized['view']    = false;
		$authorized['manage']  = false;
		$authorized['content'] = false;

		// Not logged in and/or not using OAuth
		if (!is_numeric($this->user_id))
		{
			return $authorized;
		}

		include_once(PATH_CORE . DS . 'components' . DS . 'com_projects'
			. DS . 'models' . DS . 'project.php');
		$this->project = new Components\Projects\Models\Project($this->project_id);

		if ($this->project->exists())
		{
			$authorized['view']    = $this->project->access('member') ? true : false;
			$authorized['manage']  = $this->project->access('manager') ? true : false;
			$authorized['content'] = $this->project->access('content') ? true : false;
		}

		return $authorized;
	}

	/**
	 * Method to report errors. creates error node for response body as well
	 *
	 * @param	$code		Error Code
	 * @param	$message	Error Message
	 * @param	$format		Error Response Format
	 *
	 * @return     void
	 */
	private function _errorMessage( $code = '404', $message, $format = 'json' )
	{
		//build error code and message
		$object = new stdClass();
		$object->error = new stdClass();
		$object->error->code    = $code;
		$object->error->message = $message;

		//set http status code and reason
		$this->getResponse()
		     ->setErrorMessage($object->error->code, $object->error->message);

		//add error to message body
		$this->setMessageType(Request::getWord('format', $format));
		$this->setMessage($object);
	}
}

