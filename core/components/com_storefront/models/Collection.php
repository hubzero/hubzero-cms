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
use Exception;

require_once(__DIR__ . DS . 'Warehouse.php');

/**
 *
 * Storefront collection class
 *
 */
class Collection
{
	// Collection data container
	var $data;

	/**
	 * Constructor
	 *
	 * @param  int		Optional: Collection ID
	 * @return void
	 */
	public function __construct($cId = false)
	{
		// Load language file
		\App::get('language')->load('com_storefront');

		$this->data = new \stdClass();

		if (isset($cId) && $cId && is_numeric($cId))
		{
			$this->setId($cId);
			$this->load();
		}
	}

	public function load()
	{
		$db = \App::get('db');

		$sql = "SELECT * FROM `#__storefront_collections` c
 				WHERE c.`cId` = " . $db->quote($this->getId());
		$db->setQuery($sql);
		$cInfo = $db->loadObject();

		if ($cInfo)
		{
			$this->setId($cInfo->cId);
			if (!empty($cInfo->cAlias))
			{
				$this->setAlias($cInfo->cAlias);
			}
			$this->setName($cInfo->cName);
			$this->setActiveStatus($cInfo->cActive);
			$this->setType($cInfo->cType);
		}
		else
		{
			throw new \Exception(Lang::txt('Error loading collection'));
		}
	}

	/**
	 * Set collection type
	 *
	 * @param	string		Collection type
	 * @return	bool		true on success, exception otherwise
	 */
	public function setType($cType)
	{
		$allowedTypes = array('category', 'collection');

		if (!in_array($cType, $allowedTypes))
		{
			throw new \Exception(Lang::txt('COM_STOREFRONT_INVALID_CATEGORY_TYPE'));
		}

		$this->data->type = $cType;
		return true;
	}

	/**
	 * Get collection type
	 *
	 * @param	void
	 * @return	int		Collection type
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
	 * Set collection id
	 *
	 * @param	int			collection ID
	 * @return	bool		true
	 */
	public function setId($cId)
	{
		$this->data->id = $cId;
		return true;
	}

	/**
	 * Get collection id (if set)
	 *
	 * @param	void
	 * @return	int		collection ID
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
	 * Set collection name
	 *
	 * @param	string		collection name
	 * @return	bool		true
	 */
	public function setName($cName)
	{
		$this->data->name = $cName;
		return true;
	}

	/**
	 * Get collection name
	 *
	 * @param	void
	 * @return	string		collection name
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
	 * Set collection alias
	 *
	 * @param	string		collection alias
	 * @return	bool		true
	 */
	public function setAlias($cAlias)
	{
		// Check if the alias is valid
		$badAliasException = new Exception('Bad collection alias. Alias should be a non-empty non-numeric alphanumeric string.');
		if (preg_match("/^[0-9a-zA-Z]+[\-_0-9a-zA-Z]*$/i", $cAlias))
		{
			if (is_numeric($cAlias))
			{
				throw $badAliasException;
			}
			$this->data->alias = $cAlias;
			return true;
		}
		throw $badAliasException;
	}

	/**
	 * Get collection alias
	 *
	 * @param	void
	 * @return	string		collection alias
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
	 * Set product collection status
	 *
	 * @param	bool		collection status
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
	 * Get collection active status
	 *
	 * @param	void
	 * @return	bool		collection status
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
	 * Check if everything checks out and the collection is ready to go
	 *
	 * @param  void
	 * @return bool		true on success, throws exception on failure
	 */
	public function verify()
	{
		if (empty($this->data->name))
		{
			throw new \Exception(Lang::txt('No collection name set'));
		}

		if (empty($this->data->type))
		{
			throw new \Exception(Lang::txt('No collection type set'));
		}
		return true;
	}

	/**
	 * Add collection to the warehouse
	 *
	 * @param  void
	 * @return object	info
	 */
	public function add()
	{
		$this->verify();

		$warehouse = new Warehouse();

		return($warehouse->addCollection($this));
	}

	/**
	 * Update collection info
	 * TODO: remove it and use save()
	 *
	 * @param  void
	 * @return object	info
	 */
	public function update()
	{
		$this->save();
		return $this->getId();
	}

	/**
	 * Save collection info
	 *
	 * @param  void
	 * @return object	info
	 */
	public function save()
	{
		$db = \App::get('db');

		$action = 'update';
		if (!$this->getId())
		{
			$action = 'add';
		}

		if ($action == 'update')
		{
			$sql = "UPDATE `#__storefront_collections` SET ";
		}
		elseif ($action == 'add')
		{
			$sql = "INSERT INTO `#__storefront_collections` SET ";
		}

		if (!$alias = $db->quote($this->getAlias()))
		{
			$alias = 'NULL';
		}

		$sql .= "
				`cName` = " . $db->quote($this->getName()) . ",
				`cAlias` = " . $alias . ",
				`cActive` = " . $db->quote($this->getActiveStatus()) . ",
				`cType` = " . $db->quote($this->getType());

		if ($action == 'update')
		{
			$sql .= " WHERE `cId` = " . $db->quote($this->getId());
		}
		$db->setQuery($sql);
		$db->query();

		if ($action == 'add')
		{
			// Set ID
			$this->setId($db->insertid());
		}
	}

	/**
	 * Delete the collection
	 *
	 * @param	void
	 * @return	bool	true on success, exception otherwise
	 */
	public function delete()
	{
		$db = \App::get('db');

		// Delete the collection record
		$sql = 'DELETE FROM `#__storefront_collections` WHERE `cId` = ' . $db->quote($this->getId());
		$db->setQuery($sql);
		$db->query();

		// Delete the product-collection relation
		$sql = 'DELETE FROM `#__storefront_product_collections` WHERE `cId` = ' . $db->quote($this->getId());
		$db->setQuery($sql);
		$db->query();

		return true;
	}

	/* ******************************** Static functions ********************************** */

	/**
	 * Delete the collection
	 *
	 * @param	void
	 * @return	bool	true on success, exception otherwise
	 */
	public static function findActiveCollectionByAlias($cAlias)
	{
		$db = \App::get('db');

		$sql = 'SELECT `cId` FROM `#__storefront_collections` c
				WHERE c.`cAlias` = ' . $db->quote($cAlias) . "
				AND c.`cActive` = 1";

		$db->setQuery($sql);
		$cId = $db->loadResult();
		return $cId;
	}

}