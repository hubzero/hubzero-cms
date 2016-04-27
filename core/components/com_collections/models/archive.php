<?php
/**
 * HUBzero CMS
 *
 * Copyright 2005-2015 HUBzero Foundation, LLC.
 *
 * Permission is hereby granted, free of charge, to any person obtaining a copy
 * of this software and associated documentation files (the "Software"), to deal
 * in the Software without restriction, including without limitation the rights
 * to use, copy, modify, merge, publish, distribute, sublicense, and/or sell
 * copies of the Software, and to permit persons to whom the Software is
 * furnished to do so, subject to the following conditions:
 *
 * The above copyright notice and this permission notice shall be included in
 * all copies or substantial portions of the Software.
 *
 * THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
 * IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
 * FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
 * AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
 * LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
 * OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN
 * THE SOFTWARE.
 *
 * HUBzero is a registered trademark of Purdue University.
 *
 * @package   hubzero-cms
 * @author    Shawn Rice <zooley@purdue.edu>
 * @copyright Copyright 2005-2015 HUBzero Foundation, LLC.
 * @license   http://opensource.org/licenses/MIT MIT
 */

namespace Components\Collections\Models;

use Components\Collections\Tables;
use Hubzero\Base\Object;
use Hubzero\Base\ItemList;
use Hubzero\User\Profile;
use Hubzero\Plugin\Params;
use User;
use Lang;

require_once(__DIR__ . DS . 'post.php');
require_once(__DIR__ . DS . 'following.php');
require_once(__DIR__ . DS . 'collection.php');

/**
 * Collections archive model
 */
class Archive extends Object
{
	/**
	 * Object type [member, group, etc.]
	 *
	 * @var string
	 */
	private $_object_type = NULL;

	/**
	 * Object ID [member ID, group ID, etc.]
	 *
	 * @var integer
	 */
	private $_object_id = NULL;

	/**
	 * JDatabase
	 *
	 * @var object
	 */
	private $_db = NULL;

	/**
	 * \Hubzero\Base\ItemList
	 *
	 * @var object
	 */
	private $_collections = null;

	/**
	 * Collection
	 *
	 * @var object
	 */
	private $_collection = null;

	/**
	 * \Hubzero\Base\ItemList
	 *
	 * @var object
	 */
	private $_followers = null;

	/**
	 * \Hubzero\Base\ItemList
	 *
	 * @var object
	 */
	private $_following = null;

	/**
	 * Is following?
	 *
	 * @var boolean
	 */
	private $_isFollowing = null;

	/**
	 * Is being followed?
	 *
	 * @var boolean
	 */
	private $_isFollowed = null;

	/**
	 * Constructor
	 *
	 * @param   string   $object_type  The object type
	 * @param   itneger  $object_id    The object ID
	 * @return  void
	 */
	public function __construct($object_type='', $object_id=0)
	{
		$this->_db = \App::get('db');

		$this->_object_type = (string) $object_type;
		$this->_object_id   = (int) $object_id;
	}

	/**
	 * Returns a reference to this model
	 *
	 * @param   string   $object_type  The object type
	 * @param   itneger  $object_id    The object ID
	 * @return  object
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
			$instances[$oid] = new self($object_type, $object_id);
		}

		return $instances[$oid];
	}

	/**
	 * Returns a property of the object or the default value if the property is not set.
	 *
	 * @param   string  $property  The name of the property
	 * @param   mixed   $default   The default value
	 * @return  mixed   The value of the property
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
	 * @param   string  $property  The name of the property
	 * @param   mixed   $value     The value of the property to set
	 * @return  mixed   Previous value of the property
	 */
	public function set($property, $value = null)
	{
		$property = '_' . $property;
		$previous = isset($this->$property) ? $this->$property : null;
		$this->$property = $value;
		return $previous;
	}

	/**
	 * Set and get a specific collection
	 *
	 * @param   mixed   $id
	 * @return  object
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
			if ($this->_collections instanceof ItemList)
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

			if (!$this->_collection)
			{
				$this->_collection = Collection::getInstance($id, $this->_object_id, $this->_object_type);
			}
		}
		// Return current offering
		return $this->_collection;
	}

	/**
	 * Get a count or list of collections
	 *
	 * @param   array  $filters  Filters to apply to the query that retrieves records
	 * @return  mixed  Integer or object
	 */
	public function collections($filters=array())
	{
		if ($this->_object_id)
		{
			$filters['object_id']   = $this->_object_id;
		}
		if ($this->_object_type)
		{
			$filters['object_type'] = $this->_object_type;
		}
		if (!isset($filters['state']))
		{
			$filters['state'] = 1;
		}

		if (isset($filters['count']) && $filters['count'])
		{
			$tbl = new Tables\Collection($this->_db);

			return $tbl->getCount($filters);
		}

		if (!($this->_collections instanceof ItemList))
		{
			$tbl = new Tables\Collection($this->_db);

			if (($results = $tbl->getRecords($filters)))
			{
				// Loop through all the items and push assets and tags
				foreach ($results as $key => $result)
				{
					$results[$key] = new Collection($result);
				}
			}

			$this->_collections = new ItemList($results);
		}

		return $this->_collections;
	}

	/**
	 * Get a count or list of followers
	 *
	 * @param   array  $filters  Filters to apply to the query that retrieves records
	 * @return  mixed  Integer or object
	 */
	public function followers($filters=array())
	{
		$filters['following_id']   = $this->_object_id;
		$filters['following_type'] = $this->_object_type;

		if (isset($filters['count']) && $filters['count'])
		{
			$tbl = new Tables\Following($this->_db);

			return $tbl->count($filters);
		}
		if (!($this->_followers instanceof ItemList))
		{
			$tbl = new Tables\Following($this->_db);

			if ($results = $tbl->find($filters))
			{
				// Loop through all the items and push assets and tags
				foreach ($results as $key => $result)
				{
					$results[$key] = new Following($result);
				}
			}

			$this->_followers = new ItemList($results);
		}

		return $this->_followers;
	}

	/**
	 * Get a count or list of following
	 *
	 * @param   array   $filters  Filters to apply to the query that retrieves records
	 * @param   string  $what     Following what? A collection or a member, etc.
	 * @return  mixed   Integer or object
	 */
	public function following($filters=array(), $what='all')
	{
		$filters['follower_id']   = $this->_object_id;
		$filters['follower_type'] = $this->_object_type;

		if (isset($filters['count']) && $filters['count'])
		{
			$tbl = new Tables\Following($this->_db);

			return $tbl->count($filters);
		}

		if ($what == 'first')
		{
			$filters['limit'] = 1;
		}

		if (!($this->_following instanceof ItemList))
		{
			$tbl = new Tables\Following($this->_db);

			if ($results = $tbl->find($filters))
			{
				// Loop through all the items and push assets and tags
				foreach ($results as $key => $result)
				{
					$results[$key] = new Following($result);
				}
			}

			$this->_following = new ItemList($results);
		}

		if ($what == 'collections')
		{
			$ids     = array();
			$members = array();
			$groups  = array();
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
					$query1 = "SELECT id FROM `#__collections` WHERE object_id IN (" . implode(',', $members) . ") AND object_type='member'";
				}
				if (count($groups) > 0)
				{
					$query2 = "SELECT id FROM `#__collections` WHERE object_id IN (" . implode(',', $groups) . ") AND object_type='group'";
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
				$ids = array_merge($ids, $this->_db->loadColumn());
			}

			return $ids;
		}

		return $this->_following;
	}

	/**
	 * Get a count or list of posts
	 *
	 * @param   array  $filters  Filters to apply to the query that retrieves records
	 * @return  mixed  Integer or array
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
			$tbl = new Tables\Post($this->_db);
			return $tbl->getCount($filters);
		}

		$tbl = new Tables\Post($this->_db);
		return $tbl->getRecords($filters);
	}

	/**
	 * Get a list of collections for a user
	 *
	 * - If no type or type='member', returns an array of collections
	 *   that user created.
	 * - If type='group', returns an array of collections in the groups
	 *   the user is a member of.
	 *
	 * @param   string $type What type ot get collections for [group, member]
	 * @return  array
	 */
	public function mine($type='')
	{
		$user = User::getInstance();

		$tbl = new Tables\Collection($this->_db);

		switch (strtolower(trim($type)))
		{
			case 'group':
			case 'groups':
				$collections = array();

				$member = Profile::getInstance($user->get('id'));

				$usergroups = $member->getGroups('members');
				$usergroups_manager = $member->getGroups('managers');

				if ($usergroups)
				{
					if ($usergroups_manager)
					{
						foreach ($usergroups_manager as $manager_group)
						{
							foreach ($usergroups as $user_group)
							{
								if ($user_group->gidNumber == $manager_group->gidNumber)
								{
									$user_group->manager = $manager_group->manager;
								}
							}
						}
					}
					foreach ($usergroups as $usergroup)
					{
						$groups = $tbl->getRecords(array(
							'object_type' => 'group',
							'object_id'   => $usergroup->gidNumber,
							'state'       => 1
						));
						if ($groups)
						{
							if (!isset($usergroup->params) || !is_object($usergroup->params))
							{
								$usergroup->params = Params::getCustomParams($usergroup->gidNumber, 'groups', 'collections');
							}
							foreach ($groups as $s)
							{
								if (!isset($collections[$s->group_alias]))
								{
									$collections[$s->group_alias] = array();
								}
								if ($usergroup->params->get('create_post', 0) == 1 && !$usergroup->manager)
								{
									continue;
								}
								/*if ($s->access == 4 && !$usergroup->manager)
								{
									continue;
								}*/
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
					'object_id'   => $user->get('id'),
					'state'       => 1
				));
			break;
		}

		return $collections;
	}

	/**
	 * Check if someone or a group is following this collection
	 *
	 * @param   integer $follower_id   ID of the follower
	 * @param   string  $follower_type Type of the follower [member, group]
	 * @return  boolean
	 */
	public function isFollowing($follower_id=null, $follower_type='member')
	{
		if (!isset($this->_isFollowing))
		{
			$this->_isFollowing = false;

			if (!$follower_id && $follower_type == 'member')
			{
				$follower_id = User::get('id');
			}

			$follow = new Following($this->_object_id, $this->_object_type, $follower_id, $follower_type);
			if ($follow->exists())
			{
				$this->_isFollowing = true;
			}
		}
		return $this->_isFollowing;
	}

	/**
	 * Check if someone or a group is following this collection
	 *
	 * @param   integer  $follower_id    ID of the follower
	 * @param   string   $follower_type  Type of the follower [member, group]
	 * @return  boolean
	 */
	public function isFollowed($follower_id=null, $follower_type='member')
	{
		if (!isset($this->_isFollowed))
		{
			$this->_isFollowed = false;

			if (!$follower_id && $follower_type == 'member')
			{
				$follower_id = User::get('id');
			}

			$follow = new Following($follower_id, $follower_type, $this->_object_id, $this->_object_type);
			if ($follow->exists())
			{
				$this->_isFollowed = true;
			}
		}
		return $this->_isFollowed;
	}

	/**
	 * Unfollow this collection
	 *
	 * @param   integer  $follower_id    ID of the follower
	 * @param   string   $follower_type  Type of the follower [member, group]
	 * @return  boolean
	 */
	public function unfollow($id, $what='collection', $follower_id=0, $follower_type='member')
	{
		$follow = new Following($id, $what, $follower_id, $follower_type);

		if (!$follow->exists())
		{
			$this->setError(Lang::txt('Item is not being followed'));
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
	 * Follow something [collection, member, goup]
	 *
	 * @param   integer  $id             ID of the thing being followed
	 * @param   string   $what           What's being followed
	 * @param   integer  $follower_id    ID of the follower
	 * @param   string   $follower_type  Type of the follower [member, group]
	 * @return  boolean
	 */
	public function follow($id, $what='collection', $follower_id=0, $follower_type='member')
	{
		$follow = new Following($id, $what, $follower_id, $follower_type);

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

	/**
	 * Get a collectible item based on component
	 *
	 * @param   string  $option
	 * @return  object
	 */
	public function collectible($option)
	{
		$cls = __NAMESPACE__ . '\\Item';

		if ($option != 'com_collections')
		{
			$option = strtolower(substr($option, 4));

			$path = __DIR__ . DS . 'item' . DS . $option . '.php';

			if (file_exists($path))
			{
				include_once($path);

				$cls = __NAMESPACE__ . '\\Item\\' . ucfirst($option);
			}
		}

		return new $cls();
	}
}
