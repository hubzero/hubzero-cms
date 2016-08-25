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
 * @copyright Copyright 2005-2015 HUBzero Foundation, LLC.
 * @license   http://opensource.org/licenses/MIT MIT
 */

namespace Components\Forum\Models;

use Hubzero\Base\Object;
use Hubzero\Config\Registry;
use Component;
use Lang;
use User;

require_once(__DIR__ . DS . 'section.php');

/**
 * Model class for a forum
 */
class Manager extends Object
{
	/**
	 * Cached data
	 *
	 * @var  array
	 */
	protected $cache = array();

	/**
	 * Constructor
	 *
	 * @param   string   $scope     Forum scope [site, group, course]
	 * @param   integer  $scope_id  Forum scope ID (group ID, couse ID)
	 * @return  void
	 */
	public function __construct($scope='site', $scope_id=0)
	{
		$this->set('scope', $scope);
		$this->set('scope_id', $scope_id);
	}

	/**
	 * Returns a reference to a forum model
	 *
	 * @param   string   $scope     Forum scope [site, group, course]
	 * @param   integer  $scope_id  Forum scope ID (group ID, couse ID)
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
			$instances[$key] = new self($scope, $scope_id);
		}

		return $instances[$key];
	}

	/**
	 * Populate the forum with defaulta section and category
	 *
	 * @return  boolean
	 */
	public function setup()
	{
		// Create a default section
		$section = Section::blank()->set(array(
			'title'    => Lang::txt('COM_FORUM_SECTION_DEFAULT'),
			'scope'    => $this->get('scope'),
			'scope_id' => $this->get('scope_id'),
			'state'    => 1
		));
		if (!$section->save())
		{
			$this->setError($section->getError());
			return false;
		}

		// Create a default category
		$category = Category::blank()->set(array(
			'title'       => Lang::txt('COM_FORUM_CATEGORY_DEFAULT'),
			'description' => Lang::txt('COM_FORUM_CATEGORY_DEFAULT_DESCRIPTION'),
			'section_id'  => $section->get('id'),
			'scope'       => $this->get('scope'),
			'scope_id'    => $this->get('scope_id'),
			'state'       => 1
		));
		if (!$category->save())
		{
			$this->setError($category->getError());
			return false;
		}

		return true;
	}

	/**
	 * Get a list of sections for a forum
	 *
	 * @param   array    $filters  Filters to apply to data fetch
	 * @return  object
	 */
	public function sections($filters=array())
	{
		if (!isset($filters['scope']))
		{
			$filters['scope'] = (string) $this->get('scope');
		}
		if (!isset($filters['scope_id']))
		{
			$filters['scope_id'] = (int) $this->get('scope_id');
		}

		$model = Section::all();

		if ($filters['scope'])
		{
			$model->whereEquals('scope', $filters['scope']);
		}

		if ($filters['scope_id'] >= 0)
		{
			$model->whereEquals('scope_id', $filters['scope_id']);
		}

		if (isset($filters['state']) && $filters['state'] >= 0)
		{
			$model->whereEquals('state', (int)$filters['state']);
		}

		if (isset($filters['state']))
		{
			$model->whereEquals('state', $filters['state']);
		}

		if (isset($filters['access']))
		{
			if (!is_array($filters['access']))
			{
				$filters['access'] = array($filters['access']);
			}

			$model->whereIn('access', $filters['access']);
		}

		return $model;
	}

	/**
	 * Get categories for this forum
	 *
	 * @param   array    $filters  Filters to apply to the query
	 * @return  object
	 */
	public function categories($filters=array())
	{
		if (!isset($filters['scope']))
		{
			$filters['scope'] = (string) $this->get('scope');
		}
		if (!isset($filters['scope_id']))
		{
			$filters['scope_id'] = (int) $this->get('scope_id');
		}

		$model = Category::all();

		if ($filters['scope'])
		{
			$model->whereEquals('scope', $filters['scope']);
		}

		if ($filters['scope_id'] >= 0)
		{
			$model->whereEquals('scope_id', $filters['scope_id']);
		}

		if (isset($filters['state']))
		{
			$model->whereEquals('state', $filters['state']);
		}

		if (isset($filters['access']))
		{
			if (!is_array($filters['access']))
			{
				$filters['access'] = array($filters['access']);
			}

			$model->whereIn('access', $filters['access']);
		}

		return $model;
	}

	/**
	 * Get posts for a forum
	 *
	 * @param   array    $filters  Filters to apply to the query
	 * @return  object
	 */
	public function posts($filters=array())
	{
		if (!isset($filters['scope']))
		{
			$filters['scope'] = (string) $this->get('scope');
		}
		if (!isset($filters['scope_id']))
		{
			$filters['scope_id'] = (int) $this->get('scope_id');
		}

		$model = Post::all();

		if ($filters['scope'])
		{
			if (is_array($filters['scope']) && !empty($filters['scope']))
			{
				$model->whereIn('scope', $filters['scope']);
			}
			else
			{
				$model->whereEquals('scope', $filters['scope']);
			}
		}

		if ($filters['scope_id'] >= 0)
		{
			$model->whereEquals('scope_id', $filters['scope_id']);
		}

		if (isset($filters['state']))
		{
			$model->whereEquals('state', $filters['state']);
		}

		if (isset($filters['sticky']))
		{
			$model->whereEquals('sticky', (int)$filters['sticky']);
		}

		if (isset($filters['thread']) && $filters['thread'])
		{
			$model->whereEquals('thread', (int)$filters['thread']);
		}

		if (isset($filters['scope_sub_id']) && $filters['scope_sub_id'])
		{
			$model->whereEquals('scope_sub_id', (int)$filters['scope_sub_id']);
		}

		if (isset($filters['object_id']) && $filters['object_id'])
		{
			$model->whereEquals('object_id', (int)$filters['object_id']);
		}

		if (isset($filters['search']))
		{
			$model->whereLike('comment', $filters['search'], 1)
				->orWhereLike('title', $filters['search'], 1)
				->resetDepth();
		}

		if (isset($filters['start_id']) && $filters['start_id'])
		{
			$model->where('id', '>', (int)$filters['start_id']);
		}

		if (isset($filters['start_at']) && $filters['start_at'])
		{
			$model->where('created', '>', $filters['start_at']);
		}

		if (isset($filters['access']))
		{
			if (!is_array($filters['access']))
			{
				$filters['access'] = array($filters['access']);
			}

			$model->whereIn('access', $filters['access']);
		}

		return $model;
	}

	/**
	 * Return a count for the type of data specified
	 *
	 * @param   string   $what     What to count
	 * @param   array    $filters
	 * @return  integer
	 */
	public function count($what='threads', $filters=array())
	{
		$key = 'stats.' . strtolower(trim($what));

		if (!isset($this->cache[$key]))
		{
			$this->cache[$key] = 0;

			switch ($key)
			{
				case 'stats.sections':
					$this->cache[$key] = $this->sections($filters)->total();
				break;

				case 'stats.categories':
					$this->cache[$key] = $this->categories($filters)->total();
				break;

				case 'stats.threads':
					$this->cache[$key] = $this->posts($filters)->whereEquals('parent', 0)->total();
				break;

				case 'stats.posts':
					$this->cache[$key] = $this->posts($filters)->total();
				break;

				default:
					$this->setError(Lang::txt('Property value of "%s" not accepted', $what));
					return 0;
				break;
			}
		}

		return $this->cache[$key];
	}

	/**
	 * Check a user's authorization
	 *
	 * @param   string   $action     Action to check
	 * @param   string   $assetType  Type of asset to check
	 * @param   integer  $assetId    ID of item to check access on
	 * @return  boolean  True if authorized, false if not
	 */
	public function access($action='view', $assetType='section', $assetId=null)
	{
		if (!$this->config()->get('access-check-done', false))
		{
			$this->config()->set('access-view-' . $assetType, true);

			if (!User::isGuest())
			{
				$asset  = 'com_forum';
				if ($assetId)
				{
					$asset .= ($assetType != 'component') ? '.' . $assetType : '';
					$asset .= ($assetId) ? '.' . $assetId : '';
				}

				$at = '';
				if ($assetType != 'component')
				{
					$at .= '.' . $assetType;
				}

				// Admin
				$this->config()->set('access-admin-' . $assetType, User::authorise('core.admin', $asset));
				$this->config()->set('access-manage-' . $assetType, User::authorise('core.manage', $asset));
				// Permissions
				$this->config()->set('access-create-' . $assetType, User::authorise('core.create' . $at, $asset));
				$this->config()->set('access-delete-' . $assetType, User::authorise('core.delete' . $at, $asset));
				$this->config()->set('access-edit-' . $assetType, User::authorise('core.edit' . $at, $asset));
				$this->config()->set('access-edit-state-' . $assetType, User::authorise('core.edit.state' . $at, $asset));
				$this->config()->set('access-edit-own-' . $assetType, User::authorise('core.edit.own' . $at, $asset));
			}

			$this->config()->set('access-check-done', true);
		}

		return $this->config()->get('access-' . $action . '-' . $assetType);
	}

	/**
	 * Get the most recent post made in the forum
	 *
	 * @return  object
	 */
	public function lastActivity()
	{
		$last = Post::all()
			->whereEquals('scope', $this->get('scope'))
			->whereEquals('scope_id', $this->get('scope_id'))
			->whereEquals('state', Post::STATE_PUBLISHED)
			->whereIn('access', User::getAuthorisedViewLevels());

		return $last->order('id', 'desc')
			->limit(1)
			->row();
	}

	/**
	 * Get all available scopes
	 *
	 * @return  array
	 */
	public function scopes()
	{
		$section = Section::blank();

		$db = \App::get('db');
		$db->setQuery("
			SELECT DISTINCT s.scope, s.scope_id
			FROM " . $section->getTableName() . " AS s
			ORDER BY s.scope, s.scope_id
		");

		$results = $db->loadObjectList();

		if (!$results || !is_array($results))
		{
			$results = array();
		}

		$scope = $this->get('scope');
		$scope_id = $this->get('scope_id');

		foreach ($results as $i => $result)
		{
			$this->set('scope', $result->scope);
			$this->set('scope_id', $result->scope_id);

			$results[$i]->caption = $this->adapter()->name();
		}

		$this->set('scope', $scope);
		$this->set('scope_id', $scope_id);

		return $results;
	}

	/**
	 * Create an adapter object based on scope
	 *
	 * @return  object
	 */
	public function adapter()
	{
		if (!$this->get('scope'))
		{
			$this->set('scope', 'site');
		}

		$scope = strtolower($this->get('scope'));
		$cls = __NAMESPACE__ . '\\Adapters\\' . ucfirst($scope);

		if (!class_exists($cls))
		{
			$path = __DIR__ . DS . 'adapters' . DS . $scope . '.php';
			if (!is_file($path))
			{
				throw new \InvalidArgumentException(Lang::txt('Invalid scope of "%s"', $scope));
			}
			include_once($path);
		}

		return new $cls($this->get('scope_id'));
	}

	/**
	 * Get a configuration value
	 * If no key is passed, it returns the configuration object
	 *
	 * @param   string  $key      Config property to retrieve
	 * @param   mixed   $default  Default value to return
	 * @return  mixed
	 */
	public function config($key=null, $default=null)
	{
		if (!($this->config instanceof Registry))
		{
			$this->config = Component::params('com_forum');
		}
		if ($key)
		{
			return $this->config->get($key, $default);
		}
		return $this->config;
	}
}

