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

namespace Components\Storefront\Models;

use Hubzero\Base\Model;
use Components\Storefront\Models\Product;
use Components\Storefront\Models\Course;
use Components\Storefront\Models\CourseOffering;
use Components\Storefront\Models\Sku;
use Components\Storefront\Models\Coupon;
use Components\Storefront\Models\Collection;
use Lang;

require_once(__DIR__ . DS . 'Product.php');
require_once(__DIR__ . DS . 'Course.php');
require_once(__DIR__ . DS . 'CourseOffering.php');
require_once(__DIR__ . DS . 'Sku.php');
require_once(__DIR__ . DS . 'Coupon.php');
require_once(__DIR__ . DS . 'Collection.php');

/**
 *
 * Products inventory and structure (only product lookup and inventory management)
 *
 */
class Warehouse extends \Hubzero\Base\Object
{
	/**
	 * array Product categories to look at (to define scope)
	 */
	var $lookupCollections = NULL;

	// Access levels scope (what is allowed to display)
	var $accessLevelsScope = false;

	// User scope (what user is trying to get the info)
	var $userScope = false;

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
		$this->_db = \App::get('db');

		// Load language file
		\App::get('language')->load('com_storefront');
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

	/**
	 * Add access level scope for entities having access property (currently products only)
	 *
	 * @param  array Access levels IDs
	 * @return void
	 */
	public function addAccessLevels($accessLevels)
	{
		$this->accessLevelsScope = array_merge(array('NULL', 0), $accessLevels);
	}

	/**
	 * Add user level scope for entities having user permission property (currently SKUs only)
	 *
	 * @param  int user ID
	 * @return void
	 */
	public function addUserScope($uId)
	{
		$this->userScope = $uId;
	}

	/* ------------------------------------- Main working functions ----------------------------------------------- */

	/**
	 * Get a collection
	 *
	 * @param	int							collection ID
	 * @return	StorefrontModelCollection 	Instance of a collection
	 */
	public function getCollection($cId)
	{
		$collection = new Collection();

		// Get all product info
		$collectionInfo = $this->getCollectionInfo($cId, true);

		//print_r($collectionInfo); die;

		$collection->setType($collectionInfo->cType);
		$collection->setId($collectionInfo->cId);
		$collection->setName($collectionInfo->cName);
		$collection->setActiveStatus($collectionInfo->cActive);

		$collection->verify();

		return $collection;
	}

	/**
	 * Get collection information
	 *
	 * @param	int			Collection ID
	 * @param  	bool		Flag whether to show inactive collection info
	 * @return 	object		Collection info
	 */
	public function getCollectionInfo($cId, $showInactive = false)
	{
		$sql = "SELECT * FROM `#__storefront_collections` c
 				WHERE c.`cId` = " . $this->_db->quote($cId);

		if (!$showInactive)
		{
			$sql .= " AND `cActive` = 1";
		}

		$this->_db->setQuery($sql);
		$collection = $this->_db->loadObject();

		return $collection;
	}


	/**
	 * Get all non-empty (those that have at least one active product) root (no parents) product 'category' collections
	 *
	 * @param  	string 	What to return (count or rows)
	 * @param	array 	Filters
	 * @return 	void
	 */
	public function getRootCategories($return = 'rows', $filters = false)
	{
		return $this->_getCollections('category', $filters);
	}

	/**
	 * Get all root (no parents) product 'category' collections
	 *
	 * @param  void
	 * @return void
	 */
	public function getCategories($rtrn='list', $filters = array())
	{
		$filters['collectionType'] = 'category';
		return $this->_getAllCollections($rtrn, $filters);
	}

	public function getCollections($rtrn='list', $filters = array())
	{
		return $this->_getAllCollections($rtrn, $filters);
	}

	/**
	 * Check if collection exists
	 *
	 * @param  	mixed	collection ID or alias
	 * @return 	int 	cId on success, null if no match found
	 */
	public function collectionExists($cId, $showInactive = false)
	{
		$sql = 'SELECT `cId` FROM `#__storefront_collections` c
				WHERE c.`cId` = ' . $this->_db->quote($cId) . ' OR
				c.`cAlias` = ' . $this->_db->quote($cId);
		if (!$showInactive)
		{
			$sql .= " AND c.`cActive` = 1";
		}

		$this->_db->setQuery($sql);
		$cId = $this->_db->loadResult();

		return $cId;
	}

	/**
	 * Add collection
	 *
	 * @param	StorefrontModelCollection
	 * @return	void
	 */
	public function addCollection($collection)
	{
		return $this->doCollection($collection, 'add');
	}

	/**
	 * Update existing collection
	 *
	 * @param	StorefrontModelCollection
	 * @return	void
	 */
	public function updateCollection($collection)
	{
		return $this->doCollection($collection, 'update');
	}

	/**
	 * Handle collection actions (add, update)
	 *
	 * @param	StorefrontModelCollection
	 * @param 	string
	 * @return	string							category ID
	 */
	private function doCollection($collection, $action)
	{
		$allowedActions = array('add', 'update');

		// Check everything
		$cId = $collection->getId();

		if (!in_array($action, $allowedActions))
		{
			throw new \Exception(Lang::txt('Bad action. Why would you try to do something like that anyway?'));
		}

		if ($action == 'update')
		{
			// Check if the ID is set
			if (empty($cId))
			{
				throw new \Exception(Lang::txt('Cannot update collection: no collection ID set.'));
			}

			// check if category exists
			if (!$this->collectionExists($cId, true))
			{
				throw new \Exception(Lang::txt('Cannot update collection: collection does not exist.'));
			}
		}
		elseif ($action == 'add')
		{
			if ($this->collectionExists($cId, true))
			{
				throw new \Exception(Lang::txt('Cannot add collection: collection already exists.'));
			}
			elseif (empty($cId))
			{
				throw new \Exception(Lang::txt('Cannot add collection: the new ID must be provided.'));
			}
		}

		// ### Do the collection
		if ($action == 'update')
		{
			$sql = "UPDATE `#__storefront_collections` SET ";
		}
		elseif ($action == 'add')
		{
			$sql = "INSERT INTO `#__storefront_collections` SET ";
		}

		$sql .= "
				`cName` = " . $this->_db->quote($collection->getName()) . ",
				`cActive` = " . $this->_db->quote($collection->getActiveStatus()) . ",
				`cType` = " . $this->_db->quote($collection->getType());

		// Set pId if needed if adding new product
		if ($action == 'add' && !empty($cId))
		{
			$sql .= ",
				`cId` = " . $this->_db->quote($collection->getId());
		}

		// Add WHERE if updating product
		if ($action == 'update')
		{
			$sql .= " WHERE `cId` = " . $this->_db->quote($collection->getId());
		}

		$this->_db->setQuery($sql);
		//print_r($this->_db->replacePrefix($this->_db->getQuery())); die;
		$this->_db->query();

		return ($cId);
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
		else
		{
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
	 * Check if product exists and can be viewed by the current user
	 *
	 * @param  	int		product ID or alias
	 * @return 	int 	pId on success, null if no match found
	 */
	public function checkProduct($product, $showInactive = false)
	{
		// Integer is a pID, string must be an alias
		if (is_numeric($product))
		{
			$lookupField = 'pId';
		}
		else
		{
			$lookupField = 'pAlias';
		}

		$sql = "SELECT `pId`, `access`";
		$sql .= ", IF(";
		$sql .= " (`publish_up` IS NULL OR `publish_up` <= NOW())";
		$sql .= " AND (`publish_down` IS NULL OR `publish_down` = '0000-00-00 00:00:00' OR `publish_down` > NOW()";
		$sql .= "), 1, 0) AS isPublished";
		$sql .= " FROM `#__storefront_products` p WHERE p.`{$lookupField}` = " . $this->_db->quote($product);
		if (!$showInactive)
		{
			$sql .= " AND p.`pActive` = 1";
		}

		$this->_db->setQuery($sql);
		$pInfo = $this->_db->loadObject();

		$response = new \stdClass();

		$response->status = 1;
		if (empty($pInfo))
		{
			$response->status = 0;
			$response->errorCode = 404;
			$response->message = 'COM_STOREFRONT_PRODUCT_NOT_FOUND';
			return $response;
		}

		// Check if the product can be viewed (if access level scope is set)
		if ($this->accessLevelsScope)
		{
			if (!in_array($pInfo->access, $this->accessLevelsScope))
			{
				$response->status = 0;
				$response->errorCode = 403;
				$response->message = 'COM_STOREFRONT_PRODUCT_ACCESS_NOT_AUTHORIZED';
				return $response;
			}
		}

		// Check if the product is published
		if (!$pInfo->isPublished)
		{
			$response->status = 0;
			$response->errorCode = 403;
			$response->message = 'COM_STOREFRONT_PRODUCT_ACCESS_NOT_AUTHORIZED';
			return $response;
		}

		$response->pId = $pInfo->pId;
		return $response;
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
	public function getProducts($return = 'rows', $showOnlyActive = true, $filters = false)
	{
		$sql = "SELECT DISTINCT p.*, pt.ptName FROM `#__storefront_products` p
				LEFT JOIN `#__storefront_product_types` pt ON p.`ptId` = pt.`ptId`
				LEFT JOIN `#__storefront_product_collections` c ON p.`pId` = c.`pId`";
		$sql .= " WHERE 1";

		if ($showOnlyActive)
		{
			$sql .= " AND `pActive` = 1";

			// check the publish times
			$sql .= " AND (`publish_up` IS NULL OR `publish_up` <= NOW())";
			$sql .= " AND (`publish_down` IS NULL OR `publish_down` = '0000-00-00 00:00:00' OR `publish_down` > NOW())";
		}

		// Filter by collections
		if ($this->lookupCollections)
		{
			foreach ($this->lookupCollections as $cId)
			{
				$sql .= " AND c.`cId` = " . $this->_db->quote($cId);
			}
		}
		// Filter by authorized view levels (if current user scope is set)
		if ($this->accessLevelsScope)
		{
			$sql .= " AND (p.`access` IS NULL OR p.`access` IN(";
			$accessLevels = '0';
			foreach ($this->accessLevelsScope as $avl)
			{
				$accessLevels .= ', ' . $avl;
			}
			$sql .= $accessLevels;
			$sql .= '))';
		}
		// Filter by filters
		//print_r($filters);

		if (isset($filters['sort']))
		{

			if ($filters['sort'] == 'title')
			{
				$filters['sort'] = 'pName';
			}
			if ($filters['sort'] == 'state')
			{
				$filters['sort'] = 'pActive';
			}

			$sql .= " ORDER BY " . $filters['sort'];

			if (isset($filters['sort_Dir']))
			{
				$sql .= ' ' . $filters['sort_Dir'];
			}
		}

		if (isset($filters['limit']) && is_numeric($filters['limit']))
		{
			$sql .= ' LIMIT ' . $filters['limit'];

			if (isset($filters['start']) && is_numeric($filters['start']))
			{
				$sql .= ' OFFSET ' . $filters['start'];
			}
		}

		$this->_db->setQuery($sql);
		//print_r($this->_db->toString()); die;
		$this->_db->execute();
		if ($return == 'count')
		{
			return($this->_db->getNumRows());
		}
		$products = $this->_db->loadObjectList();

		return $products;
	}

	/**
	 * Get product information TODO: phase out the legacy format
	 *
	 * @param	int			Product ID
	 * @param  	bool		Flag whether to show inactive product info
	 * @return 	object		Product info
	 */
	public function getProductInfo($pId, $showInactive = false)
	{
		// Get the product
		try
		{
			$product = Product::getInstance($pId);
		}
		catch (\Exception $e)
		{
			return false;
		}

		// Do the inactive/active check
		if (!$showInactive)
		{
			// check if active
			if (!$product->getActiveStatus())
			{
				return false;
			}

			// check if published
			if (!$product->getPublishTime()->publishedNow)
			{
				return false;
			}
		}

		// Reformat the output for legacy code
		$productInfo = new \stdClass();
		$productInfo->pId = $product->getId();
		$productInfo->pAlias = $product->getAlias();
		$productInfo->ptId = $product->getTypeInfo()->id;
		$productInfo->pName = $product->getName();
		$productInfo->pTagline = $product->getTagline();
		$productInfo->pDescription = $product->getDescription();
		$productInfo->pFeatures = $product->getFeatures();
		$productInfo->pActive = $product->getActiveStatus();
		$productInfo->pAllowMultiple = $product->getAllowMultiple();
		$productInfo->access = $product->getAccessLevel();
		$productInfo->publish_up = $product->getPublishTime()->publish_up;
		$productInfo->publish_down = $product->getPublishTime()->publish_down;
		$productInfo->ptName = $product->getTypeInfo()->name;
		$productInfo->ptModel = $product->getTypeInfo()->model;
		$productInfo->images = $product->getImages();

		return $productInfo;
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
					LEFT JOIN `#__storefront_option_groups` og ON o.`ogId` = og.`ogId`";

		// check user scope if needed
		if ($this->userScope)
		{
			$sql .= " LEFT JOIN #__storefront_permissions pr ON pr.`scope_id` = s.sId";
		}

		$sql .= "	WHERE s.`pId` = {$pId} AND s.`sActive` = 1";
		$sql .= " 	AND (s.`publish_up` IS NULL OR s.`publish_up` <= NOW())";
		$sql .= " 	AND (s.`publish_down` IS NULL OR s.`publish_down` = '0000-00-00 00:00:00' OR s.`publish_down` > NOW())";
		$sql .= "   AND (s.`sInventory` > 0 OR s.`sTrackInventory` = 0)";
		if ($this->userScope)
		{
			$sql .= " AND (s.`sRestricted` = 0 OR (pr.scope = 'sku' AND pr.uId = '{$this->userScope}'))";
		}
		else
		{
			$sql .= " AND s.`sRestricted` = 0";
		}
		$sql .= "	ORDER BY og.`ogId`, o.`oId`";

		$this->_db->setQuery($sql);
		//print_r($this->_db->toString()); die;
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
		foreach ($res as $line) {
			// Populate options
			if ($line->ogId) {
				// Keep track of option groups and do not do anything if no options
				if ($currentOgId != $line->ogId) {
					$currentOgId = $line->ogId;

					$ogInfo = new \stdClass();
					$ogInfo->ogId = $line->ogId;
					$ogInfo->ogName = $line->ogName;

					$options[$currentOgId]['info'] = $ogInfo;
					unset($ogInfo);
				}

				$oInfo = new \stdClass();
				$oInfo->oId = $line->skusOptionId;
				$oInfo->oName = $line->oName;
				$options[$currentOgId]['options'][$line->skusOptionId] = $oInfo;
				unset($oInfo);
			}

			// populate SKUs for JS
			$skusInfo = new \stdClass();
			$skusInfo->sId = $line->skuId;
			$skusInfo->sPrice = $line->sPrice;
			$skusInfo->sAllowMultiple = $line->sAllowMultiple;
			$skusInfo->sTrackInventory = $line->sTrackInventory;
			$skusInfo->sInventory = $line->sInventory;

			$skus[$line->skuId]['info'] = $skusInfo;
			$skus[$line->skuId]['options'][] = $line->skusOptionId;
			unset($skusInfo);

		}

		$ret = new \stdClass();
		$ret->options = $options;
		$ret->skus = $skus;
		//print_r($ret); die;

		return $ret;
	}

	// Get all option groups (for admin)
	public function getOptionGroups($rtrn = 'list', $filters = array())
	{
		$sql = "SELECT og.*
				FROM `#__storefront_option_groups` og
				WHERE 1";

		// Filter by filters
		//print_r($filters);
		if (isset($filters['active']) && $filters['active'] == 1)
		{
			$sql .= " AND ogActive = 1";
		}

		if (isset($filters['ids']) && !empty($filters['ids']))
		{
			if (!is_array($filters['ids']))
			{
				$filters['ids'] = array($filters['ids']);
			}

			$sql .= " AND ogId IN (0";
			foreach ($filters['ids'] as $ogId)
			{
				$sql .= ', ' . $ogId;
			}
			$sql .= ")";
		}

		if (isset($filters['sort']))
		{
			$sql .= " ORDER BY " . $filters['sort'];

			if (isset($filters['sort_Dir']))
			{
				$sql .= ' ' . $filters['sort_Dir'];
			}
		}
		else {
			$sql .= " ORDER BY ogName";
		}

		if (isset($filters['limit']) && is_numeric($filters['limit']))
		{
			$sql .= ' LIMIT ' . $filters['limit'];

			if (isset($filters['start']) && is_numeric($filters['start']))
			{
				$sql .= ' OFFSET ' . $filters['start'];
			}
		}

		$this->_db->setQuery($sql);
		$this->_db->execute();
		if ($rtrn == 'count')
		{
			return($this->_db->getNumRows());
		}

		//$res = $this->_db->loadObjectList('ogId');
		$res = $this->_db->loadObjectList();
		//print_r($res); die;

		return $res;
	}

	/**
	 * Get all options for the option group (for admin)
	 *
	 * @param	int			option group ID
	 * @return	array 		options IDs
	 */
	public function getOptionGroupOptions($ogId, $return = 'rows', $showOnlyActive = true, $filters = false)
	{
		$sql = "SELECT * FROM `#__storefront_options` WHERE 1";
		if ($showOnlyActive)
		{
			$sql .= " AND `oActive` = 1";
		}
		$sql .= " AND `ogId` = " . $this->_db->quote($ogId);

		// Filter by filters
		//print_r($filters);
		if (isset($filters['sort']))
		{
			if ($filters['sort'] == 'title')
			{
				$filters['sort'] = 'oName';
			}
			if ($filters['sort'] == 'state')
			{
				$filters['sort'] = 'oActive';
			}

			$sql .= " ORDER BY " . $filters['sort'];

			if (isset($filters['sort_Dir']))
			{
				$sql .= ' ' . $filters['sort_Dir'];
			}
		}
		else
		{
			$sql .= " ORDER BY `oId`";
		}

		if (isset($filters['limit']) && is_numeric($filters['limit']))
		{
			$sql .= ' LIMIT ' . $filters['limit'];

			if (isset($filters['start']) && is_numeric($filters['start']))
			{
				$sql .= ' OFFSET ' . $filters['start'];
			}
		}
		$this->_db->setQuery($sql);
		//print_r($this->_db->replacePrefix($this->_db->getQuery()));
		$this->_db->execute();
		if ($return == 'count')
		{
			$res = $this->_db->getNumRows();
		}
		else
		{
			$res = $this->_db->loadObjectList();
		}

		return $res;
	}

	/**
	 * Get SKU mapping to the provided options
	 *
	 * @param 	$pId 						Product ID
	 * @param 	$options 					Selected options (optional for products with no options)
	 * @param 	$throwExceptionOnNomatch	Should the exception be thrown if no match found (default: true)
	 * @return 	SKU ID
	 */
	public function mapSku($pId, $options, $throwExceptionOnNomatch = true)
	{
		// Find the number of options required for this product
		$sql = "SELECT COUNT(pog.`ogId`) AS cnt FROM `#__storefront_product_option_groups` pog WHERE pog.`pId` = '{$pId}'";

		/*
		$sql = "SELECT COUNT(s.`sId`) AS cnt FROM `#__storefront_skus` s
				INNER JOIN `#__storefront_sku_options` so ON s.`sId` = so.`sId`
				WHERE s.`pId` = '{$pId}' AND s.`sActive` > 0
				GROUP BY s.`sId` ORDER BY cnt DESC LIMIT 1";
		*/
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
			throw new \Exception(Lang::txt('COM_STOREFRONT_NOT_ENOUGH_OPTIONS'));
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
		if ($throwExceptionOnNomatch)
		{
			throw new \Exception(Lang::txt('COM_STOREFRONT_SKU_NOT_FOUND'));
		}
		return false;
	}

	/**
	 * Get all info for SKUs provided, including options
	 *
	 * @param 	array 		SKU IDs
	 * @param 	bool 		Flag whether to show inactive SKU and product info
	 * @return 	array 		info
	 */
	public function getSkusInfo($skus, $showInactive = false, $filters = false)
	{
		$sqlIn = '(0';
		if (is_array($skus) || is_object($skus))
		{
			foreach ($skus as $sId)
			{
				$sqlIn .= ', ' . $sId;
			}
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

			// check publish up and down
			$sql .= " AND (p.`publish_up` IS NULL OR p.`publish_up` <= NOW())";
			$sql .= " AND (p.`publish_down` IS NULL OR p.`publish_down` = '0000-00-00 00:00:00' OR p.`publish_down` > NOW())";

			$sql .= " AND (s.`publish_up` IS NULL OR s.`publish_up` <= NOW())";
			$sql .= " AND (s.`publish_down` IS NULL OR s.`publish_down` = '0000-00-00 00:00:00' OR s.`publish_down` > NOW())";
		}

		// Filter by filters
		//print_r($filters); die;
		if (isset($filters['sort']))
		{
			if ($filters['sort'] == 'title')
			{
				$filters['sort'] = 'sSku';
			}
			if ($filters['sort'] == 'state')
			{
				$filters['sort'] = 'sActive';
			}

			$sql .= " ORDER BY " . $filters['sort'];

			if (isset($filters['sort_Dir']))
			{
				$sql .= ' ' . $filters['sort_Dir'];
			}
		}
		else
		{
			$sql .= " ORDER BY s.`sId`";
		}

		if (isset($filters['limit']) && is_numeric($filters['limit']))
		{
			$sql .= ' LIMIT ' . $filters['limit'];

			if (isset($filters['start']) && is_numeric($filters['start']))
			{
				$sql .= ' OFFSET ' . $filters['start'];
			}
		}

		$this->_db->setQuery($sql);
		//print_r($this->_db->toString()); die;
		$this->_db->execute();

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

			// Fix the NULL price
			if ($row->sPrice == '')
			{
				$row->sPrice = 0;
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
	public function getSkuInfo($sId , $showInactive = true)
	{
		$sInfo = $this->getSkusInfo(array($sId), $showInactive);

		if (empty($sInfo[$sId]))
		{
			return false;
		}

		$sInfo = $sInfo[$sId];

		// Check if the product can be viewed (if access level scope is set)
		if ($this->accessLevelsScope)
		{
			if (!in_array($sInfo['info']->access, $this->accessLevelsScope))
			{
				throw new \Exception(Lang::txt('COM_STOREFRONT_PRODUCT_ACCESS_NOT_AUTHORIZED') . ': ' . $sInfo['info']->pName . ', ' . $sInfo['info']->oName);
			}
		}

		return($sInfo);
	}

	/**
	 * Get product SKU IDs
	 *
	 * @param	int			product id
	 * @return	array 		SKU IDs
	 */
	public function getProductSkus($pId, $return = 'rows', $showOnlyActive = true)
	{
		$sql = "SELECT";
		if ($return == 'all')
		{
			$sql .= " *";
		}
		else
		{
			$sql .= " `sId`";
		}
		$sql .= " FROM `#__storefront_skus` WHERE 1";
		if ($showOnlyActive)
		{
			$sql .= " AND `sActive` = 1";
		}
		$sql .= " AND `pId` = " . $this->_db->quote($pId);
		$this->_db->setQuery($sql);
		//print_r($this->_db->replacePrefix($this->_db->getQuery()));

		$this->_db->execute();
		if ($return == 'count')
		{
			$res = $this->_db->getNumRows();
		}
		elseif ($return == 'all')
		{
			$res = $this->_db->loadObjectList();
		}
		else
		{
			$res = $this->_db->loadColumn();
		}

		return $res;
	}

	/**
	 * Get product types
	 *
	 * @param	int					product type ID
	 * @return	array 				info
	 */
	public function getProductTypes()
	{
		$sql = "SELECT * FROM `#__storefront_product_types`";

		$this->_db->setQuery($sql);
		$res = $this->_db->loadObjectList();
		return $res;
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
		$course = \Components\Storefront\Models\Product::getInstance($courseId);

		// check if this is a course
		if (!($course instanceof Course))
		{
			throw new \Exception(Lang::txt('Invalid course'));
		}

		$course->getCollections();
		$course->getSkus();

		return $course;
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
		$this->_db->query();
		return($this->_db->loadResult());
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
			throw new \Exception(Lang::txt('Cannot delete coupon: no code provided.'));
		}

		$cnId = $this->couponExists($code);

		// check if coupon exists
		if (!$cnId)
		{
			throw new \Exception(Lang::txt('Cannot delete coupon: coupon does not exixt.'));
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
			throw new \Exception(Lang::txt('Bad action. Why would you try to do something like that anyway?'));
		}

		// check if this is a coupon
		if (!($coupon instanceof Coupon))
		{
			throw new \Exception(Lang::txt('Bad coupon. Unable to continue.'));
		}

		if ($action == 'update')
		{
			// check if product exists
			if (!$cnId)
			{
				throw new \Exception(Lang::txt('Cannot update coupon: coupon does not exixt.'));
			}
		}
		elseif ($action == 'add')
		{
			if ($cnId)
			{
				throw new \Exception(Lang::txt('Cannot add coupon: coupon already exists.'));
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

		$return = new \stdClass();
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
	private function _getCollections($collectionType = 'category', $filters = false)
	{
		$sql = "SELECT DISTINCT c.`cId`, c.`cAlias`, c.`cName`
				FROM `#__storefront_collections` c
				LEFT JOIN `#__storefront_product_collections` pc ON c.`cId` = pc.`cId`
				LEFT JOIN `#__storefront_products` p ON p.`pId` = pc.`pId`
				WHERE c.`cParent` IS NULL AND c.`cActive` = 1 AND p.`pActive` = 1";

		$sql .= " AND c.`cType` = '{$collectionType}'";

		$this->_db->setQuery($sql);
		$res = $this->_db->loadObjectList();
		//print_r($this->_db->replacePrefix($this->_db->getQuery()));

		return $res;
	}

	/**
	 * Get all including empty root (no parents) collections
	 * TODO: implement subcategories
	 *
	 * @param  $collectionType -- type of collection, category by default
	 * @return void
	 */
	private function _getAllCollections($return = 'list', $filters = array())
	{
		$sql = "SELECT c.*
				FROM `#__storefront_collections` c
				WHERE c.`cParent` IS NULL";

		// Filter by filters
		//print_r($filters);
		if (isset($filters['collectionType']))
		{
			$sql .= " AND c.`cType` = '{$filters['collectionType']}'";
		}

		if (isset($filters['active']))
		{
			$sql .= " AND cActive = 1";
		}

		if (isset($filters['sort']))
		{
			if ($filters['sort'] == 'title')
			{
				$filters['sort'] = 'cName';
			}
			if ($filters['sort'] == 'state')
			{
				$filters['sort'] = 'cActive';
			}

			$sql .= " ORDER BY " . $filters['sort'];

			if (isset($filters['sort_Dir']))
			{
				$sql .= ' ' . $filters['sort_Dir'];
			}
		}
		else {
			$sql .= " ORDER BY cType";
		}

		if (isset($filters['limit']) && is_numeric($filters['limit']))
		{
			$sql .= ' LIMIT ' . $filters['limit'];

			if (isset($filters['start']) && is_numeric($filters['start']))
			{
				$sql .= ' OFFSET ' . $filters['start'];
			}
		}

		$this->_db->setQuery($sql);
		$this->_db->execute();
		if ($return == 'count')
		{
			return($this->_db->getNumRows());
		}

		$res = $this->_db->loadObjectList();
		//print_r($this->_db->replacePrefix($this->_db->getQuery()));

		return $res;
	}

	/**
	 * Get product collections -- collections product is included in
	 *
	 * @param	int			product id
	 * @return	array 		collections
	 */
	public function getProductCollections($pId)
	{
		$sql = "SELECT `cId` FROM `#__storefront_product_collections` WHERE `pId` = " . $this->_db->quote($pId);
		$this->_db->setQuery($sql);
		$res = $this->_db->loadColumn();

		return $res;
	}

}