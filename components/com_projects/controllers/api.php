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
						$this->_not_found();
						break;
				}
			break;

			// Project list
			case 'list':
				$this->_projectList();
			break;

			default:
				$this->_not_found();
			break;
		}
	}

	//--------------------------
	// Projects functions
	//--------------------------

	/**
	 * List projects user has access to
	 *
	 * @return array
	 */
	private function _projectList()
	{
		//get the userid and attempt to load user profile
		$userid = JFactory::getApplication()->getAuthn('user_id');

		$result = \Hubzero\User\Profile::getInstance($userid);

		// make sure we have a user
		if ($result === false)	return $this->_not_found('User not found');

		include_once(JPATH_ROOT . DS . 'administrator' . DS . 'components' . DS . 'com_projects' . DS . 'tables' . DS . 'project.php');
		$objP = new Project($this->_database);

		// Set filters
		$filters = array();
		$filters['mine']     = 1;
		$filters['updates']  = 1;
		$filters['sortby']   = JRequest::getVar('sortby', 'title');
		$filters['getowner'] = 1;
		$filters['sortdir']  = JRequest::getVar('sortdir', 'ASC');

		$setup_complete = $this->_config->get('confirm_step', 0) ? 3 : 2;

		$response 			= new stdClass;
		$response->projects = array();
		$response->total 	= $objP->getCount($filters, $admin = false, $userid, 0, $setup_complete);
		$response->success 	= true;

		if ($response->total)
		{
			$projects = $objP->getRecords($filters, $admin = false, $userid, 0, $setup_complete);

			$juri = JURI::getInstance();
			$jconfig 	= JFactory::getConfig();

			// Get config
			$livesite = $jconfig->getValue('config.live_site')
				? $jconfig->getValue('config.live_site')
				: trim(preg_replace('/\/administrator/', '', $juri->base()), DS);
			$livesite = trim(preg_replace('/\/api/', '', $juri->base()), DS);

			$webdir = JPATH_ROOT . DS . trim($this->_config->get('imagepath', '/site/projects'), DS);
			$weburl = $livesite . DS . trim($this->_config->get('imagepath', '/site/projects'), DS);

			include_once( JPATH_ROOT . DS . 'components' . DS . 'com_projects' . DS . 'helpers' . DS . 'imghandler.php' );
			$ih = new ProjectsImgHandler();

			foreach ($projects as $i => $entry)
			{
				$obj 			= new stdClass;
				$obj->id        = $entry->id;
				$obj->alias     = $entry->alias;
				$obj->title     = $entry->title;
				$obj->state     = $entry->state;
				$obj->author 	= $entry->authorname;
				$obj->created 	= $entry->created;
				$obj->userRole 	= $entry->role;

				$path = $webdir . DS . strtolower($entry->alias) . DS . 'images';
				$url  = $weburl . DS . strtolower($entry->alias) . DS . 'images';

				// Get thumbnail
				if ($entry->picture)
				{
					$thumb = is_file($path . DS . 'thumb.png') ? 'thumb.png' : $ih->createThumbName($entry->picture);
					$obj->thumb = is_file($path . DS . $thumb) ? $url . DS . $thumb : NULL;
				}
				else
				{
					$obj->thumb = $livesite . DS . $this->_config->get('defaultpic');
				}

				$response->projects[] = $obj;
			}
		}

		$this->setMessage($response);

		return;
	}

	//--------------------------
	// Files functions
	//--------------------------

	/**
	 * Manage project files
	 *
	 * @return array
	 */
	private function _manageFiles()
	{
		// Require authorization
		$authorized = $this->_authorize();
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

		$output = empty($output) ? NULL : $output[0];

		if (!$output || (isset($output['error']) && $output['error'] == true))
		{
			$response->error 	= (isset($output['error']) && $output['error'] == true) ? $output['message'] : 'Failed to perform action';
			$response->success 	= false;
		}
		else
		{
			$response->success 	= true;
			$response->error 	= NULL;
			$response->items 	= isset($output['output']) ? $output['output'] : NULL;
			$response->message 	= isset($output['message']) ? $output['message'] : NULL;
		}

		$this->setMessage($response);

		return;
	}

	//--------------------------
	// Miscelaneous methods
	//--------------------------

	/**
	 * Default method - not found
	 *
	 * @return 404, method not found error
	 */
	private function _not_found($text = 'Invalid task')
	{
		// Set the error message
		$this->_errorMessage(
			404,
			$text,
			JRequest::getWord('format', 'json')
		);
		return;
	}

	/**
	 * Helper function to check whether or not someone is using oauth and authorized
	 *
	 * @return bool - true if in group, false otherwise
	 */
	private function _authorize()
	{
		// Get the user id
		$this->user_id = JFactory::getApplication()->getAuthn('user_id');

		// Get the project id
		$this->project_id     = JRequest::getVar('project_id', 0);

		$authorized           = array();
		$authorized['view']   = false;
		$authorized['manage'] = false;

		// Not logged in and/or not using OAuth OR no project ID
		if (!is_numeric($this->user_id) || !$this->project_id)
		{
			return $authorized;
		}

		include_once(JPATH_ROOT . DS . 'administrator' . DS . 'components' . DS . 'com_projects' . DS . 'tables' . DS . 'project.php');
		$objP = new Project($this->_database);

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

