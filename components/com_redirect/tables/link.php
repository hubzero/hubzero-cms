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

namespace Components\Redirect\Tables;

/**
 * Link Table for Redirect.
 */
class Link extends \JTable
{
	/**
	 * Constructor
	 *
	 * @param   object  $db  Database object
	 * @return  void
	 */
	public function __construct(&$db)
	{
		parent::__construct('#__redirect_links', 'id', $db);
	}

	/**
	 * Overloaded check function
	 *
	 * @return  boolean
	 */
	public function check()
	{
		$this->old_url = trim($this->old_url);
		$this->new_url = trim($this->new_url);

		// Check for valid name.
		if (empty($this->old_url))
		{
			$this->setError(Lang::txt('COM_REDIRECT_ERROR_SOURCE_URL_REQUIRED'));
			return false;
		}

		// Check for valid name.
		if (empty($this->new_url))
		{
			$this->setError(Lang::txt('COM_REDIRECT_ERROR_DESTINATION_URL_REQUIRED'));
			return false;
		}

		// Check for duplicates
		if ($this->old_url == $this->new_url)
		{
			$this->setError(Lang::txt('COM_REDIRECT_ERROR_DUPLICATE_URLS'));
			return false;
		}

		$db = $this->getDbo();

		// Check for existing name
		$query = 'SELECT id FROM `#__redirect_links` WHERE `old_url`=' . $db->Quote($this->old_url);
		$db->setQuery($query);

		$xid = intval($db->loadResult());

		if ($xid && $xid != intval($this->id))
		{
			$this->setError(Lang::txt('COM_REDIRECT_ERROR_DUPLICATE_OLD_URL'));
			return false;
		}

		return true;
	}

	/**
	 * Overriden store method to set dates.
	 *
	 * @param   boolean  $updateNulls  True to update fields even if they are null.
	 * @return  boolean  True on success.
	 */
	public function store($updateNulls = false)
	{
		// Initialise variables.
		$date = \Date::toSql();

		if ($this->id)
		{
			// Existing item
			$this->modified_date = $date;
		}
		else
		{
			// New record.
			$this->created_date = $date;
		}

		return parent::store($updateNulls);
	}
}
