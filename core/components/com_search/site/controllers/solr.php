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

namespace Components\Search\Site\Controllers;

use Hubzero\Component\SiteController;
use Components\Search\Models\Solr\Facet;
use Components\Search\Models\Solr\SearchComponent;
use Components\Tags\Models\Tag as Tag;
use Document;
use Pathway;
use Request;
use Plugin;
use Config;
use Lang;
use stdClass;
use Components\Resources\Models\Entry;

require_once \Component::path('com_search') . '/models/solr/facet.php';
require_once \Component::path('com_search') . '/models/solr/searchcomponent.php';
require_once \Component::path('com_search') . '/helpers/urlqueryhelper.php';
require_once \Component::path('com_resources') . '/models/entry.php';
require_once \Component::path('com_tags') . '/models/tag.php';

/**
 * Search controller class
 */
class Solr extends SiteController
{
	/**
	 * Display search form and results (if any)
	 *
	 * @param   unknown  $response
	 * @return  void
	 */
	public function displayTask($response = null)
	{
		$config = Component::params('com_search');
		$query = new \Hubzero\Search\Query($config);

		$childTerms = Request::getArray('childTerms', array());
		$terms = Request::getString('terms', '');
		$limit = Request::getInt('limit', Config::get('list_limit'));
		$start = Request::getInt('start', 0);
		$sortBy = Request::getString('sortBy', '');
		$sortDir = Request::getString('sortDir', '');
		$type = Request::getInt('type', null);
		$section = Request::getString('section', 'content');
		$tagString = Request::getString('tags', '');
		$filters = Request::getArray('filters', '');
		$tags = null;
		if ($tagString)
		{
			$tagsquery = Tag::all();
			$tags = explode(',', $tagString);
			foreach ($tags as $k => $t)
			{
				$tags[$k] = $tagsquery->normalize($t);
			}
			$tagString = implode(',', $tags);
			$tags = $tagsquery->whereIn('tag', $tags)->rows();
		}
		// Map coordinates
		if ($section == 'map')
		{
			$minLon = Request::getString('minlon', false);
			$maxLon = Request::getString('maxlon', false);
			$minLat = Request::getString('minlat', false);
			$maxLat = Request::getString('maxlat', false);

			if ($minLon && $maxLon && $minLat && $maxLat)
			{
				$locationFilter = 'coverage:"INTERSECTS(ENVELOPE(' . $minLon . ',' .  $maxLon. ',' . $maxLat . ',' . $minLat . '))"';
			}
		}

		$searchComponents = SearchComponent::all()
			->whereEquals('state', 1);
		// Add categories for Facet functions (mainly counting the different categories)
		$multifacet = $query->adapter->getFacetMultiQuery('hubtypes');
		$allFacets = Facet::all()
			->whereEquals('state', 1)
			->including('parentFacet')
			->rows();
		foreach ($searchComponents as $searchComponent)
		{
			$hubType = 'hubtype:' . $searchComponent->getSearchNamespace();
			$multifacet->createQuery($searchComponent->getQueryName(), $hubType, array('exclude' => 'filter_type', 'include' => 'child_type'));
		}

		$queryTerms = $terms;
		if ($tags && $tags->count() > 0)
		{
			foreach ($tags as $tag)
			{
				// This string tells Solr to filter the parents out based on childTerm
				$queryTerms .= ' +{!parent which=hubtype:*}' . 'id:tag-' . $tag->id;
			}
		}
		$tagParams = '';
		if (!empty($tagString))
		{
			$tagParams = '&tags=' . $tagString;
		}
		$urlQuery = '?terms=' . $terms . $tagParams;

		// Apply the sorting
		if ($sortBy != '' && $sortDir != '')
		{
			$query = $query->sortBy($sortBy, $sortDir);
		}
		$typeComponent = null;
		if ($type != null)
		{
			$typeComponent = SearchComponent::one($type);
			foreach ($typeComponent->filters as $filter)
			{
				if (method_exists($filter, 'addCounts'))
				{
					$filter->addCounts($multifacet);
				}

				if (method_exists($filter, 'applyFilters') && !empty(array_filter($filters)))
				{
					$filter->applyFilters($query, $filters);
				}
			}
			$hubType = 'hubtype:' . $typeComponent->getSearchNamespace();
			$query->addFilter('Type', $hubType, 'root_type');
			// Add a type
			$urlQuery .= '&type=' . $type;
		}
		else
		{
			$hubTypes = array();
			foreach ($searchComponents as $component)
			{
				$hubTypes[] = $component->getSearchNamespace();
			}

			if (!empty($hubTypes))
			{
				$query->addFilter('Type', '(hubtype:(' . implode(' OR ', $hubTypes) . '))', 'root_type');
			}
		}


		$query->query($queryTerms)->limit($limit)->start($start);
		$childFilter = '[child parentFilter=hubtype:*';
		// Administrators can see all records
		if (!User::authorise('core.admin'))
		{
			$childFilter .= ' childFilter=access_level:public';
			$query->restrictAccess();
		}
		$childFilter .= ']';

		if (isset($locationFilter))
		{
			$query->addFilter('BoundingBox', $locationFilter, 'root_type');
		}
		$query->fields(array('*', $childFilter));

		// Build the reset of the query string
		$urlQuery .= '&limit=' . $limit;
		$urlQuery .= '&start=' . $start;

		// Perform the query
		try
		{
			$query = $query->run();
		}
		catch (\Solarium\Exception\HttpException $e)
		{
			$query->query('')->limit($limit)->start($start)->run();
			\Notify::warning(Lang::txt('COM_SEARCH_MALFORMED_QUERY'));
		}

		$results  = $query->getResults();
		$numFound = $query->getNumFound();
		$facetResult = array();
		if (isset($query->resultsFacetSet) && $query->resultsFacetSet)
		{
			$facetResult = $query->resultsFacetSet->getFacet('hubtypes');
		}
		$facetCounts = array();
		foreach ($facetResult as $facet => $count)
		{
			$facetCounts[$facet] = $count;
		}
		// Format the results (highlighting, snippet, etc)
		$results = $this->formatResults($results, $terms);

		// 'Did you mean' functionality.
		if ($terms != '' && $numFound == 0)
		{
			// Get MoreLikeThis results
		}

		$this->view->pagination = new \Hubzero\Pagination\Paginator($numFound, $start, $limit);
		$this->view->pagination->setAdditionalUrlParam('terms', $terms);
		$this->view->pagination->setAdditionalUrlParam('type', $type);
		foreach ($childTerms as $index => $child)
		{
			$this->view->pagination->setAdditionalUrlParam('tags', $tagString);
			$this->view->pagination->setAdditionalUrlParam('tags', $child['title']);
		}

		if (isset($results) && count($results) > 0)
		{
			$this->view->query = $terms;
			$this->view->results = $results;
			$this->view->facets = $searchComponents;
			$this->view->facetCounts = $facetCounts;
			$this->view->total = 0;
			foreach ($this->view->facets as $facet)
			{
				$facetIndex = $facet->getQueryName();
				$this->view->total = $this->view->total + $facetCounts[$facetIndex];
			}
		}
		else
		{
			$this->view->queryString = '';
			$this->view->results = null;
		}

		// Set breadcrumbs
		\Pathway::append(
			Lang::txt('COM_SEARCH'),
			'index.php?option=' . $this->_option
		);

		// Set the document title
		\Document::setTitle($terms ? Lang::txt('COM_SEARCH_RESULTS_FOR', $this->view->escape($terms)) : Lang::txt('COM_SEARCH'));
		$viewOverrides = array();
		foreach ($searchComponents as $component)
		{
			if (!$viewOverride = $component->getViewOverride())
			{
				continue;
			}
			$name = \Hubzero\Utility\Inflector::singularize(strtolower($component->get('name')));
			$viewOverrides[$name] = $viewOverride;
		}

		$this->view->terms = $terms;
		$this->view->tags = $tagString;
		$this->view->childTerms = $childTerms;
		$this->view->childTermsString =  $tagParams;
		$this->view->type = $type;
		$this->view->filters = $filters;
		$this->view->searchComponent = $typeComponent;
		$this->view->viewOverrides = $viewOverrides;
		$this->view->section = $section;
		$this->view->setLayout('display');
		$this->view->urlQuery = $urlQuery;
		$this->view->display();
	}


	/**
	 * Format the results
	 *
	 * @param   array  $results
	 * @param   array  $terms
	 * @return  array
	 */
	private function formatResults($results, $terms)
	{
		$highlightOptions = array(
			'format' =>'<strong>\1</strong>',
			'html'   => false,
			'regex'  => "|%s|iu"
		);

		$snippetFields = array('description', 'fulltext', 'abstract');

		// Format the results for the view
		foreach ($results as &$result)
		{
			// Event for special formatting
			$override = Event::trigger('search.onFormatResult', array($result['hubtype'], &$result, $terms, $highlightOptions));

			// Only allow one override per result 
			if (count($override) == 1)
			{
				$override = $override[0];
			}

			if (empty($override))
			{
				//@FIXME: SOLR-specific
				$result['title'] = $result['title'][0];
				$snippet = '';
				foreach ($result as $field => &$r)
				{
					// Only work on strings
					if (is_string($r))
					{
						$r = strip_tags($r);
					}

					// Generate the snippet
					// A snippet is the search result text which is displayed
					if (in_array($field, $snippetFields))
					{
						$snippet .= $r . " ";
					}
				}

				// Do some filtering 
				$snippet = str_replace("\n", '', $snippet);
				$snippet = str_replace("\r", '', $snippet);
				$snippet = str_replace("<br/>", '', $snippet);
				$snippet = str_replace("<br>", '', $snippet);
				$snippet = \Hubzero\Utility\Str::excerpt($snippet, $terms, $radius = 500, $ellipsis = '…');
				$snippet = \Hubzero\Utility\Str::highlight($snippet, $terms, $highlightOptions);
				$result['snippet'] = trim($snippet);

				if (!empty($result['author']))
				{
					$result['authorString'] = implode(', ', $result['author']);
				}
			}
			else
			{
				$result = $override;
			}
		} // End foreach results

		return $results;
	}
}
