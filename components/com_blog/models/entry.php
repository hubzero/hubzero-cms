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
 * Model class for a blog entry
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
	 * @param   mixed    $oid       ID (int) or alias (string)
	 * @param   string   $scope     site|member|group
	 * @param   integer  $scope_id  ID of the scope object
	 * @return  void
	 */
	public function __construct($oid, $scope=null, $scope_id=null)
	{
		$this->_db = JFactory::getDBO();

		$this->_tbl = new BlogTableEntry($this->_db);

		if ($oid)
		{
			if (is_numeric($oid) && $scope_id == null)
			{
				$this->_tbl->load($oid);
			}
			else if (is_string($oid))
			{
				$this->_tbl->loadAlias($oid, $scope, $scope_id);
			}
			else if (is_object($oid) || is_array($oid))
			{
				$this->bind($oid);
			}

			$this->params = new JRegistry($this->_tbl->get('params'));
		}
	}

	/**
	 * Returns a reference to a blog entry model
	 *
	 * @param   mixed    $oid       ID (int) or alias (string)
	 * @param   string   $scope     site|member|group
	 * @param   integer  $scope_id  ID of the scope object
	 * @return  object   BlogModelentry
	 */
	static function &getInstance($oid=null, $scope=null, $scope_id=null)
	{
		static $instances;

		if (!isset($instances))
		{
			$instances = array();
		}

		if (!isset($instances[$oid]))
		{
			$instances[$oid] = new static($oid, $scope, $scope_id);
		}

		return $instances[$oid];
	}

	/**
	 * Has the publish window started?
	 *
	 * @return  boolean
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
	 * @return  boolean
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
	 * Has the offering started?
	 *
	 * @return     boolean
	 */
	public function isDeleted()
	{
		if ($this->get('state') == -1)
		{
			return true;
		}
		return false;
	}

	/**
	 * Check if the entry is available
	 *
	 * @return  boolean
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
	 * it returns the entire user object
	 *
	 * @param   string  $property
	 * @param   mixed   $default
	 * @return  mixed
	 */
	public function creator($property=null, $default=null)
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
			return $this->_creator->get($property, $default);
		}
		return $this->_creator;
	}

	/**
	 * Set and get a specific comment
	 *
	 * @param   integer  $id  ID of specific comment to fetch
	 * @return  object   BlogModelComment
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
	 * @param   string   $rtrn     Data format to return
	 * @param   array    $filters  Filters to apply to data fetch
	 * @param   boolean  $clear    Clear cached data?
	 * @return  mixed
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
	 * @param   string   $what   Data format to return (string, array, cloud)
	 * @param   integer  $admin  Get admin tags? 0=no, 1=yes
	 * @return  mixed
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
	 * @param   string   $tags     Tags to apply
	 * @param   integer  $user_id  ID of tagger
	 * @param   integer  $admin    Tag as admin? 0=no, 1=yes
	 * @return  boolean
	 */
	public function tag($tags=null, $user_id=0, $admin=0)
	{
		$cloud = new BlogModelTags($this->get('id'));

		return $cloud->setTags($tags, $user_id, $admin);
	}

	/**
	 * Get the state of the entry as either text or numerical value
	 *
	 * @param   string  $as  Format to return state in [text, number]
	 * @return  mixed   String or Integer
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
	 * The magic call method is used to call object methods using the adapter.
	 *
	 * @param   string  $method     The name of the method called.
	 * @param   array   $arguments  The arguments of the method called.
	 * @return  array   An array of values returned by the methods called on the objects in the data set.
	 * @since   1.3.1
	 */
	public function __call($method, $arguments = array())
	{
		$callback = array($this->_adapter(), $method);

		if (is_callable($callback))
		{
			return call_user_func_array($callback, $arguments);
		}

		throw new \BadMethodCallException(JText::sprintf('Method "%s" does not exist.', $method));
	}

	/**
	 * Return the adapter for this entry's scope,
	 * instantiating it if it doesn't already exist
	 *
	 * @return  object
	 */
	private function _adapter()
	{
		if (!$this->_adapter)
		{
			$scope = strtolower($this->get('scope'));

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
	 * @param   string  $as  What format to return
	 * @return  string
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
	 * @param   string   $as       Format to return state in [text, number]
	 * @param   integer  $shorten  Number of characters to shorten text to
	 * @return  string
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
	 * @param   string   $action  Action to check
	 * @return  boolean  True if authorized, false if not
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
				// Do not allow logged-out users to see private, 
				// or 'registered' entries.
				if ($this->get('state') != 1)
				{
					$this->params->set('access-view-entry', false);
				}
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
					// Disallow access if the entry is private
					if ($this->get('state') == 0)
					{
						$this->params->set('access-view-entry', false);
					}

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
				else
				{
					$this->params->set('access-view-entry', true);
				}

				$this->params->set('access-check-done', true);
			}
		}
		return $this->params->get('access-' . strtolower($action) . '-entry');
	}

	/**
	 * Delete the record and all associated data
	 *
	 * @return  boolean  False if error, True on success
	 */
	public function delete()
	{
		// Can't delete what doesn't exist
		if (!$this->exists())
		{
			return true;
		}

		// Remove comments
		$comment = new BlogTableComment($this->_db);
		if (!$comment->deleteByEntry($this->get('id')))
		{
			$this->setError($comment->getError());
			return false;
		}

		$cloud = new BlogModelTags($this->get('id'));
		if (!$cloud->removeAll())
		{
			$this->setError(JText::_('COM_BLOG_ERROR_UNABLE_TO_DELETE_TAGS'));
			return false;
		}

		return parent::delete();
	}
}

