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
	 * @var string
	 **/
	protected $shifter = null;

	/**
	 * Constructs a new object instance
	 *
	 * @param   \Hubzero\Database\Relational|static  $model              the primary model
	 * @param   \Hubzero\Database\Relational|static  $related            the related model
	 * @param   string                               $associativeTable   the associative entity
	 * @param   string                               $localKey           the local key
	 * @param   string                               $relatedKey         the related key
	 * @param   string                               $shifter            the field identifying model type
	 * @return  void
	 * @since   2.0.0
	 **/
	public function __construct($model, $related, $associativeTable, $localKey, $relatedKey, $shifter)
	{
		parent::__construct($model, $related, $associativeTable, $localKey, $relatedKey);

		$this->shifter = $shifter;
	}

	/**
	 * Constrains the relationship content to the applicable rows on the related model
	 *
	 * @return  object
	 * @since   2.0.0
	 **/
	public function constrain()
	{
		return parent::constrain()->whereEquals($this->associativeTable . '.' . $this->shifter, strtolower($this->model->getModelName()));
	}

	/**
	 * Gets the constrained count
	 *
	 * @param  int $count the count to limit by
	 * @return array
	 * @since  1.3.2
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
	 * Gets the relations that will be seeded on to the provided rows
	 *
	 * @param  array   $keys       the keys for which to fetch related items
	 * @param  closure $constraint the constraint function to limit related items
	 * @return array
	 * @since  1.3.2
	 **/
	protected function getRelations($keys, $constraint=null)
	{
		return parent::getRelations($keys, $constraint)->whereEquals($this->associativeTable . '.' . $this->shifter, strtolower($this->model->getModelName()));
	}

	/**
	 * Generates the connection data needed to create the associative entry
	 *
	 * @return array
	 * @since  1.3.2
	 **/
	protected function getConnectionData()
	{
		return [$this->associativeLocal => $this->model->getPkValue(), $this->shifter => strtolower($this->model->getModelName())];
	}

	/**
	 * Removes the relationship between the two sides of the many to many
	 * (not deleting either of the actual sides of the models themselves)
	 *
	 * @param  array   $ids        the identifiers to remove from the associative table
	 * @param  closure $constraint additional constraints to place on the query
	 * @return $this
	 * @since  1.3.2
	 **/
	public function disconnect($ids, $constraint=null)
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