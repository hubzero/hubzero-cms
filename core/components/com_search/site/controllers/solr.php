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
use Components\Tags\Models\Tag as Tag;
use Document;
use Pathway;
use Request;
use Plugin;
use Config;
use Lang;
use stdClass;

require_once \Component::path('com_search') . '/models/solr/facet.php';
require_once \Component::path('com_search') . '/helpers/urlqueryhelper.php';
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
		$terms = Request::getVar('terms', '');
		$limit = Request::getInt('limit', Config::get('list_limit'));
		$start = Request::getInt('start', 0);
		$sortBy = Request::getVar('sortBy', '');
		$sortDir = Request::getVar('sortDir', '');
		$type = Request::getInt('type', null);
		$section = Request::getVar('section', 'content');
		$tagString = Request::getVar('tags','');
		$tags = null;
		if ($tagString)
		{
			$tags = explode(",", $tagString);
			$tags = Tag::all()->whereIn('tag', $tags)->rows();
		}
		// Map coordinates
		if ($section == 'map')
		{
			$minLon = Request::getVar('minlon', false);
			$maxLon = Request::getVar('maxlon', false);
			$minLat = Request::getVar('minlat', false);
			$maxLat = Request::getVar('maxlat', false);

			if ($minLon && $maxLon && $minLat && $maxLat)
			{
				$locationFilter = 'coverage:"INTERSECTS(ENVELOPE(' . $minLon . ',' .  $maxLon. ',' . $maxLat . ',' . $minLat . '))"';
			}
		}

		// Add categories for Facet functions (mainly counting the different categories)
		$multifacet = $query->adapter->getFacetMultiQuery('hubtypes');
		$allFacets = Facet::all()
			->whereEquals('state', 1)
			->including('parentFacet')
			->rows();
		foreach ($allFacets as $facet)
		{
			$facetString = !User::authorise('core.admin') ? $facet->facet . ' AND (' . $query->adapter->getAccessString() . ')' : $facet->facet;
			$multifacet->createQuery($facet->getQueryName(), $facetString, array('exclude' => 'root_type', 'include' => 'child_type'));
		}

		$filters = Request::getVar('filters', array());
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
		$rootFacets = Facet::all()
			->including('children')
			->including('parentFacet')
			->whereEquals('state', 1)
			->whereEquals('parent_id', 0)
			->rows();

		// Apply the sorting
		if ($sortBy != '' && $sortDir != '')
		{
			$query = $query->sortBy($sortBy, $sortDir);
		}

		if ($type != null)
		{
			$facet = Facet::one($type);
			$query->addFilter('Type', $facet->facet, 'root_type');

			// Add a type
			$urlQuery .= '&type=' . $type;
		}
		else
		{
			$allfacets = array();
			foreach ($rootFacets as $facet)
			{
				$allfacets[] = $facet->facet;
			}

			if (!empty($allfacets))
			{
				$query->addFilter('Type', '(' . implode(' OR ', $allfacets) . ')', 'root_type');
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
			$this->view->facets = $rootFacets;
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

		$this->view->terms = $terms;
		$this->view->tags = $tagString;
		$this->view->childTerms = $childTerms;
		$this->view->childTermsString =  $tagParams;
		$this->view->type = $type;
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
				$snippet = \Hubzero\Utility\Str::excerpt($snippet, $terms, $radius = 200, $ellipsis = '…');
				$snippet = \Hubzero\Utility\Str::highlight($snippet, $terms, $highlightOptions);
				$result['snippet'] = trim($snippet);

				if (isset($result['author']))
				{
					$authorCnt = 1;
					$authorString = '';
					foreach ($result['author'] as $author)
					{
						if ($authorCnt < count($result['author']))
						{
							$authorString .= $author;
							$authorString .= ',';
						}
						else
						{
							$authorString .= $author;
						}
						$authorCnt++;
					}
					$result['authorString'] = $authorString;
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
