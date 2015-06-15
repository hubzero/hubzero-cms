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
 * @author    Christopher Smoak <csmoak@purdue.edu>
 * @copyright Copyright 2005-2015 Purdue University. All rights reserved.
 * @license   http://www.gnu.org/licenses/lgpl-3.0.html LGPLv3
 */

namespace Components\Newsletter\Tables;

/**
 * Table class for primary stories
 */
class PrimaryStory extends \JTable
{
	/**
	 * Newsletter Primary Story Constructor
	 *
	 * @param   object  $db  Database Object
	 * @return  void
	 */
	public function __construct(&$db)
	{
		parent::__construct('#__newsletter_primary_story', 'id', $db);
	}

	/**
	 * Get Primary Stories
	 *
	 * @param   integer  $newsletterId  Newsletter Id
	 * @return  array
	 */
	public function getStories($newsletterId)
	{
		$sql = "SELECT * FROM {$this->_tbl} WHERE deleted=0";

		if ($newsletterId)
		{
			$sql .= " AND nid=" . $this->_db->quote($newsletterId);
		}

		$sql .= " ORDER BY `order`";
		$this->_db->setQuery($sql);
		return $this->_db->loadObjectList();
	}

	/**
	 * Get Highest Story Order
	 *
	 * @param   integer  $newsletterId  Newsletter Id
	 * @return 	integer
	 */
	public function _getCurrentHighestOrder($newsletterId)
	{
		$sql = "SELECT `order` FROM {$this->_tbl} WHERE deleted=0 AND nid=" . $this->_db->quote($newsletterId) . " ORDER BY `order` DESC LIMIT 1";
		$this->_db->setQuery($sql);
		return $this->_db->loadResult();
	}
}