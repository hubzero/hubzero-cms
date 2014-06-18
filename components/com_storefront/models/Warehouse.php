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
 * @author    Ilya Shunko <ishunko@purdue.edu>
 * @copyright Copyright 2005-2011 Purdue University. All rights reserved.
 * @license   http://www.gnu.org/licenses/lgpl-3.0.html LGPLv3
 */

// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die('Restricted access');

include_once(JPATH_ROOT . DS . 'components' . DS . 'com_storefront' . DS . 'models' . DS . 'Product.php');
include_once(JPATH_ROOT . DS . 'components' . DS . 'com_storefront' . DS . 'models' . DS . 'Course.php');
include_once(JPATH_ROOT . DS . 'components' . DS . 'com_storefront' . DS . 'models' . DS . 'CourseOffering.php');
include_once(JPATH_ROOT . DS . 'components' . DS . 'com_storefront' . DS . 'models' . DS . 'Sku.php');
include_once(JPATH_ROOT . DS . 'components' . DS . 'com_storefront' . DS . 'models' . DS . 'Coupon.php');

/**
 *
 * Products inventory and structure (only product lookup and inventory management)
 *
 */
class StorefrontModelWarehouse
{
	/**
	 * array Product categories to look at (to define scope)
	 */
	var $lookupCollections		= NULL;

	// Database instance
	var $db = NULL;

	/**
	 * Contructor method
	 *
	 * @param  void
	 * @return void
	 */
	public function __construct()
	{
		$this->_db = JFactory::getDBO();

		// Load language file
		JFactory::getLanguage()->load('com_storefront');
	}


	/* ------------------------------------- Instance config functions ----------------------------------------------- */


	/**
	 * Add a collection to the instance lookup scope
	 *
	 * @param  $cId Collection ID
	 * @return void
	 */
	public function addLookupCollection($cId)
	{
		$this->lookupCollections[] = $cId;
	}

	/**
	 * Raset instance lookup scope
	 *
	 * @param  void
	 * @return void
	 */
	public function resetLookupCollections()
	{
		$this->lookupCollections[] = NULL;
	}


	/* ------------------------------------- Main working functions ----------------------------------------------- */


	/**
	 * Get all non-empty (those that have at least one active product) root (no parents) product category collections
	 *
	 * @param  void
	 * @return void
	 */
	public function getRootCategories()
	{
		return $this->_getCollections('category');
	}

	/**
	 * Check if collection exists
	 *
	 * @param  $c -- collection ID (+ alias in the future)
	 * @return int cId on sucess, false if no match found
	 */
	public function collectionExists($c)
	{
		$sql = "SELECT cId FROM `#__storefront_collections` c WHERE c.`cId` = " . $this->_db->quote($c) . " AND c.`cActive` = 1";

		$this->_db->setQuery($sql);
		//echo $this->_db->_sql; die;
		$cId = $this->_db->loadResult();

		return $cId;
	}

	/**
	 * Check if product exists
	 *
	 * @param  	int		product ID (+ alias in the future)
	 * @return 	int 	pId on sucess, null if no match found
	 */
	public function productExists($pId, $showInactive = false)
	{
		$sql = "SELECT pId FROM `#__storefront_products` p WHERE p.`pId` = '{$pId}'";
		if (!$showInactive)
		{
			$sql .= " AND p.`pActive` = 1";
		}

		$this->_db->setQuery($sql);
		$cId = $this->_db->loadResult();

		return $cId;
	}

	/**
	 * Check if coupon exists
	 *
	 * @param  	string		coupon code
	 * @return 	int 		coupon ID on sucess, null if no match found
	 */
	public function couponExists($code)
	{
		$sql = "SELECT cnId FROM `#__storefront_coupons` WHERE `cnCode` = " . $this->_db->quote($code);

		$this->_db->setQuery($sql);
		$cnId = $this->_db->loadResult();

		return $cnId;
	}

	/**
	 * Get the products within the given scope
	 *
	 * @param  void
	 * @return void
	 */
	public function getProducts()
	{
		$sql = "SELECT DISTINCT p.* FROM `#__storefront_products` p JOIN `#__storefront_product_collections` c ON p.`pId` = c.`pId`";
		$sql .= " WHERE p.`pActive` = 1";

		foreach	($this->lookupCollections as $cId)
		{
			$sql .= " AND c.`cId` = " . $this->_db->quote($cId);
		}

		$this->_db->setQuery($sql);
		//echo $this->_db->_sql; die;
		$products = $this->_db->loadObjectList();

		return $products;
	}

	/**
	 * Get product inforamtion
	 *
	 * @param	int			Product ID
	 * @param  	bool		Flag whether to show inactive product info
	 * @return 	object		Product info
	 */
	public function getProductInfo($pId, $showInactive = false)
	{
		$sql = "SELECT p.* FROM `#__storefront_products` p WHERE p.`pId` = {$pId}";

		if (!$showInactive)
		{
			$sql .= " AND `pActive` = 1";
		}

		$this->_db->setQuery($sql);
		$product = $this->_db->loadObject();

		return $product;
	}

	/**
	 * Get product option groups and options
	 *
	 * @param  $pId Product ID
	 * @return array
	 */
	public function getProductOptions($pId)
	{
		// Get all SKUs' options and option groups
		$sql  = "	SELECT
					s.`sId` AS skuId, so.`oId` AS skusOptionId, s.`sPrice`, s.`sAllowMultiple`, s.`sInventory`, s.`sTrackInventory`, og.`ogId`, `oName`, `ogName`";
		$sql .= "	FROM `#__storefront_skus` s
					LEFT JOIN `#__storefront_sku_options` so ON s.`sId` = so.`sId`
					LEFT JOIN `#__storefront_options` o ON so.`oId` = o.`oId`
					LEFT JOIN `#__storefront_option_groups` og ON o.`ogId` = og.`ogId`

					WHERE s.`pId` = {$pId} AND s.`sActive` = 1 AND (s.`sInventory` > 0 OR s.`sTrackInventory` = 0)

					ORDER BY og.`ogId`";

		$this->_db->setQuery($sql);
		//print_r($this->_db->_sql); die;
		$this->_db->query();
		if (!$this->_db->getNumRows())
		{
			return false;
		}

		$res = $this->_db->loadObjectList();

		// Initialize array for option groups
		$options = array();
		// Array for SKUs
		$skus = array();

		// Parse result and populate $options with option groups and corresponding options
		$currentOgId = false;
		foreach ($res as $line)
		{
			// Populate options
			if ($line->ogId)
			{
				// Keep track of option groups and do not do anything if no options
				if ($currentOgId != $line->ogId)
				{
					$currentOgId = $line->ogId;

					$ogInfo->ogId = $line->ogId;
					$ogInfo->ogName = $line->ogName;

					$options[$currentOgId]['info'] = $ogInfo;
					unset($ogInfo);
				}

				$oInfo->oId = $line->skusOptionId;
				$oInfo->oName = $line->oName;
				$options[$currentOgId]['options'][$line->skusOptionId] = $oInfo;
				unset($oInfo);
			}

			// populate SKUs for JS

			$skusInfo->sId = $line->skuId;
			//$skusInfo->sId = $line->skusOptionId;
			$skusInfo->sPrice = $line->sPrice;
			$skusInfo->sAllowMultiple = $line->sAllowMultiple;
			$skusInfo->sTrackInventory = $line->sTrackInventory;
			$skusInfo->sInventory = $line->sInventory;

			$skus[$line->skuId]['info'] = $skusInfo;
			$skus[$line->skuId]['options'][] = $line->skusOptionId;
			unset($skusInfo);

		}

		$ret->options = $options;
		$ret->skus = $skus;

		//print_r($ret); die;

		return $ret;
	}

	/**
	 * Get SKU mapping to the provided options
	 *
	 * @param $pId Product ID
	 * @param $options Selected options (optional for products with no options)
	 * @return SKU ID
	 */
	public function mapSku($pId, $options)
	{
		// Find the number of options required for this product
		$sql = "SELECT COUNT(pog.`ogId`) FROM `#__storefront_product_option_groups` pog WHERE pog.`pId` = '{$pId}'";
		$this->_db->setQuery($sql);
		$totalOptionsRequired = $this->_db->loadResult();

		if ($totalOptionsRequired > count($options))
		{
			throw new Exception(JText::_('COM_STOREFRONT_NOT_ENOUGH_OPTIONS'));
		}

		// find if there is a SKU match
		if (!empty($options))
		{
			$skuOptionsSql = '(0';
			foreach ($options as $oId)
			{
				$skuOptionsSql .= " OR so.`oId` = '{$oId}'";
			}
			$skuOptionsSql .= ')';
		}

		$sql = "SELECT s.`sId`, COUNT(so.`oId`) AS matches FROM `#__storefront_skus` s
				LEFT JOIN `#__storefront_sku_options` so ON s.`sId` = so.`sId`
				WHERE s.`pId` = '{$pId}'";
		if (!empty($options))
		{
			$sql .= " AND {$skuOptionsSql}";
		}
		$sql .= " GROUP BY s.`sId` HAVING matches = {$totalOptionsRequired}";

		$this->_db->setQuery($sql);
		$sId = $this->_db->loadObject();

		if ($sId)
		{
			return $sId->sId;
		}

		// no match
		throw new Exception(JText::_('COM_STOREFRONT_SKU_NOT_FOUND'));
	}

	/**
	 * Get all info for SKUs provided, including options
	 *
	 * @param 	array 		SKU IDs
	 * @param 	bool 		Flag whether to show inactive SKU and product info
	 * @return 	array 		info
	 */
	public function getSkusInfo($skus, $showInactive = false)
	{
		$sqlIn = '(0';
		foreach ($skus as $sId)
		{
			$sqlIn .= ', ' . $sId;
		}
		$sqlIn .= ')';

		// Get only results for exixting SKUs and exixting products associated with each SKU
		$sql = "SELECT p.*, s.*, o.`oId`, o.`oName`, m.`smKey`, m.`smValue` FROM `#__storefront_skus` s
				LEFT JOIN `#__storefront_products` p ON s.`pId` = p.`pId`
				LEFT JOIN `#__storefront_sku_options` so ON so.`sId` = s.`sId`
				LEFT JOIN `#__storefront_options` o ON o.`oId` = so.`oId`
				LEFT JOIN `#__storefront_sku_meta` m ON s.`sId` = m.`sId`
				WHERE
				s.`sId` IN {$sqlIn}
				AND p.`pId` IS NOT NULL ";
		if (!$showInactive)
		{
			$sql .= "
				AND p.`pActive` = 1
				AND s.`sActive` = 1 ";
		}
		$sql .= "
				ORDER BY s.`sId`";

		$this->_db->setQuery($sql);
		// echo $this->_db->_sql; die;
		$rawSkusInfo = $this->_db->loadObjectList();

		/*
			Parse the result and organize it by SKU (since same SKU can be returned several times, depending on the number of optinos):

			$skusInfo => Array(
				[sId] => Array(
					[info] => Object
					[options] => Array(
						[oId] => Option name
						...
					)
				)
			)
		*/

		$skusInfo = array();
		$skuoptions = array();
		$skuMeta = array();
		$currentSku = false;
		foreach ($rawSkusInfo as $row)
		{
			if ($currentSku != $row->sId)
			{
				$skusInfo[$row->sId]['info'] = $row;
				if ($currentSku && !empty($skuoptions))
				{
					$skusInfo[$currentSku]['options'] = $skuoptions;
				}
				if ($currentSku && !empty($skuMeta))
				{
					$skusInfo[$currentSku]['meta'] = $skuMeta;
				}
				$currentSku = $row->sId;
				$skuoptions = array();
				$skuMeta = array();
			}

			if (!empty($row->oName))
			{
				$skuoptions[$row->oId] = $row->oName;
			}

			if (!empty($row->smKey) && !empty($row->smValue))
			{
				$skuMeta[$row->smKey] = $row->smValue;
			}
		}

		if (!empty($skuoptions))
		{
			$skusInfo[$currentSku]['options'] = $skuoptions;
		}
		if (!empty($skuMeta))
		{
			$skusInfo[$currentSku]['meta'] = $skuMeta;
		}

		//print_r($skusInfo); die();
		return $skusInfo;
	}

	/**
	 * Get all info for SKU provided, including options
	 *
	 * @param 	int 		SKU ID
	 * @return 	array 		info
	 */
	public function getSkuInfo($sId)
	{
		$info = $this->getSkusInfo(array($sId), true);
		return($info[$sId]);
	}

	/**
	 * TODO: delete
	 * Get basic info for a single SKU provided
	 *
	 * @param SKU ID
	 * @return object
	 */
	public function _DELETE_getSkuInfo($sId)
	{
		$sql = "SELECT s.`sAllowMultiple`, s.`sInventory`, s.`sTrackInventory`, s.`sPrice` FROM `#__storefront_skus` s WHERE s.`sId` = {$sId}";
		$this->_db->setQuery($sql);
		$skuInventoryInfo = $this->_db->loadObject();

		if ($skuInventoryInfo)
		{
			return $skuInventoryInfo;
		}
		return false;
	}

	/**
	 * Get product SKU IDs
	 *
	 * @param	int			product id
	 * @return	array 		collections
	 */
	public function getProductSkus($pId)
	{
		$sql = "SELECT `sId` FROM `#__storefront_skus` WHERE `sActive` = 1 AND `pId` = " . $this->_db->quote($pId);
		$this->_db->setQuery($sql);
		$res = $this->_db->loadResultArray();

		return $res;
	}

	/**
	 * Update SKU inventory
	 *
	 * @param int SKU ID
	 * @param qty quantity
	 * @param string method: 'add' -- add to existing qty, 'subtract' -- subtract from existing qty, 'update' -- update existing qty
	 * @return void
	 */
	public function updateInventory($sId, $qty, $method = 'update')
	{
		if ($method == 'add')
		{
			$sql = "UPDATE `#__storefront_skus` s SET s.`sInventory` = s.`sInventory` + {$qty}
					WHERE s.`sId` = {$sId} AND s.`sTrackInventory` = 1";
		}
		elseif ($method == 'subtract')
		{
			$sql = "UPDATE `#__storefront_skus` s SET s.`sInventory` = s.`sInventory` - {$qty}
					WHERE s.`sId` = {$sId} AND s.`sTrackInventory` = 1";
		}
		elseif ($method == 'update')
		{
			$sql = "UPDATE `#__storefront_skus` s SET s.`sInventory` = {$qty}
					WHERE s.`sId` = {$sId} AND s.`sTrackInventory` = 1";
		}
		else
		{
			return false;
		}
		$this->_db->setQuery($sql);
		$this->_db->query();
	}

	/**
	 * Get product type info
	 *
	 * @param	int					product type ID
	 * @return	array 				info
	 */
	public function getProductTypeInfo($ptId)
	{
		$sql = "SELECT * FROM `#__storefront_product_types` WHERE `ptId` = " . $this->_db->quote($ptId);

		$this->_db->setQuery($sql);
		$res = $this->_db->loadAssoc();
		return $res;
	}

	/**
	 * Get a course
	 *
	 * @param	int							course ID
	 * @return	StorefrontModelCourse 	Instance of a product
	 */
	public function getCourse($courseId)
	{
		$course = $this->getProduct($courseId, 'course');
		return $course;
	}

	/**
	 * Get a product
	 *
	 * @param	int							product ID
	 * @param	string						product type
	 * @return	StorefrontModelProduct 	Instance of a product
	 */
	public function getProduct($pId, $productType = 'product')
	{
		$allowedProductTypes = array('product', 'course');

		if (!in_array($productType, $allowedProductTypes))
		{
			throw new Exception(JText::_('Bad product type.'));
		}

		// Create a StorefrontModelProduct

		if ($productType == 'product')
		{
			$product = new StorefrontModelProduct();
		}
		elseif ($productType == 'course')
		{
			$product = new StorefrontModelCourse();
		}

		// Get all product info
		$productInfo = $this->getProductInfo($pId, true);

		$product->setType($productInfo->ptId);
		$product->setId($productInfo->pId);
		$product->setName($productInfo->pName);
		$product->setDescription($productInfo->pDescription);
		$product->setTagline($productInfo->pTagline);
		$product->setActiveStatus($productInfo->pActive);

		// Get collections
		$collections = $this->_getProductCollections($pId);
		foreach ($collections as $cId)
		{
			$product->addToCollection($cId);
		}

		// Get SKUs
		$skus = $this->getProductSkus($pId);

		// Add SKUs to product
		foreach ($skus as $sId)
		{
			$sku = $this->getSku($sId, $productType);
			$product->addSku($sku);
		}

		$product->verify();

		return $product;
	}

	/**
	 * Get course by alias
	 *
	 * @param	int							course ID
	 * @return	StorefrontModelCourse 	Instance of a product
	 */
	public function getCourseByAlias($alias)
	{
		$sql = "SELECT s.`sId` FROM `#__storefront_skus` s
				LEFT JOIN `#__storefront_sku_meta` m ON s.`sId` = m.`sId`
				LEFT JOIN `#__storefront_products` p ON s.`pId` = p.`pId`
				WHERE p.`ptId` = 20
				AND `smKey` = 'courseId'
				AND `smValue` = " . $this->_db->quote($alias);

		$this->_db->setQuery($sql);
		//echo $this->_db->_sql;
		$this->_db->query();
		return($this->_db->loadResult());
	}

	/**
	 * Get a SKU
	 *
	 * @param	int							SKU ID
	 * @param	string						product type
	 * @return	StorefrontModelSku 		Instance of a SKU
	 */
	public function getSku($sId, $productType = 'product')
	{
		if ($productType == 'product')
		{
			$sku = new StorefrontModelSku();
		}
		elseif ($productType == 'course')
		{
			$sku = new StorefrontModelCourseOffering();
		}

		$skuInfo = $this->getSkuInfo($sId);
		//print_r($skuInfo); die;

		$sku->setId($sId);
		$sku->setPrice($skuInfo['info']->sPrice);
		$sku->setAllowMultiple($skuInfo['info']->sAllowMultiple);
		$sku->setTrackInventory($skuInfo['info']->sTrackInventory);
		$sku->setInventoryLevel($skuInfo['info']->sInventory);
		$sku->setEnumerable($skuInfo['info']->sEnumerable);
		$sku->setActiveStatus($skuInfo['info']->sActive);

		// Set meta
		if (!empty($skuInfo['meta']))
		{
			foreach ($skuInfo['meta'] as $key => $val)
			{
				if ($productType == 'course' && $key == 'courseId')
				{
					$sku->setCourseId($val);
				}

				$sku->addMeta($key, $val);
			}
		}

		$sku->verify();

		return $sku;
	}

	/**
	 * Add a new product
	 *
	 * @param	StorefrontModelProduct 	Instance of a product to add
	 * @return	int							product ID
	 */
	public function addProduct($product)
	{
		return $this->doProduct($product, 'add');
	}

	/**
	 * Update existing product
	 *
	 * @param	StorefrontModelProduct 	Instance of a product to add
	 * @return	int							product ID
	 */
	public function updateProduct($product)
	{
		return $this->doProduct($product, 'update');
	}

	/**
	 * Delete product
	 *
	 * @param	int		product ID
	 * @return	bool
	 */
	public function deleteProduct($pId)
	{
		// Check if product ID is set
		if (empty($pId))
		{
			throw new Exception(JText::_('Cannot delete product: no product ID set.'));
		}

		if (!is_numeric($pId))
		{
			throw new Exception(JText::_('Cannot delete product: bad product ID.'));
		}

		// check if product exists
		if (!$this->productExists($pId, true))
		{
			throw new Exception(JText::_('Cannot delete product: product does not exixt.'));
		}

		$sql = "UPDATE `#__storefront_products` SET `pActive` = 0 WHERE `pId` = " . $this->_db->quote($pId);
		$this->_db->setQuery($sql);
		//echo '<br>'; echo $this->_db->_sql; die;
		$this->_db->query();
	}

	/**
	 * Handle product actions (add, update)
	 *
	 * @param	StorefrontModelProduct 	Instance of a product to add
	 * @return	int							product ID
	 */
	private function doProduct($product, $action)
	{
		$allowedActions = array('add', 'update');

		// Get product ID (if set)
		$pId = $product->getId();

		// Check everything

		if (!in_array($action, $allowedActions))
		{
			throw new Exception(JText::_('Bad action. Why would you try to do something like that anyway?'));
		}

		$product->verify($action);

		// check if this is a product
		if (!($product instanceof StorefrontModelProduct))
		{
			throw new Exception(JText::_('Bad product. Unable to nable to .'));
		}

		if ($action == 'update')
		{
			// Check if product ID is set
			if (empty($pId))
			{
				throw new Exception(JText::_('Cannot update product: no product ID set.'));
			}

			// check if product exists
			if (!$this->productExists($pId, true))
			{
				throw new Exception(JText::_('Cannot update product: product does not exixt.'));
			}
		}
		elseif ($action == 'add')
		{
			if ($this->productExists($pId, true))
			{
				throw new Exception(JText::_('Cannot add product: product ID already exists.'));
			}
		}

		// ### Do the product

		if ($action == 'update')
		{
			$sql = "UPDATE `#__storefront_products` SET ";
		}
		elseif ($action == 'add')
		{
			$sql = "INSERT INTO `#__storefront_products` SET ";
		}

			$sql .= "
				`ptId` = " . $this->_db->quote($product->getType()) . ",
				`pName` = " . $this->_db->quote($product->getName()) . ",
				`pTagline` = " . $this->_db->quote($product->getTagline()) . ",
				`pDescription` = " . $this->_db->quote($product->getDescription()) . ",
				`pActive` = " . $product->getActiveStatus();


		// Set pId if needed if adding new product
		if ($action == 'add' && !empty($pId))
		{
			$sql .= ",
				`pId` = " . $this->_db->quote($pId);
		}

		// Add WHERE if updating product
		if ($action == 'update')
		{
			$sql .= " WHERE `pId` = " . $this->_db->quote($pId);
		}

		$this->_db->setQuery($sql);
		//echo '<br>'; echo $this->_db->_sql; die;
		$this->_db->query();
		if (empty($pId))
		{
			$pId = $this->_db->insertid();
		}


		// ### Do SKUs

		$skus = $product->getSkus();

		// Remember all sId used
		$activeSIds = array();

		foreach ($skus as $sku)
		{
			// Get sId
			$sId = $sku->getId();

			// If no sID set -- this is a new SKU -- create a new record
			if (empty($sId))
			{
				$sql = "INSERT INTO `#__storefront_skus` SET ";
			}
			// If sId is set -- update the existing SKU
			else
			{
				$sql = "UPDATE `#__storefront_skus` SET ";
			}

			$sql .= "
						`pId` = " . $this->_db->quote($pId) . ",
						`sPrice` = " . $this->_db->quote($sku->getPrice()) . ",
						`sAllowMultiple` = " . $sku->getAllowMultiple() . ",
						`sTrackInventory` = " . $sku->getTrackInventory() . ",
						`sInventory` = " . $sku->getInventoryLevel() . ",
						`sEnumerable` = " . $sku->getEnumerable() . ",
						`sActive` = " . $sku->getActiveStatus();

			if (!empty($sId))
			{
				$sql .= " WHERE `sId` = " . $this->_db->quote($sId);
			}

			$this->_db->setQuery($sql);
			//echo '<br>'; echo $this->_db->_sql;
			$this->_db->query();
			if (empty($sId))
			{
				$sId = $this->_db->insertid();
			}
			$activeSIds[] = $sId;

			// Do SKU meta (if any)
			$skuMeta = $sku->getMeta();

			$activeMetaIds = array();

			if (!empty($skuMeta))
			{
				// Go through each meta key and insert/update it, remembering the affected ID
				foreach ($skuMeta as $k => $v)
				{
					$sql = "SET @skuMetaId := 0";
					$this->_db->setQuery($sql);
					$this->_db->query();
					//echo '<br>'; echo $this->_db->_sql;

					$sql = "INSERT INTO `#__storefront_sku_meta` SET
							`sId` = " . $this->_db->quote($sId) . ",
							`smKey` = " . $this->_db->quote($k) . ",
							`smValue` = " . $this->_db->quote($v) . "
							ON DUPLICATE KEY UPDATE
							`smId` = (@skuMetaId := `smId`),
							`sId` = " . $this->_db->quote($sId) . ",
							`smKey` = " . $this->_db->quote($k) . ",
							`smValue` = " . $this->_db->quote($v);

					$this->_db->setQuery($sql);
					$this->_db->query();
					//echo '<br>'; echo $this->_db->_sql;

					$sql = "SELECT IF(@skuMetaId = 0, LAST_INSERT_ID(), @skuMetaId)";
					$this->_db->setQuery($sql);
					$this->_db->query();
					//echo '<br>'; echo $this->_db->_sql;

					$activeMetaIds[] = $this->_db->loadResult();
				}
			}

			// Delete unused Meta info: everything not affected above
			$deleteSql = '(0';
			foreach ($activeMetaIds as $metaId)
			{
				$deleteSql .= ", " . $this->_db->quote($metaId);
			}
			$deleteSql .= ')';

			$sql = "DELETE FROM `#__storefront_sku_meta` WHERE `sId` = " . $this->_db->quote($sId) . " AND `smId` NOT IN {$deleteSql}";
			$this->_db->setQuery($sql);
			//echo '<br>'; echo $this->_db->_sql; die;
			$this->_db->query();
		}

		// Delete unused SKUs
		$deleteSql = '(0';
		foreach ($activeSIds as $activeSId)
		{
			$deleteSql .= ", " . $this->_db->quote($activeSId);
		}
		$deleteSql .= ')';
		$sql = "DELETE FROM `#__storefront_skus` WHERE `pId` = " . $this->_db->quote($pId) . " AND `sId` NOT IN {$deleteSql}";
		$this->_db->setQuery($sql);
		$this->_db->query();


		// ### Do collections
		$collections = $product->getCollections();

		$affectedCollectionIds = array();
		if (!empty($collections))
		{
			foreach ($collections as $cId)
			{
				$sql = "SET @collectionId := 0";
				$this->_db->setQuery($sql);
				$this->_db->query();

				$sql = "INSERT INTO `#__storefront_product_collections` SET
						`cId` = " . $this->_db->quote($cId) . ",
						`pId` = " . $this->_db->quote($pId) . "
						ON DUPLICATE KEY UPDATE
						`cllId` = (@collectionId := `cllId`),
						`cId` = " . $this->_db->quote($cId) . ",
						`pId` = " . $this->_db->quote($pId);

				$this->_db->setQuery($sql);
				$this->_db->query();

				$sql = "SELECT IF(@collectionId = 0, LAST_INSERT_ID(), @collectionId)";
				$this->_db->setQuery($sql);
				$this->_db->query();

				$affectedCollectionIds[] = $this->_db->loadResult();
			}
		}

		// Delete unused collections
		$deleteSql = '(0';
		foreach ($affectedCollectionIds as $activeCllId)
		{
			$deleteSql .= ", " . $this->_db->quote($activeCllId);
		}
		$deleteSql .= ')';
		$sql = "DELETE FROM `#__storefront_product_collections` WHERE `pId` = " . $this->_db->quote($pId) . " AND `cllId` NOT IN {$deleteSql}";
		$this->_db->setQuery($sql);
		$this->_db->query();

		$return->pId = $pId;
		return $return;
	}

	/**
	 * Get coupon
	 *
	 * @param	string						coupon code
	 * @return	StorefrontModelCoupon	Instance of a coupon
	 */
	public function getCoupon($code)
	{
		// Create a StorefrontModelCoupon

		if ($productType == 'product')
		{
			$product = new StorefrontModelProduct();
		}
		elseif ($productType == 'course')
		{
			$product = new StorefrontModelCourse();
		}

		// Get all product info
		$productInfo = $this->getProductInfo($pId, true);

		$product->setType($productInfo->ptId);
		$product->setId($productInfo->pId);
		$product->setName($productInfo->pName);
		$product->setDescription($productInfo->pDescription);
		$product->setTagline($productInfo->pTagline);
		$product->setActiveStatus($productInfo->pActive);

		// Get collections
		$collections = $this->_getProductCollections($pId);
		foreach ($collections as $cId)
		{
			$product->addToCollection($cId);
		}

		// Get SKUs
		$skus = $this->getProductSkus($pId);

		// Add SKUs to product
		foreach ($skus as $sId)
		{
			$sku = $this->getSku($sId, $productType);
			$product->addSku($sku);
		}

		$product->verify();

		return $product;
	}

	/**
	 * Add product coupon
	 *
	 * @param	StorefrontModelCoupon 	Instance of a coupon to add
	 * @return	int							coupon ID
	 */
	public function addCoupon($coupon)
	{
		$this->_doCoupon($coupon, 'add');
	}

	public function updateCoupon($coupon)
	{
		$this->_doCoupon($coupon, 'update');
	}

	/**
	 * Delete coupon
	 *
	 * @param	string		coupon code
	 * @return	bool
	 */
	public function deleteCoupon($code)
	{
		// Check if code is set
		if (empty($code))
		{
			throw new Exception(JText::_('Cannot delete coupon: no code provided.'));
		}

		$cnId = $this->couponExists($code);

		// check if coupon exists
		if (!$cnId)
		{
			throw new Exception(JText::_('Cannot delete coupon: coupon does not exixt.'));
		}

		// Delete conditions
		$sql = "DELETE FROM `#__storefront_coupon_conditions` WHERE `cnId` = " . $this->_db->quote($cnId);
		$this->_db->setQuery($sql);
		//echo '<br>'; echo $this->_db->_sql; die;
		$this->_db->query();

		// Delete actions
		$sql = "DELETE FROM `#__storefront_coupon_actions` WHERE `cnId` = " . $this->_db->quote($cnId);
		$this->_db->setQuery($sql);
		//echo '<br>'; echo $this->_db->_sql; die;
		$this->_db->query();

		// Delete objects
		$sql = "DELETE FROM `#__storefront_coupon_objects` WHERE `cnId` = " . $this->_db->quote($cnId);
		$this->_db->setQuery($sql);
		//echo '<br>'; echo $this->_db->_sql; die;
		$this->_db->query();

		// Delete coupon
		$sql = "DELETE FROM `#__storefront_coupons` WHERE `cnId` = " . $this->_db->quote($cnId);
		$this->_db->setQuery($sql);
		//echo '<br>'; echo $this->_db->_sql; die;
		$this->_db->query();
	}

	/**
	 * Handle product coupon (add, update)
	 *
	 * @param	StorefrontModelCoupon 	Instance of a product
	 * @return	int							coupon ID
	 */
	private function _doCoupon($coupon, $action)
	{
		$allowedActions = array('add', 'update');

		$couponCode = $coupon->getCode();

		$cnId = $this->couponExists($couponCode);

		// Check everything

		$coupon->verify();

		if (!in_array($action, $allowedActions))
		{
			throw new Exception(JText::_('Bad action. Why would you try to do something like that anyway?'));
		}

		// check if this is a coupon
		if (!($coupon instanceof StorefrontModelCoupon))
		{
			throw new Exception(JText::_('Bad coupon. Unable to continue.'));
		}

		if ($action == 'update')
		{
			// check if product exists
			if (!$cnId)
			{
				throw new Exception(JText::_('Cannot update coupon: coupon does not exixt.'));
			}
		}
		elseif ($action == 'add')
		{
			if ($cnId)
			{
				throw new Exception(JText::_('Cannot add coupon: coupon already exists.'));
			}
		}


		// ### Do the coupon

		if ($action == 'update')
		{
			$sql = "UPDATE `#__storefront_coupons` SET ";
		}
		elseif ($action == 'add')
		{
			$sql = "INSERT INTO `#__storefront_coupons` SET ";
		}

			$sql .= "
				`cnDescription` = " . $this->_db->quote($coupon->getDescription()) . ",
				`cnUseLimit` = " . $coupon->getUseLimit() . ",
				`cnExpires` = FROM_UNIXTIME(" . $this->_db->quote($coupon->getExpiration()) . "),
				`cnObject` = " . $this->_db->quote($coupon->getobjectType()) . ",
				`cnActive` = " . $coupon->getActiveStatus();


		// Set code if adding new coupon
		if ($action == 'add')
		{
			$sql .= ", `cnCode` = " . $this->_db->quote($couponCode);
		}

		// Add WHERE if updating coupon
		if ($action == 'update')
		{
			$sql .= " WHERE `cnCode` = " . $this->_db->quote($couponCode);
		}

		$this->_db->setQuery($sql);
		//echo '<br>'; echo $this->_db->_sql; die;
		$this->_db->query();
		if (empty($cnId))
		{
			$cnId = $this->_db->insertid();
		}


		// ### Do objects
		$objects = $coupon->getObjects();

		// Remember all obejct IDs used
		$activeObjectIds = array();

		foreach ($objects as $obj)
		{
			$sql = "INSERT INTO `#__storefront_coupon_objects`
					SET `cnId` = " . $this->_db->quote($cnId) . ",
					`cnoObjectId` = " . $this->_db->quote($obj->id) . ",
					`cnoObjectsLimit` = " . $obj->objectLimit . "
					ON DUPLICATE KEY UPDATE `cnoObjectsLimit` = " . $this->_db->quote($obj->objectLimit);

			$this->_db->setQuery($sql);
			//echo '<br>'; echo $this->_db->_sql;
			$this->_db->query();

			$activeObjectIds[] = $obj->id;
		}

		// Delete unused object IDs
		$deleteSql = '(0';
		foreach ($activeObjectIds as $activeObjectId)
		{
			$deleteSql .= ", " . $this->_db->quote($activeObjectId);
		}
		$deleteSql .= ')';
		$sql = "DELETE FROM `#__storefront_coupon_objects` WHERE `cnId` = " . $this->_db->quote($cnId) . " AND `cnoObjectId` NOT IN {$deleteSql}";
		$this->_db->setQuery($sql);
		$this->_db->query();


		// ### Do action
		$action = $coupon->getAction();

		// Delete old action
		$sql = "DELETE FROM `#__storefront_coupon_actions` WHERE `cnId` = " . $this->_db->quote($cnId);
		$this->_db->setQuery($sql);
		$this->_db->query();

		$sql = "INSERT INTO `#__storefront_coupon_actions`
				SET
				`cnId` = " . $this->_db->quote($cnId) . ",
				`cnaAction` = " . $this->_db->quote($action->action) . ",
				`cnaVal` = " . $this->_db->quote($action->value);

		$this->_db->setQuery($sql);
		//echo '<br>'; echo $this->_db->_sql;
		$this->_db->query();

		$return->cnId = $cnId;
		return $return;
	}



	/* -------------------------------------------------------------- Private functions -------------------------------------------------------------- */



	/**
	 * Get all non-empty (those that have at least one active product) root (no parents) collections
	 *
	 * @param  $collectionType -- type of collection, category by default
	 * @return void
	 */
	private function _getCollections($collectionType = 'category')
	{
		$sql = "SELECT DISTINCT c.`cId`, c.`cName`
				FROM `#__storefront_collections` c
				LEFT JOIN `#__storefront_product_collections` pc ON c.`cId` = pc.`cId`
				LEFT JOIN `#__storefront_products` p ON p.`pId` = pc.`pId`
				WHERE c.`cParent` IS NULL AND c.`cActive` = 1 AND p.`pActive` = 1";

		$sql .= " AND c.`cType` = '{$collectionType}'";

		$this->_db->setQuery($sql);
		//echo $this->_db->_sql;
		$res = $this->_db->loadObjectList();

		return $res;
	}

	/**
	 * Get product collections -- collections product is included in
	 *
	 * @param	int			product id
	 * @return	array 		collections
	 */
	private function _getProductCollections($pId)
	{
		$sql = "SELECT `cId` FROM `#__storefront_product_collections` WHERE `pId` = " . $this->_db->quote($pId);
		$this->_db->setQuery($sql);
		$res = $this->_db->loadResultArray();

		return $res;
	}

}