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

if (version_compare(JVERSION, '1.6', 'ge'))
{
	define('BLOG_DATE_YEAR', "Y");
	define('BLOG_DATE_MONTH', "m");
	define('BLOG_DATE_DAY', "d");
	define('BLOG_DATE_TIMEZONE', true);
	define('BLOG_DATE_FORMAT', 'd M Y');
	define('BLOG_TIME_FORMAT', 'H:i p');
}
else
{
	define('BLOG_DATE_YEAR', "%Y");
	define('BLOG_DATE_MONTH', "%m");
	define('BLOG_DATE_DAY', "%d");
	define('BLOG_DATE_TIMEZONE', 0);
	define('BLOG_DATE_FORMAT', '%d %b %Y');
	define('BLOG_TIME_FORMAT', '%I:%M %p');
}

require_once(JPATH_ROOT . DS . 'components' . DS . 'com_blog' . DS . 'tables' . DS . 'entry.php');
require_once(JPATH_ROOT . DS . 'components' . DS . 'com_blog' . DS . 'models' . DS . 'tags.php');
require_once(JPATH_ROOT . DS . 'components' . DS . 'com_blog' . DS . 'models' . DS . 'comment.php');
require_once(JPATH_ROOT . DS . 'components' . DS . 'com_blog' . DS . 'models' . DS . 'iterator.php');

/**
 * Courses model class for a forum
 */
class BlogModelEntry extends JObject
{
	/**
	 * BlogTableEntry
	 * 
	 * @var object
	 */
	private $_tbl = null;

	/**
	 * BlogModelComment
	 * 
	 * @var object
	 */
	private $_comment = null;

	/**
	 * BlogModelIterator
	 * 
	 * @var object
	 */
	private $_comments = null;

	/**
	 * Comment count
	 * 
	 * @var integer
	 */
	private $_comments_count = null;

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
	 * Constructor
	 * 
	 * @param      integer $id Course ID or alias
	 * @return     void
	 */
	public function __construct($oid, $scope=null, $group_id=null)
	{
		$this->_db = JFactory::getDBO();

		$this->_tbl = new BlogTableEntry($this->_db);

		if (is_numeric($oid))
		{
			$this->_tbl->load($oid);
		}
		else if (is_string($oid))
		{
			if ($scope == 'member')
			{
				$this->_tbl->loadAlias($oid, $scope, $group_id, null);
			}
			else
			{
				$this->_tbl->loadAlias($oid, $scope, null, $group_id);
			}
		}
		else if (is_object($oid))
		{
			$this->_tbl->bind($oid);
			$properties = $this->_tbl->getProperties();
			foreach (get_object_vars($oid) as $key => $property)
			{
				if (!array_key_exists($key, $properties)) // && in_array($property, self::$_section_keys))
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
				if (!array_key_exists($key, $properties)) // && in_array($property, self::$_section_keys))
				{
					$this->_tbl->set('__' . $key, $oid[$key]);
				}
			}
		}

		$paramsClass = 'JParameter';
		if (version_compare(JVERSION, '1.6', 'ge'))
		{
			$paramsClass = 'JRegistry';
		}
		$this->params = new $paramsClass($this->_tbl->get('params'));
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
	static function &getInstance($oid=null, $scope=null, $group_id=null)
	{
		static $instances;

		if (!isset($instances)) 
		{
			$instances = array();
		}

		if (!isset($instances[$oid])) 
		{
			$instances[$oid] = new BlogModelEntry($oid, $scope, $group_id);
		}

		return $instances[$oid];
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
	 * Has the offering started?
	 * 
	 * @return     boolean
	 */
	public function isPublished()
	{
		if (!in_array('state', array_keys($this->_tbl->getProperties())))
		{
			return true;
		}
		if ($this->get('state') == 1) 
		{
			return true;
		}
		return false;
	}

	/**
	 * Has the offering started?
	 * 
	 * @return     boolean
	 */
	public function isUnpublished()
	{
		if (!in_array('state', array_keys($this->_tbl->getProperties())))
		{
			return false;
		}
		if ($this->get('state') == 0) 
		{
			return true;
		}
		return false;
	}

	/**
	 * Has the offering started?
	 * 
	 * @return     boolean
	 */
	public function isDeleted()
	{
		if (!in_array('state', array_keys($this->_tbl->getProperties())))
		{
			return false;
		}
		if ($this->get('state') == 2 || $this->get('state') == -1) 
		{
			return true;
		}
		return false;
	}

	/**
	 * Has the offering started?
	 * 
	 * @return     boolean
	 */
	public function started()
	{
		// If it doesn't exist or isn't published
		if (!$this->exists()) // || !$this->isPublished()) 
		{
			return false;
		}

		$now = date('Y-m-d H:i:s', time());

		if ($this->get('publish_up') 
		 && $this->get('publish_up') != '0000-00-00 00:00:00' 
		 && $this->get('publish_up') > $now) 
		{
			return false;
		}

		return true;
	}

	/**
	 * Has the offering ended?
	 * 
	 * @return     boolean
	 */
	public function ended()
	{
		// If it doesn't exist or isn't published
		if (!$this->exists()) // || !$this->isPublished()) 
		{
			return true;
		}

		$now = date('Y-m-d H:i:s', time());

		if ($this->get('publish_down') 
		 && $this->get('publish_down') != '0000-00-00 00:00:00' 
		 && $this->get('publish_down') <= $now) 
		{
			return true;
		}

		return false;
	}

	/**
	 * Check if the offering is available
	 * 
	 * @return     boolean
	 */
	public function isAvailable()
	{
		// If it doesn't exist or isn't published
		if (!$this->exists() || $this->isDeleted())
		{
			return false;
		}

		// Make sure the item is published and within the available time range
		if ($this->started() && !$this->ended()) 
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
	public function comment($id=null)
	{
		if (!isset($this->_comment) 
		 || ($id !== null && (int) $this->_comment->get('id') != $id))
		{
			$this->_comment = null;
			if (isset($this->_comments) && is_a($this->_comments, 'BlogModelIterator'))
			{
				foreach ($this->_comments as $key => $comment)
				{
					if ((int) $comment->get('id') == $id)
					{
						$this->_comment = $comment;
						break;
					}
				}
			}
			if (!$this->_comment)
			{
				$this->_comment = BlogModelComment::getInstance($id);
			}
		}
		return $this->_comment;
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
	public function comments($rtrn='list', $filters=array())
	{
		$tbl = new BlogTableComment($this->_db);

		if (!isset($filters['entry_id']))
		{
			$filters['entry_id'] = $this->get('id');
		}
		if (!isset($filters['state']))
		{
			$filters['state'] = 1;
		}

		switch (strtolower($rtrn))
		{
			case 'count':
				if (!isset($this->_comments_count) || !is_numeric($this->_comments_count))
				{
					$this->_comments_count = 0;
					//$this->_comments_count = (int) $tbl->count($filters);
					if (!$this->_comments) 
					{
						$c = $this->comments('list', $filters);
					}
						foreach ($this->_comments as $com)
						{
							$this->_comments_count++;
							if ($com->replies()) 
							{
								foreach ($com->replies() as $rep)
								{
									$this->_comments_count++;
									if ($rep->replies()) 
									{
										$this->_comments_count += $rep->replies()->total();
									}
								}
							}
						}
					}
				return $this->_comments_count;
			break;

			case 'list':
			case 'results':
			default:
				if (!isset($this->_comments) || !is_a($this->_comments, 'BlogModelIterator'))
				{
					if ($results = $tbl->getAllComments($this->get('id')))
					{
						foreach ($results as $key => $result)
						{
							$results[$key] = new BlogModelComment($result);
						}
					}
					else
					{
						$results = array();
					}
					$this->_comments = new BlogModelIterator($results);
				}
				return $this->_comments;
			break;
		}
	}

	/**
	 * Check if the current user is enrolled
	 * 
	 * @return     boolean
	 */
	public function tags($what='cloud', $admin=0)
	{
		$cloud = new BlogModelTags($this->get('id'));

		return $cloud->render($what, array('admin' => $admin));
	}

	/**
	 * Tag the entry
	 * 
	 * @return     boolean
	 */
	public function tag($tags=null, $user_id=0, $admin=0)
	{
		$cloud = new BlogModelTags($this->get('id'));

		return $cloud->setTags($tags, $user_id, $admin);
	}

	/**
	 * Get the state of the entry as either text or numerical value
	 * 
	 * @param      string $as Format to return state in [text, number]
	 * @return     mixed String or Integer
	 */
	public function state($as='text')
	{
		$as = strtolower($as);

		if ($as == 'text')
		{
			switch ($this->get('state'))
			{
				case 1:
					return 'public';
				break;
				case 2:
					return 'registered';
				break;
				case 0:
				default:
					return 'private';
				break;
			}
		}
		else
		{
			return $this->get('state');
		}
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
		if (!isset($this->_base))
		{
			switch (strtolower($this->get('scope')))
			{
				case 'group':
					$group = Hubzero_Group::getInstance($this->get('group_id'));
					$this->_base = 'index.php?option=com_groups&cn=' . $group->get('cn') . '&active=blog';
				break;

				case 'member':
					$this->_base = 'index.php?option=com_members&id=' . $this->get('created_by') . '&active=blog';
				break;

				case 'site':
				default:
					$this->_base = 'index.php?option=com_blog';
				break;
			}
		}
		$link = $this->_base;

		// If it doesn't exist or isn't published
		switch (strtolower($type))
		{
			case 'edit':
				if (strtolower($this->get('scope')))
				{
					$link .= '&action=edit&entry=' . $this->get('id');
				}
				else 
				{
					$link .= '&task=edit&entry=' . $this->get('id');
				}
			break;

			case 'delete':
				if (strtolower($this->get('scope')))
				{
					$link .= '&action=delete&entry=' . $this->get('id');
				}
				else 
				{
					$link .= '&task=delete&entry=' . $this->get('id');
				}
			break;

			case 'comments':
				if (strtolower($this->get('scope')) == 'group')
				{
					$link .= '&scope=';
				}
				else
				{
					$link .= '&task=';
				}
				$link .= JHTML::_('date', $this->get('publish_up'), BLOG_DATE_YEAR, BLOG_DATE_TIMEZONE) . '/';
				$link .= JHTML::_('date', $this->get('publish_up'), BLOG_DATE_MONTH, BLOG_DATE_TIMEZONE) . '/';
				$link .= $this->get('alias');
				$link .= '#comments';
			break;

			case 'permalink':
			default:
				if (strtolower($this->get('scope')) == 'group')
				{
					$link .= '&scope=';
				}
				else
				{
					$link .= '&task=';
				}
				$link .= JHTML::_('date', $this->get('publish_up'), BLOG_DATE_YEAR, BLOG_DATE_TIMEZONE) . '/';
				$link .= JHTML::_('date', $this->get('publish_up'), BLOG_DATE_MONTH, BLOG_DATE_TIMEZONE) . '/';
				$link .= $this->get('alias');
			break;
		}

		return $link;
	}

	/**
	 * Return a formatted timestamp
	 * 
	 * @param      string $as What data to return
	 * @return     boolean
	 */
	public function published($as='')
	{
		switch (strtolower($as))
		{
			case 'date':
				return JHTML::_('date', $this->get('publish_up'), BLOG_DATE_FORMAT, BLOG_DATE_TIMEZONE);
			break;

			case 'time':
				return JHTML::_('date', $this->get('publish_up'), BLOG_TIME_FORMAT, BLOG_DATE_TIMEZONE);
			break;

			default:
				return $this->get('publish_up');
			break;
		}
	}

	/**
	 * Get the state of the entry as either text or numerical value
	 * 
	 * @param      string  $as      Format to return state in [text, number]
	 * @param      integer $shorten Number of characters to shorten text to
	 * @return     mixed String or Integer
	 */
	public function content($as='parsed', $shorten=0)
	{
		$as = strtolower($as);

		switch ($as)
		{
			case 'parsed':
				if ($this->get('content_parsed'))
				{
					return $this->get('content_parsed');
				}

				$paramsClass = 'JParameter';
				if (version_compare(JVERSION, '1.6', 'ge'))
				{
					$paramsClass = 'JRegistry';
				}

				switch ($this->get('scope'))
				{
					case 'group':
						$option = 'com_groups';
						$plg = JPluginHelper::getPlugin('groups', 'blog');
						$config = new $paramsClass($plg->params);
						$path = str_replace('{{gid}}', $this->get('scope_id'), $config->get('uploadpath', '/site/groups/{{gid}}/blog'));
						$scope = $this->get('scope_id') . '/blog';
					break;

					case 'member':
						ximport('Hubzer_View_Helper_Html');
						$option = 'com_members';
						$plg = JPluginHelper::getPlugin('members', 'blog');
						$config = new $paramsClass($plg->params);
						$path = str_replace('{{uid}}', Hubzero_View_Helper_Html::niceidformat($this->get('created_by')), $config->get('uploadpath', '/site/members/{{uid}}/blog'));
						$scope = $this->get('created_by') . '/blog';
					break;

					case 'site':
					default:
						$option = 'com_blog';
						$config = JComponentHelper::getParams($option);
						$path = $config->get('uploadpath', '/site/blog');
						$scope = $this->get('scope');
					break;
				}

				$p =& Hubzero_Wiki_Parser::getInstance();

				$wikiconfig = array(
					'option'   => $option,
					'scope'    => $scope, //$this->get('scope')
					'pagename' => $this->get('alias'),
					'pageid'   => 0, //$this->get('id'),
					'filepath' => $path,
					'domain'   => ''
				);

				$this->set('content_parsed', $p->parse(stripslashes($this->get('content')), $wikiconfig));

				if ($shorten)
				{
					$content = Hubzero_View_Helper_Html::shortenText($this->get('content_parsed'), $shorten, 0, 0);
					if (substr($content, -7) == '&#8230;') 
					{
						$content .= '</p>';
					}
					return $content;
				}

				return $this->get('content_parsed');
			break;

			case 'clean':
				$content = strip_tags($this->content('parsed'));
				if ($shorten)
				{
					$content = Hubzero_View_Helper_Html::shortenText($content, $shorten, 0, 1);
				}
				return $content;
			break;

			case 'raw':
			default:
				return $this->get('content');
			break;
		}
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
			$this->params->set('access-view-entry', false);

			if ($this->exists() && $this->isAvailable())
			{
				$this->params->set('access-view-entry', true);
			}

			$juser = JFactory::getUser();
			if ($juser->get('guest'))
			{
				$this->_authorized = true;
			}
			else
			{
				// Anyone logged in can create a forum
				//$this->params->set('access-create-entry', true);

				// Check if they're a site admin
				if (version_compare(JVERSION, '1.6', 'lt'))
				{
					if ($juser->authorize('com_blog', 'manage')) 
					{
						$this->params->set('access-admin-entry', true);
						$this->params->set('access-manage-entry', true);
						$this->params->set('access-delete-entry', true);
						$this->params->set('access-edit-entry', true);
						$this->params->set('access-edit-state-entry', true);
						$this->params->set('access-edit-own-entry', true);
					}
				}
				else 
				{
					$this->params->set('access-admin-entry', $juser->authorise('core.admin', $this->get('id')));
					$this->params->set('access-manage-entry', $juser->authorise('core.manage', $this->get('id')));
					$this->params->set('access-delete-entry', $juser->authorise('core.manage', $this->get('id')));
					$this->params->set('access-edit-entry', $juser->authorise('core.manage', $this->get('id')));
					$this->params->set('access-edit-state-entry', $juser->authorise('core.manage', $this->get('id')));
					$this->params->set('access-edit-own-entry', $juser->authorise('core.manage', $this->get('id')));
				}

				// If they're not an admin
				if (!$this->params->get('access-admin-entry') 
				 && !$this->params->get('access-manage-entry'))
				{
					// Does the forum exist?
					/*if (!$this->exists())
					{
						// Give editing access if the blog doesn't exist
						// i.e., it's a new forum
						switch ($this->get('scope'))
						{
							case 'site':
							break;
							
							case 'member':
							break;
						}
						$this->params->set('access-view-entry', true);
						$this->params->set('access-delete-entry', true);
						$this->params->set('access-edit-entry', true);
						$this->params->set('access-edit-state-entry', true);
						$this->params->set('access-edit-own-entry', true);
					}
					// Check if they're the forum creator or forum manager
					else*/
					if ($this->get('created_by') == $juser->get('id')) 
					{
						// Give full access
						$this->params->set('access-view-entry', true);
						$this->params->set('access-manage-entry', true);
						$this->params->set('access-delete-entry', true);
						$this->params->set('access-edit-entry', true);
						$this->params->set('access-edit-state-entry', true);
						$this->params->set('access-edit-own-entry', true);
					}
				}

				$this->_authorized = true;
			}
		}
		return $this->params->get('access-' . strtolower($action) . '-entry');
	}
}

