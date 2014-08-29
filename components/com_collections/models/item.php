<?php
/**
 * HUBzero CMS
 *
 * Copyright 2005-2013 Purdue University. All rights reserved.
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
 * @copyright Copyright 2005-2013 Purdue University. All rights reserved.
 * @license   http://www.gnu.org/licenses/lgpl-3.0.html LGPLv3
 */

// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die('Restricted access');

require_once(JPATH_ROOT . DS . 'components' . DS . 'com_collections' . DS . 'tables' . DS . 'item.php');
require_once(JPATH_ROOT . DS . 'components' . DS . 'com_collections' . DS . 'models' . DS . 'asset.php');
require_once(JPATH_ROOT . DS . 'components' . DS . 'com_collections' . DS . 'helpers' . DS . 'tags.php');

/**
 * Collections model for an item
 */
class CollectionsModelItem extends CollectionsModelAbstract
{
	/**
	 * Table class name
	 *
	 * @var strong
	 */
	protected $_tbl_name = 'CollectionsTableItem';

	/**
	 * Model context
	 *
	 * @var string
	 */
	protected $_context = 'com_collections.item.description';

	/**
	 * CoursesTableInstance
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
	 * @param   mixed $oid Integer, object, or array
	 * @return  void
	 */
	public function __construct($oid=null)
	{
		$this->_db = JFactory::getDBO();

		$this->_tbl = new CollectionsTableItem($this->_db);

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
	 * @param   mixed $oid Integer or string
	 * @return  object CollectionsModelItem
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
	 * @param   string $property The name of the property
	 * @param   mixed  $default The default value
	 * @return  mixed  The value of the property
 	 */
	public function get($property, $default=null)
	{
		switch (strtolower($property))
		{
			case 'reposts':
				if (!isset($this->_tbl->{'__' . $property}))
				{
					$this->set($property, $this->_tbl->getReposts());
				}
			break;
			case 'voted':
				if (!isset($this->_tbl->{'__' . $property}))
				{
					$this->set($property, $this->_tbl->getVote());
				}
			break;
			case 'comments':
				if (!isset($this->_tbl->{'__' . $property}))
				{
					$this->set($property, $this->comments('count'));
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
	 * @param   string $as What format to return
	 * @return  string
	 */
	public function modified($as='')
	{
		switch (strtolower($as))
		{
			case 'date':
				return JHTML::_('date', $this->get('modified'), JText::_('DATE_FORMAT_HZ1'));
			break;

			case 'time':
				return JHTML::_('date', $this->get('modified'), JText::_('TIME_FORMAT_HZ1'));
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
	 * @param   string $property Property to retrieve
	 * @param   mixed  $default  Default value if property not set
	 * @return  mixed
	 */
	public function modifier($property=null, $default=null)
	{
		if (!($this->_modifier instanceof \Hubzero\User\Profile))
		{
			$this->_modifier = \Hubzero\User\Profile::getInstance($this->get('modified_by'));
			if (!$this->_modifier)
			{
				$this->_modifier = new \Hubzero\User\Profile();
			}
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
					$tbl = new \Hubzero\Item\Comment($this->_db);
					$this->_cache['comments.count'] = $tbl->count($filters);
				}
				return $this->_cache['comments.count'];
			break;

			case 'list':
			case 'results':
			default:
				if (!is_array($this->_cache['comments.list']))
				{
					$tbl = new \Hubzero\Item\Comment($this->_db);

					if (!($results = $tbl->getComments('collection', $this->get('id'))))
					{
						$results = array();
					}

					$this->_cache['comments.list'] = $results;
				}
				return $this->_cache['comments.list'];
			break;
		}
	}

	/**
	 * Get a count or a list of the assets on this entry
	 *
	 * @param   array $filters Filters to apply to data fetch
	 * @return  mixed
	 */
	public function assets($filters=array())
	{
		if (!isset($this->_assets) || !($this->_assets instanceof \Hubzero\Base\ItemList))
		{
			$tbl = new CollectionsTableAsset($this->_db);

			if (!isset($filters['item_id']))
			{
				$filters['item_id'] = $this->exists() ? $this->get('id') : 0;
			}

			if (($results = $tbl->getRecords($filters)))
			{
				foreach ($results as $key => $result)
				{
					$results[$key] = new CollectionsModelAsset($result);
				}
			}
			else
			{
				$results = array();
			}

			$this->_assets = new \Hubzero\Base\ItemList($results);
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
		if (!isset($this->_assets) || !($this->_assets instanceof \Hubzero\Base\ItemList))
		{
			$this->_assets = new \Hubzero\Base\ItemList(array());
		}
		if ($asset)
		{
			if (!($asset instanceof CollectionsModelAsset))
			{
				$asset = new CollectionsModelAsset($asset);
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
		if ($asset instanceof CollectionsModelAsset)
		{
			if (!$asset->remove())
			{
				$this->setError(JText::_('Failed to remove asset.'));
				return false;
			}
		}
		else
		{
			$tbl = new CollectionsTableAsset($this->_db);
			if (!$tbl->remove($asset))
			{
				$this->setError(JText::_('Failed to remove asset.'));
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
	public function tags($as='array')
	{
		if (!isset($this->_tags) || !is_array($this->_tags))
		{
			$ids = array(
				$this->get('id')
			);

			$bt = new CollectionsTags($this->_db);
			if (($tags = $bt->getTagsForIds($ids)))
			{
				$results = isset($tags[$this->get('id')]) ? $tags[$this->get('id')] : array();
			}
			else
			{
				$results = array();
			}
			$this->_tags = $results;
		}
		switch (strtolower(trim($as)))
		{
			case 'string':
				$tags = array();
				foreach ($this->_tags as $tag)
				{
					$tags[] = $tag->raw_tag;
				}
				return implode(', ', $tags);
			break;

			case 'html':
			case 'render':
				$bt = new CollectionsTags($this->_db);
				return $bt->buildCloud($this->_tags);
			break;

			case 'array':
			default:
				return $this->_tags;
			break;
		}
	}

	/**
	 * Add a tag to the list
	 *
	 * @param   object $tag
	 * @return  void
	 */
	public function addTag($tag=null)
	{
		if (!isset($this->_tags) || !is_array($this->_tags))
		{
			$this->_tags = array();
		}
		if (is_array($tag))
		{
			foreach ($tag as $t)
			{
				$this->_tags[] = $t;
			}
		}
		else if ($tag !== null)
		{
			$this->_tags[] = $tag;
		}
	}

	/**
	 * Vote for this item
	 *
	 * @return  boolean True on success, false on error
	 */
	public function vote()
	{
		require_once(JPATH_ROOT . DS . 'components' . DS . 'com_collections' . DS . 'tables' . DS . 'vote.php');

		$juser = JFactory::getUser();

		$vote = new CollectionsTableVote($this->_db);
		$vote->loadByBulletin($this->get('id'), $juser->get('id'));

		$like = true;

		if (!$vote->id)
		{
			$vote->user_id = $juser->get('id');
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
					$this->setError(JText::_('Error occurred while saving vote'));
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

				$a = new CollectionsModelAsset($asset['id']);
				$a->set('type', $asset['type']);
				$a->set('item_id', $this->get('id'));
				$a->set('ordering', $k);
				$a->set('filename', $asset['filename']);
				if (strtolower($a->get('filename')) == 'http://')
				{
					if (!$a->remove())
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
			$config = JComponentHelper::getParams('com_collections');

			// Build the upload path if it doesn't exist
			$path = JPATH_ROOT . DS . trim($config->get('filepath', '/site/collections'), DS) . DS . $this->get('id');

			if (!is_dir($path))
			{
				jimport('joomla.filesystem.folder');
				if (!JFolder::create($path))
				{
					$this->setError(JText::_('Error uploading. Unable to create path.'));
					return false;
				}
			}

			$descriptions = $this->get('_descriptions', array());

			if (isset($files['name']))
			{
				foreach ($files['name'] as $i => $file)
				{
					// Make the filename safe
					jimport('joomla.filesystem.file');
					$files['name'][$i] = urldecode($files['name'][$i]);
					$files['name'][$i] = JFile::makeSafe($files['name'][$i]);
					$files['name'][$i] = str_replace(' ', '_', $files['name'][$i]);

					// Upload new files
					if (!JFile::upload($files['tmp_name'][$i], $path . DS . $files['name'][$i]))
					{
						$this->setError(JText::_('ERROR_UPLOADING') . ': ' . $files['name'][$i]);
					}
					// File was uploaded
					else
					{
						$asset = new CollectionsModelAsset();
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
		require_once(JPATH_ROOT . DS . 'components' . DS . 'com_collections' . DS . 'helpers' . DS . 'tags.php');

		$bt = new CollectionsTags($this->_db);
		$bt->tag_object($this->get('created_by'), $this->get('id'), $this->get('_tags', ''), 1, 1);

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
						'option'   => $this->get('option', JRequest::getCmd('option', 'com_collections')),
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
			$content = \Hubzero\Utility\String::truncate($content, $shorten);
		}
		return $content;
	}

	/**
	 * Get the item type
	 *
	 * @return  string
	 */
	public function type()
	{
		if ($this->get('state') == 2)
		{
			$this->set('type', 'deleted');
		}

		$type = $this->get('type');
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
			$filters['access'] = (!JFactory::getUser()->get('guest') ? array(0, 1) : 0);
		}

		switch (strtolower($what))
		{
			case 'count':
				if (!isset($this->_cache['collections.count']) || $clear)
				{
					$tbl = new CollectionsTableCollection($this->_db);
					$this->_cache['collections.count'] = $tbl->getCount($filters);
				}
				return $this->_cache['collections.count'];
			break;

			case 'list':
			case 'results':
			default:
				if (!($this->_cache['collections.list'] instanceof \Hubzero\Base\ItemList) || $clear)
				{
					$tbl = new CollectionsTableCollection($this->_db);

					if ($results = $tbl->getRecords($filters))
					{
						foreach ($results as $key => $result)
						{
							$results[$key] = new CollectionsModelCollection($result);
						}
					}
					else
					{
						$results = array();
					}
					$this->_cache['collections.list'] = new \Hubzero\Base\ItemList($results);
				}
				return $this->_cache['collections.list'];
			break;
		}
	}
}

