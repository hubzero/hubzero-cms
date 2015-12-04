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

require_once(JPATH_ROOT . DS . 'administrator' . DS . 'components' . DS . 'com_support' . DS . 'helpers' . DS . 'acl.php');
require_once(JPATH_ROOT . DS . 'administrator' . DS . 'components' . DS . 'com_support' . DS . 'tables' . DS . 'ticket.php');
require_once(JPATH_ROOT . DS . 'administrator' . DS . 'components' . DS . 'com_support' . DS . 'tables' . DS . 'watching.php');
require_once(JPATH_ROOT . DS . 'components' . DS . 'com_support' . DS . 'models' . DS . 'comment.php');
require_once(JPATH_ROOT . DS . 'components' . DS . 'com_support' . DS . 'models' . DS . 'tags.php');
require_once(JPATH_ROOT . DS . 'components' . DS . 'com_support' . DS . 'models' . DS . 'status.php');

/**
 * Support model for a ticket
 */
class SupportModelTicket extends \Hubzero\Base\Model
{
	/**
	 * Table name
	 *
	 * @var string
	 */
	protected $_tbl_name = 'SupportTicket';

	/**
	 * \Hubzero\Base\ItemList
	 *
	 * @var object
	 */
	private $_data = null;

	/**
	 * Support ACL
	 *
	 * @var object
	 */
	private $_acl = null;

	/**
	 * URL for this entry
	 *
	 * @var string
	 */
	private $_base = 'index.php?option=com_support';

	/**
	 * Constructor
	 *
	 * @param      mixed $oid Integer, array, or object
	 * @return     mixed
	 */
	public function __construct($oid=null)
	{
		parent::__construct($oid);

		if (!$this->get('summary') && $this->get('report'))
		{
			$this->set('summary', substr($this->get('report'), 0, 70));
			if (strlen($this->get('summary')) >= 70)
			{
				$this->set('summary', $this->get('summary') . '...');
			}
		}

		$this->_data = new \Hubzero\Base\Object();
	}

	/**
	 * Returns a reference to a support ticket model
	 *
	 * @param      mixed  $id  ID (int), array, or object
	 * @return     object SupportModelTicket
	 */
	static function &getInstance($id=null)
	{
		static $instances;

		if (!isset($instances))
		{
			$instances = array();
		}

		if (!isset($instances[$id]))
		{
			$instances[$id] = new self($id);
		}

		return $instances[$id];
	}

	/**
	 * Get the submitter of this entry
	 *
	 * Accepts an optional property name. If provided
	 * it will return that property value. Otherwise,
	 * it returns the entire JUser object
	 *
	 * @param      string $property User property to look up
	 * @param      mixed  $default  Value to return if property not found
	 * @return     mixed
	 */
	public function submitter($property=null, $default=null)
	{
		if (!($this->_data->get('submitter.profile') instanceof \Hubzero\User\Profile))
		{
			$user = \Hubzero\User\Profile::getInstance($this->get('login'));
			if (!is_object($user) || !$user->get('uidNumber'))
			{
				$user = new \Hubzero\User\Profile();
			}
			$user->set('name', $this->get('name'));
			$user->set('username', $this->get('login'));
			$user->set('email', $this->get('email'));

			$this->_data->set('submitter.profile', $user);
		}
		if ($property)
		{
			$property = ($property == 'id') ? 'uidNumber' : $property;
			return $this->_data->get('submitter.profile')->get($property, $default);
		}
		return $this->_data->get('submitter.profile');
	}

	/**
	 * Get the owner of this entry
	 *
	 * Accepts an optional property name. If provided
	 * it will return that property value. Otherwise,
	 * it returns the entire JUser object
	 *
	 * @param      string $property User property to look up
	 * @param      mixed  $default  Value to return if property not found
	 * @return     mixed
	 */
	public function owner($property=null, $default=null)
	{
		if (!($this->_data->get('owner.profile') instanceof \Hubzero\User\Profile))
		{
			$user = \Hubzero\User\Profile::getInstance($this->get('owner'));
			if (!is_object($user) || !$user->get('uidNumber'))
			{
				$user = new \Hubzero\User\Profile();
			}
			$this->_data->set('owner.profile', $user);
		}
		if ($property)
		{
			$property = ($property == 'id') ? 'uidNumber' : $property;
			return $this->_data->get('owner.profile')->get($property, $default);
		}
		return $this->_data->get('owner.profile');
	}

	/**
	 * Is the ticket open?
	 *
	 * @return     boolean
	 */
	public function isOpen()
	{
		if ($this->get('open') == 1)
		{
			return true;
		}
		return false;
	}

	/**
	 * Is the ticket in "waiting" status?
	 *
	 * @return     boolean
	 */
	public function isWaiting()
	{
		if ($this->isOpen())
		{
			if ($this->get('status') == 2)
			{
				return true;
			}
		}
		return false;
	}

	/**
	 * Is the ticket owned?
	 *
	 * @return     boolean
	 */
	public function isOwned()
	{
		if ($this->get('owner'))
		{
			return true;
		}
		return false;
	}

	/**
	 * Is the user the owner of the ticket?
	 *
	 * @param   integer $id
	 * @return  boolean
	 */
	public function isOwner($id='')
	{
		if ($this->isOwned())
		{
			$id = $id ?: JFactory::getUser()->get('id');

			if ($this->get('owner') == $id)
			{
				return true;
			}
		}
		return false;
	}

	/**
	 * Is the user the submitter of the ticket?
	 *
	 * @param   string  $username
	 * @return  boolean
	 */
	public function isSubmitter($username='')
	{
		$username = $username ?: JFactory::getUser()->get('username');

		if ($this->get('login') == $username)
		{
			return true;
		}

		return false;
	}

	/**
	 * Get the status text for a status
	 *
	 * @param      string $as Data to return
	 * @return     string
	 */
	public function status($as='text')
	{
		switch ($as)
		{
			case 'text':
				if ($this->get('status'))
				{
					foreach ($this->statuses() as $s)
					{
						if ($this->get('status') == $s->get('id'))
						{
							$status = $s->get('title');
							break;
						}
					}
				}
				else
				{
					$status = ($this->get('open') ? JText::_('COM_SUPPORT_TICKET_STATUS_NEW') : JText::_('COM_SUPPORT_TICKET_STATUS_CLOSED'));
				}
			break;

			case 'open':
				if ($this->get('status'))
				{
					foreach ($this->statuses() as $s)
					{
						if ($this->get('status') == $s->get('id'))
						{
							$status = $s->get('open');
							break;
						}
					}
				}
				else
				{
					$status = ($this->get('open') ? 1 : 0);
				}
			break;

			case 'color':
				if ($this->get('status'))
				{
					foreach ($this->statuses() as $s)
					{
						if ($this->get('status') == $s->get('id'))
						{
							$status = $s->get('color');
							break;
						}
					}
				}
				else
				{
					$status ='transparent';
				}
			break;

			case 'class':
				if ($this->get('status'))
				{
					foreach ($this->statuses() as $s)
					{
						if ($this->get('status') == $s->get('id'))
						{
							$status = $s->get('alias');
							break;
						}
					}
				}
				else
				{
					$status = ($this->get('open') ? 'new' : 'closed');
				}
			break;

			default:
				$status = $this->get('status');
			break;
		}
		return $status;
	}

	/**
	 * Mark a ticket as open
	 *
	 * @return     boolean
	 */
	public function open()
	{
		$this->set('open', 1)
		     ->set('status', 1)
		     ->set('resolved', '');

		/*if (!$this->store(false))
		{
			return false;
		}
		return true;*/
		return $this;
	}

	/**
	 * Mark a ticket as closed
	 *
	 * @param   string $resolution
	 * @return  boolean
	 */
	public function close($resolution=null)
	{
		$this->set('open', 0)
		     ->set('status', 0)
		     ->set('resolved', $resolution);

		/*if (!$this->store(false))
		{
			return false;
		}
		return true;*/

		return $this;
	}

	/**
	 * Get a count of or list of attachments on this model
	 *
	 * @param      string  $rtrn    Data to return state in [count, list]
	 * @param      array   $filters Filters to apply to the query
	 * @param      boolean $clear   Clear data cache?
	 * @return     mixed
	 */
	public function attachments($rtrn='list', $filters=array(), $clear=false)
	{
		$filters['comment_id'] = 0;
		if (!isset($filters['ticket']))
		{
			$filters['ticket'] = $this->get('id');
		}

		switch (strtolower($rtrn))
		{
			case 'count':
				if (!is_numeric($this->_data->get('attachments.count')) || $clear)
				{
					$tbl = new SupportAttachment($this->_db);
					$this->_data->set('attachments.count', $tbl->find('count', $filters));
				}
				return $this->_data->get('attachments.count');
			break;

			case 'list':
			case 'results':
			default:
				if (!$this->_data->get('attachments') instanceof \Hubzero\Base\ItemList || $clear)
				{
					$tbl = new SupportAttachment($this->_db);
					if ($results = $tbl->find('list', $filters))
					{
						foreach ($results as $key => $result)
						{
							$results[$key] = new SupportModelAttachment($result);
						}
					}
					else
					{
						$results = array();
					}
					$this->_data->set('attachments', new \Hubzero\Base\ItemList($results));
				}
				return $this->_data->get('attachments');
			break;
		}
	}

	/**
	 * Get a count of or list of comments on this model
	 *
	 * @param      string  $rtrn    Data to return state in [count, list]
	 * @param      array   $filters Filters to apply to the query
	 * @param      boolean $clear   Clear data cache?
	 * @return     mixed
	 */
	public function comments($rtrn='list', $filters=array(), $clear=false)
	{
		if (!isset($filters['ticket']))
		{
			$filters['ticket'] = $this->get('id');
		}
		if (!isset($filters['access']))
		{
			$filters['access'] = 1; //$this->access('read', 'private_comments');
		}
		if (!isset($filters['sort']))
		{
			$filters['sort'] = 'id';
		}
		if (!isset($filters['sort_Dir']))
		{
			$filters['sort_Dir'] = 'ASC';
		}

		switch (strtolower($rtrn))
		{
			case 'count':
				if (!is_numeric($this->_data->get('comments.count')) || $clear)
				{
					$tbl = new SupportComment($this->_db);
					$this->_data->set('comments.count', $tbl->countComments($filters['access'], $filters['ticket']));
				}
				return $this->_data->get('comments.count');
			break;

			case 'list':
			case 'results':
			default:
				if (!($this->_data->get('comments.list') instanceof \Hubzero\Base\ItemList) || $clear)
				{
					$tbl = new SupportComment($this->_db);
					if ($results = $tbl->getComments($filters['access'], $filters['ticket'], $filters['sort'], $filters['sort_Dir']))
					{
						foreach ($results as $key => $result)
						{
							$results[$key] = new SupportModelComment($result);
						}
					}
					else
					{
						$results = array();
					}
					$this->_data->set('comments.list', new \Hubzero\Base\ItemList($results));
				}
				return $this->_data->get('comments.list');
			break;
		}
	}

	/**
	 * Get a count of or list of ticket statuses
	 *
	 * @param      string  $rtrn    Data to return state in [count, list]
	 * @param      array   $filters Filters to apply to the query
	 * @param      boolean $clear   Clear data cache?
	 * @return     mixed
	 */
	public function statuses($rtrn='all', $filters=array(), $clear=false)
	{
		static $statuses;

		if (!isset($statuses) || $clear)
		{
			$tbl = new SupportTableStatus($this->_db);

			if (!isset($filters['sort']))
			{
				$filters['sort'] = 'id';
				$filters['sort_Dir'] = 'ASC';
			}

			$statuses = array();
			if ($rows = $tbl->find('list', $filters))
			{
				foreach ($rows as $row)
				{
					$statuses[] = new SupportModelStatus($row);
				}
			}
		}

		switch (strtolower($rtrn))
		{
			case 'open':
				$results = array();
				foreach ($statuses as $status)
				{
					if ($status->get('open'))
					{
						$results[] = $status;
					}
				}
			break;

			case 'closed':
				$results = array();
				foreach ($statuses as $status)
				{
					if (!$status->get('open'))
					{
						$results[] = $status;
					}
				}
			break;

			case 'all':
			default:
				$results = $statuses;
			break;
		}

		return new \Hubzero\Base\ItemList($results);
	}

	/**
	 * Get a user's ID
	 *
	 * Accepts a user ID, JUser object, \Hubzero\User\Profile object
	 * or username
	 *
	 * @param      mixed   $user Object, ID, or username
	 * @return     integer
	 */
	private function _resolveUserID($user)
	{
		$id = 0;

		if (!$user)
		{
			$user = JFactory::getUser();
		}
		if (is_numeric($user))
		{
			$id = $user;
		}
		else if (is_string($user))
		{
			$user = JUser::getInstance($user);
		}

		if ($user instanceof JUser)
		{
			$id = $user->get('id');
		}
		else if ($user instanceof \Hubzero\User\Profile)
		{
			$id = $user->get('uidNumber');
		}

		return $id;
	}

	/**
	 * Mark a user as "watching" this ticket
	 *
	 * @param      mixed   $user User object, username, or ID
	 * @return     boolean
	 */
	public function watch($user)
	{
		$user_id = $this->_resolveUserID($user);

		$tbl = new SupportTableWatching($this->_db);
		$tbl->load(
			$this->get('id'),
			$user_id
		);

		if ($tbl->get('id'))
		{
			return true;
		}

		$tbl->set('ticket_id', $this->get('id'));
		$tbl->set('user_id', $user_id);

		if (!$tbl->store())
		{
			$this->setError($tbl->getError());
			return false;
		}

		return true;
	}

	/**
	 * Remove a user from the watch list for this ticket
	 *
	 * @param      mixed   $user User object, username, or ID
	 * @return     boolean
	 */
	public function stopWatching($user)
	{
		$tbl = new SupportTableWatching($this->_db);
		$tbl->load(
			$this->get('id'),
			$this->_resolveUserID($user)
		);

		if (!$tbl->get('id'))
		{
			return true;
		}

		if (!$tbl->delete())
		{
			$this->setError($tbl->getError());
			return false;
		}
		$this->_data->set('watchers.list', null);

		return true;
	}

	/**
	 * Get a count of or list of watchers on this ticket
	 *
	 * @param      string  $rtrn    Data to return state in [count, list]
	 * @param      array   $filters Filters to apply to the query
	 * @param      boolean $clear   Clear data cache?
	 * @return     mixed
	 */
	public function watchers($rtrn='list', $filters=array(), $clear=false)
	{
		if (!isset($filters['ticket_id']))
		{
			$filters['ticket_id'] = $this->get('id');
		}

		switch (strtolower($rtrn))
		{
			case 'count':
				if (!is_numeric($this->_data->get('watchers.count')) || $clear)
				{
					$tbl = new SupportTableWatching($this->_db);
					$this->_data->set('watchers.count', $tbl->count($filters));
				}
				return $this->_data->get('watchers.count');
			break;

			case 'list':
			case 'results':
			default:
				if (!($this->_data->get('watchers.list') instanceof \Hubzero\Base\ItemList) || $clear)
				{
					$tbl = new SupportTableWatching($this->_db);
					if (!($results = $tbl->find($filters)))
					{
						$results = array();
					}
					$this->_data->set('watchers.list', new \Hubzero\Base\ItemList($results));
				}
				return $this->_data->get('watchers.list');
			break;
		}
	}

	/**
	 * Check if a user is watching this ticket
	 *
	 * @param      mixed   $user User object, username, or ID
	 * @return     boolean True if watching, False if not
	 */
	public function isWatching($user=null, $recheck=false)
	{
		$user_id = $this->_resolveUserID($user);

		foreach ($this->watchers('list', array(), $recheck) as $watcher)
		{
			if ($watcher->user_id == $user_id)
			{
				return true;
			}
		}
		return false;
	}

	/**
	 * Get the state of the entry as either text or numerical value
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
				$content = $this->get('report_parsed', null);

				if ($content === null)
				{
					$config = JComponentHelper::getParams('com_support');
					$path = trim($config->get('webpath', '/site/tickets'), DS) . DS . $this->get('id');

					$webpath = str_replace('//', '/', rtrim(JURI::getInstance()->base(), '/') . '/' . $path);
					if (isset($_SERVER['HTTPS']))
					{
						$webpath = str_replace('http:', 'https:', $webpath);
					}
					if (!strstr($webpath, '://'))
					{
						$webpath = str_replace(':/', '://', $webpath);
					}

					$attach = new SupportAttachment($this->_db);
					$attach->webpath = $webpath;
					$attach->uppath  = JPATH_ROOT . DS . $path;
					$attach->output  = 'web';

					// Escape potentially bad characters
					$this->set('report_parsed', htmlentities($this->get('report'), ENT_COMPAT, 'UTF-8'));
					// Convert line breaks to <br /> tags
					$this->set('report_parsed', nl2br($this->get('report_parsed')));
					// Convert tabs to spaces to preserve indention
					$this->set('report_parsed', str_replace("\t",' &nbsp; &nbsp;', $this->get('report_parsed')));
					// Look for any attachments (old style)
					$this->set('report_parsed', $attach->parse($this->get('report_parsed')));

					if (!$this->get('report_parsed'))
					{
						$this->set('report_parsed', JText::_('(no content found)'));
					}

					return $this->content('parsed');
				}

				$options = array('html' => true);
			break;

			case 'clean':
				$content = strip_tags($this->content('parsed'));
			break;

			case 'raw':
			default:
				$content = stripslashes($this->get('report'));
			break;
		}

		if ($shorten)
		{
			$content = \Hubzero\Utility\String::truncate($content, $shorten, $options);
		}
		return $content;
	}

	/**
	 * Delete the record and all associated data
	 *
	 * @return    boolean False if error, True on success
	 */
	public function delete()
	{
		// Can't delete what doesn't exist
		if (!$this->exists())
		{
			return true;
		}

		// Remove attachments
		foreach ($this->attachments('list') as $attachment)
		{
			if (!$attachment->delete())
			{
				$this->setError($attachment->getError());
				return false;
			}
		}

		// Remove watchers
		foreach ($this->watchers('list') as $watcher)
		{
			if (!$this->stopWatching($watcher->user_id))
			{
				return false;
			}
		}

		// Remove comments
		foreach ($this->comments('list') as $comment)
		{
			if (!$comment->delete())
			{
				$this->setError($comment->getError());
				return false;
			}
		}

		return parent::delete();
	}

	/**
	 * Get tags on the entry
	 * Optinal first agument to determine format of tags
	 *
	 * @param      string  $as    Format to return state in [comma-deliminated string, HTML tag cloud, array]
	 * @param      integer $admin Include amdin tags? (defaults to no)
	 * @return     mixed
	 */
	public function tags($as='cloud', $admin=null)
	{
		if (!$this->exists())
		{
			switch (strtolower($as))
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

		if (!$this->_data->get('cloud'))
		{
			$this->_data->set('cloud', new SupportModelTags($this->get('id')));
		}

		return $this->_data->get('cloud')->render($as, array('admin' => $admin));
	}

	/**
	 * Tag the entry
	 *
	 * @param   string   $tags
	 * @param   integer  $user_id
	 * @param   integer  $admin
	 * @return  boolean
	 */
	public function tag($tags=null, $user_id=0, $admin=0)
	{
		$cloud = new SupportModelTags($this->get('id'));

		return $cloud->setTags($tags, $user_id, $admin);
	}

	/**
	 * Tag the entry
	 *
	 * @param   string   $tag
	 * @return  boolean
	 */
	public function appendTag($tag)
	{
		if (!$this->_data->get('cloud'))
		{
			$this->_data->set('cloud', new SupportModelTags($this->get('id')));
		}

		return $this->_data->get('cloud')->append($tag);
	}

	/**
	 * Return a formatted timestamp
	 *
	 * @param      string $as What format to return
	 * @return     string
	 */
	public function created($as='')
	{
		switch (strtolower($as))
		{
			case 'date':
				return JHTML::_('date', $this->get('created'), JText::_('DATE_FORMAT_HZ1'));
			break;

			case 'time':
				return JHTML::_('date', $this->get('created'), JText::_('TIME_FORMAT_HZ1'));
			break;

			case 'local':
				return JHTML::_('date', $this->get('created'), $this->_db->getDateFormat());
			break;

			default:
				return $this->get('created');
			break;
		}
	}

	/**
	 * Generate and return various links to the entry
	 * Link will vary depending upon action desired, such as edit, delete, etc.
	 *
	 * @param      string $type The type of link to return
	 * @return     string
	 */
	public function link($type='')
	{
		$link = $this->_base;

		// If it doesn't exist or isn't published
		switch (strtolower($type))
		{
			case 'edit':
				$link .= '&controller=tickets&task=ticket&id=' . $this->get('id');
			break;

			case 'delete':
				$link .= '&controller=tickets&task=delete&id=' . $this->get('id');
			break;

			case 'update':
				$link .= '&controller=tickets&task=update';
			break;

			case 'stopwatching':
				$link .= '&controller=tickets&task=ticket&id=' . $this->get('id') . '&watch=stop';
			break;

			case 'watch':
			case 'startwatching':
				$link .= '&controller=tickets&task=ticket&id=' . $this->get('id') . '&watch=start';
			break;

			case 'comments':
				$link .= '&controller=tickets&task=ticket&id=' . $this->get('id') . '#comments';
			break;

			case 'permalink':
			default:
				$link .= '&controller=tickets&task=ticket&id=' . $this->get('id');
			break;
		}

		return $link;
	}

	/**
	 * Store changes to this database entry
	 *
	 * @param     boolean $check Perform data validation check?
	 * @return    boolean False if error, True on success
	 */
	public function store($check=true)
	{
		$this->set('open', $this->status('open'));
		if ($this->get('open'))
		{
			$this->set('resolved', '');
		}

		$result = parent::store($check);

		if ($result && !$this->_tbl->id)
		{
			$this->_tbl->getId();
		}

		return $result;
	}

	/**
	 * Access check
	 *
	 * @param      string $action The action to check
	 * @param      string $item   The item to check the action against
	 * @return     boolean
	 */
	public function access($action='view', $item='tickets')
	{
		if (!$this->get('_access-check-done', false))
		{
			$this->_acl = SupportACL::getACL();

			if ($this->isSubmitter() || $this->isOwner())
			{
				if (!$this->_acl->check('read', 'tickets'))
				{
					$this->_acl->setAccess('read', 'tickets', 1);
				}
				if (!$this->_acl->check('update', 'tickets'))
				{
					$this->_acl->setAccess('update', 'tickets', $this->isOwner() ? 1 : -1);
				}
				if (!$this->_acl->check('create', 'comments'))
				{
					$this->_acl->setAccess('create', 'comments', -1);
				}
				if (!$this->_acl->check('read', 'comments'))
				{
					$this->_acl->setAccess('read', 'comments', 1);
				}
			}

			if ($this->_acl->authorize($this->get('group')))
			{
				$this->_acl->setAccess('read',   'tickets',  1);
				$this->_acl->setAccess('update', 'tickets',  1);
				$this->_acl->setAccess('delete', 'tickets',  1);
				$this->_acl->setAccess('create', 'comments', 1);
				$this->_acl->setAccess('read',   'comments', 1);
				$this->_acl->setAccess('create', 'private_comments', 1);
				$this->_acl->setAccess('read',   'private_comments', 1);

				$this->set('_cc-check-done', true);
			}

			$this->set('_access-check-done', true);
		}

		if ($action == 'read' && $item == 'tickets' && !$this->_acl->check('read', 'tickets') && !$this->get('_cc-check-done'))
		{
			$user = JFactory::getUser();
			if (!$user->get('guest') && $this->comments()->total() > 0)
			{
				$last = $this->comments('list')->last(); //, array('access' => 1), true)->last();
				$cc = $last->changelog()->get('cc');
				if (in_array($user->get('username'), $cc) || in_array($user->get('email'), $cc))
				{
					$this->_acl->setAccess('read', 'tickets', 1);
					$this->_acl->setAccess('create', 'comments', -1);
					$this->_acl->setAccess('read', 'comments', 1);
				}
			}
			$this->set('_cc-check-done', true);
		}

		return $this->_acl->check($action, $item);
	}
}

