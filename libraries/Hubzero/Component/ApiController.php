<?php
/**
 * HUBzero CMS
 *
 * Copyright 2005-2014 Purdue University. All rights reserved.
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
 * @copyright Copyright 2005-2014 Purdue University. All rights reserved.
 * @license   http://www.gnu.org/licenses/lgpl-3.0.html LGPLv3
 */

namespace Hubzero\Component;

use Hubzero\Component\Exception\InvalidTaskException;
use Hubzero\Component\Exception\InvalidControllerException;
use Hubzero\Base\Object;
use ReflectionClass;
use ReflectionMethod;

/**
 * Base controller for components to extend.
 * 
 * Accepts an array of configuration values to the constructor. If no config 
 * passed, it will automatically determine the component and controller names.
 * Internally, sets the $database, $user, $view, and component $config.
 * 
 * Executable tasks are determined by method name. All public methods that end in 
 * "Task" (e.g., displayTask, editTask) are callable by the end user.
 */
class ApiController extends Object implements ControllerInterface
{
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
	 * Constructor
	 *
	 * @param   array $config Optional configurations to be used
	 * @return  void
	 */
	public function __construct($config=array())
	{
		// Get the reflection info
		$r = new ReflectionClass($this);

		// Is it namespaced?
		if ($r->inNamespace())
		{
			// It is! This makes things easy.
			$this->_controller = strtolower($r->getShortName());
		}

		// Set the name
		if (empty($this->_name))
		{
			if (isset($config['name']))
			{
				$this->_name = $config['name'];
			}
			else
			{
				$segments = null;
				$cls = $r->getName();

				// If namespaced...
				if (strstr($cls, '\\'))
				{
					$segments = explode('\\', $cls);
				}
				// If matching the pattern of ComponentControllerName
				else if (preg_match('/(.*)Controller(.*)/i', $cls, $segments))
				{
					$this->_controller = isset($segments[2]) ? strtolower($segments[2]) : null;
				}
				// Uh-oh!
				else
				{
					throw new InvalidControllerException(\JText::_('Controller::__construct() : Can\'t get or parse class name.'), 500);
				}

				$this->_name = strtolower($segments[1]);
			}
		}

		// Set the base path
		if (array_key_exists('base_path', $config)) 
		{
			$this->_basePath = $config['base_path'];
		} 
		else 
		{
			// Set base path relative to the controller file rather than 
			// an absolute path. This gives us a little more flexibility.
			$this->_basePath = dirname(dirname($r->getFileName()));
		}

		// Set the component name
		$this->_option = 'com_' . $this->_name;

		// Determine the methods to exclude from the base class.
		$xMethods = get_class_methods('\\Hubzero\\Component\\ApiController');

		// Get all the public methods of this class
		foreach ($r->getMethods(ReflectionMethod::IS_PUBLIC) as $method)
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
	}

	/**
	 * Method to set an overloaded variable to the component
	 *
	 * @param   string $property Name of overloaded variable to add
	 * @param   mixed  $value    Value of the overloaded variable
	 * @return  void
	 */
	public function __set($property, $value)
	{
		$this->_data[$property] = $value;
	}

	/**
	 * Method to get an overloaded variable of the component
	 *
	 * @param   string $property Name of overloaded variable to retrieve
	 * @return  mixed  Value of the overloaded variable
	 */
	public function __get($property)
	{
		if (isset($this->_data[$property])) 
		{
			return $this->_data[$property];
		}
	}

	/**
	 * Method to check if a poperty is set
	 *
	 * @param   string  $property Name of overloaded variable to add
	 * @return  boolean
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
		$this->_task = strtolower(\JRequest::getWord('task', \JRequest::getWord('layout', '')));

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
			throw new InvalidTaskException(\JText::sprintf('The requested task "%s" was not found.', $this->_task), 404);
		}

		$name = $this->_controller;
		$layout = preg_replace('/[^A-Z0-9_]/i', '', $doTask);
		if (!$this->_controller)
		{
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
		}

		// Record the actual task being fired
		$doTask .= 'Task';

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
}

