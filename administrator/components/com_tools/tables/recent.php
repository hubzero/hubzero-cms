<?php
/**
 * @package		HUBzero CMS
 * @author		Shawn Rice <zooley@purdue.edu>
 * @copyright	Copyright 2005-2009 by Purdue Research Foundation, West Lafayette, IN 47906
 * @license		http://www.gnu.org/licenses/gpl-2.0.html GPLv2
 *
 * Copyright 2005-2009 by Purdue Research Foundation, West Lafayette, IN 47906.
 * All rights reserved.
 *
 * This program is free software; you can redistribute it and/or
 * modify it under the terms of the GNU General Public License,
 * version 2 as published by the Free Software Foundation.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program; if not, write to the Free Software
 * Foundation, Inc., 51 Franklin Street, Fifth Floor, Boston, MA  02110-1301, USA.
 */

// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die('Restricted access');

/**
 * Table class for recent tools
 */
class ToolRecent extends JTable
{
	/**
	 * int(11)
	 *
	 * @var integer
	 */
	var $id      = NULL;

	/**
	 * int(11)
	 *
	 * @var integer
	 */
	var $uid     = NULL;

	/**
	 * varchar(200)
	 *
	 * @var string
	 */
	var $tool    = NULL;

	/**
	 * datetime(0000-00-00 00:00:00)
	 *
	 * @var stirng
	 */
	var $created = NULL;

	/**
	 * Constructor
	 *
	 * @param      object &$db JDatabase
	 * @return     void
	 */
	public function __construct(&$db)
	{
		parent::__construct('#__recent_tools', 'id', $db);
	}

	/**
	 * Get a list of recently used tools
	 *
	 * @param      integer $uid User ID
	 * @return     array
	 */
	public function getRecords($uid=null)
	{
		if ($uid == null)
		{
			$uid = $this->uid;
		}
		if ($uid === null)
		{
			return false;
		}
		$this->_db->setQuery("SELECT * FROM $this->_tbl WHERE uid=" . $this->_db->Quote($uid) . " ORDER BY created DESC");
		return $this->_db->loadObjectList();
	}
}