<?php
/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Components\Blog\Models;

use Hubzero\Base\Obj;
use Component;
use Lang;
use User;
use Date;

require_once __DIR__ . DS . 'entry.php';

/**
 * Blog archive model class
 */

/** @phpstan-consistent-constructor */
class Archive extends Obj
{
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
	 * Get a count of, model for, or list of entries
	 *
	 * @param   array   $filters  Filters to apply to data retrieval
	 * @return  object
	 */
	public function entries($filters = array())
	{
		if (!isset($filters['scope']))
		{
			$filters['scope'] = (string) $this->get('scope');
		}
		if (!isset($filters['scope_id']))
		{
			$filters['scope_id'] = (int) $this->get('scope_id');
		}

		$results = Entry::all()
			->including(['creator', function ($creator){
				$creator->select('*');
			}])
			->including(['comments', function ($comment){
				$comment
					->select('id')
					->select('entry_id')
					->whereIn('state', array(
						Comment::STATE_PUBLISHED,
						Comment::STATE_FLAGGED
					));
			}]);

		if ($filters['scope'])
		{
			$results->whereEquals('scope', $filters['scope'])
				->whereEquals('scope_id', $filters['scope_id']);
		}

		if (isset($filters['state']))
		{
			if (!is_array($filters['state']))
			{
				$filters['state'] = array($filters['state']);
			}
			$results->whereIn('state', $filters['state']);
		}

		if (isset($filters['access']))
		{
			$results->whereIn('access', $filters['access']);
		}

		if (isset($filters['year']) && $filters['year'] != 0)
		{
			if (isset($filters['month']) && $filters['month'] != 0)
			{
				$startdate = $filters['year'] . '-' . $filters['month'] . '-01 00:00:00';

				if ($filters['month']+1 == 13)
				{
					$year  = $filters['year'] + 1;
					$month = 1;
				}
				else
				{
					$month = ($filters['month']+1);
					$year  = $filters['year'];
				}
				$enddate = sprintf("%4d-%02d-%02d 00:00:00", $year, $month, 1);
			}
			else
			{
				$startdate = $filters['year'] . '-01-01 00:00:00';
				$enddate   = ($filters['year']+1) . '-01-01 00:00:00';
			}

			$results->where('publish_up', '>=', $startdate)
					->where('publish_up', '<', $enddate);
		}
		else
		{
			$results->where('publish_up', 'IS', null, 'and', 1)
						->orWhere('publish_up', '<=', Date::toSql(), 1)
						->resetDepth();
		}

		if (!isset($filters['authorized']) || !$filters['authorized'])
		{
			$results->where('publish_down', 'IS', null, 'and', 1)
						->orWhere('publish_down', '>=', Date::toSql(), 1)
						->resetDepth();
		}

		if (isset($filters['search']) && $filters['search'])
		{
			$filters['search'] = '%' . strtolower(stripslashes($filters['search'])) . '%';
			$results->whereLike('title', $filters['search'], 1)
				->orWhereLike('content', $filters['search'], 1)
				->resetDepth();
		}

		if (isset($filters['limit']) && $filters['limit'])
		{
			$results->limit($filters['limit']);
		}

		return $results;
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
				include_once $path;
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
