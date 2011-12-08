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
 * @author    Shawn Rice <zooley@purdue.edu>
 * @copyright Copyright 2005-2011 Purdue University. All rights reserved.
 * @license   http://www.gnu.org/licenses/lgpl-3.0.html LGPLv3
 */

// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die( 'Restricted access' );

/**
 * @see JView
 */
jimport('joomla.application.component.view');

/**
 * @package		HUBzero                                  CMS
 * @author		Shawn                                     Rice <zooley@purdue.edu>
 * @copyright	Copyright                               2005-2009 by Purdue Research Foundation, West Lafayette, IN 47906
 * @license		http://www.gnu.org/licenses/gpl-2.0.html GPLv2
 */
class Hubzero_Controller extends JObject
{
	/**
	 * Container for component messages
	 * @var		array
	 */
	public $componentMessageQueue = array();

	/**
	 * The name of the component derived from the controller class name
	 * @var		string
	 */
	protected $_name = NULL;

	/**
	 * Container for storing overloaded data
	 * @var		array
	 */
	protected $_data = array();

	/**
	 * The task the component is to perform
	 * @var		string
	 */
	protected $_task = NULL;

	/**
	 * A list of executable tasks
	 *
	 * @param array
	 */
	protected $_taskMap = array('__default' => 'display');

	/**
	 * The name of the task to be executed
	 *
	 * @param string
	 */
	protected $_doTask = null;

	/**
	 * The name of this controller
	 *
	 * @param string
	 */
	protected $_controller = null;

	/**
	 * Constructor
	 *
	 * @access	public
	 * @param	array	$config		Optional configurations to be used
	 * @return	void
	 */
	public function __construct($config=array())
	{
		$this->_redirect = NULL;
		$this->_message = NULL;
		$this->_messageType = 'message';

		// Set the controller name
		if (empty($this->_name))
		{
			if (isset($config['name']))
			{
				$this->_name = $config['name'];
			}
			else
			{
				$r = null;
				if (!preg_match('/(.*)Controller/i', get_class($this), $r))
				{
					return JError::raiseError(500, JText::_('Controller::__construct() : Can\'t get or parse class name.'));
				}
				$this->_name = strtolower($r[1]);
			}
		}

		// Set the component name
		$this->_option = 'com_' . $this->_name;

		// Determine the methods to exclude from the base class.
		$xMethods = get_class_methods('Hubzero_Controller');

		// Get all the public methods of this class
		$r = new ReflectionClass($this);
		$methods = $r->getMethods(ReflectionMethod::IS_PUBLIC);
		foreach ($methods as $method)
		{
			$name = $method->getName();

			// Ensure task isn't in the exclude list and ends in 'Task'
			if (!in_array($name, $xMethods)
			 || $name == 'displayTask'
			 || substr(strtolower($name), -4) == 'task')
			{
				// Remove the 'Task' suffix
				$name = substr($name, 0, -4);
				// Auto register the methods as tasks.
				$this->_taskMap[strtolower($name)] = $name;
			}
		}

		// Set some commonly used vars
		$this->juser = JFactory::getUser();
		$this->database = JFactory::getDBO();
		$this->config = JComponentHelper::getParams($this->_option);

		// Clear component messages - for cross component messages
		$this->getComponentMessage();
	}

	/**
	 * Method to set an overloaded variable to the component
	 *
	 * @param	string	$property	Name of overloaded variable to add
	 * @param	mixed	$value 		Value of the overloaded variable
	 * @return	void
	 */
	public function __set($property, $value)
	{
		$this->_data[$property] = $value;
	}

	/**
	 * Method to get an overloaded variable of the component
	 *
	 * @param	string	$property	Name of overloaded variable to retrieve
	 * @return	mixed 	Value of the overloaded variable
	 */
	public function __get($property)
	{
		if (isset($this->_data[$property])) {
			return $this->_data[$property];
		}
	}

	/**
	 * Determines task being called and attempts to execute it
	 *
	 * @return	void
	 */
	public function execute()
	{
		// Incoming task
		$this->_task = strtolower(JRequest::getWord('task', ''));

		// Check if the task is in the taskMap
		if (isset($this->_taskMap[$this->_task]))
		{
			$doTask = $this->_taskMap[$this->_task];
		}
		// Check if the default task is set
		elseif (isset($this->_taskMap['__default']))
		{
			$doTask = $this->_taskMap['__default'];
		}
		// Raise an error (hopefully, this shouldn't happen)
		else
		{
			return JError::raiseError(404, JText::sprintf('JLIB_APPLICATION_ERROR_TASK_NOT_FOUND', $this->_task));
		}

		//$this->_controller = JRequest::getCmd('controller', '');
		// Attempt to parse the controller name from the class name
		if ((ucfirst($this->_name) . 'Controller') != get_class($this)
		 && preg_match('/' . ucfirst($this->_name) . 'Controller(.*)/i', get_class($this), $r))
		{
			$this->_controller = strtolower($r[1]);

			// Instantiate a view with layout the same name as the task
			$this->view = new JView(array(
				'name' => $this->_controller,
				'layout' => preg_replace('/[^A-Z0-9_]/i', '', $doTask)
			));
		}
		// No controller name found - single controller component
		else
		{
			$this->view = new JView(array(
				'name' => $doTask
			));
		}

		// Set some commonly used vars
		$this->view->option = $this->_option;
		$this->view->task = $doTask;
		$this->view->controller = $this->_controller;

		// Record the actual task being fired
		$doTask .= 'Task';
		$this->_doTask = $doTask;

		// Call the task
		$this->$doTask();
	}

	/**
	 * Register (map) a task to a method in the class.
	 *
	 * @param	string	The task.
	 * @param	string	The name of the method in the derived class to perform for this task.
	 * @return	void
	 */
	public function registerTask($task, $method)
	{
		/*if (in_array(strtolower($method), $this->_methods)) 
		{
			$this->_taskMap[strtolower($task)] = $method;
		}*/
		if (in_array(strtolower($method), $this->_taskMap))
		{
			$this->_taskMap[strtolower($task)] = $method;
		}
	}

	/**
	 * Method to redirect the application to a new URL and optionally include a message
	 *
	 * @return	void
	 */
	public function redirect()
	{
		if ($this->_redirect != NULL)
		{
			$app = JFactory::getApplication();
			$app->redirect($this->_redirect, $this->_message, $this->_messageType);
		}
	}

	/**
	 * Set a URL for browser redirection.
	 *
	 * @access	public
	 * @param	string URL to redirect to.
	 * @param	string	Message to display on redirect. Optional, defaults to
	 * 			value set internally by controller, if any.
	 * @param	string	Message type. Optional, defaults to 'message'.
	 * @return	void
	 * @since	1.5
	 */
	public function setRedirect($url, $msg=null, $type='message')
	{
		$this->_redirect = $url;
		if ($msg !== null)
		{
			// controller may have set this directly
			$this->_message	= $msg;
		}
		$this->_messageType	= $type;
	}

	/**
	 * Method to add a message to the component message que
	 *
	 * @param	string	$message	The message to add
	 * @param	string	$type		The type of message to add
	 * @return	void
	 */
	public function addComponentMessage($message, $type='message')
	{
		//if message is somthing
		if ($message != '')
		{
			$this->componentMessageQueue[] = array(
				'message' => $message,
				'type' => strtolower($type),
				'option' => $this->_option
			);
		}

		$session = JFactory::getSession();
		$session->set('component.message.queue', $this->componentMessageQueue);
	}

	/**
	 * Method to get component messages
	 *
	 * @return	array
	 */
	public function getComponentMessage()
	{
		if (!count($this->componentMessageQueue))
		{
			$session = JFactory::getSession();
			$componentMessage = $session->get('component.message.queue');
			if (count($componentMessage))
			{
				$this->componentMessageQueue = $componentMessage;
				$session->set('component.message.queue', null);
			}
		}

		foreach ($this->componentMessageQueue as $k => $cmq)
		{
			if ($cmq['option'] != $this->_option)
			{
				$this->componentMessageQueue[$k] = array();
			}
		}

		return $this->componentMessageQueue;
	}

	/**
	 * Method to add stylesheets to the document.
	 * Defaults to current component and stylesheet name the same as component.
	 *
	 * @param	string	$option 	Component name to load stylesheet from
	 * @param	string	$script 	Name of the stylesheet to load
	 * @return	void
	 */
	protected function _getStyles($option='', $stylesheet='')
	{
		ximport('Hubzero_Document');
		$option = ($option) ? $option : $this->_option;
		Hubzero_Document::addComponentStylesheet($option, $stylesheet);
	}

	/**
	 * Method to add scripts to the document.
	 * Defaults to current component and script name the same as component.
	 *
	 * @param	string	$script 	Name of the script to load
	 * @param	string	$option 	Component name to load script from
	 * @return	void
	 */
	protected function _getScripts($script='', $option='')
	{
		$document = JFactory::getDocument();

		$option = ($option) ? $option : $this->_option;
		$script = ($script) ? $script : $this->_name;

		if (is_file(JPATH_ROOT . DS . 'components' . DS . $option . DS . $script . '.js'))
		{
			$document->addScript(DS . 'components' . DS . $option . DS . $script . '.js');
		}
	}

	/**
	 * Method to set the document path
	 *
	 * @return	void
	 */
	protected function _buildPathway()
	{
		$app = JFactory::getApplication();
		$pathway = $app->getPathway();

		if (count($pathway->getPathWay()) <= 0)
		{
			$pathway->addItem(
				JText::_(strtoupper($this->_option)),
				'index.php?option=' . $this->_option
			);
		}
		if ($this->_task)
		{
			$pathway->addItem(
				JText::_(strtoupper($this->_option) . '_' . strtoupper($this->_task)),
				'index.php?option=' . $this->_option . '&task=' . $this->_task
			);
		}
	}

	/**
	 * Method to build and set the document title
	 *
	 * @return	void
	 */
	protected function _buildTitle()
	{
		$this->_title = JText::_(strtoupper($this->_option));
		if ($this->_task)
		{
			$this->_title .= ': ' . JText::_(strtoupper($this->_option) . '_' . strtoupper($this->_task));
		}
		$document = JFactory::getDocument();
		$document->setTitle($this->_title);
	}

	/**
	 * Method to check admin access permission
	 *
	 * @return	boolean	True on success
	 */
	protected function _authorize()
	{
		// Check if they are logged in
		if ($this->juser->get('guest'))
		{
			return false;
		}

		// Check if they're a site admin (from Joomla)
		if ($this->juser->authorize($this->_option, 'manage'))
		{
			return true;
		}

		return false;
	}
}

