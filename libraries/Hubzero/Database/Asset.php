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
 * @since     Class available since release 1.3.2
 */

namespace Hubzero\Database;

/**
 * Database asset helper class for Joomla permissions compatibility
 */
class Asset
{
	/**
	 * The database model
	 *
	 * @var \Hubzero\Database\Relational|static
	 **/
	private $model = null;

	/**
	 * Constructs a new object, setting the model
	 *
	 * @param  object $model the model to which the asset will refer
	 * @return void
	 * @since  1.3.2
	 **/
	public function __construct($model)
	{
		$this->model = $model;
	}

	/**
	 * Resolves the asset id based on the default parameters and expectations
	 *
	 * @param  object $model the database model to which the asset refers
	 * @return int
	 * @since  1.3.2
	 **/
	public static function resolve($model)
	{
		return with(new self($model))->getId();
	}

	/**
	 * Deletes the asset entry for the provided model
	 *
	 * @param  object $model the model being deleted
	 * @return bool
	 * @since  1.3.2
	 **/
	public static function destroy($model)
	{
		return with(new self($model))->delete();
	}

	/**
	 * Gets the asset id for the object instance
	 *
	 * @return int
	 * @since  1.3.2
	 **/
	public function getId()
	{
		// Check for current asset id and compute other vars
		$current  = $this->model->get('asset_id', null);
		$parentId = $this->getAssetParentId();
		$name     = $this->getAssetName();
		$title    = $this->getAssetTitle();

		// Get joomla jtable model for assets
		$asset = \JTable::getInstance('Asset', 'JTable', array('dbo' => \JFactory::getDbo()));
		$asset->loadByName($name);

		// Re-inject the asset id into the model
		$this->model->set('asset_id', $asset->id);

		if ($asset->getError()) return false;

		// Specify how a new or moved node asset is inserted into the tree
		if (!$this->model->get('asset_id', null) || $asset->parent_id != $parentId)
		{
			$asset->setLocation($parentId, 'last-child');
		}

		// Prepare the asset to be stored
		$asset->parent_id = $parentId;
		$asset->name      = $name;
		$asset->title     = $title;

		if ($this->model->assetRules instanceof \JAccessRules)
		{
			$asset->rules = (string)$this->model->assetRules;
		}

		if (!$asset->check() || !$asset->store()) return false;

		// Register an event to update the asset name once we know the model id
		if ($this->model->isNew())
		{
			$me = $this;
			\Hubzero\Console\Event::register(
				$this->model->getTableName() . '.new',
				function($model) use ($asset, $me)
				{
					$asset->name = $me->getAssetName();
					$asset->store();
				});
		}

		// Return the id
		return (int)$asset->id;
	}

	/**
	 * Deletes the current asset entry
	 *
	 * @return bool
	 * @since  1.3.2
	 **/
	public function delete()
	{
		$asset = \JTable::getInstance('Asset');

		if ($asset->loadByName($this->getAssetName()))
		{
			if (!$asset->delete()) return false;
		}

		return true;
	}

	/**
	 * Computes the (distinct) name of the asset
	 *
	 * @return string
	 * @since  1.3.2
	 */
	private function getAssetName()
	{
		// @FIXME: this scheme won't always work...
		//          * namespace isn't always defined, at which point the model name is the namespace
		//          * namespace might be something like time_hub, which should become time.hub
		//          * non-integer ids will fail
		return "com_{$this->model->getNamespace()}.{$this->model->getModelName()}." . (int)$this->model->getPkValue();
	}

	/**
	 * Gets the title to use for the asset table
	 *
	 * @return string
	 * @since  1.3.2
	 */
	private function getAssetTitle()
	{
		// @FIXME: need a way to override this
		return $this->model->name;
	}

	/**
	 * Gets the parent asset id for the record
	 *
	 * @return int
	 * @since  1.3.2
	 */
	private function getAssetParentId()
	{
		$assetId = null;

		// Build the query to get the asset id for the parent category
		$query = new Query;
		$query->select('id')
		      ->from('#__assets')
		      ->whereEquals('name', 'com_' . $this->model->getNamespace());

		if ($results = $query->fetch())
		{
			$result  = $results[0];
			$assetId = (int)$result->id;
		}

		return ($assetId) ? $assetId : $this->getRootId();
	}

	/**
	 * Gets the root asset id from the #__assets table, defaulting to 1
	 *
	 * @return int
	 * @since   11.1
	 */
	private function getRootId()
	{
		$assets = self::getInstance('Asset', 'JTable', array('dbo' => \JFactory::getDbo()));
		$rootId = $assets->getRootId();
		if (!empty($rootId))
		{
			return $rootId;
		}

		return 1;
	}
}