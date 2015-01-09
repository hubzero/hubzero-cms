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

/**
 * Database base relationship
 */
class Relationship
{
	/**
	 * The primary model
	 *
	 * @var \Hubzero\Database\Relational|static
	 **/
	protected $model = null;

	/**
	 * The related model
	 *
	 * @var \Hubzero\Database\Relational|static
	 **/
	protected $related = null;

	/**
	 * The local key (probably 'id')
	 *
	 * @var string
	 **/
	protected $localKey = null;

	/**
	 * The related key (probably 'modelName_id')
	 *
	 * @var string
	 **/
	protected $relatedKey = null;

	/**
	 * Constructs a new object instance
	 *
	 * @param  \Hubzero\Database\Relational|static $model      the primary model
	 * @param  \Hubzero\Database\Relational|static $related    the related model
	 * @param  \Hubzero\Database\Relational|static $localKey   the local key
	 * @param  \Hubzero\Database\Relational|static $relatedKey the related key
	 * @return void
	 * @since  1.3.2
	 **/
	public function __construct($model, $related, $localKey, $relatedKey)
	{
		$this->model      = $model;
		$this->related    = $related;
		$this->localKey   = $localKey;
		$this->relatedKey = $relatedKey;
	}

	/**
	 * Handles calls to undefined methods, assuming they should be passed up to the model
	 *
	 * @param  string $name the method name being called
	 * @param  array  $arguments the method arguments provided
	 * @return mixed
	 * @since  1.3.2
	 **/
	public function __call($name, $arguments)
	{
		return call_user_func_array(array($this->constrain(), $name), $arguments);
	}

	/**
	 * Returns the key name of the primary table
	 *
	 * @return string
	 * @since  1.3.2
	 **/
	public function getLocalKey()
	{
		return $this->localKey;
	}

	/**
	 * Returns the key name of the related table
	 *
	 * @return string
	 * @since  1.3.2
	 **/
	public function getRelatedKey()
	{
		return $this->relatedKey;
	}

	/**
	 * Fetch results of relationship
	 *
	 * @return \Hubzero\Database\Relational
	 * @since  1.3.2
	 **/
	public function rows()
	{
		$rows    = $this->constrain()->rows();
		$related = $this->related;

		return ($rows->count()) ? $rows->first() : $related::blank();
	}

	/**
	 * Constrains the relationship content to the applicable rows on the related model
	 *
	 * @return object
	 * @since  1.3.2
	 **/
	public function constrain()
	{
		return $this->related->whereEquals($this->relatedKey, $this->model->{$this->localKey});
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
		call_user_func_array($constraint, array($this->related));

		// Return the ids resulting from the contraint query
		return $this->related->select($this->relatedKey)->rows()->fieldsByKey($this->relatedKey);
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
		$this->model->select($this->model->getQualifiedFieldName('*'))
		            ->join($this->related->getTableName(),
		                   $this->model->getQualifiedFieldName($this->localKey),
		                   $this->related->getQualifiedFieldName($this->relatedKey),
		                   'LEFT OUTER');

		return $this;
	}

	/**
	 * Associates the rows provided back to the model by way of their proper keys
	 *
	 * @param  array $rows
	 * @return array
	 * @since  1.3.2
	 **/
	public function associate($rows)
	{
		foreach ($rows as $model)
		{
			$model->set($this->relatedKey, $this->model->getPkValue());
		}

		return $rows;
	}

	/**
	 * Saves a new related model with the given data
	 *
	 * @param  array $data the data being saved on the new model
	 * @return bool
	 * @since  1.3.2
	 **/
	public function save($data)
	{
		$related = $this->related;
		$model   = $related::newFromResults($data);

		$model->set($this->relatedKey, $this->model->getPkValue());

		return $model->save();
	}

	/**
	 * Deletes all rows attached to the current model
	 *
	 * @return bool
	 * @since  1.3.2
	 **/
	public function destroyAll()
	{
		// @FIXME: could make this a single query...
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
	 * Loads the relationship content, and sets it on the related model
	 *
	 * This is used when pre-loading relationship content
	 * via ({@link \Hubzero\Database\Relational::including()})
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
		else
		{
			$relations = $relations->rows();
		}

		foreach ($rows as $row)
		{
			if ($related = $relations->seek($row->{$this->localKey}))
			{
				$row->addRelationship($name, $related);
			}
			else
			{
				$related = $this->related;
				$row->addRelationship($name, $related::blank());
			}
		}

		return $rows;
	}
}