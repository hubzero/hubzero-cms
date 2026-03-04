<?php
/**
 * @package    framework
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Hubzero\View;

use Hubzero\Base\Obj;
use Hubzero\View\Exception\InvalidLayoutException;
use Exception;

/**
 * Base class for a View
 *
 * Inspired, in parts, by Joomla's JView class
 */
class View extends Obj
{
	use \Hubzero\Base\Traits\Escapable;

	/**
	 * The name of the view
	 *
	 * @var  string
	 */
	protected $_name = null;

	/**
	 * The base path of the view
	 *
	 * @var  string
	 */
	protected $_basePath = null;

	/**
	 * Root directory of the override
	 *
	 * @var  string
	 */
	protected $_overrideRoot = null;

	/**
	 * The override path
	 *
	 * @var  string
	 */
	protected $_overridePath = null;

	/**
	 * Layout name
	 *
	 * @var  string
	 */
	protected $_layout = 'default';

	/**
	 * Layout extension
	 *
	 * @var  string
	 */
	protected $_layoutExt = 'php';

	/**
	 * Layout template
	 *
	 * @var  string
	 */
	protected $_layoutTemplate = '_';

	/**
	 * The set of search directories for resources (templates)
	 *
	 * @var  array
	 */
	protected $_path = array(
		'template' => array(),
		'helper'   => array()
	);

	/**
	 * The name of the default template source file.
	 *
	 * @var  string
	 */
	protected $_template = null;

	/**
	 * The output of the template script.
	 *
	 * @var  string
	 */
	protected $_output = null;

	/**
	 * The registered helpers.
	 *
	 * @var  array
	 */
	protected static $helpers = array();

	/**
	 * Constructor
	 *
	 * @param   array  $config  A named configuration array for object construction.<br/>
	 *                          name: the name (optional) of the view (defaults to the view class name suffix).<br/>
	 *                          charset: the character set to use for display<br/>
	 *                          escape: the name (optional) of the function to use for escaping strings<br/>
	 *                          base_path: the parent path (optional) of the views directory (defaults to the component folder)<br/>
	 *                          template_path: the path (optional) of the layout directory (defaults to base_path + /views/ + view name<br/>
	 *                          override_root: the root directory (optional) of the template override directory
	 *                          override_path: the path (optional) of the template files inside override_root
	 *                          helper_path: the path (optional) of the helper files (defaults to base_path + /helpers/)<br/>
	 *                          layout: the layout (optional) to use to display the view
	 * @return  void
	 */
	public function __construct($config = array())
	{
		// Set the override path
		//
		// NOTE: This needs to come before getName()
		// as it calls setPath()
		if (!array_key_exists('override_root', $config))
		{
			$config['override_root'] = '';

			if (\App::has('template'))
			{
				$config['override_root'] = \App::get('template')->path . '/html';
			}
		}
		$this->_overrideRoot = $config['override_root'];

		if (array_key_exists('override_path', $config))
		{
			$this->_overridePath = $config['override_path'];
		}

		// Set the view name
		if (!array_key_exists('name', $config))
		{
			$config['name'] = $this->getName();
		}
		$this->_name = $config['name'];

		// Set the charset (used by the variable escaping functions)
		if (array_key_exists('charset', $config))
		{
			$this->_charset = $config['charset'];
		}

		// User-defined escaping callback
		if (array_key_exists('escape', $config))
		{
			$this->setEscape($config['escape']);
		}

		// Set a base path for use by the view
		if (!array_key_exists('base_path', $config))
		{
			$config['base_path'] = '';

			if (defined('PATH_COMPONENT'))
			{
				$config['base_path'] = PATH_COMPONENT;
			}
		}
		$this->_basePath = $config['base_path'];

		// Set the default template search path
		if (!array_key_exists('template_path', $config))
		{
			$config['template_path'] = $this->_basePath . '/views/' . $this->getName() . '/tmpl';
		}
		$this->setPath('template', $config['template_path']);

		// Set the default helper search path
		if (!array_key_exists('helper_path', $config))
		{
			$config['helper_path'] = $this->_basePath . '/helpers';
		}
		$this->setPath('helper', $config['helper_path']);

		// Set the layout
		if (!array_key_exists('layout', $config))
		{
			$config['layout'] = $this->_layout;
		}
		$this->setLayout($config['layout']);

		// Set the site's base URL
		$this->baseurl = \App::get('request')->base(true);
	}

	/**
	 * Execute and display a template script.
	 *
	 * @param   string  $tpl  The name of the template file to parse; automatically searches through the template paths.
	 * @return  mixed   A string if successful, otherwise an exception.
	 */
	public function display($tpl = null)
	{
		$result = $this->loadTemplate($tpl);

		if ($result instanceof Exception)
		{
			return $result;
		}

		echo $result;
	}

	/**
	 * Assigns variables to the view script via differing strategies.
	 *
	 * This method is overloaded; you can assign all the properties of
	 * an object, an associative array, or a single value by name.
	 *
	 * You are not allowed to set variables that begin with an underscore;
	 * these are either private properties for View or private variables
	 * within the template script itself.
	 *
	 * <code>
	 * $view = new View;
	 *
	 * // Assign directly
	 * $view->var1 = 'something';
	 * $view->var2 = 'else';
	 *
	 * // Assign by name and value
	 * $view->assign('var1', 'something');
	 * $view->assign('var2', 'else');
	 *
	 * // Assign by assoc-array
	 * $ary = array('var1' => 'something', 'var2' => 'else');
	 * $view->assign($obj);
	 *
	 * // Assign by object
	 * $obj = new stdClass;
	 * $obj->var1 = 'something';
	 * $obj->var2 = 'else';
	 * $view->assign($obj);
	 *
	 * </code>
	 *
	 * @return  boolean  True on success, false on failure.
	 */
	public function assign()
	{
		// Get the arguments; there may be 1 or 2.
		$arg0 = @func_get_arg(0);
		$arg1 = @func_get_arg(1);

		// Assign by object
		if (is_object($arg0))
		{
			// Assign public properties
			foreach (get_object_vars($arg0) as $key => $val)
			{
				if (substr($key, 0, 1) != '_')
				{
					$this->$key = $val;
				}
			}
			return true;
		}

		// Assign by associative array
		if (is_array($arg0))
		{
			foreach ($arg0 as $key => $val)
			{
				if (substr($key, 0, 1) != '_')
				{
					$this->$key = $val;
				}
			}
			return true;
		}

		// Assign by string name and mixed value.

		// We use array_key_exists() instead of isset() because isset()
		// fails if the value is set to null.
		if (is_string($arg0) && substr($arg0, 0, 1) != '_' && func_num_args() > 1)
		{
			$this->$arg0 = $arg1;
			return true;
		}

		// $arg0 was not object, array, or string.
		return false;
	}

	/**
	 * Get the layout path.
	 *
	 * @return  string  The layout name
	 */
	public function getBasePath()
	{
		return $this->_basePath;
	}

	/**
	 * Set the layout path.
	 *
	 * @param   string  $path
	 * @return  object
	 */
	public function setBasePath($path)
	{
		$this->_basePath = $path;

		return $this;
	}

	/**
	 * Get the layout.
	 *
	 * @return  string
	 */
	public function getLayout()
	{
		return $this->_layout;
	}

	/**
	 * Get the layout template.
	 *
	 * @return  string
	 */
	public function getLayoutTemplate()
	{
		return $this->_layoutTemplate;
	}

	/**
	 * Get the site template.
	 *
	 * @return  string
	 */
	public function getOverridePath()
	{
		return $this->_overridePath;
	}

	/**
	 * Method to get the view name
	 *
	 * The model name by default parsed using the classname, or it can be set
	 * by passing a $config['name'] in the class constructor
	 *
	 * @return  string  The name of the model
	 */
	public function getName()
	{
		if (empty($this->_name))
		{
			$this->_name = \App::get('request')->getCmd('controller');

			if (!$this->_name)
			{
				$r = null;

				if (!preg_match('/View((view)*(.*(view)?.*))$/i', get_class($this), $r))
				{
					throw new Exception('Cannot get or parse view class name.', 500);
				}

				if (strpos($r[3], 'view'))
				{
					throw new Exception('Classname contains the substring "view" which causes problems when extracting the classname.', 500);
				}

				$this->_name = strtolower($r[3]);
			}
		}
		return $this->_name;
	}

	/**
	 * Set the name
	 *
	 * @param   string  The name to set
	 * @return  object
	 */
	public function setName($name)
	{
		$this->_name = $name;
		$this->setPath('template', $this->_basePath . '/views/' . $this->getName() . '/tmpl');

		return $this;
	}

	/**
	 * Sets the layout name to use
	 *
	 * @param   string  $layout  The layout name or a string in format <template>:<layout file>
	 * @return  object
	 */
	public function setLayout($layout)
	{
		if (strpos($layout, ':') === false)
		{
			$this->_layout = $layout;
		}
		else
		{
			// Convert parameter to array based on :
			$temp = explode(':', $layout);
			$this->_layout = $temp[1];

			// Set layout template
			$this->_layoutTemplate = $temp[0];
		}

		return $this;
	}

	/**
	 * Allows a different extension for the layout files to be used
	 *
	 * @param   string  $value  The extension.
	 * @return  string  Previous value
	 */
	public function setLayoutExt($value)
	{
		if ($value = preg_replace('/[^A-Za-z0-9]/', '', trim($value)))
		{
			$this->_layoutExt = $value;
		}

		return $this;
	}

	/**
	 * Set a path to the site template
	 *
	 * @param   string  $template
	 * @return  object
	 */
	public function setOverridePath($path)
	{
		$this->_overridePath = $path;
		return $this;
	}

	/**
	 * Adds to the stack of view script paths in LIFO order.
	 *
	 * @param   mixed  $path  A directory path or an array of paths.
	 * @return  object
	 */
	public function addTemplatePath($path)
	{
		$this->addPath('template', $path);
		return $this;
	}

	/**
	 * Adds to the stack of helper script paths in LIFO order.
	 *
	 * @param   mixed  $path  A directory path or an array of paths.
	 * @return  object
	 */
	public function addHelperPath($path)
	{
		$this->addPath('helper', $path);
		return $this;
	}

	/**
	 * Load a template file -- first look in the templates folder for an override
	 *
	 * @param   string  $tpl  The name of the template source file; automatically searches the template paths and compiles as needed.
	 * @return  string  The output of the the template script.
	 */
	public function loadTemplate($tpl = null)
	{
		// Clear prior output
		$this->_output = null;

		$template = $this->getOverridePath();
		$layout   = $this->getLayout();
		$layoutTemplate = $this->getLayoutTemplate();

		// Create the template file name based on the layout
		$file = $tpl ? $layout . '_' . $tpl : $layout;
		$file = preg_replace('/[^A-Z0-9_\.-]/i', '', $file);
		$tpl  = preg_replace('/[^A-Z0-9_\.-]/i', '', (string) $tpl);

		// Change the template folder if alternative layout is in different template
		if (isset($layoutTemplate)
		 && $layoutTemplate != '_'
		 && $layoutTemplate != $template)
		{
			$this->_path['template'] = str_replace($template ?? '', $layoutTemplate, $this->_path['template']);
		}
		// Load the template script
		$this->_template = $this->find(
			$this->_path['template'],
			$this->createFileName('template', array('name' => $file))
		);
		// If alternate layout can't be found, fall back to default layout
		if ($this->_template == false)
		{
			$this->_template = $this->find(
				$this->_path['template'],
				$this->createFileName('', array('name' => 'default' . ($tpl ? '_' . $tpl : $tpl)))
			);
		}
		if ($this->_template != false)
		{
			// Unset so as not to introduce into template scope
			unset($tpl);
			unset($file);

			// Never allow a 'this' property
			if (isset($this->this))
			{
				unset($this->this);
			}

			// Start capturing output into a buffer
			ob_start();

			// Include the requested template filename in the local scope
			// (this will execute the view logic).
			include $this->_template;

			// Done with the requested template; get the buffer and
			// clear it.
			$this->_output = ob_get_contents();
			ob_end_clean();

			return $this->_output;
		}

		throw new InvalidLayoutException(sprintf('Layout %s not found', $file), 404);
	}

	/**
	 * Sets an entire array of search paths for templates or resources.
	 *
	 * @param   string  $type  The type of path to set, typically 'template'.
	 * @param   mixed   $path  The new search path, or an array of search paths.  If null or false, resets to the current directory only.
	 * @return  void
	 */
	protected function setPath($type, $path)
	{
		$type = strtolower($type);

		// Clear out the prior search dirs
		$this->_path[$type] = array();

		// Add view directories without the '/tmpl' legacy directory
		if ($type == 'template' && basename($path) == 'tmpl')
		{
			// Push to the bottom of the stack
			$this->addPath($type, dirname($path));
		}

		// Actually add the user-specified directories
		$this->addPath($type, $path);

		// Always add the fallback directories as last resort
		if ($type == 'template' && $this->_overrideRoot)
		{
			// Set the alternative template search dir
			if (empty($this->_overridePath))
			{
				$component = strtolower(\App::get('request')->getCmd('option'));
				$component = preg_replace('/[^A-Z0-9_\.-]/i', '', $component);
			}
			else
			{
				$component = ltrim($this->_overridePath, DIRECTORY_SEPARATOR);
			}

			$path = $this->_overrideRoot . DIRECTORY_SEPARATOR . $component . DIRECTORY_SEPARATOR . $this->getName();
			$this->addPath($type, $path);
		}
	}

	/**
	 * Adds to the search path for templates and resources.
	 *
	 * @param   string  $type  The type of path to add.
	 * @param   mixed   $path  The directory or stream, or an array of either, to search.
	 * @return  void
	 */
	protected function addPath($type, $path)
	{
		// Just force to array
		settype($path, 'array');

		// Loop through the path directories
		foreach ($path as $dir)
		{
			// no surrounding spaces allowed!
			$dir = trim($dir);

			// Add trailing separators as needed
			if (substr($dir, -1) != DIRECTORY_SEPARATOR)
			{
				// Directory
				$dir .= DIRECTORY_SEPARATOR;
			}

			// Add to the top of the search dirs
			array_unshift($this->_path[$type], $dir);
		}
	}

	/**
	 * Create the filename for a resource
	 *
	 * @param   string  $type   The resource type to create the filename for
	 * @param   array   $parts  An associative array of filename information
	 * @return  string  The filename
	 */
	protected function createFileName($type, $parts = array())
	{
		$filename = strtolower($parts['name']);

		$ext = 'php';

		if ($type == 'template')
		{
			$ext = $this->_layoutExt;
		}

		return $filename . '.' . $ext;
	}

	/**
	 * Find a file from a list of paths
	 *
	 * @param   array   $paths
	 * @param   string  $file
	 * @return  mixed   FALSE if not found, string if found
	 */
	protected function find($paths, $file)
	{
		$paths = is_array($paths) ? $paths : array($paths);
		foreach ($paths as $path)
		{
			$fullname = $path . $file;
			// Is the path based on a stream?
			if (strpos($path, '://') === false)
			{
				// Not a stream, so do a realpath() to avoid directory
				// traversal attempts on the local file system.
				$path     = realpath($path);
				$fullname = realpath($fullname);
			}

			// The substr() check added to make sure that the realpath()
			// results in a directory registered so that
			// non-registered directories are not accessible via directory
			// traversal attempts.
			if (file_exists($fullname) && substr($fullname, 0, strlen($path)) == $path)
			{
				return $fullname;
			}
		}

		return false;
	}

	/**
	 * Get the string contents of the view.
	 *
	 * @return  string
	 */
	public function __toString()
	{
		return $this->loadTemplate();
	}

	/**
	 * Register a custom helper.
	 *
	 * @param   string    $name
	 * @param   callable  $helper
	 * @return  void
	 * @since   1.3.1
	 */
	public function helper($name, $helper)
	{
		static::$helpers[$name] = $helper;
	}

	/**
	 * Checks if helper is registered
	 *
	 * @param   string   $name
	 * @return  boolean
	 * @since   1.3.1
	 */
	public function hasHelper($name)
	{
		return isset(static::$helpers[$name]);
	}

	/**
	 * Dynamically handle calls to the class.
	 *
	 * @param   string  $method
	 * @param   array   $parameters
	 * @return  mixed
	 * @throws  \BadMethodCallException
	 * @since   1.3.1
	 */
	public function __call($method, $parameters)
	{
		if (static::hasHelper($method))
		{
			$callback = static::$helpers[$method]->setView($this);

			return call_user_func_array($callback, $parameters);
		}

		$invokable = __NAMESPACE__ . '\\Helper\\' . ucfirst(strtolower($method));

		if (class_exists($invokable))
		{
			$callback = new $invokable();

			if (is_callable($callback))
			{
				$callback->setView($this);

				$this->helper($method, $callback);

				return call_user_func_array($callback, $parameters);
			}
		}

		throw new \BadMethodCallException("Method {$method} does not exist.");
	}
}
