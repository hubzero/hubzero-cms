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

		$relations = $this->related->whereIn($this->relatedKey, array_unique($keys));

		if (isset($subs))
		{
			$relations = $relations->including($subs);
		}

		$resultsByRelatedKey = array();

		foreach ($relations as $relation)
		{
			if (!isset($resultsByRelatedKey[$relation->{$this->relatedKey}]))
			{
				$resultsByRelatedKey[$relation->{$this->relatedKey}] = new Rows;
			}

			$resultsByRelatedKey[$relation->{$this->relatedKey}]->push($relation);
		}

		foreach ($rows as $row)
		{
			if (isset($resultsByRelatedKey[$row->{$this->localKey}]))
			{
				$row->addRelationship($name, $resultsByRelatedKey[$row->{$this->localKey}]);
			}
		}

		return $rows;
	}
}