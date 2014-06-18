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
 * @author    Shawn Rice <zooley@purdue.edu>
 * @copyright Copyright 2005-2011 Purdue University. All rights reserved.
 * @license   http://www.gnu.org/licenses/lgpl-3.0.html LGPLv3
 */

// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die('Restricted access');

/**
 * Table class for logging group actions
 */
class GroupsTableLog extends JTable
{
	/**
	 * int(11) Primary key
	 *
	 * @var integer
	 */
	var $id        = NULL;

	/**
	 * int(11)
	 *
	 * @var integer
	 */
	var $gidNumber = NULL;

	/**
	 * datetime(0000-00-00 00:00:00)
	 *
	 * @var string
	 */
	var $timestamp = NULL;

	/**
	 * int(11)
	 *
	 * @var integer
	 */
	var $userid    = NULL;

	/**
	 * varchar(50)
	 *
	 * @var string
	 */
	var $action    = NULL;

	/**
	 * text
	 *
	 * @var string
	 */
	var $comments  = NULL;

	/**
	 * int(11)
	 *
	 * @var integer
	 */
	var $actorid   = NULL;

	/**
	 * Constructor
	 *
	 * @param      object &$db JDatabase
	 * @return     void
	 */
	public function __construct(&$db)
	{
		parent::__construct('#__xgroups_log', 'id', $db);
	}

	/**
	 * Validate data
	 *
	 * @return     boolean True if data is valid
	 */
	public function check()
	{
		if (trim($this->gidNumber) == '')
		{
			$this->setError(JText::_('GROUPS_LOGS_MUST_HAVE_GROUP_ID'));
			return false;
		}

		return true;
	}

	/**
	 * Find all logs matching filters
	 *
	 * @param      array   $filters
	 * @return     array
	 */
	public function find( $filters = array() )
	{
		$sql  = "SELECT * FROM {$this->_tbl}";
		$sql .= $this->_buildQuery( $filters );

		$this->_db->setQuery($sql);
		return $this->_db->loadObjectList();
	}

	/**
	 * Build query string for getting list or count of pages
	 *
	 * @param      array   $filters
	 * @return     string
	 */
	private function _buildQuery( $filters = array() )
	{
		// var to hold conditions
		$where = array();
		$sql   = '';

		// gidnumber
		if (isset($filters['gidNumber']))
		{
			$where[] = "gidNumber=" . $this->_db->quote( $filters['gidNumber'] );
		}

		// action
		if (isset($filters['action']))
		{
			$where[] = "action=" . $this->_db->quote( $filters['action'] );
		}

		// if we have and conditions
		if (count($where) > 0)
		{
			$sql = " WHERE " . implode(" AND ", $where);
		}

		if (isset($filters['orderby']))
		{
			$sql .= " ORDER BY " . $filters['orderby'];
		}

		return $sql;
	}
}