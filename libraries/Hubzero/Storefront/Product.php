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

// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die('Restricted access');

/**
 *
 * Storefront product class
 * 
 */
class Hubzero_Storefront_Product
{	
	// Product data container
	var $data;
	
	// Product SKUs
	var $skus = array();
	
	/**
	 * Contructor
	 * 
	 * @param  void
	 * @return void
	 */
	public function __construct()
	{
		// Load language file
		JFactory::getLanguage()->load('com_storefront');
	}
	
	/**
	 * Set product type
	 * 
	 * @param	string		Product type
	 * @return	bool		true on success, exception otherwise
	 */
	public function setType($productType)
	{
		if (is_numeric($productType))
		{
			$this->data->type = $productType;
			return true;
		}
		
		switch (strtolower($productType)) 
		{
			case 'course':
				$this->data->type = 20;
				return true;
				break;
			case 'product':
				$this->data->type = 1;
				return true;
				break;
		}
		
		throw new Exception(JText::_('COM_STOREFRONT_INVALID_PRODUCT_TYPE'));
	}
	
	/**
	 * Get product type
	 * 
	 * @param	void
	 * @return	int		Product type
	 */
	public function getType()
	{
		return $this->data->type;
	}
	
	/**
	 * add product to collection
	 * 
	 * @param	int		collection ID
	 * @return	bool	true
	 */
	public function addToCollection($cId)
	{
		$this->data->collections[] = $cId;	
		return true;
	}
	
	/**
	 * Get product collections
	 * 
	 * @param	void
	 * @return	array		collection IDs
	 */
	public function getCollections()
	{
		return $this->data->collections;	
	}
	
	public function addSku($sku)
	{
		if (!($sku instanceof Hubzero_Storefront_Sku))
		{
			throw new Exception(JText::_('Bad SKU. Unable to add.'));	
		}		
		
		$sku->verify();
		
		$this->skus[] = $sku;
	}
	
	/**
	 * Sets a new SKU for the product, used by single SKU products
	 * 
	 * @param	Hubzero_Storefront_Sku
	 * @return	void
	 */
	protected function setSku($sku) 
	{		
		if (!($sku instanceof Hubzero_Storefront_Sku))
		{
			throw new Exception(JText::_('Bad SKU. Unable to add.'));	
		}		
				
		// Overwrite the existing SKU(s)
		$this->skus = array($sku);
	}
	
	/**
	 * Get product skus
	 * 
	 * @param	void
	 * @return	array		product SKUs
	 */
	public function getSkus()
	{		
		return $this->skus;	
	}
	
	/**
	 * Set product id (used to update product or to create a product with given ID)
	 * 
	 * @param	int			product ID
	 * @return	bool		true
	 */
	public function setId($pId)
	{
		$this->data->id = $pId;
		return true;
	}
	
	/**
	 * Get product id (if set)
	 * 
	 * @param	void
	 * @return	int		product ID
	 */
	public function getId()
	{
		if (!empty($this->data->id))
		{
			return $this->data->id;
		}
		return false;
	}
	
	/**
	 * Set product name
	 * 
	 * @param	string		Product name
	 * @return	bool		true
	 */
	public function setName($productName)
	{
		$this->data->name = $productName;
		return true;
	}
	
	/**
	 * Get product name
	 * 
	 * @param	void
	 * @return	string		Product name
	 */
	public function getName()
	{
		return $this->data->name;
	}
	
	/**
	 * Set product description
	 * 
	 * @param	string		Product description
	 * @return	bool		true
	 */
	public function setDescription($productDescription)
	{
		$this->data->description = $productDescription;
		return true;
	}
	
	/**
	 * Get product description
	 * 
	 * @param	void
	 * @return	string		Product description
	 */
	public function getDescription()
	{
		return $this->data->description;
	}
	
	/**
	 * Set product tagline
	 * 
	 * @param	string		Product tagline
	 * @return	bool		true
	 */
	public function setTagline($productTagline)
	{
		$this->data->tagline = $productTagline;
		return true;
	}
	
	/**
	 * Get product tagline
	 * 
	 * @param	void
	 * @return	string		Product tagline
	 */
	public function getTagline()
	{
		if (empty($this->data->tagline))
		{
			return NULL;	
		}
		return $this->data->tagline;
	}
	
	/**
	 * Set product active status
	 * 
	 * @param	bool		Product status
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
	 * Get product active status
	 * 
	 * @param	void
	 * @return	bool		Product status
	 */
	public function getActiveStatus()
	{
		if (!isset($this->data->activeStatus))
		{
			return 'DEFAULT';
		}
		return $this->data->activeStatus;
	}
	
	/**
	 * Check if everything checks out and the product is ready to go
	 * 
	 * @param  int		courseId
	 * @return bool		true on sucess, throws exception on failure
	 */
	public function verify()
	{		
		if (empty($this->data->name))
		{
			throw new Exception(JText::_('No product name set'));	
		}
		if (empty($this->data->description))
		{
			//throw new Exception(JText::_('No product description set'));	
		}
		
		foreach ($this->skus as $sku)
		{
			$sku->verify();
		}
		
		return true;
	}
	
	/**
	 * Add product to the warehouse
	 * 
	 * @param  void
	 * @return object	info
	 */
	public function add()
	{
		$this->verify();
		
		ximport('Hubzero_Storefront_Warehouse');
		$warehouse = new Hubzero_Storefront_Warehouse();
		
		return($warehouse->addProduct($this));
	}
	
	/**
	 * Update product info
	 * 
	 * @param  void
	 * @return object	info
	 */
	public function update()
	{
		ximport('Hubzero_Storefront_Warehouse');
		$warehouse = new Hubzero_Storefront_Warehouse();		
		
		return($warehouse->updateProduct($this));
	}
		
	/**
	 * Debug
	 * 
	 * @param	void
	 * @return	void
	 */
	public function debug()
	{
		echo "\n\n<br><br>";
		print_r($this->data);
		echo "\n\n<br><br>";
		
		print_r($this->skus);
		echo "\n\n<br><br>";		
	}
	
}

/**
 *
 * Storefront course product class
 * 
 */
class Hubzero_Storefront_Single_Sku_Product extends Hubzero_Storefront_Product
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
		$this->setSku(new Hubzero_Storefront_Sku());
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
		ximport('Hubzero_Storefront_Warehouse');
		$warehouse = new Hubzero_Storefront_Warehouse();
		
		$sku = $warehouse->getProductSkus($this->data->id);
		
		// Must be just one SKU
		if (sizeof($sku) != 1)
		{
			throw new Exception(JText::_('Only one SKU is allowed'));
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

/**
 *
 * Storefront course product class
 * 
 */
class Hubzero_Storefront_Course extends Hubzero_Storefront_Single_Sku_Product
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
		
		// Set type course
		$this->setType('course');
		
		// Set collection to courses
		$this->addToCollection(11);
		
		// Override SKU
		$this->setSku(new Hubzero_Storefront_Course_Offering());
	}
	
	/**
	 * Set the course ID associated with product 
	 * 
	 * @param  string		courseId (alias)
	 * @return bool			true on sucess
	 */
	public function setCourseId($courseId)
	{
		$this->getSku()->setCourseId($courseId);
		return true;
	}
		
}

class Hubzero_Storefront_Sku
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
			throw new Exception(JText::_('Price must be numeric'));	
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
			throw new Exception(JText::_('No SKU price'));	
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
			throw new Exception(JText::_('Bad inventory level value'));	
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
	 * @param	strng		expected MySQL formatted interval values like 1 DAY, 2 MONTH, 3 YEAR
	 * @return	bool		SKU status
	*/
	public function setTimeToLive($ttl)
	{
		ximport('Hubzero_Storefront_Memberships');
		Hubzero_Storefront_Memberships::checkTtl($ttl);
		
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


class Hubzero_Storefront_Course_Offering extends Hubzero_Storefront_Sku
{
	
	public function __construct()
	{
		parent::__construct();
		
		//$this->setAllowMultiple(0);
		$this->setTrackInventory(0);
	}
	
	public function setCourseId($courseId)
	{
		$this->data->courseId = $courseId;
		
		$this->data->meta['courseId'] = $courseId;
	}
		
	public function verify()
	{
		parent::verify();
		
		// Each course has to have a course ID
		if (empty($this->data->courseId))
		{
			throw new Exception(JText::_('No course id'));	
		}	
	}
	
}