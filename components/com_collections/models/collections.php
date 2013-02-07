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

require_once(JPATH_ROOT . DS . 'components' . DS . 'com_collections' . DS . 'models' . DS . 'post.php');
require_once(JPATH_ROOT . DS . 'components' . DS . 'com_collections' . DS . 'models' . DS . 'following.php');
require_once(JPATH_ROOT . DS . 'components' . DS . 'com_collections' . DS . 'models' . DS . 'collection.php');
require_once(JPATH_ROOT . DS . 'components' . DS . 'com_collections' . DS . 'models' . DS . 'iterator.php');

/**
 * Table class for forum posts
 */
class CollectionsModel extends JObject
{
	/**
	 * Resource ID
	 * 
	 * @var mixed
	 */
	private $_authorized = false;

	/**
	 * JDatabase
	 * 
	 * @var object
	 */
	private $_object_type = NULL;

	/**
	 * JDatabase
	 * 
	 * @var object
	 */
	private $_object_id = NULL;

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
	private $_collections = null;

	/**
	 * Container for properties
	 * 
	 * @var array
	 */
	private $_collection = null;


	private $_followers = null;
	
	private $_following = null;
	
	private $_followed = null;

	/**
	 * Constructor
	 * 
	 * @param      integer $id  Resource ID or alias
	 * @param      object  &$db JDatabase
	 * @return     void
	 */
	public function __construct($object_type='', $object_id=0)
	{
		$this->_db = JFactory::getDBO();

		$this->_object_type = (string) $object_type;
		$this->_object_id   = (int) $object_id;
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
	static function &getInstance($object_type='', $object_id=0)
	{
		static $instances;

		if (!isset($instances)) 
		{
			$instances = array();
		}

		$oid = $object_type . '_' . $object_id;

		if (!isset($instances[$oid])) 
		{
			$instances[$oid] = new CollectionsModel($object_type, $object_id);
		}

		return $instances[$oid];
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
		$property = '_' . $property;
		if (isset($this->$property)) 
		{
			return $this->$property;
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
		$property = '_' . $property;
		$previous = isset($this->$property) ? $this->$property : null;
		$this->$property = $value;
		return $previous;
	}

	/**
	 * Set and get a specific offering
	 * 
	 * @return     void
	 */
	public function collection($id=null)
	{
		// If the current offering isn't set
		//    OR the ID passed doesn't equal the current offering's ID or alias
		if (!isset($this->_collection) 
		 || ($id !== null && (int) $this->_collection->get('id') != $id && (string) $this->_collection->get('alias') != $id))
		{
			// Reset current offering
			$this->_collection = null;

			// If the list of all offerings is available ...
			if (isset($this->_collections) && is_a($this->_collections, 'CollectionsModelIterator'))
			{
				// Find an offering in the list that matches the ID passed
				foreach ($this->collections() as $key => $collection)
				{
					if ((int) $collection->get('id') == $id || (string) $collection->get('alias') == $id)
					{
						// Set current offering
						$this->_collection = $collection;
						break;
					}
				}
			}
			else
			{
				$this->_collection = CollectionsModelCollection::getInstance($id, $this->_object_id, $this->_object_type);
			}
		}
		// Return current offering
		return $this->_collection;
	}

	/**
	 * Get a list of resource types
	 *   Accepts either a numeric array index or a string [id, name]
	 *   If index, it'll return the entry matching that index in the list
	 *   If string, it'll return either a list of IDs or names
	 * 
	 * @param      mixed $idx Index value
	 * @return     array
	 */
	public function collections($filters=array())
	{
		$filters['object_id']   = $this->_object_id;
		$filters['object_type'] = $this->_object_type;
		if (!isset($filters['state']))
		{
			$filters['state'] = 1;
		}

		if (isset($filters['count']) && $filters['count'])
		{
			$tbl = new CollectionsTableCollection($this->_db);

			return $tbl->getCount($filters);
		}

		if (!isset($this->_collections) || !is_a($this->_collections, 'CollectionsModelIterator'))
		{
			$tbl = new CollectionsTableCollection($this->_db);

			if (($results = $tbl->getRecords($filters)))
			{
				// Loop through all the items and push assets and tags
				foreach ($results as $key => $result)
				{
					$results[$key] = new CollectionsModelCollection($result);
				}
			/*}
			else
			{
				$tbl->setup($filters['object_id'], $filters['object_type']);
				if (!($results = $tbl->getRecords($filters)))
				{
					$results = array();
				}*/
			}

			$this->_collections = new CollectionsModelIterator($results);
		}

		return $this->_collections;
	}

	/**
	 * Get a list of resource types
	 *   Accepts either a numeric array index or a string [id, name]
	 *   If index, it'll return the entry matching that index in the list
	 *   If string, it'll return either a list of IDs or names
	 * 
	 * @param      mixed $idx Index value
	 * @return     array
	 */
	public function followers($filters=array())
	{
		$filters['following_id']   = $this->_object_id;
		$filters['following_type'] = $this->_object_type;

		if (isset($filters['count']) && $filters['count'])
		{
			$tbl = new CollectionsTableFollowing($this->_db);

			return $tbl->count($filters);
		}
		if (!isset($this->_followers) || !is_a($this->_followers, 'CollectionsModelIterator'))
		{
			$tbl = new CollectionsTableFollowing($this->_db);

			if (($results = $tbl->find($filters)))
			{
				// Loop through all the items and push assets and tags
				foreach ($results as $key => $result)
				{
					$results[$key] = new CollectionsModelFollowing($result);
				}
			}

			$this->_followers = new CollectionsModelIterator($results);
		}

		return $this->_followers;
	}

	/**
	 * Get a list of resource types
	 *   Accepts either a numeric array index or a string [id, name]
	 *   If index, it'll return the entry matching that index in the list
	 *   If string, it'll return either a list of IDs or names
	 * 
	 * @param      mixed $idx Index value
	 * @return     array
	 */
	public function following($filters=array(), $what='all')
	{
		$filters['follower_id']   = $this->_object_id;
		$filters['follower_type'] = $this->_object_type;

		if (isset($filters['count']) && $filters['count'])
		{
			$tbl = new CollectionsTableFollowing($this->_db);

			return $tbl->count($filters);
		}

		if ($what == 'first')
		{
			$filters['limit'] = 1;
		}

		if (!isset($this->_following) || !is_a($this->_following, 'CollectionsModelIterator'))
		{
			$tbl = new CollectionsTableFollowing($this->_db);

			if (($results = $tbl->find($filters)))
			{
				// Loop through all the items and push assets and tags
				foreach ($results as $key => $result)
				{
					$results[$key] = new CollectionsModelFollowing($result);
				}
			}

			$this->_following = new CollectionsModelIterator($results);
		}

		if ($what == 'collections')
		{
			$ids = array();
			$members = array();
			$groups = array();
			foreach ($this->_following as $following)
			{
				if ($following->get('following_type') == 'collection')
				{
					$ids[] = $following->get('following_id');
				}
				else
				{
					if ($following->get('following_type') == 'member')
					{
						$members[] = $following->get('following_id');
					}
					else if ($following->get('following_type') == 'group')
					{
						$groups[] = $following->get('following_id');
					}
				}
				
			}
			if (count($members) > 0 || count($groups) > 0)
			{
				if (count($members) > 0)
				{
					$query1 = "SELECT id FROM #__collections WHERE object_id IN (" . implode(',', $members) . ") AND object_type='member'";
				}
				if (count($groups) > 0)
				{
					$query2 = "SELECT id FROM #__collections WHERE object_id IN (" . implode(',', $groups) . ") AND object_type='group'";
				}
				if (count($members) > 0 && count($groups) > 0)
				{
					$query = "( $query1 ) UNION ( $query2 );";
				} 
				else if (count($members) > 0)
				{
					$query = $query1;
				}
				else if (count($groups) > 0)
				{
					$query = $query2;
				}

				$this->_db->setQuery($query);
				$ids = array_merge($ids, $this->_db->loadResultArray());
			}
			
			return $ids;
		}

		return $this->_following;
	}

	/**
	 * Get a list of resource types
	 *   Accepts either a numeric array index or a string [id, name]
	 *   If index, it'll return the entry matching that index in the list
	 *   If string, it'll return either a list of IDs or names
	 * 
	 * @param      mixed $idx Index value
	 * @return     array
	 */
	public function posts($filters=array())
	{
		$filters['object_id']   = $this->_object_id;
		$filters['object_type'] = $this->_object_type;
		if (!isset($filters['state']))
		{
			$filters['state'] = 1;
		}

		if (isset($filters['count']) && $filters['count'])
		{
			$tbl = new CollectionsTablePost($this->_db);

			return $tbl->getCount($filters);
		}
	}

	/**
	 * Get a list of resource types
	 *   Accepts either a numeric array index or a string [id, name]
	 *   If index, it'll return the entry matching that index in the list
	 *   If string, it'll return either a list of IDs or names
	 * 
	 * @param      mixed $idx Index value
	 * @return     array
	 */
	public function mine($type='')
	{
		$juser = JFactory::getUser();

		$tbl = new CollectionsTableCollection($this->_db);

		switch (strtolower(trim($type)))
		{
			case 'group':
			case 'groups':
				$collections = array();

				ximport('Hubzero_User_Profile');
				$member = Hubzero_User_Profile::getInstance($juser->get('id'));

				$usergroups = $member->getGroups('members');
				if ($usergroups)
				{
					foreach ($usergroups as $usergroup)
					{
						$groups = $tbl->getRecords(array(
							'object_type' => 'group',
							'object_id'   => $usergroup->gidNumber,
							'state'       => 1
						));
						if ($groups)
						{
							foreach ($groups as $s)
							{
								if (!isset($collections[$s->group_alias]))
								{
									$collections[$s->group_alias] = array();
								}
								if ($s->access == 4 && !$usergroup->manager)
								{
									continue;
								}
								$collections[$s->group_alias][] = $s;
								asort($collections[$s->group_alias]);
							}
						}
					}
				}

				asort($collections);
			break;

			case 'member':
			default:
				$collections = $tbl->getRecords(array(
					'object_type' => 'member',
					'object_id'   => $juser->get('id'),
					'state'       => 1
				));
			break;
		}

		return $collections;
	}

	/**
	 * Check if someone or a group is following this collection
	 * 
	 * @param      integer $follower_id   ID of the follower
	 * @param      string  $follower_type Type of the follower [member, group]
	 * @return     boolean
	 */
	public function isFollowing($follower_id=null, $follower_type='member')
	{
		if (!isset($this->_followed))
		{
			$this->_followed = false;

			if (!$follower_id && $follower_type == 'member')
			{
				$follower_id = JFactory::getUser()->get('id');
			}

			$follow = new CollectionsModelFollowing($this->_object_id, $this->_object_type, $follower_id, $follower_type);
			if ($follow->exists())
			{
				$this->_followed = true;
			}
		}
		return $this->_followed;
	}

	/**
	 * Unfollow this collection
	 * 
	 * @param      integer $follower_id   ID of the follower
	 * @param      string  $follower_type Type of the follower [member, group]
	 * @return     boolean
	 */
	public function unfollow($id, $what='collection', $follower_id=0, $follower_type='member')
	{
		$follow = new CollectionsModelFollowing($id, $what, $follower_id, $follower_type);

		if (!$follow->exists())
		{
			$this->setError(JText::_('Item is not being followed'));
			return true;
		}

		if (!$follow->delete())
		{
			$this->setError($follow->getError());
			return false;
		}

		return true;
	}

	/**
	 * Follow this collection
	 * 
	 * @param      integer $follower_id   ID of the follower
	 * @param      string  $follower_type Type of the follower [member, group]
	 * @return     boolean
	 */
	public function follow($id, $what='collection', $follower_id=0, $follower_type='member')
	{
		$follow = new CollectionsModelFollowing($id, $what, $follower_id, $follower_type);

		if (!$follow->exists())
		{
			$follow->bind(array(
				'following_id'   => $id,
				'following_type' => $what,
				'follower_id'    => $follower_id,
				'follower_type'  => $follower_type
			));
			if (!$follow->store(true))
			{
				$this->setError($follow->getError());
				return false;
			}
		}

		return true;
	}
}
