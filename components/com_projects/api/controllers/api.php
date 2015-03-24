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

		$this->_config   = JComponentHelper::getParams('com_projects');
		$this->_database = JFactory::getDBO();
		$this->_action   = $this->segments[1] ? $this->segments[1] : 'list';

		// Get the user id
		$this->user_id = JFactory::getApplication()->getAuthn('user_id');

		// Switch based on entity type and action
		switch($this->segments[0])
		{
			// Files
			case 'files':
				switch($this->_action)
				{
					case 'list':
					case 'get':
					case 'insert':
						$this->_manageFiles();
						break;
					default:
						return $this->_errorMessage(404, JText::_('Method unavailable'));
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
				'description' => JText::_('Get a list of projects user is a member of'),
				'parameters'  => array(
					'sortby' => array(
						'description' => JText::_('Field to sort results by.'),
						'type'        => 'string',
						'default'     => 'created',
						'accepts'     => array('created', 'title', 'alias', 'id', 'publish_up', 'publish_down', 'state')
					),
					'sortdir' => array(
						'description' => JText::_('Direction to sort results by.'),
						'type'        => 'string',
						'default'     => 'desc',
						'accepts'     => array('asc', 'desc')
					),
					'limit' => array(
						'description' => JText::_('Number of result to return.'),
						'type'        => 'integer',
						'default'     => '0'
					),
					'limitstart' => array(
						'description' => JText::_('Number of where to start returning results.'),
						'type'        => 'integer',
						'default'     => '0'
					),
					'verbose' => array(
						'description' => JText::_('Receive verbose output for project status, team member role and privacy.'),
						'type'        => 'integer',
						'default'     => '0',
						'accepts'     => array('0', '1')
					),
				),
			),
			'files' => array(
				'list' => array(
					'description' => JText::_('Get a list of project files'),
					'parameters'  => array(
						'project_id' => array(
							'description' => JText::_('Project alias or numeric id.'),
							'type'        => 'string',
							'default'     => '0',
							'required'    => 'true'
						),
					),
					'subdir' => array(
						'description' => JText::_('Directory path within project repo, if not included in the asset file path.'),
						'type'        => 'string'
					),
				),
				'get' => array(
					'description' => JText::_('Get project file metadata.'),
					'parameters'  => array(
						'project_id' => array(
							'description' => JText::_('Project alias or numeric id.'),
							'type'        => 'string',
							'default'     => '0',
							'required'    => 'true'
						),
						'asset' => array(
							'description' => JText::_('Array of file paths.'),
							'type'        => 'array',
							'required'    => 'true'
						),
						'subdir' => array(
							'description' => JText::_('Directory path within project repo, if not included in the asset file path.'),
							'type'        => 'string'
						),
					),
				),
				'insert' => array(
					'description' => JText::_('Insert a file into project.'),
					'parameters'  => array(
						'project_id' => array(
							'description' => JText::_('Project alias or numeric id.'),
							'type'        => 'string',
							'default'     => '0',
							'required'    => 'true'
						),
						'data_path' => array(
							'description' => JText::_('Path to local or remote file.'),
							'type'        => 'string',
							'required'    => 'true'
						),
					),
					'subdir' => array(
						'description' => JText::_('Directory path within project repo to insert file into.'),
						'type'        => 'string'
					),
				),
			),
		);

		$this->setMessageType(JRequest::getWord('format', 'json'));
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
			return $this->_errorMessage(404, JText::_('User not found'));
		}

		include_once(PATH_CORE . DS . 'components' . DS
			. 'com_projects' . DS . 'tables' . DS . 'project.php');
		$objP = new Components\Projects\Tables\Project($this->_database);

		// Set filters
		$filters = array(
			'limit'      => JRequest::getInt('limit', 0),
			'start'      => JRequest::getInt('limitstart', 0),
			'sortby'     => JRequest::getWord('sortby', 'title'),
			'sortdir'    => strtoupper(JRequest::getWord('sortdir', 'ASC')),
			'getowner'   => 1,
			'updates'    => 1,
			'mine'       => 1
		);

		// Incoming
		$verbose = JRequest::getInt('verbose', 0);

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
							$obj->state = ($entry->setup_stage < $setupComplete) ? JText::_('setup') : JText::_('suspended');
							break;

						case 1:
						default:
							$obj->state = JText::_('active');
							break;

						case 2:
							$obj->state = JText::_('deleted');
							break;

						case 5:
							$obj->state = JText::_('pending approval');
							break;
					}

					// Privacy
					$obj->privacy = $obj->privacy == 1 ? JText::_('private') : JText::_('public');

					// Team role
					switch ($entry->role)
					{
						case 0:
						default:
							$obj->userRole = JText::_('collaborator');
							break;
						case 1:
							$obj->userRole = JText::_('manager');
							break;
						case 2:
							$obj->userRole = JText::_('author');
							break;
						case 3:
							$obj->userRole = JText::_('reviewer');
							break;
					}
				}

				$response->projects[] = $obj;
			}
		}

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
			return $this->_errorMessage(404, JText::_('User not found'));
		}

		// Authorization for project team
		$authorized = $this->_authorize();

		// Missing required param
		if (!$this->project_id)
		{
			// Set the error message
			$this->_errorMessage(
				404,
				JText::_('Missing required parameter: project_id.'),
				JRequest::getWord('format', 'json')
			);
			return;
		}

		// Project did not load?
		if (!$this->project)
		{
			// Set the error message
			$this->_errorMessage(
				404,
				JText::_('Project not found.'),
				JRequest::getWord('format', 'json')
			);
			return;
		}

		// Unauthorized
		if (!$authorized['manage'])
		{
			// Set the error message
			$this->_errorMessage(
				401,
				JText::_('Unauthorized task.'),
				JRequest::getWord('format', 'json')
			);
			return;
		}

		// Get plugin
		JPluginHelper::importPlugin( 'projects', 'files' );
		$dispatcher = JDispatcher::getInstance();

		// Plugin params
		$plugin_params = array(
			$this->project_id,
			$this->_action,
			$this->user_id
		);

		// Perform action
		$output = $dispatcher->trigger( 'onProjectExternal', $plugin_params);

		$response 			= new stdClass;
		$response->task 	= 'files';
		$response->action 	= $this->_action;
		$response->project 	= $this->project_id;

		$output = empty($output) ? NULL : json_decode($output[0], TRUE);

		if (!$output || (isset($output['error']) && $output['error'] == true))
		{
			$response->error 	= (isset($output['error']) && $output['error'] == true) ? $output['message'] : 'Failed to perform action';
			$response->success 	= false;
		}
		else
		{
			$response->success 	= true;
			$response->error 	= NULL;
			$response->items 	= isset($output['results']) ? $output['results'] : NULL;
			$response->message 	= isset($output['message']) ? $output['message'] : NULL;
		}

		$this->setMessage($response);

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
		$this->project_id     = JRequest::getWord('project_id', 0);
		$this->project 		  = NULL;

		$authorized           = array();
		$authorized['view']   = false;
		$authorized['manage'] = false;

		// Not logged in and/or not using OAuth
		if (!is_numeric($this->user_id))
		{
			return $authorized;
		}

		include_once(PATH_CORE . DS . 'components' . DS . 'com_projects'
			. DS . 'tables' . DS . 'project.php');
		$objP = new Components\Projects\Tables\Project($this->_database);

		$this->project 	= $objP->getProject($this->project_id, $this->user_id);

		if ($this->project)
		{
			$authorized['view']   = true;
			$authorized['manage'] = true;
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
	private function _errorMessage( $code, $message, $format = 'json' )
	{
		//build error code and message
		$object = new stdClass();
		$object->error->code = $code;
		$object->error->message = $message;

		//set http status code and reason
		$response = $this->getResponse();
		$response->setErrorMessage( $object->error->code, $object->error->message );

		//add error to message body
		$this->setMessageType( $format );
		$this->setMessage( $object );
	}
}

