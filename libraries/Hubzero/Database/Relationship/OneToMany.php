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

use Hubzero\Database\Rows;

/**
 * Database one to many relationship
 */
class OneToMany extends Relationship
{
	/**
	 * Fetch results of relationship
	 *
	 * @return \Hubzero\Database\Relational
	 * @since  1.3.2
	 **/
	public function rows()
	{
		return $this->constrain()->rows();
	}

	/**
	 * Saves new related models with the given data
	 *
	 * @param  array $data an array of datasets being saved to new models
	 * @return bool
	 * @since  1.3.2
	 **/
	public function save($data)
	{
		// Check and make sure this is an array of arrays
		if (!is_array($data)) return false;

		if (is_array($data[0]))
		{
			foreach ($data as $d)
			{
				if (!parent::save($d)) return false;
			}
		}
		else
		{
			// If not an array of arrays, we'll assume it's just one item to save
			if (!parent::save($data)) return false;
		}

		return true;
	}

	/**
	 * Deletes all rows attached to the current model
	 *
	 * @return bool
	 * @since  1.3.2
	 **/
	public function destroyAll()
	{
		// @FIXME: could make this a single query...i.e. delete where id in (...)
		foreach ($this->related as $model)
		{
			if (!$model->destroy())
			{
				return false;
			}
		}

		return true;
	}

	/**
	 * Get keys based on given constraint
	 *
	 * @param  closure $constraint the constraint function to apply
	 * @return array
	 * @since  1.3.2
	 **/
	public function getConstrainedKeys($constraint)
	{
		call_user_func_array($constraint, array($this->related));

		return $this->related->select($this->related->getPrimaryKey())
		                     ->select($this->relatedKey)
		                     ->rows()
		                     ->fieldsByKey($this->relatedKey);
	}

	/**
	 * Loads the relationship content, and sets it on the related model
	 *
	 * @param  array  $rows the rows that we'll be seeding
	 * @param  string $name the relationship name that we'll use to attach to the rows
	 * @param  string $subs the nested relationships that should be passed on to the child
	 * @return object
	 * @since  1.3.2
	 **/
	public function seedRelationship($rows, $name, $subs=null)
	{
		if (!$keys = $rows->fieldsByKey($this->localKey))
		{
			return $rows;
		}

		$relations = $this->getRelations($keys);

		if (isset($subs))
		{
			$relations = $relations->including($subs);
		}

		$resultsByRelatedKey = $this->getResultsByRelatedKey($relations);

		// Add the relationships back to the original models
		foreach ($rows as $row)
		{
			if (isset($resultsByRelatedKey[$row->{$this->localKey}]))
			{
				$row->addRelationship($name, $resultsByRelatedKey[$row->{$this->localKey}]);
			}
		}

		return $rows;
	}

	/**
	 * Gets the relations that will be seeded on to the provided rows
	 *
	 * @param  array $keys the keys for which to fetch related items
	 * @return array
	 * @since  1.3.2
	 **/
	protected function getRelations($keys)
	{
		return $this->related->whereIn($this->relatedKey, array_unique($keys));
	}

	/**
	 * Sorts the relations into arrays keyed by the related key
	 *
	 * @param  array $relations the relations to sort
	 * @return array
	 * @since  1.3.2
	 **/
	protected function getResultsByRelatedKey($relations)
	{
		$resultsByRelatedKey = [];

		foreach ($relations as $relation)
		{
			if (!isset($resultsByRelatedKey[$relation->{$this->relatedKey}]))
			{
				$resultsByRelatedKey[$relation->{$this->relatedKey}] = new Rows;
			}

			$resultsByRelatedKey[$relation->{$this->relatedKey}]->push($relation);
		}

		return $resultsByRelatedKey;
	}
}