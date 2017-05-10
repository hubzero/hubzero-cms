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

namespace Components\Storefront\Models;

require_once(__DIR__ . DS . 'Memberships.php');
require_once(__DIR__ . DS . 'Product.php');
require_once(__DIR__ . DS . 'Option.php');
require_once(__DIR__ . DS . 'OptionGroup.php');
require_once(__DIR__ . DS . 'Warehouse.php');

/**
 *
 * Storefront SKU class
 *
 */
class Sku
{
	var $data;

	/**
	 * Contructor
	 *
	 * @param   int   $sId
	 * @return  void
	 */
	public function __construct($sId = false)
	{
		$this->data = new \stdClass();
		if (isset($sId) && is_numeric($sId) && $sId)
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
		$warehouse = new Warehouse();
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
			$this->setRestricted($skuInfo['info']->sRestricted);
			$this->setPublishTime($skuInfo['info']->publish_up, $skuInfo['info']->publish_down);
			$this->setCheckoutNotes($skuInfo['info']->sCheckoutNotes);
			$this->setCheckoutNotesRequired($skuInfo['info']->sCheckoutNotesRequired);

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
			throw new \Exception(Lang::txt('Error loading SKU'));
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
			//throw new \Exception(Lang::txt('Price must be numeric'));
		}

		$this->data->price = $price;
		return true;
	}

	public function getPrice()
	{
		if (empty($this->data->price))
		{
			return null;
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
			return null;
		}
		return $this->data->name;
	}

	public function setWeight($weight)
	{
		if (!is_numeric($weight))
		{
			throw new \Exception(Lang::txt('Weight must be numeric'));
		}

		$this->data->weight = $weight;
		return true;
	}

	public function getWeight()
	{
		if (empty($this->data->weight))
		{
			return null;
		}
		return $this->data->weight;
	}

	/**
	 * Set publishing times
	 *
	 * @param	string		publish up time
	 * @param	string		publish down time
	 * @return	bool		true
	 */
	public function setPublishTime($publishUp = '', $publishDown = '')
	{
		$this->data->publishTime = new \stdClass();
		if (empty($publishUp))
		{
			$publishUp = '0000-00-00 00:00:00';
		}
		$this->data->publishTime->publish_up = $publishUp;
		if (empty($publishDown))
		{
			$publishDown = '0000-00-00 00:00:00';
		}
		$this->data->publishTime->publish_down = $publishDown;

		return true;
	}

	/**
	 * Get publishing times
	 *
	 * @param	void
	 * @return	object
	 */
	public function getPublishTime()
	{
		if (empty($this->data->publishTime))
		{
			$this->setPublishTime();
		}
		return $this->data->publishTime;
	}

	public function setCheckoutNotes($checkoutNotes)
	{
		$this->data->checkoutNotes = $checkoutNotes;
		return true;
	}

	public function getCheckoutNotes()
	{
		if (empty($this->data->checkoutNotes))
		{
			return null;
		}
		return $this->data->checkoutNotes;
	}

	public function setCheckoutNotesRequired($checkoutNotesRequired)
	{
		if (!$checkoutNotesRequired)
		{
			$checkoutNotesRequired = 0;
		}
		else
		{
			$checkoutNotesRequired = 1;
		}
		$this->data->checkoutNotesRequired = $checkoutNotesRequired;
	}

	public function getCheckoutNotesRequired()
	{
		if (!isset($this->data->checkoutNotesRequired))
		{
			return 'DEFAULT';
		}

		return $this->data->checkoutNotesRequired;
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
			return null;
		}
		return $this->data->id;
	}

	public function setProductId($pId)
	{
		if (!is_numeric($pId))
		{
			throw new \Exception(Lang::txt('Price must be numeric'));
		}
		$this->data->pId = $pId;
		return true;
	}

	public function getProductId()
	{
		if (empty($this->data->pId))
		{
			return null;
		}
		return $this->data->pId;
	}

	public function verify()
	{
		if (!isset($this->data->price) || !is_numeric($this->data->price))
		{
			throw new \Exception(Lang::txt('No SKU price set'));
		}
		if (!isset($this->data->pId) || !is_numeric($this->data->pId))
		{
			throw new \Exception(Lang::txt('No SKU Product Set'));
		}

		// Verify that the SKU has all options set (one option from each option group assigned to the parent product)
		$product = new Product($this->getProductId());
		$productOptionGroups = $product->getOptionGroups();

		// Init the set options array()
		$optionGroupOptionsSet = array();
		// Init the flag whether the extra/useless options are set
		$extraOptionsSet = false;
		foreach ($this->getOptions() as $oId)
		{
			$option = new Option($oId);
			$optionGroupId = $option->getOptionGroupId();

			if (in_array($optionGroupId, $productOptionGroups))
			{
				$optionGroupOptionsSet[] = $optionGroupId;
			}
			else
			{
				// There are some options set that are from option groups not applied to this product
				// (most likely due to the removal of the option group from the product.) This should never happen.
				$extraOptionsSet = true;
			}
		}

		// At this point option groups options set must be the same as the product options groups, throw exception if not
		$missingOptions = array_diff($productOptionGroups, $optionGroupOptionsSet);
		if (!empty($missingOptions))
		{
			throw new \Exception(Lang::txt('Not all product options are set'));
		}

		if ($extraOptionsSet)
		{
			throw new \Exception(Lang::txt('Extra options are set'));
		}

		// Integrity check
		require_once(dirname(__DIR__) . DS . 'helpers' . DS . 'Integrity.php');
		$integrityCheck = \Integrity::skuIntegrityCheck($this);

		if ($integrityCheck->status != 'ok')
		{
			$errorMessage = "Integrity check error:";
			foreach ($integrityCheck->errors as $error)
			{
				$errorMessage .= '<br>' . $error;
			}
			throw new \Exception($errorMessage);
		}

		return true;
	}

	public function save()
	{
		$db = \App::get('db');
		$sId = $this->saveBase();

		// Do SKU meta (if any)
		$skuMeta = $this->getMeta();

		$activeMetaIds = array();

		if (!empty($skuMeta))
		{
			// Go through each meta key and insert/update it, remembering the affected ID
			foreach ($skuMeta as $k => $v)
			{
				$sql = "SET @skuMetaId := 0";
				$db->setQuery($sql);
				$db->query();

				$sql = "INSERT INTO `#__storefront_sku_meta` SET
						`sId` = " . $db->quote($sId) . ",
						`smKey` = " . $db->quote($k) . ",
						`smValue` = " . $db->quote($v) . "
						ON DUPLICATE KEY UPDATE
						`smId` = (@skuMetaId := `smId`),
						`sId` = " . $db->quote($sId) . ",
						`smKey` = " . $db->quote($k) . ",
						`smValue` = " . $db->quote($v);

				$db->setQuery($sql);
				$db->query();

				$sql = "SELECT IF(@skuMetaId = 0, LAST_INSERT_ID(), @skuMetaId)";
				$db->setQuery($sql);
				$db->query();

				$activeMetaIds[] = $db->loadResult();
			}
		}

		// Delete unused Meta info: everything not affected above
		$deleteSql = '(0';
		foreach ($activeMetaIds as $metaId)
		{
			$deleteSql .= ", " . $db->quote($metaId);
		}
		$deleteSql .= ')';

		$sql = "DELETE FROM `#__storefront_sku_meta` WHERE `sId` = " . $db->quote($sId) . " AND `smId` NOT IN {$deleteSql}";
		$db->setQuery($sql);
		$db->query();

		// Save options
		if (isset($this->data->options))
		{
			$sql = 'DELETE FROM `#__storefront_sku_options` WHERE `sId` = ' . $db->quote($this->getId());
			$db->setQuery($sql);
			$db->query();

			foreach ($this->data->options as $oId)
			{
				if ($oId && $oId > 0)
				{
					$sql = 'INSERT INTO `#__storefront_sku_options` (`sId`, `oId`)
						VALUES (' . $db->quote($this->getId()) . ', ' . $db->quote($oId) . ')';
					$db->setQuery($sql);
					$db->query();
				}
			}
		}
		return $sId;
	}

	private function saveBase()
	{
		$db = \App::get('db');

		if ($this->getActiveStatus() && $this->getActiveStatus() != 'DEFAULT')
		{
			// verify SKU if it gets published
			$this->verify();
		}

		// If no sId set -- this is a new SKU -- create a new record
		$sId = $this->getId();
		if (!$sId)
		{
			$sql = "INSERT INTO `#__storefront_skus` SET ";
		}
		// If sId is set -- update the existing SKU
		else
		{
			$sql = "UPDATE `#__storefront_skus` SET ";
		}

		$sql .= "	`pId` = " . $this->getProductId() . ",
					`sSku` = " . $db->quote($this->getName()) . ",
					`sPrice` = " . $db->quote($this->getPrice()) . ",
					`sAllowMultiple` = " . $this->getAllowMultiple() . ",
					`sTrackInventory` = " . $this->getTrackInventory() . ",
					`sInventory` = " . $this->getInventoryLevel() . ",
					`sEnumerable` = " . $this->getEnumerable() . ",
					`publish_up` = " . $db->quote($this->getPublishTime()->publish_up) . ",
					`publish_down` = " . $db->quote($this->getPublishTime()->publish_down) . ",
					`sRestricted` = " . $this->getRestricted() . ",
					`sCheckoutNotes` = " . $db->quote($this->getCheckoutNotes()) . ",
					`sCheckoutNotesRequired` = " . $this->getCheckoutNotesRequired() . ",
					`sActive` = " . $this->getActiveStatus();

		if (!empty($sId))
		{
			$sql .= " WHERE `sId` = " . $db->quote($sId);
		}

		$db->setQuery($sql);
		//print_r($db->toString());
		$db->query();
		if (empty($sId))
		{
			$sId = $db->insertid();
			$this->setId($sId);
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
		$db = \App::get('db');

		// Delete the SKU record
		$sql = 'DELETE FROM `#__storefront_skus` WHERE `sId` = ' . $db->quote($this->getId());
		$db->setQuery($sql);
		//print_r($db->replacePrefix($db->getQuery()));
		$db->query();

		// Delete the SKU-related files
		//	-- SKU image
		$config = Component::params('com_storefront');
		$imgWebPath = trim($config->get('imagesFolder', '/site/storefront/products'), DS);
		$dir = PATH_APP . DS . $imgWebPath . DS . $this->getProductId() . DS . $this->getId();

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
			$params = Component::params('com_storefront');
			$downloadFolder = $params->get('downloadFolder', '/site/protected/storefront/software');
			$dir = PATH_APP . $downloadFolder;
			$file = $dir . DS . $this->getMeta('downloadFile');

			if (file_exists($file))
			{
				// unlink($file);
			}
		}

		// Delete the SKU meta
		$sql = 'DELETE FROM `#__storefront_sku_meta` WHERE `sId` = ' . $db->quote($this->getId());
		$db->setQuery($sql);
		$db->query();

		// Delete the SKU options
		$sql = 'DELETE FROM `#__storefront_sku_options` WHERE `sId` = ' . $db->quote($this->getId());
		$db->setQuery($sql);
		$db->query();

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

	public function reserveInventory($qty)
	{
		if (!$this->getTrackInventory() || $this->getTrackInventory() == 'DEFAULT')
		{
			// no tracking
			return;
		}
		if (!is_numeric($qty))
		{
			throw new \Exception(Lang::txt('Bad inventory quantity value'));
		}

		if ($this->getInventoryLevel() < $qty)
		{
			throw new \Exception(Lang::txt('Cannot reserve more than current inventory'));
		}

		$this->setInventoryLevel($this->getInventoryLevel() - $qty);

		// Save the changes right away
		$this->saveBase();
	}

	public function releaseInventory($qty)
	{
		if (!$this->getTrackInventory() || $this->getTrackInventory() == 'DEFAULT')
		{
			// no tracking
			return;
		}
		if (!is_numeric($qty))
		{
			throw new \Exception(Lang::txt('Bad inventory quantity value'));
		}

		$this->setInventoryLevel($this->getInventoryLevel() + $qty);

		// Save the changes right away
		$this->saveBase();
	}

	public function setInventoryLevel($inventoryLevel)
	{
		if (!is_numeric($inventoryLevel) && $inventoryLevel != 'DEFAULT')
		{
			throw new \Exception(Lang::txt('Bad inventory level value'));
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

	public function unPublish()
	{
		$this->setActiveStatus(false);
		$this->save();
	}

	/**
	 * Get SKU active status
	 *
	 * @param	void
	 * @return	mixed		SKU status
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
	 * Set restricted flag
	 *
	 * @param	bool		SKU restricted flag
	 * @return	bool		true
	 */
	public function setRestricted($activeStatus)
	{
		if ($activeStatus)
		{
			$this->data->restricted = 1;
		}
		else
		{
			$this->data->restricted = 0;
		}
		return true;
	}

	/**
	 * Get restricted flag
	 *
	 * @param	void
	 * @return	bool	Restricted flag
	 */
	public function getRestricted()
	{
		if (!isset($this->data->restricted))
		{
			return 0;
		}
		return $this->data->restricted;
	}

	public function addMeta($key, $val)
	{
		$this->data->meta[$key] = $val;
	}

	public function getMeta($key = false)
	{
		if (!isset($this->data->meta))
		{
			return null;
		}
		if (!empty($key))
		{
			if (!empty($this->data->meta[$key]))
			{
				return $this->data->meta[$key];
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
		Memberships::checkTtl($ttl);
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
		$db = \App::get('db');

		if (!isset($this->data->options))
		{
			$sql = 'SELECT oId';
			$sql .= ' FROM `#__storefront_sku_options` WHERE `sId` = ' . $db->quote($this->getId());
			$db->setQuery($sql);
			$this->data->options = $db->loadColumn();
		}
		return $this->data->options;
	}

	// Overwrites all options
	public function setOptions($options)
	{
		$this->data->options = $options;
	}

	// Static ----------------------------

	public static function getInstance($sId)
	{
		$warehouse = new Warehouse();
		$skuInfo = $warehouse->getSkuInfo($sId);

		if (!$skuInfo)
		{
			return false;
		}

		$productType = $warehouse->getProductTypeInfo($skuInfo['info']->ptId)['ptName'];

		$sku = self::getProperInstance($productType, $sId);

		return($sku);
	}

	public static function newInstance($pId)
	{
		$warehouse = new Warehouse();
		$product = new Product($pId);
		$productType = $warehouse->getProductTypeInfo($product->getType())['ptName'];

		$sku = self::getProperInstance($productType);
		$sku->setProductId($pId);

		return($sku);
	}

	private static function getProperInstance($productType, $sId = false)
	{
		// Initialize the correct SKU based on the product type
		if ($productType && $productType == 'Software Download')
		{
			require_once(__DIR__ . DS . 'SoftwareSku.php');
			$sku = new SoftwareSku($sId);
		}
		elseif ($productType && $productType == 'Course')
		{
			require_once(__DIR__ . DS . 'CourseOffering.php');
			$sku = new CourseOffering();
		}
		else
		{
			$sku = new Sku($sId);
		}

		return($sku);
	}

	// Update all SKUs' references for a given product -- called when the product is updated to bring the SKUs up to speed
	public static function updateReferences($pId)
	{
		$product = new Product($pId);

		self::updateOptions($product);
	}

	private static function updateOptions($product)
	{
		$db = \App::get('db');

		$productOptionGroups = $product->getOptionGroups();
		$optionsSql = '(0';
		foreach ($productOptionGroups as $ogId)
		{
			$optionGroup = new OptionGroup($ogId);
			$optionGroupOptions = $optionGroup->getOptions();
			foreach ($optionGroupOptions as $option)
			{
				$optionsSql .= ", " . $db->quote($option->getId());
			}
		}
		$optionsSql .= ')';

		$productSkus = $product->getSkus();
		$skusSql = '(0';
		foreach ($productSkus as $sku)
		{
			$skusSql .= ", " . $db->quote($sku->getId());
		}
		$skusSql .= ')';

		$sql = "DELETE FROM `#__storefront_sku_options` WHERE `sId` IN {$skusSql} AND `oId` NOT IN {$optionsSql}";
		$db->setQuery($sql);
		$db->query();
	}
}
