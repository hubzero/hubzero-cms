<?php

require_once(JPATH_COMPONENT_SITE . DS . 'models' . DS . 'Warehouse.php');

/**
* Archive model.
*
* @package		Joomla.Administrator
* @subpackage	com_storefront
* @since		1.6
*/
class StorefrontModelArchive extends \Hubzero\Base\Object
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
		$this->_db = JFactory::getDBO();
	}

	/**
	 * Get a count or list of categories
	 *
	 * @param      string  $rtrn    What data to return
	 * @param      array   $filters Filters to apply to data retrieval
	 * @param      boolean $boolean Clear cached data?
	 * @return     mixed
	 */
	public function products($rtrn='list', $filters=array(), $clear=false)
	{
		if (!isset($filters['state']))
		{
			$filters['state']   = \Hubzero\Base\Model::APP_STATE_PUBLISHED;
		}
		if (!isset($filters['access']) && JFactory::getUser()->get('guest'))
		{
			$filters['access']  = 0;
		}
		if (!isset($filters['section']))
		{
			$filters['section'] = 0;
		}
		if (!isset($filters['empty']))
		{
			$filters['empty']   = false;
		}
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
					$warehouse = new StorefrontModelWarehouse();
					$this->_products_count = $warehouse->getProducts('count');
				}
				return $this->_products_count;
				break;

			case 'list':
			case 'rows':
			case 'results':
			default:
				if (!$this->_products instanceof \Hubzero\Base\ItemList || $clear)
				{
					$warehouse = new StorefrontModelWarehouse();
					if ($results = $warehouse->getProducts('rows'))
					{
						foreach ($results as $key => $result)
						{
							//$results[$key] = new KbModelCategory($result);
						}
					}
					else
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
		$warehouse = new StorefrontModelWarehouse();
		$pInfo = $warehouse->getProductInfo($pId, true);
		return $pInfo;
	}

	public function _product($pId, $info = 'full')
	{
		$warehouse = new StorefrontModelWarehouse();
		$pInfo = $warehouse->getProduct($pId);
		if ($info == 'info-only')
		{
			$pInfo = $pInfo->info;
		}
		return $pInfo;
	}

	/**
	 * Update product info
	 *
	 * @param      int 		$pId Product ID
	 * @param      array 	$fields New info
	 * @return     throws exception
	 */
	public function updateProduct($pId, $fields)
	{
		$warehouse = new StorefrontModelWarehouse();
		$product = $warehouse->getProduct($pId);
		$product->setName($fields['pName']);
		$product->setDescription($fields['pDescription']);
		$product->setTagline($fields['pTagline']);

		$product->setAccessLevel($fields['access']);

		$product->update();
	}
}