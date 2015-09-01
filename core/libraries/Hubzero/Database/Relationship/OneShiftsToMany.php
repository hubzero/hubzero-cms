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

/**
 * Database one shifts to many relationship
 */
class OneShiftsToMany extends OneToMany
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
	 * @param   \Hubzero\Database\Relational|static  $model       The primary model
	 * @param   \Hubzero\Database\Relational|static  $related     The related model
	 * @param   \Hubzero\Database\Relational|static  $localKey    The local key
	 * @param   \Hubzero\Database\Relational|static  $relatedKey  The related key
	 * @param   string                               $shifter     The field identifying model type
	 * @return  void
	 * @since   2.0.0
	 **/
	public function __construct($model, $related, $localKey, $relatedKey, $shifter)
	{
		parent::__construct($model, $related, $localKey, $relatedKey);

		$this->shifter = $shifter;
	}

	/**
	 * Associates the models provided back to the model by way of their proper keys
	 *
	 * We use this time to also set a callback where we define our shifter.
	 *
	 * @param   object|array  $models    A single model or array of models to associate
	 * @param   closure       $callback  A callback to potentially append additional data
	 * @return  object|array
	 * @since   2.0.0
	 **/
	public function associate($models, $callback = null)
	{
		$modelName = $this->model->getModelName();
		$shifter   = $this->shifter;

		parent::associate($models, function($model) use ($modelName, $shifter)
		{
			$model->set($shifter, strtolower($modelName));
		});

		return $models;
	}

	/**
	 * Constrains the relationship content to the applicable rows on the related model
	 *
	 * @return  object
	 * @since   2.0.0
	 **/
	public function constrain()
	{
		return $this->related
		            ->whereEquals($this->relatedKey, $this->model->{$this->localKey})
		            ->whereEquals($this->shifter, strtolower($this->model->getModelName()));
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
		if (isset($constraint)) call_user_func_array($constraint, array($this->related));

		return $this->related
		            ->whereIn($this->relatedKey, array_unique($keys))
		            ->whereEquals($this->shifter, strtolower($this->model->getModelName()));
	}
}