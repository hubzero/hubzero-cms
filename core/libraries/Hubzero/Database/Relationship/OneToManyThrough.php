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
 * @package   hubzero-cms
 * @author    Sam Wilson <samwilson@purdue.edu>
 * @copyright Copyright 2005-2015 HUBzero Foundation, LLC.
 * @license   http://opensource.org/licenses/MIT MIT
 * @since     Class available since release 2.0.0
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
	 * @var  string
	 **/
	protected $associativeTable = null;

	/**
	 * Key on the left side of the associative table
	 *
	 * @var  string
	 **/
	protected $associativeLocal = null;

	/**
	 * Key on the right side of the associative table
	 *
	 * @var  string
	 **/
	protected $associativeRelated = null;

	/**
	 * Constructs a new object instance
	 *
	 * @param   \Hubzero\Database\Relational|static  $model               The local model
	 * @param   \Hubzero\Database\Relational|static  $related             The related model
	 * @param   string                               $associativeTable    The associative entity
	 * @param   string                               $associativeLocal    The local key on the associative table
	 * @param   string                               $associativeRelated  The related key on the associative table
	 * @return  void
	 * @since   2.0.0
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
	 * @return  object
	 * @since   2.0.0
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
	 * @param   closure  $constraint  The constraint function to apply
	 * @return  array
	 * @since   2.0.0
	 **/
	public function getConstrainedKeys($constraint)
	{
		$this->mediate();

		return array_unique($this->getConstrained($constraint)->fieldsByKey($this->associativeLocal));
	}

	/**
	 * Gets the constrained count
	 *
	 * @param   int     $count     The count to limit by
	 * @param   string  $operator  The comparison operator used between the column and the count
	 * @return  array
	 * @since   2.0.0
	 **/
	public function getConstrainedKeysByCount($count, $operator = '>=')
	{
		$associativeLocal = $this->associativeLocal;

		return $this->getConstrainedKeys(function($related) use ($count, $associativeLocal, $operator)
		{
			$related->group($associativeLocal)->having('COUNT(*)', $operator, $count);
		});
	}

	/**
	 * Joins the intermediate and related tables together to the model for the pending query
	 *
	 * @return  $this
	 * @since   2.0.0
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
	 * @return  $this
	 * @since   2.0.0
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
	 * @param   array    $keys        The keys for which to fetch related items
	 * @param   closure  $constraint  The constraint function to limit related items
	 * @return  array
	 * @since   2.0.0
	 **/
	protected function getRelations($keys, $constraint = null)
	{
		$this->mediate();

		if (isset($constraint)) call_user_func_array($constraint, array($this->related));

		return $this->related->whereIn($this->associativeTable . '.' . $this->associativeLocal, array_unique($keys));
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
			if (!isset($resultsByRelatedKey[$relation->{$this->associativeLocal}]))
			{
				$resultsByRelatedKey[$relation->{$this->associativeLocal}] = new Rows;
			}

			$resultsByRelatedKey[$relation->{$this->associativeLocal}]->push($relation);
		}

		return $resultsByRelatedKey;
	}
}