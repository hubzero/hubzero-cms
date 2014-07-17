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

require_once(JPATH_ROOT . DS . 'administrator' . DS . 'components' . DS . 'com_support' . DS . 'tables' . DS . 'comment.php');
require_once(JPATH_ROOT . DS . 'components' . DS . 'com_support' . DS . 'models' . DS . 'attachment.php');
require_once(JPATH_ROOT . DS . 'components' . DS . 'com_support' . DS . 'models' . DS . 'changelog.php');

/**
 * Support mdoel for a ticket comment
 */
class SupportModelComment extends \Hubzero\Base\Model
{
	/**
	 * Table name
	 *
	 * @var string
	 */
	protected $_tbl_name = 'SupportComment';

	/**
	 * Cached data
	 *
	 * @var array
	 */
	private $_cache = array(
		'attachments.count' => null,
		'attachments.list'  => null,
		'recipients.added'  => array(),
		'recipients.failed' => array()
	);

	/**
	 * Base URL
	 *
	 * @var string
	 */
	private $_base = null;

	/**
	 * JUser
	 *
	 * @var object
	 */
	private $_creator = NULL;

	/**
	 * Changelog
	 *
	 * @var object
	 */
	private $_log;

	/**
	 * Is the question open?
	 *
	 * @return     boolean
	 */
	public function isPrivate()
	{
		if ($this->get('access') == 1)
		{
			return true;
		}
		return false;
	}

	/**
	 * Return a formatted timestamp
	 *
	 * @param      string $as What format to return
	 * @return     boolean
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
	 * Get the creator of this entry
	 *
	 * Accepts an optional property name. If provided
	 * it will return that property value. Otherwise,
	 * it returns the entire JUser object
	 *
	 * @return     mixed
	 */
	public function creator($property=null, $default=null)
	{
		if (!($this->_creator instanceof \Hubzero\User\Profile))
		{
			$this->_creator = \Hubzero\User\Profile::getInstance($this->get('created_by'));
			if (!$this->_creator)
			{
				$this->_creator = new \Hubzero\User\Profile;
			}
		}
		if ($property)
		{
			$property = ($property == 'id') ? 'uidNumber' : $property;
			if ($property == 'picture')
			{
				return $this->_creator->getPicture(($this->_creator->get('uidNumber') ? 0 : 1));
			}
			return $this->_creator->get($property, $default);
		}
		return $this->_creator;
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
		if (!isset($filters['ticket']))
		{
			$filters['ticket'] = $this->get('ticket');
		}
		if (!isset($filters['comment_id']))
		{
			$filters['comment_id'] = $this->get('id');
		}

		switch (strtolower($rtrn))
		{
			case 'count':
				if (!isset($this->_cache['attachments.count']) || $clear)
				{
					$tbl = new SupportAttachment($this->_db);
					$this->_cache['attachments.count'] = $tbl->find('count', $filters);
				}
				return $this->_cache['attachments.count'];
			break;

			case 'list':
			case 'results':
			default:
				if (!($this->_cache['attachments.list'] instanceof \Hubzero\Base\ItemList) || $clear)
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
					$this->_cache['attachments.list'] = new \Hubzero\Base\ItemList($results);
				}
				return $this->_cache['attachments.list'];
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
		static $attach;

		$as = strtolower($as);
		$options = array();

		switch ($as)
		{
			case 'parsed':
				$content = $this->get('comment.parsed', null);

				if ($content === null)
				{
					if (!$attach)
					{
						$config = JComponentHelper::getParams('com_support');
						$path = trim($config->get('webpath', '/site/tickets'), DS) . DS . $this->get('ticket');

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
					}

					$comment = $this->get('comment');
					if (!strstr($comment, '</p>') && !strstr($comment, '<pre class="wiki">'))
					{
						$comment = preg_replace("/<br\s?\/>/i", '', $comment);
						$comment = htmlentities($comment, ENT_COMPAT, 'UTF-8');
						$comment = nl2br($comment);
						$comment = str_replace("\t", ' &nbsp; &nbsp;', $comment);
						$comment = preg_replace('/  /', ' &nbsp;', $comment);
					}

					$comment = preg_replace_callback('/\{attachment#[0-9]*\}/sU', array(&$this,'_getAttachment'), $comment);
					$this->set('comment.parsed', $comment);

					return $this->content($as, $shorten);
				}

				$options['html'] = true;
			break;

			case 'clean':
				$content = strip_tags($this->content('parsed'));
			break;

			case 'raw':
			default:
				$content = stripslashes($this->get('comment'));
			break;
		}

		if ($shorten)
		{
			$content = \Hubzero\Utility\String::truncate($content, $shorten, $options);
		}
		return $content;
	}

	/**
	 * Process an attachment macro and output a link to the file
	 *
	 * @param      array $matches Macro info
	 * @return     string HTML
	 */
	protected function _getAttachment($matches)
	{
		$tokens = explode('#', $matches[0]);
		$id = intval(end($tokens));

		$attach = new SupportAttachment($this->_db);
		$attach->load($id);
		if ($attach->id && !$attach->comment_id)
		{
			$attach->comment_id = $this->get('id');
			$attach->created    = $this->get('created');
			$attach->created_by = $this->creator('id');
			$attach->store();
		}

		if (!($this->_cache['attachments.list'] instanceof \Hubzero\Base\ItemList))
		{
			$this->_cache['attachments.list'] = new \Hubzero\Base\ItemList(array());
		}

		$this->_cache['attachments.list']->add(new SupportModelAttachment($attach));

		return '';
	}

	/**
	 * Delete the record and all associated data
	 *
	 * @return    boolean False if error, True on success
	 */
	public function store($check=true)
	{
		$this->set('changelog', $this->changelog()->__toString());
		if (!$this->get('comment'))
		{
			$this->set('access', 1);
		}

		return parent::store($check);
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

		// Remove comments
		foreach ($this->attachments('list') as $attachment)
		{
			if (!$attachment->delete())
			{
				$this->setError($attachment->getError());
				return false;
			}
		}

		return parent::delete();
	}

	/**
	 * Get the changelog
	 *
	 * @return    object
	 */
	public function changelog()
	{
		if (!($this->_log instanceof SupportModelChangelog))
		{
			$this->_log = new SupportModelChangelog($this->get('changelog'));
		}
		return $this->_log;
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
		if (!isset($this->_base))
		{
			$this->_base = 'index.php?option=com_support&task=ticket&id=' . $this->get('ticket');
		}
		$link  = $this->_base;

		// If it doesn't exist or isn't published
		switch (strtolower($type))
		{
			case 'component':
			case 'base':
				return $this->_base;
			break;

			case 'permalink':
			default:
				$link .= '#c' . $this->get('id');
			break;
		}

		return $link;
	}

	/**
	 * Add to the recipient list
	 *
	 * @return    object
	 */
	public function addTo($to, $role='')
	{
		$added = false;

		// User ID
		if (is_numeric($to))
		{
			$user = JUser::getInstance($to);
			if ($user->get('id'))
			{
				if (isset($this->_cache['recipients.added'][$user->get('email')]))
				{
					return $this;
				}
				$this->_cache['recipients.added'][$user->get('email')] = array(
					'role'    => $role,
					'name'    => $user->get('name'),
					'email'   => $user->get('email'),
					'id'      => $user->get('id')
				);
				$added = true;
			}
		}
		else if (is_string($to))
		{
			// Email
			if (strstr($to, '@') && \Hubzero\Utility\Validate::email($to))
			{
				if (isset($this->_cache['recipients.added'][$to]))
				{
					return $this;
				}
				$this->_cache['recipients.added'][$to] = array(
					'role'    => $role,
					'name'    => JText::_('COM_SUPPORT_UNKNOWN'),
					'email'   => $to,
					'id'      => 0
				);
				$added = true;
			}
			// Username
			else
			{
				$user = JUser::getInstance($to);
				if ($user->get('id'))
				{
					if (isset($this->_cache['recipients.added'][$user->get('email')]))
					{
						return $this;
					}
					$this->_cache['recipients.added'][$user->get('email')] = array(
						'role'    => $role,
						'name'    => $user->get('name'),
						'email'   => $user->get('email'),
						'id'      => $user->get('id')
					);
					$added = true;
				}
			}
		}
		else if (is_array($to))
		{
			if (isset($this->_cache['recipients.added'][$to['email']]))
			{
				return $this;
			}
			$this->_cache['recipients.added'][$to['email']] = $to;
			$added = true;
		}

		if (!$added)
		{
			$this->_cache['recipients.failed'][] = $to;
		}

		return $this;
	}

	/**
	 * Get the recipient list
	 *
	 * @return    array
	 */
	public function to($who='')
	{
		$who = strtolower(trim($who));

		switch ($who)
		{
			case 'id':
			case 'ids':
				$tos = array();
				foreach ($this->_cache['recipients.added'] as $to)
				{
					if ($to['id'])
					{
						$tos[] = $to;
					}
				}
				return $tos;
			break;

			case 'email':
			case 'emails':
				$tos = array();
				foreach ($this->_cache['recipients.added'] as $to)
				{
					if (!$to['id'] && $to['email'])
					{
						$tos[] = $to;
					}
				}
				return $tos;
			break;
		}

		return $this->_cache['recipients.added'];
	}
}

