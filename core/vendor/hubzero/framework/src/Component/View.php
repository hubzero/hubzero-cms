<?php
/**
 * HUBzero CMS
 *
 * Copyright 2005-2015 HUBzero Foundation, LLC.
 *
 * Permission is hereby granted, free of charge, to any person obtaining a copy
 * of this software and associated documentation files (the "Software"), to deal
 * in the Software without restriction, including without limitation the rights
 * to use, copy, modify, merge, publish, distribute, sublicense, and/or sell
 * copies of the Software, and to permit persons to whom the Software is
 * furnished to do so, subject to the following conditions:
 *
 * The above copyright notice and this permission notice shall be included in
 * all copies or substantial portions of the Software.
 *
 * THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
 * IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
 * FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
 * AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
 * LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
 * OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN
 * THE SOFTWARE.
 *
 * HUBzero is a registered trademark of Purdue University.
 *
 * @package   framework
 * @author    Shawn Rice <zooley@purdue.edu>
 * @copyright Copyright 2005-2015 HUBzero Foundation, LLC.
 * @license   http://opensource.org/licenses/MIT MIT
 */

namespace Hubzero\Component;

use Hubzero\View\View as AbstractView;
use Hubzero\Document\Assets;

/**
 * Class for a component View
 */
class View extends AbstractView
{
	/**
	 * Layout name
	 *
	 * @var  string
	 */
	protected $_layout = 'display';

	/**
	 * Constructor
	 *
	 * @param   array  $config  A named configuration array for object construction.<br/>
	 * @return  void
	 */
	public function __construct($config = array())
	{
		parent::__construct($config);

		// Set a base path for use by the view
		if (array_key_exists('base_path', $config))
		{
			$this->_basePath = $config['base_path'];
		}
		else
		{
			$this->_basePath = JPATH_COMPONENT;
		}
	}

	/**
	 * Create a component view and return it
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
			'base_path' => $this->_basePath,
			'name'      => ($name ? $name : $this->_name),
			'layout'    => $layout
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
				$file = $path . DS . $method . '.php';
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
