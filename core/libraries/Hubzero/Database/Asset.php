<?php
/**
 * @package    framework
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Hubzero\Database;

use Hubzero\Access\Asset as Model;

/**
 * Database asset helper class for permissions compatibility
 */
class Asset
{
	/**
	 * The database model
	 *
	 * @var  \Hubzero\Database\Relational|static
	 **/
	private $model = null;

	/**
	 * Constructs a new object, setting the model
	 *
	 * @param   object  $model  The model to which the asset will refer
	 * @return  void
	 * @since   2.0.0
	 **/
	public function __construct($model)
	{
		$this->model = $model;
	}

	/**
	 * Resolves the asset id based on the default parameters and expectations
	 *
	 * @param   object  $model  The database model to which the asset refers
	 * @return  int
	 * @since   2.0.0
	 **/
	public static function resolve($model)
	{
		return with(new self($model))->getId();
	}

	/**
	 * Deletes the asset entry for the provided model
	 *
	 * @param   object  $model  The model being deleted
	 * @return  bool
	 * @since   2.0.0
	 **/
	public static function destroy($model)
	{
		return with(new self($model))->delete();
	}

	/**
	 * Gets the asset id for the object instance
	 *
	 * @return  int
	 * @since   2.0.0
	 **/
	public function getId()
	{
		// Check for current asset id and compute other vars
		$current  = $this->model->get('asset_id', null);
		$parentId = $this->getAssetParentId();
		$name     = $this->getAssetName();
		$title    = $this->getAssetTitle();
		$title    = $title ?: $name;

		// Get model for assets
		$asset = Model::oneByName($name);

		// Re-inject the asset id into the model
		$this->model->set('asset_id', $asset->get('id'));

		// Prepare the asset to be stored
		$asset->set('parent_id', $parentId);
		$asset->set('name', $name);
		$asset->set('title', $title);

		if ($this->model->assetRules instanceof \JAccessRules || $this->model->assetRules instanceof \Hubzero\Access\Rules)
		{
			$asset->set('rules', (string)$this->model->assetRules);
		}

		// Specify how a new or moved node asset is inserted into the tree
		if (!$this->model->get('asset_id', null) || $asset->parent_id != $parentId)
		{
			$parent = Model::one($parentId);

			if (!$asset->saveAsLastChildOf($parent))
			{
				return false;
			}
		}
		elseif (!$asset->save())
		{
			return false;
		}

		// Register an event to update the asset name once we know the model id
		if ($this->model->isNew())
		{
			$me = $this;
			\Event::listen(
				function($event) use ($asset, $me)
				{
					$asset->set('name', $me->getAssetName());
					$asset->save();
				},
				$this->model->getTableName() . '_new'
			);
		}

		// Return the id
		return (int)$asset->get('id');
	}

	/**
	 * Deletes the current asset entry
	 *
	 * @return  bool
	 * @since   2.0.0
	 **/
	public function delete()
	{
		$asset = Model::oneByName($this->getAssetName());

		if ($asset->get('id'))
		{
			if (!$asset->destroy())
			{
				return false;
			}
		}

		return true;
	}

	/**
	 * Computes the (distinct) name of the asset
	 *
	 * @return  string
	 * @since   2.0.0
	 */
	private function getAssetName()
	{
		// @FIXME: this scheme won't always work...
		//          * namespace isn't always defined, at which point the model name is the namespace
		//          * namespace might be something like time_hub, which should become time.hub
		//          * non-integer ids will fail
		return strtolower("com_{$this->model->getNamespace()}.{$this->model->getModelName()}.") . (int)$this->model->getPkValue();
	}

	/**
	 * Gets the title to use for the asset table
	 *
	 * @return  string
	 * @since   2.0.0
	 */
	private function getAssetTitle()
	{
		// @FIXME: need a way to override this
		return $this->model->name;
	}

	/**
	 * Gets the parent asset id for the record
	 *
	 * @return  int
	 * @since   2.0.0
	 */
	private function getAssetParentId()
	{
		$assetId = null;

		// Build the query to get the asset id for the parent category
		$asset = Model::oneByName('com_' . $this->model->getNamespace());

		if ($asset->get('id'))
		{
			$assetId = (int)$asset->get('id');
		}

		return ($assetId) ? $assetId : $this->getRootId();
	}

	/**
	 * Gets the root asset id from the #__assets table, defaulting to 1
	 *
	 * @return  int
	 * @since   2.0.0
	 */
	private function getRootId()
	{
		$rootId = Model::getRootId();

		if (empty($rootId))
		{
			$rootId = 1;
		}

		return $rootId;
	}
}
