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
 * @package   hubzero-cms
 * @author    Shawn Rice <zooley@purdue.edu>
 * @copyright Copyright 2005-2015 HUBzero Foundation, LLC.
 * @license   http://opensource.org/licenses/MIT MIT
 */

namespace Components\Blog\Models;

use Components\Blog\Tables;
use Hubzero\Base\Object;
use Hubzero\Base\ItemList;
use Component;
use Lang;
use User;

require_once(__DIR__ . DS . 'entry.php');

/**
 * Blog archive model class
 */
class Archive extends Object
{
	/**
	 * Tables\Entry
	 *
	 * @var  object
	 */
	private $_tbl = null;

	/**
	 * Models\Entry
	 *
	 * @var  object
	 */
	private $_entry = null;

	/**
	 * \JDatabase
	 *
	 * @var  object
	 */
	private $_db = NULL;

	/**
	 * Registry
	 *
	 * @var  object
	 */
	private $_config;

	/**
	 * File space path
	 *
	 * @var  string
	 */
	private $_adapter;

	/**
	 * Constructor
	 *
	 * @param   string   $scope     Blog scope [site, group, member]
	 * @param   integer  $scope_id  Scope ID if scope is member or group
	 * @return  void
	 */
	public function __construct($scope='site', $scope_id=0)
	{
		$this->_db = \App::get('db');

		$this->_tbl = new Tables\Entry($this->_db);

		$this->set('scope', $scope);
		$this->set('scope_id', $scope_id);
	}

	/**
	 * Returns a reference to a blog archive model
	 *
	 * @param   string   $scope     Blog scope [site, group, member]
	 * @param   integer  $scope_id  Scope ID if scope is member or group
	 * @return  object
	 */
	static function &getInstance($scope='site', $scope_id=0)
	{
		static $instances;

		if (!isset($instances))
		{
			$instances = array();
		}

		$key = $scope . '_' . $scope_id;

		if (!isset($instances[$key]))
		{
			$instances[$key] = new static($scope, $scope_id);
		}

		return $instances[$key];
	}

	/**
	 * Get a specific entry
	 *
	 * @param   mixed   $id  String (alias) or integer (ID) of an entry
	 * @return  object
	 */
	public function entry($id=null)
	{
		if (!isset($this->_entry)
		 || ($id !== null && (int) $this->_entry->get('id') != $id && (string) $this->_entry->get('alias') != $id))
		{
			$this->_entry = Entry::getInstance($id, $this->get('scope'), $this->get('scope_id'));
		}
		return $this->_entry;
	}

	/**
	 * Get a count of, model for, or list of entries
	 *
	 * @param   string   $rtrn     Data to return
	 * @param   array    $filters  Filters to apply to data retrieval
	 * @param   boolean  $reset    Clear cached data?
	 * @return  mixed
	 */
	public function entries($rtrn='list', $filters=array(), $reset=false, $admin=true)
	{
		if (!isset($filters['scope']))
		{
			$filters['scope'] = (string) $this->get('scope');
		}
		if (!isset($filters['scope_id']))
		{
			$filters['scope_id'] = (int) $this->get('scope_id');
		}

		switch (strtolower($rtrn))
		{
			case 'count':
				return (int) $this->_tbl->find('count', $filters);
			break;

			case 'first':
				$filters['sort']     = 'publish_up';
				$filters['sort_Dir'] = 'ASC';

				$result = $this->_tbl->find('first', $filters);
				return new Entry($result);
			break;

			case 'popular':
				$filters['sort']     = 'hits';
				$filters['sort_Dir'] = 'DESC';
			break;

			case 'recent':
				$filters['sort']     = 'publish_up';
				$filters['sort_Dir'] = 'DESC';
			break;
		}

		if ($results = $this->_tbl->find('list', $filters, NULL ,$admin))
		{
			foreach ($results as $key => $result)
			{
				$results[$key] = new Entry($result);
			}
		}

		return new ItemList($results);
	}

	/**
	 * The magic call method is used to call object methods using the adapter.
	 *
	 * @param   string  $method     The name of the method called.
	 * @param   array   $arguments  The arguments of the method called.
	 * @return  array   An array of values returned by the methods called on the objects in the data set.
	 * @since   1.3.1
	 */
	public function __call($method, $arguments = array())
	{
		$callback = array($this->_adapter(), $method);

		if (is_callable($callback))
		{
			return call_user_func_array($callback, $arguments);
		}

		throw new \BadMethodCallException(Lang::txt('Method "%s" does not exist.', $method));
	}

	/**
	 * Return the adapter for this entry's scope,
	 * instantiating it if it doesn't already exist
	 *
	 * @return  object
	 * @since   1.3.1
	 */
	private function _adapter()
	{
		if (!$this->_adapter)
		{
			$scope = strtolower($this->get('scope'));

			$cls = __NAMESPACE__ . '\\Adapters\\' . ucfirst($scope);

			if (!class_exists($cls))
			{
				$path = __DIR__ . '/adapters/' . $scope . '.php';
				if (!is_file($path))
				{
					throw new \InvalidArgumentException(Lang::txt('Invalid scope of "%s"', $scope));
				}
				include_once($path);
			}

			$this->_adapter = new $cls($this->get('scope_id'));
		}
		return $this->_adapter;
	}

	/**
	 * Get a parameter from the component config
	 *
	 * @param   string  $property  Param to return
	 * @param   mixed   $default   Value to return if property not found
	 * @return  object  Registry
	 * @since   1.3.1
	 */
	public function config($property=null, $default=null)
	{
		if (!isset($this->_config))
		{
			$this->_config = Component::params('com_blog');
		}
		if ($property)
		{
			return $this->_config->get($property, $default);
		}
		return $this->_config;
	}

	/**
	 * Check a user's authorization
	 *
	 * @param   string   $action  Action to check
	 * @param   string   $item    Item type to check action against
	 * @return  boolean  True if authorized, false if not
	 */
	public function access($action='view', $item='entry')
	{
		if (!$this->config()->get('access-check-done', false))
		{
			if (User::isGuest())
			{
				$this->config()->set('access-check-done', true);
			}
			else
			{
				foreach (array('admin', 'manage', 'delete', 'edit', 'edit-state', 'edit-own') as $option)
				{
					$this->config()->set(
						'access-' . $option . '-entry',
						User::authorise('core.' . ($option == 'admin' ? 'admin' : 'manage'), $this->get('id'))
					);
				}

				$this->config()->set('access-check-done', true);
			}
		}
		return $this->config()->get('access-' . strtolower($action) . '-entry');
	}
}
