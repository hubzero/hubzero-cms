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

require_once(JPATH_ROOT . DS . 'components' . DS . 'com_collections' . DS . 'tables' . DS . 'post.php');
require_once(JPATH_ROOT . DS . 'components' . DS . 'com_collections' . DS . 'models' . DS . 'item.php');

/**
 * Courses model class for a course
 */
class CollectionsModelPost extends \Hubzero\Base\Model
{
	/**
	 * Table class name
	 * 
	 * @var string
	 */
	protected $_tbl_name = 'CollectionsTablePost';

	/**
	 * Hubzero_User_Profile
	 * 
	 * @var object
	 */
	private $_creator = NULL;

	/**
	 * CollectionsModelAdapterAbstract
	 * 
	 * @var object
	 */
	private $_adapter = NULL;

	/**
	 * CollectionsModelPost
	 * 
	 * @var object
	 */
	private $_data = null;

	/**
	 * Constructor
	 * 
	 * @param      integer $id  Resource ID or alias
	 * @param      object  &$db JDatabase
	 * @return     void
	 */
	/*public function __construct($oid=null)
	{
		$this->_db = JFactory::getDBO();

		$this->_tbl = new CollectionsTablePost($this->_db);

		$item = null;

		if (is_numeric($oid) || is_string($oid))
		{
			if ($oid)
			{
				$this->_tbl->load($oid);
			}
		}
		else if (is_object($oid))
		{
			$this->_tbl->bind($oid);

			$item = new stdClass;

			$properties = get_object_vars($this->_tbl);
			foreach (get_object_vars($oid) as $key => $property)
			{
				if (substr($key, 0, strlen('item_')) == 'item_')
				{
					$nk = substr($key, strlen('item_'));
					$item->$nk = $property;
					continue;
				}
				if (!array_key_exists($key, $properties))
				{
					$this->_tbl->set($key, $property);
				}
			}
		}
		else if (is_array($oid))
		{
			$this->_tbl->bind($oid);

			$item = new stdClass;

			$properties = get_object_vars($this->_tbl);
			foreach (array_keys($oid) as $key)
			{
				if (substr($key, 0, strlen('item_')) == 'item_')
				{
					$nk = substr($key, strlen('item_'));
					$item->$nk = $oid[$key];
					continue;
				}
				if (!array_key_exists($key, $properties))
				{
					$this->_tbl->set($key, $oid[$key]);
				}
			}
		}

		//if (is_object($oid) && isset($oid->title))
		if (is_object($item))
		{
			$this->item($item);
			//$this->set('item_id', $this->item()->get('id'));
		}
	}*/

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
			$instances[$key] = new CollectionsModelPost($oid);
		}

		return $instances[$key];
	}

	/**
	 * Bind data to the model
	 * 
	 * @param      mixed $data Object or array
	 * @return     boolean True on success, False on error
	 */
	public function bind($data=null)
	{
		$item = new stdClass;

		if (is_object($data))
		{
			$res = $this->_tbl->bind($data);

			if ($res)
			{
				$properties = $this->_tbl->getProperties();
				foreach (get_object_vars($data) as $key => $property)
				{
					if (substr($key, 0, strlen('item_')) == 'item_')
					{
						$nk = substr($key, strlen('item_'));
						$item->$nk = $property;
						continue;
					}
					if (!array_key_exists($key, $properties))
					{
						$this->_tbl->set('__' . $key, $property);
					}
				}
			}
		}
		else if (is_array($data))
		{
			$res = $this->_tbl->bind($data);

			if ($res)
			{
				$properties = $this->_tbl->getProperties();
				foreach (array_keys($data) as $key)
				{
					if (substr($key, 0, strlen('item_')) == 'item_')
					{
						$nk = substr($key, strlen('item_'));
						$item->$nk = $oid[$key];
						continue;
					}
					if (!array_key_exists($key, $properties))
					{
						$this->_tbl->set('__' . $key, $data[$key]);
					}
				}
			}
		}
		else
		{
			$this->_logError(
				__CLASS__ . '::' . __FUNCTION__ . '(); ' . \JText::sprintf('Data must be of type object or array. Type given was %s', gettype($data))
			);
			throw new \InvalidArgumentException(\JText::sprintf('Data must be of type object or array. Type given was %s', gettype($data)));
		}

		$this->item($item);

		return $res;
	}

	/**
	 * Check if the resource exists
	 * 
	 * @param      mixed $idx Index value
	 * @return     array
	 */
	public function item($oid=null)
	{
		if (!isset($this->_data) || !($this->_data instanceof CollectionsModelItem))
		{
			if ($oid === null)
			{
				$oid = $this->get('item_id', 0);
			}

			$this->_data = new CollectionsModelItem($oid);
		}

		return $this->_data;
	}

	/**
	 * Check if the post is the original (first) post
	 * 
	 * @return     boolean True if original, false if not
	 */
	public function original()
	{
		if ((int) $this->get('original') > 0) 
		{
			return true;
		}
		return false;
	}

	/**
	 * Remove a post
	 * 
	 * @return     boolean True on success, false on error
	 */
	public function remove()
	{
		if ($this->original()) 
		{
			$this->setError(JText::_('Original posts must be deleted or moved.'));
			return false;
		}

		if (!$this->_tbl->delete($this->get('id'))) 
		{
			$this->setError($this->_tbl->getError());
			return false;
		}

		return true;
	}

	/**
	 * Move a post
	 * 
	 * @return     boolean True on success, false on error
	 */
	public function move($collection_id)
	{
		$collection_id = intval($collection_id);

		if (!$collection_id)
		{
			$this->setError(JText::_('Empty collection ID.'));
			return false;
		}

		$this->set('collection_id', $collection_id);

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
	public function creator($property=null)
	{
		if (!isset($this->_creator) || !is_object($this->_creator))
		{
			ximport('Hubzero_User_Profile');
			$this->_creator = Hubzero_User_Profile::getInstance($this->get('created_by'));
		}
		if ($property && $this->_creator instanceof Hubzero_User_Profile)
		{
			return $this->_creator->get($property);
		}
		return $this->_creator;
	}

	/**
	 * Get the URL for this group
	 *
	 * @param      string $type   The type of link to return
	 * @param      mixed  $params Optional string or associative array of params to append
	 * @return     string
	 */
	public function link($type='', $params=null)
	{
		return $this->_adapter()->build($type, $params);
	}

	/**
	 * Return the adapter for this entry's scope,
	 * instantiating it if it doesn't already exist
	 * 
	 * @return    object
	 */
	private function _adapter()
	{
		if (!$this->_adapter)
		{
			$scope = strtolower($this->get('object_type'));
			$cls = 'CollectionsModelAdapter' . ucfirst($scope);

			if (!class_exists($cls))
			{
				$path = dirname(__FILE__) . '/adapters/' . $scope . '.php';
				if (!is_file($path))
				{
					throw new \InvalidArgumentException(JText::sprintf('Invalid scope of "%s"', $scope));
				}
				include_once($path);
			}

			$this->_adapter = new $cls($this->get('object_id'));
			$this->_adapter->set('id', $this->get('id'));
			$this->_adapter->set('alias', $this->get('alias'));
		}
		return $this->_adapter;
	}
}

