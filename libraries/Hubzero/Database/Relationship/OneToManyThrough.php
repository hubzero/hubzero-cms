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
 * Database one to many through relationship
 *
 * This also serves as the base for the many to many relationship,
 * so some of the language herein may reflect that class as well.
 */
class OneToManyThrough extends OneToMany
{
	/**
	 * The associative table used to capture the through relationship
	 *
	 * @var string
	 **/
	protected $associativeTable = null;

	/**
	 * Key on the left side of the associative table
	 *
	 * @var string
	 **/
	protected $associativeLocal = null;

	/**
	 * Key on the right side of the associative table
	 *
	 * @var string
	 **/
	protected $associativeRelated = null;

	/**
	 * Constructs a new object instance
	 *
	 * @param  \Hubzero\Database\Relational|static $model              the local model
	 * @param  \Hubzero\Database\Relational|static $related            the related model
	 * @param  string                              $associativeTable   the associative entity
	 * @param  string                              $associativeLocal   the local key on the associative table
	 * @param  string                              $associativeRelated the related key on the associative table
	 * @return void
	 * @since  1.3.2
	 **/
	public function __construct($model, $related, $associativeTable, $associativeLocal, $associativeRelated)
	{
		parent::__construct($model, $related, $model->getPrimaryKey(), $related->getPrimaryKey());

		$this->associativeTable   = $associativeTable;
		$this->associativeLocal   = $associativeLocal;
		$this->associativeRelated = $associativeRelated;
	}

	/**
	 * Loads the relationship content and returns the related side of the model
	 *
	 * @return object
	 * @since  1.3.2
	 **/
	public function constrain()
	{
		$this->mediate();

		$this->related->whereEquals($this->associativeTable . '.' . $this->associativeLocal, $this->model->getPkValue());

		return $this->related;
	}

	/**
	 * Get keys based on a given constraint
	 *
	 * @param  closure $constraint the constraint function to apply
	 * @return array
	 * @since  1.3.2
	 **/
	public function getConstrainedKeys($constraint)
	{
		$this->mediate();

		return array_unique($this->getConstrained($constraint)->fieldsByKey($this->associativeLocal));
	}

	/**
	 * Joins the intermediate and related tables together to the model for the pending query
	 *
	 * @return $this
	 * @since  1.3.2
	 **/
	public function join()
	{
		// We do a left outer join here because we're not trying to limit the primary table's results
		// This function is primarily used when needing to sort by a field in the joined table
		$this->model->select($this->model->getQualifiedFieldName('*'))
		            ->select($this->related->getQualifiedFieldName('*'))
		            ->join($this->associativeTable,
		                   $this->model->getQualifiedFieldName($this->localKey),
		                   $this->associativeLocal,
		                   'LEFT OUTER')
		            ->join($this->related->getTableName(),
		                   $this->associativeRelated,
		                   $this->related->getQualifiedFieldName($this->relatedKey),
		                   'LEFT OUTER');

		return $this;
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
		$this->related->select($this->related->getQualifiedFieldName('*'))
		              ->select($this->associativeLocal)
		              ->join($this->associativeTable,
		                     $this->related->getQualifiedFieldName($this->relatedKey),
		                     $this->associativeRelated);

		return $this;
	}

	/**
	 * Gets the relations that will be seeded on to the provided rows
	 *
	 * @param  array   $keys       the keys for which to fetch related items
	 * @param  closure $constraint the constraint function to limit related items
	 * @return array
	 * @since  1.3.2
	 **/
	protected function getRelations($keys, $constraint=null)
	{
		$this->mediate();

		if (isset($constraint)) call_user_func_array($constraint, array($this->related));

		return $this->related->whereIn($this->associativeTable . '.' . $this->associativeLocal, array_unique($keys));
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
			if (!isset($resultsByRelatedKey[$relation->{$this->associativeLocal}]))
			{
				$resultsByRelatedKey[$relation->{$this->associativeLocal}] = new Rows;
			}

			$resultsByRelatedKey[$relation->{$this->associativeLocal}]->push($relation);
		}

		return $resultsByRelatedKey;
	}
}