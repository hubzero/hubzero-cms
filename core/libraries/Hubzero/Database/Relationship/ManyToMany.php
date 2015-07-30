<?php
/**
 * HUBzero CMS
 *
 * Copyright 2005-2013 Purdue University. All rights reserved.
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
 * @copyright Copyright 2005-2013 Purdue University. All rights reserved.
 * @license   http://www.gnu.org/licenses/lgpl-3.0.html LGPLv3
 * @since     Class available since release 1.3.2
 */

namespace Hubzero\Database\Relationship;

use Hubzero\Database\Query;

/**
 * Database many to many relationship
 */
class ManyToMany extends OneToManyThrough
{
	/**
	 * Fetches the results of relationship
	 *
	 * @return \Hubzero\Database\Relational
	 * @since  1.3.2
	 **/
	public function rows()
	{
		$rows = parent::rows();

		// Now remove any associative fields
		foreach ($rows as $row)
		{
			$associatives = new \stdClass();

			foreach ($row->getAttributes() as $k => $v)
			{
				if (strpos($k, 'associative_') === 0)
				{
					$key = substr($k, 12);
					$associatives->$key = $v;
					$row->removeAttribute($k);
				}
			}

			if (!empty($associatives))
			{
				$row->associated = $associatives;
			}
		}

		return $rows;
	}

	/**
	 * Joins the related table together with the intermediate table for the pending query
	 *
	 * This is primarily used when we're getting the related results and we need to work
	 * our way backwards through the intermediate table.
	 *
	 * @return $this
	 * @since  1.3.2
	 **/
	public function mediate()
	{
		parent::mediate();

		// We also want to grab any associative fields at this time, rather than having to come back for them later
		// To do that, we'll prefix the columns and then strip them after the query
		$columns = $this->model->getStructure()->getTableColumns($this->associativeTable);

		// Get rid of known columns (don't use a primary key other than id and you'll be fine here!)
		if (isset($columns['id'])) unset($columns['id']);
		unset($columns[$this->associativeLocal]);
		unset($columns[$this->associativeRelated]);

		// Add remaining fields to our select statement
		if (count($columns) > 0)
		{
			foreach ($columns as $column => $type)
			{
				$this->related->select($this->associativeTable . '.' . $column, 'associative_' . $column);
			}
		}

		return $this;
	}

	/**
	 * Connects the provided identifiers back to the parent model by way of associative entities
	 *
	 * This will add a new entry, irrelevant of whether or not a comparable entry is already there.
	 * To avoid this behavior, either use the sync function or set a constraint on your associative
	 * table.
	 *
	 * @param  array $ids the identifiers to place in the associative table
	 * @return $this
	 * @since  1.3.2
	 **/
	public function connect($ids)
	{
		if (is_array($ids) && count($ids) > 0)
		{
			$localKeyValue = $this->model->getPkValue();
			foreach ($ids as $id => $associative)
			{
				// Build base data
				$id   = (is_array($associative)) ? $id : $associative;
				$data = [$this->associativeLocal => $localKeyValue, $this->associativeRelated => $id];

				// If we have associative data, include that in the query
				if (is_array($associative)) $data = array_merge($data, $associative);

				// Save data
				$query = $this->model->getQuery()->push($this->associativeTable, $data, true);
			}
		}

		return $this;
	}

	/**
	 * Removes the relationship between the two sides of the many to many
	 * (not deleting either of the actual sides of the models themselves)
	 *
	 * @param  array $ids the identifiers to remove from the associative table
	 * @return $this
	 * @since  1.3.2
	 **/
	public function disconnect($ids)
	{
		if (is_array($ids) && count($ids) > 0)
		{
			$query = $this->model->getQuery();
			$query->delete($this->associativeTable)
			      ->whereEquals($this->associativeLocal, $this->model->getPkValue())
			      ->whereIn($this->associativeRelated, $ids)
			      ->execute();
		}

		return $this;
	}

	/**
	 * Syncs the provided identifiers back to the parent model by way of associative entities,
	 * deleting ones that should no longer be there, and adding ones that are missing.
	 *
	 * @param  array $ids the identifiers to place in the associative table
	 * @return $this
	 * @since  1.3.2
	 **/
	public function sync($ids)
	{
		if (is_array($ids))
		{
			// Get a query instance
			$query = $this->model->getQuery();

			// Get the parent primary key value
			$localKeyValue = $this->model->getPkValue();

			// Get any existing entries
			$existing = $query->select($this->associativeRelated)
			                  ->from($this->associativeTable)
			                  ->whereEquals($this->associativeLocal, $localKeyValue)
			                  ->fetch('column');

			// See if there's anything to delete
			$deletes = array_diff($existing, $ids);
			if (!empty($deletes))
			{
				$query->delete($this->associativeTable)
				      ->whereEquals($this->associativeLocal, $localKeyValue)
				      ->whereIn($this->associativeRelated, $deletes)
				      ->execute();
			}

			// Now see if there's anything to add
			$inserts = array_diff($ids, $existing);
			$this->connect($inserts);
		}

		return $this;
	}
}