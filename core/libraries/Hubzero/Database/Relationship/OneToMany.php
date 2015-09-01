<?php
/**
 * HUBzero CMS
 *
 * Copyright 2005-2015 Purdue University. All rights reserved.
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
 * @copyright Copyright 2005-2015 Purdue University. All rights reserved.
 * @license   http://www.gnu.org/licenses/lgpl-3.0.html LGPLv3
 * @since     Class available since release 2.0.0
 */

namespace Hubzero\Database\Relationship;

use Hubzero\Database\Rows;

/**
 * Database one to many relationship
 */
class OneToMany extends Relationship
{
	/**
	 * Fetches the results of relationship
	 *
	 * @return  \Hubzero\Database\Relational
	 * @since   2.0.0
	 **/
	public function rows()
	{
		return $this->constrain()->rows();
	}

	/**
	 * Associates the models provided back to the model by way of their proper keys
	 *
	 * Because this is a one to many relationship, we could be setting either one
	 * or many items on the related side at a given time.  We must then be prepared
	 * to loop over the items.  
	 *
	 * @param   object|array   $models    A single model or array of models to associate
	 * @param   closure        $callback  A callback to potentially append additional data
	 * @return  object|array
	 * @since   2.0.0
	 **/
	public function associate($models, $callback = null)
	{
		if (is_array($models) || $models instanceof \Hubzero\Database\Rows)
		{
			foreach ($models as $model)
			{
				parent::associate($model, $callback);
			}
		}
		else
		{
			parent::associate($models, $callback);
		}

		return $models;
	}

	/**
	 * Saves new related models with the given data
	 *
	 * @param   array  $data  An array of datasets being saved to new models
	 * @return  bool
	 * @since   2.0.0
	 **/
	public function save($data)
	{
		// Check and make sure this is an array of arrays
		if (!is_array($data)) return false;

		if (isset($data[0]) && is_array($data[0]))
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
	 * @return  bool
	 * @since   2.0.0
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
	 * @param   closure  $constraint  The constraint function to apply
	 * @return  array
	 * @since   2.0.0
	 **/
	public function getConstrainedKeys($constraint)
	{
		$this->related->select($this->related->getPrimaryKey())
		              ->select($this->relatedKey);

		return $this->getConstrained($constraint)->fieldsByKey($this->relatedKey);
	}

	/**
	 * Loads the relationship content with the provided data
	 *
	 * @param   array   $rows  The rows that we'll be seeding
	 * @param   string  $data  The data to seed
	 * @param   string  $name  The name of the relationship
	 * @return  object
	 * @since   2.0.0
	 **/
	public function seedWithData($rows, $data, $name)
	{
		$resultsByRelatedKey = $this->getResultsByRelatedKey($data);

		return $this->seed($rows, $resultsByRelatedKey, $name);
	}

	/**
	 * Seeds the given rows with data
	 *
	 * @param   array   $rows  The rows to seed on to
	 * @param   array   $data  The data from which to seed
	 * @param   string  $name  The relationship name
	 * @return  array
	 * @since   2.0.0
	 **/
	protected function seed($rows, $data, $name)
	{
		// Add the relationships back to the original models
		foreach ($rows as $row)
		{
			if (isset($data[$row->{$this->localKey}]))
			{
				$row->addRelationship($name, $data[$row->{$this->localKey}]);
			}
			else
			{
				$row->addRelationship($name, new Rows);
			}
		}

		return $rows;
	}

	/**
	 * Sorts the relations into arrays keyed by the related key
	 *
	 * @param   array  $relations  The relations to sort
	 * @return  array
	 * @since   2.0.0
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