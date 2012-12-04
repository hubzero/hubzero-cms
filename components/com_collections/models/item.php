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

require_once(JPATH_ROOT . DS . 'components' . DS . 'com_collections' . DS . 'tables' . DS . 'item.php');
require_once(JPATH_ROOT . DS . 'components' . DS . 'com_collections' . DS . 'models' . DS . 'asset.php');
require_once(JPATH_ROOT . DS . 'components' . DS . 'com_collections' . DS . 'models' . DS . 'iterator.php');

/**
 * Courses model class for a course
 */
class CollectionsModelItem extends JObject
{
	/**
	 * CoursesTableInstance
	 * 
	 * @var object
	 */
	public $_tbl = NULL;

	/**
	 * CoursesTableInstance
	 * 
	 * @var object
	 */
	private $_creator = NULL;

	/**
	 * CoursesTableInstance
	 * 
	 * @var object
	 */
	private $_modifier = NULL;

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
	private $_assets = null;

	/**
	 * Container for properties
	 * 
	 * @var array
	 */
	private $_tags = null;

	/**
	 * Constructor
	 * 
	 * @param      integer $id  Resource ID or alias
	 * @param      object  &$db JDatabase
	 * @return     void
	 */
	public function __construct($oid)
	{
		$this->_db = JFactory::getDBO();

		$this->_tbl = new CollectionsTableItem($this->_db);

		if (is_numeric($oid) || is_string($oid))
		{
			$this->_tbl->load($oid);
		}
		else if (is_object($oid))
		{
			$this->_tbl->bind($oid);
			if (isset($oid->reposts))
			{
				$this->set('reposts', $oid->reposts);
			}
			if (isset($oid->comments))
			{
				$this->set('comments', $oid->comments);
			}
			if (isset($oid->voted))
			{
				$this->set('voted', $oid->voted);
			}
		}
		else if (is_array($oid))
		{
			$this->_tbl->bind($oid);
			if (isset($oid['reposts']))
			{
				$this->set('reposts', $oid['reposts']);
			}
			if (isset($oid['comments']))
			{
				$this->set('comments', $oid['comments']);
			}
			if (isset($oid['voted']))
			{
				$this->set('voted', $oid['voted']);
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
			$instances[$key] = new CollectionsModelItem($oid);
		}

		return $instances[$key];
	}

	/**
	 * Returns a property of the object or the default value if the property is not set.
	 *
	 * @access	public
	 * @param	string $property The name of the property
	 * @param	mixed  $default The default value
	 * @return	mixed The value of the property
	 * @see		getProperties()
	 * @since	1.5
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
	 * @access	public
	 * @param	string $property The name of the property
	 * @param	mixed  $value The value of the property to set
	 * @return	mixed Previous value of the property
	 * @see		setProperties()
	 * @since	1.5
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
			$this->_creator = JUser::getInstance($this->get('created_by'));
		}
		/*if ($property && is_a($this->_creator, 'JUser'))
		{
			return $this->_creator->get($property);
		}*/
		return $this->_creator;
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
	public function modifier($property=null)
	{
		if (!isset($this->_modifier) || !is_object($this->_modifier))
		{
			$this->_modifier = JUser::getInstance($this->get('modified_by'));
		}
		/*if ($property && is_a($this->_creator, 'JUser'))
		{
			return $this->_creator->get($property);
		}*/
		return $this->_modifier;
	}

	/**
	 * Check if the resource exists
	 * 
	 * @param      mixed $idx Index value
	 * @return     array
	 */
	public function assets($filters=array())
	{
		if (!isset($this->_assets) || !is_a($this->_assets, 'CollectionsModelIterator'))
		{
			$tbl = new CollectionsTableAsset($this->_db);

			$filters['item_id'] = $this->get('id');

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

			$this->_assets = new CollectionsModelIterator($results);
		}
		return $this->_assets;
	}

	/**
	 * Add an asset to the list
	 * 
	 * @param      object $asset
	 * @return     void
	 */
	public function addAsset($asset)
	{
		if (!isset($this->_assets) || !is_a($this->_assets, 'CollectionsModelIterator'))
		{
			$this->_assets = new CollectionsModelIterator(array());
		}
		if (!is_a($asset, 'CollectionsModelAsset'))
		{
			$asset = new CollectionsModelAsset($asset);
		}
		$this->_assets->add($asset);
	}

	/**
	 * Remove an asset from the list
	 * 
	 * @param      integer $asset
	 * @return     void
	 */
	public function removeAsset($asset)
	{
		// Remove the asset
		$tbl = new CollectionsTableAsset($this->_db);
		if (!$tbl->remove($asset))
		{
			$this->setError(JText::_('Failed to remove asset.'));
			return false;
		}

		// Reset the asset list so the next time assets 
		// are called, the list if fresh
		$this->_assets = null;

		return true;
	}

	/**
	 * Get tags on an item
	 * 
	 * @param      string $as How to return data
	 * @return     mixed Returns an array of tags by default
	 */
	public function tags($as='array')
	{
		if (!isset($this->_tags) || !is_array($this->_tags))
		{
			require_once(JPATH_ROOT . DS . 'components' . DS . 'com_collections' . DS . 'helpers' . DS . 'tags.php');

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

			case 'array':
			default:
				return $this->_tags;
			break;
		}
	}

	/**
	 * Add an asset to the list
	 * 
	 * @param      object $asset
	 * @return     void
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
	 * @return     boolean True on success, false on error
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
	}
}

