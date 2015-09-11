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
 * @author    Ilya Shunko <ishunko@purdue.edu>
 * @copyright Copyright 2005-2015 HUBzero Foundation, LLC.
 * @license   http://opensource.org/licenses/MIT MIT
 */

// No direct access.
defined('_HZEXEC_') or die();

include_once(__DIR__ . DS . 'Product.php');
include_once(__DIR__ . DS . 'Course.php');
include_once(__DIR__ . DS . 'CourseOffering.php');
include_once(__DIR__ . DS . 'Sku.php');
include_once(__DIR__ . DS . 'Coupon.php');

/**
 *
 * Products inventory and structure (only product lookup and inventory management)
 *
 */
class StorefrontModelWarehouse extends \Hubzero\Base\Object
{
	/**
	 * array Product categories to look at (to define scope)
	 */
	var $lookupCollections = NULL;

	// Database instance
	var $db = NULL;

	/**
	 * Constructor method
	 *
	 * @param  void
	 * @return void
	 */
	public function __construct()
	{
		$this->_db = App::get('db');

		// Load language file
		Lang::load('com_storefront');
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
	 * Reset instance lookup scope
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
	 * @return int cId on success, false if no match found
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
	 * @param  	int		product ID or alias
	 * @return 	int 	pId on success, null if no match found
	 */
	public function productExists($product, $showInactive = false)
	{
		// Integer is a pID, string must be an alias
		if (is_numeric($product))
		{
			$lookupField = 'pId';
		}
		else {
			$lookupField = 'pAlias';
		}

		$sql = "SELECT `pId` FROM `#__storefront_products` p WHERE p.`{$lookupField}` = " . $this->_db->quote($product);
		if (!$showInactive)
		{
			$sql .= " AND p.`pActive` = 1";
		}

		$this->_db->setQuery($sql);
		$pId = $this->_db->loadResult();

		return $pId;
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
		$sql = "SELECT DISTINCT p.* FROM `#__storefront_products` p
				JOIN `#__storefront_product_collections` c ON p.`pId` = c.`pId`";
		$sql .= " WHERE p.`pActive` = 1";

		foreach ($this->lookupCollections as $cId)
		{
			$sql .= " AND c.`cId` = " . $this->_db->quote($cId);
		}

		$this->_db->setQuery($sql);
		$products = $this->_db->loadObjectList();

		return $products;
	}

	/**
	 * Get product information
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
					s.`sId` AS skuId, so.`oId` AS skusOptionId, s.`sPrice`, s.`sAllowMultiple`, s.`sInventory`,
					s.`sTrackInventory`, og.`ogId`, `oName`, `ogName`";
		$sql .= "	FROM `#__storefront_skus` s
					LEFT JOIN `#__storefront_sku_options` so ON s.`sId` = so.`sId`
					LEFT JOIN `#__storefront_options` o ON so.`oId` = o.`oId`
					LEFT JOIN `#__storefront_option_groups` og ON o.`ogId` = og.`ogId`

					WHERE s.`pId` = {$pId} AND s.`sActive` = 1 AND (s.`sInventory` > 0 OR s.`sTrackInventory` = 0)

					ORDER BY og.`ogId`, o.`oId`";

		$this->_db->setQuery($sql);
		$this->_db->query();
		//print_r($this->_db->replacePrefix( (string) $sql )); die;
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
		foreach ($res as $line) {
			// Populate options
			if ($line->ogId) {
				// Keep track of option groups and do not do anything if no options
				if ($currentOgId != $line->ogId) {
					$currentOgId = $line->ogId;

					$ogInfo = new stdClass();
					$ogInfo->ogId = $line->ogId;
					$ogInfo->ogName = $line->ogName;

					$options[$currentOgId]['info'] = $ogInfo;
					unset($ogInfo);
				}

				$oInfo = new stdClass();
				$oInfo->oId = $line->skusOptionId;
				$oInfo->oName = $line->oName;
				$options[$currentOgId]['options'][$line->skusOptionId] = $oInfo;
				unset($oInfo);
			}

			// populate SKUs for JS
			$skusInfo = new stdClass();
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

		$ret = new stdClass();
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
		//$sql = "SELECT COUNT(pog.`ogId`) FROM `#__storefront_product_option_groups` pog WHERE pog.`pId` = '{$pId}'";
		$sql = "SELECT COUNT(s.`sId`) AS cnt FROM `#__storefront_skus` s
				INNER JOIN `#__storefront_sku_options` so ON s.`sId` = so.`sId`
				WHERE s.`pId` = '{$pId}' AND s.`sActive` > 0
				GROUP BY s.`sId` ORDER BY cnt DESC LIMIT 1";
		$this->_db->setQuery($sql);
		$this->_db->execute();

		if ($this->_db->getNumRows() < 1)
		{
			$totalOptionsRequired = 0;
		}
		else {
			$totalOptionsRequired = $this->_db->loadResult();
		}
		//print_r($this->_db->replacePrefix( (string) $sql )); die;

		if (!empty($options) && $totalOptionsRequired > count($options))
		{
			throw new Exception(Lang::txt('COM_STOREFRONT_NOT_ENOUGH_OPTIONS'));
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
				WHERE s.`pId` = '{$pId}' AND s.sActive > 0";
		if (!empty($options))
		{
			$sql .= " AND {$skuOptionsSql}";
		}
		$sql .= " GROUP BY s.`sId` HAVING matches = {$totalOptionsRequired}";

		//print_r($this->_db->replacePrefix( (string) $sql )); die;

		$this->_db->setQuery($sql);
		$sId = $this->_db->loadObject();

		if ($sId)
		{
			return $sId->sId;
		}

		// no match
		throw new Exception(Lang::txt('COM_STOREFRONT_SKU_NOT_FOUND'));
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

		// Get only results for existing SKUs and existing products associated with each SKU
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
		$this->_db->execute();
		$found = $this->_db->getNumRows();

		$rawSkusInfo = $this->_db->loadObjectList();

		/*
			Parse the result and organize it by SKU (since same SKU can be returned several times, depending on the number of options):

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
	 * Get product SKU IDs
	 *
	 * @param	int			product id
	 * @return	array 		collections
	 */
	public function getProductSkus($pId)
	{
		$sql = "SELECT `sId` FROM `#__storefront_skus` WHERE `sActive` = 1 AND `pId` = " . $this->_db->quote($pId);
		$this->_db->setQuery($sql);
		$res = $this->_db->loadColumn();

		return $res;
	}

	/**
	 * Update SKU inventory if the item is tracking inventory
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
			throw new Exception(Lang::txt('Bad product type.'));
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
			throw new Exception(Lang::txt('Cannot delete product: no product ID set.'));
		}

		if (!is_numeric($pId))
		{
			throw new Exception(Lang::txt('Cannot delete product: bad product ID.'));
		}

		// check if product exists
		if (!$this->productExists($pId, true))
		{
			throw new Exception(Lang::txt('Cannot delete product: product does not exixt.'));
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
			throw new Exception(Lang::txt('Bad action. Why would you try to do something like that anyway?'));
		}

		$product->verify($action);

		// check if this is a product
		if (!($product instanceof StorefrontModelProduct))
		{
			throw new Exception(Lang::txt('Bad product.'));
		}

		if ($action == 'update')
		{
			// Check if product ID is set
			if (empty($pId))
			{
				throw new Exception(Lang::txt('Cannot update product: no product ID set.'));
			}

			// check if product exists
			if (!$this->productExists($pId, true))
			{
				throw new Exception(Lang::txt('Cannot update product: product does not exixt.'));
			}
		}
		elseif ($action == 'add')
		{
			if ($this->productExists($pId, true))
			{
				throw new Exception(Lang::txt('Cannot add product: product ID already exists.'));
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

		$return = new stdClass();
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
		// Seems like unused code
		throw new Exception('Not sure if this is ever used. Warehouse.php');

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
			throw new Exception(Lang::txt('Cannot delete coupon: no code provided.'));
		}

		$cnId = $this->couponExists($code);

		// check if coupon exists
		if (!$cnId)
		{
			throw new Exception(Lang::txt('Cannot delete coupon: coupon does not exixt.'));
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
			throw new Exception(Lang::txt('Bad action. Why would you try to do something like that anyway?'));
		}

		// check if this is a coupon
		if (!($coupon instanceof StorefrontModelCoupon))
		{
			throw new Exception(Lang::txt('Bad coupon. Unable to continue.'));
		}

		if ($action == 'update')
		{
			// check if product exists
			if (!$cnId)
			{
				throw new Exception(Lang::txt('Cannot update coupon: coupon does not exixt.'));
			}
		}
		elseif ($action == 'add')
		{
			if ($cnId)
			{
				throw new Exception(Lang::txt('Cannot add coupon: coupon already exists.'));
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

		$return = new stdClass();
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
		$res = $this->_db->loadColumn();

		return $res;
	}

}