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

use Hubzero\Database\Table;
use Lang;
use Date;
use User;

/**
 * Course assets table class
 */
class Asset extends Table
{
	/**
	 * Contructor method for Table class
	 *
	 * @param  database object
	 * @return void
	 */
	public function __construct($db)
	{
		parent::__construct('#__courses_assets', 'id', $db);
	}

	/**
	 * Override the check function to do a little input cleanup
	 *
	 * @return return true
	 */
	public function check()
	{
		$this->course_id = intval($this->course_id);
		if (!$this->course_id)
		{
			$this->setError(Lang::txt('Please provide a course ID.'));
			return false;
		}

		$this->title = trim($this->title);
		if (!$this->title)
		{
			$this->setError(Lang::txt('Please provide a title.'));
			return false;
		}

		if (!isset($this->type) && !$this->url && $this->content)
		{
			$this->type    = 'text';
			$this->subtype = 'note';
		}

		if (!$this->id)
		{
			$this->state = (isset($this->state)) ? $this->state : 1;

			$this->created    = Date::toSql();
			$this->created_by = User::get('id');
		}

		return true;
	}

	/**
	 * Overload the store function to make sure a path is set
	 *
	 * @param   boolean  $updateNulls  True to update fields even if they are null.
	 * @return  boolean  True on success.
	 **/
	public function store($updateNulls = false)
	{
		if (!$this->path)
		{
			Event::listen(function($event)
			{
				$table       = $event->getArgument('table');
				$table->path = $table->course_id . '/' . $table->id;
				$table->store();
			}, $this->getTableName() . '_new');
		}

		return parent::store($updateNulls);
	}

	/**
	 * Build query method
	 *
	 * @param  array $filters
	 * @return $query database query
	 */
	private function _buildQuery($filters=array())
	{
		$query  = " FROM $this->_tbl AS ca";
		$query .= " LEFT JOIN #__courses_offering_section_dates AS sd ON sd.scope='asset' AND sd.scope_id=ca.id";

		if (isset($filters['section_id']))
		{
			$query .= " AND sd.section_id=" . $this->_db->quote((int) $filters['section_id']);
		}

		$query .= " LEFT JOIN #__courses_asset_associations AS caa ON caa.asset_id = ca.id";
		$query .= " LEFT JOIN #__courses_asset_groups AS cag ON caa.scope_id = cag.id";

		if (isset($filters['offering_id']))
		{
			$query .= " LEFT JOIN `#__courses_units` AS cu ON cag.unit_id = cu.id";
		}

		if (isset($filters['progress_calculation']) && $filters['progress_calculation'])
		{
			$query .= " INNER JOIN `#__courses_progress_factors` AS cpf ON ca.id = cpf.asset_id";

			if (isset($filters['section_id']))
			{
				$query .= " AND cpf.section_id = " . $this->_db->quote($filters['section_id']);
			}
		}

		$where = array();

		if (!empty($filters['asset_id']))
		{
			$where[] = "ca.id=" . $this->_db->quote((int) $filters['asset_id']);
		}
		if (!empty($filters['asset_scope_id']))
		{
			$where[] = "cag.id=" . $this->_db->quote((int) $filters['asset_scope_id']);
		}
		if (!empty($filters['asset_scope']))
		{
			$where[] = "caa.scope=" . $this->_db->quote((string) $filters['asset_scope']);
		}
		if (isset($filters['state']) && $filters['state'] >= 0)
		{
			$where[] = "ca.state=" . $this->_db->quote($filters['state']);
		}
		if (!empty($filters['course_id']))
		{
			$where[] = "ca.course_id=" . $this->_db->quote((int) $filters['course_id']);
		}
		if (!empty($filters['asset_type']))
		{
			$where[] = "ca.type=" . $this->_db->quote((string) $filters['asset_type']);
		}
		if (!empty($filters['asset_subtype']))
		{
			$where[] = "ca.subtype=" . $this->_db->quote((string) $filters['asset_subtype']);
		}
		if (!empty($filters['unit_id']))
		{
			$where[] = "cag.unit_id=" . $this->_db->quote((int) $filters['unit_id']);
		}
		if (!empty($filters['offering_id']))
		{
			$where[] = "cu.offering_id=" . $this->_db->quote((int) $filters['offering_id']);
		}
		if (!empty($filters['graded']))
		{
			$where[] = "ca.graded=1";
		}
		if (isset($filters['search']) && $filters['search'])
		{
			$where[] = "(LOWER(ca.url) LIKE " . $this->_db->quote('%' . strtolower($filters['search']) . '%') . "
					OR LOWER(ca.title) LIKE " . $this->_db->quote('%' . strtolower($filters['search']) . '%') . ")";
		}

		if (count($where) > 0)
		{
			$query .= " WHERE " . implode(" AND ", $where);
		}

		return $query;
	}

	/**
	 * Get an object list of course units
	 *
	 * @param  array $filters
	 * @return object Return course units
	 */
	public function count($filters=array())
	{
		if (!isset($filters['w']))
		{
			$filters['w'] = array();
		}
		$query  = "SELECT COUNT(DISTINCT ca.id)";
		$query .= $this->_buildQuery($filters['w']);

		$this->_db->setQuery($query);
		return $this->_db->loadResult();
	}

	/**
	 * Get an object list of course units
	 *
	 * @param  array $filters
	 * @return object Return course units
	 */
	public function find($filters=array())
	{
		if (!isset($filters['w']))
		{
			$filters['w'] = array();
		}
		$query  = "SELECT DISTINCT ca.*, caa.ordering, sd.publish_up, sd.publish_down, sd.section_id, cag.unit_id";
		$query .= $this->_buildQuery($filters['w']);

		if (!empty($filters['start']) && !empty($filters['limit']))
		{
			$query .= " LIMIT " . $filters['start'] . "," . $filters['limit'];
		}

		if (!empty($filters['order_by']) && !empty($filters['order_dir']))
		{
			$query .= " ORDER BY " . $filters['order_by'] . " " . $filters['order_dir'];
		}
		else
		{
			$query .= " ORDER BY caa.ordering";
		}

		$this->_db->setQuery($query);
		return $this->_db->loadObjectList();
	}

	/**
	 * Find all assets for a given scope/scope_id
	 *
	 * @param  string $scope
	 * @param  int    $scope_id
	 * @param  array  $filters
	 * @return object Return course assets
	 */
	public function findByScope($scope, $scope_id, $filters=array())
	{
		$query  = "SELECT DISTINCT ca.*";
		$query .= " FROM {$this->_tbl} AS ca";
		$query .= " LEFT JOIN `#__courses_asset_associations` AS caa ON caa.asset_id = ca.id";
		$query .= " LEFT JOIN `#__courses_{$scope}s` AS scope ON caa.scope_id = scope.id";
		$query .= " WHERE caa.scope = '{$scope}'";
		$query .= " AND caa.scope_id = '{$scope_id}'";

		$where = array();

		if (isset($filters['state']) && $filters['state'] >= 0)
		{
			$where[] = "ca.state=" . $this->_db->quote($filters['state']);
		}
		if (!empty($filters['asset_type']))
		{
			$where[] = "ca.type=" . $this->_db->quote((string) $filters['asset_type']);
		}
		if (!empty($filters['asset_subtype']))
		{
			$where[] = "ca.subtype=" . $this->_db->quote((string) $filters['asset_subtype']);
		}
		if (!empty($filters['graded']))
		{
			$where[] = "ca.graded=1";
		}
		if (count($where) > 0)
		{
			$query .= " AND " . implode(" AND ", $where);
		}

		$this->_db->setQuery($query);
		return $this->_db->loadObjectList();
	}

	/**
	 * Check to see if this asset has any associations connected to it
	 *
	 * @return bool
	 */
	public function isOrphaned()
	{
		if (!$this->id)
		{
			return false;
		}

		$query  = "SELECT caa.id";
		$query .= " FROM $this->_tbl AS ca";
		$query .= " LEFT JOIN #__courses_asset_associations AS caa ON caa.asset_id = ca.id";
		$query .= " WHERE ca.id = " . $this->_db->quote($id);

		$this->_db->setQuery($query);
		$result = $this->_db->loadObjectList();

		return ($result) ? false : true;
	}
}
