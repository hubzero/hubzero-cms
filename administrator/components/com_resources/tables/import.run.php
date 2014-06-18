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

class ResourcesTableImportRun extends JTable
{
	var $id         = NULL;
	var $import_id  = NULL;
	var $processed  = NULL;
	var $count      = NULL;
	var $ran_by     = NULL;
	var $ran_at     = NULL;
	var $dry_run    = NULL;

	/**
	 * Constructor
	 *
	 * @param      object &$db JDatabase
	 * @return     void
	 */
	public function __construct(&$db)
	{
		parent::__construct('#__resource_import_runs', 'id', $db);
	}

	/**
	 * [check description]
	 * @return [type] [description]
	 */
	public function check()
	{
		if ($this->import_id == '')
		{
			$this->setError( JText::_('Import ID # is required for import run.') );
			return false;
		}

		return true;
	}

	/**
	 * [find description]
	 * @param  array  $filters [description]
	 * @return [type]          [description]
	 */
	public function find( $filters = array())
	{
		$sql  = "SELECT * FROM {$this->_tbl}";
		$sql .= $this->_buildQuery( $filters );

		$this->_db->setQuery($sql);
		return $this->_db->loadObjectList();
	}

	/**
	 * [_buildQuery description]
	 * @param  array  $filters [description]
	 * @return [type]          [description]
	 */
	private function _buildQuery( $filters = array() )
	{
		// var to hold conditions
		$where = array();
		$sql   = '';

		// which import?
		if (isset($filters['import']))
		{
			$where[] = "import_id=" . $this->_db->quote($filters['import']);
		}

		// dry runs?
		if (isset($filters['dry_run']))
		{
			$where[] = "dry_run=" . $this->_db->quote($filters['dry_run']);
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
		else
		{
			$sql .= " ORDER BY ran_at DESC";
		}

		return $sql;
	}
}