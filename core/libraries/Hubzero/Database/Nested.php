<?php
/**
 * @package    framework
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Hubzero\Database;

/**
 * Database ORM class for implementing nested set records
 */
class Nested extends Relational
{
	/**
	 * Scopes to limit the realm of the nested set functions
	 *
	 * @var  array
	 **/
	protected $scopes = [];

	/**
	 * Updates all subsequent vars after new child insertion
	 *
	 * @param   string  $pos   The position being updated, whether left or right
	 * @param   int     $base  The base level after which values should be changed
	 * @param   bool    $add   Whether or not we're adding or subtracted from existing
	 * @return  $this
	 * @since   2.1.0
	 **/
	private function updateTrailing($pos = 'lft', $base = 0, $add = true)
	{
		// Reposition new values of displaced items
		$query = $this->getQuery();

		$query->update($this->getTableName());
		$query->set([
			$pos => new Value\Raw($pos . ($add ? '+' : '-') . '2'),
		]);
		$query->where($pos, '>=', $base)
		      ->where('id', '!=', $this->id)
		      ->execute();

		return $this;
	}

	/**
	 * Resolves the trailing left and right values for the new model
	 *
	 * @param   int    $base  The base level after which values should be changed
	 * @return  $this
	 * @since   2.1.0
	 **/
	private function resolveTrailing($base, $add = true)
	{
		return $this->updateTrailing('lft', $base, $add)
		            ->updateTrailing('rgt', $base, $add);
	}

	/**
	 * Establishes the model as a proper object as needed
	 *
	 * @param   object|int  $model  The model to resolve
	 * @return  $this
	 * @since   2.1.0
	 **/
	private function establishIsModel(&$model)
	{
		// Turn model into an object if need be
		if (!is_object($model))
		{
			$model = static::oneOrFail((int) $model);
		}

		return $this;
	}

	/**
	 * Sets the default scopes on the model
	 *
	 * @param   object|int  $parent  The parent of the child being created
	 * @return  $this
	 * @since   2.1.0
	 **/
	private function establishBaseParametersFromParent($parent)
	{
		$this->set('parent_id', $parent->id);
		$this->set('level', $parent->level + 1);

		return $this->applyScopes($parent);
	}

	/**
	 * Applies the scopes of the given model to the current
	 *
	 * @param   object|int  $parent  The parent from which to inherit
	 * @param   string      $method  The way in which scopes are applied
	 * @return  $this
	 * @since   2.1.0
	 **/
	private function applyScopes($parent, $method = 'set')
	{
		// Inherit scopes from parent
		foreach ($this->scopes as $scope)
		{
			$this->$method($scope, $parent->$scope);
		}

		return $this;
	}

	/**
	 * Applies the scopes of the given model to the current pending query
	 *
	 * @param   object|int  $parent  The parent from which to inherit
	 * @return  $this
	 * @since   2.1.0
	 **/
	private function applyScopesWhere($parent)
	{
		return $this->applyScopes($parent, 'whereEquals');
	}

	/**
	 * Saves the current model to the database as the nth child of the given parent
	 *
	 * @param   object|int  $parent  The parent of the child being created
	 * @return  bool
	 * @since   2.1.0
	 **/
	public function saveAsChildOf($parent)
	{
		$this->establishIsModel($parent)
		     ->establishBaseParametersFromParent($parent);

		// Compute the location where the item should reside
		$this->set('lft', $parent->rgt);
		$this->set('rgt', $parent->rgt + 1);

		// Save
		if (!$this->save())
		{
			return false;
		}

		// Reposition new values of displaced items
		$this->resolveTrailing($parent->rgt);

		return true;
	}

	/**
	 * Saves the current model to the database as the first child of the given parent
	 *
	 * @param   object|int  $parent  The parent of the child being created
	 * @return  bool
	 * @since   2.1.0
	 **/
	public function saveAsFirstChildOf($parent)
	{
		$this->establishIsModel($parent)
		     ->establishBaseParametersFromParent($parent);

		// Compute the location where the item should reside
		$this->set('lft', $parent->lft + 1);
		$this->set('rgt', $parent->lft + 2);

		// Save
		if (!$this->save())
		{
			return false;
		}

		// Reposition new values of displaced items
		$this->resolveTrailing($parent->lft + 1);

		return true;
	}

	/**
	 * Saves the current model to the database as the last child of the given parent
	 *
	 * @param   object|int  $parent  The parent of the child being created
	 * @return  bool
	 * @since   2.1.0
	 **/
	public function saveAsLastChildOf($parent)
	{
		return $this->saveAsChildOf($parent);
	}

	/**
	 * Saves a new root node element
	 *
	 * @return  bool
	 * @since   2.1.0
	 **/
	public function saveAsRoot()
	{
		// Compute the location where the item should reside
		$this->set('parent_id', 0);
		$this->set('level', 0);
		$this->set('lft', 0);
		$this->set('rgt', 1);

		// Save
		return $this->save();
	}

	/**
	 * Deletes a model, rearranging subordinate nodes as appropriate
	 *
	 * @return  bool
	 * @since   2.1.0
	 **/
	public function destroy()
	{
		if (!parent::destroy())
		{
			return false;
		}

		foreach ($this->getDescendants() as $descendant)
		{
			$descendant->destroy();

			// We have to decrement our internal reference to right here
			// so that we ultimately resolve trailing below based on the
			// properly updated value, otherwise anything upstream of
			// what we're destroying won't be properly updated
			$this->rgt -= 2;
		}

		// Reposition new values of displaced items
		$this->resolveTrailing($this->rgt, false);

		return true;
	}

	/**
	 * Establishes the query for the immediate children of the current model
	 *
	 * @return  array
	 * @since   2.1.0
	 **/
	public function children()
	{
		return $this->descendants(1);
	}

	/**
	 * Grabs the immediate children of the current model
	 *
	 * @return  array
	 * @since   2.1.0
	 **/
	public function getChildren()
	{
		return $this->children()->rows();
	}

	/**
	 * Establishes the query for all of the descendants of the current model
	 *
	 * @param   int  $level  The level to limit to
	 * @return  array
	 * @since   2.1.0
	 **/
	public function descendants($level = null)
	{
		$instance = self::blank();
		$instance->where('level', '>', $this->level)
		         ->order('lft', 'asc');

		if (isset($level))
		{
			$instance->where('level', '<=', $this->level + $level);
		}

		return $instance->where('lft', '>', $this->lft)
		                ->where('rgt', '<', $this->rgt)
		                ->applyScopesWhere($this);
	}

	/**
	 * Grabs all of the descendants of the current model
	 *
	 * @param   int  $level  The level to limit to
	 * @return  array
	 * @since   2.1.0
	 **/
	public function getDescendants($level = null)
	{
		return $this->descendants($level)->rows();
	}
}
