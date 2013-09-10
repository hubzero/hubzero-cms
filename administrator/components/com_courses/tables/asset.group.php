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
 *
 * Course asset groups table class
 * 
 */
class CoursesTableAssetGroup extends JTable
{
	/**
	 * int(11) ID, primary key for course asset grouping table
	 * 
	 * @var integer
	 */
	var $id = NULL;

	/**
	 * int(11) Course unit id of this asset group (references #__course_units.gidNumber)
	 * 
	 * @var integer
	 */
	var $unit_id = NULL;

	/**
	 * varchar(255) Alias
	 * 
	 * @var string
	 */
	var $alias = NULL;

	/**
	 * varchar(255) Asset grouping title
	 * 
	 * @var string
	 */
	var $title = NULL;

	/**
	 * varchar(255) Asset group description
	 * 
	 * @var string
	 */
	var $description = NULL;

	/**
	 * int(11) Ordering
	 * 
	 * @var integer
	 */
	var $ordering = NULL;

	/**
	 * varchar(255) Asset group type
	 * 
	 * @var string
	 */
	var $parent = NULL;

	/**
	 * datetime Created date for unit
	 * 
	 * @var string
	 */
	var $created = NULL;

	/**
	 * int(11) Who created the unit (reference #__users.id)
	 * 
	 * @var integer
	 */
	var $created_by = NULL;

	/**
	 * tinyint(2)
	 * 
	 * @var integer
	 */
	var $state = NULL;

	/**
	 * text
	 * 
	 * @var string
	 */
	var $params = NULL;

	/**
	 * Contructor method for JTable class
	 * 
	 * @param  database object
	 * @return void
	 */
	public function __construct(&$db)
	{
		parent::__construct('#__courses_asset_groups', 'id', $db);
	}

	/**
	 * Override the check function to do a little input cleanup
	 * 
	 * @return return true
	 */
	public function check()
	{
		$this->unit_id = intval($this->unit_id);
		if (!$this->unit_id) 
		{
			$this->setError(JText::_('Missing unit ID'));
			return false;
		}

		$this->title = trim($this->title);
		if (!$this->title) 
		{
			$this->setError(JText::_('Missing title'));
			return false;
		}

		if (!$this->alias)
		{
			$this->alias = strtolower($this->title);
		}
		$this->alias = preg_replace("/[^a-zA-Z0-9\-_]/", '', $this->alias);
		$this->makeAliasUnique();

		if (!$this->id)
		{
			$high = $this->getHighestOrder($this->unit_id, $this->parent);
			$this->ordering = ($high + 1);

			$this->state = ($this->state) ? $this->state : 1;

			$juser =& JFactory::getUser();
			$this->created = date('Y-m-d H:i:s', time());
			$this->created_by = $juser->get('id');
		}

		return true;
	}

	/**
	 * Get the last page in the ordering
	 * 
	 * @param      string  $offering_id    Course alias (cn)
	 * @return     integer
	 */
	public function getHighestOrder($unit_id, $parent=0)
	{
		$sql = "SELECT ordering from $this->_tbl WHERE `unit_id`=" . $this->_db->Quote(intval($unit_id)) . " AND `parent`=" . $this->_db->Quote(intval($parent)) . " ORDER BY ordering DESC LIMIT 1";
		$this->_db->setQuery($sql);
		return $this->_db->loadResult();
	}

	/**
	 * Build query method
	 * 
	 * @param  array $filters
	 * @return $query database query
	 */
	private function _buildQuery($filters=array())
	{
		$query  = " FROM $this->_tbl AS cag";
		$query .= " LEFT JOIN #__courses_offering_section_dates AS sd ON sd.scope='asset_group' AND sd.scope_id=cag.id";
		if (isset($filters['section_id']) && $filters['section_id']) 
		{
			$query .= " AND sd.section_id=" . $this->_db->Quote($filters['section_id']);
		}
		$query .= " LEFT JOIN #__courses_units AS cu ON cu.id = cag.unit_id";

		$where = array();

		if (isset($filters['unit_id']) && $filters['unit_id']) 
		{
			$where[] = "cag.unit_id=" . $this->_db->Quote($filters['unit_id']);
		}
		if (isset($filters['parent'])) 
		{
			$where[] = "cag.parent=" . $this->_db->Quote($filters['parent']);
		}
		if (isset($filters['alias']) && $filters['alias']) 
		{
			$where[] = "cag.alias=" . $this->_db->Quote($filters['alias']);
		}
		if (isset($filters['state'])) 
		{
			$where[] = "cag.state=" . $this->_db->Quote($filters['state']);
		}
		if (isset($filters['search']) && $filters['search']) 
		{
			$where[] = "(LOWER(cag.alias) LIKE '%" . $this->_db->getEscaped(strtolower($filters['search'])) . "%' 
					OR LOWER(cag.title) LIKE '%" . $this->_db->getEscaped(strtolower($filters['search'])) . "%')";
		}

		if (count($where) > 0)
		{
			$query .= " WHERE " . implode(" AND ", $where);
		}

		return $query;
	}

	/**
	 * Get a record count
	 * 
	 * @param     array $filters Filters to build query from
	 * @return    integer
	 */
	public function count($filters=array())
	{
		$query  = "SELECT COUNT(cag.id)";
		$query .= $this->_buildQuery($filters['w']);

		$this->_db->setQuery($query);
		return $this->_db->loadResult();
	}

	/**
	 * Get a list of course asset groups
	 * 
	 * @param     array $filters Filters to build query from
	 * @return    array
	 */
	public function find($filters=array())
	{
		$query  = "SELECT cag.*, sd.publish_up, sd.publish_down, sd.section_id";
		$query .= $this->_buildQuery($filters['w']);

		/*if (!empty($filters['w']))
		{
			$first = true;

			if (!empty($filters['w']['unit_id']))
			{
				$query .= ($first) ? ' WHERE' : ' AND';
				$query .= " cu.id = " . $this->_db->Quote($filters['w']['unit_id']);

				$first = false;
			}
		}*/

		if (!empty($filters['start']) && !empty($filters['limit']))
		{
			$query .= " LIMIT " . $filters['start'] . "," . $filters['limit'];
		}

		$query .= " ORDER BY cag.ordering";

		$this->_db->setQuery($query);
		return $this->_db->loadObjectList();
	}

	/**
	 * Return a unique alias based on given alias
	 * 
	 * @return     integer
	 */
	private function makeAliasUnique()
	{
		$sql = "SELECT alias from $this->_tbl WHERE `unit_id`=" . $this->_db->Quote(intval($this->unit_id));
		if ($this->id)
		{
			$sql .= " AND `id`!=" . $this->_db->Quote(intval($this->id));
		}
		$this->_db->setQuery($sql);
		$result = $this->_db->loadResultArray();

		$original_alias = $this->alias;

		if ($result)
		{
			for ($i=1; in_array($this->alias, $result); $i++)
			{
				$this->alias = $original_alias . $i;
			}
		}
	}

	/**
	 * Delete a record and any associated content
	 * 
	 * @param      integer $oid Record ID
	 * @return     boolean True on success
	 */
	/*public function delete($oid=null)
	{
		$k = $this->_tbl_key;
		if ($oid) 
		{
			$this->$k = intval($oid);
		}

		// Dlete attachments
		$ids = array();
		$this->_db->setQuery("SELECT * FROM {$this->_tbl} WHERE parent=" . $this->_db->Quote($this->$k));
		if (($groups = $this->_db->loadObjectList()))
		{
			foreach ($groups as $group)
			{
				$ids[] = $group->get('id');
			}
		}

		// Delete sub groups
		$query = "DELETE FROM #__courses_offering_section_dates WHERE scope_id = " . $this->_db->Quote($this->$k) . " ANd scope=" . $this->_db->Quote('asset_group');
		$this->_db->setQuery($query);
		if (!$this->_db->query())
		{
			$this->setError($this->_db->getErrorMsg());
			return false;
		}

		// Delete the wish
		return parent::delete($oid);
	}*/
}