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

include_once(JPATH_ROOT . DS . 'components' . DS . 'com_storefront' . DS . 'models' . DS . 'Warehouse.php');
include_once(JPATH_ROOT . DS . 'components' . DS . 'com_storefront' . DS . 'models' . DS . 'Sku.php');

/**
 *
 * Storefront product class
 *
 */
class StorefrontModelProduct
{
	// Product data container
	var $data;
	private $db;

	// Product SKUs
	var $skus = array();

	/**
	 * Constructor
	 *
	 * @param  int		Product ID
	 * @return void
	 */
	public function __construct($pId = false)
	{
		// Load language file
		JFactory::getLanguage()->load('com_storefront');

		$this->data = new stdClass();
		$this->db = JFactory::getDBO();

		if (isset($pId) && is_numeric($pId))
		{
			$this->setId($pId);
			$this->load();
		}
	}

	/**
	 * Load existing product
	 *
	 * @param	void
	 * @return	bool		true on success, exception otherwise
	 */
	private function load()
	{
		// Get all product info
		include_once(JPATH_ROOT . DS . 'components' . DS . 'com_storefront' . DS . 'models' . DS . 'Warehouse.php');
		$warehouse = new StorefrontModelWarehouse();
		$productInfo = $warehouse->getProductInfo($this->getId(), true);

		if ($productInfo)
		{
			$this->setType($productInfo->ptId);
			$this->setName($productInfo->pName);
			$this->setDescription($productInfo->pDescription);
			$this->setFeatures($productInfo->pFeatures);
			$this->setTagline($productInfo->pTagline);
			$this->setActiveStatus($productInfo->pActive);
			$this->setAccessLevel($productInfo->access);
			$this->setAllowMultiple($productInfo->pAllowMultiple);
			$this->setImages($productInfo->images);

			// Get collections
			$collections = $warehouse->getProductCollections($this->getId());
			foreach ($collections as $cId)
			{
				$this->addToCollection($cId);
			}

			// Get SKUs
			$skus = $warehouse->getProductSkus($this->getId(), 'rows', false);

			// Add SKUs to a product
			foreach ($skus as $sId)
			{
				$sku = $warehouse->getSku($sId);
				$this->addSku($sku);
			}
		}
		else
		{
			throw new Exception(JText::_('Error loading product'));
		}
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
		if (empty($this->data->type))
		{
			return false;
		}
		return $this->data->type;
	}

	/**
	 * Add product to collection
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
		if (empty($this->data->collections))
		{
			return array();
		}
		return $this->data->collections;
	}

	/**
	 * Set product collections
	 *
	 * @param	array		collection IDs
	 * @return	void
	 */
	public function setCollections($collections)
	{
		// Reset old collections
		$this->data->collections = array();

		foreach ($collections as $cId)
		{
			$this->addToCollection($cId);
		}
	}

	public function addSku($sku)
	{
		if (!($sku instanceof StorefrontModelSku))
		{
			throw new Exception(JText::_('Bad SKU. Unable to add.'));
		}

		$sku->verify();

		$this->skus[] = $sku;
	}

	/**
	 * Sets a new SKU for the product, used by single SKU products
	 *
	 * @param	StorefrontModelSku
	 * @return	void
	 */
	protected function setSku($sku)
	{
		if (!($sku instanceof StorefrontModelSku))
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
		if (empty($this->data->name))
		{
			return false;
		}
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
	 * Get product features
	 *
	 * @param	void
	 * @return	string		Product description
	 */
	public function getDescription()
	{
		if (empty($this->data->description))
		{
			return false;
		}
		return $this->data->description;
	}

	/**
	 * Set product Features
	 *
	 * @param	string		Product Features
	 * @return	bool		true
	 */
	public function setFeatures($productFeatures)
	{
		$this->data->features = $productFeatures;
		return true;
	}

	/**
	 * Get product Features
	 *
	 * @param	void
	 * @return	string		Product Features
	 */
	public function getFeatures()
	{
		if (empty($this->data->features))
		{
			return false;
		}
		return $this->data->features;
	}

	/**
	 * Set product images
	 *
	 * @param	array		Product images
	 * @return	bool		true
	 */
	public function setImages($img)
	{
		$this->data->images = $img;
		return true;
	}

	public function setImage($img)
	{
		$image = new stdClass();
		$image->imgName = $img;
		$this->data->images = array($image);
		return true;
	}

	/**
	 * Add product images
	 *
	 * @param	array		Product images
	 * @return	bool		true
	 */
	public function addImages($img)
	{
		$this->data->images = array_merge($this->data->images, $img);
		return true;
	}

	/**
	 * Add primary image
	 *
	 * @param	string		Product image name
	 * @return	bool		true
	 */
	public function addImage($img)
	{
		if (!empty($this->data->images[0]))
		{
			$this->data->images[] = $this->data->images[0];
		}
		$image = new stdClass();
		$image->imgName = $img;
		$this->data->images[0] = $image;
		return true;
	}

	/**
	 * Get product image
	 *
	 * @param	void
	 * @return	array		Product image
	 */
	public function getImages()
	{
		if (empty($this->data->images))
		{
			return NULL;
		}
		return $this->data->images;
	}

	public function getImage()
	{
		if (empty($this->data->images))
		{
			return NULL;
		}
		return $this->data->images[0];
	}

	/**
	 * Remove image
	 *
	 * @param	int			Image ID
	 * @return	bool		Succes of Failure
	 */
	public function removeImage($imgId)
	{
		if (empty($this->data->images))
		{
			return false;
		}

		foreach ($this->data->images as $key => $img)
		{
			if ($imgId == $img->imgId)
			{
				unset($this->data->images[$key]);

				// Remove the actual file
				$jconfig = JFactory::getConfig();
				$path = JPATH_ROOT . DS . trim($jconfig->get('imagesFolder', '/site/storefront/products'), DS) . DS . $this->getId();

				JFile::delete($path . DS . $img->imgName);
				return true;
			}
		}
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
	 * Set product multiple flag
	 *
	 * @param	int			allowMultiple
	 * @return	bool		true
	 */
	public function setAllowMultiple($productAllowMiltiple)
	{
		$this->data->allowMiltiple = $productAllowMiltiple;
		return true;
	}

	/**
	 * Get product multiple flag
	 *
	 * @param	void
	 * @return	int		allowMultiple
	 */
	public function getAllowMultiple()
	{
		if (empty($this->data->allowMiltiple))
		{
			return NULL;
		}
		return $this->data->allowMiltiple;
	}

	/**
	 * Set product access level
	 *
	 * @param	int		Product access level
	 * @return	bool		true
	 */
	public function setAccessLevel($accessLevel)
	{
		$this->data->accessLevel = $accessLevel;
		return true;
	}

	/**
	 * Get product access level
	 *
	 * @param	void
	 * @return	int		Product access level
	 */
	public function getAccessLevel()
	{
		if (empty($this->data->accessLevel))
		{
			return 0;
		}
		return $this->data->accessLevel;
	}

	/**
	 * Set product active status
	 *
	 * @param	bool		Product status
	 * @return	bool		true
	 */
	public function setActiveStatus($activeStatus)
	{
		// This is to accommodate admin's 'trashed' value
		// TODO redo it properly to allow trashing
		if ($activeStatus == 2)
		{
			$this->data->activeStatus = 0;
		}
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
	 * @param  void
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

		include_once(JPATH_ROOT . DS . 'components' . DS . 'com_storefront' . DS . 'models' . DS . 'Warehouse.php');
		$warehouse = new StorefrontModelWarehouse();

		$productAdded = $warehouse->addProduct($this);
		$this->setId($productAdded->pId);
		return($productAdded);
	}

	/**
	 * Update product info
	 *
	 * @param  void
	 * @return object	info
	 */
	public function update()
	{
		$this->verify();

		include_once(JPATH_ROOT . DS . 'components' . DS . 'com_storefront' . DS . 'models' . DS . 'Warehouse.php');
		$warehouse = new StorefrontModelWarehouse();

		$return = $warehouse->updateProduct($this);

		$this->load();

		return($return);
	}

	/**
	 * Delete the product
	 *
	 * @param	void
	 * @return 	true on success, throws exception on failure
	 */
	public function delete()
	{
		$this->verify();

		// Delete product record
		$sql = 'DELETE FROM `#__storefront_products` WHERE `pId` = ' . $this->db->quote($this->getId());
		$this->db->setQuery($sql);
		//print_r($this->db->replacePrefix($this->db->getQuery()));
		$this->db->query();

		// Delete product-related files (product image)
		$imgWebPath = DS . 'site' . DS . 'storefront' . DS . 'products' . DS . $this->getId();
		$dir = JPATH_ROOT . $imgWebPath;

		if (file_exists($dir))
		{
			$it = new RecursiveDirectoryIterator($dir, RecursiveDirectoryIterator::SKIP_DOTS);
			$files = new RecursiveIteratorIterator($it,
				RecursiveIteratorIterator::CHILD_FIRST);
			foreach ($files as $file)
			{
				if ($file->isDir())
				{
					rmdir($file->getRealPath());
				}
				else
				{
					unlink($file->getRealPath());
				}
			}
			rmdir($dir);
		}

		// Delete all SKUs
		$skus = $this->getSkus();
		foreach ($skus as $sku)
		{
			//print_r($sku);
			$sku->delete();
		}

		// Delete product-collection relations
		$sql = 'DELETE FROM `#__storefront_product_collections` WHERE `pId` = ' . $this->db->quote($this->getId());
		$this->db->setQuery($sql);
		$this->db->query();

		// Delete product meta
		$sql = 'DELETE FROM `#__storefront_product_meta` WHERE `pId` = ' . $this->db->quote($this->getId());
		$this->db->setQuery($sql);
		$this->db->query();

		// Delete prduct-option groups relations
		$sql = 'DELETE FROM `#__storefront_product_option_groups` WHERE `pId` = ' . $this->db->quote($this->getId());
		$this->db->setQuery($sql);
		$this->db->query();

		//
	}

	/* ************************************* Static functions ***************************************************/

	/**
	 * Get product meta value by name or all product meta values if $metaKey is false
	 *
	 * @param  	int		Product ID
	 * @param	String	Optional: Meta key to get a certain value, if empty returns all product meta
	 * @return 	mixed	Product meta
	 */
	public static function getMeta($pId, $metaKey = false)
	{
		$db = JFactory::getDBO();

		$sql  = 'SELECT ';
		if (!$metaKey)
		{
			$sql .= '`pmKey`, ';
		}
		$sql .= '`pmValue` FROM `#__storefront_product_meta` WHERE `pId` = ' . $db->quote($pId);
		if ($metaKey)
		{
			$sql .= ' AND `pmKey` = ' . $db->quote($metaKey);
		}
		$db->setQuery($sql);
		if ($metaKey)
		{
			$meta = $db->loadResult();
		}
		else
		{
			$meta = $db->loadObjectList();
		}
		return $meta;
	}

	public static function setMeta($pId, $meta)
	{
		$db = JFactory::getDBO();

		foreach ($meta as $key => $val)
		{
			if ($key != 'pId')
			{
				$sql  = "	INSERT INTO `#__storefront_product_meta` (`pmKey`, `pmValue`, `pId`)
							VALUES (" . $db->quote($key) . ", " . $db->quote($val) . ", " . $db->quote($pId) . ")
	  						ON DUPLICATE KEY UPDATE `pmValue` = " . $db->quote($val);
				$db->setQuery($sql);
				//print_r($db->replacePrefix($db->getQuery()));
				$db->query();
			}
		}
	}

	public static function optionGroups($pId)
	{
		$db = JFactory::getDBO();

		$sql = "SELECT ogId
				FROM `#__storefront_product_option_groups` pog
				WHERE pId = {$pId}";

		$db->setQuery($sql);
		$db->execute();
		$optionGroups = $db->loadColumn();
		return $optionGroups;
	}

}