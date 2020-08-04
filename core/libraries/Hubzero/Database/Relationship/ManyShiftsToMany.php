<?php
/**
 * @package    framework
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
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
