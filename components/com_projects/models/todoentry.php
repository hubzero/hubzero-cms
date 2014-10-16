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

require_once(JPATH_ROOT . DS . 'administrator' . DS . 'components' . DS . 'com_projects' . DS . 'tables' . DS . 'project.todo.php');
require_once(JPATH_ROOT . DS . 'components' . DS . 'com_projects' . DS . 'models' . DS . 'comment.php');

/**
 * Model class for a todo entry
 */
class ProjectModelTodoEntry extends \Hubzero\Base\Model
{
	/**
	 * Table name
	 *
	 * @var string
	 */
	protected $_tbl_name = 'ProjectTodo';

	/**
	 * Model context
	 *
	 * @var string
	 */
	protected $_context = 'com_projects.todo.details';

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
	 * Hubzero\User\Profile
	 *
	 * @var object
	 */
	private $_owner = null;

	/**
	 * Hubzero\User\Profile
	 *
	 * @var object
	 */
	private $_closer = null;

	/**
	 * Constructor
	 *
	 * @param   mixed    $oid       ID (int) or alias (string)
	 * @return  void
	 */
	public function __construct($oid)
	{
		$this->_db = JFactory::getDBO();

		$this->_tbl = new ProjectTodo($this->_db);

		if ($oid)
		{
			if (is_numeric($oid))
			{
				$this->_tbl->load($oid);
			}
			else if (is_object($oid) || is_array($oid))
			{
				$this->bind($oid);
			}
		}
	}

	/**
	 * Returns a reference to a blog entry model
	 *
	 * @param   mixed    $oid       ID (int) or alias (string)
	 * @return  object   ProjectModelTodoEntry
	 */
	static function &getInstance($oid=null)
	{
		static $instances;

		if (!isset($instances))
		{
			$instances = array();
		}

		if (is_object($oid))
		{
			$key = $oid->id;
		}
		else if (is_array($oid))
		{
			$key = $oid['id'];
		}
		else
		{
			$key = $oid;
		}

		if (!isset($instances[$key]))
		{
			$instances[$key] = new self($oid);
		}

		return $instances[$key];
	}

	/**
	 * Return a formatted created timestamp
	 *
	 * @param      string $as What data to return
	 * @return     string
	 */
	public function created($as='')
	{
		return $this->_date('created', $as);
	}

	/**
	 * Return a formatted modified timestamp
	 *
	 * @param      string $as What data to return
	 * @return     string
	 */
	public function due($as='')
	{
		return $this->_date('duedate', $as);
	}

	/**
	 * Return a formatted modified timestamp
	 *
	 * @param      string $as What data to return
	 * @return     string
	 */
	public function closed($as='')
	{
		return $this->_date('closed', $as);
	}

	/**
	 * Is item overdue?
	 *
	 * @return  boolean
	 */
	public function isOverdue()
	{
		if ($this->get('duedate') != '0000-00-00 00:00:00' && $this->get('duedate') < JFactory::getDate())
		{
			return true;
		}
		return false;
	}

	/**
	 * Is item complete?
	 *
	 * @return  boolean
	 */
	public function isComplete()
	{
		if ($this->get('state') == 1)
		{
			return true;
		}
		return false;
	}

	/**
	 * Return a formatted timestamp
	 *
	 * @param      string $key Field to return
	 * @param      string $as  What data to return
	 * @return     string
	 */
	protected function _date($key, $as='')
	{
		if ($this->get($key) == '0000-00-00 00:00:00')
		{
			return NULL;
		}
		switch (strtolower($as))
		{
			case 'date':
				return JHTML::_('date', $this->get($key), JText::_('DATE_FORMAT_HZ1'));
			break;

			case 'time':
				return JHTML::_('date', $this->get($key), JText::_('TIME_FORMAT_HZ1'));
			break;

			default:
				return $this->get($key);
			break;
		}
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
	 * Get the owner of this entry
	 *
	 * Accepts an optional property name. If provided
	 * it will return that property value. Otherwise,
	 * it returns the entire object
	 *
	 * @param   string $property What data to return
	 * @param   mixed  $default  Default value
	 * @return  mixed
	 */
	public function owner($property=null, $default=null)
	{
		if (!($this->_owner instanceof \Hubzero\User\Profile))
		{
			$this->_owner = \Hubzero\User\Profile::getInstance($this->get('assigned_to'));
			if (!$this->_owner)
			{
				$this->_owner = new \Hubzero\User\Profile();
			}
		}
		if ($property)
		{
			$property = ($property == 'id') ? 'uidNumber' : $property;
			if ($property == 'picture')
			{
				return $this->_owner->getPicture();
			}
			return $this->_owner->get($property, $default);
		}
		return $this->_owner;
	}

	/**
	 * Get the owner of this entry
	 *
	 * Accepts an optional property name. If provided
	 * it will return that property value. Otherwise,
	 * it returns the entire object
	 *
	 * @param   string $property What data to return
	 * @param   mixed  $default  Default value
	 * @return  mixed
	 */
	public function closer($property=null, $default=null)
	{
		if (!($this->_closer instanceof \Hubzero\User\Profile))
		{
			$this->_closer = \Hubzero\User\Profile::getInstance($this->get('closed_by'));
			if (!$this->_closer)
			{
				$this->_closer = new \Hubzero\User\Profile();
			}
		}
		if ($property)
		{
			$property = ($property == 'id') ? 'uidNumber' : $property;
			if ($property == 'picture')
			{
				return $this->_closer->getPicture();
			}
			return $this->_closer->get($property, $default);
		}
		return $this->_closer;
	}

	/**
	 * Get a list or count of comments
	 *
	 * @param   string  $rtrn    Data format to return
	 * @param   array   $filters Filters to apply to data fetch
	 * @param   boolean $clear   Clear cached data?
	 * @return  mixed
	 */
	public function comments($rtrn='list', $filters=array(), $clear = false)
	{
		$tbl = new ProjectComment($this->_db);

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
					}
				}
				return $this->_comments_count;
			break;

			case 'list':
			case 'results':
			default:
				if (!($this->_comments instanceof \Hubzero\Base\ItemList) || $clear)
				{
					if ($results = $tbl->getComments($this->get('id'), 'todo'))
					{
						foreach ($results as $key => $result)
						{
							$results[$key] = new ProjectModelComment($result);
							$results[$key]->set('option', 'com_projects');
							$results[$key]->set('scope', '');
							$results[$key]->set('alias', '');
							$results[$key]->set('path', '');
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
}

