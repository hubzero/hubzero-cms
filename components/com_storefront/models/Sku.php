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

include_once(JPATH_ROOT . DS . 'components' . DS . 'com_storefront' . DS . 'models' . DS . 'Memberships.php');

/**
 *
 * Storefront SKU class
 *
 */
class StorefrontModelSku
{

	var $data;
	var $db;

	/**
	 * Contructor
	 *
	 * @param  void
	 * @return void
	 */
	public function __construct()
	{
		$this->data = new stdClass();
		$this->db = JFactory::getDBO();
	}

	/**
	 * Set price
	 *
	 * @param	double		price
	 * @return	bool		true on success, exception otherwise
	 */
	public function setPrice($price)
	{
		if (!is_numeric($price))
		{
			throw new Exception(JText::_('Price must be numeric'));
		}

		$this->data->price = $price;
		return true;
	}

	public function getPrice()
	{
		return $this->data->price;
	}

	public function setName($skuName)
	{
		$this->data->name = $skuName;
		return true;
	}

	public function getName()
	{
		return $this->data->name;
	}

	public function setWeight($weight)
	{
		if (!is_numeric($weight))
		{
			throw new Exception(JText::_('Weight must be numeric'));
		}

		$this->data->weight = $weight;
		return true;
	}

	public function getWeight()
	{
		if (empty($this->data->weight))
		{
			return NULL;
		}
		return $this->data->weight;
	}


	/**
	 * Set ID
	 *
	 * @param	int		sku ID
	 * @return	bool	true on success, exception otherwise
	 */
	public function setId($sId)
	{
		$this->data->id = $sId;
		return true;
	}

	public function getId()
	{
		if (empty($this->data->id))
		{
			return NULL;
		}
		return $this->data->id;
	}

	public function setProductId($pId)
	{
		if (!is_numeric($pId))
		{
			throw new Exception(JText::_('Price must be numeric'));
		}
		$this->data->pId = $pId;
		return true;
	}

	public function getProductId()
	{
		if (empty($this->data->pId))
		{
			return NULL;
		}
		return $this->data->pId;
	}

	public function verify()
	{
		if (!isset($this->data->price) || !is_numeric($this->data->price))
		{
			throw new Exception(JText::_('No SKU price'));
		}
		if (!isset($this->data->pId) || !is_numeric($this->data->pId))
		{
			throw new Exception(JText::_('No SKU Product Set'));
		}
	}

	// TODO: Move saving logic here from warehouse
	public function save()
	{
		$this->verify();

		// Save options
		if (isset($this->data->options))
		{
			$sql = 'DELETE FROM `#__storefront_sku_options` WHERE `sId` = ' . $this->db->quote($this->getId());
			$this->db->setQuery($sql);
			$this->db->query();

			foreach ($this->data->options as $oId)
			{
				$sql = 'INSERT INTO `#__storefront_sku_options` (`sId`, `oId`)
						VALUES (' . $this->db->quote($this->getId()) . ', ' . $this->db->quote($oId) . ')';
				$this->db->setQuery($sql);
				$this->db->query();
			}
		}

		include_once(JPATH_ROOT . DS . 'components' . DS . 'com_storefront' . DS . 'models' . DS . 'Warehouse.php');
		$warehouse = new StorefrontModelWarehouse();

		return($warehouse->saveSku($this));
	}

	public function setAllowMultiple($allowMultiple)
	{
		if (!$allowMultiple)
		{
			$allowMultiple = 0;
		}
		else
		{
			$allowMultiple = 1;
		}
		return $this->data->allowMultiple = $allowMultiple;
	}

	public function getAllowMultiple()
	{
		if (!isset($this->data->allowMultiple))
		{
			return 'DEFAULT';
		}

		return $this->data->allowMultiple;
	}

	public function setTrackInventory($trackInventory)
	{
		if (!$trackInventory)
		{
			$trackInventory = 0;
		}
		else
		{
			$trackInventory = 1;
		}
		$this->data->trackInventory = $trackInventory;
	}

	public function getTrackInventory()
	{
		if (!isset($this->data->trackInventory))
		{
			return 'DEFAULT';
		}

		return $this->data->trackInventory;
	}

	public function setInventoryLevel($inventoryLevel)
	{
		if (!is_numeric($inventoryLevel))
		{
			throw new Exception(JText::_('Bad inventory level value'));
		}

		$this->data->inventoryLevel = $inventoryLevel;
	}

	public function getInventoryLevel()
	{
		if (!isset($this->data->inventoryLevel))
		{
			return 'DEFAULT';
		}

		return $this->data->inventoryLevel;
	}

	public function setEnumerable($enumerable)
	{
		if (!$enumerable)
		{
			$enumerable = 0;
		}
		else
		{
			$enumerable = 1;
		}
		$this->data->enumerable = $enumerable;
	}

	public function getEnumerable()
	{
		if (!isset($this->data->enumerable))
		{
			return 'DEFAULT';
		}

		return $this->data->enumerable;
	}

	/**
	 * Set SKU active status
	 *
	 * @param	bool		SKU status
	 * @return	bool		true
	 */
	public function setActiveStatus($activeStatus)
	{
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
	 * Get SKU active status
	 *
	 * @param	void
	 * @return	bool		SKU status
	 */
	public function getActiveStatus()
	{
		if (!isset($this->data->activeStatus))
		{
			return 'DEFAULT';
		}
		return $this->data->activeStatus;
	}

	public function addMeta($key, $val)
	{
		$this->data->meta[$key] = $val;
	}

	public function getMeta()
	{
		return $this->data->meta;
	}

	/*
	 * Set time to live
	 *
	 * @param	string		expected MySQL formatted interval values like 1 DAY, 2 MONTH, 3 YEAR
	 * @return	bool		SKU status
	*/
	public function setTimeToLive($ttl)
	{
		StorefrontModelMemberships::checkTtl($ttl);

		$this->data->meta['ttl'] = $ttl;
	}

	public function getTimeToLive()
	{
		if (isset($this->data->meta['ttl']))
		{
			return $this->data->meta['ttl'];
		}

		return false;
	}

	public function getOptions()
	{
		if (!isset($this->data->options))
		{
			$sql = 'SELECT oId';
			$sql .= ' FROM `#__storefront_sku_options` WHERE `sId` = ' . $this->db->quote($this->getId());
			$this->db->setQuery($sql);
			$this->data->options = $this->db->loadColumn();
		}
		return $this->data->options;
	}

	// Overwrites all options
	public function setOptions($options)
	{
		$this->data->options = $options;
	}

}