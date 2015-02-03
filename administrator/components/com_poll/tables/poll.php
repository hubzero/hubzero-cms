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

namespace Components\Poll\Tables;

/**
 * Table class for a poll
 */
class Poll extends \JTable
{
	/**
	 * Constructor
	 *
	 * @param   object  $db  JDatabase
	 * @return  void
	 */
	public function __construct(&$db)
	{
		parent::__construct('#__polls', 'id', $db);
	}

	/**
	 * Binds an array to the object
	 *
	 * @param   array    Named array
	 * @param   string   Space separated list of fields not to bind
	 * @return  boolean
	 */
	public function bind($array, $ignore='')
	{
		$result = parent::bind($array);

		// Cast properties
		$this->id = (int) $this->id;

		return $result;
	}

	/**
	 * Overloaded check function
	 *
	 * @return  boolean
	 */
	public function check()
	{
		// Check for valid name
		$this->title = trim($this->title);
		if ($this->title == '')
		{
			$this->setError(\JText::_('COM_POLL_MISSING_TITLE'));
			return false;
		}

		// Check for valid lag
		$this->lag = intval($this->lag);
		if ($this->lag == 0)
		{
			$this->setError(\JText::_('COM_POLL_MISSING_LAG'));
			return false;
		}

		if (empty($this->alias))
		{
			$this->alias = $this->title;
		}
		$this->alias = \JFilterOutput::stringURLSafe($this->alias);
		if (trim(str_replace('-','',$this->alias)) == '')
		{
			$datenow = \JFactory::getDate();
			$this->alias = $datenow->toFormat("%Y-%m-%d-%H-%M-%S");
		}

		return true;
	}

	/**
	 * Delete a record
	 *
	 * @param   integer  $oid  Record ID
	 * @return  boolean
	 */
	public function delete($oid=null)
	{
		$k = $this->_tbl_key;
		if ($oid)
		{
			$this->$k = intval($oid);
		}

		if (parent::delete($oid))
		{
			$query = 'DELETE FROM `#__poll_data` WHERE pollid = ' . (int) $this->$k;
			$this->_db->setQuery($query);
			if (!$this->_db->query())
			{
				$this->setError($this->_db->getErrorMsg());
			}

			$query = 'DELETE FROM `#__poll_date` WHERE poll_id = ' . (int) $this->$k;
			$this->_db->setQuery($query);
			if (!$this->_db->query())
			{
				$this->setError($this->_db->getErrorMsg());
			}

			$query = 'DELETE FROM `#__poll_menu` WHERE pollid = ' . (int) $this->$k;
			$this->_db->setQuery($query);
			if (!$this->_db->query())
			{
				$this->setError($this->_db->getErrorMsg());
			}

			return true;
		}

		return false;
	}
}
