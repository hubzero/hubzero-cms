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

namespace Hubzero\View;

use Hubzero\Base\Object;
use Hubzero\View\Exception\InvalidLayoutException;
use Hubzero\View\Exception\InvalidHelperManagerException;

/**
 * Base class for a View
 *
 * Class holding methods for displaying presentation data.
 */
class View extends Object
{
	/**
	 * The name of the view
	 *
	 * @var    array
	 */
	protected $_name = null;

	/**
	 * The base path of the view
	 *
	 * @var    string
	 */
	protected $_basePath = null;

	/**
	 * Layout name
	 *
	 * @var    string
	 */
	protected $_layout = 'default';

	/**
	 * Layout extension
	 *
	 * @var    string
	 */
	protected $_layoutExt = 'php';

	/**
	 * Layout template
	 *
	 * @var    string
	 */
	protected $_layoutTemplate = '_';

	/**
	 * The set of search directories for resources (templates)
	 *
	 * @var array
	 */
	protected $_path = array(
		'template' => array(),
		'helper'   => array()
	);

	/**
	 * The name of the default template source file.
	 *
	 * @var string
	 */
	protected $_template = null;

	/**
	 * The output of the template script.
	 *
	 * @var string
	 */
	protected $_output = null;

	/**
	 * Callback for escaping.
	 *
	 * @var string
	 */
	protected $_escape = 'htmlspecialchars';

	/**
	 * Charset to use in escaping mechanisms; defaults to urf8 (UTF-8)
	 *
	 * @var string
	 */
	protected $_charset = 'UTF-8';

	/**
	 * HelperManager
	 *
	 * @var object
	 */
	public $helpers;

	/**
	 * Constructor
	 *
	 * @param   array  $config  A named configuration array for object construction.<br/>
	 *                          name: the name (optional) of the view (defaults to the view class name suffix).<br/>
	 *                          charset: the character set to use for display<br/>
	 *                          escape: the name (optional) of the function to use for escaping strings<br/>
	 *                          base_path: the parent path (optional) of the views directory (defaults to the component folder)<br/>
	 *                          template_plath: the path (optional) of the layout directory (defaults to base_path + /views/ + view name<br/>
	 *                          helper_path: the path (optional) of the helper files (defaults to base_path + /helpers/)<br/>
	 *                          layout: the layout (optional) to use to display the view<br/>
	 */
	public function __construct($config = array())
	{
		// Set the view name
		if (empty($this->_name))
		{
			if (array_key_exists('name', $config))
			{
				$this->_name = $config['name'];
			}
			else
			{
				$this->_name = $this->getName();
			}
		}

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
		if (array_key_exists('base_path', $config))
		{
			$this->_basePath = $config['base_path'];
		}
		else
		{
			$this->_basePath = JPATH_COMPONENT;
		}

		// Set the default template search path
		if (array_key_exists('template_path', $config))
		{
			// User-defined dirs
			$this->_setPath('template', $config['template_path']);
		}
		else
		{
			$this->_setPath('template', $this->_basePath . '/views/' . $this->getName() . '/tmpl');
		}

		// Set the default helper search path
		if (array_key_exists('helper_path', $config))
		{
			// User-defined dirs
			$this->_setPath('helper', $config['helper_path']);
		}
		else
		{
			$this->_setPath('helper', $this->_basePath . '/helpers');
		}

		// Set the layout
		if (array_key_exists('layout', $config))
		{
			$this->setLayout($config['layout']);
		}
		else
		{
			$this->setLayout('default');
		}

		$this->baseurl = \JURI::base(true);
	}

	/**
	 * Execute and display a template script.
	 *
	 * @param   string  $tpl  The name of the template file to parse; automatically searches through the template paths.
	 * @return  mixed  A string if successful, otherwise a JError object.
	 */
	public function display($tpl = null)
	{
		$result = $this->loadTemplate($tpl);
		if ($result instanceof \Exception)
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
	 * Escapes a value for output in a view script.
	 *
	 * If escaping mechanism is either htmlspecialchars or htmlentities, uses
	 * {@link $_encoding} setting.
	 *
	 * @param   mixed  $var  The output to escape.
	 * @return  mixed  The escaped value.
	 */
	public function escape($var)
	{
		if (in_array($this->_escape, array('htmlspecialchars', 'htmlentities')))
		{
			return call_user_func($this->_escape, $var, ENT_COMPAT, $this->_charset);
		}

		return call_user_func($this->_escape, $var);
	}

	/**
	 * Get the layout.
	 *
	 * @return  string  The layout name
	 */
	public function getBasePath()
	{
		return $this->_basePath;
	}

	/**
	 * Get the layout.
	 *
	 * @return  string  The layout name
	 */
	public function setBasePath($path)
	{
		$this->_basePath = $path;
		return $this;
	}

	/**
	 * Get the layout.
	 *
	 * @return  string  The layout name
	 */
	public function getLayout()
	{
		return $this->_layout;
	}

	/**
	 * Get the layout template.
	 *
	 * @return  string  The layout template name
	 */
	public function getLayoutTemplate()
	{
		return $this->_layoutTemplate;
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
			$this->_name = \JRequest::getCmd('controller');
			if (!$this->_name)
			{
				$r = null;
				if (!preg_match('/View((view)*(.*(view)?.*))$/i', get_class($this), $r))
				{
					\JError::raiseError(500, \JText::_('JLIB_APPLICATION_ERROR_VIEW_GET_NAME'));
				}
				if (strpos($r[3], 'view'))
				{
					\JError::raiseWarning('SOME_ERROR_CODE', JText::_('JLIB_APPLICATION_ERROR_VIEW_GET_NAME_SUBSTRING'));
				}
				$this->_name = strtolower($r[3]);
			}
		}

		return $this->_name;
	}

	/**
	 * Set the name
	 *
	 * @param  string  The name to set
	 * @return object
	 */
	public function setName($name)
	{
		$this->_name = $name;
		$this->_setPath('template', $this->_basePath . '/views/' . $this->getName() . '/tmpl');
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
	 * @return  string   Previous value
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
	 * Sets the _escape() callback.
	 *
	 * @param   mixed  $spec  The callback for _escape() to use.
	 * @return  void
	 */
	public function setEscape($spec)
	{
		$this->_escape = $spec;
		return $this;
	}

	/**
	 * Adds to the stack of view script paths in LIFO order.
	 *
	 * @param   mixed  $path  A directory path or an array of paths.
	 * @return  void
	 */
	public function addTemplatePath($path)
	{
		$this->_addPath('template', $path);
		return $this;
	}

	/**
	 * Adds to the stack of helper script paths in LIFO order.
	 *
	 * @param   mixed  $path  A directory path or an array of paths.
	 * @return  void
	 */
	public function addHelperPath($path)
	{
		$this->_addPath('helper', $path);
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

		$template = \JFactory::getApplication()->getTemplate();
		$layout = $this->getLayout();
		$layoutTemplate = $this->getLayoutTemplate();

		// Create the template file name based on the layout
		$file = isset($tpl) ? $layout . '_' . $tpl : $layout;

		// Clean the file name
		$file = preg_replace('/[^A-Z0-9_\.-]/i', '', $file);
		$tpl = isset($tpl) ? preg_replace('/[^A-Z0-9_\.-]/i', '', $tpl) : $tpl;

		// Load the language file for the template
		$lang = \JFactory::getLanguage();
		$lang->load('tpl_' . $template, JPATH_BASE, null, false, false)
			|| $lang->load('tpl_' . $template, JPATH_THEMES . "/$template", null, false, false)
			|| $lang->load('tpl_' . $template, JPATH_BASE, $lang->getDefault(), false, false)
			|| $lang->load('tpl_' . $template, JPATH_THEMES . "/$template", $lang->getDefault(), false, false);

		// Change the template folder if alternative layout is in different template
		if (isset($layoutTemplate) && $layoutTemplate != '_' && $layoutTemplate != $template)
		{
			$this->_path['template'] = str_replace($template, $layoutTemplate, $this->_path['template']);
		}

		// Load the template script
		jimport('joomla.filesystem.path');
		$filetofind = $this->_createFileName('template', array('name' => $file));
		$this->_template = \JPath::find($this->_path['template'], $filetofind);

		// If alternate layout can't be found, fall back to default layout
		if ($this->_template == false)
		{
			$filetofind = $this->_createFileName('', array('name' => 'default' . (isset($tpl) ? '_' . $tpl : $tpl)));
			$this->_template = \JPath::find($this->_path['template'], $filetofind);
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
		else
		{
			throw new InvalidLayoutException(\JText::sprintf('JLIB_APPLICATION_ERROR_LAYOUTFILE_NOT_FOUND', $file), 500);
		}
	}

	/**
	 * Load a helper file
	 *
	 * @param   string  $hlp  The name of the helper source file automatically searches the helper paths and compiles as needed.
	 * @return  void
	 */
	public function loadHelper($hlp = null)
	{
		// Clean the file name
		$file = preg_replace('/[^A-Z0-9_\.-]/i', '', $hlp);

		// Load the template script
		jimport('joomla.filesystem.path');
		$helper = \JPath::find($this->_path['helper'], $this->_createFileName('helper', array('name' => $file)));

		if ($helper != false)
		{
			// Include the requested template filename in the local scope
			include_once $helper;
		}
	}

	/**
	 * Sets an entire array of search paths for templates or resources.
	 *
	 * @param   string  $type  The type of path to set, typically 'template'.
	 * @param   mixed   $path  The new search path, or an array of search paths.  If null or false, resets to the current directory only.
	 * @return  void
	 */
	protected function _setPath($type, $path)
	{
		// Clear out the prior search dirs
		$this->_path[$type] = array();

		// Actually add the user-specified directories
		$this->_addPath($type, $path);

		// Always add the fallback directories as last resort
		switch (strtolower($type))
		{
			case 'template':
				$app = \JFactory::getApplication();

				// Set the alternative template search dir
				if (isset($app))
				{
					$component = \JApplicationHelper::getComponentName();
					$component = preg_replace('/[^A-Z0-9_\.-]/i', '', $component);

					$this->_addPath(
						'template',
						JPATH_THEMES . '/' . $app->getTemplate() . '/html/' . $component . '/' . $this->getName()
					);
				}
			break;
		}
	}

	/**
	 * Adds to the search path for templates and resources.
	 *
	 * @param   string  $type  The type of path to add.
	 * @param   mixed   $path  The directory or stream, or an array of either, to search.
	 * @return  void
	 */
	protected function _addPath($type, $path)
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
	protected function _createFileName($type, $parts = array())
	{
		$filename = '';

		switch ($type)
		{
			case 'template':
				$filename = strtolower($parts['name']) . '.' . $this->_layoutExt;
				break;

			default:
				$filename = strtolower($parts['name']) . '.php';
				break;
		}
		return $filename;
	}

	/**
	 * Set helper manager instance
	 *
	 * @param   mixed  $helpers string|HelperManager
	 * @return  object HelperManager
	 * @throws  Exception\InvalidHelperManagerException
	 */
	public function setHelperManager($helpers)
	{
		if (is_string($helpers))
		{
			if (!class_exists($helpers))
			{
				throw new InvalidHelperManagerException(sprintf(
					'Invalid helper manager class provided (%s)',
					$helpers
				));
			}
			$helpers = new $helpers();
		}
		if (!$helpers instanceof HelperManager)
		{
			throw new InvalidHelperManagerException(sprintf(
				'Helper manager must extend Hubzero\View\HelperManager; got type "%s" instead',
				(is_object($helpers) ? get_class($helpers) : gettype($helpers))
			));
		}
		$helpers->setView($this);

		$this->helpers = $helpers;

		return $this;
	}

	/**
	 * Get helper manager instance
	 *
	 * @return HelperManager
	 */
	public function helpers($helpers=null)
	{
		if (null === $this->helpers)
		{
			if (null === $helpers)
			{
				$helpers = new HelperManager();
			}

			if (is_string($helpers))
			{
				if (!class_exists($helpers))
				{
					throw new InvalidHelperManagerException(\JText::sprintf(
						'Invalid helper manager class provided (%s)',
						$helpers
					));
				}
				$helpers = new $helpers();
			}

			if (!$helpers instanceof HelperManager)
			{
				throw new InvalidHelperManagerException(\JText::sprintf(
					'Helper manager must extend Hubzero\View\HelperManager; got type "%s" instead',
					(is_object($helpers) ? get_class($helpers) : gettype($helpers))
				));
			}
			$helpers->setView($this);

			$this->helpers = $helpers;
		}
		return $this->helpers;
	}

	/**
	 * Get helper instance
	 *
	 * @param   string $name    Name of helper to return
	 * @param   mixed  $options Options to pass to helper constructor (if not already instantiated)
	 * @return  AbstractHelper
	 */
	public function helper($name, array $options = null)
	{
		return $this->helpers()->get($name, $options);
	}

	/**
	 * Get the string contents of the view.
	 *
	 * @return string
	 */
	public function __toString()
	{
		return $this->loadTemplate();
	}

	/**
	 * Get the string contents of the view.
	 *
	 * @return object
	 */
	/*public function view($layout=null, $name=null)
	{
		if (!$layout)
		{
			throw new \InvalidArgumentException(
				'Either a view object or layout name must be provided.'
			);
		}

		// If we were passed only a view object, just return it.
		if ($layout instanceof View)
		{
			return $layout;
		}

		$partial = clone $this;
		$partial->setLayout($layout);
		if ($name)
		{
			$partial->setName($name);
		}

		return $partial;
	}*/
}
