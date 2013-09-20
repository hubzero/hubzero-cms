<?php
/**
 * HUBzero CMS
 *
 * Copyright 2005-2011 Purdue University. All rights reserved.
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
 * @copyright Copyright 2005-2011 Purdue University. All rights reserved.
 * @license   http://www.gnu.org/licenses/lgpl-3.0.html LGPLv3
 */

// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die('Restricted access');

require_once(JPATH_ROOT . DS . 'components' . DS . 'com_forum' . DS . 'tables' . DS . 'section.php');
require_once(JPATH_ROOT . DS . 'components' . DS . 'com_forum' . DS . 'models' . DS . 'abstract.php');
require_once(JPATH_ROOT . DS . 'components' . DS . 'com_forum' . DS . 'models' . DS . 'iterator.php');
require_once(JPATH_ROOT . DS . 'components' . DS . 'com_forum' . DS . 'models' . DS . 'category.php');

/**
 * Courses model class for a forum
 */
class ForumModelSection extends ForumModelAbstract
{
	/**
	 * Table class name
	 * 
	 * @var object
	 */
	protected $_tbl_name = 'ForumSection';

	/**
	 * Container for instance data
	 * 
	 * @var array
	 */
	private $_cache = array();

	/**
	 * Constructor
	 * 
	 * @param      integer $id Course ID or alias
	 * @return     void
	 */
	public function __construct($oid, $scope='site', $scope_id=0)
	{
		$this->_db = JFactory::getDBO();

		$cls = $this->_tbl_name;
		$this->_tbl = new $cls($this->_db);

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

		if (!$this->get('scope'))
		{
			$this->set('scope', $scope);
		}
	}

	/**
	 * Returns a reference to a forum model
	 *
	 * This method must be invoked as:
	 *     $offering = ForumModelCourse::getInstance($alias);
	 *
	 * @param      mixed $oid Course ID (int) or alias (string)
	 * @return     object ForumModelCourse
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
			$instances[$key] = new ForumModelSection($oid, $scope_id, $scope);
		}

		return $instances[$key];
	}

	/**
	 * Set and get a specific offering
	 * 
	 * @return     void
	 */
	public function category($id=null)
	{
		if (!isset($this->_cache['category']) 
		 || ($id !== null && (int) $this->_cache['category']->get('id') != $id && (string) $this->_cache['category']->get('alias') != $id))
		{
			$this->_cache['category'] = null;
			if (isset($this->_cache['categories']) && is_a($this->_cache['categories'], 'ForumModelIterator'))
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
				$this->_cache['category'] = ForumModelCategory::getInstance($id, $this->get('id')); //, $this->get('scope'), $this->get('scope_id'));
			}
		}
		return $this->_cache['category'];
	}

	/**
	 * Get a list of categories for a forum
	 *   Accepts either a numeric array index or a string [id, name]
	 *   If index, it'll return the entry matching that index in the list
	 *   If string, it'll return either a list of IDs or names
	 * 
	 * @param      mixed $idx Index value
	 * @return     array
	 */
	public function categories($rtrn='', $filters=array(), $clear=false)
	{
		$filters['section_id'] = (isset($filters['section_id'])) ? $filters['section_id'] : (int) $this->get('id');
		$filters['state']      = (isset($filters['state']))      ? $filters['state']      : 1;
		$filters['scope']      = (isset($filters['scope']))      ? $filters['scope']      : (string) $this->get('scope');
		$filters['scope_id']   = (isset($filters['scope_id']))   ? $filters['scope_id']   : (int) $this->get('scope_id');

		switch (strtolower($rtrn))
		{
			case 'count':
				if (!isset($this->_cache['categories_count']) || $clear)
				{
					$tbl = new ForumCategory($this->_db);
					$this->_cache['categories_count'] = (int) $tbl->getCount($filters);
				}
				return $this->_cache['categories_count'];
			break;

			case 'first':
				return $this->categories('list', $filters)->fetch('first');
			break;

			case 'list':
			case 'results':
			default:
				if (!isset($this->_cache['categories']) || !is_a($this->_cache['categories'], 'ForumModelIterator') || $clear)
				{
					$tbl = new ForumCategory($this->_db);
					if (($results = $tbl->getRecords($filters)))
					{
						foreach ($results as $key => $result)
						{
							$results[$key] = new ForumModelCategory($result, $this->get('id'), $this->get('scope'), $this->get('group_id'));
						}
					}
					else
					{
						$results = array();
					}
					$this->_cache['categories'] = new ForumModelIterator($results);
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

		if (!isset($this->_stats[$what]))
		{
			$this->_stats[$what] = 0;

			switch ($what)
			{
				case 'categories':
					$this->_stats[$what] = $this->categories()->total();
				break;

				case 'threads':
					foreach ($this->categories() as $category)
					{
						$this->_stats[$what] += $category->count('threads');
					}
				break;

				case 'posts':
					foreach ($this->categories() as $category)
					{
						$this->_stats[$what] += $category->count('posts');
					}
				break;

				default:
					$this->setError(JText::_('Property value not accepted'));
					return 0;
				break;
			}
		}

		return $this->_stats[$what];
	}

	/**
	 * Store changes to this offering
	 *
	 * @param     boolean $check Perform data validation check?
	 * @return    boolean False if error, True on success
	 */
	public function store($check=true)
	{
		$res = parent::store($check);

		if (!$res)
		{
			return false;
		}

		if ($this->get('state') == 2)
		{
			$old = new ForumModelSection($this->get('id'));
			if ($old->get('state') != 2)
			{
				$cats = array();
				foreach ($this->categories('list', array('state' => -1)) as $category)
				{
					$cats[] = $category->get('id');
				}

				// Set all the threads/posts in all the categories to "deleted"
				$post = new ForumPost($this->_db);
				if (!$post->setStateByCategory($cats, 2))  /* 0 = unpublished, 1 = published, 2 = deleted */
				{
					$this->setError($post->getError());
				}

				// Set all the categories to "deleted"
				$cModel = new ForumCategory($this->_db);
				if (!$cModel->setStateBySection($this->get('id'), 2))  /* 0 = unpublished, 1 = published, 2 = deleted */
				{
					$this->setError($cModel->getError());
				}
			}
		}

		return true;
	}
}

