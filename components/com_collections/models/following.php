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

require_once(JPATH_ROOT . DS . 'components' . DS . 'com_collections' . DS . 'tables' . DS . 'following.php');
require_once(JPATH_ROOT . DS . 'components' . DS . 'com_collections' . DS . 'models' . DS . 'iterator.php');

/**
 * Table class for forum posts
 */
class CollectionsModelFollowing extends JObject
{
	/**
	 * Resource ID
	 * 
	 * @var mixed
	 */
	private $_authorized = false;

	/**
	 * CollectionsTableCollection
	 * 
	 * @var object
	 */
	private $_tbl = NULL;

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
	private $_posts = null;

	/**
	 * Container for properties
	 * 
	 * @var array
	 */
	private $_post = null;

	/**
	 * Container for properties
	 * 
	 * @var array
	 */
	private $_item = null;

	/**
	 * Constructor
	 * 
	 * @param      integer $id  Resource ID or alias
	 * @param      object  &$db JDatabase
	 * @return     void
	 */
	public function __construct($oid=null, $following_type=null, $follower_id=0, $follower_type='member')
	{
		$this->_db = JFactory::getDBO();

		$this->_tbl = new CollectionsTableFollowing($this->_db);

		if (is_numeric($oid))
		{
			$this->_tbl->load($oid, $following_type, $follower_id, $follower_type);
		}
		else if (is_object($oid))
		{
			$this->_tbl->bind($oid);
			if (isset($oid->posts))
			{
				$this->_tbl->set('posts', $oid->posts);
			}
		}
		else if (is_array($oid))
		{
			$this->_tbl->bind($oid);
			if (isset($oid['posts']))
			{
				$this->_tbl->set('posts', $oid['posts']);
			}
		}
	}

	/**
	 * Returns a reference to a wiki page object
	 *
	 * This method must be invoked as:
	 *     $inst = CoursesInstance::getInstance($alias);
	 *
	 * @param      string $pagename The page to load
	 * @param      string $scope    The page scope
	 * @return     object WikiPage
	 */
	static function &getInstance($oid=null, $following_type=null, $follower_id=0, $follower_type='member')
	{
		static $instances;

		if (!isset($instances)) 
		{
			$instances = array();
		}

		$key = $oid . '_' . $following_type . '_' . $follower_id . '_' . $follower_type;

		if (!isset($instances[$key])) 
		{
			$instances[$key] = new CollectionsModelFollowing($oid, $following_type, $follower_id, $follower_type);
		}

		return $instances[$key];
	}

	/**
	 * Returns a property of the object or the default value if the property is not set.
	 *
	 * @param     string $property The name of the property
	 * @param     mixed  $default  The default value
	 * @return    mixed The value of the property
 	 */
	public function get($property, $default=null)
	{
		if (isset($this->_tbl->$property)) 
		{
			return $this->_tbl->$property;
		}
		return $default;
	}

	/**
	 * Modifies a property of the object, creating it if it does not already exist.
	 *
	 * @param     string $property The name of the property
	 * @param     mixed  $value    The value of the property to set
	 * @return    mixed Previous value of the property
	 */
	public function set($property, $value = null)
	{
		$previous = isset($this->_tbl->$property) ? $this->_tbl->$property : null;
		$this->_tbl->$property = $value;
		return $previous;
	}

	/**
	 * Check if the resource exists
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
	 * @return     object
	 */
	public function follower()
	{
		if (!isset($this->_follower) || !is_object($this->_follower))
		{
			if ($this->get('follower_type') == 'group')
			{
				$this->_follower = Hubzero_Group::getInstance($this->get('follower_id'));
			}
			else
			{
				$this->_follower = Hubzero_User_Profile::getInstance($this->get('follower_id'));
			}
		}
		return $this->_follower;
	}

	/**
	 * Get the creator of this entry
	 *
	 * @return     object
	 */
	public function following()
	{
		if (!isset($this->_follower) || !is_object($this->_follower))
		{
			switch ($this->get('following_type'))
			{
				case 'group':
					$this->_follower = Hubzero_Group::getInstance($this->get('following_id'));
				break;

				case 'member':
					$this->_follower = JUser::getInstance($this->get('following_id'));
				break;

				case 'collection':
					$this->_follower = CollectionsModelCollection::getInstance($this->get('following_id'));
				break;
			}
		}
		return $this->_follower;
	}

	/**
	 * Get the creator of this entry
	 *
	 * @return     object
	 */
	public function count($what='following')
	{
		$value = 0;

		switch (strtolower(trim($what)))
		{
			case 'following':
				$value = $this->_tbl->count(array(
					'follower_type' => $this->get('following_type'),
					'follower_id' => $this->get('following_id')
				));
			break;

			case 'followers':
				$value = $this->_tbl->count(array(
					'following_type' => $this->get('following_type'),
					'following_id' => $this->get('following_id')
				));
			break;

			case 'collections':
			break;

			case 'posts':
			break;
		}

		return $value;
	}

	/**
	 * Check if the resource exists
	 * 
	 * @param      mixed $idx Index value
	 * @return     array
	 */
	public function unfollow($id=null)
	{
		if (!$id)
		{
			$id = $this->get('id');
		}

		if (!$this->_tbl->delete($id))
		{
			$this->setError($this->_tbl->getError());
			return false;
		}

		return true;
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
	 * Short title for 'update'
	 * Long title (if any) ...
	 *
	 * @param unknown $course_id Parameter title (if any) ...
	 * @param array $data Parameter title (if any) ...
	 * @return boolean Return title (if any) ...
	 */
	public function store($check=true)
	{
		if (empty($this->_db))
		{
			return false;
		}

		if ($check)
		{
			if (!$this->_tbl->check())
			{
				$this->setError($this->_tbl->getError());
				return false;
			}
		}

		if (!$this->_tbl->store())
		{
			$this->setError($this->_tbl->getError());
			return false;
		}

		return true;
	}

	/**
	 * Short title for 'update'
	 * Long title (if any) ...
	 *
	 * @param unknown $course_id Parameter title (if any) ...
	 * @param array $data Parameter title (if any) ...
	 * @return boolean Return title (if any) ...
	 */
	public function delete()
	{
		if (empty($this->_db))
		{
			return false;
		}

		if (!$this->_tbl->delete())
		{
			$this->setError($this->_tbl->getError());
			return false;
		}

		return true;
	}

	/**
	 * Get the item entry for a collection
	 * 
	 * @return     void
	 */
	public function posts()
	{
		switch ($this->get('following_type'))
		{
			case 'member':
			break;

			case 'collection':
			break;
		}
	}
}
