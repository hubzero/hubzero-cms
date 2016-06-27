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
use Document;
use Pathway;
use Request;
use Plugin;
use Config;
use Lang;
use stdClass;

/**
 * Search controller class
 */
class Hubsearch extends SiteController
{
	/**
	 * Display search form and results (if any)
	 *
	 * @return  void
	 */
	public function displayTask($response = NULL)
	{
		$config = Component::params('com_search');
		$query = new \Hubzero\Search\Query($config);

		$terms = Request::getVar('terms', '');
		$limit = Request::getInt('limit', 10);
		$start = Request::getInt('start', 0);
		$sortBy = Request::getVar('sortBy', '');
		$sortDir = Request::getVar('sortDir', '');
		$type = Request::getVar('type', '');

		$filters = Request::getVar('filters', array());

		// Apply the sorting
		if ($sortBy != '' && $sortDir != '')
		{
			$query = $query->sortBy($sortBy, $sortDir);
		}

		if ($type != '')
		{
			$query->addFilter('Type', array('hubtype', '=', $type));
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

		// Perform the query
		$query = $query->run();
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
			$categories = $this->getCategories($type, $terms, $limit, $start);
			$this->view->categories = $categories['facets'];
			$this->view->catTotal = $categories['total'];
		}
		else
		{
			$this->view->queryString = '';
			$this->view->results = null; 
		}

		$this->view->terms = $terms;
		$this->view->total = $numFound;
		$this->view->type = $type;
		$this->view->setLayout('display');
		$this->view->display();
	}

	private function getCategories($type, $terms, $limit, $start)
	{
		$config = Component::params('com_search');
		$query = new \Hubzero\Search\Query($config);

		$types = Event::trigger('search.onGetTypes');
		foreach ($types as $type)
		{
			$query->addFacet($type, array('hubtype', '=', $type));
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

		$query = $query->run();
		$facets = array();
		$total = 0;
		foreach ($types as $type)
		{
			$name = $type;
			if (strpos($type, "-") !== false)
			{
				$name = substr($type, 0, strpos($type, "-"));
			}

			$count = $query->getFacetCount($type);
			$total += $count;


			$name = ucfirst(\Hubzero\Utility\Inflector::pluralize($name));
			array_push($facets, array('type'=> $type, 'name' => $name,'count' => $count));
		}

		return array('facets' => $facets, 'total' => $total);
	}

	private function formatResults($results, $terms)
	{
		$highlightOptions = array('format' =>'<b>\1</b>',
															'html' => false,
															'regex'  => "|%s|iu"
														);

		$snippetFields = array('description', 'fulltext', 'abstract');

		// Format the results for the view
		foreach ($results as &$result)
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

				// Highlight everything except the URL
				if ($field != 'url')
				{
					$r = \Hubzero\Utility\String::highlight($r, $terms, $highlightOptions);
				}

				// Generate the snippet
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
				$result['authorString'] = $authorString; 
			}
				
		} // End foreach results

		return $results;

	}
}

