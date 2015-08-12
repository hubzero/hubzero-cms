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
	private $db;

	/**
	 * Contructor
	 *
	 * @param  void
	 * @return void
	 */
	public function __construct($sId = false)
	{
		$this->data = new stdClass();
		$this->db = JFactory::getDBO();

		if (isset($sId) && is_numeric($sId))
		{
			$this->load($sId);
		}
	}

	/**
	 * Load existing SKU
	 *
	 * @param	int			SKU ID
	 * @return	bool		true on success, exception otherwise
	 */
	public function load($sId)
	{
		include_once(JPATH_ROOT . DS . 'components' . DS . 'com_storefront' . DS . 'models' . DS . 'Warehouse.php');
		$warehouse = new StorefrontModelWarehouse();
		$skuInfo = $warehouse->getSkuInfo($sId);
		//print_r($skuInfo); die;

		if ($skuInfo)
		{
			$this->setId($sId);
			$this->setProductId($skuInfo['info']->pId);
			$this->setName($skuInfo['info']->sSku);
			$this->setPrice($skuInfo['info']->sPrice);
			$this->setAllowMultiple($skuInfo['info']->sAllowMultiple);
			$this->setTrackInventory($skuInfo['info']->sTrackInventory);
			$this->setInventoryLevel($skuInfo['info']->sInventory);
			$this->setEnumerable($skuInfo['info']->sEnumerable);
			$this->setActiveStatus($skuInfo['info']->sActive);

			// Set meta
			if (!empty($skuInfo['meta']))
			{
				foreach ($skuInfo['meta'] as $key => $val)
				{
					$this->addMeta($key, $val);
				}
			}
		}
		else
		{
			throw new Exception(JText::_('Error loading SKU'));
		}
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
			$price = 0;
			//throw new Exception(JText::_('Price must be numeric'));
		}

		$this->data->price = $price;
		return true;
	}

	public function getPrice()
	{
		if (empty($this->data->price))
		{
			return NULL;
		}
		return $this->data->price;
	}

	public function setName($skuName)
	{
		$this->data->name = $skuName;
		return true;
	}

	public function getName()
	{
		if (empty($this->data->name))
		{
			return NULL;
		}
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
			throw new Exception(JText::_('No SKU price set'));
		}
		if (!isset($this->data->pId) || !is_numeric($this->data->pId))
		{
			throw new Exception(JText::_('No SKU Product Set'));
		}

		// Verify that the SKU has all options set (one option from each option group assigned to the parent product)
		include_once(JPATH_ROOT . DS . 'components' . DS . 'com_storefront' . DS . 'models' . DS . 'Product.php');
		$productOptionGroups = StorefrontModelProduct::optionGroups($this->getProductId());

		include_once(JPATH_ROOT . DS . 'components' . DS . 'com_storefront' . DS . 'models' . DS . 'Option.php');

		// Init the set options array()
		$optionGroupOptionsSet = array();
		foreach ($this->getOptions() as $oId)
		{
			$option = new StorefrontModelOption($oId);
			$optionGroupId = $option->getOptionGroupId();

			if (in_array($optionGroupId, $productOptionGroups))
			{
				$optionGroupOptionsSet[] = $optionGroupId;
			}
		}

		// At this point option groups options set must be the same as the product options groups, throw exception if not
		$missingOptions = array_diff($productOptionGroups, $optionGroupOptionsSet);
		if (!empty($missingOptions))
		{
			throw new Exception(JText::_('Not all product options are set'));
		}
	}

	// TODO: Move saving logic here from warehouse
	public function save()
	{
		$this->verify();

		include_once(JPATH_ROOT . DS . 'components' . DS . 'com_storefront' . DS . 'models' . DS . 'Warehouse.php');
		$warehouse = new StorefrontModelWarehouse();

		$sId = $warehouse->saveSku($this);
		$this->setId($sId);

		// Save options
		if (isset($this->data->options))
		{
			$sql = 'DELETE FROM `#__storefront_sku_options` WHERE `sId` = ' . $this->db->quote($this->getId());
			$this->db->setQuery($sql);
			$this->db->query();

			foreach ($this->data->options as $oId)
			{
				if ($oId && $oId > 0)
				{
					$sql = 'INSERT INTO `#__storefront_sku_options` (`sId`, `oId`)
						VALUES (' . $this->db->quote($this->getId()) . ', ' . $this->db->quote($oId) . ')';
					$this->db->setQuery($sql);
					$this->db->query();
				}
			}
		}

		return $sId;
	}

	/**
	 * Delete a SKU and everything related to it
	 *
	 * @param	void
	 * @return	bool	true on success, exception otherwise
	 */
	public function delete()
	{
		$this->verify();

		// Delete the SKU record
		$sql = 'DELETE FROM `#__storefront_skus` WHERE `sId` = ' . $this->db->quote($this->getId());
		$this->db->setQuery($sql);
		//print_r($this->db->replacePrefix($this->db->getQuery()));
		$this->db->query();

		// Delete the SKU-related files
		//	-- SKU image
		$imgWebPath = DS . 'site' . DS . 'storefront' . DS . 'products' . DS . $this->getProductId() . DS . $this->getId();
		$dir = JPATH_ROOT . $imgWebPath;

		if (file_exists($dir))
		{
			$it = new RecursiveDirectoryIterator($dir, RecursiveDirectoryIterator::SKIP_DOTS);
			$files = new RecursiveIteratorIterator($it,
				RecursiveIteratorIterator::CHILD_FIRST);
			foreach ($files as $file)
			{
				if ($file->isDir())
				{
					rmdir($file->getRealPath());
				}
				else
				{
					unlink($file->getRealPath());
				}
			}
			rmdir($dir);
		}

		//	-- SKU downloadFile (?)
		if ($this->getMeta('downloadFile'))
		{
			// Path and file name
			$dir = JPATH_ROOT . DS . 'media' . DS . 'software';
			$file = $dir . DS . $this->getMeta('downloadFile');

			if (file_exists($file))
			{
				// unlink($file);
			}
		}

		// Delete the SKU meta
		$sql = 'DELETE FROM `#__storefront_sku_meta` WHERE `sId` = ' . $this->db->quote($this->getId());
		$this->db->setQuery($sql);
		$this->db->query();

		// Delete the SKU options
		$sql = 'DELETE FROM `#__storefront_sku_options` WHERE `sId` = ' . $this->db->quote($this->getId());
		$this->db->setQuery($sql);
		$this->db->query();

		// TODO Check if the parent product has any SKUs left and mark it unpublished if needed (?)

		return true;
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
		if (!is_numeric($inventoryLevel) && $inventoryLevel != 'DEFAULT')
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

	public function getMeta($key = false)
	{
		if (!isset($this->data->meta))
		{
			return NULL;
		}
		if (!empty($key))
		{
			if (!empty($this->data->$key))
			{
				return $this->data->key;
			}
			return false;
		}
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