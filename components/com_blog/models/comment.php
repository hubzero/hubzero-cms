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

require_once(JPATH_ROOT . DS . 'components' . DS . 'com_blog' . DS . 'tables' . DS . 'comment.php');

if (!defined('BLOG_DATE_FORMAT'))
{
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
}

/**
 * Courses model class for a forum
 */
class BlogModelComment extends JObject
{
	/**
	 * ForumTablePost
	 * 
	 * @var object
	 */
	private $_tbl = null;

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
	public function __construct($oid)
	{
		$this->_db = JFactory::getDBO();

		$this->_tbl = new BlogTableComment($this->_db);

		if (is_numeric($oid) || is_string($oid))
		{
			$this->_tbl->load($oid);
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
	static function &getInstance($oid=0)
	{
		static $instances;

		if (!isset($instances)) 
		{
			$instances = array();
		}

		if (!isset($instances[$oid])) 
		{
			$instances[$oid] = new BlogModelComment($oid);
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
	public function isDeleted()
	{
		if (!in_array('state', array_keys($this->_tbl->getProperties())))
		{
			return false;
		}
		if ($this->get('state') == 2) 
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
	public function isReported()
	{
		if ($this->get('reports', -1) > 0)
		{
			return true;
		}
		// Reports hasn't been set
		if ($this->get('reports', -1) == -1) 
		{
			if (is_file(JPATH_ROOT . DS . 'administrator' . DS . 'components' . DS . 'com_support' . DS . 'tables' . DS . 'reportabuse.php')) 
			{
				include_once(JPATH_ROOT . DS . 'administrator' . DS . 'components' . DS . 'com_support' . DS . 'tables' . DS . 'reportabuse.php');
				$ra = new ReportAbuse($this->_db);
				$val = $ra->getCount(array(
					'id'       => $this->get('id'), 
					'category' => 'blog'
				));
				$this->set('reports', $val);
				if ($this->get('reports') > 0)
				{
					return true;
				}
			}
		}
		return false;
	}

	/**
	 * Return a formatted timestamp
	 * 
	 * @param      string $as What data to return
	 * @return     boolean
	 */
	public function created($as='')
	{
		switch (strtolower($as))
		{
			case 'date':
				return JHTML::_('date', $this->get('created'), BLOG_DATE_FORMAT, BLOG_DATE_TIMEZONE);
			break;

			case 'time':
				return JHTML::_('date', $this->get('created'), BLOG_TIME_FORMAT, BLOG_DATE_TIMEZONE);
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
	public function creator($property=null)
	{
		if (!isset($this->_creator) || !is_object($this->_creator))
		{
			//$this->_creator = JUser::getInstance($this->get('created_by'));
			$this->_creator = Hubzero_User_Profile::getInstance($this->get('created_by'));
		}
		if ($property && is_a($this->_creator, 'Hubzero_User_Profile')) //JUser
		{
			$property = ($property == 'id') ? 'uidNumber' : $property;
			return $this->_creator->get($property);
		}
		return $this->_creator;
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
	 * Get the creator of this entry
	 * 
	 * Accepts an optional property name. If provided
	 * it will return that property value. Otherwise,
	 * it returns the entire JUser object
	 *
	 * @return     mixed
	 */
	public function replies($rtrn='list', $filters=array())
	{
		if (!isset($filters['entry_id']))
		{
			$filters['entry_id'] = $this->get('entry_id');
		}
		if (!isset($filters['parent']))
		{
			$filters['parent'] = $this->get('id');
		}

		switch (strtolower($rtrn))
		{
			case 'count':
				if (!isset($this->_comments_count) || !is_numeric($this->_comments_count))
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
				if (!isset($this->_comments) || !is_a($this->_comments, 'BlogModelIterator'))
				{
					if ($this->get('replies', null) !== null)
					{
						$results = $this->get('replies');
					}
					else
					{
						$results = $this->_tbl->getAllComments($this->get('entry_id'), $this->get('id'));
					}

					if ($results)
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
}

