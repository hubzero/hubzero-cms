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

require_once(JPATH_ROOT . DS . 'administrator' . DS . 'components' . DS . 'com_support' . DS . 'tables' . DS . 'ticket.php');
require_once(JPATH_ROOT . DS . 'administrator' . DS . 'components' . DS . 'com_support' . DS . 'tables' . DS . 'watching.php');
require_once(JPATH_ROOT . DS . 'components' . DS . 'com_support' . DS . 'models' . DS . 'comment.php');
//require_once(JPATH_ROOT . DS . 'components' . DS . 'com_support' . DS . 'models' . DS . 'tags.php');

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
	 * \Hubzero\ItemList
	 * 
	 * @var object
	 */
	private $_data = null;

	/**
	 * URL for this entry
	 * 
	 * @var string
	 */
	private $_base = 'index.php?option=com_support';

	/**
	 * Get a count of or list of attachments on this model
	 * 
	 * @param      string  $rtrn    Data to return state in [count, list]
	 * @param      array   $filters Filters to apply to the query
	 * @param      boolean $clear   Clear data cache?
	 * @return     mixed
	 */
	public function __construct($oid)
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
	 * @param      mixed   $id      ID (int), array, or object
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
			$instances[$id] = new SupportModelTicket($id);
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
	 * @return     mixed
	 */
	public function submitter($property=null)
	{
		if (!($this->_data->get('submitter.profile') instanceof \Hubzero\User\Profile))
		{
			$user = \Hubzero\User\Profile::getInstance($this->get('login'));
			if (!is_object($user) || !$user->get('uidNumber'))
			{
				$user = new \Hubzero\User\Profile(0);
				$user->set('name', $this->get('name'));
				$user->set('username', $this->get('login'));
				$user->set('email', $this->get('email'));
			}
			$this->_data->set('submitter.profile', $user);
		}
		if ($property)
		{
			$property = ($property == 'id') ? 'uidNumber' : $property;
			return $this->_data->get('submitter.profile')->get($property);
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
	 * @return     mixed
	 */
	public function owner($property=null)
	{
		if (!($this->_data->get('owner.profile') instanceof \Hubzero\User\Profile))
		{
			$this->_data->set('owner.profile', \Hubzero\User\Profile::getInstance($this->get('owner')));
		}
		if ($property)
		{
			$property = ($property == 'id') ? 'uidNumber' : $property;
			return $this->_data->get('owner.profile')->get($property);
		}
		return $this->_data->get('owner.profile');
	}

	/**
	 * Is the question open?
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
	 * Is the question open?
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
	 * Get the status text for a status number
	 * 
	 * @param      integer $int Status number
	 * @return     string 
	 */
	public function status($as='text')
	{
		switch ($as)
		{
			case 'text':
				switch ($this->get('open'))
				{
					case 1:
						switch ($this->get('status'))
						{
							case 2:
								$status = JText::_('TICKET_STATUS_WAITING');
							break;
							case 1:
								$status = 'accepted';
							break;
							case 0:
							default:
								$status = JText::_('TICKET_STATUS_NEW');
							break;
						}
					break;
					case 0:
						$status = JText::_('TICKET_STATUS_RESOLVED');
					break;
				}
			break;

			default:
				$status = $this->get('status');
			break;
		}
		return $status;
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

		switch (strtolower($rtrn))
		{
			case 'count':
				if (!is_numeric($this->_data->get('comments.count')) || $clear)
				{
					$tbl = new SupportTableComment($this->_db);
					$this->_data->set('comments.count', $tbl->countComments($this->access('read', 'private_comments'), $this->get('id'))); //count($filters));
				}
				return $this->_data->get('comments.count');
			break;

			case 'list':
			case 'results':
			default:
				if (!($this->_data->get('comments.list') instanceof \Hubzero\Base\ItemList) || $clear)
				{
					$tbl = new SupportTableComment($this->_db);
					if ($results = $tbl->getComments($this->access('read', 'private_comments'), $this->get('id'))) //find($filters))
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
	 * Get a count of or list of comments on this model
	 *
	 * @param      string  $rtrn    Data to return state in [count, list]
	 * @return     boolean
	 */
	public function watch($user_id)
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
	 * Get a count of or list of comments on this model
	 *
	 * @param      string  $rtrn    Data to return state in [count, list]
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

		return true;
	}

	/**
	 * Get a count of or list of comments on this model
	 *
	 * @param      string  $rtrn    Data to return state in [count, list]
	 * @param      array   $filters Filters to apply to the query
	 * @param      boolean $clear   Clear data cache?
	 * @return     mixed
	 */
	public function watchers($rtrn='list', $filters=array(), $clear=false)
	{
		if (!isset($filters['ticket']))
		{
			$filters['ticket'] = $this->get('id');
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
						/*foreach ($results as $key => $result)
						{
							$results[$key] = new SupportModelComment($result);
						}
					}
					else
					{*/
						$results = array();
					}
					$this->_data->set('watchers.list', new \Hubzero\Base\ItemList($results));
				}
				return $this->_data->get('watchers.list');
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
	public function isWatching($user=null)
	{
		$user_id = $this->_resolveUserID($user);

		foreach ($this->watchers('list') as $watcher)
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
	 * @return     mixed String or Integer
	 */
	public function content($as='parsed', $shorten=0)
	{
		$as = strtolower($as);

		switch ($as)
		{
			case 'parsed':
				if ($content = $this->get('report_parsed'))
				{
					if ($shorten)
					{
						$content = \Hubzero\Utility\String::truncate($content, $shorten, array('html' => true));
					}
					return $content;
				}

				$attach = SupportModelAttachment::getInstance(0);
				$attach->set('ticket', $this->get('id'));

				$this->set('report_parsed', $attach->parse(stripslashes($this->get('report'))));

				return $this->content('parsed');
			break;

			case 'clean':
				$content = strip_tags($this->content('parsed'));
				if ($shorten)
				{
					$content = \Hubzero\Utility\String::truncate($content, $shorten);
				}
				return $content;
			break;

			case 'raw':
			default:
				$content = stripslashes($this->get('report'));
				if ($shorten)
				{
					$content = \Hubzero\Utility\String::truncate($content, $shorten);
				}
				return $content;
			break;
		}
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
	public function tags($as='cloud', $admin=0)
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
	 * @return     boolean
	 */
	public function tag($tags=null, $user_id=0, $admin=0)
	{
		if (!$this->_data->get('cloud'))
		{
			$this->_data->set('cloud', new SupportModelTags($this->get('id')));
		}

		return $this->_data->get('cloud')->setTags($tags, $user_id, $admin);
	}

	/**
	 * Tag the entry
	 * 
	 * @return     boolean
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
	 * Tag the entry
	 * 
	 * @return     boolean
	 */
	public function access($action='view', $item='tickets')
	{
		$juser = JFactory::getUser();

		if (!$this->get('_access-check-done', false))
		{
			$this->_acl = SupportACL::getACL();

			if ($this->get('login') == $juser->get('username')
			 || $this->get('owner') == $juser->get('username')) 
			{
				if (!$this->_acl->check('read', 'tickets')) 
				{
					$this->_acl->setAccess('read', 'tickets', 1);
				}
				if (!$this->_acl->check('update', 'tickets')) 
				{
					$this->_acl->setAccess('update', 'tickets', -1);
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
			}

			$this->set('_access-check-done', true);
		}

		return $this->_acl->check($action, $item);
	}
}

