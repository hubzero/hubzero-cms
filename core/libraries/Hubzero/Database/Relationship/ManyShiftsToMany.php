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
 * @copyright Copyright 2005-2015 Purdue University. All rights reserved.
 * @license   http://www.gnu.org/licenses/lgpl-3.0.html LGPLv3
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
	 * @param   int  $count  The count to limit by
	 * @return  array
	 * @since   2.0.0
	 **/
	public function getConstrainedKeysByCount($count)
	{
		$associativeTable = $this->associativeTable;
		$associativeLocal = $this->associativeLocal;
		$shifter          = $this->shifter;
		$model            = $this->model;

		return $this->getConstrainedKeys(function($related) use ($count, $associativeTable, $associativeLocal, $shifter, $model)
		{
			$related->whereEquals($associativeTable . '.' . $shifter, strtolower($model->getModelName()))
			        ->group($shifter)
			        ->group($associativeLocal)
			        ->having('COUNT(*)', '>=', $count);
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