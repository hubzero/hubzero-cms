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

/**
 *
 * Storefront option class
 *
 */
class Option
{
	// Data container
	var $data;
	private $db;

	/**
	 * Constructor
	 *
	 * @param  void
	 * @return void
	 */
	public function __construct($oId = false)
	{
		$this->data = new \stdClass();
		$this->db = \App::get('db');

		if (isset($oId) && is_numeric($oId) && $oId)
		{
			$this->load($oId);
		}
	}

	/**
	 * Load existing option
	 *
	 * @param	int			option ID
	 * @return	bool		true on success, exception otherwise
	 */
	public function load($oId)
	{
		$sql = "SELECT * FROM `#__storefront_options` o
 				WHERE o.`oId` = " . $this->db->quote($oId);

		$this->db->setQuery($sql);
		//print_r($this->_db->replacePrefix($this->_db->getQuery())); die;
		if ($oInfo = $this->db->loadObject())
		{
			$this->setId($oInfo->oId);
			$this->setOptionGroupId($oInfo->ogId);
			$this->setName($oInfo->oName);
			$this->setActiveStatus($oInfo->oActive);
		}
	}

	/**
	 * Set ID
	 *
	 * @param	int			option ID
	 * @return	bool		true
	 */
	public function setId($oId)
	{
		$this->data->id = $oId;
		return true;
	}

	/**
	 * Get ID
	 *
	 * @param	void
	 * @return	int		option ID
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
	 * Set Option Group ID
	 *
	 * @param	int			option group ID
	 * @return	bool		true
	 */
	public function setOptionGroupId($oId)
	{
		$this->data->optionGroupId = $oId;
		return true;
	}

	/**
	 * Get Option Group ID
	 *
	 * @param	void
	 * @return	int		option group ID
	 */
	public function getOptionGroupId()
	{
		if (!empty($this->data->optionGroupId))
		{
			return $this->data->optionGroupId;
		}
		return false;
	}

	/**
	 * Set name
	 *
	 * @param	string		option name
	 * @return	bool		true
	 */
	public function setName($oName)
	{
		$this->data->name = $oName;
		return true;
	}

	/**
	 * Get name
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
	 * Set status
	 *
	 * @param	bool		collection status
	 * @return	bool		true
	 */
	public function setActiveStatus($activeStatus)
	{
		// TODO redo it properly to allow trashing
		if ($activeStatus == 2)
		{
			$this->data->activeStatus = 0;
		}
		elseif ($activeStatus)
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
	 * Get option group active status
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
	 * Check if everything checks out and the option group is ready to go
	 *
	 * @param  void
	 * @return bool		true on success, throws exception on failure
	 */
	public function verify()
	{
		if (empty($this->data->name))
		{
			throw new \Exception(Lang::txt('No option group name set'));
		}
		if (empty($this->data->optionGroupId))
		{
			throw new \Exception(Lang::txt('No option group set'));
		}

		return true;
	}

	/**
	 * Update option group info
	 *
	 * @param  void
	 * @return object	info
	 */
	public function save()
	{
		$action = 'update';
		if (!$this->getId())
		{
			$action = 'add';
		}

		if ($action == 'update')
		{
			$sql = "UPDATE `#__storefront_options` SET ";
		}
		elseif ($action == 'add')
		{
			$sql = "INSERT INTO `#__storefront_options` SET ";
		}

		$sql .= "
				`ogId` = " . $this->db->quote($this->getOptionGroupId()) . ",
				`oName` = " . $this->db->quote($this->getName()) . ",
				`oActive` = " . $this->db->quote($this->getActiveStatus());

		// Add WHERE if updating
		if ($action == 'update')
		{
			$sql .= " WHERE `oId` = " . $this->db->quote($this->getId());
		}

		$this->db->setQuery($sql);
		//print_r($this->db->replacePrefix($this->db->getQuery())); die;
		$this->db->query();
		if ($action == 'add')
		{
			$this->setId($this->db->insertid());
		}
	}

	/**
	 * Delete an option and everything related to it
	 *
	 * @param	void
	 * @return	void
	 */
	public function delete()
	{
		// Delete the option record
		$sql = 'DELETE FROM `#__storefront_options` WHERE `oId` = ' . $this->db->quote($this->getId());
		$this->db->setQuery($sql);
		//print_r($this->db->replacePrefix($this->db->getQuery()));
		$this->db->query();

		// Find all SKUs that use this option before removing all references
		$sql = 'SELECT o.`sId` FROM `#__storefront_sku_options` o
				LEFT JOIN `#__storefront_skus` s ON o.`sId` = s.`sId`
				WHERE o.`oId` = ' . $this->db->quote($this->getId());
		$sql .= ' AND s.`sActive` = 1';
		$this->db->setQuery($sql);
		$sIds = $this->db->loadColumn();

		// Delete the SKU-option relation
		$sql = 'DELETE FROM `#__storefront_sku_options` WHERE `oId` = ' . $this->db->quote($this->getId());
		$this->db->setQuery($sql);
		//print_r($this->db->replacePrefix($this->db->getQuery()));
		$this->db->query();

		// Update dependencies, disable SKUs that became invalid
		$skusDisabled = false;
		foreach ($sIds as $sId)
		{
			$sku = new Sku($sId);
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

}