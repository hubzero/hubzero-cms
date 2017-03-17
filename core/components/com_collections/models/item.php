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

use Components\Members\Models\Member;
use Components\Collections\Tables;
use Hubzero\Item\Comment;
use Hubzero\Base\ItemList;
use Hubzero\Utility\String;
use Filesystem;
use Request;
use Date;
use User;
use Lang;

require_once \Component::path('com_members') . DS . 'models' . DS . 'member.php';
require_once(dirname(__DIR__) . DS . 'tables' . DS . 'item.php');
require_once(__DIR__ . DS . 'asset.php');
require_once(__DIR__ . DS . 'tags.php');

/**
 * Collections model for an item
 */
class Item extends Base
{
	/**
	 * Item type
	 *
	 * @var  string
	 */
	protected $_type = 'file';

	/**
	 * Table class name
	 *
	 * @var strong
	 */
	protected $_tbl_name = '\\Components\\Collections\\Tables\\Item';

	/**
	 * Model context
	 *
	 * @var string
	 */
	protected $_context = 'com_collections.item.description';

	/**
	 * Modifier
	 *
	 * @var object
	 */
	private $_modifier = NULL;

	/**
	 * Container for properties
	 *
	 * @var array
	 */
	private $_assets = null;

	/**
	 * Tag Cloud model
	 *
	 * @var object
	 */
	private $_tags = null;

	/**
	 * Container for properties
	 *
	 * @var array
	 */
	private $_cache = array(
		'comments.count'    => null,
		'comments.list'     => null,
		'collections.count' => null,
		'collections.list'  => null
	);

	/**
	 * Constructor
	 *
	 * @param   mixed  $oid  Integer, object, or array
	 * @return  void
	 */
	public function __construct($oid=null)
	{
		$this->_db = \App::get('db');

		$tbl = $this->_tbl_name;
		$this->_tbl = new $tbl($this->_db);

		if ($oid)
		{
			if (is_numeric($oid) || is_string($oid))
			{
				if (substr($oid, 0, 3) == 'tmp')
				{
					$this->_tbl->loadByTitle($oid);
				}
				else
				{
					$this->_tbl->load($oid);
				}
			}
			else if (is_object($oid) || is_array($oid))
			{
				$this->bind($oid);
			}
		}
	}

	/**
	 * Returns a reference to a collections item instance
	 *
	 * @param   mixed   $oid  Integer or string
	 * @return  object
	 */
	static function &getInstance($oid=null)
	{
		static $instances;

		if (!isset($instances))
		{
			$instances = array();
		}

		if (is_numeric($oid) || is_string($oid))
		{
			$key = $oid;
		}
		else if (is_object($oid))
		{
			$key = $oid->id;
		}
		else if (is_array($oid))
		{
			$key = $oid['id'];
		}

		if (!isset($instances[$key]))
		{
			$instances[$key] = new self($oid);
		}

		return $instances[$key];
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
		switch (strtolower($property))
		{
			case 'reposts':
				if (!isset($this->_tbl->{'__' . $property}))
				{
					$this->set($property, intval($this->_tbl->getReposts()));
				}
			break;
			case 'voted':
				if (!isset($this->_tbl->{'__' . $property}))
				{
					$this->set($property, intval($this->_tbl->getVote()));
				}
			break;
			case 'comments':
				if (!isset($this->_tbl->{'__' . $property}))
				{
					$this->set($property, intval($this->comments('count')));
				}
			break;
			default:
			break;
		}
		return parent::get($property, $default);
	}

	/**
	 * Return a formatted timestamp
	 *
	 * @param   string  $as  What format to return
	 * @return  string
	 */
	public function modified($as='')
	{
		switch (strtolower($as))
		{
			case 'date':
				return Date::of($this->get('modified'))->toLocal(Lang::txt('DATE_FORMAT_HZ1'));
			break;

			case 'time':
				return Date::of($this->get('modified'))->toLocal(Lang::txt('TIME_FORMAT_HZ1'));
			break;

			default:
				return $this->get('modified');
			break;
		}
	}

	/**
	 * Get the modifier of this entry
	 *
	 * Accepts an optional property name. If provided
	 * it will return that property value. Otherwise,
	 * it returns the entire user object
	 *
	 * @param   string  $property  Property to retrieve
	 * @param   mixed   $default   Default value if property not set
	 * @return  mixed
	 */
	public function modifier($property=null, $default=null)
	{
		if (!($this->_modifier instanceof Member))
		{
			$this->_modifier = Member::oneOrNew($this->get('modified_by'));
		}
		if ($property)
		{
			$property = ($property == 'id' ? 'uidNumber' : $property);
			return $this->_modifier->get($property, $default);
		}
		return $this->_modifier;
	}

	/**
	 * Get a count or list of the comments on an item
	 *
	 * @param   string  $rtrn    Data format to return
	 * @param   array   $filters Filters to apply to data fetch
	 * @param   boolean $clear   Clear cached data?
	 * @return  mixed
	 */
	public function comments($what='list', $filters=array(), $clear=false)
	{
		if (!isset($filters['item_id']))
		{
			$filters['item_id'] = $this->get('id');
		}
		if (!isset($filters['item_type']))
		{
			$filters['item_type'] = 'collection';
		}
		if (!isset($filters['state']))
		{
			$filters['state'] = array(1, 3);
		}

		switch (strtolower(trim($what)))
		{
			case 'count':
				if ($this->_cache['comments.count'] === null)
				{
					$total = Comment::all()
						->whereEquals('item_type', $filters['item_type'])
						->whereEquals('item_id', $this->get('id'))
						->whereIn('state', $filters['state'])
						->ordered()
						->total();

					$this->_cache['comments.count'] = $total;
				}
				return $this->_cache['comments.count'];
			break;

			case 'list':
			case 'results':
			default:
				if (!is_array($this->_cache['comments.list']))
				{
					$results = Comment::all()
						->whereEquals('item_type', $filters['item_type'])
						->whereEquals('item_id', $this->get('id'))
						->whereIn('state', $filters['state'])
						->ordered()
						->rows();

					$this->_cache['comments.list'] = $results;
				}
				return $this->_cache['comments.list'];
			break;
		}
	}

	/**
	 * Get a count or a list of the assets on this entry
	 *
	 * @param   array    $filters  Filters to apply to data fetch
	 * @param   boolean  $clear    Reset internal cahce?
	 * @return  mixed
	 */
	public function assets($filters=array(), $reset = false)
	{
		if (!($this->_assets instanceof ItemList) || $reset)
		{
			$tbl = new Tables\Asset($this->_db);

			if (!isset($filters['item_id']))
			{
				$filters['item_id'] = $this->exists() ? $this->get('id') : 0;
			}

			if ($results = $tbl->getRecords($filters))
			{
				foreach ($results as $key => $result)
				{
					$results[$key] = new Asset($result);
				}
			}
			else
			{
				$results = array();
			}

			$this->_assets = new ItemList($results);
		}
		return $this->_assets;
	}

	/**
	 * Add an asset to the list
	 *
	 * @param      object $asset
	 * @return     void
	 */
	public function addAsset($asset=null)
	{
		if (!isset($this->_assets) || !($this->_assets instanceof ItemList))
		{
			$this->_assets = new ItemList(array());
		}
		if ($asset)
		{
			if (!($asset instanceof Asset))
			{
				$asset = new Asset($asset);
			}
			$this->_assets->add($asset);
		}
	}

	/**
	 * Remove an asset
	 *
	 * @param   integer $asset
	 * @return  boolean
	 */
	public function removeAsset($asset)
	{
		// Remove the asset
		if ($asset instanceof Asset)
		{
			if (!$asset->remove())
			{
				$this->setError(Lang::txt('Failed to remove asset.'));
				return false;
			}
		}
		else
		{
			$tbl = new Tables\Asset($this->_db);
			if (!$tbl->remove($asset))
			{
				$this->setError(Lang::txt('Failed to remove asset.'));
				return false;
			}
		}

		// Reset the asset list so the next time assets
		// are called, the list if fresh
		$this->_assets = null;

		return true;
	}

	/**
	 * Get tags on an item
	 *
	 * @param   string $as How to return data
	 * @return  mixed Returns an array of tags by default
	 */
	public function tags($as='array', $admin=0)
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

		if (!isset($this->_tags))
		{
			$this->_tags = new Tags($this->get('id'));
		}

		return $this->_tags->render($as, array('admin' => $admin));
	}

		/**
	 * Tag the entry
	 *
	 * @return  boolean
	 */
	public function tag($tags=null, $user_id=0, $admin=0)
	{
		if (!isset($this->_tags))
		{
			$this->_tags = new Tags($this->get('id'));
		}
		$user_id = $user_id ?: User::get('id');

		return $this->_tags->setTags($tags, $user_id, $admin);
	}

	/**
	 * Add a tag to the list
	 *
	 * @param   object $tag
	 * @return  void
	 */
	public function addTag($tag=null, $user_id=0, $admin=0)
	{
		if (!isset($this->_tags))
		{
			$this->_tags = new Tags($this->get('id'));
		}
		//$user_id = $user_id ?: User::get('id');

		//return $this->_tags->add($tag, $user_id, $admin);
		return $this->_tags->append($tag);
	}

	/**
	 * Vote for this item
	 *
	 * @return  boolean True on success, false on error
	 */
	public function vote()
	{
		require_once(dirname(__DIR__) . DS . 'tables' . DS . 'vote.php');

		$vote = new Tables\Vote($this->_db);
		$vote->loadByBulletin($this->get('id'), User::get('id'));

		$like = true;

		if (!$vote->id)
		{
			$vote->user_id = User::get('id');
			$vote->item_id = $this->get('id');
			// Store the record
			if (!$vote->check())
			{
				$this->setError($vote->getError());
				return false;
			}
			else
			{
				if (!$vote->store())
				{
					$this->setError(Lang::txt('Error occurred while saving vote'));
					return false;
				}
			}
		}
		else
		{
			$like = false;
			// Load the vote record
			if (!$vote->delete())
			{
				$this->setError($vote->getError());
				return false;
			}
		}

		if ($like)
		{
			// Increase like count
			$this->set('positive', ($this->get('positive') + 1));
		}
		else if ($this->get('positive') > 0) // Make sure we don't go below 0
		{
			// Decrease like count
			$this->set('positive', ($this->get('positive') - 1));
		}
		$this->_tbl->store();

		return true;
	}

	/**
	 * Store content
	 * Can be passed a boolean to turn off check() method
	 *
	 * @param   boolean $check Call check() method?
	 * @return  boolean True on success, false if errors
	 */
	public function store($check=true)
	{
		if (!parent::store($check))
		{
			return false;
		}

		if (!$this->get('id'))
		{
			if (!$this->_tbl->id)
			{
				$this->_tbl->id = $this->_tbl->_db->insertid();
			}
			$this->set('id', $this->_tbl->id);
		}

		if ($this->get('_assets'))
		{
			$k = 0;

			foreach ($this->get('_assets') as $i => $asset)
			{
				$k++;

				$a = new Asset($asset['id']);
				$a->set('type', $asset['type']);
				$a->set('item_id', $this->get('id'));
				$a->set('ordering', $k);
				$a->set('filename', $asset['filename']);
				if (strtolower($a->get('filename')) == 'http://')
				{
					if ($a->get('id') && !$a->remove())
					{
						$this->setError($a->getError());
					}
				}
				else if (!$a->store())
				{
					$this->setError($a->getError());
				}
			}
			$a->reorder();
		}

		if ($files = $this->get('_files'))
		{
			$config = Component::params('com_collections');

			// Build the upload path if it doesn't exist
			$path = $this->filespace() . DS . $this->get('id');

			if (!is_dir($path))
			{
				if (!Filesystem::makeDirectory($path))
				{
					$this->setError(Lang::txt('Error uploading. Unable to create path.'));
					return false;
				}
			}

			$descriptions = $this->get('_descriptions', array());

			if (isset($files['name']))
			{
				foreach ($files['name'] as $i => $file)
				{
					// Make the filename safe
					$files['name'][$i] = urldecode($files['name'][$i]);
					$files['name'][$i] = Filesystem::clean($files['name'][$i]);
					$files['name'][$i] = str_replace(' ', '_', $files['name'][$i]);

					// Upload new files
					if (!Filesystem::upload($files['tmp_name'][$i], $path . DS . $files['name'][$i]))
					{
						$this->setError(Lang::txt('ERROR_UPLOADING') . ': ' . $files['name'][$i]);
					}
					// File was uploaded
					else
					{
						$asset = new Asset();
						//$asset->set('_file', $file);
						$asset->set('item_id', $this->get('id'));
						$asset->set('filename', $files['name'][$i]);
						$asset->set('description', (isset($descriptions[$i]) ? $descriptions[$i] : ''));
						if (!$asset->store())
						{
							$this->setError($asset->getError());
						}
					}
				}
			}

			if ($this->getError())
			{
				return false;
			}
		}

		$trashed = $this->assets(array('state' => self::APP_STATE_DELETED));
		if ($trashed->total() > 0)
		{
			foreach ($trashed as $trash)
			{
				if ($trash->get('filename') == 'http://')
				{
					$trash->remove();
				}
			}
		}

		// Process tags
		if ($this->get('_tags', null) !== null)
		{
			$this->tag($this->get('_tags', ''), $this->get('created_by'));
		}

		return true;
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
						'option'   => $this->get('option', Request::getCmd('option', 'com_collections')),
						'scope'    => 'collections',
						'pagename' => 'collections',
						'pageid'   => 0,
						'filepath' => '',
						'domain'   => 'collection'
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
			$content = String::truncate($content, $shorten);
		}
		return $content;
	}

	/**
	 * Get the item type
	 *
	 * @param   string  $as  Return type as?
	 * @return  string
	 */
	public function is($type)
	{
		if ($type == $this->_type)
		{
			return true;
		}
		return false;
	}

	/**
	 * Get the item type
	 *
	 * @param   string  $as  Return type as?
	 * @return  string
	 */
	public function type($as=null)
	{
		static $collectibles;

		if ($this->get('state') == 2)
		{
			$this->set('type', 'deleted');
		}

		$type = $this->get('type');

		if ($as == 'title')
		{
			if (!isset($collectibles))
			{
				// Include the avilable collectibles
				foreach (glob(__DIR__ . DS . 'item' . DS . '*.php') as $collectible)
				{
					require_once $collectible;
				}

				// Filter available classes to just our collectibles
				$collectibles = array_values(array_filter(get_declared_classes(), function($class)
				{
					return (in_array('Components\\Collectionss\\Models\\Item', class_parents($class)));
				}));
			}

			// Find a collectible that responds to this type
			foreach ($collectibles as $key => $collectible)
			{
				if (!is_object($collectible))
				{
					$collectibles[$key] = new $collectible;
					$collectible = $collectibles[$key];
				}
				if ($collectible->is($type))
				{
					return $collectible->type($as);
				}
			}
		}

		if (!in_array($type, array('collection', 'deleted', 'image', 'file', 'text', 'link')))
		{
			$type = 'link';
		}
		return $type;
	}

	/**
	 * Get a counr or list of collections this item can be found in
	 *
	 * @param   string  $what    What to return?
	 * @param   array   $filters Filters to apply
	 * @param   boolean $clear   Clear cached results?
	 * @return  mixed
	 */
	public function collections($what='list', $filters=array(), $clear=false)
	{
		if (!isset($filters['item_id']))
		{
			$filters['item_id'] = $this->get('id');
		}
		if (!isset($filters['state']))
		{
			$filters['state'] = self::APP_STATE_PUBLISHED;
		}
		if (!isset($filters['access']))
		{
			$filters['access'] = (!User::isGuest() ? array(0, 1) : 0);
		}

		switch (strtolower($what))
		{
			case 'count':
				if (!isset($this->_cache['collections.count']) || $clear)
				{
					$tbl = new Tables\Collection($this->_db);
					$this->_cache['collections.count'] = $tbl->getCount($filters);
				}
				return $this->_cache['collections.count'];
			break;

			case 'list':
			case 'results':
			default:
				if (!($this->_cache['collections.list'] instanceof ItemList) || $clear)
				{
					$tbl = new Tables\Collection($this->_db);

					if ($results = $tbl->getRecords($filters))
					{
						foreach ($results as $key => $result)
						{
							$results[$key] = new Collection($result);
						}
					}
					else
					{
						$results = array();
					}
					$this->_cache['collections.list'] = new ItemList($results);
				}
				return $this->_cache['collections.list'];
			break;
		}
	}

	/**
	 * Chck if we're on a URL where an item can be collected
	 *
	 * @return  boolean
	 */
	public function canCollect()
	{
		if (Request::getCmd('option') != 'com_collections')
		{
			return false;
		}

		if (!Request::getInt('post', 0))
		{
			return false;
		}

		return true;
	}

	/**
	 * Create an item entry
	 *
	 * @param   integer  $id  Optional ID to use
	 * @return  boolean
	 */
	public function make($id=null)
	{
		if ($this->exists())
		{
			return true;
		}

		$id = ($id ?: Request::getInt('post', 0));

		if ($id)
		{
			require_once(dirname(__DIR__) . DS . 'tables' . DS . 'post.php');

			$post = new Tables\Post($this->_db);
			$post->load($id);

			if (!$this->_tbl->load($post->item_id))
			{
				$this->setError($this->_tbl->getError());
				return false;
			}
		}

		if ($this->exists())
		{
			return true;
		}

		$this->set('type', 'file')
		     ->set('object_id', 0);

		if (!$this->store())
		{
			return false;
		}

		return true;
	}
}

