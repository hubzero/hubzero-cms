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
class OneToManyThrough extends Relationship
{
	/**
	 * The parent side of the relationship
	 *
	 * @var \Hubzero\Database\Relational|static
	 **/
	private $one = null;

	/**
	 * The many side of the relationship
	 *
	 * @var \Hubzero\Database\Relational|static
	 **/
	private $many = null;

	/**
	 * The though portion of the relationship
	 *
	 * @var \Hubzero\Database\Relational|static
	 **/
	private $through = null;

	/**
	 * The relationship key on the one side
	 *
	 * @var string
	 **/
	private $oneKey = null;

	/**
	 * The key relating this model to the parent
	 *
	 * @var string
	 **/
	private $manyKey = null;

	/**
	 * Constructs a new object instance
	 *
	 * @param  \Hubzero\Database\Relational|static $one the one side
	 * @param  \Hubzero\Database\Relational|static $many the many side
	 * @param  \Hubzero\Database\Relational|static $through the through side
	 * @param  \Hubzero\Database\Relational|static $oneKey the parent key
	 * @param  \Hubzero\Database\Relational|static $manyKey the foreign key
	 * @return void
	 * @since  1.3.2
	 **/
	public function __construct($one, $many, $through, $oneKey, $manyKey)
	{
		$this->one        = $one;
		$this->many       = $many;
		$this->through    = $through;
		$this->oneKey     = $oneKey;
		$this->manyKey    = $manyKey;
	}

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
	 * Loads the relationship content and returns the many side of the model
	 *
	 * @return object
	 * @since  1.3.2
	 **/
	public function constrain()
	{
		$this->join();
		$this->many->whereEquals($this->through->getQualifiedFieldName($this->oneKey), $this->one->{$this->one->getPrimaryKey()});

		return $this->many;
	}

	/**
	 * Joins the related table together for the pending query
	 *
	 * @return $this
	 * @since  1.3.2
	 **/
	public function join()
	{
		// We do a left outer join here because we're not trying to limit the primary table's results
		// This function is primarily used when needing to sort by a field in the joined table
		$this->many
		     ->select($this->many->getQualifiedFieldName('*'))
		     ->select($this->through->getQualifiedFieldName($this->oneKey))
		     ->join($this->through->getTableName(),
		            $this->through->getQualifiedFieldName($this->through->getPrimaryKey()),
		            $this->many->getQualifiedFieldName($this->manyKey),
		            'LEFT OUTER');

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
		if (!$keys = $rows->fieldsByKey($this->one->getPrimaryKey()))
		{
			return $rows;
		}

		$this->join();
		$relations = $this->related->whereIn($this->through->getQualifiedFieldName($this->oneKey), array_unique($keys));

		if (isset($subs))
		{
			$relations = $relations->including($subs);
		}

		$resultsByRelatedKey = array();

		foreach ($relations as $relation)
		{
			if (!isset($resultsByRelatedKey[$relation->{$this->oneKey}]))
			{
				$resultsByRelatedKey[$relation->{$this->oneKey}] = new Rows;
			}

			$resultsByRelatedKey[$relation->{$this->oneKey}]->push($relation);
		}

		foreach ($rows as $row)
		{
			if (isset($resultsByRelatedKey[$row->{$this->one->getPrimaryKey()}]))
			{
				$row->addRelationship($name, $resultsByRelatedKey[$row->{$this->one->getPrimaryKey()}]);
			}
		}

		return $rows;
	}
}