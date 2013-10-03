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

// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die('Restricted access');

require_once(JPATH_ROOT . DS . 'components' . DS . 'com_forum' . DS . 'tables' . DS . 'category.php');
require_once(JPATH_ROOT . DS . 'components' . DS . 'com_forum' . DS . 'models' . DS . 'abstract.php');
require_once(JPATH_ROOT . DS . 'components' . DS . 'com_forum' . DS . 'models' . DS . 'thread.php');

/**
 * Courses model class for a forum
 */
class ForumModelCategory extends ForumModelAbstract
{
	/**
	 * Table class name
	 * 
	 * @var object
	 */
	protected $_tbl_name = 'ForumCategory';

	/**
	 * Container for properties
	 * 
	 * @var array
	 */
	private $_cache = array();

	/**
	 * Constructor
	 * 
	 * @param      mixed   $oid        ID (integer), alias (string), array or object
	 * @param      integer $section_id Section ID
	 * @return     void
	 */
	public function __construct($oid, $section_id=0)
	{
		$this->_db = JFactory::getDBO();

		$cls = $this->_tbl_name;
		$this->_tbl = new $cls($this->_db);

		if (!($this->_tbl instanceof \JTable))
		{
			$this->_logError(
				__CLASS__ . '::' . __FUNCTION__ . '(); ' . \JText::_('Table class must be an instance of JTable.')
			);
			throw new \LogicException(\JText::_('Table class must be an instance of JTable.'));
		}

		if ($oid)
		{
			if (is_numeric($oid))
			{
				$this->_tbl->load($oid);
			}
			else if (is_string($oid))
			{
				$this->_tbl->loadByAlias($oid, $section_id);
			}
			else if (is_object($oid) || is_array($oid))
			{
				$this->bind($oid);
			}
		}

		switch (strtolower($this->get('scope')))
		{
			case 'group':
				$group = Hubzero_Group::getInstance($this->get('scope_id'));
				$this->_base = 'index.php?option=com_groups&cn=' . $group->get('cn') . '&active=forum';
			break;

			case 'course':
				$offering = CoursesModelOffering::getInstance($this->get('scope_id'));
				$course = CoursesModelCourse::getInstance($offering->get('course_id'));
				$this->_base = 'index.php?option=com_courses&gid=' . $course->get('alias') . '&offering=' . $offering->get('alias') . ($offering->section()->get('alias') != '__default' ? ':' . $offering->section()->get('alias') : '') . '&active=discussions';
			break;

			case 'site':
			default:
				$this->_base = 'index.php?option=com_forum';
			break;
		}
	}

	/**
	 * Returns a reference to a forum category model
	 *
	 * @param      mixed $oid Course ID (int) or alias (string)
	 * @return     object ForumModelCategory
	 */
	static function &getInstance($oid=null, $section_id=0) //, $scope='site', $scope_id=0)
	{
		static $instances;

		if (!isset($instances)) 
		{
			$instances = array();
		}

		if (is_numeric($oid) || is_string($oid))
		{
			$key = $section_id . '_' . $oid; //$scope . '_' . $scope_id . '_' . $oid;
		}
		else if (is_object($oid))
		{
			$key = $section_id . '_' . $oid->id; //$scope . '_' . $scope_id . '_' . $oid->id;
		}
		else if (is_array($oid))
		{
			$key = $section_id . '_' . $oid['id']; //$scope . '_' . $scope_id . '_' . $oid['id'];
		}

		if (!isset($instances[$key])) 
		{
			$instances[$key] = new ForumModelCategory($oid, $section_id); //, $scope, $scope_id);
		}

		return $instances[$key];
	}

	/**
	 * Is the category closed?
	 * 
	 * @return     boolean
	 */
	public function isClosed()
	{
		if ($this->get('closed', 0) == 1) 
		{
			return true;
		}
		return false;
	}

	/**
	 * Set and get a specific thread
	 * 
	 * @param      mixed $id ID (integer) or alias (string)
	 * @return     object ForumModelThread
	 */
	public function thread($id=null)
	{
		if (!isset($this->_cache['thread']) 
		 || ($id !== null && (int) $this->_cache['thread']->get('id') != $id))
		{
			$this->_cache['thread'] = null;

			if (isset($this->_cache['threads']) && ($this->_cache['threads'] instanceof \Hubzero\ItemList))
			{
				foreach ($this->_cache['threads'] as $key => $thread)
				{
					if ((int) $thread->get('id') == $id)
					{
						$this->_cache['thread'] = $thread;
						break;
					}
				}
			}

			if (!$this->_cache['thread'])
			{
				$this->_cache['thread'] = ForumModelThread::getInstance($id);
			}
		}
		return $this->_cache['thread'];
	}

	/**
	 * Get a list of threads for a forum
	 *   Accepts either a numeric array index or a string [id, name]
	 *   If index, it'll return the entry matching that index in the list
	 *   If string, it'll return either a list of IDs or names
	 * 
	 * @param      mixed $idx Index value
	 * @return     array
	 */
	public function threads($rtrn='list', $filters=array(), $clear=false)
	{
		$filters['category_id'] = isset($filters['category_id']) ? $filters['category_id'] : $this->get('id');
		$filters['state']       = isset($filters['state'])       ? $filters['state']       : self::APP_STATE_PUBLISHED;
		$filters['parent']      = isset($filters['parent'])      ? $filters['parent']      : 0;

		switch (strtolower($rtrn))
		{
			case 'count':
				if (!isset($this->_cache['threads_count']) || $clear)
				{
					$tbl = new ForumPost($this->_db);
					$this->_cache['threads_count'] = $tbl->getCount($filters);
				}
				return $this->_cache['threads_count'];
			break;

			case 'first':
				return $this->threads('list', $filters)->first();
			break;

			case 'list':
			case 'results':
			default:
				if (!isset($this->_cache['threads']) || !($this->_cache['threads'] instanceof \Hubzero\ItemList) || $clear)
				{
					$tbl = new ForumPost($this->_db);

					if (($results = $tbl->getRecords($filters)))
					{
						foreach ($results as $key => $result)
						{
							$results[$key] = new ForumModelThread($result);
						}
					}
					else
					{
						$results = array();
					}

					$this->_cache['threads'] = new \Hubzero\ItemList($results);
				}

				return $this->_cache['threads'];
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
				case 'threads':
					if ($this->get('threads', null) !== null)
					{
						$this->_cache[$key] += (int) $this->get('threads');
					}
					else
					{
						$this->_cache[$key] += (int) $this->threads('count');
					}
				break;

				case 'posts':
					if ($this->get('posts', null) !== null)
					{
						$this->_cache[$key] += (int) $this->get('posts');
					}
					else
					{
						foreach ($this->threads() as $thread)
						{
							$this->_cache[$key] += (int) $thread->posts('count');
						}
					}
				break;

				default:
					$this->setError(JText::sprintf('Property value of "%" not accepted', $what));
					return $this->_cache[$key];
				break;
			}
		}

		return $this->_cache[$key];
	}

	/**
	 * Generate and return various links to the entry
	 * Link will vary depending upon action desired, such as edit, delete, etc.
	 * 
	 * @param      string $type   The type of link to return
	 * @param      mixed  $params Optional string or associative array of params to append
	 * @return     string
	 */
	public function link($type='', $params=null)
	{
		$link  = $this->_base;

		switch (strtolower($this->get('scope')))
		{
			case 'group':
				$link .= '&scope='  . $this->get('section_alias');
				$link .= '/' . $this->get('alias');
			break;

			case 'course':
				$link .= '&unit='  . $this->get('section_alias');
				$link .= '&b=' . $this->get('alias');
			break;

			case 'site':
			default:
				$link .= '&section='  . $this->get('section_alias');
				$link .= '&category=' . $this->get('alias');
			break;
		}

		// If it doesn't exist or isn't published
		switch (strtolower($type))
		{
			case 'base':
				return $this->_base;
			break;

			case 'edit':
				switch (strtolower($this->get('scope')))
				{
					case 'group':
						$link .= '/edit';
					break;

					case 'course':
						$link .= '&c=edit';
					break;

					case 'site':
					default:
						$link .= '&task=edit';
					break;
				}
			break;

			case 'delete':
				switch (strtolower($this->get('scope')))
				{
					case 'group':
						$link .= '/delete';
					break;

					case 'course':
						$link .= '&c=delete';
					break;

					case 'site':
					default:
						$link .= '&task=delete';
					break;
				}
			break;

			case 'new':
			case 'newthread':
				switch (strtolower($this->get('scope')))
				{
					case 'group':
						$link .= '/new';
					break;

					case 'course':
						$link .= '&c=new';
					break;

					case 'site':
					default:
						$link .= '&task=new';
					break;
				}
			break;

			case 'permalink':
			default:

			break;
		}

		if (is_array($params))
		{
			$bits = array();
			foreach ($params as $key => $param)
			{
				$bits[] = $key . '=' . $param;
			}
			$params = implode('&', $bits);
		}

		if ($params)
		{
			if (strtolower($this->get('scope')) == 'group')
			{
				if (substr($params, 0, 1) == '&')
				{
					$params = substr($params, 1);
				}
				if (substr($params, 0, 1) != '?' && substr($params, 0, 1) != '#')
				{
					$params = '?' . $params;
				}
			}
			else
			{
				if (substr($params, 0, 1) == '?')
				{
					$params = substr($params, 1);
				}
				if (substr($params, 0, 1) != '&' && substr($params, 0, 1) != '#')
				{
					$params = '&' . $params;
				}
			}
		}

		return $link . (string) $params;
	}

	/**
	 * Get the most recent post mad ein the forum
	 * 
	 * @return     ForumModelPost
	 */
	public function lastActivity()
	{
		if (!isset($this->_cache['last']) || !is_a($this->_cache['last'], 'ForumModelPost'))
		{
			$post = new ForumPost($this->_db);
			if (!($last = $post->getLastActivity($this->get('scope_id'), $this->get('scope'), $this->get('id'))))
			{
				$last = 0;
			}
			$this->_cache['last'] = new ForumModelPost($last);
		}
		return $this->_cache['last'];
	}
}

