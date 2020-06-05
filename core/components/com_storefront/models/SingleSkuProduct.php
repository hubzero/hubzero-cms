<?php
/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Components\Storefront\Models;

require_once __DIR__ . DS . 'Product.php';
require_once __DIR__ . DS . 'Sku.php';
require_once __DIR__ . DS . 'Warehouse.php';

/**
 *
 * Storefront single sku product class
 *
 */
class SingleSkuProduct extends Product
{
	/**
	 * Contructor
	 *
	 * @param  void
	 * @return void
	 */
	public function __construct($pId = false)
	{
		parent::__construct($pId);

		if (!$pId)
		{
			// Create SKU automatically
			$this->setSku(new Sku());
		}
	}

	/**
	 * Set SKU price
	 *
	 * @param	double		price
	 * @return	bool		true on success, exception otherwise
	 */
	public function setPrice($productPrice)
	{
		$this->getSku()->setPrice($productPrice);
		return true;
	}

	/**
	 * Get SKU price
	 *
	 * @param	void
	 * @return	double		price
	 */
	public function getPrice()
	{
		return $this->defaultSku->getPrice();
	}

	public function getSku()
	{
		$skus = $this->getSkus();
		return $skus[0];
	}

	/*
	 * Set time to live
	 *
	 * @param	strng		expected MySQL formatted interval values like 1 DAY, 2 MONTH, 3 YEAR
	 * @return	bool		SKU status
	*/
	public function setTimeToLive($ttl)
	{
		$this->getSku()->setTimeToLive($ttl);
	}

	public function getTimeToLive()
	{
		$this->getSku()->getTimeToLive();
	}

	/**
	 * Update product info
	 *
	 * @param  void
	 * @return object	info
	 */
	public function update()
	{
		// For single product update SKU must save the original SKU ID (since SKU was generated automatically)
		// Find the SKU ID for this product and save
		$warehouse = new Warehouse();

		$sku = $warehouse->getProductSkus($this->data->id);

		// Must be just one SKU
		if (count($sku) != 1)
		{
			throw new \Exception(Lang::txt('Only one SKU is allowed'));
		}

		$skuId = $sku[0];

		// save product sku with the current ID to resave the changes with this ID
		$sku = $this->getSku()->setId($skuId);

		return parent::update();
	}

	public function addSku($sku)
	{
		$this->setSku($sku);
	}
}
