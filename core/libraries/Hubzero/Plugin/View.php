<?php
/**
 * @package    framework
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Hubzero\Plugin;

use Hubzero\View\View as AbstractView;
use Hubzero\Document\Assets;
use ReflectionClass;
use Exception;

/**
 * Base class for a plugin View
 */
class View extends AbstractView
{
	/**
	 * Folder
	 *
	 * @var  string
	 */
	protected $_folder = null;

	/**
	 * Folder
	 *
	 * @var  string
	 */
	protected $_element = null;

	/**
	 * Constructor
	 *
	 * @param   array  $config  A named configuration array for object construction.
	 * @return  void
	 */
	public function __construct($config = array())
	{
		// Set the override path
		//
		// For a plugin view this is the template root it may be overridden
		// from. Left alone it is the site template and the base view's
		// override_root (the supergroup template, then the site template)
		// does the searching; given explicitly it is the only root searched,
		// and '' turns overrides off.
		if (!array_key_exists('override_path', $config))
		{
			$config['override_path'] = '';

			if (\App::has('template'))
			{
				$config['override_path'] = \App::get('template')->path;
			}
		}
		elseif (!array_key_exists('override_root', $config))
		{
			$config['override_root'] = $config['override_path'] ? array($config['override_path'] . '/html') : array();
		}
		$this->_overridePath = $config['override_path'];

		// Template override roots, highest priority first (this constructor
		// does not go through the base one, which would otherwise set them)
		if (!array_key_exists('override_root', $config))
		{
			$config['override_root'] = array();

			foreach (\Hubzero\Template\Overrides::roots() as $root)
			{
				$config['override_root'][] = $root . '/html';
			}
		}
		$this->_overrideRoot = array_values(array_filter((array) $config['override_root']));

		// Set the view name
		if (!array_key_exists('folder', $config))
		{
			$config['folder'] = $this->getFolder();
		}
		$this->_folder = $config['folder'];

		// Set the view name
		if (!array_key_exists('element', $config))
		{
			$config['element'] = $this->getElement();
		}
		$this->_element = $config['element'];

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
			if (defined('PATH_APP'))
			{
				$config['base_path'] = PATH_APP . DIRECTORY_SEPARATOR . 'plugins' . DIRECTORY_SEPARATOR . $this->_folder . DIRECTORY_SEPARATOR . $this->_element;

				if (!file_exists($config['base_path']) && defined('PATH_CORE'))
				{
					$config['base_path'] = PATH_CORE . DIRECTORY_SEPARATOR . 'plugins' . DIRECTORY_SEPARATOR . $this->_folder . DIRECTORY_SEPARATOR . $this->_element;
				}
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
			$config['layout'] = 'default';
		}
		$this->setLayout($config['layout']);

		// Set the site's base URL
		$this->baseurl = \Request::base(true);
	}

	/**
	 * Method to get the plugin folder
	 *
	 * The model name by default parsed using the classname, or it can be set
	 * by passing a $config['folder'] in the class constructor
	 *
	 * @return  string  The name of the model
	 */
	public function getFolder()
	{
		$folder = $this->_folder;

		if (empty($folder))
		{
			$r = new ReflectionClass($this);
			if ($r->inNamespace())
			{
				$bits = explode('\\', $r->getNamespaceName());

				// Should match either:
				//   Plugins\Folder\Element
				//   Components\Folder\Plugins\Element
				$folder = strtolower($bits[1]);
			}
			else
			{
				throw new Exception('Cannot get or parse view class name.', 500);
			}
		}

		return $folder;
	}

	/**
	 * Method to get the plugin folder
	 *
	 * The model name by default parsed using the classname, or it can be set
	 * by passing a $config['folder'] in the class constructor
	 *
	 * @return  string  The name of the model
	 */
	public function getElement()
	{
		$element = $this->_element;

		if (empty($element))
		{
			$r = new ReflectionClass($this);
			if ($r->inNamespace())
			{
				$bits = explode('\\', $r->getNamespaceName());

				// Should match either:
				//   Plugins\Folder\Element
				//   Components\Folder\Plugins\Element
				$element = strtolower($bits[2]);

				if (strtolower($bits[0]) == 'components')
				{
					$element = strtolower($bits[3]);
				}
			}
			else
			{
				throw new Exception('Cannot get or parse view class name.', 500);
			}
		}

		return $element;
	}

	/**
	 * Sets an entire array of search paths for templates or resources.
	 *
	 * @param   string        $type  The type of path to set, typically 'template'.
	 * @param   string|array  $path  The new set of search paths. If null or false, resets to the current directory only.
	 * @return  void
	 */
	protected function setPath($type, $path)
	{
		$type = strtolower($type);

		// Clear out the prior search dirs
		$this->_path[$type] = array();

		// Actually add the user-specified directories
		$this->addPath($type, $path);

		// Always add the fallback directories as last resort
		if ($type == 'template' && $this->_overridePath && $this->_overrideRoot)
		{
			$option = 'plg_' . $this->_folder . '_' . $this->_element;
			$option = preg_replace('/[^A-Z0-9_\.-]/i', '', $option);

			// Lowest priority first: addPath() pushes onto the top of the stack
			foreach (array_reverse($this->_overrideRoot) as $root)
			{
				$this->addPath($type, $root . DIRECTORY_SEPARATOR . $option . DIRECTORY_SEPARATOR . $this->getName());
			}
		}
	}

	/**
	 * Create a plugin view and return it
	 *
	 * @param   string  $layout  View layout
	 * @param   string  $name    View name
	 * @return  object
	 */
	public function view($layout, $name=null)
	{
		// If we were passed only a view model, just render it.
		if ($layout instanceof AbstractView)
		{
			return $layout;
		}

		$view = new self(array(
			'folder'  => $this->_folder,
			'element' => $this->_element,
			'name'    => ($name ? $name : $this->_name),
			'layout'  => $layout
		));
		$view->set('folder', $this->_folder)
		     ->set('element', $this->_element);

		return $view;
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
		if (!static::hasHelper($method))
		{
			foreach ($this->_path['helper'] as $path)
			{
				$file = $path . DIRECTORY_SEPARATOR . $method . '.php';
				if (file_exists($file))
				{
					include_once $file;
					break;
				}
			}

			// Namespaced
			$invokable1 = '\\Plugins\\' . ucfirst($this->_folder) . '\\' . ucfirst($this->_element) . '\\Helpers\\' . ucfirst($method);

			// Old naming scheme "PluginFolderElementHelperMethod"
			$invokable2 = 'Plugin' . ucfirst($this->_folder) . ucfirst($this->_element) . 'Helper' . ucfirst($method);

			$callback = null;
			if (class_exists($invokable1))
			{
				$callback = new $invokable1();
			}
			else if (class_exists($invokable2))
			{
				$callback = new $invokable2();
			}

			if (is_callable($callback))
			{
				$callback->setView($this);

				$this->helper($method, $callback);
			}
		}

		return parent::__call($method, $parameters);
	}
}
