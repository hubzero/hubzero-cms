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

namespace Components\Support\Models;

use Components\Support\Tables;
use Hubzero\Base\Model;
use Hubzero\User\Profile;
use Hubzero\Base\ItemList;
use Hubzero\Utility\String;
use Hubzero\Utility\Validate;
use Component;
use Request;
use Route;
use Lang;
use User;
use Date;

require_once(dirname(__DIR__) . DS . 'tables' . DS . 'comment.php');
require_once(__DIR__ . DS . 'attachment.php');
require_once(__DIR__ . DS . 'changelog.php');

/**
 * Support mdoel for a ticket comment
 */
class Comment extends Model
{
	/**
	 * Table name
	 *
	 * @var string
	 */
	protected $_tbl_name = '\\Components\\Support\\Tables\\Comment';

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
	 * User
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
	 * @return  boolean
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
	 * @param   string  $as  What format to return
	 * @return  boolean
	 */
	public function created($as='')
	{
		switch (strtolower($as))
		{
			case 'date':
				return Date::of($this->get('created'))->toLocal(Lang::txt('DATE_FORMAT_HZ1'));
			break;

			case 'time':
				return Date::of($this->get('created'))->toLocal(Lang::txt('TIME_FORMAT_HZ1'));
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
	 * it returns the entire User object
	 *
	 * @param   string  $property  Property to retrieve
	 * @param   mixed   $default   Default value if property not set
	 * @return  mixed
	 */
	public function creator($property=null, $default=null)
	{
		if (!($this->_creator instanceof Profile))
		{
			$this->_creator = Profile::getInstance($this->get('created_by'));
			if (!$this->_creator)
			{
				$this->_creator = new Profile;
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
	 * @param   string   $rtrn     Data to return state in [count, list]
	 * @param   array    $filters  Filters to apply to the query
	 * @param   boolean  $clear    Clear data cache?
	 * @return  mixed
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
					$tbl = new Tables\Attachment($this->_db);
					$this->_cache['attachments.count'] = $tbl->find('count', $filters);
				}
				return $this->_cache['attachments.count'];
			break;

			case 'list':
			case 'results':
			default:
				if (!($this->_cache['attachments.list'] instanceof ItemList) || $clear)
				{
					$tbl = new Tables\Attachment($this->_db);
					if ($results = $tbl->find('list', $filters))
					{
						foreach ($results as $key => $result)
						{
							$results[$key] = new Attachment($result);
						}
					}
					else
					{
						$results = array();
					}
					$this->_cache['attachments.list'] = new ItemList($results);
				}
				return $this->_cache['attachments.list'];
			break;
		}
	}

	/**
	 * Get the content of the entry is various formats
	 *
	 * @param   string   $as       Format to return state in [text, number]
	 * @param   integer  $shorten  Number of characters to shorten text to
	 * @return  mixed    String or Integer
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
						$config = Component::params('com_support');
						$path = trim($config->get('webpath', '/site/tickets'), DS) . DS . $this->get('ticket');

						$webpath = str_replace('//', '/', rtrim(Request::base(), '/') . '/' . $path);
						if (isset($_SERVER['HTTPS']))
						{
							$webpath = str_replace('http:', 'https:', $webpath);
						}
						if (!strstr($webpath, '://'))
						{
							$webpath = str_replace(':/', '://', $webpath);
						}

						$attach = new Tables\Attachment($this->_db);
						$attach->webpath = $webpath;
						$attach->uppath  = PATH_APP . DS . $path;
						$attach->output  = 'web';
					}

					$comment = $this->get('comment');

					//if (!strstr($comment, '</p>') && !strstr($comment, '<pre class="wiki">'))
					//{
						$comment = preg_replace("/<br\s?\/>/i", '', $comment);
						$comment = htmlentities($comment, ENT_COMPAT, 'UTF-8');
						$comment = nl2br($comment);
						$comment = str_replace("\t", ' &nbsp; &nbsp;', $comment);
						$comment = preg_replace('/  /', ' &nbsp;', $comment);
					//}
					$comment = preg_replace('/\{ticket#([\d]+)\}/i', '<a href="' . Route::url("index.php?option=com_support&task=ticket&id=$1") . '">' . Lang::txt('ticket #%s', "$1") . '</a>', $comment);

					$comment = preg_replace_callback('/\{attachment#[0-9]*\}/sU', array(&$this,'_getAttachment'), $comment);
					$this->set('comment.parsed', $comment);

					return $this->content($as, $shorten);
				}

				$options['html'] = true;
			break;

			case 'clean':
				$content = html_entity_decode(strip_tags($this->content('parsed')));
				$content = str_replace('&nbsp;', ' ', $content);
			break;

			case 'raw':
			default:
				$content = stripslashes($this->get('comment'));
			break;
		}

		if ($shorten)
		{
			$content = String::truncate($content, $shorten, $options);
		}
		return $content;
	}

	/**
	 * Process an attachment macro and output a link to the file
	 *
	 * @param   array   $matches  Macro info
	 * @return  string  HTML
	 */
	protected function _getAttachment($matches)
	{
		$tokens = explode('#', $matches[0]);
		$id = intval(end($tokens));

		$attach = new Tables\Attachment($this->_db);
		$attach->load($id);
		if ($attach->id && !$attach->comment_id)
		{
			$attach->comment_id = $this->get('id');
			$attach->created    = $this->get('created');
			$attach->created_by = $this->creator('id');
			$attach->store();
		}

		if (!($this->_cache['attachments.list'] instanceof ItemList))
		{
			$this->_cache['attachments.list'] = new ItemList(array());
		}

		$this->_cache['attachments.list']->add(new Attachment($attach));

		return '';
	}

	/**
	 * Delete the record and all associated data
	 *
	 * @param   boolean  $check  Validate data?
	 * @return  boolean  False if error, True on success
	 */
	public function store($check=true)
	{
		$this->set('changelog', $this->changelog()->__toString());

		return parent::store($check);
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
	 * @return  object
	 */
	public function changelog()
	{
		if (!($this->_log instanceof Changelog))
		{
			$this->_log = new Changelog($this->get('changelog'));
		}
		return $this->_log;
	}

	/**
	 * Generate and return various links to the entry
	 * Link will vary depending upon action desired, such as edit, delete, etc.
	 *
	 * @param   string  $type  The type of link to return
	 * @return  string
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
	 * @param   string  $to
	 * @param   string  $role
	 * @return  object
	 */
	public function addTo($to, $role='')
	{
		$added = false;

		// User ID
		if (is_numeric($to))
		{
			$user = User::getInstance($to);
			if (is_object($user) && $user->get('id'))
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
			if (strstr($to, '@') && Validate::email($to))
			{
				if (isset($this->_cache['recipients.added'][$to]))
				{
					return $this;
				}
				$this->_cache['recipients.added'][$to] = array(
					'role'    => $role,
					'name'    => Lang::txt('COM_SUPPORT_UNKNOWN'),
					'email'   => $to,
					'id'      => 0
				);
				$added = true;
			}
			// Username
			else
			{
				$user = User::getInstance($to);
				if (is_object($user) && $user->get('id'))
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
	 * @param   string  $to
	 * @return  array
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

