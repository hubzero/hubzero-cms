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
 * Storefront product class
 *
 */
class OptionGroup
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
	public function __construct($ogId = false)
	{
		$this->data = new \stdClass();
		$this->db = \App::get('db');

		if (isset($ogId) && is_numeric($ogId) && $ogId)
		{
			$this->load($ogId);
		}
	}

	/**
	 * Load existing option group
	 *
	 * @param	int			option group ID
	 * @return	bool		true on success, exception otherwise
	 */
	public function load($ogId)
	{
		$sql = "SELECT * FROM `#__storefront_option_groups` og
 				WHERE og.`ogId` = " . $this->db->quote($ogId);

		$this->db->setQuery($sql);
		if ($ogInfo = $this->db->loadObject())
		{
			$this->setId($ogInfo->ogId);
			$this->setName($ogInfo->ogName);
			$this->setActiveStatus($ogInfo->ogActive);
		}
		else
		{
			throw new \Exception(Lang::txt('Error loading option group'));
		}
	}

	/**
	 * Set ID
	 *
	 * @param	int			option group ID
	 * @return	bool		true
	 */
	public function setId($ogId)
	{
		$this->data->id = $ogId;
		return true;
	}

	/**
	 * Get option group id (if set)
	 *
	 * @param	void
	 * @return	int		option group ID
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
	 * Set option group name
	 *
	 * @param	string		collection name
	 * @return	bool		true
	 */
	public function setName($ogName)
	{
		$this->data->name = $ogName;
		return true;
	}

	/**
	 * Get option group name
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
	 * Set option group status
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
			$sql = "UPDATE `#__storefront_option_groups` SET ";
		}
		elseif ($action == 'add')
		{
			$sql = "INSERT INTO `#__storefront_option_groups` SET ";
		}

		$sql .= "
				`ogName` = " . $this->db->quote($this->getName()) . ",
				`ogActive` = " . $this->db->quote($this->getActiveStatus());

		// Add WHERE if updating
		if ($action == 'update')
		{
			$sql .= " WHERE `ogId` = " . $this->db->quote($this->getId());
		}

		$this->db->setQuery($sql);
		//print_r($this->db->replacePrefix($this->db->getQuery())); die;
		$this->db->query();
		if ($action = 'add')
		{
			$this->setId($this->db->insertid());
		}
	}

	/**
	 * Delete the option group
	 *
	 * @param	void
	 * @return 	true on success, throws exception on failure
	 */
	public function delete()
	{
		// Delete option group record
		$sql = 'DELETE FROM `#__storefront_option_groups` WHERE `ogId` = ' . $this->db->quote($this->getId());
		$this->db->setQuery($sql);
		$this->db->query();

		// Delete all options
		$options = $this->getOptions();

		// Save warning messages to display
		$warnings = array();

		foreach ($options as $option)
		{
			$option->delete();
			if ($optionWarnings = $option->getMessages())
			{
				foreach ($optionWarnings as $optionWarning)
				{
					// Don't save duplicate messages, one is enough
					if (!in_array($optionWarning, $warnings))
					{
						$warnings[] = $optionWarning;
					}
				}
			}
		}

		// Delete product-option group relations
		$sql = 'DELETE FROM `#__storefront_product_option_groups` WHERE `ogId` = ' . $this->db->quote($this->getId());
		$this->db->setQuery($sql);
		$this->db->query();

		// Set warning messages
		if (!empty($warnings))
		{
			foreach ($warnings as $warning)
			{
				$this->addMessage($warning);
			}
		}
	}

	public function getOptions()
	{
		if (!empty($this->data->options))
		{
			return $this->data->options;
		}

		$sql = "SELECT * FROM `#__storefront_options`
				WHERE ogId = " . $this->db->quote($this->getId());

		$this->db->setQuery($sql);
		//print_r($this->db->replacePrefix( (string) $sql )); die;
		$this->db->execute();
		$res = $this->db->loadObjectList();

		require_once(__DIR__ . DS . 'Option.php');
		foreach ($res as $option)
		{
			$option = new Option($option->oId);
			$this->addOption($option);
		}

		if (!isset($this->data->options))
		{
			$this->data->options = array();
		}

		return $this->data->options;
	}

	public function addOption($option)
	{
		$this->data->options[$option->getId()] = $option;
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