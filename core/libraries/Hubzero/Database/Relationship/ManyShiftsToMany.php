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

/**
 * Database many shifts to many relationship
 */
class ManyShiftsToMany extends ManyToMany
{
	/**
	 * The field identifying model type
	 *
	 * @var  string
	 **/
	protected $shifter = null;

	/**
	 * Constructs a new object instance
	 *
	 * @param   \Hubzero\Database\Relational|static  $model              The primary model
	 * @param   \Hubzero\Database\Relational|static  $related            The related model
	 * @param   string                               $associativeTable   The associative entity
	 * @param   string                               $localKey           The local key
	 * @param   string                               $relatedKey         The related key
	 * @param   string                               $shifter            The field identifying model type
	 * @return  void
	 * @since   2.0.0
	 **/
	public function __construct($model, $related, $associativeTable, $localKey, $relatedKey, $shifter)
	{
		parent::__construct($model, $related, $associativeTable, $localKey, $relatedKey);

		$this->shifter = $shifter;
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

		// Add this where clause in mediation as it's really a factor of the join itself
		$this->related->whereEquals($this->associativeTable . '.' . $this->shifter, strtolower($this->model->getModelName()));

		return $this;
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
		$associativeTable = $this->associativeTable;
		$associativeLocal = $this->associativeLocal;
		$shifter          = $this->shifter;
		$model            = $this->model;

		return $this->getConstrainedKeys(function($related) use ($count, $associativeTable, $associativeLocal, $shifter, $model, $operator)
		{
			$related->whereEquals($associativeTable . '.' . $shifter, strtolower($model->getModelName()))
			        ->group($shifter)
			        ->group($associativeLocal)
			        ->having('COUNT(*)', $operator, $count);
		});
	}

	/**
	 * Generates the connection data needed to create the associative entry
	 *
	 * @return  array
	 * @since   2.0.0
	 **/
	protected function getConnectionData()
	{
		return [$this->associativeLocal => $this->model->getPkValue(), $this->shifter => strtolower($this->model->getModelName())];
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
		$associativeTable = $this->associativeTable;
		$shifter          = $this->shifter;
		$model            = $this->model;

		parent::disconnect($ids, function($query) use ($associativeTable, $model, $shifter)
		{
			$query->whereEquals($associativeTable . '.' . $shifter, strtolower($model->getModelName()));
		});

		return $this;
	}
}