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
 * @copyright Copyright 2005-2011 Purdue University. All rights reserved.
 * @license   http://www.gnu.org/licenses/lgpl-3.0.html LGPLv3
 */

// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die('Restricted access');

/**
 * Table class for group page
 */
Class GroupsTablePageVersion extends JTable
{
	/**
	 * int(11) Primary key
	 *
	 * @var integer
	 */
	var $id = NULL;

	/**
	 * int(11)
	 *
	 * @var integer
	 */
	var $pageid = NULL;

	/**
	 * int(11)
	 *
	 * @var integer
	 */
	var $version = NULL;

	/**
	 * longtext
	 *
	 * @var string
	 */
	var $content = NULL;

	/**
	 * datetime
	 *
	 * @var date
	 */
	var $created = NULL;

	/**
	 * int(11)
	 *
	 * @var integer
	 */
	var $created_by = NULL;

	/**
	 * int(11)
	 *
	 * @var string
	 */
	var $approved = NULL;

	/**
	 * datetime
	 *
	 * @var date
	 */
	var $approved_on = NULL;

	/**
	 * int(11)
	 *
	 * @var integer
	 */
	var $approved_by = NULL;

	/**
	 * int(11)
	 *
	 * @var integer
	 */
	var $checked_errors = NULL;

	/**
	 * int(11)
	 *
	 * @var integer
	 */
	var $scanned = NULL;

	/**
	 * Constructor
	 *
	 * @param      object &$db JDatabase
	 * @return     void
	 */
	public function __construct(&$db)
	{
		parent::__construct('#__xgroups_pages_versions', 'id', $db);
	}

	/**
	 *
	 */
	public function check()
	{
		/*
		// need page ID
		if ($this->get('pageid') == null || $this->get('pageid') == 0)
		{
			$this->setError( JText::_('Page version must have a page ID.') );
			return false;
		}

		// need page version number
		if ($this->get('version') == null)
		{
			$this->setError( JText::_('Page version must have a version number.') );
			return false;
		}
		*/

		// need page content
		if ($this->get('content') == null || $this->get('content') == '')
		{
			$this->setError( JText::_('Page version must contain content.') );
			return false;
		}

		return true;
	}

	/**
	 *
	 */
	public function find( $filters = array() )
	{
		$sql = "SELECT * FROM {$this->_tbl}";
		$sql .= $this->_buildQuery( $filters );

		$this->_db->setQuery( $sql );
		return $this->_db->loadObjectList();
	}

	/**
	 *
	 */
	public function count( $filters = array() )
	{
		$sql = "SELECT COUNT(*) FROM {$this->_tbl}";
		$sql .= $this->_buildQuery( $filters );

		$this->_db->setQuery( $sql );
		return $this->_db->loadResult();
	}

	/**
	 *
	 */
	private function _buildQuery( $filters = array() )
	{
		$where = array();
		$sql   = '';

		if (isset($filters['pageid']) && is_numeric($filters['pageid']))
		{
			$where[] = 'pageid=' . $this->_db->quote( $filters['pageid'] );
		}

		if (isset($filters['version']) && is_numeric($filters['version']))
		{
			$where[] = 'version=' . $this->_db->quote( $filters['version'] );
		}

		if (count($where) > 0)
		{
			$sql .= " WHERE " . implode(" AND ", $where);
		}

		if (isset($filters['orderby']))
		{
			$sql .= " ORDER BY " . $filters['orderby'];
		}

		return $sql;
	}
}
