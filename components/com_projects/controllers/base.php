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
 * @copyright Copyright 2005-2011 Purdue University. All rights reserved.
 * @license   http://www.gnu.org/licenses/lgpl-3.0.html LGPLv3
 */

// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die('Restricted access');

/**
 * Base projects controller (extends \Hubzero\Component\SiteController)
 */
class ProjectsControllerBase extends \Hubzero\Component\SiteController
{
	/**
	 * Execute function
	 *
	 * @return void
	 */
	public function execute()
	{
		// Is component on?
		if (!$this->config->get( 'component_on', 0 ))
		{
			$this->_redirect = '/';
			return;
		}

		// Publishing enabled?
		$this->_publishing = JPluginHelper::isEnabled('projects', 'publications') ? 1 : 0;

		// Setup complete?
		$this->_setupComplete = $this->config->get('confirm_step', 0) ? 3 : 2;

		// Include scripts
		$this->_includeScripts();

		// Incoming project identifier
		$id = JRequest::getInt( 'id', 0 );
		$alias = JRequest::getWord( 'alias', '' );
		$this->_identifier  = $id ? $id : $alias;

		// Incoming
		$this->_task = strtolower(JRequest::getWord( 'task', '' ));
		$this->_gid  = JRequest::getVar( 'gid', 0 );

		// Execute the task
		parent::execute();
	}

	/**
	 * Include necessary scripts
	 *
	 * @return     void
	 */
	protected function _includeScripts()
	{
		// Enable publication management
		if ($this->_publishing)
		{
			require_once( JPATH_ROOT . DS . 'administrator' . DS . 'components'.DS
				.'com_publications' . DS . 'tables' . DS . 'publication.php');
			require_once( JPATH_ROOT . DS . 'administrator' . DS . 'components'.DS
				.'com_publications' . DS . 'tables' . DS . 'version.php');
			require_once( JPATH_ROOT . DS . 'administrator' . DS . 'components'.DS
				.'com_publications' . DS . 'tables' . DS . 'access.php');
			require_once( JPATH_ROOT . DS . 'administrator' . DS . 'components'.DS
				.'com_publications' . DS . 'tables' . DS . 'audience.level.php');
			require_once( JPATH_ROOT . DS . 'administrator' . DS . 'components'.DS
				.'com_publications' . DS . 'tables' . DS . 'audience.php');
			require_once( JPATH_ROOT . DS . 'administrator' . DS . 'components'.DS
				.'com_publications' . DS . 'tables' . DS . 'author.php');
			require_once( JPATH_ROOT . DS . 'administrator' . DS . 'components'.DS
				.'com_publications' . DS . 'tables' . DS . 'license.php');
			require_once( JPATH_ROOT . DS . 'administrator' . DS . 'components'.DS
				.'com_publications' . DS . 'tables' . DS . 'category.php');
			require_once( JPATH_ROOT . DS . 'administrator' . DS . 'components'.DS
				.'com_publications' . DS . 'tables' . DS . 'master.type.php');
			require_once( JPATH_ROOT . DS . 'administrator' . DS . 'components'.DS
				.'com_publications' . DS . 'tables' . DS . 'screenshot.php');
			require_once( JPATH_ROOT . DS . 'administrator' . DS . 'components'.DS
				.'com_publications' . DS . 'tables' . DS . 'attachment.php');
			require_once( JPATH_ROOT . DS . 'components'.DS
				. 'com_publications' . DS . 'helpers' . DS . 'helper.php');
		}

		// Database development on?
		if (JPluginHelper::isEnabled('projects', 'databases'))
		{
			require_once( JPATH_ROOT . DS . 'administrator' . DS . 'components'. DS
					.'com_projects' . DS . 'tables' . DS . 'project.database.php');
			require_once( JPATH_ROOT . DS . 'administrator' . DS . 'components'. DS
					.'com_projects' . DS . 'tables' . DS . 'project.database.version.php');
		}

		// Logging and stats
		require_once( JPATH_ROOT . DS . 'administrator' . DS . 'components'. DS
				.'com_projects' . DS . 'tables' . DS . 'project.log.php');
		require_once( JPATH_ROOT . DS . 'administrator' . DS . 'components'. DS
				.'com_projects' . DS . 'tables' . DS . 'project.stats.php');

		// Include external file connection
		require_once( JPATH_ROOT . DS . 'components' . DS . 'com_projects' . DS
				. 'helpers' . DS . 'connect.php' );
		require_once( JPATH_ROOT . DS . 'administrator' . DS . 'components'
				. DS . 'com_projects' . DS . 'tables' . DS . 'project.remote.file.php');
		require_once( JPATH_SITE . DS . 'components' . DS . 'com_projects'
				. DS . 'helpers' . DS . 'remote' . DS . 'google.php' );
	}

	/**
	 * Set notifications
	 *
	 * @param  string $message
	 * @param  string $type
	 * @return void
	 */
	protected function _setNotification( $message, $type = 'success' )
	{
		// If message is set push to notifications
		if ($message != '')
		{
			$this->addComponentMessage($message, $type);
		}
	}

	/**
	 * Get notifications
	 * @param  string $type
	 * @return $messages if they exist
	 */
	protected function _getNotifications($type = 'success')
	{
		// Get messages in queue
		$messages = $this->getComponentMessage();

		// Return first message of type
		if ($messages && count($messages) > 0)
		{
			foreach ($messages as $message)
			{
				if ($message['type'] == $type)
				{
					return $message['message'];
				}
			}
		}
		else
		{
			return false;
		}
	}

	/**
	 * Login view
	 *
	 * @return     void
	 */
	protected function _login()
	{
		$task = (isset($this->_task) && !empty($this->_task))
			? '&task=' . $this->_task
			: '';
		$message = isset($this->_msg)
			? $this->_msg
			: JText::_('COM_PROJECTS_LOGIN_PRIVATE_PROJECT_AREA');

		$rtrn = JRequest::getVar('REQUEST_URI', JRoute::_('index.php?option='
			. $this->_option . '&controller=' . $this->_controller . $task), 'server');

		// Needed for a weird redirect problem with /files
		if (substr($rtrn, -1, 1) != '/'
			&& substr($rtrn, -9, 9) != 'sponsored'
			&& substr($rtrn, -9, 9) != 'sensitive')
		{
			$rtrn .= DS;
		}

		$this->setRedirect(
			JRoute::_('index.php?option=com_users&view=login').'?return=' . base64_encode($rtrn),
			$this->_msg,
			'warning'
		);
	}

	/**
	 * Authorize users
	 *
	 * @param  int $check_site_admin
	 * @return void
	 */
	protected function _authorize( $check_site_admin = 0 )
	{
		// Check login
		if ($this->juser->get('guest'))
		{
			return false;
		}

		// Check whether user belongs to the project
		if ($this->_identifier !== NULL)
		{
			$pOwner = new ProjectOwner( $this->database );
			if ($result = $pOwner->isOwner($this->juser->get('id'), $this->_identifier))
			{
				return $result;
			}
		}

		// Check if they're a site admin (from Joomla)
		if ($check_site_admin)
		{
			if ($this->juser->get('id') && $this->juser->authorize($this->_option, 'manage'))
			{
				return 'admin';
			}
		}

		return false;
	}
}