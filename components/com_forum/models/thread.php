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

require_once(JPATH_ROOT . DS . 'components' . DS . 'com_forum' . DS . 'models' . DS . 'post.php');
require_once(JPATH_ROOT . DS . 'components' . DS . 'com_forum' . DS . 'models' . DS . 'iterator.php');
require_once(JPATH_ROOT . DS . 'components' . DS . 'com_forum' . DS . 'models' . DS . 'attachment.php');
require_once(JPATH_ROOT . DS . 'components' . DS . 'com_forum' . DS . 'models' . DS . 'tags.php');

/**
 * Courses model class for a forum
 */
class ForumModelThread extends ForumModelPost
{
	/**
	 * Container for data
	 * 
	 * @var array
	 */
	private $_cache = array();

	/**
	 * Is the thread closed?
	 * 
	 * @return     boolean
	 */
	public function isClosed()
	{
		if ($this->get('closed') == 1) 
		{
			return true;
		}
		return false;
	}

	/**
	 * Is the thread sticky?
	 * 
	 * @return     boolean
	 */
	public function isSticky()
	{
		if ($this->get('sticky') == 1) 
		{
			return true;
		}
		return false;
	}

	/**
	 * Set and get a specific offering
	 * 
	 * @return     void
	 */
	public function post($id=null)
	{
		if (!isset($this->_cache['post']) 
		 || ($id !== null && $this->_cache['post']->get('id') != $id && $this->_cache['post']->get('alias') != $id))
		{
			$this->_cache['post'] = null;
			if (isset($this->_cache['posts']))
			{
				foreach ($this->_cache['posts'] as $key => $post)
				{
					if ($post->get('id') == $id || $post->get('alias') == $id)
					{
						$this->_cache['post'] = $post;
						break;
					}
				}
			}
			if (!$this->_cache['post'])
			{
				$this->_cache['post'] = ForumModelPost::getInstance($id);
			}
		}
		return $this->_cache['post'];
	}

	/**
	 * Get a list of posts in this thread
	 * 
	 * @param      array $filters Filters to build query from
	 * @return     object ForumModelIterator
	 */
	public function posts($rtrn='list', $filters=array(), $clear=false)
	{
		$filters['thread']      = isset($filters['thread'])      ? $filters['thread']      : $this->get('thread');
		//$filters['category_id'] = isset($filters['category_id']) ? $filters['category_id'] : $this->get('category_id');
		$filters['state']       = isset($filters['state'])       ? $filters['state']       : 1;

		switch (strtolower($rtrn))
		{
			case 'count':
				return $this->_tbl->getCount($filters);
			break;

			case 'first':
				return $this->posts('list', $filters)->fetch('first');
			break;

			case 'tree':
				if (!isset($this->_cache['tree']) || !is_a($this->_cache['tree'], 'ForumModelIterator') || $clear)
				{
					if ($rows = $this->_tbl->getTree($filters['thread'])) //getTree
					{
						$children = array(
							0 => array()
						);

						$levellimit = ($filters['limit'] == 0) ? 500 : $filters['limit'];

						foreach ($rows as $row)
						{
							$v = new ForumModelPost($row);

							$pt      = $v->get('parent');
							$list    = @$children[$pt] ? $children[$pt] : array();
							array_push($list, $v);
							$children[$pt] = $list;
						}

						$results = $this->_treeRecurse($children[$this->get('parent')], $children);
					}

					$this->_cache['tree'] = new ForumModelIterator($results);
				}
				return $this->_cache['tree'];
			break;

			case 'list':
			case 'results':
			default:
				if (!isset($this->_cache['posts']) || !is_a($this->_cache['posts'], 'ForumModelIterator') || $clear)
				{
					if (($results = $this->_tbl->getRecords($filters)))
					{
						foreach ($results as $key => $result)
						{
							$results[$key] = new ForumModelPost($result);
						}
					}
					else
					{
						$results = array();
					}
					$this->_cache['posts'] = new ForumModelIterator($results);
				}
				return $this->_cache['posts'];
			break;
		}
	}

	/**
	 * Recursive function to build tree
	 * 
	 * @param      integer $id       Parent ID
	 * @param      string  $indent   Indent text
	 * @param      array   $list     List of records
	 * @param      array   $children Container for parent/children mapping
	 * @param      integer $maxlevel Maximum levels to descend
	 * @param      integer $level    Indention level
	 * @param      integer $type     Indention type
	 * @return     void
	 */
	public function _treeRecurse($children, $list, $maxlevel=9999, $level=0)
	{
		if ($level <= $maxlevel)
		{
			foreach ($children as $v => $child)
			{
				if (isset($list[$child->get('id')]))
				{
					$children[$v]->set('replies', new ForumModelIterator($this->_treeRecurse($list[$child->get('id')], $list, $maxlevel, $level+1)));
				}
				else
				{
					$children[$v]->set('replies', new ForumModelIterator(array()));
				}
			}
		}
		return $children;
	}

	/**
	 * Get a list of participants in this thread
	 * 
	 * @param      array $filters Filters to build query from
	 * @return     object ForumModelIterator
	 */
	public function participants($filters=array(), $clear=false)
	{
		$filters['thread'] = isset($filters['thread'])      ? $filters['thread']      : $this->get('thread');
		$filters['parent'] = isset($filters['parent'])      ? $filters['parent']      : $this->get('id');
		//$filters['category_id'] = isset($filters['category_id']) ? $filters['category_id'] : $this->get('category_id');
		$filters['state']  = isset($filters['state'])       ? $filters['state']       : 1;

		if (!isset($this->_participants) || !is_a($this->_participants, 'ForumModelIterator') || $clear)
		{
			if (!($results = $this->_tbl->getParticipants($filters)))
			{
				$results = array();
			}
			$this->_participants = new ForumModelIterator($results);
		}

		return $this->_participants;
	}

	/**
	 * Get a list of attachments in this thread
	 * 
	 * @param      array $filters Filters to build query from
	 * @return     object ForumModelIterator
	 */
	public function attachments($rtrn='list', $clear=false)
	{
		switch (strtolower($rtrn))
		{
			case 'count':
				return $this->attachments('list')->total();
			break;

			case 'first':
				return $this->attachments('list')->fetch('first');
			break;

			case 'list':
			case 'results':
			default:
				if (!isset($this->_cache['attachments']) || !is_a($this->_cache['attachments'], 'ForumModelIterator') || $clear)
				{
					$tbl = new ForumAttachment($this->_db);

					if ($results = $tbl->getAttachments($this->get('id')))
					{
						foreach ($results as $key => $result)
						{
							$results[$key] = new ForumModelAttachment($result);
						}
					}
					else
					{
						$results = array();
					}
					$this->_cache['attachments'] = new ForumModelIterator($results);
				}
				return $this->_cache['attachments'];
			break;
		}
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
			if (!($last = $post->getLastPost($this->get('id'))))
			{
				$last = 0;
			}
			$this->_cache['last'] = new ForumModelPost($last);
		}
		return $this->_cache['last'];
	}

	/**
	 * Get tags on the entry
	 * Optinal first agument to determine format of tags
	 * 
	 * @param      string  $as    Format to return state in [comma-deliminated string, HTML tag cloud, array]
	 * @param      integer $admin Include amdin tags? (defaults to no)
	 * @return     boolean
	 */
	public function tags($as='cloud', $admin=0)
	{
		$cloud = new ForumModelTags($this->get('id'));

		return $cloud->render($as, array('admin' => $admin));
	}
}

