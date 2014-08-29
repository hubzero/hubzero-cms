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

require_once(JPATH_ROOT . DS . 'components' . DS . 'com_collections' . DS . 'tables' . DS . 'asset.php');
require_once(JPATH_ROOT . DS . 'components' . DS . 'com_collections' . DS . 'models' . DS . 'abstract.php');

/**
 * Collections model class for an Asset
 */
class CollectionsModelAsset extends CollectionsModelAbstract
{
	/**
	 * Table class name
	 *
	 * @var string
	 */
	public $_tbl_name = 'CollectionsTableAsset';

	/**
	 * Constructor
	 *
	 * @param   mixed   $oid     ID, string, array, or object
	 * @param   integer $item_id ID of the item asset is attached
	 * @return  void
	 */
	public function __construct($oid=null, $item_id=null)
	{
		$this->_db = JFactory::getDBO();

		$this->_tbl = new CollectionsTableAsset($this->_db);

		if (is_numeric($oid) || is_string($oid))
		{
			$this->_tbl->load($oid, $item_id);
		}
		else if (is_object($oid) || is_array($oid))
		{
			$this->bind($oid);
		}
	}

	/**
	 * Returns a reference to an asset object
	 *
	 * @param   mixed   $oid     ID, string, array, or object
	 * @param   integer $item_id ID of the item asset is attached
	 * @return  object  CollectionsModelAsset
	 */
	static function &getInstance($oid=null, $item_id=null)
	{
		static $instances;

		if (!isset($instances))
		{
			$instances = array();
		}

		if (is_numeric($oid) || is_string($oid))
		{
			$key = $oid . '_' . $item_id;
		}
		else if (is_object($oid))
		{
			$key = $oid->id . '_' . $item_id;
		}
		else if (is_array($oid))
		{
			$key = $oid['id'] . '_' . $item_id;
		}

		if (!isset($instances[$key]))
		{
			$instances[$key] = new self($oid, $item_id);
		}

		return $instances[$key];
	}

	/**
	 * Is an asset an image?
	 *
	 * @return  boolean True if image, false if not
	 */
	public function image()
	{
		jimport('joomla.filesystem.file');
		$ext = strtolower(JFile::getExt($this->get('filename')));

		if (in_array($ext, array('jpg', 'jpe', 'jpeg', 'gif', 'png')))
		{
			return true;
		}

		return false;
	}

	/**
	 * Remove a record
	 *
	 * @return  boolean True on success, false if errors
	 */
	public function remove()
	{
		if (!$this->_tbl->remove($this->get('id')))
		{
			$this->setError($this->_tbl->getError());
			return false;
		}
		return true;
	}

	/**
	 * Update content
	 *
	 * @param   string $field  Field name
	 * @param   string $before Old value
	 * @param   string $after  New value
	 * @return  boolean True on success, false if errors
	 */
	public function update($field, $before, $after)
	{
		if (!$this->_tbl->updateField($field, $before, $after))
		{
			$this->setError($this->_tbl->getError());
			return false;
		}

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
		if ($this->get('_file'))
		{
			$config = JComponentHelper::getParams('com_collections');

			$path = JPATH_ROOT . DS . trim($config->get('filepath', '/site/collections'), DS) . DS . $this->get('item_id');

			if (!is_dir($path))
			{
				jimport('joomla.filesystem.folder');
				if (!JFolder::create($path))
				{
					$this->setError(JText::_('Error uploading. Unable to create path.'));
					return false;
				}
			}

			$file = $this->get('_file');

			// Make the filename safe
			jimport('joomla.filesystem.file');
			$file['name'] = urldecode($files['name']);
			$file['name'] = JFile::makeSafe($file['name']);
			$file['name'] = str_replace(' ', '_', $file['name']);

			// Upload new files
			if (!JFile::upload($file['tmp_name'], $path . DS . $file['name']))
			{
				$this->setError(JText::_('ERROR_UPLOADING') . ': ' . $file['name']);
				return false;
			}

			$this->set('filename', $file['name']);
		}

		return parent::store($check);
	}

	/**
	 * Update ordering
	 *
	 * @param   integer $item_id ITem ID
	 * @return  boolean True on success, false if errors
	 */
	public function reorder($item_id=0)
	{
		if (!$item_id)
		{
			$item_id = $this->get('item_id');
		}
		return $this->_tbl->reorder("item_id=" . $this->_db->Quote(intval($item_id)));
	}
}

