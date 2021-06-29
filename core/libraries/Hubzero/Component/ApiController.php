<?php
/**
 * @package    framework
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Hubzero\Component;

use Hubzero\Component\Exception\InvalidTaskException;
use Hubzero\Component\Exception\InvalidControllerException;
use Hubzero\Http\Response;
use Hubzero\Utility\Date;
use ReflectionClass;
use ReflectionMethod;
use stdClass;
use Request;
use Route;
use Event;
use Lang;
use User;
use App;

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
	protected $_name = null;

	/**
	 * The task the component is to perform
	 *
	 * @var	 string
	 */
	protected $_task = null;

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
	 * The name of the model
	 *
	 * Default is a singular inflection of a plural controller name
	 *
	 * @var  string
	 */
	protected $_model = null;

	/**
	 * Is this a dynamically generated endpoint?
	 *
	 * @var  bool
	 */
	protected $isDynamic = false;

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

		$r = new ReflectionClass($this);

		if (!$r->inNamespace())
		{
			throw new InvalidControllerException(Lang::txt('JLIB_APPLICATION_ERROR_INVALID_CONTROLLER_CLASS'), 500);
		}

		// Set the name
		if (!isset($config['name']) || !$config['name'])
		{
			// Components\Component\Api\Controllers\Controller
			$segments = explode('\\', $r->getName());
			$config['name'] = strtolower($segments[1]);
		}
		$this->_name = $config['name'];

		// Set the controller name
		if (!isset($config['controller']) || !$config['controller'])
		{
			// Components\Component\Api\Controllers\Controller
			$config['controller'] = strtolower($r->getShortName());
		}
		$this->_controller = $config['controller'];

		// Set the component name
		$this->_option = 'com_' . $this->_name;

		// Is this a dynamically created endpoint?
		// If so, we'll need to do some route parsing on the fly
		if ($r->getName() == 'Hubzero\\Component\\ApiController')
		{
			$this->isDynamic = true;

			$request = App::get('request');
			$segment = $request->segment(3);

			if ($segment)
			{
				if ($segment == 'list')
				{
					$request->setVar('task', $request->getCmd('task', $segment));
				}
				else
				{
					$request->setVar('task', $request->getCmd('task', 'read'));
					$request->setVar($this->resolveModel()->getPrimaryKey(), $segment);
				}
			}
		}

		// Get all the public methods of this class
		foreach ($r->getMethods(ReflectionMethod::IS_PUBLIC) as $method)
		{
			$name = $method->getName();

			// Ensure task isn't in the exclude list and ends in 'Task'
			if (!in_array($name, array('registerTask', 'unregisterTask'))
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
		$this->_task = strtolower(Request::getCmd('task', ''));

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
			throw new InvalidTaskException(Lang::txt('JLIB_APPLICATION_ERROR_TASK_NOT_FOUND', $this->_task), 404);
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
		if (!App::get('authn')['user_id'])
		{
			App::abort(403, Lang::txt('JGLOBAL_AUTH_ACCESS_DENIED'));
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

		if ($this->isDynamic)
		{
			$model = $this->resolveModel();
			$properties = $model->getStructure()->getTableColumns($model->getTableName());
			$properties = $this->normalizeProperties($properties);

			$output->version = 1;
		}

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
				$output->errors[] = Lang::txt(
					'JLIB_APPLICATION_ERROR_DOCBLOCK_MISSING',
					$method->getName(),
					str_replace(PATH_ROOT, '', $classReflector->getFileName())
				);
				continue;
			}

			// create endpoint data array
			$endpoint = array(
				'name'        => substr($method->getName(), 0, -4),
				'description' => preg_replace('/\s+/', ' ', $phpdoc->getShortDescription()),
				'method'      => '',
				'uri'         => '',
				'parameters'  => array()
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
						$output->errors[] = Lang::txt(
							'JLIB_APPLICATION_ERROR_DOCLBOCK_PARSING',
							$method->getName(),
							str_replace(PATH_ROOT, '', $classReflector->getFileName())
						);
						continue;
					}

					$endpoint['parameters'][] = (array) $parameter;
					continue;
				}

				if ($name == 'uri' && $method->getName() == 'indexTask')
				{
					$content .= $output->component;

					if ($this->isDynamic)
					{
						$content .= '/' . $this->_controller;
					}
				}

				// add data to endpoint data
				$endpoint[$name] = $content;
			}

			if ($this->isDynamic && $endpoint['name'] != 'index')
			{
				$endpoint['uri'] = str_replace('{component}', $output->component, $endpoint['uri']);
				$endpoint['uri'] = str_replace('{controller}', $this->_controller, $endpoint['uri']);
				$endpoint['uri'] = str_replace('{primary key}', '{' . $model->getPrimaryKey() . '}', $endpoint['uri']);

				foreach ($properties as $prop => $type)
				{
					$parameter = array(
						'name'        => $prop,
						'description' => '',
						'type'        => $type,
						'required'    => false,
						'default'     => null
					);

					if ($prop == $model->getPrimaryKey() && in_array($endpoint['name'], array('read', 'update', 'delete')))
					{
						$parameter['required'] = true;

						if (in_array($endpoint['name'], array('read', 'delete')))
						{
							$endpoint['parameters'] = array($parameter);
							break;
						}
					}

					$endpoint['parameters'][] = $parameter;
				}
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

	/**
	 * List entries
	 *
	 * @apiMethod GET
	 * @apiUri    /{component}/{controller}/list
	 * @return    void
	 */
	public function listTask()
	{
		$query = $this->resolveModel();

		$properties = $query->getStructure()->getTableColumns($query->getTableName());
		$properties = $this->normalizeProperties($properties);

		$searches = array();

		// Build a list of incoming filters from
		// the propertes of the model
		foreach ($properties as $property => $type)
		{
			if ($property == $query->getPrimaryKey())
			{
				continue;
			}

			if ($type == 'text')
			{
				$searches[] = $property;
				continue;
			}

			if ($type == 'integer')
			{
				$dflt = null;

				// 'state' is a very commonly used field and we want
				// it to default to published entries
				if ($property == 'state')
				{
					$dflt = $query::STATE_PUBLISHED;
					if (User::authorise('core.manage', $this->_option))
					{
						$dflt = null;
					}
				}

				$filters[$property] = Request::getInt($property, $dflt);

				// 'access' is another commonly used field where we
				// want to control what values are actually allowed
				if ($property == 'access')
				{
					if (is_null($filters[$property]))
					{
						$filters[$property] = User::getAuthorisedViewLevels();
					}
				}
			}
			else
			{
				$filters[$property] = Request::getVar($property);
			}
		}

		$filters['sort'] = Request::getString('sort', $query->orderBy);
		$filters['sort_Dir'] = Request::getWord('sort_Dir', $query->orderDir);

		if (!isset($properties[$filters['sort']]))
		{
			$filters['sort'] = $query->orderBy;
		}

		if (!in_array($filters['sort_Dir'], array('asc', 'desc')))
		{
			$filters['sort_Dir'] = $query->orderDir;
		}

		foreach ($filters as $field => $value)
		{
			if ($field == 'sort' || $field == 'sort_Dir')
			{
				continue;
			}

			if (is_null($value))
			{
				continue;
			}

			if (is_array($value))
			{
				$query->whereIn($field, $value);
			}
			else
			{
				if ($properties[$field] == 'string' && !$value)
				{
					continue;
				}

				if ($properties[$field] == 'date' && !$value)
				{
					continue;
				}

				$query->whereEquals($field, $value);
			}
		}

		// Are searching for anything?
		if ($search = (string)Request::getString('search'))
		{
			foreach ($searches as $i => $property)
			{
				if ($i == 0)
				{
					$query->whereLike($property, $search, 1);
				}
				else
				{
					$query->orWhereLike($property, $search, 1);
				}
			}
			$query->resetDepth();
		}

		// Get a total record count
		$total = with(clone $query)->total();

		// Build the response
		$response = new stdClass;
		$response->records = array();
		$response->total = $total;

		if ($response->total)
		{
			$base = rtrim(Request::base(), '/');

			$rows = $query
				->order($filters['sort'], $filters['sort_Dir'])
				->paginated('limitstart', 'limit')
				->rows();

			foreach ($rows as $row)
			{
				$obj = $row->toObject();
				foreach ($properties as $property => $type)
				{
					if ($type == 'date')
					{
						if ($obj->$property && $obj->$property != '0000-00-00 00:00:00')
						{
							$obj->$property = with(new Date($obj->$property))->format('Y-m-d\TH:i:s\Z');
						}
					}
					if ($type == 'text')
					{
						unset($obj->$property);
					}
				}
				if (method_exists($row, 'link'))
				{
					$obj->url = str_replace('/api', '', $base . '/' . ltrim(Route::url($row->link()), '/'));
				}

				$response->records[] = $obj;
			}
		}

		$this->send($response);
	}

	/**
	 * Create an entry
	 *
	 * @apiMethod POST
	 * @apiUri    /{component}/{controller}
	 * @return    void
	 */
	public function createTask()
	{
		$this->requiresAuthentication();

		$name = $this->resolveModel();

		$properties = $model->getStructure()->getTableColumns($model->getTableName());
		$properties = $this->normalizeProperties($properties);

		foreach ($properties as $property => $type)
		{
			if ($property == $model->getPrimaryKey())
			{
				continue;
			}

			if ($type == 'integer')
			{
				$fields[$property] = Request::getInt($property, 0, 'post');
			}
			/*else if ($type == 'date')
			{
				$fields[$property] = Request::getString($property, with(new Date('now'))->toSql(), 'post');
			}*/
			else
			{
				$fields[$property] = Request::getVar($property, null, 'post');
			}
		}

		if (!$model->set($fields))
		{
			App::abort(500, Lang::txt('JLIB_APPLICATION_ERROR_BIND_FAILED', $model->getError()));
		}

		// Trigger before save event
		$result = Event::trigger('on' . $model->getModelName() . 'BeforeSave', array(&$model, true));

		// Save the data
		if (!$model->save())
		{
			App::abort(500, Lang::txt('JLIB_APPLICATION_ERROR_SAVE_FAILED', $model->getError()));
		}

		// Trigger after save event
		Event::trigger('on' . $model->getModelName() . 'AfterSave', array(&$model, true));

		// Log activity
		if (method_exists($model, 'link'))
		{
			$base = rtrim(Request::base(), '/');
			$url  = str_replace('/api', '', $base . '/' . ltrim(Route::url($model->link()), '/'));
		}

		$recipients = array();

		if ($model->hasAttribute('created_by'))
		{
			$recipients[] = $model->get('created_by');
		}

		Event::trigger('system.logActivity', [
			'activity' => [
				'action'      => 'created',
				'scope'       => $this->_name . '.' . $model->getModelName(),
				'scope_id'    => $model->get($model->getPrimaryKey()),
				'description' => Lang::txt(
					'JLIB_ACTIVITY_ITEM_CREATED',
					$model->getModelName() . ' #' . $model->get($model->getPrimaryKey())
				),
				'details'     => $model->toArray()
			],
			'recipients' => $recipients
		]);

		/*if ($model->hasAttribute('created_by')
		 && $model->hasAttribute('anonymous'))
		{
			$model->set('created_by', 0);
		}*/

		// Format timestamps before sending output
		foreach ($properties as $property => $type)
		{
			if ($type == 'date' && $model->get($property) && $model->get($property) != '0000-00-00 00:00:00')
			{
				$model->set($property, with(new Date($model->get($property)))->format('Y-m-d\TH:i:s\Z'));
			}
		}

		// Send results
		$this->send($model->toObject());
	}

	/**
	 * Read an entry
	 *
	 * @apiMethod GET
	 * @apiUri    /{component}/{controller}/{primary key}
	 * @return    void
	 */
	public function readTask()
	{
		$model = $this->resolveModel();

		// Load record
		$model = $model
			->whereEquals($model->getPrimaryKey(), Request::getVar($model->getPrimaryKey()))
			->row();

		if ($model->isNew())
		{
			App::abort(404, Lang::txt('JLIB_APPLICATION_ERROR_ITEM_NOT_FOUND'));
		}

		// Format timestamps before sending output
		$properties = $model->getStructure()->getTableColumns($model->getTableName());
		$properties = $this->normalizeProperties($properties);

		foreach ($properties as $property => $type)
		{
			if ($type == 'date' && $model->get($property) && $model->get($property) != '0000-00-00 00:00:00')
			{
				$model->set($property, with(new Date($model->get($property)))->format('Y-m-d\TH:i:s\Z'));
			}
		}

		// Send results
		$this->send($model->toObject());
	}

	/**
	 * Update an entry
	 *
	 * @apiMethod PUT
	 * @apiUri    /{component}/{controller}/{primary key}
	 * @return    void
	 */
	public function updateTask()
	{
		// Require authenitcation
		$this->requiresAuthentication();

		$model = $this->resolveModel();

		// Load record
		$model = $model
			->whereEquals($model->getPrimaryKey(), Request::getVar($model->getPrimaryKey()))
			->row();

		if ($model->isNew())
		{
			App::abort(404, Lang::txt('JLIB_APPLICATION_ERROR_ITEM_NOT_FOUND'));
		}

		// Collect data
		$properties = $model->getStructure()->getTableColumns($model->getTableName());
		$properties = $this->normalizeProperties($properties);

		foreach ($properties as $property => $type)
		{
			if ($property == $model->getPrimaryKey())
			{
				continue;
			}

			if ($type == 'integer')
			{
				$fields[$property] = Request::getInt($property, $model->get($property, 0));
			}
			else
			{
				$fields[$property] = Request::getVar($property, $model->get($property));
			}
		}

		if (!$model->set($fields))
		{
			App::abort(500, Lang::txt('JLIB_APPLICATION_ERROR_BIND_FAILED', $model->getError()));
		}

		// Trigger before save event
		$result = Event::trigger('on' . $model->getModelName() . 'BeforeSave', array(&$model, true));

		// Save the data
		if (!$model->save())
		{
			App::abort(500, Lang::txt('JLIB_APPLICATION_ERROR_SAVE_FAILED', $model->getError()));
		}

		// Trigger after save event
		Event::trigger('on' . $model->getModelName() . 'AfterSave', array(&$model, true));

		// Log activity
		if (method_exists($model, 'link'))
		{
			$base = rtrim(Request::base(), '/');
			$url  = str_replace('/api', '', $base . '/' . ltrim(Route::url($model->link()), '/'));
		}

		$recipients = array();

		if ($model->hasAttribute('created_by'))
		{
			$recipients[] = $model->get('created_by');
		}
		if ($model->hasAttribute('modified_by'))
		{
			$recipients[] = $model->get('modified_by');
		}

		Event::trigger('system.logActivity', [
			'activity' => [
				'action'      => 'updated',
				'scope'       => $this->_name . '.' . $model->getModelName(),
				'scope_id'    => $model->get($model->getPrimaryKey()),
				'description' => Lang::txt(
					'JLIB_ACTIVITY_ITEM_UPDATED',
					$model->getModelName() . ' #' . $model->get($model->getPrimaryKey())
				),
				'details'     => $model->toArray()
			],
			'recipients' => $recipients
		]);

		// Format timestamps before sending output
		foreach ($properties as $property => $type)
		{
			if ($type == 'date' && $model->get($property) && $model->get($property) != '0000-00-00 00:00:00')
			{
				$model->set($property, with(new Date($model->get($property)))->format('Y-m-d\TH:i:s\Z'));
			}
		}

		// Send results
		$this->send($model->toObject());
	}

	/**
	 * Delete an entry
	 *
	 * @apiMethod DELETE
	 * @apiUri    /{component}/{controller}/{primary key}
	 * @return    void
	 */
	public function deleteTask()
	{
		// Require authenitcation
		$this->requiresAuthentication();

		$model = $this->resolveModel();

		// Load record
		$model = $model
			->whereEquals($model->getPrimaryKey(), Request::getVar($model->getPrimaryKey()))
			->row();

		if ($model->isNew())
		{
			App::abort(404, Lang::txt('JLIB_APPLICATION_ERROR_ITEM_NOT_FOUND'));
		}

		if (!$model->destroy())
		{
			App::abort(500, Lang::txt('JLIB_APPLICATION_ERROR_DELETE_FAILED', $model->getError()));
		}

		// Log activity
		$recipients = array();

		if ($model->hasAttribute('created_by'))
		{
			$recipients[] = $model->get('created_by');
		}
		if ($model->hasAttribute('modified_by'))
		{
			$recipients[] = $model->get('modified_by');
		}

		Event::trigger('system.logActivity', [
			'activity' => [
				'action'      => 'deleted',
				'scope'       => $this->_name . '.' . $model->getModelName(),
				'scope_id'    => $model->get($model->getPrimaryKey()),
				'description' => Lang::txt(
					'JLIB_ACTIVITY_ITEM_DELETED',
					$model->getModelName() . ' #' . $model->get($model->getPrimaryKey())
				),
				'details'     => $model->toArray()
			],
			'recipients' => $recipients
		]);

		// Send status
		$this->send(null, 204);
	}

	/**
	 * Reduce field types to a couple generic types
	 *
	 * @param   array  $properties
	 * @return  array
	 */
	protected function normalizeProperties($properties)
	{
		foreach ($properties as $property => $type)
		{
			if (preg_match('/(.*?)\(\d+\).*/i', $type, $matches))
			{
				$type = $matches[1];

				$properties[$property] = $type;
			}

			switch ($type)
			{
				case 'text':
				case 'tinytext':
				case 'mediumtext':
				case 'longtext':
				case 'blob':
					$properties[$property] = 'text';
				break;

				case 'int':
				case 'integer':
				case 'smallint':
				case 'tinyint':
				case 'bigint':
				case 'mediumint':
				case 'year':
					$properties[$property] = 'integer';
				break;

				case 'datetime':
				case 'date':
				case 'timestamp':
				case 'time':
					$properties[$property] = 'date';
				break;

				case 'varchar':
				case 'char':
				default:
					$properties[$property] = 'string';
				break;
			}
		}

		return $properties;
	}

	/**
	 * Get the model
	 *
	 * @return  object
	 * @throws  Exception
	 */
	protected function resolveModel()
	{
		if (!$this->_model)
		{
			// If no model name is explicitely set then derive the
			// name (singular) from the controller name (plural)
			if (!$this->_model)
			{
				$this->_model = \Hubzero\Utility\Inflector::singularize($this->_controller);
			}

			// Does the model name include the namespace?
			// We need a fully qualified name
			if ($this->_model && !strstr($this->_model, '\\'))
			{
				$this->_model = 'Components\\' . ucfirst($this->_name) . '\\Models\\' . ucfirst(strtolower($this->_model));
			}
		}

		$model = $this->_model;

		// Make sure the class exists
		if (!class_exists($model))
		{
			$file = explode('\\', $model);
			$file = strtolower(end($file));

			$path = \Component::path($this->_option) . '/models/' . $file . '.php';

			require_once $path;

			if (!class_exists($model))
			{
				App::abort(500, Lang::txt('JLIB_APPLICATION_ERROR_MODEL_GET_NAME', $model));
			}
		}

		return new $model;
	}
}
