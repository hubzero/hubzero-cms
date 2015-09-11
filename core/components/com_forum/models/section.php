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

namespace Components\Forum\Models;

use Components\Forum\Tables;
use Hubzero\Base\ItemList;
use Hubzero\Utility\String;
use LogicException;
use Lang;

require_once(dirname(__DIR__) . DS . 'tables' . DS . 'section.php');
require_once(__DIR__ . DS . 'base.php');
require_once(__DIR__ . DS . 'category.php');

/**
 * Model class for a forum section
 */
class Section extends Base
{
	/**
	 * Table class name
	 *
	 * @var object
	 */
	protected $_tbl_name = '\\Components\\Forum\\Tables\\Section';

	/**
	 * Container for instance data
	 *
	 * @var array
	 */
	private $_cache = array(
		'categories_count' => null,
		'categories'       => null,
		'category'         => null
	);

	/**
	 * Constructor
	 *
	 * @param      integer $id       Section ID (integer), alias (string), array, or object
	 * @param      string  $scope    Forum scope [site, group, course]
	 * @param      integer $scope_id Forum scope ID (group ID, couse ID)
	 * @return     void
	 */
	public function __construct($oid, $scope='site', $scope_id=0)
	{
		$this->_db = \App::get('db');

		$cls = $this->_tbl_name;
		$this->_tbl = new $cls($this->_db);

		if (!($this->_tbl instanceof \JTable))
		{
			$this->_logError(
				__CLASS__ . '::' . __FUNCTION__ . '(); ' . Lang::txt('Table class must be an instance of JTable.')
			);
			throw new LogicException(Lang::txt('Table class must be an instance of JTable.'));
		}

		if ($oid)
		{
			if (is_numeric($oid))
			{
				$this->_tbl->load($oid);
			}
			else if (is_string($oid))
			{
				$this->_tbl->loadByAlias($oid, $scope, $scope_id);
			}
			else if (is_object($oid) || is_array($oid))
			{
				$this->bind($oid);
			}
		}

		if (!$this->get('scope'))
		{
			$this->set('scope', $scope);
		}
	}

	/**
	 * Returns a reference to a forum section model
	 *
	 * @param      integer $id       Section ID (integer), alias (string), array, or object
	 * @param      string  $scope    Forum scope [site, group, course]
	 * @param      integer $scope_id Forum scope ID (group ID, couse ID)
	 * @return     object
	 */
	static function &getInstance($oid=0, $scope='site', $scope_id=0)
	{
		static $instances;

		if (!isset($instances))
		{
			$instances = array();
		}

		$key = $scope . '_' . $scope_id . '_';
		if (is_numeric($oid) || is_string($oid))
		{
			$key .= $oid;
		}
		else if (is_object($oid))
		{
			$key .= $oid->id;
		}
		else if (is_array($oid))
		{
			$key .= $oid['id'];
		}

		if (!isset($instances[$key]))
		{
			$instances[$key] = new self($oid, $scope_id, $scope);
		}

		return $instances[$key];
	}

	/**
	 * Set and get a specific category
	 *
	 * @param   mixed  $id Integer or string (ID or alias) for a category
	 * @return  object
	 */
	public function category($id=null)
	{
		if (!isset($this->_cache['category'])
		 || ($id !== null && (int) $this->_cache['category']->get('id') != $id && (string) $this->_cache['category']->get('alias') != $id))
		{
			$this->_cache['category'] = null;

			if ($this->_cache['categories'] instanceof ItemList)
			{
				foreach ($this->_cache['categories'] as $key => $category)
				{
					if ((int) $category->get('id') == $id || (string) $category->get('alias') == $id)
					{
						$this->_cache['category'] = $category;
						break;
					}
				}
			}

			if (!$this->_cache['category']);
			{
				$this->_cache['category'] = Category::getInstance($id, $this->get('id')); //, $this->get('scope'), $this->get('scope_id'));
			}
			if (!$this->_cache['category']->exists())
			{
				$this->_cache['category']->set('scope', $this->get('scope'));
				$this->_cache['category']->set('scope_id', $this->get('scope_id'));
			}
			$this->_cache['category']->set('section_alias', $this->get('alias'));
		}
		return $this->_cache['category'];
	}

	/**
	 * Get a count or list of categories
	 *
	 * @param   string  $rtrn    Data type to return?
	 * @param   array   $filters Filters to apply to query
	 * @param   boolean $clear   Clear cached data?
	 * @return  mixed
	 */
	public function categories($rtrn='', $filters=array(), $clear=false)
	{
		$filters['section_id'] = (isset($filters['section_id'])) ? $filters['section_id'] : (int) $this->get('id');
		$filters['state']      = (isset($filters['state']))      ? $filters['state']      : self::APP_STATE_PUBLISHED;
		$filters['scope']      = (isset($filters['scope']))      ? $filters['scope']      : (string) $this->get('scope');
		$filters['scope_id']   = (isset($filters['scope_id']))   ? $filters['scope_id']   : (int) $this->get('scope_id');

		switch (strtolower($rtrn))
		{
			case 'count':
				if (!isset($this->_cache['categories_count']) || $clear)
				{
					$tbl = new Tables\Category($this->_db);
					$this->_cache['categories_count'] = (int) $tbl->getCount($filters);
				}
				return $this->_cache['categories_count'];
			break;

			case 'first':
				return $this->categories('list', $filters)->first();
			break;

			case 'list':
			case 'results':
			default:
				if (!($this->_cache['categories'] instanceof ItemList) || $clear)
				{
					$tbl = new Tables\Category($this->_db);
					if (($results = $tbl->getRecords($filters)))
					{
						foreach ($results as $key => $result)
						{
							$results[$key] = new Category($result, $this->get('id'), $this->get('scope'), $this->get('group_id'));
							$results[$key]->set('section_alias', $this->get('alias'));
						}
					}
					else
					{
						$results = array();
					}
					$this->_cache['categories'] = new ItemList($results);
				}
				return $this->_cache['categories'];
			break;
		}
	}

	/**
	 * Return a count for the type of data specified
	 *
	 * @param      string $what What to count
	 * @return     integer
	 */
	public function count($what='threads')
	{
		$what = strtolower(trim($what));
		$key  = 'stats.' . $what;

		if (!isset($this->_cache[$key]))
		{
			$this->_cache[$key] = 0;

			switch ($what)
			{
				case 'categories':
					$this->_cache[$key] = $this->categories()->total();
				break;

				case 'threads':
					foreach ($this->categories() as $category)
					{
						$this->_cache[$key] += $category->count('threads');
					}
				break;

				case 'posts':
					foreach ($this->categories() as $category)
					{
						$this->_cache[$key] += $category->count('posts');
					}
				break;

				default:
					$this->setError(Lang::txt('Property value of "%" not accepted', $what));
					return $this->_cache[$key];
				break;
			}
		}

		return $this->_cache[$key];
	}

	/**
	 * Store changes to this entry
	 *
	 * @param     boolean $check Perform data validation check?
	 * @return    boolean False if error, True on success
	 */
	public function store($check=true)
	{
		// Get the entry before changes were made
		$old = new self($this->get('id'));

		// Store entry
		if (!parent::store($check))
		{
			return false;
		}

		// If the section is marked as "deleted" and it wasn't already marked as such
		if ($this->get('state') == self::APP_STATE_DELETED
		  && $old->get('state') != self::APP_STATE_DELETED)
		{
			// Collect a list of category IDs
			$cats = array();
			foreach ($this->categories('list', array('state' => -1)) as $category)
			{
				$cats[] = $category->get('id');
			}

			if (count($cats) > 0)
			{
				// Set all the threads/posts in all the categories to "deleted"
				$post = new Tables\Post($this->_db);
				if (!$post->setStateByCategory($cats, self::APP_STATE_DELETED))
				{
					$this->setError($post->getError());
				}

				// Set all the categories to "deleted"
				$cModel = new Tables\Category($this->_db);
				if (!$cModel->setStateBySection($this->get('id'), self::APP_STATE_DELETED))
				{
					$this->setError($cModel->getError());
				}
			}
		}

		return true;
	}

	/**
	 * Get the adapter
	 *
	 * @return  object
	 */
	public function adapter()
	{
		if (!$this->_adapter)
		{
			$this->_adapter = $this->_adapter();
			$this->_adapter->set('section', $this->get('alias'));
		}

		return $this->_adapter;
	}
}

