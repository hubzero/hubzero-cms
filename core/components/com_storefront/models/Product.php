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

use Components\Storefront\Models\Warehouse;
use Exception;
use Filesystem;

require_once(__DIR__ . DS . 'Warehouse.php');

/**
 *
 * Storefront product class
 *
 */
class Product
{
	// Product data container
	var $data;

	/**
	 * Constructor
	 *
	 * @param  int		Product ID
	 * @return void
	 */
	public function __construct($pId = false)
	{
		// Load language file
		\App::get('language')->load('com_storefront');

		$this->data = new \stdClass();

		if (isset($pId) && is_numeric($pId) && $pId)
		{
			$this->setId($pId);
			$this->load();
		}
	}

	/**
	 * Load existing product
	 *
	 * @param	void
	 * @return	void		Throws exception if product cannot be loaded
	 */
	private function load()
	{
		$db = \App::get('db');
		$pId = $this->getId();

		// Get all product info
		$sql = "SELECT p.*, pt.ptName, pt.ptModel FROM `#__storefront_products` p
 				LEFT JOIN `#__storefront_product_types` pt ON pt.ptId = p.ptId
 				WHERE p.`pId` = " . $db->quote($pId);
		$db->setQuery($sql);
		$productInfo = $db->loadObject();

		if (empty($productInfo))
		{
			throw new \Exception(Lang::txt('Error loading product'));
		}
		$this->setType($productInfo->ptId);
		$this->setName($productInfo->pName);
		if (!empty($productInfo->pAlias))
		{
			$this->setAlias($productInfo->pAlias);
		}
		$this->setDescription($productInfo->pDescription);
		$this->setFeatures($productInfo->pFeatures);
		$this->setTagline($productInfo->pTagline);
		$this->setActiveStatus($productInfo->pActive);
		$this->setAccessLevel($productInfo->access);
		$this->setAllowMultiple($productInfo->pAllowMultiple);
	}

	/**
	 * Set product type
	 *
	 * @param	string		Product type
	 * @return	void		Throws exception if the produt type is bad
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

		throw new \Exception(Lang::txt('COM_STOREFRONT_INVALID_PRODUCT_TYPE'));
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


	/* ****************************** Collections ******************************* */

	/**
	 * Get product collections
	 *
	 * @param	void
	 * @return	array		collection IDs
	 */
	public function getCollections()
	{
		if (!isset($this->data->collections))
		{
			if ($this->getId())
			{
				$db = \App::get('db');
				$sql = "SELECT `cId` FROM `#__storefront_product_collections` WHERE `pId` = " . $db->quote($this->getId());
				$db->setQuery($sql);
				$collections = $db->loadColumn();
				$this->setCollections($collections);
			}
			else
			{
				return array();
			}
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
		$this->data->collections = array_unique($collections);
	}

	/**
	 * Add product to collection
	 *
	 * @param	int		collection ID
	 * @return	bool	true
	 */
	public function addToCollection($cId)
	{
		$collections = $this->getCollections();
		$collections[] = $cId;
		$this->setCollections($collections);
		return true;
	}


	/* ****************************** SKUs ******************************* */

	/**
	 * Get product skus
	 *
	 * @param	void
	 * @return	array		product SKUs
	 */
	public function getSkus()
	{
		if (!isset($this->skus))
		{
			if ($this->getId())
			{
				$db = \App::get('db');
				$sql = "SELECT `sId` FROM `#__storefront_skus` WHERE `pId` = " . $db->quote($this->getId());
				$db->setQuery($sql);
				$db->execute();

				$skuIds = $db->loadColumn();
				$skus = array();

				// Find out product type to instantiate the correct object
				// software
				if ($this->getType() == 30)
				{
					//include_once(JPATH_ROOT . DS . 'components' . DS . 'com_storefront' . DS . 'models' . DS . 'SoftwareSku.php');
					require_once(__DIR__ . DS . 'SoftwareSku.php');
					$instanceName = '\Components\Storefront\Models\SoftwareSku';
				}
				else
				{
					require_once(__DIR__ . DS . 'Sku.php');
					$instanceName = '\Components\Storefront\Models\Sku';
				}

				foreach ($skuIds as $sId)
				{
					//print_r($instanceName); die;
					$sku = new $instanceName($sId);
					$skus[] = $sku;
				}
				$this->setSkus($skus, false);
			}
			else
			{
				return array();
			}
		}
		return $this->skus;
	}

	// Removes all old SKUs sets given SKUs
	private function setSkus($skus, $deleteOld = true)
	{
		// Delete all skus that are not part of the new SKUs
		if ($deleteOld)
		{
			$newSkuIds = array();
			foreach ($skus as $sku)
			{
				$newSkuIds[] = $sku->getId();
				$sku->setProductId($this->getId());
			}

			$oldSkus = $this->getSkus();
			foreach ($oldSkus as $oldSku)
			{
				if (!in_array($oldSku->getId(), $newSkuIds))
				{
					$oldSku->delete();
				}
			}
		}
		$this->skus = $skus;
	}

	/**
	 * Sets a new SKU for the product, used by single SKU products, removes all other SKUs from the product
	 *
	 * @param	StorefrontModelSku
	 * @return	void
	 */
	protected function setSku($sku)
	{
		if (!($sku instanceof Sku))
		{
			throw new \Exception(Lang::txt('Bad SKU. Unable to add.'));
		}

		$this->setSkus(array($sku));
	}

	public function addSku($sku)
	{
		if (!($sku instanceof Sku))
		{
			throw new \Exception(Lang::txt('Bad SKU. Unable to add.'));
		}

		// Set SKU product ID to this product
		$sku->setProductId($this->getId());
		$sku->save();

		$skus = $this->getSkus();
		$skus[] = $sku;
		$this->setSkus($skus);
	}

	/**
	 * Set product id
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
	 * Set product alias
	 *
	 * @param	string		Product alias
	 * @return	bool		true
	 */
	public function setAlias($pAlias)
	{
		// Check if the alias is valid
		$badAliasException = new \Exception('Bad product alias. Alias should be a non-empty non-numeric alphanumeric string.');
		if (preg_match("/^[0-9a-zA-Z]+[\-_0-9a-zA-Z]*$/i", $pAlias))
		{
			if (is_numeric($pAlias))
			{
				throw $badAliasException;
			}
			$this->data->alias = $pAlias;
			return true;
		}
		throw $badAliasException;
	}

	/**
	 * Get product alias
	 *
	 * @param	void
	 * @return	string		Product alias
	 */
	public function getAlias()
	{
		if (empty($this->data->alias))
		{
			return false;
		}
		return $this->data->alias;
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
		if (empty($this->data->description))
		{
			return false;
		}
		return $this->data->description;
	}

	/**
	 * Set product features
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

	/* ****************************** Images ******************************* */

	/**
	 * Get product images
	 *
	 * @param	void
	 * @return	array		Product images
	 */
	public function getImages($forceReload = false)
	{
		if (!isset($this->data->images) || $forceReload)
		{
			if ($this->getId())
			{
				// Get product image(s)
				$db = \App::get('db');
				$sql = "SELECT imgId, imgName FROM `#__storefront_images`
				WHERE `imgObject` = 'product'
				AND `imgObjectId` = " . $db->quote($this->getId()) . "
				ORDER BY `imgPrimary` DESC";
				$db->setQuery($sql);
				$images = $db->loadObjectList();
				$this->setImages($images);
			}
			else
			{
				return array();
			}
		}
		return $this->data->images;
	}

	/**
	 * Get primary image
	 *
	 * @param	void
	 * @return	obj		image info
	 */
	public function getImage()
	{
		$images = $this->getImages();

		return (empty($images) ? NULL : $images[0]);
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
		$image = new \stdClass();
		$image->imgName = $img;
		$this->setImages(array($image));
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
		$this->setImages(array_merge($this->getImages(), $img));
		return true;
	}

	/**
	 * Add primary image (the first image in the array)
	 *
	 * @param	string		Product image name
	 * @return	bool		true
	 */
	public function addImage($img)
	{
		$images = $this->getImages();
		if (!empty($images[0]))
		{
			// Move the current primary image to the end
			$images[] = $images[0];
		}
		$primaryImage = new \stdClass();
		$primaryImage->imgName = $img;
		// Set the new image as primary
		$images[0] = $primaryImage;
		return $this->setImages($images);
	}

	/**
	 * Remove image
	 *
	 * @param	int			Image ID
	 * @return	bool		Succes of Failure
	 */
	public function removeImage($imgId)
	{
		$images = $this->getImages();
		if (empty($images))
		{
			return false;
		}

		foreach ($images as $key => $img)
		{
			if ($imgId == $img->imgId)
			{
				unset($this->data->images[$key]);

				// Remove the actual file
				$config = Component::params('com_storefront');
				$imgWebPath = trim($config->get('imagesFolder', '/site/storefront/products'), DS);
				$path = PATH_ROOT . DS . $imgWebPath . DS . $this->getId();

				Filesystem::delete($path . DS . $img->imgName);
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

	/* ****************************** Option groups ******************************* */

	public function getOptionGroups()
	{
		if (!isset($this->data->optionGroups))
		{
			if ($this->getId())
			{
				$db = \App::get('db');

				$sql = "SELECT ogId
						FROM `#__storefront_product_option_groups` pog
						WHERE pId = " . $this->getId();

				$db->setQuery($sql);
				$db->execute();
				$res = $db->loadColumn();
				$this->setOptionGroups($res);
			}
			else {
				return array();
			}
		}
		return $this->data->optionGroups;
	}

	public function setOptionGroups($ogIds)
	{
		$this->data->optionGroups = array_unique($ogIds);
		return true;
	}

	/**
	 * Add option group
	 *
	 * @param	int		option group ID
	 * @return	bool	true
	 */
	public function addOptionGroup($ogId)
	{
		$optionGroups = $this->getOptionGroups();
		$optionGroups[] = $ogId;
		$this->setOptionGroups($optionGroups);
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
	 * @param  	void
	 * @return 	void
	 */
	public function verify()
	{
		if (empty($this->data->name))
		{
			throw new \Exception(Lang::txt('No product name set'));
		}

		// Check if the alias is used by another product (only if gets published, allow duplicates for unpublished items)
		$warehouse = new Warehouse();
		if ($this->getAlias() && $this->getActiveStatus())
		{
			$otherProduct = $warehouse->checkProduct($this->getAlias(), true);
			if ($otherProduct->status > 0 && $otherProduct->pId != $this->getId())
			{
				throw new \Exception(Lang::txt('There is another product with the same alias. Alias cannot be set.'));
			}
		}

		return true;
	}

	/**
	 * Add product to the warehouse -- TODO: seems like something that needs to go soon
	 *
	 * @param  void
	 * @return object	info
	 */
	public function add()
	{
		$this->verify();

		$warehouse = new Warehouse();

		$productAdded = $warehouse->addProduct($this);
		$this->setId($productAdded->pId);
		return($productAdded);
	}

	/**
	 * Update product info -- TODO: phase it out -- use save() instead
	 *
	 * @param  void
	 * @return object	info
	 */
	public function update()
	{
		$this->verify();

		$warehouse = new Warehouse();

		$return = $warehouse->updateProduct($this);

		$this->load();

		return($return);
	}

	/**
	 * Save product
	 *
	 * @param  	void
	 * @return 	bool		true
	 */
	public function save()
	{
		$this->verify();

		$pId = $this->getId();

		if ($pId)
		{
			$sql = "UPDATE `#__storefront_products` SET ";
		}
		else
		{
			$sql = "INSERT INTO `#__storefront_products` SET ";
		}

		$db = \App::get('db');

		$sql .= "
				`ptId` = " . $db->quote($this->getType()) . ",
				`pName` = " . $db->quote($this->getName()) . ",
				`pAlias` = " . $db->quote($this->getAlias()) . ",
				`pTagline` = " . $db->quote($this->getTagline()) . ",
				`pDescription` = " . $db->quote($this->getDescription()) . ",
				`pFeatures` = " . $db->quote($this->getFeatures()) . ",
				`pAllowMultiple` = " . $db->quote($this->getAllowMultiple()) . ",
				`pActive` = " . $db->quote($this->getActiveStatus()) . ",
				`access` = " . $db->quote($this->getAccessLevel());


		// Set pId if needed if adding new product
		if (!$pId)
		{
			$sql .= ",
				`pId` = " . $db->quote($pId);
		}
		else
		{
			$sql .= " WHERE `pId` = " . $db->quote($pId);
		}

		$db->setQuery($sql);
		$db->query();
		if (!$pId)
		{
			$pId = $db->insertid();
			$this->setId($pId);
		}

		// ### Do collections
		$collections = $this->getCollections();

		$affectedCollectionIds = array();
		if (!empty($collections))
		{
			foreach ($collections as $cId)
			{
				$sql = "SET @collectionId := 0";
				$db->setQuery($sql);
				$db->query();

				$sql = "INSERT INTO `#__storefront_product_collections` SET
						`cId` = " . $db->quote($cId) . ",
						`pId` = " . $db->quote($pId) . "
						ON DUPLICATE KEY UPDATE
						`pcId` = (@collectionId := `pcId`),
						`cId` = " . $db->quote($cId) . ",
						`pId` = " . $db->quote($pId);

				$db->setQuery($sql);
				$db->query();

				$sql = "SELECT IF(@collectionId = 0, LAST_INSERT_ID(), @collectionId)";
				$db->setQuery($sql);
				$db->query();

				$affectedCollectionIds[] = $db->loadResult();
			}
		}

		// Delete unused collections
		$deleteSql = '(0';
		foreach ($affectedCollectionIds as $activeCllId)
		{
			$deleteSql .= ", " . $db->quote($activeCllId);
		}
		$deleteSql .= ')';
		$sql = "DELETE FROM `#__storefront_product_collections` WHERE `pId` = " . $db->quote($pId) . " AND `pcId` NOT IN {$deleteSql}";
		$db->setQuery($sql);
		$db->query();

		// ### Do images
		$images = $this->getImages();

		// First delete all old references
		$sql = "DELETE FROM `#__storefront_images` WHERE `imgObject` = 'product' AND `imgObjectId` = " . $db->quote($pId);
		$db->setQuery($sql);
		$db->query();

		if (!empty($images))
		{
			$firstImage = true;
			foreach ($images as $key => $img)
			{
				$primary = 0;
				if ($firstImage)
				{
					$primary = 1;
					$firstImage = false;
				}
				$sql = "INSERT INTO `#__storefront_images` SET
						`imgName` = " . $db->quote($img->imgName) . ",
						`imgObject` = 'product',
						`imgObjectId` = " . $db->quote($pId) . ",
						`imgPrimary` = " . $primary;
				$db->setQuery($sql);
				$db->query();
			}

			// Refresh object's images info to get the latest image IDs
			$this->getImages(true);
		}

		// ### Do option groups
		// erase all old option groups
		$sql = "DELETE FROM `#__storefront_product_option_groups` WHERE `pId` = " . $db->quote($pId);
		$db->setQuery($sql);
		$db->query();

		foreach ($this->getOptionGroups() as $ogId) {
			$sql = "INSERT INTO `#__storefront_product_option_groups` (pId, ogId)
					VALUES (" . $db->quote($pId) . ", " . $db->quote($ogId) . ")";
			$db->setQuery($sql);
			$db->execute();
		}

		// Finally, since the product updates can potentially affect other elements of the storefront, update dependencies
		$this->updateDependencies();

		return true;
	}

	/**
	 * Delete the product
	 *
	 * @param	void
	 * @return 	true on success, throws exception on failure
	 */
	public function delete()
	{
		$db = \App::get('db');

		// First get all SKUs to delete later
		$skus = $this->getSkus();

		// Delete product record
		$sql = 'DELETE FROM `#__storefront_products` WHERE `pId` = ' . $db->quote($this->getId());
		$db->setQuery($sql);
		//print_r($db->replacePrefix($db->getQuery()));
		$db->query();

		// Delete product-related files (product image)
		$config = Component::params('com_storefront');
		$imgWebPath = trim($config->get('imagesFolder', '/site/storefront/products'), DS);
		$dir = PATH_APP . DS . $imgWebPath . DS . $this->getId();

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

		// Delete images from database
		$sql = "DELETE FROM `#__storefront_images` WHERE `imgObject` = 'product' AND `imgObjectId` = " . $db->quote($this->getId());
		$db->setQuery($sql);
		$db->query();


		// Delete all SKUs
		foreach ($skus as $sku)
		{
			//print_r($sku);
			$sku->delete();
		}

		// Delete product-collection relations
		$sql = 'DELETE FROM `#__storefront_product_collections` WHERE `pId` = ' . $db->quote($this->getId());
		$db->setQuery($sql);
		$db->query();

		// Delete product meta
		$sql = 'DELETE FROM `#__storefront_product_meta` WHERE `pId` = ' . $db->quote($this->getId());
		$db->setQuery($sql);
		$db->query();

		// Delete prduct-option groups relations
		$sql = 'DELETE FROM `#__storefront_product_option_groups` WHERE `pId` = ' . $db->quote($this->getId());
		$db->setQuery($sql);
		$db->query();
	}

	private function updateDependencies()
	{
		// Check all active product SKUs and disable those that do not verify anymore
		$skus = $this->getSkus();
		$skusDisabled = false;
		foreach ($skus as $sku)
		{
			if ($sku->getActiveStatus())
			{
				try
				{
					$sku->verify();
				}
				catch (\Exception $e)
				{
					$sku->unpublish();
					$skusDisabled = true;
				}
			}
		}

		if ($skusDisabled)
		{
			$this->addMessage('Some SKUs were unpublished because of the recent update. Check each SKU to fix the issues.');
		}
	}

	private function addMessage($msg)
	{
		$this->data->messages[] = $msg;
	}

	public function getMessages()
	{
		if (empty($this->data->messages))
		{
			return false;
		}
		return $this->data->messages;
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
		$db = \App::get('db');

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
		$db = \App::get('db');

		foreach ($meta as $key => $val)
		{
			if ($key != 'pId')
			{
				$sql  = "	INSERT INTO `#__storefront_product_meta` (`pmKey`, `pmValue`, `pId`)
							VALUES (" . $db->quote($key) . ", " . $db->quote($val) . ", " . $db->quote($pId) . ")
	  						ON DUPLICATE KEY UPDATE `pmValue` = " . $db->quote($val);
				$db->setQuery($sql);
				$db->query();
			}
		}
	}

}