<?php
/**
 * @package    hubzero-cms
 * @copyright  Copyright 2005-2019 HUBzero Foundation, LLC.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Plugins\Content\Formathtml;

use ReflectionClass;
use RuntimeException;

/**
 * Base class for wiki macros
 * Should be extended
 */
class Macro
{
	/**
	 * Name of the macro
	 *
	 * @var  string
	 */
	protected $_name = null;

	/**
	 * Container for internal data
	 *
	 * @var  array
	 */
	protected $_data  = array();

	/**
	 * Database
	 *
	 * @var  object
	 */
	protected $_db = null;

	/**
	 * Container for errors
	 *
	 * @var  string
	 */
	protected $_error = null;

	/**
	 * Container for errors
	 *
	 * @var  array
	 */
	protected $_arguments = null;

	/**
	 * Allow macro in partial parsing?
	 *
	 * @var  string
	 */
	public $allowPartial = false;

	/**
	 * Allow macro in partial parsing?
	 *
	 * @var  string
	 */
	public $linkLog = array();

	/**
	 * Instance of a macro
	 *
	 * @var  object
	 */
	static protected $thisInstance = null;

	/**
	 * Constructor
	 *
	 * @param   array  $config  Configuration options
	 * @return  void
	 */
	public function __construct($config=array())
	{
		$this->_db = \App::get('db');

		$this->args = '';

		// Set the controller name
		if (empty($this->_name))
		{
			if (isset($config['name']))
			{
				$this->_name = $config['name'];
			}
			else
			{
				// Get the reflection info
				$r = new ReflectionClass($this);

				// Is it namespaced?
				if ($r->inNamespace())
				{
					// It is! This makes things easy.
					$this->_name = strtolower($r->getShortName());
				}
				else if (preg_match('/(.*)Macro/i', get_class($this), $r))
				{
					$this->_name = strtolower($r[1]);
				}
				else
				{
					throw new RuntimeException(__CLASS__ . '::__construct(); Can\'t get or parse class name.');
				}
			}
		}
	}

	/**
	 * Set a property
	 *
	 * @param   string  $property  Name of property to set
	 * @param   mixed   $value     Value to set property to
	 * @return  void
	 */
	public function __set($property, $value)
	{
		if ($property == 'args')
		{
			$this->_arguments = null;
		}
		$this->_data[$property] = $value;
	}

	/**
	 * Get a property
	 *
	 * @param   string  $property  Name of property to retrieve
	 * @return  mixed
	 */
	public function __get($property)
	{
		if (isset($this->_data[$property]))
		{
			return $this->_data[$property];
		}
	}

	/**
	 * Get an instance of this macro, creating it if not found
	 *
	 * @param   array  $config  Configuration parameters
	 * @return  object
	 */
	public static function getInstance($config=array())
	{
		if (self::$thisInstance == null)
		{
			if (isset($config['name']))
			{
				$name = $config['name'];
			}
			else
			{
				$name = get_class();
			}
			self::$thisInstance = new $name();
		}
		return self::$thisInstance;
	}

	/**
	 * Render macro output
	 * this should be overriden by extended classes
	 *
	 * @return     void
	 */
	public function render()
	{
		// Abstract function for overloading
	}

	/**
	 * Returnt he macro's name
	 *
	 * @return  string
	 */
	public function name()
	{
		return $this->_name;
	}

	/**
	 * Returns description of macro, use, and accepted arguments
	 * this should be overriden by extended classes
	 *
	 * @return     string
	 */
	public function description()
	{
		return \Lang::txt('Not implemented.');
	}

	/**
	 * Prost process the text
	 *
	 * @param   string   $text
	 * @return  string
	 */
	public function postProcess($text)
	{
		return $text;
	}

	/**
	 * Get macro argumentss
	 *
	 * @return  array  List of arguments
	 */
	protected function getArguments()
	{
		if (!is_array($this->_arguments))
		{
			$arguments = str_replace(
				array('&nbsp;', '&quot;'),
				array(' ', '"'),
				(string)$this->args
			);
			$arguments = html_entity_decode($arguments);

			// get the args passed in
			$arguments = explode(',', $arguments);
			$arguments = array_map('trim', (array)$arguments);

			$this->_arguments = $arguments;
		}

		return $this->_arguments;
	}

	/**
	 * Get a macro argument
	 *
	 * @param   mixed   $key
	 * @param   mixed   $default
	 * @return  string
	 */
	protected function getArgument($key, $default = null)
	{
		$arguments = $this->getArguments();

		if (!$this->hasArgument($key))
		{
			return $default;
		}

		$value = $arguments[$key];

		return $value ? $value : $default;
	}

	/**
	 * Set all macro argument values
	 *
	 * @param   array   $data
	 * @return  object
	 */
	protected function setArguments($data)
	{
		$this->arguments = (array)$data;

		return $this;
	}

	/**
	 * Set a macro argument value
	 *
	 * @param   mixed   $key
	 * @param   mixed   $val
	 * @return  object
	 */
	protected function setArgument($key, $val)
	{
		$this->_arguments[$key] = $val;

		return $this;
	}

	/**
	 * Check if macro has any arguments
	 *
	 * @return  boolean
	 */
	protected function hasArguments()
	{
		$arguments = $this->getArguments();

		return !empty($arguments);
	}

	/**
	 * Check if macro has specified argument
	 *
	 * @param   string   $key
	 * @return  boolean
	 */
	protected function hasArgument($key)
	{
		$arguments = $this->getArguments();

		return isset($arguments[$key]);
	}
}
