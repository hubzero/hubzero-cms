<?php
/**
 * HUBzero CMS
 *
 * Copyright 2005-2015 Purdue University. All rights reserved.
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
 * @copyright Copyright 2005-2015 Purdue University. All rights reserved.
 * @license   http://www.gnu.org/licenses/lgpl-3.0.html LGPLv3
 */

namespace Components\Forum\Models;

use Components\Forum\Tables;
use Hubzero\Base\ItemList;
use LogicException;
use Lang;

require_once(dirname(__DIR__) . DS . 'tables' . DS . 'category.php');
require_once(__DIR__ . DS . 'base.php');
require_once(__DIR__ . DS . 'thread.php');

/**
 * Forum model class for a forum category
 */
class Category extends Base
{
	/**
	 * Table class name
	 *
	 * @var  object
	 */
	protected $_tbl_name = '\\Components\\Forum\\Tables\\Category';

	/**
	 * Container for properties
	 *
	 * @var  array
	 */
	private $_cache = array(
		'thread'        => null,
		'threads_count' => null,
		'threads'       => null,
		'last'          => null
	);

	/**
	 * Constructor
	 *
	 * @param   mixed    $oid         ID (integer), alias (string), array or object
	 * @param   integer  $section_id  Section ID
	 * @return  void
	 */
	public function __construct($oid, $section_id=0)
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
			if (is_numeric($oid) || is_string($oid))
			{
				if ($section_id)
				{
					$this->_tbl->loadByAlias($oid, $section_id);
				}
				else
				{
					$this->_tbl->load($oid);
				}
			}
			else if (is_object($oid) || is_array($oid))
			{
				$this->bind($oid);
			}
		}
	}

	/**
	 * Returns a reference to a forum category model
	 *
	 * @param   mixed  $oid  ID (int) or alias (string)
	 * @return  object
	 */
	static function &getInstance($oid=null, $section_id=0)
	{
		static $instances;

		if (!isset($instances))
		{
			$instances = array();
		}

		if (is_numeric($oid) || is_string($oid))
		{
			$key = $section_id . '_' . $oid;
		}
		else if (is_object($oid))
		{
			$key = $section_id . '_' . $oid->id;
		}
		else if (is_array($oid))
		{
			$key = $section_id . '_' . $oid['id'];
		}

		if (!isset($instances[$key]))
		{
			$instances[$key] = new self($oid, $section_id);
		}

		return $instances[$key];
	}

	/**
	 * Is the category closed?
	 *
	 * @return  boolean
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
	 * @param   mixed  $id  ID (integer) or alias (string)
	 * @return  object
	 */
	public function thread($id=null)
	{
		if (!isset($this->_cache['thread'])
		 || ($id !== null && (int) $this->_cache['thread']->get('id') != $id))
		{
			$this->_cache['thread'] = null;

			if (isset($this->_cache['threads']) && ($this->_cache['threads'] instanceof ItemList))
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
				$this->_cache['thread'] = Thread::getInstance($id);
			}
			if (!$this->_cache['thread']->exists())
			{
				$this->_cache['thread']->set('scope', $this->get('scope'));
				$this->_cache['thread']->set('scope_id', $this->get('scope_id'));
			}
		}
		return $this->_cache['thread'];
	}

	/**
	 * Get a list of threads for a forum category
	 *
	 * @param   string   $rtrn     What data to return?
	 * @param   array    $filters  Filters to apply to data fetch
	 * @param   boolean  $clear    Clear cached data?
	 * @return  mixed
	 */
	public function threads($rtrn='list', $filters=array(), $clear=false)
	{
		$filters['category_id'] = isset($filters['category_id']) ? $filters['category_id'] : $this->get('id');
		$filters['state']       = isset($filters['state'])       ? $filters['state']       : array(self::APP_STATE_PUBLISHED, self::APP_STATE_FLAGGED);
		$filters['parent']      = isset($filters['parent'])      ? $filters['parent']      : 0;

		switch (strtolower($rtrn))
		{
			case 'count':
				if (!isset($this->_cache['threads_count']) || $clear)
				{
					$tbl = new Tables\Post($this->_db);
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
				if (!($this->_cache['threads'] instanceof ItemList) || $clear)
				{
					$tbl = new Tables\Post($this->_db);

					if (($results = $tbl->getRecords($filters)))
					{
						foreach ($results as $key => $result)
						{
							$results[$key] = new Thread($result);
							$results[$key]->set('category', $this->get('alias'));
							$results[$key]->set('section', $this->adapter()->get('section'));
						}
					}
					else
					{
						$results = array();
					}

					$this->_cache['threads'] = new ItemList($results);
				}

				return $this->_cache['threads'];
			break;
		}
	}

	/**
	 * Return a count for the type of data specified
	 *
	 * @param   string   $what  What to count
	 * @return  integer
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
					$this->setError(Lang::txt('Property value of "%" not accepted', $what));
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
	 * @param   string  $type    The type of link to return
	 * @param   mixed   $params  Optional string or associative array of params to append
	 * @return  string
	 */
	public function link($type='', $params=null)
	{
		return $this->adapter()->build($type, $params);
	}

	/**
	 * Get the most recent post
	 *
	 * @return  object
	 */
	public function lastActivity()
	{
		if (!($this->_cache['last'] instanceof Post))
		{
			$post = new Tables\Post($this->_db);
			if (!($last = $post->getLastActivity($this->get('scope_id'), $this->get('scope'), $this->get('id'))))
			{
				$last = 0;
			}
			$this->_cache['last'] = new Post($last);
		}
		return $this->_cache['last'];
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
			if (!$this->get('section_alias'))
			{
				$this->set('section_alias', Section::getInstance($this->get('section_id'))->get('alias'));
			}
			$this->_adapter->set('section', $this->get('section_alias'));
			$this->_adapter->set('category', $this->get('alias'));
		}

		return $this->_adapter;
	}

	/**
	* Verifies no duplicate aliases within a secton's categories listing.
	* Returns true if duplicate detected.
	*
	* @param integer $id the id of the category object
	*
	* @return boolean
	*/
	public function uniqueAliasCheck($id = null)
	{
		$alias = $this->get('alias');
		$section = new Section($this->get('section_id'));

		// all categories within a section
		$categories = $section->categories('list');

		// check for duplicate aliases within the same section;
		foreach ($categories as $category)
		{
			$existing = $category->get('alias');
			if ($alias == $existing
				&& ($category->get('id') != $id))
			{
				$this->setError(Lang::txt('The alias must be unique within a section.'));
				return true;
			}
			else
			{
				continue;
			}
		}
		return false;
	}
}
