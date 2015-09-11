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
 * @author    Hubzero
 * @copyright Copyright 2005-2015 HUBzero Foundation, LLC.
 * @license   http://opensource.org/licenses/MIT MIT
 */

// No direct access.
defined('_HZEXEC_') or die();

include_once(__DIR__ . DS . 'Product.php');
include_once(__DIR__ . DS . 'Sku.php');

/**
 *
 * Storefront course product class
 *
 */
class StorefrontModelSingleSkuProduct extends StorefrontModelProduct
{
	/**
	 * Contructor
	 *
	 * @param  void
	 * @return void
	 */
	public function __construct()
	{
		parent::__construct();

		// Create SKU automatically
		$this->setSku(new StorefrontModelSku());
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
		include_once(PATH_CORE . DS . 'components' . DS . 'com_storefront' . DS . 'models' . DS . 'Warehouse.php');
		$warehouse = new StorefrontModelWarehouse();

		$sku = $warehouse->getProductSkus($this->data->id);

		// Must be just one SKU
		if (sizeof($sku) != 1)
		{
			throw new Exception(Lang::txt('Only one SKU is allowed'));
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