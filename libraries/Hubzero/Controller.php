<?php
/**
 * HUBzero CMS
 *
 * Copyright 2005-2013 Purdue University. All rights reserved.
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
 * @copyright Copyright 2005-2013 Purdue University. All rights reserved.
 * @license   http://www.gnu.org/licenses/lgpl-3.0.html LGPLv3
 */

// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die('Restricted access');

/**
 * @see JView
 */
jimport('joomla.application.component.view');

/**
 * Base controller for components to extend.
 * 
 * Accepts an array of configuration values to the constructor. If no config 
 * passed, it will automatically determine the component and controller names.
 * Internally, sets the $database, $user, $view, and component $config.
 * 
 * Executable tasks are determined by method name. All public methods that end in 
 * "Task" (e.g., displayTask, editTask) are callable by the end user.
 * 
 * View name defaults to controller name with layout defaulting to task name. So,
 * a $controller of "One" and a $task of "two" will map to:
 *
 *    /{component name}
 *        /views
 *            /one
 *                /tmpl
 *                    /two.php
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
	protected $_taskMap = array(
		'__default' => 'display'
	);

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
	 * The name of this component
	 *
	 * @param string
	 */
	protected $_option = null;

	/**
	 * The base path to this component
	 *
	 * @param string
	 */
	protected $_basePath = null;

	/**
	 * Redirection URL
	 *
	 * @param string
	 */
	protected $_redirect = null;

	/**
	 * The message to display
	 *
	 * @param string
	 */
	protected $_message = null;

	/**
	 * Message type
	 *
	 * @param string
	 */
	protected $_messageType = 'message';

	/**
	 * Constructor
	 *
	 * @access	public
	 * @param	array	$config		Optional configurations to be used
	 * @return	void
	 */
	public function __construct($config=array())
	{
		$this->_redirect    = null;
		$this->_message     = null;
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
				$cls = get_class($this);
				if (strstr($cls, '\\'))
				{
					$r = explode('\\', $cls);
				}
				else if (!preg_match('/(.*)Controller/i', $cls, $r))
				{
					return JError::raiseError(500, JText::_('Controller::__construct() : Can\'t get or parse class name.'));
				}

				$this->_name = strtolower($r[1]);
			}
		}

		if (array_key_exists('base_path', $config)) 
		{
			$this->_basePath = $config['base_path'];
		} 
		else 
		{
			$this->_basePath = JPATH_COMPONENT;
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
			if ((!in_array($name, $xMethods) || $name == 'displayTask')
			 && substr(strtolower($name), -4) == 'task')
			{
				// Remove the 'Task' suffix
				$name = substr($name, 0, -4);
				// Auto register the methods as tasks.
				$this->_taskMap[strtolower($name)] = $name;
			}
		}

		// Set some commonly used vars
		$this->juser    = JFactory::getUser();
		$this->database = JFactory::getDBO();
		$this->config   = JComponentHelper::getParams($this->_option);

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
		if (isset($this->_data[$property])) 
		{
			return $this->_data[$property];
		}
	}

	/**
	 * Method to set an overloaded variable to the component
	 *
	 * @param	string	$property	Name of overloaded variable to add
	 * @param	mixed	$value 		Value of the overloaded variable
	 * @return	void
	 */
	public function __isset($property)
	{
		return isset($this->_data[$property]);
	}

	/**
	 * Determines task being called and attempts to execute it
	 *
	 * @return	void
	 */
	public function execute()
	{
		// Incoming task
		$this->_task = strtolower(JRequest::getWord('task', JRequest::getWord('layout', '')));

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
			return JError::raiseError(404, JText::sprintf('THE REQUESTED RESOURCE WAS NOT FOUND', $this->_task));
		}

		$cls = get_class($this);
		// Attempt to parse the controller name from the class name
		if ((ucfirst($this->_name) . 'Controller') != $cls
		 && preg_match('/(\w)Controller(.*)/i', $cls, $r))
		{
			$this->_controller = strtolower($r[2]);
			$name   = $this->_controller;
			$layout = preg_replace('/[^A-Z0-9_]/i', '', $doTask);
		}
		// Namepsaced component
		else if (preg_match('/(.?)Controllers\\\(.*)/i', $cls, $r))
		{
			$this->_controller = strtolower($r[2]);
			$name   = $this->_controller;
			$layout = preg_replace('/[^A-Z0-9_]/i', '', $doTask);
		}
		// No controller name found - single controller component
		else
		{
			$name = $doTask;
		}

		// Instantiate a view with layout the same name as the task
		$this->view = new JView(array(  //\Hubzero\View\
			'base_path' => $this->_basePath,
			'name'      => $name,
			'layout'    => $layout
		));

		// Set some commonly used vars
		$this->view->option     = $this->_option;
		$this->view->task       = $doTask;
		$this->view->controller = $this->_controller;

		// Record the actual task being fired
		$doTask .= 'Task';
		$this->_doTask = $doTask;

		// Call the task
		$this->$doTask();
	}

	/**
	 * Reset the view object
	 *
	 * @param	string	The name of the view
	 * @param	string	The name of the layout (optional)
	 * @return	void
	 */
	public function setView($name, $layout=null)
	{
		$config = array(
			'name' => $name
		);
		if ($layout)
		{
			$config['layout'] = $layout;
		}
		$this->view = new JView($config);

		// Set some commonly used vars
		$this->view->option     = $this->_option;
		$this->view->task       = $name;
		$this->view->controller = $this->_controller;
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
		if (in_array(strtolower($method), $this->_taskMap))
		{
			$this->_taskMap[strtolower($task)] = $method;
		}

		return $this;
	}

	/**
	 * Disable default task, remove __default from the taskmap
	 *
	 * When default task disabled the controller will give a 404 error if the method called doesn't exist
	 *
	 * @return	void
	 */
	public function disableDefaultTask()
	{
		unset($this->_taskMap['__default']);

		return $this;
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
			//preserve component messages after redirect
			if (count($this->componentMessageQueue))
			{
				$session = JFactory::getSession();
				$session->set('component.message.queue', $this->componentMessageQueue);
			}

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

		//if message is somthing
		if ($message != '')
		{
			$this->componentMessageQueue[] = array(
				'message' => $message,
				'type'    => strtolower($type),
				'option'  => $this->_option
			);
		}

		return $this;
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
	 * Clear the component message queue
	 *
	 * @return	object
	 */
	public function clearComponentMessage()
	{
		$this->componentMessageQueue = array();

		return $this;
	}

	/**
	 * Method to add stylesheets to the document.
	 * Defaults to current component and stylesheet name the same as component.
	 *
	 * @param	string	$option 	Component name to load stylesheet from
	 * @param	string	$script 	Name of the stylesheet to load
	 * @param	boolean	$system 	Pull contents from shared /media/system folder
	 * @return	void
	 */
	protected function _getStyles($option='', $stylesheet='', $system=false)
	{
		ximport('Hubzero_Document');

		$option = ($option) ? $option : $this->_option;
		if (substr($option, 0, strlen('com_')) !== 'com_')
		{
			$option = 'com_' . $option;
		}

		if ($system)
		{
			Hubzero_Document::addSystemStylesheet($stylesheet);
		}
		else 
		{
			Hubzero_Document::addComponentStylesheet($option, $stylesheet);
		}
	}

	/**
	 * Method to add scripts to the document.
	 * Defaults to current component and script name the same as component.
	 *
	 * @param	string	$script 	Name of the script to load
	 * @param	string	$option 	Component name to load script from
	 * @param	boolean	$system 	Pull contents from shared /media/system folder
	 * @return	void
	 */
	protected function _getScripts($script='', $option='', $system=false)
	{
		ximport('Hubzero_Document');

		$option = ($option) ? $option : $this->_option;
		if (substr($option, 0, strlen('com_')) !== 'com_')
		{
			$option = 'com_' . $option;
		}
		$script = ($script) ? $script : $this->_name;

		if ($system)
		{
			Hubzero_Document::addSystemScript($script);
		}
		else 
		{
			Hubzero_Document::addComponentScript($option, $script);
		}
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

		if (version_compare(JVERSION, '1.6', 'ge'))
		{
			if ($this->juser->authorise('core.admin', $this->_option))
			{
				return true;
			}
		}
		else 
		{
			// Check if they're a site admin (from Joomla)
			if ($this->juser->authorize($this->_option, 'manage'))
			{
				return true;
			}
		}

		return false;
	}
}

