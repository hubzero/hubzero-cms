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

			$option = ($this->option ? $this->option : \JRequest::getCmd('option'));
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
