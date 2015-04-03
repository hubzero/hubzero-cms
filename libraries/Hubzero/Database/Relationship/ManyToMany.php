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
			foreach ($ids as $id)
			{
				$data  = [$this->associativeLocal => $localKeyValue, $this->associativeRelated => $id];
				$query = with(new Query)->push($this->associativeTable, $data, true);
			}
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
			$query = new Query;

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