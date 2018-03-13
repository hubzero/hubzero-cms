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
 * @copyright Copyright 2005-2015 HUBzero Foundation, LLC.
 * @license   http://opensource.org/licenses/MIT MIT
 */

// No direct access
defined('_HZEXEC_') or die();

/**
 * Handles asynchrous enqueuement, helps maintain the index
 */
require_once Component::path('com_search') . DS . 'helpers' . DS . 'discoveryhelper.php';
require_once Component::path('com_search') . DS . 'helpers' . DS . 'solr.php';
require_once Component::path('com_search') . '/models/solr/searchcomponent.php';

use \Components\Search\Helpers\DiscoveryHelper;
use \Components\Search\Models\Solr\SearchComponent;
use \Hubzero\Search\Index;
use ReflectionClass;

class plgSearchSolr extends \Hubzero\Plugin\Plugin
{
	/**
	 * onContentSave 
	 * 
	 * @param   mixed  $table
	 * @param   mixed  $model
	 * @return  void
	 */
	public function onAddIndex($table, $model)
	{
		$modelClass = new ReflectionClass($model);
		$modelNamespace = explode('\\', $modelClass->getNamespaceName());
		$componentName = strtolower($modelNamespace[1]); 
		$searchComponent = SearchComponent::all()->whereEquals('name', $componentName)->row();
		if ($searchComponent && $searchComponent->get('state') == 1)
		{
			$searchModel = \Components\Search\Helpers\DiscoveryHelper::isSearchable($model);
			if ($searchModel === false)
			{
				$searchModel = $searchComponent->getSearchableModel();
				$searchModelBlank = new $searchModel;
				$searchModelTable = $searchModelBlank->getTableName();
				if ($table != $searchModelTable)
				{
					return false;
				}
				$model = $searchModel::one($model->get('id'));
			}

			if ($model)
			{
				$config = Component::params('com_search');
				$commitWithin = $config->get('solr_commit');
				$index = new \Hubzero\Search\Index($config);
				$modelIndex = $model->searchResult();
				if ($modelIndex !== false)
				{
					$index->updateIndex($modelIndex, $commitWithin);
				}
				else
				{
					$modelIndexId = $model->searchId();
					$index->delete($modelIndexId);
				}
			}
		}
	}

	/**
	 * onContentDestroy 
	 * 
	 * @param   mixed  $table
	 * @param   mixed  $model
	 * @return  void
	 */
	public function onRemoveIndex($table, $model)
	{
		// @TODO: Implement mechanism to send to Solr index
		// This Event is called in the Relational save() method.
		$modelName = '';
		if ($modelName = \Components\Search\Helpers\DiscoveryHelper::isSearchable($model))
		{
			$extensionName = strtolower(explode('\\', $modelName)[1]);
			$searchComponent = SearchComponent::all()->whereEquals('name', $extensionName)->row();
			if ($searchComponent->get('state') == 1)
			{
				$config = Component::params('com_search');
				$index = new \Hubzero\Search\Index($config);
				$modelIndexId = $model->searchId();
				$index->delete($modelIndexId);
			}
		}
	}
}
