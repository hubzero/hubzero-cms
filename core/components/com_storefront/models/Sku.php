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

include_once(__DIR__ . DS . 'Memberships.php');

/**
 *
 * Storefront SKU class
 *
 */
class StorefrontModelSku
{

	var $data;

	/**
	 * Contructor
	 *
	 * @param  void
	 * @return void
	 */
	public function __construct()
	{
	}

	/**
	 * Set price
	 *
	 * @param	double		price
	 * @return	bool		true on success, exception otherwise
	 */
	public function setPrice($productPrice)
	{
		if (!is_numeric($productPrice))
		{
			throw new Exception(Lang::txt('Price must be numeric'));
		}

		$this->data->price = $productPrice;
		return true;
	}

	public function getPrice()
	{
		return $this->data->price;
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
			return NULL;
		}
		return $this->data->id;
	}

	public function verify()
	{
		if (empty($this->data->price))
		{
			throw new Exception(Lang::txt('No SKU price'));
		}
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

	public function setInventoryLevel($inventoryLevel)
	{
		if (!is_numeric($inventoryLevel))
		{
			throw new Exception(Lang::txt('Bad inventory level value'));
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

	/**
	 * Get SKU active status
	 *
	 * @param	void
	 * @return	bool		SKU status
	 */
	public function getActiveStatus()
	{
		if (!isset($this->data->activeStatus))
		{
			return 'DEFAULT';
		}
		return $this->data->activeStatus;
	}

	public function addMeta($key, $val)
	{
		$this->data->meta[$key] = $val;
	}

	public function getMeta()
	{
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
		StorefrontModelMemberships::checkTtl($ttl);

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

}