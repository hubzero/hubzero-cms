<?php

namespace Components\Storefront\Models;

use Components\Storefront\Models\Warehouse;

require_once(__DIR__ . DS . 'Warehouse.php');

/**
* Archive model. Interface between admin and Warehouse
*
* @package		Joomla.Administrator
* @subpackage	com_storefront
* @since		1.6
*/
class Archive extends \Hubzero\Base\Object
{
	/**
	 * Products
	 *
	 * @var object
	 */
	private $_products = null;

	/**
	 * Products count
	 *
	 * @var integer
	 */
	private $_products_count = null;

	public function __construct()
	{
		$this->_db = \App::get('db');
	}

	/**
	 * Get a count or list of products
	 *
	 * @param      string  $rtrn    What data to return
	 * @param      array   $filters Filters to apply to data retrieval
	 * @param      boolean $boolean Clear cached data?
	 * @return     mixed
	 */
	public function products($rtrn = 'list', $filters = array(), $clear = false)
	{
		if (!isset($filters['sort']))
		{
			$filters['sort'] = 'title';
		}
		if (!isset($filters['sort_Dir']))
		{
			$filters['sort_Dir']  = 'ASC';
		}

		switch (strtolower($rtrn))
		{
			case 'count':
				if (!isset($this->_products_count) || !is_numeric($this->_products_count) || $clear)
				{
					$warehouse = new Warehouse();
					// Reset limit, since all records count is needed here
					unset($filters['limit']);

					$this->_products_count = $warehouse->getProducts('count', false, $filters);
				}
				return $this->_products_count;
				break;

			case 'list':
			case 'rows':
			case 'results':
			default:
				if (!$this->_products instanceof \Hubzero\Base\ItemList || $clear)
				{
					$warehouse = new Warehouse();
					if (!$results = $warehouse->getProducts('rows', false, $filters))
					{
						$results = array();
					}
					$this->_products = new \Hubzero\Base\ItemList($results);
					return $this->_products;
				}
				break;
		}
	}

	/**
	 * Get product info
	 *
	 * @param      int $pId Product ID
	 * @return     mixed product info
	 */
	public function product($pId)
	{
		$product = new Product($pId);
		return $product;
	}

	/**
	 * Get product types
	 *
	 * @param      void
	 * @return     types
	 */
	public function getProductTypes()
	{
		$warehouse = new Warehouse();
		$types = $warehouse->getProductTypes();
		return $types;
	}

	/**
	 * Get product meta
	 *
	 * @param      pId
	 * @return     types
	 */
	public function getProductMeta($pId)
	{
		$warehouse = new Warehouse();
		$meta = $warehouse->getProductMeta($pId, true);

		$metaObj = new \stdClass();
		foreach ($meta as $key => $m)
		{
			$metaObj->$key = $m->pmValue;
		}

		return $metaObj;
	}

	/**
	 * Set product meta
	 *
	 * @param      pId
	 * @param      array Meta
	 * @return     types
	 */
	public function setProductMeta($pId, $meta)
	{
		$warehouse = new Warehouse();
		$meta = $warehouse->setProductMeta($pId, $meta);
	}

	/* SKUs */
	/**
	 * Get a count or list of skus
	 *
	 * @param      string  	$rtrn    What data to return
	 * @param      int   	$pId products
	 * @param      array   	$filters Filters to apply to data retrieval
	 * @return     mixed
	 */
	public function skus($rtrn='list', $pId, $filters = array())
	{
		if (!isset($filters['sort']))
		{
			$filters['sort'] = 'title';
		}
		if (!isset($filters['sort_Dir']))
		{
			$filters['sort_Dir']  = 'ASC';
		}

		$warehouse = new Warehouse();

		switch (strtolower($rtrn))
		{
			case 'count':
				$this->_count = $warehouse->getProductSkus($pId, 'count', false);
				return $this->_count;
				break;

			case 'list':
			case 'rows':
			case 'results':
			default:
				if (!$results = $warehouse->getProductSkus($pId, 'rows', false))
				{
					$results = array();
				}
				else
				{
					// Get SKUs info
					$results = $warehouse->getSkusInfo($results, true, $filters);

					// Strip to just info
					$resultsPlain = array();
					foreach ($results as $k => $res)
					{
						$resultsPlain[] = $res['info'];
					}
					$results = $resultsPlain;
				}

				$this->_products = new \Hubzero\Base\ItemList($results);
				return $this->_products;
		}
	}

	public function sku($sId)
	{
		$warehouse = new Warehouse();

		if ($sId)
		{
			$skuInfo = $warehouse->getSkuInfo($sId);
			$productType = $warehouse->getProductTypeInfo($skuInfo['info']->ptId)['ptName'];
		}

		// Initialize the correct SKU
		if (!empty($productType) && $productType == 'Software Download')
		{
			require_once(__DIR__ . DS . 'SoftwareSku.php');
			$sku = new SoftwareSku($sId);
		}
		else
		{
			require_once(__DIR__ . DS . 'Sku.php');
			$sku = new Sku($sId);
		}
		return $sku;
	}

	/**
	 * Update SKU info
	 *
	 * @param      int 		$sku SKU
	 * @param      array 	$fields New info
	 * @return     throws exception
	 */
	public function updateSku($sku, $fields)
	{
		$checkIntegrity = true;
		//print_r($fields); die;
		if (isset($fields['sPrice']))
		{
			$sku->setPrice($fields['sPrice']);
		}
		if (isset($fields['sAllowMultiple']))
		{
			$sku->setAllowMultiple($fields['sAllowMultiple']);
		}
		if (isset($fields['sTrackInventory']))
		{
			$sku->setTrackInventory($fields['sTrackInventory']);
		}
		if (isset($fields['sInventory']) && $fields['sInventory'])
		{
			$sku->setInventoryLevel($fields['sInventory']);
		}
		if (isset($fields['sSku']))
		{
			$sku->setName($fields['sSku']);
		}
		if (isset($fields['state']))
		{
			$sku->setActiveStatus($fields['state']);
			if (!$fields['state'])
			{
				$checkIntegrity = false;
			}
		}
		if (isset($fields['options']))
		{
			$sku->setOptions($fields['options']);
		}

		// Meta
		if (isset($fields['meta']))
		{
			foreach ($fields['meta'] as $metaKey => $metaVal)
			{
				$sku->addMeta($metaKey, $metaVal);
			}
		}

		// Before saving SKU, check the for possible conflicts (integrity check) except when the SKU gets unpublished
		if ($checkIntegrity)
		{
			require_once(dirname(__DIR__) . DS . 'helpers' . DS . 'Integrity.php');
			$integrityCheck = \Integrity::skuIntegrityCheck($sku);

			if ($integrityCheck->status != 'ok')
			{
				$errorMessage = "Integrity check error:";
				foreach ($integrityCheck->errors as $error)
				{
					$errorMessage .= '<br>' . $error;
				}
				throw new \Exception($errorMessage);
			}
		}

		$sku->save();
		return $sku;
	}

	/* *************************************** */
	// Options

	/**
	 * Get a count or list of options
	 *
	 * @param      string  	$rtrn	What data to return
	 * @param      int   	$ogId 	Option group Id
	 * @param      array   	$filters Filters to apply to data retrieval
	 * @return     mixed
	 */
	public function options($rtrn = 'rows', $ogId, $filters = array())
	{
		if (!isset($filters['sort']))
		{
			$filters['sort'] = 'title';
		}
		if (!isset($filters['sort_Dir']))
		{
			$filters['sort_Dir']  = 'ASC';
		}

		$warehouse = new Warehouse();

		switch (strtolower($rtrn))
		{
			case 'count':
				return $warehouse->getOptionGroupOptions($ogId, 'count', false);
				break;

			case 'list':
			case 'rows':
			case 'results':
			default:
				if (!$results = $warehouse->getOptionGroupOptions($ogId, 'rows', false, $filters))
				{
					$results = array();
				}

				return new \Hubzero\Base\ItemList($results);
		}
	}

	public function option($oId)
	{
		require_once(__DIR__ . DS . 'Option.php');
		$option = new Option($oId);

		//print_r(($option)); die;
		return $option;
	}

	/**
	 * Update option info
	 *
	 * @param      int 		Option ID
	 * @param      array 	$fields New info
	 * @return     throws exception
	 */
	public function updateOption($oId, $fields)
	{
		$option = $this->option($oId);

		if (isset($fields['oName']))
		{
			$option->setName($fields['oName']);
		}
		if (isset($fields['state']))
		{
			$option->setActiveStatus($fields['state']);
		}
		if (isset($fields['ogId']))
		{
			$option->setOptionGroupId($fields['ogId']);
		}

		$option->save();
		return $option;
	}

	/* *************************************** */
	// Categories

	/**
	 * Get a count or list of categories
	 *
	 * @param      string  $rtrn    What data to return
	 * @param      array   $filters Filters to apply to data retrieval
	 * @param      boolean $boolean Clear cached data?
	 * @return     mixed
	 */
	public function categories($rtrn='list', $filters=array())
	{
		if (!isset($filters['sort']))
		{
			$filters['sort'] = 'title';
		}
		if (!isset($filters['sort_Dir']))
		{
			$filters['sort_Dir']  = 'ASC';
		}

		switch (strtolower($rtrn))
		{
			case 'count':
				$warehouse = new Warehouse();
				$count = $warehouse->getCategories('count');
				return $count;
				break;

			case 'list':
			case 'rows':
			case 'results':
			default:
				$warehouse = new Warehouse();
				if (!$results = $warehouse->getCategories('rows', $filters))
				{
					$results = array();
				}
				$categories = new \Hubzero\Base\ItemList($results);
				return $categories;
				break;
		}
	}

	public function collections($rtrn='list', $filters=array())
	{
		if (!isset($filters['sort']))
		{
			$filters['sort'] = 'title';
		}
		if (!isset($filters['sort_Dir']))
		{
			$filters['sort_Dir']  = 'ASC';
		}

		switch (strtolower($rtrn))
		{
			case 'count':
				$warehouse = new Warehouse();
				$count = $warehouse->getCollections('count');
				return $count;
				break;

			case 'list':
			case 'rows':
			case 'results':
			default:
				$warehouse = new Warehouse();
				if (!$results = $warehouse->getCollections('rows', $filters))
				{
					$results = array();
				}
				$categories = new \Hubzero\Base\ItemList($results);
				return $categories;
				break;
		}
	}

	public function optionGroup($ogId)
	{
		require_once(__DIR__ . DS . 'OptionGroup.php');
		$optionGroup = new OptionGroup($ogId);

		//print_r(($optionGroup)); die;
		return $optionGroup;
	}

	public function optionGroups($rtrn='list', $filters=array())
	{
		if (isset($filters['sort']))
		{
			if ($filters['sort'] == 'title')
			{
				$filters['sort'] = 'ogName';
			}
			if ($filters['sort'] == 'state')
			{
				$filters['sort'] = 'ogActive';
			}
		}
		if (!isset($filters['sort_Dir']))
		{
			$filters['sort_Dir']  = 'ASC';
		}

		switch (strtolower($rtrn))
		{
			case 'count':
				$warehouse = new Warehouse();
				$count = $warehouse->getOptionGroups('count');
				return $count;
				break;

			case 'list':
			case 'rows':
			case 'results':
			default:
				$warehouse = new Warehouse();
				if (!$results = $warehouse->getOptionGroups('rows', $filters))
				{
					$results = array();
				}
				$categories = new \Hubzero\Base\ItemList($results);
				return $categories;
				break;
		}
	}

	/**
	 * Update option group info
	 *
	 * @param      int 		$ogId Option Group ID
	 * @param      array 	$fields New info
	 * @return     throws exception
	 */
	public function updateOptionGroup($ogId, $fields)
	{
		require_once(__DIR__ . DS . 'OptionGroup.php');
		$optionGroup = new OptionGroup($ogId);

		//print_r($fields);die;

		if (isset($fields['ogName']))
		{
			$optionGroup->setName($fields['ogName']);
		}
		if (isset($fields['state']))
		{
			$optionGroup->setActiveStatus($fields['state']);
		}

		$optionGroup->save();
		return $optionGroup;
	}

	public function getProductOptions($pId)
	{
		$sql = "SELECT og.*, o.oId, o.oName, o.oActive FROM
				`#__storefront_product_option_groups` pog
				JOIN `#__storefront_option_groups` og on pog.ogId = og.ogId
				LEFT JOIN `#__storefront_options` o on o.ogId = og.ogId
				WHERE pog.pId = {$pId}
				-- AND (ogActive IS NULL OR ogActive = 1)
				ORDER BY og.ogName, o.oName";


		$this->_db->setQuery($sql);
		//print_r($this->_db->replacePrefix( (string) $sql )); die;
		$this->_db->execute();
		$res = $this->_db->loadObjectList();

		// reformat it a little
		$optionGroups = array();
		$og = '';

		foreach ($res as $option)
		{
			//print_r($option); die;
			if ($og != $option->ogId) {
				$og = $option->ogId;
				$optionGroups[$og] = new \stdClass();
				$optionGroups[$og]->ogId = $option->ogId;
				$optionGroups[$og]->ogName = $option->ogName;
				$optionGroups[$og]->options = array();
			}

			if ($option->oId)
			{
				$opt = new \stdClass();
				$opt->oId = $option->oId;
				$opt->oName = $option->oName;
				$opt->oActive = $option->oActive;

				$optionGroups[$og]->options[] = $opt;
			}
			else {
				$optionGroups[$og]->options = array();
			}


		}
		return $optionGroups;
	}

	/**
	 * Get category info
	 *
	 * @param      int $cId Category ID
	 * @return     mixed category info
	 */
	public function category($cId)
	{
		$warehouse = new Warehouse();
		$cInfo = $warehouse->getCollectionInfo($cId, true);
		return $cInfo;
	}

}