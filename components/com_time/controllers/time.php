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
 * @author    Sam Wilson <samwilson@purdue.edu>
 * @copyright Copyright 2005-2011 Purdue University. All rights reserved.
 * @license   http://www.gnu.org/licenses/lgpl-3.0.html LGPLv3
 */

// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die('Restricted access');

/**
 * Primary component controller (extends \Hubzero\Component\SiteController)
 */
class TimeController extends \Hubzero\Component\SiteController
{
	/**
	 * Execute function
	 * 
	 * @return void
	 */
	public function execute()
	{
		// Get the task
		$this->_task  = JRequest::getVar('task', 'view');

		// Get the current/active tab
		$this->active_tab = JRequest::getVar('active', 'overview');

		// Get the action (if applicable)
		$this->action = JRequest::getVar('action', 'view');

		// Execute the task
		switch ($this->_task)
		{
			// Core component functions
			case 'ajax':      $this->ajax();   break;
			case 'view':      $this->view();   break;

			default:          $this->view();   break;
		}
	}

	/**
	 * Set notifications
	 * 
	 * @param  string $message
	 * @param  string $type
	 * @return void
	 */
	public function setNotification($message, $type)
	{
		// If type is not set, set to error message
		$type = ($type == '') ? 'error' : $type;

		// If message is set, push to notifications
		if ($message != '')
		{
			$this->addComponentMessage($message, $type);
		}
	}

	/**
	 * Get notifications
	 * 
	 * @return $messages if they exist
	 */
	public function getNotifications()
	{
		// Get messages currently in quene
		$messages = $this->getComponentMessage();

		// If we have any messages return them
		if ($messages)
		{
			return $messages;
		}
	}

	/**
	 * Get CSS for the component
	 * 
	 * @return void
	 */
	protected function _getTimeStyles()
	{
		$doc       = JFactory::getDocument();
		$mainframe = JFactory::getApplication();
		$template  = $mainframe->getTemplate();
		$task      = $this->_task;

		$template_css  = "/templates".DS.$template.DS."html".DS."com_time".DS."time.css";
		$component_css = "/components".DS."com_time".DS."time.css";

		if(!JPluginHelper::isEnabled('system', 'jquery'))
		{
			$doc->addStyleSheet(DS.'plugins'.DS.'system'.DS.'jquery'.DS.'css'.DS.'jquery-ui-1.8.6.custom.css');
		}

		if (file_exists(JPATH_ROOT.$template_css))
		{
			$doc->addStyleSheet($template_css);
		}
		elseif (file_exists(JPATH_ROOT.$component_css))
		{
			$doc->addStyleSheet($component_css);
		}
		else
		{
			$this->_getStyles();
		}
	}

	/**
	 * Get scripts for this component
	 * 
	 * @return void
	 */
	protected function _getTimeScripts()
	{
		$doc = JFactory::getDocument();

		$component_js = "/components".DS."com_time".DS."time.js";
		$hover_intent = DS . 'media' . DS . 'system' . DS . 'js' . DS . 'jquery.hoverIntent.js';

		if(!JPluginHelper::isEnabled('system', 'jquery'))
		{
			$doc->addScript('https://ajax.googleapis.com/ajax/libs/jquery/1.7.2/jquery.min.js');
			$doc->addScript('https://ajax.googleapis.com/ajax/libs/jqueryui/1.8.21/jquery-ui.min.js');
		}

		if(file_exists(JPATH_ROOT.$hover_intent))
		{
			$doc->addScript($hover_intent);
		}

		if(file_exists(JPATH_ROOT.$component_js))
		{
			$doc->addScript($component_js);
		}
		else
		{
			$this->_getScripts();
		}

		\Hubzero\Document\Assets::addSystemScript('jquery.infinitescroll');
	}

	/**
	 * Build the "trail"
	 * 
	 * @return void
	 */
	protected function _buildPathway()
	{
		//$option = substr($this->_option,4);
		$app = JFactory::getApplication();
		$pathway = $app->getPathway();

		if (count($pathway->getPathWay()) <= 0)
		{
			$pathway->addItem(
				JText::_(strtoupper($this->_option)),
				'index.php?option='.$this->_option
			);
			if ($this->active_tab)
			{
				$pathway->addItem(
					JText::_('PLG_TIME_'.strtoupper($this->active_tab)),
					'index.php?option='.$this->_option.'&active='.$this->active_tab
				);
			}
			if ($this->action != 'view')
			{
				$pathway->addItem(
					JText::_('PLG_TIME_'.strtoupper($this->active_tab).'_'.strtoupper($this->action)),
					'index.php?option='.$this->_option.'&active='.$this->active_tab
				);
			}
		}
	}

	/**
	 * Build the title for this component
	 * 
	 * @return void
	 */
	protected function _buildTitle()
	{
		// Set the title
		$this->_title = JText::_(strtoupper($this->_option));

		// Set the title of the browser window
		$document = JFactory::getDocument();
		$document->setTitle($this->_title);
	}

	//----------------------------------------------------------
	// Main displays
	//----------------------------------------------------------

	/**
	 * Main/default view.  Just call all our plugins
	 * 
	 * @return void
	 */
	protected function view()
	{
		// Force login if user isn't already
		if ($this->juser->get('guest'))
		{
			// Set the active tab and action if we need to
			$active = ($this->active_tab != 'overview') ? '&active=' . $this->active_tab : '';
			$action = ($this->action != 'view') ? '&active=' . $this->action : '';

			// Set the redirect
			$this->setRedirect(
				JRoute::_('index.php?option=com_login&return=' . base64_encode(JRoute::_('index.php?option=' . $this->_option . $active . $action))),
				JText::_('You must be a logged in to access this area.'),
				'warning'
			);
			return;
		}

		// Check access
		if(!$this->_authorize())
		{
			JError::raiseError(401, 'You\'re not authorized to use this component');
			return;
		}

		// Push some styles and scripts to the template
		$this->_getTimeStyles();
		$this->_getTimeScripts();

		// Get time plugins
		JPluginHelper::importPlugin('time');
		$dispatcher = JDispatcher::getInstance();

		// Trigger the functions that return the areas we'll be using
		$time_plugins = $dispatcher->trigger('onTimeAreas', array());

		// Get the sections
		$sections = $dispatcher->trigger('onTime', array($this->action, $this->_option, $this->active_tab));

		// Build the title
		$this->_buildTitle();

		// Build pathway
		$this->_buildPathway();

		// Output the HTML
		$view                = new \Hubzero\Component\View(array('name'=>'view'));
		$view->option        = $this->_option;
		$view->title         = $this->_title;
		$view->active_tab    = $this->active_tab;
		$view->time_plugins  = $time_plugins;
		$view->sections      = $sections;
		$view->notifications = ($this->getNotifications()) ? $this->getNotifications() : array();
		$view->display();
	}

	/**
	 * Ajax method
	 * 
	 * @return void
	 */
	protected function ajax()
	{
		// Get time plugins
		JPluginHelper::importPlugin('time');
		$dispatcher = JDispatcher::getInstance();

		// Trigger the functions that return the areas we'll be using
		$time_plugins = $dispatcher->trigger('onTimeAreas', array());

		// Get the sections
		$sections = $dispatcher->trigger('onTime', array($this->action, $this->_option, $this->active_tab));
	}

	/**
	 * Simple login function
	 * 
	 * @param  string $title - page title
	 * @return void
	 */
	protected function login($title = "")
	{
		// Set the page title
		$title = ($title) ? $title : JText::_(strtoupper($this->_name));

		// Output HTML
		$view                = new \Hubzero\Component\View(array('name'=>'login'));
		$view->title         = $title;
		$view->notifications = ($this->getNotifications()) ? $this->getNotifications() : array();
		$view->display();
	}

	/**
	 * Authorize current user
	 * 
	 * @return true or false
	 */
	protected function _authorize()
	{
		// @FIXME: add parameter for group access
		$accessgroup = isset($this->config->parameters['accessgroup']) ? trim($this->config->parameters['accessgroup']) : 'time';

		// Check if they're a member of admin group
		$ugs = \Hubzero\User\Helper::getGroups($this->juser->get('id'));
		if ($ugs && count($ugs) > 0)
		{
			foreach ($ugs as $ug)
			{
				if ($ug->cn == $accessgroup)
				{
					return true;
				}
			}
		}

		return false;
	}
}