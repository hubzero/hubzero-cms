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

require_once(JPATH_ROOT . DS . 'components' . DS . 'com_blog' . DS . 'tables' . DS . 'entry.php');
require_once(JPATH_ROOT . DS . 'components' . DS . 'com_blog' . DS . 'models' . DS . 'tags.php');
require_once(JPATH_ROOT . DS . 'components' . DS . 'com_blog' . DS . 'models' . DS . 'comment.php');

/**
 * Courses model class for a forum
 */
class BlogModelEntry extends \Hubzero\Base\Model
{
	/**
	 * Table name
	 *
	 * @var string
	 */
	protected $_tbl_name = 'BlogTableEntry';

	/**
	 * Model context
	 *
	 * @var string
	 */
	protected $_context = 'com_blog.entry.content';

	/**
	 * BlogModelComment
	 *
	 * @var object
	 */
	private $_comment = null;

	/**
	 * \Hubzero\Base\ItemList
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
	 * JUser
	 *
	 * @var object
	 */
	private $_creator = NULL;

	/**
	 * JRegistry
	 *
	 * @var object
	 */
	public $params = NULL;

	/**
	 * Scope adapter
	 *
	 * @var object
	 */
	private $_adapter = null;

	/**
	 * Constructor
	 *
	 * @param      mixed   $oid      ID (int) or alias (string)
	 * @param      string  $scope    site|member|group
	 * @param      integer $group_id Group ID if scope is 'group'
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
		else if (is_object($oid) || is_array($oid))
		{
			$this->bind($oid);
		}

		$this->params = new JRegistry($this->_tbl->get('params'));
	}

	/**
	 * Returns a reference to a blog entry model
	 *
	 * This method must be invoked as:
	 *     $offering = BlogModelentry::getInstance($alias, $scope, $group_id);
	 *
	 * @param      mixed   $oid      ID (int) or alias (string)
	 * @param      string  $scope    site|member|group
	 * @param      integer $group_id Group ID if scope is 'group'
	 * @return     object BlogModelentry
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
			$instances[$oid] = new self($oid, $scope, $group_id);
		}

		return $instances[$oid];
	}

	/**
	 * Has the publish window started?
	 *
	 * @return     boolean
	 */
	public function started()
	{
		// If it doesn't exist or isn't published
		if (!$this->exists())
		{
			return false;
		}

		$now = JFactory::getDate();

		if ($this->get('publish_up')
		 && $this->get('publish_up') != $this->_db->getNullDate()
		 && $this->get('publish_up') > $now)
		{
			return false;
		}

		return true;
	}

	/**
	 * Has the publish window ended?
	 *
	 * @return     boolean
	 */
	public function ended()
	{
		// If it doesn't exist or isn't published
		if (!$this->exists())
		{
			return true;
		}

		$now = JFactory::getDate();

		if ($this->get('publish_down')
		 && $this->get('publish_down') != $this->_db->getNullDate()
		 && $this->get('publish_down') <= $now)
		{
			return true;
		}

		return false;
	}

	/**
	 * Check if the entry is available
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
		if (!($this->_creator instanceof \Hubzero\User\Profile))
		{
			$this->_creator = \Hubzero\User\Profile::getInstance($this->get('created_by'));
			if (!$this->_creator)
			{
				$this->_creator = new \Hubzero\User\Profile();
			}
		}
		if ($property)
		{
			$property = ($property == 'id') ? 'uidNumber' : $property;
			if ($property == 'picture')
			{
				return $this->_creator->getPicture();
			}
			return $this->_creator->get($property);
		}
		return $this->_creator;
	}

	/**
	 * Set and get a specific comment
	 *
	 * @param      integer $id ID of specific comment to fetch
	 * @return     object BlogModelComment
	 */
	public function comment($id=null)
	{
		if (!isset($this->_comment)
		 || ($id !== null && (int) $this->_comment->get('id') != $id))
		{
			$this->_comment = null;
			if ($this->_comments instanceof \Hubzero\Base\ItemList)
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
	 * Get a list or count of comments
	 *
	 * @param      string  $rtrn    Data format to return
	 * @param      array   $filters Filters to apply to data fetch
	 * @param      boolean $clear   Clear cached data?
	 * @return     mixed
	 */
	public function comments($rtrn='list', $filters=array(), $clear = false)
	{
		$tbl = new BlogTableComment($this->_db);

		if (!isset($filters['entry_id']))
		{
			$filters['entry_id'] = $this->get('id');
		}
		if (!isset($filters['state']))
		{
			$filters['state'] = array(self::APP_STATE_PUBLISHED, self::APP_STATE_FLAGGED);
		}

		switch (strtolower($rtrn))
		{
			case 'count':
				if (!isset($this->_comments_count) || !is_numeric($this->_comments_count) || $clear)
				{
					$this->_comments_count = 0;

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
				if (!($this->_comments instanceof \Hubzero\Base\ItemList) || $clear)
				{
					if ($results = $tbl->getAllComments($this->get('id'), $filters))
					{
						foreach ($results as $key => $result)
						{
							$results[$key] = new BlogModelComment($result);
							$results[$key]->set('option', $this->_adapter()->get('option'));
							$results[$key]->set('scope', $this->_adapter()->get('scope'));
							$results[$key]->set('alias', $this->_adapter()->get('alias'));
							$results[$key]->set('path', $this->_adapter()->get('path'));
						}
					}
					else
					{
						$results = array();
					}
					$this->_comments = new \Hubzero\Base\ItemList($results);
				}
				return $this->_comments;
			break;
		}
	}

	/**
	 * Get tags on an entry
	 *
	 * @param      string  $what  Data format to return (string, array, cloud)
	 * @param      integer $admin Get admin tags? 0=no, 1=yes
	 * @return     mixed
	 */
	public function tags($what='cloud', $admin=0)
	{
		if (!$this->exists())
		{
			switch (strtolower($what))
			{
				case 'array':
					return array();
				break;

				case 'string':
				case 'cloud':
				case 'html':
				default:
					return '';
				break;
			}
		}

		$cloud = new BlogModelTags($this->get('id'));

		return $cloud->render($what, array('admin' => $admin));
	}

	/**
	 * Tag the entry
	 *
	 * @param      string  $tags    Tags to apply
	 * @param      integer $user_id ID of tagger
	 * @param      integer $admin   Tag as admin? 0=no, 1=yes
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
	 * @param      string $type   The type of link to return
	 * @param      mixed  $params String or array of extra params to append
	 * @return     string
	 */
	public function link($type='', $params=null)
	{
		return $this->_adapter()->build($type, $params);
	}

	/**
	 * Gets a property of the adapter's object (e.g., a group, a member)
	 *
	 * @param      string $property Property to get
	 * @return     string
	 */
	public function item($property=null)
	{
		return $this->_adapter()->item($property);
	}

	/**
	 * Return the adapter for this entry's scope,
	 * instantiating it if it doesn't already exist
	 *
	 * @return    object
	 */
	private function _adapter()
	{
		if (!$this->_adapter)
		{
			$scope = strtolower($this->get('scope'));
			if ($scope == 'group')
			{
				$this->set('scope_id', $this->get('group_id'));
			}
			else if ($scope == 'member')
			{
				$this->set('scope_id', $this->get('created_by'));
			}

			$cls = 'BlogModelAdapter' . ucfirst($scope);

			if (!class_exists($cls))
			{
				$path = __DIR__ . '/adapters/' . $scope . '.php';
				if (!is_file($path))
				{
					throw new \InvalidArgumentException(JText::sprintf('Invalid scope of "%s"', $scope));
				}
				include_once($path);
			}

			$this->_adapter = new $cls($this->get('scope_id'));
			$this->_adapter->set('publish_up', $this->get('publish_up'));
			$this->_adapter->set('id', $this->get('id'));
			$this->_adapter->set('alias', $this->get('alias'));
		}
		return $this->_adapter;
	}

	/**
	 * Return a formatted timestamp
	 *
	 * @param      string $as What format to return
	 * @return     string
	 */
	public function published($as='')
	{
		switch (strtolower($as))
		{
			case 'date':
				return JHTML::_('date', $this->get('publish_up'), JText::_('DATE_FORMAT_HZ1'));
			break;

			case 'time':
				return JHTML::_('date', $this->get('publish_up'), JText::_('TIME_FORMAT_HZ1'));
			break;

			default:
				return $this->get('publish_up');
			break;
		}
	}

	/**
	 * Get the content of the entry
	 *
	 * @param      string  $as      Format to return state in [text, number]
	 * @param      integer $shorten Number of characters to shorten text to
	 * @return     string
	 */
	public function content($as='parsed', $shorten=0)
	{
		$as = strtolower($as);
		$options = array();

		switch ($as)
		{
			case 'parsed':
				$content = $this->get('content.parsed', null);

				if ($content === null)
				{
					$scope  = JHTML::_('date', $this->get('publish_up'), 'Y') . '/';
					$scope .= JHTML::_('date', $this->get('publish_up'), 'm');

					$config = array(
						'option'   => $this->_adapter()->get('option'),
						'scope'    => $this->_adapter()->get('scope') . '/' . $scope,
						'pagename' => $this->get('alias'),
						'pageid'   => 0, //$this->get('id'),
						'filepath' => $this->_adapter()->get('path'),
						'domain'   => ''
					);

					$content = str_replace(array('\"', "\'"), array('"', "'"), (string) $this->get('content', ''));
					$this->importPlugin('content')->trigger('onContentPrepare', array(
						$this->_context,
						&$this,
						&$config
					));

					$this->set('content.parsed', (string) $this->get('content', ''));
					$this->set('content', $content);

					return $this->content($as, $shorten);
				}

				$options['html'] = true;
			break;

			case 'clean':
				$content = strip_tags($this->content('parsed'));
			break;

			case 'raw':
			default:
				$content = str_replace(array('\"', "\'"), array('"', "'"), $this->get('content'));
				$content = preg_replace('/^(<!-- \{FORMAT:.*\} -->)/i', '', $content);
			break;
		}

		if ($shorten)
		{
			$content = \Hubzero\Utility\String::truncate($content, $shorten, $options);
		}
		return $content;
	}

	/**
	 * Check a user's authorization
	 *
	 * @param      string $action Action to check
	 * @return     boolean True if authorized, false if not
	 */
	public function access($action='view')
	{
		if (!$this->params->get('access-check-done', false))
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
				$this->params->set('access-check-done', true);
			}
			else
			{
				// Check if they're a site admin
				$this->params->set('access-admin-entry', $juser->authorise('core.admin', $this->get('id')));
				$this->params->set('access-manage-entry', $juser->authorise('core.manage', $this->get('id')));
				$this->params->set('access-delete-entry', $juser->authorise('core.manage', $this->get('id')));
				$this->params->set('access-edit-entry', $juser->authorise('core.manage', $this->get('id')));
				$this->params->set('access-edit-state-entry', $juser->authorise('core.manage', $this->get('id')));
				$this->params->set('access-edit-own-entry', $juser->authorise('core.manage', $this->get('id')));

				// If they're not an admin
				if (!$this->params->get('access-admin-entry')
				 && !$this->params->get('access-manage-entry'))
				{
					// Was the entry created by the current user?
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

				$this->params->set('access-check-done', true);
			}
		}
		return $this->params->get('access-' . strtolower($action) . '-entry');
	}
}

