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
use Document;
use Pathway;
use Request;
use Plugin;
use Config;
use Lang;
use stdClass;

require_once Component::path('com_search') . DS . 'models' . DS . 'solr' . DS .'facet.php';

/**
 * Search controller class
 */
class Solr extends SiteController
{
	/**
	 * Display search form and results (if any)
	 *
	 * @return  void
	 */
	public function displayTask($response = null)
	{
		$config = Component::params('com_search');
		$query = new \Hubzero\Search\Query($config);

		$terms = Request::getVar('terms', '');
		$limit = Request::getInt('limit', 10);
		$start = Request::getInt('start', 0);
		$sortBy = Request::getVar('sortBy', '');
		$sortDir = Request::getVar('sortDir', '');
		$type = Request::getInt('type', null);
		$section = Request::getVar('section', 'content');

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

		$filters = Request::getVar('filters', array());

		// To pass to the view
		$urlQuery = '?terms='.$terms;

		// Apply the sorting
		if ($sortBy != '' && $sortDir != '')
		{
			$query = $query->sortBy($sortBy, $sortDir);
		}

		if ($type != null)
		{
			$facet = Facet::all()->whereEquals('id', $type)->limit(1)->row();
			$query->addFilter('Type', $facet->facet);

			// Add a type
			$urlQuery .= '&type='.$type;
		}

		// Administrators can see all records
		$isAdmin = User::authorise('core.admin', 'com_users');
		if ($isAdmin)
		{
			$query = $query->query($terms)->limit($limit)->start($start);
		}
		else
		{
			$query = $query->query($terms)->limit($limit)->start($start)->restrictAccess();
		}

		if (isset($locationFilter))
		{
			$query->addFilter('BoundingBox', $locationFilter);
		}

		// Build the reset of the query string
		$urlQuery .= '&limit='.$limit;
		$urlQuery .= '&start='.$start;

		// Perform the query
		try
		{
			$query = $query->run();
		}
		catch (\Solarium\Exception\HttpException $e)
		{
			//@TODO: 'Did you mean' functionality.
			$query->query('')->limit($limit)->start($start)->run();
			\Notify::warning(Lang::txt('COM_SEARCH_MALFORMED_QUERY'));
		}

		$results = $query->getResults();
		$numFound = $query->getNumFound();

		// Format the results (highlighting, snippet, etc)
		$results = $this->formatResults($results, $terms);

		$this->view->pagination = new \Hubzero\Pagination\Paginator($numFound, $start, $limit);
		$this->view->pagination->setAdditionalUrlParam('terms', $terms);

		if (isset($results) && count($results) > 0)
		{
			$this->view->query = $terms;
			$this->view->results = $results;
			$this->view->facets = $this->getCategories($type, $terms, $limit, $start);

			$this->view->total = 0;
			foreach ($this->view->facets as $facet)
			{
				$this->view->total = $this->view->total + $facet->count;
			}
		}
		else
		{
			$this->view->queryString = '';
			$this->view->results = null;
		}

		$this->view->terms = $terms;
		$this->view->total = $numFound;
		$this->view->type = $type;
		$this->view->section = $section;
		$this->view->setLayout('display');
		$this->view->urlQuery = $urlQuery;
		$this->view->display();
	}

	private function getCategories($type, $terms, $limit, $start)
	{
		$config = Component::params('com_search');
		$query = new \Hubzero\Search\Query($config);

		$facets = Facet::all()->whereEquals('state', 1)->rows()->toObject();

		foreach ($facets as &$facet)
		{
			// Instantitate and get all results for a particular document type
			try
			{
				$config = Component::params('com_search');
				$query = new \Hubzero\Search\Query($config);
				$results = $query
					->query($facet->facet . ' AND ' . $terms)
					->limit($limit)
					->start($start)
					->restrictAccess()
					->run()->getResults();

				// Get the total number of records
				$total = $query->getNumFound();
				$facet->count = $total;
			}
			catch (\Solarium\Exception\HttpException $e)
			{
				$query->query('')->limit($limit)->start($start)->run();
				\Notify::warning(Lang::txt('COM_SEARCH_MALFORMED_QUERY'));
			}
		}
		return $facets;
	}

	private function formatResults($results, $terms)
	{
		$highlightOptions = array('format' =>'<strong>\1</strong>',
															'html' => false,
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

				// Appends http(s)://
				if (isset($result['url']))
				{
					$result['url']  = rtrim(Request::base(), "/") . $result['url'];
				}
				else
				{
					$result['url'] = '';
				}

				$snippet = '';
				foreach ($result as $field => &$r)
				{
					// Only work on strings
					if (is_string($r))
					{
						$r = strip_tags($r);
					}

					/** 
					 * Generate the snippet
					 * A snippet is the search result text which is displayed
					 **/
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
				$snippet  = \Hubzero\Utility\String::excerpt($snippet, $terms, $radius = 200, $ellipsis = '…');
				$snippet = \Hubzero\Utility\String::highlight($snippet, $terms, $highlightOptions);
				$result['snippet'] = $snippet;

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
					$result['authorString'] = $authorString; }
			}
			else
			{
				$result = $override;
			}
		} // End foreach results
			return $results;
	}
}
