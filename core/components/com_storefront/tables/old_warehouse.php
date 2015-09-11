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

/**
 *
 * Products inventory and structure (only product lookup and inventory management)
 *
 */
class StorefrontWarehouse extends JTable
{
	/**
	 * array Product categories to look at (to define scope)
	 */
	var $lookupCollections		= NULL;

	/**
	 * Contructor method for JTable class
	 *
	 * @param  database object
	 * @return void
	 */
	public function __construct(&$db)
	{
		parent::__construct('#__shop_products', 'pId', $db);
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
		$sql = "SELECT cId FROM `#__storefront_collections` c WHERE c.`cId` = {$c} AND c.`cActive` = 1";

		$this->_db->setQuery($sql);
		$cId = $this->_db->loadResult();

		return $cId;
	}

	/**
	 * Check if product exists
	 *
	 * @param  $p -- product ID (+ alias in the future)
	 * @return int pId on sucess, false if no match found
	 */
	public function productExists($p)
	{
		$sql = "SELECT pId FROM `#__storefront_products` p WHERE p.`pId` = '{$p}' AND p.`pActive` = 1";

		$this->_db->setQuery($sql);
		$cId = $this->_db->loadResult();

		return $cId;
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

		foreach ($this->lookupCollections as $cId)
		{
			$sql .= " AND c.`cId` = {$cId}";
		}

		$this->_db->setQuery($sql);
		$products = $this->_db->loadObjectList();

		return $products;
	}

	/**
	 * Get product inforamtion
	 *
	 * @param  $pId Product ID
	 * @return void
	 */
	public function getProduct($pId)
	{
		$sql = "SELECT p.* FROM `#__storefront_products` p WHERE p.`pId` = {$pId} AND `pActive` = 1";

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
					so.`sId` AS skuId, so.`oId` AS skusOptionId, sPrice, og.`ogId`, `oName`, `ogName`";
		$sql .= "	FROM `#__storefront_skus` s
					LEFT JOIN `#__storefront_sku_options` so ON s.`sId` = so.`sId`
					LEFT JOIN `#__storefront_options` o ON so.`oId` = o.`oId`
					LEFT JOIN `#__storefront_option_groups` og ON o.`ogId` = og.`ogId`

					WHERE s.`pId` = {$pId} AND s.`sActive` = 1

					ORDER BY og.`ogId`";

		$this->_db->setQuery($sql);
		//print_r($this->_db->_sql); die;
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
	 * Get a SKU mapping to the provided options
	 *
	 * @param $pId Product ID
	 * @param $options Selected options (optional for products with no options)
	 * @return SKU ID
	 */
	public function getSku($pId, $options)
	{
		// Find the number of options required for this product
		$sql = "SELECT COUNT(pog.`ogId`) FROM `#__storefront_product_option_groups` pog WHERE pog.`pId` = '{$pId}'";
		$this->_db->setQuery($sql);
		$totalOptionsRequired = $this->_db->loadResult();

		if ($totalOptionsRequired > count($options))
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
		throw new Exception(Lang::txt('COM_STOREFRONT_SKU_NOT_FOUND'));
	}


	/* -------------------------------------------------------------- Private functinos -------------------------------------------------------------- */



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
		$res = $this->_db->loadObjectList();

		return $res;
	}

}