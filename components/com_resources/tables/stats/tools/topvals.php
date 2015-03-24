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

namespace Components\Resources\Tables\Stats\Tools;

/**
 * Resources table class for tool top value stats
 */
class Topvals extends \JTable
{
	/**
	 * Construct
	 *
	 * @param   object  &$db  JDatabase
	 * @return  void
	 */
	public function __construct(&$db)
	{
		parent::__construct('#__resource_stats_tools_topvals', 'id', $db);
	}

	/**
	 * Validate data
	 *
	 * @return  boolean  True if valid, False if not
	 */
	public function check()
	{
		if (trim($this->name) == '')
		{
			$this->setError(\Lang::txt('Your entry must have a name.'));
			return false;
		}
		return true;
	}

	/**
	 * Get top countries for a resource
	 *
	 * @param   integer  $id   Resource Id
	 * @param   integer  $top  Top value
	 * @return  mixed    False on error, Array on success
	 */
	public function getTopCountryRes($id=NULL, $top=NULL)
	{
		if ($id == NULL)
		{
			$id = $this->id;
		}
		if ($id == NULL)
		{
			return false;
		}
		if ($top == NULL)
		{
			$top = $this->top;
		}
		if ($top == NULL)
		{
			return false;
		}

		$this->_db->setQuery("SELECT * FROM $this->_tbl WHERE id=" . $this->_db->Quote($id) . " AND top=" . $this->_db->Quote($top) . " ORDER BY rank");
		return $this->_db->loadObjectList();
	}
}