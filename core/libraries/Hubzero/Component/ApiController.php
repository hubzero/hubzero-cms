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
use Hubzero\Http\Response;
use ReflectionClass;
use ReflectionMethod;
use stdClass;

/**
 * Base API controller for components to extend.
 */
class ApiController implements ControllerInterface
{
	/**
	 * The name of the component derived from the controller class name
	 *
	 * @var  string
	 */
	protected $_name = NULL;

	/**
	 * Container for storing overloaded data
	 *
	 * @var	 array
	 */
	protected $_data = array();

	/**
	 * The task the component is to perform
	 *
	 * @var	 string
	 */
	protected $_task = NULL;

	/**
	 * A list of executable tasks
	 *
	 * @var  array
	 */
	protected $_taskMap = array(
		'__default' => 'index'
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
	 * Response object
	 * 
	 * @var  object
	 */
	public $response = null;

	/**
	 * Methods needing Auth
	 * 
	 * @var  array
	 */
	public $authenticated = array('all');

	/**
	 * Methods skipping Auth
	 * 
	 * @var  array
	 */
	public $unauthenticated = array();

	/**
	 * Methods needing rate limiting
	 * 
	 * @var  array
	 */
	public $rateLimited = array();

	/**
	 * Methods skipping rate limiting
	 * 
	 * @var  array
	 */
	public $notRateLimited = array('all');

	/**
	 * Constructor
	 *
	 * @param   array  $config  Optional configurations to be used
	 * @return  void
	 */
	public function __construct(Response $response, $config=array())
	{
		$this->response = $response;

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
					throw new InvalidControllerException(\App::get('language')->txt('Controller::__construct() : Can\'t get or parse class name.'), 500);
				}

				$this->_name = strtolower($segments[1]);
			}
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
			if ((!in_array($name, $xMethods) || $name == 'indexTask')
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
	 * Register (map) a task to a method in the class.
	 *
	 * @param   string  $task    The task.
	 * @param   string  $method  The name of the method in the derived class to perform for this task.
	 * @return  void
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
	 * @return  object  This object to support chaining.
	 */
	public function unregisterTask($task)
	{
		unset($this->_taskMap[strtolower($task)]);

		return $this;
	}

	/**
	 * Determines task being called and attempts to execute it
	 *
	 * @return  void
	 */
	public function execute()
	{
		// Incoming task
		$this->_task = strtolower(\App::get('request')->getCmd('task', ''));

		$doTask = null;

		// Check if the default task is set
		if (!$this->_task)
		{
			if (isset($this->_taskMap['__default']))
			{
				$doTask = $this->_taskMap['__default'];
			}
		}
		// Check if the task is in the taskMap
		else if (isset($this->_taskMap[$this->_task]))
		{
			$doTask = $this->_taskMap[$this->_task];
		}

		// Raise an error (hopefully, this shouldn't happen)
		if (!$doTask)
		{
			throw new InvalidTaskException(\App::get('language')->txt('The requested task "%s" was not found.', $this->_task), 404);
		}

		// Record the actual task being fired
		$doTask .= 'Task';

		// Call the task
		$this->$doTask();
	}

	/**
	 * Check that the user is authenticated
	 *
	 * @return  void
	 */
	protected function requiresAuthentication()
	{
		if (!\App::get('authn')['user_id'])
		{
			\App::abort(404, 'Not Found');
		}
	}

	/**
	 * Set response content
	 *
	 * @param   string   $message
	 * @param   integer  $status
	 * @param   string   $reason
	 * @return  void
	 */
	public function send($message = null, $status = null, $reason = null)
	{
		$this->response->setContent($message);
		$this->response->setStatusCode($status ? $status : 200);
	}

	/**
	 * Displays available options and parameters this component offers.
	 *
	 * @apiMethod GET
	 * @apiUri    /
	 * @return    void
	 */
	public function indexTask()
	{
		// var to hold output
		$output = new stdClass();
		$output->component = substr($this->_option, 4);
		$bits = explode('v', get_class($this));
		$output->version   = str_replace('_', '.', end($bits));
		$output->tasks     = array();
		$output->errors    = array();

		// create reflection class of file
		$classReflector = new ReflectionClass($this);

		// loop through each method and process doc
		foreach ($classReflector->getMethods() as $method)
		{
			// create docblock object & make sure we have something
			$phpdoc = new \phpDocumentor\Reflection\DocBlock($method);

			// skip constructor
			if (substr($method->getName(), -4) != 'Task' || in_array($method->getName(), array('registerTask', 'unregisterTask')))
			{
				continue;
			}

			// skip method in the parent class (already processed), 
			/*if ($className != $method->getDeclaringClass()->getName())
			{
				//continue;
			}*/

			// skip if we dont have a short desc
			// but put in error
			if (!$phpdoc->getShortDescription())
			{
				$output->errors[] = sprintf('Missing docblock for method "%s" in "%s"', $method->getName(), str_replace(PATH_ROOT, '', $classReflector->getFileName()));
				continue;
			}

			// create endpoint data array
			$endpoint = array(
				'name'        => substr($method->getName(), 0, -4),
				'description' => preg_replace('/\s+/', ' ', $phpdoc->getShortDescription()), // $phpdoc->getLongDescription()->getContents()
				'method'      => '',
				'uri'         => '',
				'parameters'  => array()/*,
				'_metadata'   => array(
					'component' => $output->component,
					'version'   => $output->version,
					'method'    => $method->getName()
				)*/
			);

			// loop through each tag
			foreach ($phpdoc->getTags() as $tag)
			{
				// get tag name and content
				$name    = strtolower(str_replace('api', '', $tag->getName()));
				$content = $tag->getContent();

				// handle parameters separately
				// json decode param input
				if ($name == 'parameter')
				{
					$parameter = json_decode($content);

					if (json_last_error() != JSON_ERROR_NONE)
					{
						$output->errors[] = sprintf('Unable to parse parameter info for method "%s" in "%s"', $method->getName(), str_replace(PATH_ROOT, '', $classReflector->getFileName()));
						continue;
					}

					$endpoint['parameters'][] = (array) $parameter;
					continue;
				}

				if ($name == 'uri' && $method->getName() == 'indexTask')
				{
					$content .= $output->component;
				}

				// add data to endpoint data
				$endpoint[$name] = $content;
			}

			// add endpoint to output
			$output->tasks[] = $endpoint;
		}

		if (count($output->errors) <= 0)
		{
			unset($output->errors);
		}

		$this->send($output);
	}
}

