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

namespace Components\Search\Admin\Controllers;

use Hubzero\Component\AdminController;
use Components\Search\Models\Solr\Blacklist;
use Components\Search\Models\Solr\Facet;
use Components\Search\Models\Solr\Filters\Filter;
use Components\Search\Models\Solr\Option;
use Components\Search\Models\Solr\SearchComponent;
use \Hubzero\Search\Query;
use \Hubzero\Search\Index;
use Components\Search\Helpers\SolrHelper;
use Components\Search\Helpers\DiscoveryHelper;
use Components\Developer\Models\Application;
use Hubzero\Access\Group as Accessgroup;
use stdClass;
use Hubzero\Utility\Inflector as Inflector;

require_once Component::path('com_search') . DS . 'helpers' . DS . 'solr.php';
require_once Component::path('com_search') . DS . 'models' . DS . 'solr' . DS . 'blacklist.php';
require_once Component::path('com_search') . DS . 'models' . DS . 'solr' . DS . 'searchcomponent.php';
require_once Component::path('com_search') . DS . 'models' . DS . 'solr' . DS . 'facet.php';
require_once Component::path('com_developer') . DS . 'models' . DS . 'application.php';

/**
 * Search AdminController Class
 */
class Searchable extends AdminController
{

	/**
	 * Display list of currently searchable components
	 * 
	 * @return  void
	 */
	public function displayTask()
	{
		$config = Component::params('com_search');
		$query = new \Hubzero\Search\Query($config);
		$multifacet = $query->adapter->getFacetMultiQuery('hubtypes');
		// Load the subfacets, if applicable
		$components = SearchComponent::all()->where('state', '!=', SearchComponent::STATE_TRASHED)->orWhere('state', 'IS', null)->rows();
		foreach ($components as $component)
		{
			$componentQuery = $component->getSearchQuery('hubtype');
			$multifacet->createQuery($component->getQueryName(), $componentQuery, array('include' => 'child_type'));
		}

		// Perform the query
		try
		{
			$query = $query->run();
		}
		catch (\Solarium\Exception\HttpException $e)
		{
			\Notify::warning(Lang::txt('COM_SEARCH_MALFORMED_QUERY'));
		}

		if (isset($query->resultsFacetSet) && $query->resultsFacetSet)
		{
			$facetResults = $query->resultsFacetSet->getFacet('hubtypes');
		}
		$facetCounts = array();
		if (!empty($facetResults))
		{
			foreach ($facetResults as $facet => $count)
			{
				$facetCounts[$facet] = $count;
			}
		}

		$this->view
			->set('components', $components)
			->set('componentCounts', $facetCounts)
			->display();
	}

	/**
	 * Task to begin indexing documents for the component
	 * 
	 * @return void
	 */
	public function activateIndexTask()
	{
		$ids = Request::getArray('id', array());
		$offset = Request::getInt('offset', 0);
		$numProcess = Request::getInt('numprocess');
		$components = SearchComponent::all()
			->whereIn('id', $ids)
			->rows();
		foreach ($components as $component)
		{
			$componentModel = $component->getSearchableModel();
			if (class_exists($componentModel))
			{
				$recordsIndexed = $component->indexSearchResults($offset);
			}
			else
			{
				$recordsIndexed = null;
			}
			if (!$recordsIndexed)
			{
				$component->set('state', SearchComponent::STATE_INDEXED);
				$componentLink = Route::url('index.php?option=com_search&controller=' . $this->_controller . '&task=documentListing&facet=hubtype:' . $component->getSearchNamespace());
				$recordsIndexed['state'] = 1;
				$recordsIndexed['total'] = '<a href="' . $componentLink . '">' . $component->getSearchCount() . '</a>';
				$recordsIndexed['link'] = Route::url('index.php?option=' . $this->_option . '&controller=searchable&task=deleteIndex&id=' . $component->get('id'), false);
			}
			elseif (isset($recordsIndexed['error']))
			{
				$error = $recordsIndexed['error'];
				Notify::error($error);
			}
			else
			{
				$component->set('indexed_records', $recordsIndexed['offset']);
				$recordsIndexed['state'] = 0;
				$recordsIndexed['numprocess'] = empty($numProcess) ? $component->getBatchSize() : $numProcess;
				$recordsIndexed['numprocess'] .= ' Batches';
			}
			$component->save();
			header('Content-Type: application/json');
			echo json_encode($recordsIndexed);
			exit();
		}

		App::redirect(Route::url('index.php?option=' . $this->_option . '&controller=searchable', false));
	}

	public function addTask()
	{
		$searchComponent = SearchComponent::blank();
		$this->editTask($searchComponent);
	}

	/**
	 * Edit a search category
	 * 
	 * @param   integer  $parentID
	 * @return  void
	 */
	public function editTask($obj = null)
	{
		$id = Request::getInt('id', 0);

		$searchComponent = is_object($obj) ? $obj : SearchComponent::oneOrFail($id);
		$fields = $searchComponent->getSearchableFields();
		$existingFields = $searchComponent->filters->fieldsByKey('field');
		$existingFields = array_filter($existingFields, 'strtolower');
		$availableFields = array_diff($fields, $existingFields);
		$filters = array();
		foreach ($searchComponent->filters()->order('ordering', 'ASC') as $filter)
		{
			$filters[$filter->field]['label'] = !empty($filter->get('label')) ? $filter->get('label') : $filter->field;
			$filters[$filter->field]['type'] = $filter->type;
			$params = $filter->params->toArray();
			if (!empty($params))
			{
				$filters[$filter->field]['params'] = $filter->params->toArray();
			}
			if ($filter->options->count() > 0)
			{
				$filters[$filter->field]['options'] = $filter->options()->order('ordering', 'ASC')->rows()->fieldsByKey('value');
			}
		}

		$this->view
			->setLayout('edit')
			->set('searchComponent', $searchComponent)
			->set('fields', array())
			->set('filters', $filters)
			->set('availableFields', $availableFields)
			->display();
	}

	/**
	 * saveTask 
	 * 
	 * @access public
	 * @return void
	 */
	public function saveTask()
	{
		Request::checkToken(["post", "get"]);

		$fields = Request::getArray('fields', array());
		$id     = Request::getInt('id', 0);
		$filters = Request::getArray('filters');
		$searchComponent = SearchComponent::oneOrNew($id);
		$name = $searchComponent->get('name');
		if (!$name)
		{
			$fields['name'] = !empty($fields['title']) ? $fields['title'] : '';
		}
		$searchComponent->set($fields);
		if (!$searchComponent->save())
		{
			Notify::error($searchComponent->getError());
			return $this->editTask($searchComponent);
		}
		else
		{
			Notify::success(Lang::txt('COM_SEARCH_COMPONENT_SAVE_SUCCESS', $searchComponent->title));
			$oldFilters = $searchComponent->filters;
			$oldFilterIds = array();
			foreach ($oldFilters as $filter)
			{
				$field = $filter->get('field');
				$oldFilterIds[$field] = $filter->get('id');
			}
			$filterCount = 1;
			foreach ($filters as $field => $element)
			{
				$filterId = isset($oldFilterIds[$field]) ? $oldFilterIds[$field] : 0;
				if ($filterId)
				{
					$oldFilters->drop($filterId);
				}
				$filter = Filter::oneOrNew($filterId);
				$filter->set('field', $field);
				$optionValues = array();
				if (isset($element['options']))
				{
					$optionValues = $element['options'];
					unset($element['options']);
				}
				$filter->set($element);
				$filter->set('ordering', $filterCount);
				$filter->set('component_id', $searchComponent->get('id'));
				if (!$filter->save())
				{
					Notify::error($filter->getError());
					continue;
				}
				$filterCount++;
				$oldOptions = $filter->options;
				$oldOptionIds = array();
				foreach ($oldOptions as $option)
				{
					$value = $option->get('value');
					$oldOptionIds[$value] = $option->get('id');
				}
				$optionsCount = 1;
				foreach ($optionValues as $value)
				{
					$optionId = isset($oldOptionIds[$value]) ? $oldOptionIds[$value] : 0;
					if ($optionId)
					{
						$oldOptions->drop($optionId);
					}
					$option = Option::oneOrNew($optionId);
					$option->set('filter_id', $filter->get('id'));
					$option->set('value', $value);
					$option->set('ordering', $optionsCount);
					if (!$option->save())
					{
						Notify::error($option->getError());
						continue;
					}
					$optionsCount++;
				}
				$oldOptions->destroyAll();
			}
			$oldFilters->destroyAll();
		}

		$return = Route::url('index.php?option=com_search&controller=searchable', false);

		App::redirect(
			Route::url($return, false)
		);
	}

	/**
	 * Removes all documents associated with the component
	 * 
	 * @return void
	 */
	public function deleteIndexTask()
	{
		$ids = Request::getArray('id', array());
		$components = SearchComponent::all()
			->whereIn('id', $ids)
			->whereEquals('state', SearchComponent::STATE_INDEXED)
			->rows();
		foreach ($components as $component)
		{
			$searchIndex = new \Hubzero\Search\Index($this->config);
			$componentSearchModel = $component->getSearchableModel();
			if (class_exists($componentSearchModel))
			{
				$modelNamespace = $componentSearchModel::searchNamespace();
				$deleteQuery = array('hubtype' => $modelNamespace);
				$searchIndex->delete($deleteQuery);
			}
			$component->set('state', SearchComponent::STATE_NOTINDEXED);
			$date = Date::of()->toSql();
			$component->set('indexed', null);
			$component->set('indexed_records', 0);
			if ($component->save())
			{
				Notify::success(Lang::txt('COM_SEARCH_DELETE_COMPONENT_SUCCESS', ucfirst($component->name)));
				$deleteObj = new stdClass;
				$deleteObj->hubtype = $modelNamespace;
				Event::trigger('search.sendSolrRequest', array($deleteObj, 'delete'));
			}
		}

		App::redirect(Route::url('index.php?option=' . $this->_option . '&controller=searchable', false));
	}

	/**
	 * Trash Component
	 * 
	 * @return void
	 */
	public function trashIndexTask()
	{
		$ids = Request::getArray('id', array());
		$components = SearchComponent::all()
			->whereIn('id', $ids)
			->rows();
		foreach ($components as $component)
		{
			$component->set('state', SearchComponent::STATE_TRASHED);
			if ($component->save())
			{
				Notify::success(Lang::txt('COM_SEARCH_TRASH_COMPONENT_SUCCESS', ucfirst($component->name)));
			}
		}
		App::redirect(Route::url('index.php?option=' . $this->_option . '&controller=searchable', false));
	}

	/**
	 * documentByTypeTask - view a type's records
	 * 
	 * @access public
	 * @return void
	 */
	public function documentListingTask()
	{
		$facet = Request::getVar('facet', '');
		$filter = Request::getVar('filter', '');
		$limitstart = Request::getInt('limitstart', 0);
		$limit = Request::getInt('limit', 10);

		// Display CMS errors
		foreach ($this->getErrors() as $error)
		{
			$this->view->setError($error);
		}

		// Get the blacklist to indidicate marked for removal
		$blacklistEntries = Blacklist::all()
			->select('doc_id')
			->rows()
			->toObject();

		// @TODO: PHP 5.5+ supports array_column()
		$blacklist = array();
		foreach ($blacklistEntries as $entry)
		{
			array_push($blacklist, $entry->doc_id);
		}

		// Temporary override to get all matching documents
		if ($filter == '')
		{
			$filter = '*:*';
		}

		// Instantitate and get all results for a particular document type
		try
		{
			$config = Component::params('com_search');
			$query = new \Hubzero\Search\Query($config);
			$results = $query->query($filter)
				->addFilter('facet', $facet)
				->limit($limit)->start($limitstart)->run()->getResults();

			// Get the total number of records
			$total = $query->getNumFound();
		}
		catch (\Solarium\Exception\HttpException $e)
		{
			App::redirect(
				Route::url('index.php?option=com_search&task=display', false)
			);
		}

		// Create the pagination
		$pagination = new \Hubzero\Pagination\Paginator($total, $limitstart, $limit);
		$pagination->setLimits(array('5','10','15','20','50','100','200'));
		$this->view->pagination = $pagination;

		// Pass the filters and documents to the display
		$this->view->filter = ($filter == '') || $filter = '*:*' ? '' : $filter;
		$this->view->facet = !isset($facet) ? '' : $facet;
		$this->view->documents = isset($results) ? $results : array();
		$this->view->blacklist = $blacklist;

		// Display the view
		$this->view->display();
	}

	/**
	 * Discover new components that are searchable
	 *
	 * @return void 
	 */
	public function discoverTask()
	{
		$componentModel = new \Components\Search\Models\Solr\SearchComponent();
		$components = $componentModel->getNewComponents();
		if ($components->count() > 0)
		{
			if ($components->save())
			{
				\Notify::success('New Searchable Components found');
			}
		}
		else
		{
			\Notify::warning('No new components found.');
		}

		App::redirect(
			Route::url('index.php?option=com_search&task=display&controller=searchable', false)
		);
	}
}
