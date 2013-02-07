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

require_once(JPATH_ROOT . DS . 'components' . DS . 'com_collections' . DS . 'tables' . DS . 'collection.php');
require_once(JPATH_ROOT . DS . 'components' . DS . 'com_collections' . DS . 'models' . DS . 'iterator.php');
require_once(JPATH_ROOT . DS . 'components' . DS . 'com_collections' . DS . 'models' . DS . 'post.php');

/**
 * Table class for forum posts
 */
class CollectionsModelCollection extends JObject
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
	 * Container for properties
	 * 
	 * @var array
	 */
	private $_following = null;

	/**
	 * Constructor
	 * 
	 * @param      integer $id  Resource ID or alias
	 * @param      object  &$db JDatabase
	 * @return     void
	 */
	public function __construct($oid=null, $object_id=0, $object_type='member')
	{
		$this->_db = JFactory::getDBO();

		$this->_tbl = new CollectionsTableCollection($this->_db);

		if (is_numeric($oid) || is_string($oid))
		{
			$this->_tbl->load($oid, $object_id, $object_type);
		}
		else if (is_object($oid))
		{
			$this->_tbl->bind($oid);
			if (isset($oid->posts))
			{
				$this->_tbl->set('posts', $oid->posts);
			}
			if (isset($oid->following))
			{
				$this->_following = $oid->following ? true : false;
			}
		}
		else if (is_array($oid))
		{
			$this->_tbl->bind($oid);
			if (isset($oid['posts']))
			{
				$this->_tbl->set('posts', $oid['posts']);
			}
			if (isset($oid['following']))
			{
				$this->_following = $oid['following'] ? true : false;
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
	static function &getInstance($oid=null, $object_id=0, $object_type='member')
	{
		static $instances;

		if (!isset($instances)) 
		{
			$instances = array();
		}

		$key = $oid . '_' . $object_id . '_' . $object_type;

		if (!isset($instances[$key])) 
		{
			$instances[$key] = new CollectionsModelCollection($oid, $object_id, $object_type);
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
	 * Check if the course exists
	 * 
	 * @param      mixed $idx Index value
	 * @return     array
	 */
	public function setup($object_id, $object_type)
	{
		return $this->_tbl->setup($object_id, $object_type);
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
	public function creator()
	{
		if (!isset($this->_creator) || !is_object($this->_creator))
		{
			$this->_creator = JUser::getInstance($this->get('created_by'));
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

		// Create an Item entry
		// This is because even collections can be reposted. Thus, there needs to be an item entry to "repost"
		require_once(JPATH_ROOT . DS . 'components' . DS . 'com_collections' . DS . 'tables' . DS . 'item.php');

		$item = new CollectionsTableItem($this->_db);
		$item->loadType($this->get('id'), 'collection');
		if (!$item->get('id'))
		{
			$item->type        = 'collection';
			$item->object_id   = $this->get('id');
			$item->title       = $this->get('title');
			$item->description = $this->get('description');

			if (!$item->check()) 
			{
				$this->setError($item->getError());
			}
			// Store new content
			if (!$item->store()) 
			{
				$this->setError($item->getError());
			}
		}

		return true;
	}

	/**
	 * Get the item entry for a collection
	 * 
	 * @return     void
	 */
	public function item()
	{
		if (!isset($this->_item) || !is_a($this->_item, 'CollectionsModelItem'))
		{
			require_once(JPATH_ROOT . DS . 'components' . DS . 'com_collections' . DS . 'tables' . DS . 'item.php');

			$item = new CollectionsTableItem($this->_db);
			$item->loadType($this->get('id'), 'collection');
			if (!$item->get('id'))
			{
				$item->type        = 'collection';
				$item->object_id   = $this->get('id');
				$item->title       = $this->get('title');
				$item->description = $this->get('description');

				if (!$item->check()) 
				{
					$this->setError($item->getError());
				}
				// Store new content
				if (!$item->store()) 
				{
					$this->setError($item->getError());
				}
			}

			$this->_item = new CollectionsModelItem($item);
		}
		return $this->_item;
	}

	/**
	 * Set and get a specific offering
	 * 
	 * @return     void
	 */
	public function reposts()
	{
		//$post = new CollectionsTablePost($this->_db);
		//$post->loadByBoard($this->get('id'), $this->item()->get('id'));

		return null;
	}

	/**
	 * Set and get a specific offering
	 * 
	 * @return     void
	 */
	public function post($id=null)
	{
		// If the current post isn't set
		//    OR the ID passed doesn't equal the current post's ID
		if (!isset($this->_post) 
		 || ($id !== null && (int) $this->_post->get('id') != $id))
		{
			// Reset current offering
			$this->_post = null;

			// If the list of all posts is available ...
			if (isset($this->_posts) && is_a($this->_posts, 'CollectionsModelIterator'))
			{
				// Find a post in the list that matches the ID passed
				foreach ($this->posts() as $key => $post)
				{
					if ((int) $post->get('id') == $id)
					{
						// Set current offering
						$this->_post = $post;
						break;
					}
				}
			}
			else
			{
				$this->_post = CollectionsModelPost::getInstance($id);
			}
		}
		// Return current post
		return $this->_post;
	}

	/**
	 * Get a list of posts in this collection
	 *   Accepts an array of filters for database query 
	 *   that retrieves results
	 * 
	 * @param      array $filters
	 * @return     object
	 */
	public function posts($filters=array())
	{
		if (!isset($this->_posts) || !is_a($this->_posts, 'CollectionsModelIterator'))
		{
			require_once(JPATH_ROOT . DS . 'components' . DS . 'com_collections' . DS . 'tables' . DS . 'item.php');

			$tbl = new CollectionsTableItem($this->_db);

			if (!isset($filters['collection_id']))
			{
				$filters['collection_id'] = $this->get('id');
			}

			if (($results = $tbl->getRecords($filters)))
			{
				$ids = array();
				foreach ($results as $key => $result)
				{
					$ids[] = $result->id;
				}

				// Get all the assets for this list of items
				require_once(JPATH_ROOT . DS . 'components' . DS . 'com_collections' . DS . 'tables' . DS . 'asset.php');

				$ba = new CollectionsTableAsset($this->_db);
				$assets = $ba->getRecords(array('item_id' => $ids));

				// Get all the tags for this list of items
				require_once(JPATH_ROOT . DS . 'components' . DS . 'com_collections' . DS . 'helpers' . DS . 'tags.php');

				$bt = new CollectionsTags($this->_db);
				$tags = $bt->getTagsForIds($ids);

				// Loop through all the items and push assets and tags
				foreach ($results as $key => $result)
				{
					$results[$key] = new CollectionsModelPost($result);
					//$results[$key]->item($result);
					if ($assets)
					{
						foreach ($assets as $asset)
						{
							if ($asset->item_id == $results[$key]->get('item_id'))
							{
								$results[$key]->item()->addAsset($asset);
							}
						}
					}
					if (isset($tags[$results[$key]->get('item_id')])) 
					{
						$results[$key]->item()->addTag($tags[$results[$key]->get('item_id')]);
					}
					else
					{
						$results[$key]->item()->addTag(null);
					}
				}
			}
			else
			{
				$results = array();
			}

			$this->_posts = new CollectionsModelIterator($results);
		}

		return $this->_posts;
	}

	/**
	 * Get a count of data associated with this collection
	 * 
	 * @param      string $what What to count
	 * @return     integer
	 */
	public function count($what='')
	{
		if (!isset($this->_counts) || !is_array($this->_counts))
		{
			$this->_counts = $this->_tbl->getPostTypeCount($this->get('id'));
		}
		$what = strtolower(trim($what));
		switch ($what)
		{
			case 'collection':
			case 'image':
			case 'text':
			case 'file':
			case 'link':
				if (isset($this->_counts[$what]))
				{
					return (int) $this->_counts[$what];
				}
				else
				{
					return 0;
				}
			break;

			case 'post':
				return (int) $this->get('posts', 0);
			break;

			default:
				return 0;
			break;
		}
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
		if (!isset($this->_following))
		{
			$this->_following = false;

			if (!$follower_id && $follower_type == 'member')
			{
				$follower_id = JFactory::getUser()->get('id');
			}

			$follow = new CollectionsModelFollowing($this->get('id'), 'collection', $follower_id, $follower_type);
			if ($follow->exists())
			{
				$this->_following = true;
			}
		}
		return $this->_following;
	}

	/**
	 * Unfollow this collection
	 * 
	 * @param      integer $follower_id   ID of the follower
	 * @param      string  $follower_type Type of the follower [member, group]
	 * @return     boolean
	 */
	public function unfollow($follower_id=null, $follower_type='member')
	{
		$follow = new CollectionsModelFollowing($this->get('id'), 'collection', $follower_id, $follower_type);

		if (!$follow->exists())
		{
			$this->setError(JText::_('Item is not being followed'));
			return false;
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
	public function follow($follower_id=null, $follower_type='member')
	{
		$follow = new CollectionsModelFollowing();
		$follow->bind(array(
			'following_id'   => $this->get('id'),
			'following_type' => 'collection',
			'follower_id'    => $follower_id,
			'follower_type'  => $follower_type
		));
		if (!$follow->store(true))
		{
			$this->setError($follow->getError());
			return false;
		}

		return true;
	}
}
