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
 * @author    Christopher Smoak <csmoak@purdue.edu>
 * @copyright Copyright 2005-2011 Purdue University. All rights reserved.
 * @license   http://www.gnu.org/licenses/lgpl-3.0.html LGPLv3
 */

// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die('Restricted access');

/**
 * Groups Module table
 */
class GroupsTableModule extends JTable
{
	var $id          = null;
	var $gidNumber   = null;
	var $title       = null;
	var $content     = null;
	var $position    = null;
	var $ordering    = null;
	var $state       = null;
	var $created     = null;
	var $created_by  = null;
	var $modified    = null;
	var $modified_by = null;
	var $approved    = null;
	var $approved_on    = null;
	var $approved_by    = null;
	var $checked_errors = null;
	var $scanned        = null;


	/**
	 * Constructor
	 *
	 * @param      object &$db JDatabase
	 * @return     void
	 */
	public function __construct(&$db)
	{
		parent::__construct('#__xgroups_modules', 'id', $db);
	}

	/**
	 * Overload check method to make sure we have needed vars
	 *
	 * @return     BOOL
	 */
	public function check()
	{
		// need group id
		if ($this->get('gidNumber') == null)
		{
			$this->setError( JText::_('Must provide group id.') );
			return false;
		}

		// need module title
		if ($this->get('title') == null)
		{
			$this->setError( JText::_('Must provide module title.') );
			return false;
		}

		// need module content
		if ($this->get('content') == null)
		{
			$this->setError( JText::_('Must provide module content.') );
			return false;
		}

		return true;
	}

	/**
	 * Find all modules matching filters
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
	 * Get count of modules matching filters
	 *
	 * @param      array   $filters
	 * @return     int
	 */
	public function count( $filters = array() )
	{
		$sql  = "SELECT COUNT(*) FROM {$this->_tbl}";
		$sql .= $this->_buildQuery( $filters );

		$this->_db->setQuery($sql);
		return $this->_db->loadResult();
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

		// published
		if (isset($filters['gidNumber']))
		{
			$where[] = "gidNumber=" . $this->_db->quote( $filters['gidNumber'] );
		}

		// title
		if (isset($filters['title']))
		{
			$where[] = "title=" . $this->_db->quote( $filters['title'] );
		}

		// position
		if (isset($filters['position']))
		{
			$where[] = "position=" . $this->_db->quote( $filters['position'] );
		}

		// state
		if (isset($filters['state']) && is_array($filters['state']))
		{
			$where[] = "state IN (" . implode(',', $filters['state']) . ")";
		}

		// approved
		if (isset($filters['approved']) && is_array($filters['approved']))
		{
			$where[] = "approved IN (" . implode(',', $filters['approved']) . ")";
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