<?php
/**
 * HUBzero CMS
 *
 * Copyright 2005-2013 Purdue University. All rights reserved.
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
 * @copyright Copyright 2005-2013 Purdue University. All rights reserved.
 * @license   http://www.gnu.org/licenses/lgpl-3.0.html LGPLv3
 */

namespace Hubzero\View;

use Hubzero\View\View;
use Hubzero\View\Exception\InvalidHelperException;
use Hubzero\View\Helper\HelperInterface;

/**
 * Extension manager implementation for view helpers
 *
 * Enforces that helpers retrieved are instances of
 * Helper\HelperInterface. Additionally, it registers a number of default
 * helpers.
 */
class HelperManager
{
	/**
	 * Map of characters to be replaced through strtr
	 *
	 * @var array
	 */
	protected $_canonicalNamesReplacements = array(
		'-'  => '',
		'_'  => '',
		' '  => '',
		'\\' => '',
		'/'  => ''
	);

	/**
	 * Default set of helpers factories
	 *
	 * @var array
	 */
	protected $_canonicalNames = array();

	/**
	 * Default set of helpers factories
	 *
	 * @var array
	 */
	protected $_instances = array();

	/**
	 * Default set of helpers
	 *
	 * @var array
	 */
	protected $_invokableClasses = array(
		'autolink'  => 'Hubzero\View\Helper\Autolink',
		'clean'     => 'Hubzero\View\Helper\Clean',
		'editor'    => 'Hubzero\View\Helper\Editor',
		'highlight' => 'Hubzero\View\Helper\Highlight',
		'obfuscate' => 'Hubzero\View\Helper\Obfuscate',
		//'paginator' => 'Hubzero\View\Helper\Paginator',
		//'partial'   => 'Hubzero\View\Helper\Partial',
		'truncate'  => 'Hubzero\View\Helper\Truncate',
	);

	/**
	 * @var View
	 */
	protected $_view = null;

	/**
	 * Overloading: proxy to helpers
	 *
	 * Proxies to the attached plugin manager to retrieve, return, and potentially
	 * execute helpers.
	 *
	 * * If the helper does not define __invoke, it will be returned
	 * * If the helper does define __invoke, it will be called as a functor
	 *
	 * @param  string $method
	 * @param  array $argv
	 * @return mixed
	 */
	public function __call($method, $argv)
	{
		$method = $this->canonicalizeName($method);

		if (!isset($this->_instances[$method]))
		{
			$this->_instances[$method] = $this->get($method); //, $argv);
		}
		if (is_callable($this->_instances[$method]))
		{
			return call_user_func_array($this->_instances[$method], $argv);
		}
		return $this->_instances[$method];
	}

	/**
	 * Retrieve a service from the manager by name
	 *
	 * Allows passing an array of options to use when creating the instance.
	 * createFromInvokable() will use these and pass them to the instance
	 * constructor if not null and a non-empty array.
	 *
	 * @param  string $name
	 * @param  array $options
	 * @param  bool $usePeeringServiceManagers
	 * @return object
	 */
	public function get($name, $options = array())
	{
		$cName = $this->canonicalizeName($name);

		if (isset($this->_instances[$cName]))
		{
			return $this->_instances[$cName];
		}

		$instance = null;

		if (isset($this->_invokableClasses[$cName]))
		{
			$invokable = $this->_invokableClasses[$cName];
		}
		else
		{
			$file = JPATH_COMPONENT . DS . 'helpers' . DS . $name . '.php';
			if (file_exists($file))
			{
				include_once $file;
			}

			$invokable = '\\Components\\' . ucfirst(substr(\JRequest::getCmd('option'), 4)) . '\Helpers\\' . ucfirst($name);
			$invokable2 = ucfirst(substr(\JRequest::getCmd('option'), 4)) . 'Helper' . ucfirst($name);
		}

		if (class_exists($invokable))
		{
			if (!$this->has($name))
			{
				$this->setInvokableClass($name, $invokable);
			}
			$instance = new $invokable($options);
		}
		else if (class_exists($invokable2))
		{
			if (!$this->has($name))
			{
				$this->setInvokableClass($name, $invokable2);
			}
			$instance = new $invokable2($options);
		}
		else
		{
			throw new InvalidHelperException(\JText::sprintf(
				'%s: failed retrieving adapter via invokable class "%s"; class does not exist',
				get_class($this) . '::' . __FUNCTION__,
				$invokable
			));
		}

		$this->validate($instance);

		$this->injectView($instance);

		$this->_instances[$cName] = $instance;

		return $instance;
	}

	/**
	 * Set invokable class
	 *
	 * @param   string $name
	 * @param   string $invokableClass
	 * @param   bool   $shared
	 * @return  object HelperManager
	 * @throws  Exception\InvalidHelperException
	 */
	public function setInvokableClass($name, $invokableClass)
	{
		$cName = $this->canonicalizeName($name);

		if ($this->has(array($cName, $name)))
		{
			throw new InvalidHelperException(\JText::sprintf(
				'A service by the name or alias "%s" already exists and cannot be overridden; please use an alternate name',
				$name
			));
		}

		$this->_invokableClasses[$cName] = $invokableClass;

		return $this;
	}

	/**
	 * Check if a class exists
	 *
	 * @param   mixed $name string|array
	 * @return  bool
	 */
	public function has($name)
	{
		if (is_array($name))
		{
			list($cName, $rName) = $name;
		}
		else
		{
			$rName = $name;

			if (isset($this->_canonicalNames[$rName]))
			{
				$cName = $this->_canonicalNames[$name];
			}
			else
			{
				$cName = $this->canonicalizeName($name);
			}
		}

		if (
			isset($this->_invokableClasses[$cName])
			|| isset($this->_instances[$cName])
		) {
			return true;
		}

		return false;
	}

	/**
	 * Set view
	 *
	 * @param   object $view
	 * @return  object HelperManager
	 */
	public function setView(View $view)
	{
		$this->_view = $view;

		return $this;
	}

	/**
	 * Retrieve view instance
	 *
	 * @return object View
	 */
	public function getView()
	{
		return $this->_view;
	}

	/**
	 * Inject a helper instance with the registered view
	 *
	 * @param  Helper\HelperInterface $helper
	 * @return void
	 */
	public function injectView($helper)
	{
		$view = $this->getView();
		if (null === $view)
		{
			return;
		}
		$helper->setView($view);
	}

	/**
	 * Canonicalize name
	 *
	 * @param  string $name
	 * @return string
	 */
	protected function canonicalizeName($name)
	{
		if (isset($this->_canonicalNames[$name]))
		{
			return $this->_canonicalNames[$name];
		}

		// this is just for performance instead of using str_replace
		$this->_canonicalNames[$name] = strtolower(strtr($name, $this->_canonicalNamesReplacements));

		return $this->_canonicalNames[$name];
	}

	/**
	 * Validate the helper
	 *
	 * Checks that the helper loaded is an instance of Helper\HelperInterface.
	 *
	 * @param  mixed $helper
	 * @return void
	 * @throws InvalidHelperException if invalid
	 */
	public function validate($helper)
	{
		if ($helper instanceof HelperInterface)
		{
			// we're okay
			return;
		}

		throw new InvalidHelperException(\JText::sprintf(
			'Helper of type %s is invalid; must implement %s\Helper\HelperInterface',
			(is_object($helper) ? get_class($helper) : gettype($helper)),
			__NAMESPACE__
		));
	}
}
