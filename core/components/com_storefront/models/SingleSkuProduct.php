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

require_once(__DIR__ . DS . 'Product.php');
require_once(__DIR__ . DS . 'Sku.php');
require_once(__DIR__ . DS . 'Warehouse.php');

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
	public function __construct()
	{
		parent::__construct();

		// Create SKU automatically
		$this->setSku(new Sku());
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
		if (sizeof($sku) != 1)
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