<?php
/**
 * @package    framework
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Hubzero\Component;

use Hubzero\View\View as AbstractView;
use Hubzero\Document\Assets;

/**
 * Class for a component View
 * 
 * @TODO: Change all public attributes of View class to protected
 * when all references have been fixed to use get()/set()
 */
class View extends AbstractView
{
	/**
	 * Layout name
	 *
	 * @var  string
	 */
	public $_layout = 'display';

	/**
	 * Base URL
	 *
	 * @var string
	 */
	public $baseurl;

	/**
	 * Current component
	 *
	 * @var string
	 */
	public $option;

	/**
	 * Current controller
	 *
	 * @var string
	 */
	public $controller;

	/**
	 * Current task
	 *
	 * @var string
	 */
	public $task;

	/**
	 * Constructor
	 *
	 * @param   array  $config  A named configuration array for object construction.<br/>
	 * @return  void
	 */
	public function __construct($config = array())
	{
		// Set the override path (call before parent constructor)
		if (!array_key_exists('override_path', $config))
		{
			$config['override_path'] = array();

			if (\App::has('template'))
	{
				$config['override_path'][] = \App::get('template')->path;
			}
		} elseif (!is_array($config['override_path'])) {
			$config['override_path'] = array($config['override_path']);
		}

		parent::__construct($config);

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
		if ($type == 'template' && $this->_overridePath)
		{
			$component = strtolower(\App::get('request')->getCmd('option'));
			$component = preg_replace('/[^A-Z0-9_\.-]/i', '', $component);

			foreach ($this->_overridePath as $overridePath) {
				$this->addPath($type, $overridePath . DIRECTORY_SEPARATOR . 'html' . DIRECTORY_SEPARATOR . $component . DIRECTORY_SEPARATOR . $this->getName());
			}
		}
	}

	/**
	 * Create a component view and return it
	 *
	 * @param   string  $layout  View layout
	 * @param   string  $name    View name
	 * @return  View
	 */
	public function view($layout, $name=null)
	{
		// If we were passed only a view model, just render it.
		if ($layout instanceof AbstractView)
		{
			return $layout;
		}

		$view = new self(array(
			'base_path' => $this->_basePath,
			'name'      => ($name ? $name : $this->_name),
			'layout'    => $layout,
			'override_path' => $this->_overridePath
		));
		$view->set('option', $this->option)
		     ->set('controller', $this->controller)
		     ->set('task', $this->task);

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

			$option = ($this->option ? $this->option : \Request::getCmd('option'));
			$option = ucfirst(substr($option, 4));

			// Namespaced
			$invokable1 = '\\Components\\' . $option . '\\Helpers\\' . ucfirst($method);

			// Old naming scheme "OptionHelperMethod"
			$invokable2 = $option . 'Helper' . ucfirst($method);

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
