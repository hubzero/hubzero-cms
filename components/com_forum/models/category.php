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

require_once(JPATH_ROOT . DS . 'components' . DS . 'com_forum' . DS . 'tables' . DS . 'category.php');
require_once(JPATH_ROOT . DS . 'components' . DS . 'com_forum' . DS . 'models' . DS . 'iterator.php');
require_once(JPATH_ROOT . DS . 'components' . DS . 'com_forum' . DS . 'models' . DS . 'thread.php');

/**
 * Courses model class for a forum
 */
class ForumModelCategory extends JObject
{
	/**
	 * ForumTableSection
	 * 
	 * @var object
	 */
	private $_thread = null;

	/**
	 * ForumTableSection
	 * 
	 * @var object
	 */
	private $_threads = null;

	/**
	 * ForumModelCategory
	 * 
	 * @var object
	 */
	private $_tbl = null;

	/**
	 * Flag for if authorization checks have been run
	 * 
	 * @var mixed
	 */
	private $_authorized = false;

	/**
	 * JUser
	 * 
	 * @var object
	 */
	private $_creator = NULL;

	/**
	 * JDatabase
	 * 
	 * @var object
	 */
	private $_db = NULL;

	/**
	 * Container for properties
	 * 
	 * @var array
	 */
	//private $_data = array();

	/**
	 * Container for properties
	 * 
	 * @var array
	 */
	private $_config = null;

	private $_stats = array();

	/**
	 * Constructor
	 * 
	 * @param      integer $id Course ID or alias
	 * @return     void
	 */
	public function __construct($oid, $section_id=0)
	{
		$this->_db = JFactory::getDBO();

		$this->_tbl = new ForumCategory($this->_db);

		if (is_numeric($oid))
		{
			$this->_tbl->load($oid);
		}
		else if (is_string($oid))
		{
			$this->_tbl->loadByAlias($oid, $section_id);
		}
		else if (is_object($oid))
		{
			$this->_tbl->bind($oid);
			$properties = $this->_tbl->getProperties();
			foreach (get_object_vars($oid) as $key => $property)
			{
				if (!array_key_exists($key, $properties))
				{
					$this->_tbl->set('__' . $key, $property);
				}
			}
		}
		else if (is_array($oid))
		{
			$this->_tbl->bind($oid);
			$properties = $this->_tbl->getProperties();
			foreach (array_keys($oid) as $key)
			{
				if (!array_key_exists($key, $properties))
				{
					$this->_tbl->set('__' . $key, $oid[$key]);
				}
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
				$this->_base = 'index.php?option=com_courses';
			break;
		}

		$this->_config = JComponentHelper::getParams('com_forum');
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
	 * Returns a property of the object or the default value if the property is not set.
	 *
	 * @param	string $property The name of the property
	 * @param	mixed  $default The default value
	 * @return	mixed The value of the property
 	 */
	public function get($property, $default=null)
	{
		if (isset($this->_tbl->$property)) 
		{
			return $this->_tbl->$property;
		}
		else if (isset($this->_tbl->{'__' . $property})) 
		{
			return $this->_tbl->{'__' . $property};
		}
		return $default;
	}

	/**
	 * Modifies a property of the object, creating it if it does not already exist.
	 *
	 * @param	string $property The name of the property
	 * @param	mixed  $value The value of the property to set
	 * @return	mixed Previous value of the property
	 */
	public function set($property, $value = null)
	{
		if (!array_key_exists($property, $this->_tbl->getProperties()))
		{
			$property = '__' . $property;
		}
		$previous = isset($this->_tbl->$property) ? $this->_tbl->$property : null;
		$this->_tbl->$property = $value;
		return $previous;
	}

	/**
	 * Check if the forum exists
	 * 
	 * @param      mixed $idx Index value
	 * @return     array
	 */
	public function exists()
	{
		if ($this->get('id') && (int) $this->get('id') > 0) 
		{
			return true;
		}
		return false;
	}

	/**
	 * Get the creator of this entry
	 * 
	 * Accepts an optional property name. If provided
	 * it will return that property value. Otherwise,
	 * it returns the entire JUser object
	 *
	 * @return     mixed
	 */
	public function creator($property=null)
	{
		if (!isset($this->_creator) || !is_object($this->_creator))
		{
			$this->_creator = JUser::getInstance($this->get('created_by'));
		}
		if ($property && is_a($this->_creator, 'JUser'))
		{
			return $this->_creator->get($property);
		}
		return $this->_creator;
	}

	/**
	 * Set and get a specific offering
	 * 
	 * @return     void
	 */
	public function thread($id=null)
	{
		if (!isset($this->_cache['thread']) 
		 || ($id !== null && (int) $this->_cache['thread']->get('id') != $id))
		{
			$this->_cache['thread'] = null;
			if (isset($this->_cache['threads']) && is_a($this->_cache['threads'], 'ForumModelIterator'))
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
	public function threads($rtrn='list', $filters=array())
	{
		$filters['category_id'] = isset($filters['category_id']) ? $filters['category_id'] : $this->get('id');
		$filters['state']       = isset($filters['state'])       ? $filters['state']       : 1;
		$filters['parent']      = isset($filters['parent'])      ? $filters['parent']      : 0;

		switch (strtolower($rtrn))
		{
			case 'count':
				$tbl = new ForumPost($this->_db);
				return $tbl->getCount($filters);
			break;

			case 'first':
				return $this->threads('list', $filters)->fetch('first');
			break;

			case 'list':
			case 'results':
			default:
				if (!isset($this->_cache['threads']) || !is_a($this->_cache['threads'], 'ForumModelIterator'))
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

					$this->_cache['threads'] = new ForumModelIterator($results);
				}

				return $this->_cache['threads'];
			break;
		}
	}

	/**
	 * Check a user's authorization
	 * 
	 * @param      string $action Action to check
	 * @return     boolean True if authorized, false if not
	 */
	public function access($action='view')
	{
		if (!$this->_authorized)
		{
			// Set NOT viewable by default
			// We need to ensure the forum is published first
			$this->params->set('access-view-forum', false);

			if ($this->exists() && $this->get('state') == 1)
			{
				$this->params->set('access-view-forum', true);
			}

			$juser = JFactory::getUser();
			if ($juser->get('guest'))
			{
				$this->_authorized = true;
			}
			else
			{
				// Anyone logged in can create a forum
				$this->params->set('access-create-forum', true);

				// Check if they're a site admin
				if (version_compare(JVERSION, '1.6', 'lt'))
				{
					if ($juser->authorize('com_forums', 'manage')) 
					{
						$this->params->set('access-admin-forum', true);
						$this->params->set('access-manage-forum', true);
						$this->params->set('access-delete-forum', true);
						$this->params->set('access-edit-forum', true);
						$this->params->set('access-edit-state-forum', true);
						$this->params->set('access-edit-own-forum', true);
					}
				}
				else 
				{
					$this->params->set('access-admin-forum', $juser->authorise('core.admin', $this->get('id')));
					$this->params->set('access-manage-forum', $juser->authorise('core.manage', $this->get('id')));
					$this->params->set('access-delete-forum', $juser->authorise('core.manage', $this->get('id')));
					$this->params->set('access-edit-forum', $juser->authorise('core.manage', $this->get('id')));
					$this->params->set('access-edit-state-forum', $juser->authorise('core.manage', $this->get('id')));
					$this->params->set('access-edit-own-forum', $juser->authorise('core.manage', $this->get('id')));
				}

				// If they're not an admin
				if (!$this->params->get('access-admin-forum') 
				 && !$this->params->get('access-manage-forum'))
				{
					// Does the forum exist?
					if (!$this->exists())
					{
						// Give editing access if the forum doesn't exist
						// i.e., it's a new forum
						$this->params->set('access-view-forum', true);
						$this->params->set('access-delete-forum', true);
						$this->params->set('access-edit-forum', true);
						$this->params->set('access-edit-state-forum', true);
						$this->params->set('access-edit-own-forum', true);
					}
					// Check if they're the forum creator or forum manager
					else if ($this->get('created_by') == $juser->get('id') 
						  || in_array($juser->get('id'), $this->get('managers'))) 
					{
						// Give full access
						$this->params->set('access-manage-forum', true);
						$this->params->set('access-delete-forum', true);
						$this->params->set('access-edit-forum', true);
						$this->params->set('access-edit-state-forum', true);
						$this->params->set('access-edit-own-forum', true);
					}
				}

				$this->_authorized = true;
			}
		}
		return $this->params->get('access-' . strtolower($action) . '-forum');
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
				case 'threads':
					if ($this->get('threads', null) !== null)
					{
						$this->_stats[$what] += $this->get('threads');
					}
					else
					{
						$this->_stats[$what] += $this->threads('count');
					}
				break;

				case 'posts':
					if ($this->get('posts', null) !== null)
					{
						$this->_stats[$what] += $this->get('posts');
					}
					else
					{
						foreach ($this->threads() as $thread)
						{
							$this->_stats[$what] += $thread->posts('count');
						}
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
	 * Generate and return various links to the entry
	 * Link will vary depending upon action desired, such as edit, delete, etc.
	 * 
	 * @param      string $type The type of link to return
	 * @return     boolean
	 */
	public function link($type='')
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

			case 'permalink':
			default:

			break;
		}

		return $link;
	}

	/**
	 * Check if the course exists
	 * 
	 * @param      mixed $idx Index value
	 * @return     array
	 */
	public function bind($data=null)
	{
		return $this->_tbl->bind($data);
	}

	/**
	 * Store changes to this offering
	 *
	 * @param     boolean $check Perform data validation check?
	 * @return    boolean False if error, True on success
	 */
	public function store($check=true)
	{
		// Ensure we have a database to work with
		if (empty($this->_db))
		{
			return false;
		}

		// Validate data?
		if ($check)
		{
			// Is data valid?
			if (!$this->_tbl->check())
			{
				$this->setError($this->_tbl->getError());
				return false;
			}
		}

		// Attempt to store data
		if (!$this->_tbl->store())
		{
			$this->setError($this->_tbl->getError());
			return false;
		}

		return true;
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
			require_once(JPATH_ROOT . DS . 'components' . DS . 'com_forum' . DS . 'models' . DS . 'post.php');

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

