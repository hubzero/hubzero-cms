<?php
/**
 * HUBzero CMS
 *
 * Copyright 2005-2015 HUBzero Foundation, LLC.
 *
 * Permission is hereby granted, free of charge, to any person obtaining a copy
 * of this software and associated documentation files (the "Software"), to deal
 * in the Software without restriction, including without limitation the rights
 * to use, copy, modify, merge, publish, distribute, sublicense, and/or sell
 * copies of the Software, and to permit persons to whom the Software is
 * furnished to do so, subject to the following conditions:
 *
 * The above copyright notice and this permission notice shall be included in
 * all copies or substantial portions of the Software.
 *
 * THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
 * IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
 * FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
 * AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
 * LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
 * OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN
 * THE SOFTWARE.
 *
 * HUBzero is a registered trademark of Purdue University.
 *
 * @package   hubzero-cms
 * @author    Sam Wilson <samwilson@purdue.edu>
 * @copyright Copyright 2005-2015 HUBzero Foundation, LLC.
 * @license   http://opensource.org/licenses/MIT MIT
 */

namespace Components\Courses\Tables;

use User;
use Date;
use Lang;

/**
 * Course asset groups table class
 */
class AssetGroup extends \JTable
{
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
			$this->setError(Lang::txt('Missing unit ID'));
			return false;
		}

		$this->title = trim($this->title);
		if (!$this->title)
		{
			$this->setError(Lang::txt('Missing title'));
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

			$this->created    = Date::toSql();
			$this->created_by = User::get('id');
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
		$sql = "SELECT ordering from $this->_tbl WHERE `unit_id`=" . $this->_db->quote(intval($unit_id)) . " AND `parent`=" . $this->_db->quote(intval($parent)) . " ORDER BY ordering DESC LIMIT 1";
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
			$query .= " AND sd.section_id=" . $this->_db->quote($filters['section_id']);
		}
		$query .= " LEFT JOIN #__courses_units AS cu ON cu.id = cag.unit_id";

		$where = array();

		if (isset($filters['unit_id']) && $filters['unit_id'])
		{
			$where[] = "cag.unit_id=" . $this->_db->quote($filters['unit_id']);
		}
		if (isset($filters['parent']))
		{
			$where[] = "cag.parent=" . $this->_db->quote($filters['parent']);
		}
		if (isset($filters['alias']) && $filters['alias'])
		{
			$where[] = "cag.alias=" . $this->_db->quote($filters['alias']);
		}
		if (isset($filters['state']) && $filters['state'] >= 0)
		{
			$where[] = "cag.state=" . $this->_db->quote($filters['state']);
		}
		if (isset($filters['search']) && $filters['search'])
		{
			$where[] = "(LOWER(cag.alias) LIKE " . $this->_db->quote('%' . strtolower($filters['search']) . '%') . "
					OR LOWER(cag.title) LIKE " . $this->_db->quote('%' . strtolower($filters['search']) . '%') . ")";
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
				$query .= " cu.id = " . $this->_db->quote($filters['w']['unit_id']);

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
		$sql = "SELECT alias from $this->_tbl WHERE `unit_id`=" . $this->_db->quote(intval($this->unit_id));
		if ($this->id)
		{
			$sql .= " AND `id`!=" . $this->_db->quote(intval($this->id));
		}
		$this->_db->setQuery($sql);
		$result = $this->_db->loadColumn();

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
		$this->_db->setQuery("SELECT * FROM {$this->_tbl} WHERE parent=" . $this->_db->quote($this->$k));
		if (($groups = $this->_db->loadObjectList()))
		{
			foreach ($groups as $group)
			{
				$ids[] = $group->get('id');
			}
		}

		// Delete sub groups
		$query = "DELETE FROM #__courses_offering_section_dates WHERE scope_id = " . $this->_db->quote($this->$k) . " ANd scope=" . $this->_db->quote('asset_group');
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