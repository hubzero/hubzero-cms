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