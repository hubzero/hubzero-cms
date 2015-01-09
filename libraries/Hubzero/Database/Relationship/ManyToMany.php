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
use Hubzero\Database\Query;

/**
 * Database many to many relationship
 */
class ManyToMany extends Relationship
{
	/**
	 * The associative table used to capture the many to many relationships
	 *
	 * @var string
	 **/
	private $associativeTable = null;

	/**
	 * Constructs a new object instance
	 *
	 * @param  \Hubzero\Database\Relational|static $model            the local model
	 * @param  \Hubzero\Database\Relational|static $related          the related model
	 * @param  string                              $associativeTable the associative entity
	 * @param  string                              $localKey         the local key on the associative table
	 * @param  string                              $relatedKey       the related key on the associative table
	 * @return void
	 * @since  1.3.2
	 **/
	public function __construct($model, $related, $associativeTable, $localKey, $relatedKey)
	{
		parent::__construct($model, $related, $localKey, $relatedKey);

		$this->associativeTable = $associativeTable;
	}

	/**
	 * Fetches tbe results of tbe relationship
	 *
	 * @return \Hubzero\Database\Rows
	 * @since  1.3.2
	 **/
	public function rows()
	{
		return $this->constrain()->rows();
	}

	/**
	 * Loads the relationship content and returns the related side of the model
	 *
	 * @return object
	 * @since  1.3.2
	 **/
	public function constrain()
	{
		$this->join();

		$this->related->whereEquals($this->associativeTable . '.' . $this->localKey, $this->model->getPkValue());

		return $this->related;
	}

	/**
	 * Joins the related table together with the intermediate table for the pending query
	 *
	 * @return $this
	 * @since  1.3.2
	 **/
	public function join()
	{
		$this->related->select($this->related->getQualifiedFieldName('*'))
		              ->select($this->localKey)
		              ->join($this->associativeTable,
		                     $this->related->getQualifiedFieldName($this->related->getPrimaryKey()),
		                     $this->relatedKey);

		return $this;
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
		if (!$keys = $rows->fieldsByKey($this->model->getPrimaryKey()))
		{
			return $rows;
		}

		$this->join();
		$relations = $this->related->whereIn($this->associativeTable . '.' . $this->localKey, array_unique($keys));

		if (isset($subs))
		{
			$relations = $relations->including($subs);
		}

		$resultsByRelatedKey = array();

		foreach ($relations as $relation)
		{
			if (!isset($resultsByRelatedKey[$relation->{$this->localKey}]))
			{
				$resultsByRelatedKey[$relation->{$this->localKey}] = new Rows;
			}

			$resultsByRelatedKey[$relation->{$this->localKey}]->push($relation);
		}

		foreach ($rows as $row)
		{
			if (isset($resultsByRelatedKey[$row->{$this->model->getPrimaryKey()}]))
			{
				$row->addRelationship($name, $resultsByRelatedKey[$row->{$this->model->getPrimaryKey()}]);
			}
		}

		return $rows;
	}

	/**
	 * Connects the provided identifiers back to the parent model by way of associative entities
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
				$data  = [$this->localKey => $localKeyValue, $this->relatedKey => $id];
				$query = with(new Query)->insert($this->associativeTable, $data, true);
			}
		}
	}
}