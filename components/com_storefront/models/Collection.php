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
 * @author    Hubzero
 * @copyright Copyright 2005-2011 Purdue University. All rights reserved.
 * @license   http://www.gnu.org/licenses/lgpl-3.0.html LGPLv3
 */

// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die('Restricted access');

include_once(JPATH_ROOT . DS . 'components' . DS . 'com_storefront' . DS . 'models' . DS . 'Warehouse.php');
/**
 *
 * Storefront product class
 *
 */
class StorefrontModelCollection
{
	// Collection data container
	var $data;

	/**
	 * Constructor
	 *
	 * @param  void
	 * @return void
	 */
	public function __construct()
	{
		// Load language file
		JFactory::getLanguage()->load('com_storefront');

		$this->data = new stdClass();
	}

	/**
	 * Set collection type
	 *
	 * @param	string		Collection type
	 * @return	bool		true on success, exception otherwise
	 */
	public function setType($cType)
	{
		$allowedTypes = array('category', 'collection');

		if (!in_array($cType, $allowedTypes))
		{
			throw new Exception(JText::_('COM_STOREFRONT_INVALID_CATEGORY_TYPE'));
		}

		$this->data->type = $cType;
		return true;
	}

	/**
	 * Get collection type
	 *
	 * @param	void
	 * @return	int		Collection type
	 */
	public function getType()
	{
		return $this->data->type;
	}

	/**
	 * Set collection id (used to update collection or to create a collection with given ID)
	 *
	 * @param	int			collection ID
	 * @return	bool		true
	 */
	public function setId($cId)
	{
		$this->data->id = $cId;
		return true;
	}

	/**
	 * Get collection id (if set)
	 *
	 * @param	void
	 * @return	int		collection ID
	 */
	public function getId()
	{
		if (!empty($this->data->id))
		{
			return $this->data->id;
		}
		return false;
	}

	/**
	 * Set collection name
	 *
	 * @param	string		collection name
	 * @return	bool		true
	 */
	public function setName($cName)
	{
		$this->data->name = $cName;
		return true;
	}

	/**
	 * Get collection name
	 *
	 * @param	void
	 * @return	string		collection name
	 */
	public function getName()
	{
		return $this->data->name;
	}

	/**
	 * Set product collection status
	 *
	 * @param	bool		collection status
	 * @return	bool		true
	 */
	public function setActiveStatus($activeStatus)
	{
		// This is to accommodate admin's 'trashed' value
		// TODO redo it properly to allow trashing
		if ($activeStatus == 2)
		{
			$this->data->activeStatus = 0;
		}
		if ($activeStatus)
		{
			$this->data->activeStatus = 1;
		}
		else
		{
			$this->data->activeStatus = 0;
		}
		return true;
	}

	/**
	 * Get collection active status
	 *
	 * @param	void
	 * @return	bool		collection status
	 */
	public function getActiveStatus()
	{
		if (!isset($this->data->activeStatus))
		{
			return 'DEFAULT';
		}
		return $this->data->activeStatus;
	}

	/**
	 * Check if everything checks out and the collection is ready to go
	 *
	 * @param  void
	 * @return bool		true on success, throws exception on failure
	 */
	public function verify()
	{
		if (empty($this->data->name))
		{
			throw new Exception(JText::_('No collection name set'));
		}

		if (empty($this->data->type))
		{
			throw new Exception(JText::_('No collection type set'));
		}

		return true;
	}

	/**
	 * Add collection to the warehouse
	 *
	 * @param  void
	 * @return object	info
	 */
	public function add()
	{
		$this->verify();

		include_once(JPATH_ROOT . DS . 'components' . DS . 'com_storefront' . DS . 'models' . DS . 'Warehouse.php');
		$warehouse = new StorefrontModelWarehouse();

		return($warehouse->addCollection($this));
	}

	/**
	 * Update collection info
	 *
	 * @param  void
	 * @return object	info
	 */
	public function update()
	{
		include_once(JPATH_ROOT . DS . 'components' . DS . 'com_storefront' . DS . 'models' . DS . 'Warehouse.php');
		$warehouse = new StorefrontModelWarehouse();

		return($warehouse->updateCollection($this));
	}

}