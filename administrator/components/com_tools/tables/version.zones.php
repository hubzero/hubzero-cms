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
 * @author    Sam Wilson <samwilson@purdue.edu>
 * @copyright Copyright 2005-2011 Purdue University. All rights reserved.
 * @license   http://www.gnu.org/licenses/lgpl-3.0.html LGPLv3
 */

// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die('Restricted access');

/**
 * Tool version zones table class
 */
class ToolVersionZones extends JTable
{
	/**
	 * Primary key
	 *
	 * @var int
	 */
	var $id;

	/**
	 * Tool version id
	 *
	 * @var int
	 */
	var $tool_version_id;

	/**
	 * Zone id
	 *
	 * @var int
	 */
	var $zone_id;

	/**
	 * Publish up datetime
	 *
	 * @var string
	 */
	var $publish_up;

	/**
	 * Publish down datetime
	 *
	 * @var string
	 */
	var $publish_down;

	/**
	 * Constructor
	 *
	 * @param      object &$db JDatabase
	 * @return     void
	 */
	public function __construct(&$db)
	{
		parent::__construct('#__tool_version_zone', 'id', $db);
	}

	/**
	 * Loads records by tool version id
	 *
	 * @param  int $version the tool version
	 * @return void
	 **/
	public function loadByToolVersion($version)
	{
		$query  = "SELECT * FROM " . $this->_db->quoteName($this->_tbl);
		$query .= "WHERE `tool_version_id` = " . (int) $version;

		$this->_db->setQuery($query);
		return $this->_db->loadObjectList();
	}
}