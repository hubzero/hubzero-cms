<?php
/**
 * HUBzero CMS
 *
 * Copyright 2005-2015 Purdue University. All rights reserved.
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
 * @author    Shawn Rice <zooley@purdue.edu>
 * @copyright Copyright 2005-2015 Purdue University. All rights reserved.
 * @license   http://www.gnu.org/licenses/lgpl-3.0.html LGPLv3
 */

// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die('Restricted access');

/**
 * Table for storing user middleware preferences
 */
class ToolsTablePreferences extends JTable
{
	/**
	 * Constructor
	 *
	 * @param      object &$db JDatabase
	 * @return     void
	 */
	public function __construct(&$db)
	{
		parent::__construct('#__users_tool_preferences', 'id', $db);
	}

	/**
	 * Validate data
	 *
	 * @return     boolean False if invalid data, true on success
	 */
	public function check()
	{
		if (!$this->user_id)
		{
			$this->user_id = \JFactory::getUser()->get('id');
		}

		$test = new self($this->_db);
		$test->loadByUser($this->user_id);
		if ($test->id)
		{
			$this->setError(\JText::sprintf('User with ID of %s already has a record.', $this->user_id));
		}

		if ($this->getError())
		{
			return false;
		}

		return true;
	}

	/**
	 * Load an entry from the database and bind to $this
	 *
	 * @param   string   $user_id
	 * @return  boolean  True if data was retrieved and loaded
	 */
	public function loadByUser($user_id)
	{
		return parent::load(array(
			'user_id' => (int) $user_id
		));
	}

	/**
	 * Load an entry from the database and bind to $this
	 *
	 * @param   string   $user_id
	 * @return  boolean  True if data was retrieved and loaded
	 */
	public function param($key='', $default=null)
	{
		if (!is_object($this->params))
		{
			$this->params = new JRegistry($this->params);
		}

		if ($key)
		{
			return $this->params->get((string) $key, $default);
		}
		return $this->params;
	}

	/**
	 * Method to store a row in the database from the JTable instance properties.
	 * If a primary key value is set the row with that primary key value will be
	 * updated with the instance property values.  If no primary key value is set
	 * a new row will be inserted into the database with the properties from the
	 * JTable instance.
	 *
	 * @param   boolean  $updateNulls  True to update fields even if they are null.
	 * @return  boolean  True on success.
	 */
	public function store($updateNulls = false)
	{
		if (is_object($this->params))
		{
			$this->params = $this->params->toString();
		}

		return parent::store($updateNulls);
	}
}
