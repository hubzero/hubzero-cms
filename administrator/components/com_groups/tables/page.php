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
Class GroupsTablePage extends JTable
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
	 * @var string
	 */
	var $gidNumber = NULL;

	/**
	 * int(11)
	 *
	 * @var string
	 */
	var $parent = NULL;

	/**
	 * int(11)
	 *
	 * @var string
	 */
	var $depth = NULL;

	/**
	 * int(11)
	 *
	 * @var string
	 */
	var $lft = NULL;

	/**
	 * int(11)
	 *
	 * @var string
	 */
	var $rgt = NULL;

	/**
	 * int(11)
	 *
	 * @var string
	 */
	var $category = NULL;

	/**
	 * varchar(100)
	 *
	 * @var string
	 */
	var $template = NULL;

	/**
	 * varchar(100)
	 *
	 * @var string
	 */
	var $title = NULL;

	/**
	 * varchar(100)
	 *
	 * @var string
	 */
	var $alias = NULL;

	/**
	 * int(11)
	 *
	 * @var integer
	 */
	var $state = 1;

	/**
	 * varchar(10)
	 *
	 * @var string
	 */
	var $privacy = NULL;

	/**
	 * int(11)
	 *
	 * @var integer
	 */
	var $home   = NULL;

	/**
	 * int(11)
	 *
	 * @var integer
	 */
	var $comments = NULL;

	/**
	 * Constructor
	 *
	 * @param      object &$db JDatabase
	 * @return     void
	 */
	public function __construct(&$db)
	{
		parent::__construct('#__xgroups_pages', 'id', $db);
	}

	/**
	 *
	 */
	public function check()
	{
		// need group id
		if ($this->get('gidNumber') == null)
		{
			$this->setError( JText::_('Must provide group id.') );
			return false;
		}

		// need page title
		if ($this->get('title') == null)
		{
			$this->setError( JText::_('Must provide page title.') );
			return false;
		}

		// need page alias
		if ($this->get('alias') == null)
		{
			$this->setError( JText::_('Must provide page alias.') );
			return false;
		}

		return true;
	}


	/**
	 * Find all pages matching filters
	 *
	 * @param      array   $filters
	 * @return     array
	 */
	public function find( $filters = array() )
	{
		$sql  = "SELECT * FROM {$this->_tbl}";
		$sql .= $this->_buildQuery( $filters );

		$this->_db->setQuery($sql);

		if (isset($filters['returnas']) && $filters['returnas'] == 'array')
		{
			return $this->_db->loadAssocList();
		}
		else
		{
			return $this->_db->loadObjectList();
		}
	}


	/**
	 * Get count of pages matching filters
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

		// published
		if (isset($filters['state']) && is_array($filters['state']))
		{
			$where[] = "state IN (" . implode(',', $filters['state']) . ")";
		}

		// category
		if (isset($filters['category']))
		{
			$where[] = "category=" . $this->_db->quote( $filters['category'] );
		}

		// home
		if (isset($filters['home']))
		{
			$where[] = "home=" . $this->_db->quote( $filters['home'] );
		}

		// parent
		if (isset($filters['depth']))
		{
			$where[] = "depth=" . $this->_db->quote( $filters['depth'] );
		}

		// left
		if (isset($filters['left']))
		{
			$where[] = "lft > " . $this->_db->quote( $filters['left'] );
		}

		// right
		if (isset($filters['right']))
		{
			$where[] = "rgt < " . $this->_db->quote( $filters['right'] );
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
