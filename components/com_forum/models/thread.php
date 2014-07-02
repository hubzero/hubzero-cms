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

require_once(JPATH_ROOT . DS . 'components' . DS . 'com_forum' . DS . 'models' . DS . 'post.php');
require_once(JPATH_ROOT . DS . 'components' . DS . 'com_forum' . DS . 'models' . DS . 'attachment.php');
require_once(JPATH_ROOT . DS . 'components' . DS . 'com_forum' . DS . 'models' . DS . 'tags.php');

/**
 * Model class for a forum thread
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
	 * Returns a reference to a forum thread model
	 *
	 * @param      mixed $oid ID (int) or array or object
	 * @return     object ForumModelThread
	 */
	static function &getInstance($oid=0)
	{
		static $instances;

		if (!isset($instances))
		{
			$instances = array();
		}

		if (is_numeric($oid) || is_string($oid))
		{
			$key = $oid;
		}
		else if (is_object($oid))
		{
			$key = $oid->id;
		}
		else if (is_array($oid))
		{
			$key = $oid['id'];
		}

		if (!isset($instances[$oid]))
		{
			$instances[$oid] = new ForumModelThread($oid);
		}

		return $instances[$oid];
	}

	/**
	 * Set and get a specific post
	 *
	 * @param      integer $id Post ID
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
				foreach ($this->_cache['posts'] as $post)
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
			if (!$this->_cache['post']->exists())
			{
				$this->_cache['post']->set('scope', $this->get('scope'));
				$this->_cache['post']->set('scope_id', $this->get('scope_id'));
			}
		}
		return $this->_cache['post'];
	}

	/**
	 * Get a list of posts in this thread
	 *
	 * @param      string  $rtrn    What data to return?
	 * @param      array   $filters Filters to apply to data fetch
	 * @param      boolean $clear   Clear cached data?
	 * @return     mixed
	 */
	public function posts($rtrn='list', $filters=array(), $clear=false)
	{
		$filters['thread'] = isset($filters['thread']) ? $filters['thread'] : $this->get('thread');
		$filters['state']  = isset($filters['state'])  ? $filters['state']  : array(self::APP_STATE_PUBLISHED, self::APP_STATE_FLAGGED);

		switch (strtolower($rtrn))
		{
			case 'count':
				if (!isset($this->_cache['posts_count']) || $clear)
				{
					$this->_cache['posts_count'] = $this->_tbl->getCount($filters);
				}
				return $this->_cache['posts_count'];
			break;

			case 'first':
				return $this->posts('list', $filters)->first();
			break;

			case 'tree':
				if (!isset($this->_cache['tree']) || !($this->_cache['tree'] instanceof \Hubzero\Base\ItemList) || $clear)
				{
					if ($rows = $this->_tbl->getTree($filters['thread']))
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

					$this->_cache['tree'] = new \Hubzero\Base\ItemList($results);
				}
				return $this->_cache['tree'];
			break;

			case 'list':
			case 'results':
			default:
				if (!isset($this->_cache['posts']) || !($this->_cache['posts'] instanceof \Hubzero\Base\ItemList) || $clear)
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
					$this->_cache['posts'] = new \Hubzero\Base\ItemList($results);
				}
				return $this->_cache['posts'];
			break;
		}
	}

	/**
	 * Recursive function to build tree
	 *
	 * @param      array   $children Container for parent/children mapping
	 * @param      array   $list     List of records
	 * @param      integer $maxlevel Maximum levels to descend
	 * @param      integer $level    Indention level
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
					$children[$v]->set('replies', new \Hubzero\Base\ItemList($this->_treeRecurse($list[$child->get('id')], $list, $maxlevel, $level+1)));
				}
				else
				{
					$children[$v]->set('replies', new \Hubzero\Base\ItemList(array()));
				}
			}
		}
		return $children;
	}

	/**
	 * Get a list of participants in this thread
	 *
	 * @param      array   $filters Filters to build query from
	 * @param      boolean $clear   Clear cached data?
	 * @return     object \Hubzero\Base\ItemList
	 */
	public function participants($filters=array(), $clear=false)
	{
		$filters['thread'] = isset($filters['thread']) ? $filters['thread'] : $this->get('thread');
		//$filters['parent'] = isset($filters['parent']) ? $filters['parent'] : $this->get('id');
		$filters['state']  = isset($filters['state'])  ? $filters['state']  : self::APP_STATE_PUBLISHED;

		if (!isset($this->_cache['participants']) || !($this->_cache['participants'] instanceof \Hubzero\Base\ItemList) || $clear)
		{
			if (!($results = $this->_tbl->getParticipants($filters)))
			{
				$results = array();
			}
			$this->_cache['participants'] = new \Hubzero\Base\ItemList($results);
		}

		return $this->_cache['participants'];
	}

	/**
	 * Get a list of attachments in this thread
	 *
	 * @param      array $filters Filters to build query from
	 * @return     object \Hubzero\Base\ItemList
	 */
	public function attachments($rtrn='list', $clear=false)
	{
		switch (strtolower($rtrn))
		{
			case 'count':
				return $this->attachments('list')->total();
			break;

			case 'first':
				return $this->attachments('list')->first();
			break;

			case 'list':
			case 'results':
			default:
				if (!isset($this->_cache['attachments']) || !($this->_cache['attachments'] instanceof \Hubzero\Base\ItemList) || $clear)
				{
					$tbl = new ForumTableAttachment($this->_db);

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
					$this->_cache['attachments'] = new \Hubzero\Base\ItemList($results);
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
		if (!isset($this->_cache['last']) || !($this->_cache['last'] instanceof ForumModelPost))
		{
			$post = new ForumTablePost($this->_db);
			if (!($last = $post->getLastPost($this->get('id'))))
			{
				$last = 0;
			}
			$this->_cache['last'] = new ForumModelPost($last);
		}
		return $this->_cache['last'];
	}
}

