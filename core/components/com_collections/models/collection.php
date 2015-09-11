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
use Hubzero\Base\ItemList;
use Hubzero\User\Group;
use Hubzero\Utility\String;
use Request;
use User;
use Lang;

require_once(dirname(__DIR__) . DS . 'tables' . DS . 'collection.php');
require_once(__DIR__ . DS . 'post.php');

/**
 * Collections model class for a collection
 */
class Collection extends Base
{
	/**
	 * Authorization checked?
	 *
	 * @var boolean
	 */
	private $_authorized = false;

	/**
	 * Table class name
	 *
	 * @var string
	 */
	protected $_tbl_name = '\\Components\\Collections\\Tables\\Collection';

	/**
	 * Model context
	 *
	 * @var string
	 */
	protected $_context = 'com_collections.collection.description';

	/**
	 * ItemList
	 *
	 * @var object
	 */
	private $_posts = null;

	/**
	 * Post
	 *
	 * @var object
	 */
	private $_post = null;

	/**
	 * Item
	 *
	 * @var object
	 */
	private $_item = null;

	/**
	 * Following
	 *
	 * @var object
	 */
	private $_following = null;

	/**
	 * Adapter
	 *
	 * @var object
	 */
	private $_adapter = NULL;

	/**
	 * Constructor
	 *
	 * @param   mixed   $oid         Integer, string, array, or object
	 * @param   integer $object_id   ID of owner object
	 * @param   string  $object_type Owner type [member, group]
	 * @return  void
	 */
	public function __construct($oid=null, $object_id=0, $object_type='member')
	{
		$this->_db = \App::get('db');

		$tbl = $this->_tbl_name;
		$this->_tbl = new $tbl($this->_db);

		if (is_numeric($oid) || is_string($oid))
		{
			$this->_tbl->load($oid, $object_id, $object_type);
		}
		else if (is_object($oid))
		{
			$this->bind($oid);

			if (isset($oid->following))
			{
				$this->_following = $oid->following ? true : false;
			}
		}
		else if (is_array($oid))
		{
			$this->bind($oid);

			if (isset($oid['following']))
			{
				$this->_following = $oid['following'] ? true : false;
			}
		}
	}

	/**
	 * Returns a reference to this object
	 *
	 * @param   mixed    $oid          ID, array, or object
	 * @param   integer  $object_id    ID
	 * @param   string   $object_type  [member, group]
	 * @return  object
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
			$instances[$key] = new self($oid, $object_id, $object_type);
		}

		return $instances[$key];
	}

	/**
	 * Create a default collection
	 *
	 * @param   integer $object_id   ID of owner object
	 * @param   string  $object_type Owner type [member, group]
	 * @return  boolean
	 */
	public function setup($object_id, $object_type)
	{
		Lang::load('com_collections', PATH_APP . DS . 'bootstrap' . DS . 'site') ||
		Lang::load('com_collections', \Component::path('com_collections') . DS . 'site');

		$result = array(
			'id'          => 0,
			'title'       => Lang::txt('COM_COLLECTIONS_DEFAULT_TITLE'),
			'description' => Lang::txt('COM_COLLECTIONS_DEFAULT_DESC'),
			'object_id'   => $object_id,
			'object_type' => $object_type,
			'is_default'  => 1,
			'created_by'  => $object_id,
			'access'      => 4 // Private by default
		);
		if (!$result['created_by'])
		{
			$result['created_by'] = User::get('id');
		}
		$this->bind($result);
		if (!$this->check())
		{
			return false;
		}

		return $this->store(false);
	}

	/**
	 * Store changes
	 *
	 * @param   boolean  $check  Validate data?
	 * @return  boolean  True on success, False on error
	 */
	public function store($check=true)
	{
		if (!parent::store($check))
		{
			return false;
		}

		// Create an Item entry
		// This is because even collections can be reposted.
		// Thus, there needs to be an item entry to "repost"
		$item = new Tables\Item($this->_db);
		$item->loadType($this->get('id'), 'collection');
		if (!$item->get('id'))
		{
			$item->type        = 'collection';
			$item->object_id   = $this->get('id');
			$item->title       = $this->get('title');
			$item->description = $this->get('description');
			$item->access      = $this->get('access', 0);

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

		/*if (!$this->getError() && $this->get('state') == self::APP_STATE_DELETED)
		{
			foreach ($this->posts(array('access' => array(0, 1, 2, 4))) as $post)
			{
				$post->delete();
			}
		}*/

		return true;
	}

	/**
	 * Get the item entry for a collection
	 *
	 * @return  object
	 */
	public function item()
	{
		if (!($this->_item instanceof Item))
		{
			$item = new Tables\Item($this->_db);
			$item->loadType($this->get('id'), 'collection');
			if (!$item->get('id'))
			{
				$item->type        = 'collection';
				$item->object_id   = $this->get('id');
				$item->title       = $this->get('title');
				$item->description = $this->get('description');
				$item->access      = $this->get('access', 0);

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

			$this->_item = new Item($item);
		}
		return $this->_item;
	}

	/**
	 * Get a list of reposts
	 *
	 * [!] Not implemented yet
	 *
	 * @return  null
	 */
	public function reposts()
	{
		//$post = new Tables\Post($this->_db);
		//$post->loadByBoard($this->get('id'), $this->item()->get('id'));

		return null;
	}

	/**
	 * Set and get a specific post
	 *
	 * @param   integer $id Post ID
	 * @return  object  CollectionsModelPost
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
			if (isset($this->_posts) && $this->_posts instanceof ItemList)
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

			if (!$this->_post)
			{
				$this->_post = Post::getInstance($id);
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
	 * @param   array   $filters Filters to apply
	 * @param   boolean $clear   Clear cached data?
	 * @return  mixed   Integer or object
	 */
	public function posts($filters=array(), $clear=false)
	{
		if (!isset($filters['collection_id']))
		{
			$filters['collection_id'] = $this->get('id');
		}
		if (!isset($filters['state']))
		{
			$filters['state'] = self::APP_STATE_PUBLISHED;
		}
		if (!isset($filters['access']))
		{
			$filters['access'] = (User::isGuest() ? 0 : array(0, 1));
		}
		if (!isset($filters['sort']))
		{
			if ($sort = $this->get('sort', 'created'))
			{
				$filters['sort'] = 'p.' . $sort;
			}
			$filters['sort_Dir'] = ($this->get('sort', 'created') == 'ordering' ? 'asc' : 'desc');
		}

		if (isset($filters['count']) && $filters['count'])
		{
			$tbl = new Tables\Post($this->_db);

			return $tbl->getCount($filters);
		}

		if (!isset($this->_posts) || !($this->_posts instanceof ItemList))
		{
			$tbl = new Tables\Post($this->_db);

			if ($results = $tbl->getRecords($filters))
			{
				$ids = array();
				foreach ($results as $key => $result)
				{
					$ids[] = $result->item_id;
				}

				// Get all the assets for this list of items
				$ba = new Tables\Asset($this->_db);
				$assets = $ba->getRecords(array('item_id' => $ids));

				// Get all the tags for this list of items
				$bt = new Tags();
				$tags = $bt->getTagsForIds($ids);

				// Loop through all the items and push assets and tags
				foreach ($results as $key => $result)
				{
					$results[$key] = new Post($result);

					if ($assets)
					{
						foreach ($assets as $asset)
						{
							if ($asset->item_id == $results[$key]->get('item_id'))
							{
								$results[$key]->item()->addAsset($asset);
							}
							else
							{
								$results[$key]->item()->addAsset(null);
							}
						}
					}
					else
					{
						$results[$key]->item()->addAsset(null);
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

			$this->_posts = new ItemList($results);
		}

		return $this->_posts;
	}

	/**
	 * Get a count of data associated with this collection
	 *
	 * @param   string $what What to count
	 * @return  integer
	 */
	public function count($what='')
	{
		if (!isset($this->_counts) || !is_array($this->_counts))
		{
			$this->_counts = array();
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

			case 'followers':
				if (!isset($this->_counts[$what]))
				{
					$tbl = new Tables\Following($this->_db);
					$this->_counts[$what] = $tbl->count(array(
						'following_type' => 'collection',
						'following_id'   => $this->get('id')
					));
				}
				return $this->_counts[$what];
			break;

			case 'likes':
			case 'like':
			case 'votes':
			case 'vote':
				if ($this->get('likes', null) == null)
				{
					$tbl = new Tables\Item($this->_db);
					$this->set('likes', $tbl->getLikes(array(
						'object_type' => 'collection',
						'object_id'   => $this->get('id')
					)));
				}
				return (int) $this->get('likes', 0);
			break;

			case 'reposts':
			case 'repost':
				if ($this->get('reposts', null) == null)
				{
					$tbl = new Tables\Item($this->_db);
					$this->set('reposts', $tbl->getReposts(array(
						'object_type' => 'collection',
						'object_id'   => $this->get('id')
					)));
				}
				return (int) $this->get('reposts', 0);
			break;

			case 'posts':
			case 'post':
				if ($this->get('posts', null) == null)
				{
					$this->set('posts', $this->posts(array(
						'count' => true
					)));
				}
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
	 * @param   integer $follower_id   ID of the follower
	 * @param   string  $follower_type Type of the follower [member, group]
	 * @return  boolean
	 */
	public function isFollowing($follower_id=null, $follower_type='member')
	{
		if (!isset($this->_following))
		{
			$this->_following = false;

			if (!$follower_id && $follower_type == 'member')
			{
				$follower_id = User::get('id');
			}

			$follow = new Following($this->get('id'), 'collection', $follower_id, $follower_type);
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
	 * @param   integer $follower_id   ID of the follower
	 * @param   string  $follower_type Type of the follower [member, group]
	 * @return  boolean
	 */
	public function unfollow($follower_id=null, $follower_type='member')
	{
		$follow = new Following($this->get('id'), 'collection', $follower_id, $follower_type);

		if (!$follow->exists())
		{
			$this->setError(Lang::txt('Item is not being followed'));
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
	 * @param   integer  $follower_id    ID of the follower
	 * @param   string   $follower_type  Type of the follower [member, group]
	 * @return  boolean
	 */
	public function follow($follower_id=null, $follower_type='member')
	{
		$follow = new Following();
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

	/**
	 * Get the URL for this collection
	 *
	 * @return  string
	 */
	public function link()
	{
		return $this->_adapter()->build();
	}

	/**
	 * Get the content of the entry
	 *
	 * @param   string  $as      Format to return state in [text, number]
	 * @param   integer $shorten Number of characters to shorten text to
	 * @return  string
	 */
	public function description($as='parsed', $shorten=0)
	{
		$as = strtolower($as);
		$options = array();

		switch ($as)
		{
			case 'parsed':
				$content = $this->get('description.parsed', null);
				if ($content === null)
				{
					$config = array(
						'option'   => $this->get('option', Request::getCmd('option')),
						'scope'    => $this->get('scope', 'collection'),
						'pagename' => $this->get('alias'),
						'pageid'   => 0,
						'filepath' => $this->get('path', '/site/collections'),
						'domain'   => ''
					);

					$content = stripslashes((string) $this->get('description', ''));
					$this->importPlugin('content')->trigger('onContentPrepare', array(
						$this->_context,
						&$this,
						&$config
					));

					$this->set('description.parsed', (string) $this->get('description', ''));
					$this->set('description', $content);

					return $this->description($as, $shorten);
				}
				$options['html'] = true;
			break;

			case 'clean':
				$content = strip_tags($this->description('parsed'));
			break;

			case 'raw':
			default:
				$content = stripslashes($this->get('description'));
				$content = preg_replace('/^(<!-- \{FORMAT:.*\} -->)/i', '', $content);
			break;
		}

		if ($shorten)
		{
			$content = String::truncate($content, $shorten, $options);
		}
		return $content;
	}

	/**
	 * Return the adapter for this entry's scope,
	 * instantiating it if it doesn't already exist
	 *
	 * @return  object
	 */
	private function _adapter()
	{
		if (!$this->_adapter)
		{
			$scope = strtolower($this->get('object_type'));
			$cls = __NAMESPACE__ . '\\Adapters\\' . ucfirst($scope);

			if (!class_exists($cls))
			{
				$path = __DIR__ . '/adapters/' . $scope . '.php';
				if (!is_file($path))
				{
					throw new \InvalidArgumentException(Lang::txt('Invalid scope of "%s"', $scope));
				}
				include_once($path);
			}

			$this->_adapter = new $cls($this->get('object_id'));
			$this->_adapter->set('id', $this->get('id'));
			$this->_adapter->set('alias', $this->get('alias'));
		}
		return $this->_adapter;
	}

	/**
	 * Check if a specified user can access this item
	 *
	 * @param   integer  $user_id  User ID
	 * @return  boolean
	 */
	public function canAccess($user_id = null)
	{
		if (!$user_id)
		{
			$user_id = User::get('id');
		}

		// If registered
		if ($this->get('access') == 1)
		{
			return (User::isGuest() ? false : true);
		}

		// If private
		if ($this->get('access') == 4)
		{
			return $this->_adapter()->canAccess($user_id);
		}

		return true;
	}

	/**
	 * Transforms a namespace to an object
	 *
	 * @return  object   An an object holding the namespace data
	 */
	public function toObject()
	{
		$data = new \stdClass;

		$properties = $this->_tbl->getProperties();
		foreach ($properties as $key => $value)
		{
			if ($key && substr($key, 0, 1) != '_')
			{
				$data->$key = $this->get($key);
			}
		}

		$data->description = $this->description('clean');
		$data->posts       = $this->count('posts');
		$data->reposts     = $this->count('reposts');

		return $data;
	}
}
