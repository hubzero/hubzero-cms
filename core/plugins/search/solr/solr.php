<?php
/**
 * @package    hubzero-cms
 * @copyright  Copyright 2005-2019 HUBzero Foundation, LLC.
 * @license    http://opensource.org/licenses/MIT MIT
 */

// No direct access
defined('_HZEXEC_') or die();

/**
 * Handles asynchrous enqueuement, helps maintain the index
 */
require_once Component::path('com_search') . DS . 'helpers' . DS . 'discoveryhelper.php';
require_once Component::path('com_search') . DS . 'helpers' . DS . 'solr.php';
require_once Component::path('com_search') . '/models/solr/searchcomponent.php';

use Components\Search\Helpers\DiscoveryHelper;
use Components\Search\Models\Solr\SearchComponent;
use Components\Search\Models\Solr\Blacklist;
use Hubzero\Search\Index;

class plgSearchSolr extends \Hubzero\Plugin\Plugin
{
	/**
	 * Table exists or not
	 *
	 * @var  bool
	 */
	public static $tableExists = null;

	/**
	 * Does the required table exist?
	 *
	 * Operations are dependent upon this table
	 *
	 * @return  bool
	 */
	private function tableExists()
	{
		if (is_null(self::$tableExists))
		{
			self::$tableExists = (bool)App::get('db')->tableExists('#__solr_search_searchcomponents');
		}

		return self::$tableExists;
	}

	/**
	 * Add index
	 *
	 * @param   mixed  $table
	 * @param   mixed  $model
	 * @return  void
	 */
	public function onAddIndex($table, $model)
	{
		if (!$this->tableExists())
		{
			return;
		}

		$modelClass = new ReflectionClass($model);
		$modelNamespace = explode('\\', $modelClass->getNamespaceName());
		$componentName = strtolower($modelNamespace[1]);
		$searchComponent = SearchComponent::all()->whereEquals('name', $componentName)->row();
		if ($searchComponent && $searchComponent->get('state') == 1)
		{
			$indexResultModel = $this->getSearchableModel($table, $model, $searchComponent);
			if ($indexResultModel)
			{
				$config = Component::params('com_search');
				$commitWithin = $config->get('solr_commit');
				$index = new Hubzero\Search\Index($config);
				$method = '';
				$modelIndex = $indexResultModel->searchResult();
				$blackListIds = Blacklist::getDocIdsByScope($indexResultModel::searchNamespace());
				if ($modelIndex !== false && !in_array($modelIndex->id, $blackListIds))
				{
					$message = $index->updateIndex($modelIndex, $commitWithin);
					$method = 'update';
				}
				else
				{
					$modelIndexId = $indexResultModel->searchId();
					$message = $index->delete($modelIndexId);
					$method = 'delete';
				}
				Event::trigger('search.sendSolrRequest', array($modelIndex, $method));
				if ($message)
				{
					Notify::error($message);
				}
			}
		}
	}

	/**
	 * Remove index
	 *
	 * @param   mixed  $table
	 * @param   mixed  $model
	 * @return  void
	 */
	public function onRemoveIndex($table, $model)
	{
		if (!$this->tableExists())
		{
			return;
		}

		// @TODO: Implement mechanism to send to Solr index
		// This Event is called in the Relational save() method.
		$modelName = '';
		if ($modelName = Components\Search\Helpers\DiscoveryHelper::isSearchable($model))
		{
			$extensionName = strtolower(explode('\\', $modelName)[1]);
			$searchComponent = SearchComponent::all()->whereEquals('name', $extensionName)->row();
			if ($searchComponent->get('state') == 1)
			{
				$indexResultModel = $this->getSearchableModel($table, $model, $searchComponent);
				if ($indexResultModel)
				{
					$config = Component::params('com_search');
					$index = new Hubzero\Search\Index($config);
					$modelIndexId = $indexResultModel->searchId();
					$index->delete($modelIndexId);
					$method = 'delete';
					Event::trigger('search.sendSolrRequest', array($indexResultModel->searchResult(), $method));
				}
			}
		}
	}

	/**
	 *
	 * @param Components\Search\Models\Solr\SearchComponent $searchComponent
	 * @param mixed	$table
	 * @param mixed	$model
	 * @return mixed
	 */
	private function getSearchableModel($table, $model, $searchComponent)
	{
		$searchModel = Components\Search\Helpers\DiscoveryHelper::isSearchable($model);
		$indexResultModel = $model;
		if ($searchModel === false)
		{
			$searchModel = $searchComponent->getSearchableModel();
			if (!$searchModel)
			{
				return false;
			}
			$searchModelBlank = new $searchModel;
			$searchModelTable = $searchModelBlank->getTableName();
			if ($table != $searchModelTable)
			{
				return false;
			}
			$indexResultModel = $searchModel::newFromResults($model);
		}
		return $indexResultModel;
	}
}
