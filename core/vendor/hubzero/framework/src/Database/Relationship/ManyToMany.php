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
 * @package   framework
 * @author    Sam Wilson <samwilson@purdue.edu>
 * @copyright Copyright 2005-2015 HUBzero Foundation, LLC.
 * @license   http://opensource.org/licenses/MIT MIT
 * @since     Class available since release 2.0.0
 */

namespace Hubzero\Database\Relationship;

/**
 * Database many to many relationship
 */
class ManyToMany extends OneToManyThrough
{
	/**
	 * Fetches the results of relationship
	 *
	 * @return  \Hubzero\Database\Relational
	 * @since   2.0.0
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
	 * Associates the model provided back to the model by way of their proper keys
	 *
	 * @param   object   $model     The model to associate
	 * @param   closure  $callback  A callback to potentially append additional data
	 * @return  object
	 * @since   2.0.0
	 **/
	public function associate($model, $callback = null)
	{
		$relationship = $this;
		Event::listen(
			function($event) use ($model, $relationship)
			{
				$relationship->connect([$model->id]);
			},
			$model->getTableName() . '_new'
		);

		return $model;
	}

	/**
	 * Joins the related table together with the intermediate table for the pending query
	 *
	 * This is primarily used when we're getting the related results and we need to work
	 * our way backwards through the intermediate table.
	 *
	 * @return  $this
	 * @since   2.0.0
	 **/
	public function mediate()
	{
		parent::mediate();

		// We also want to grab any associative fields at this time, rather than having to come back for them later
		// To do that, we'll prefix the columns and then strip them after the query
		$columns = $this->model->getStructure()->getTableColumns($this->associativeTable);

		// Get rid of known columns (don't use a primary key other than id and you'll be fine here!)
		if (isset($columns['id'])) unset($columns['id']);
		if (isset($this->shifter)) unset($columns[$this->shifter]);
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
	 * @param   array  $ids  The identifiers to place in the associative table
	 * @return  $this
	 * @since   2.0.0
	 **/
	public function connect($ids)
	{
		if (is_array($ids) && count($ids) > 0)
		{
			foreach ($ids as $id => $associative)
			{
				// Build base data
				$id   = (is_array($associative)) ? $id : $associative;
				$data = array_merge($this->getConnectionData(), [$this->associativeRelated => $id]);

				// If we have associative data, include that in the query
				if (is_array($associative)) $data = array_merge($data, $associative);

				// Save data
				$query = $this->model->getQuery()->push($this->associativeTable, $data, true);
			}
		}

		return $this;
	}

	/**
	 * Generates the connection data needed to create the associative entry
	 *
	 * @return  array
	 * @since   2.0.0
	 **/
	protected function getConnectionData()
	{
		return [$this->associativeLocal => $this->model->getPkValue()];
	}

	/**
	 * Removes the relationship between the two sides of the many to many
	 * (not deleting either of the actual sides of the models themselves)
	 *
	 * @param   array    $ids         The identifiers to remove from the associative table
	 * @param   closure  $constraint  Additional constraints to place on the query
	 * @return  $this
	 * @since   2.0.0
	 **/
	public function disconnect($ids, $constraint = null)
	{
		if (is_array($ids) && count($ids) > 0)
		{
			$query = $this->model->getQuery();
			$query->delete($this->associativeTable)
			      ->whereEquals($this->associativeLocal, $this->model->getPkValue())
			      ->whereIn($this->associativeRelated, $ids);

			if (isset($constraint) && is_callable($constraint)) call_user_func_array($constraint, [$query]);

			$query->execute();
		}

		return $this;
	}

	/**
	 * Syncs the provided identifiers back to the parent model by way of associative entities,
	 * deleting ones that should no longer be there, and adding ones that are missing.
	 *
	 * @param   array  $ids  The identifiers to place in the associative table
	 * @return  $this
	 * @since   2.0.0
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