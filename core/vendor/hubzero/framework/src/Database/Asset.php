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
 * @package   framework
 * @author    Sam Wilson <samwilson@purdue.edu>
 * @copyright Copyright 2005-2015 HUBzero Foundation, LLC.
 * @license   http://opensource.org/licenses/MIT MIT
 * @since     Class available since release 2.0.0
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

		// Get joomla jtable model for assets
		$asset = \JTable::getInstance('Asset', 'JTable', array('dbo' => \App::get('db')));
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
			Event::listen(
				function($event) use ($asset, $me)
				{
					$asset->name = $me->getAssetName();
					$asset->store();
				}, $this->model->getTableName() . '_new');
		}

		// Return the id
		return (int)$asset->id;
	}

	/**
	 * Deletes the current asset entry
	 *
	 * @return  bool
	 * @since   2.0.0
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
	 * @return  int
	 * @since   2.0.0
	 */
	private function getRootId()
	{
		$assets = \JTable::getInstance('Asset', 'JTable', array('dbo' => \App::get('db')));
		$rootId = $assets->getRootId();

		if (!empty($rootId))
		{
			return $rootId;
		}

		return 1;
	}
}