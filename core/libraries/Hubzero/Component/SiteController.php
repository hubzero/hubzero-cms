<?php
/**
 * @package    framework
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Hubzero\Component;

use Hubzero\Component\Exception\InvalidTaskException;
use Hubzero\Component\Exception\InvalidControllerException;
use Hubzero\Base\Obj;
use Hubzero\Document\Assets;
use ReflectionClass;
use ReflectionMethod;
use Lang;

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
class SiteController extends Obj implements ControllerInterface
{
	use \Hubzero\Base\Traits\AssetAware;

	/**
	 * The name of the component derived from the controller class name
	 *
	 * @var  string
	 */
	protected $_name = null;

	/**
	 * Container for storing overloaded data
	 *
	 * @var  array
	 */
	protected $_data = array();

	/**
	 * The task the component is to perform
	 *
	 * @var  string
	 */
	protected $_task = null;

	/**
	 * A list of executable tasks
	 *
	 * @var  array
	 */
	protected $_taskMap = array(
		'__default' => 'display'
	);

	/**
	 * The name of the task to be executed
	 *
	 * @var  string
	 */
	protected $_doTask = null;

	/**
	 * The name of this controller
	 *
	 * @var  string
	 */
	protected $_controller = null;

	/**
	 * The name of this component
	 *
	 * @var  string
	 */
	protected $_option = null;

	/**
	 * The base path to this component
	 *
	 * @var  string
	 */
	protected $_basePath = null;

	/**
	 * Redirection URL
	 *
	 * @var  string
	 * @deprecated
	 */
	protected $_redirect = null;

	/**
	 * The message to display
	 *
	 * @var  string
	 * @deprecated
	 */
	protected $_message = null;

	/**
	 * Message type
	 *
	 * @var  string
	 * @deprecated
	 */
	protected $_messageType = 'message';

	/**
	 * Constructor
	 *
	 * @param   array  $config  Optional configurations to be used
	 * @return  void
	 */
	public function __construct($config=array())
	{
		$this->_redirect    = null;
		$this->_message     = null;
		$this->_messageType = 'message';

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
					throw new InvalidControllerException(Lang::txt('Controller::__construct() : Can\'t get or parse class name.'), 500);
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
		$xMethods = get_class_methods('\\Hubzero\\Component\\SiteController');

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

		// get language object & get any loaded lang for option
		$lang   = \Lang::getRoot();
		$loaded = $lang->getPaths($this->_option);

		// Load language file if we dont have one yet
		if (!isset($loaded) || empty($loaded))
		{
			$lang->load($this->_option, $this->_basePath . '/../..');
		}

		// Set some commonly used vars
		//
		// [!] Deprecated
		//     These will be going away in a future version. Do not use.
		$this->juser    = \User::getInstance();
		$this->database = \App::get('db');
		$this->config   = \Component::params($this->_option);
	}

	/**
	 * Method to set an overloaded variable to the component
	 *
	 * @param   string  $property  Name of overloaded variable to add
	 * @param   mixed   $value     Value of the overloaded variable
	 * @return  void
	 */
	public function __set($property, $value)
	{
		$this->_data[$property] = $value;
	}

	/**
	 * Method to get an overloaded variable of the component
	 *
	 * @param   string  $property  Name of overloaded variable to retrieve
	 * @return  mixed   Value of the overloaded variable
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
	 * @param   string  $property  Name of overloaded variable to add
	 * @return  boolean
	 */
	public function __isset($property)
	{
		return isset($this->_data[$property]);
	}

	/**
	 * Determines task being called and attempts to execute it
	 *
	 * @return  void
	 */
	public function execute()
	{
		// Incoming task
		$this->_task = strtolower(\Request::getCmd('task', \Request::getWord('layout', '')));

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
			throw new InvalidTaskException(Lang::txt('The requested task "%s" was not found.', $this->_task), 404);
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

		// Instantiate a view with layout the same name as the task
		$this->view = new View(array(
			'base_path' => $this->_basePath,
			'name'      => $name,
			'layout'    => $layout
		));

		// Set some commonly used vars
		$this->view->set('option', $this->_option)
					->set('task', $doTask)
					->set('controller', $this->_controller);

		// Record the actual task being fired
		$doTask .= 'Task';

		// On before do task hook
		$this->_onBeforeDoTask();

		// Call the task
		$this->$doTask();
	}

	/**
	 * Reset the view object
	 *
	 * @param   string  $name    The name of the view
	 * @param   string  $layout  The name of the layout (optional)
	 * @return  void
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
		$this->view = new View($config);

		// Set some commonly used vars
		$this->view->option     = $this->_option;
		$this->view->task       = $name;
		$this->view->controller = $this->_controller;
	}

	/**
	 * Get the last task that is being performed or was most recently performed.
	 *
	 * @return  string  The task that is being performed or was most recently performed.
	 */
	public function getTask()
	{
		return $this->_task;
	}

	/**
	 * Register (map) a task to a method in the class.
	 *
	 * @param   string  $task    The task.
	 * @param   string  $method  The name of the method in the derived class to perform for this task.
	 * @return  object  Supports chaining.
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
	 * Unregister (unmap) a task in the class.
	 *
	 * @param   string  $task  The task.
	 * @return  object  Supports chaining.
	 */
	public function unregisterTask($task)
	{
		unset($this->_taskMap[strtolower($task)]);

		return $this;
	}

	/**
	 * Register the default task to perform if a mapping is not found.
	 *
	 * @param   string  $method  The name of the method in the derived class to perform if a named task is not found.
	 * @return  object  Supports chaining.
	 */
	public function registerDefaultTask($method)
	{
		return $this->registerTask('__default', $method);
	}

	/**
	 * Disable default task, remove __default from the taskmap
	 *
	 * When default task disabled the controller will give a 404 error if the method called doesn't exist
	 *
	 * @return  void
	 */
	public function disableDefaultTask()
	{
		return $this->unregisterTask('__default');
	}

	/**
	 * Method to redirect the application to a new URL and optionally include a message
	 *
	 * @param   string  $url   URL to redirect to. Optional.
	 * @param   string  $msg   Message to display on redirect. Optional.
	 * @param   string  $type  Message type. Optional, defaults to 'message'.
	 * @return  void
	 * @deprecated
	 */
	public function redirect($url=null, $msg=null, $type=null)
	{
		if ($url)
		{
			$this->setRedirect($url, $msg, $type);
		}

		if ($this->_redirect != null)
		{
			\App::redirect($this->_redirect, $this->_message, $this->_messageType);
		}
	}

	/**
	 * Set a URL for browser redirection.
	 *
	 * @param   string  $url   URL to redirect to.
	 * @param   string  $msg   Message to display on redirect. Optional, defaults to
	 *                         value set internally by controller, if any.
	 * @param   string  $type  Message type. Optional, defaults to 'message'.
	 * @return  object
	 * @deprecated
	 */
	public function setRedirect($url, $msg=null, $type=null)
	{
		$this->_redirect = $url;
		if ($msg !== null)
		{
			// controller may have set this directly
			$this->_message = $msg;
		}

		// Ensure the type is not overwritten by a previous call to setMessage.
		if (empty($type))
		{
			if (empty($this->_messageType))
			{
				$this->_messageType = 'message';
			}
		}
		// If the type is explicitly set, set it.
		else
		{
			$this->_messageType = $type;
		}

		return $this;
	}

	/**
	 * Set a URL for browser redirection.
	 *
	 * @param   string  $msg   Message to display on redirect. Optional, defaults to
	 *                         value set internally by controller, if any.
	 * @param   string  $type  Message type. Optional, defaults to 'message'.
	 * @return  object
	 * @deprecated
	 */
	public function setMessage($msg, $type='message')
	{
		// controller may have set this directly
		$this->_message     = $msg;
		$this->_messageType = $type;

		return $this;
	}

	/**
	 * Method to check admin access permission
	 *
	 * @return  boolean  True on success
	 * @deprecated
	 */
	protected function _authorize()
	{
		// Check if they are logged in
		if ($this->juser->isGuest())
		{
			return false;
		}

		if ($this->juser->authorise('core.admin', $this->_option))
		{
			return true;
		}

		return false;
	}

	/**
	 * Perform before actually calling the given task
	 *
	 * @return  void
	 */
	protected function _onBeforeDoTask()
	{
		// Do nothing - override in subclass
	}
}
