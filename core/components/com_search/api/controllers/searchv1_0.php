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

namespace Components\Search\Api\Controllers;

use Hubzero\Component\ApiController;
use Hubzero\Utility\Inflector;
use Hubzero\Utility\String;
use Hubzero\Search\Query;
use Component;
use stdClass;
use Request;
use Event;
use User;

/**
 * API controller class for search
 */
class Searchv1_0 extends ApiController
{
	/**
	 * Display a list of entries
	 *
	 * @apiMethod GET
	 * @apiUri    /search/list
	 * @apiParameter {
	 * 		"name":          "type",
	 * 		"description":   "Content type (groups, members, etc.)",
	 * 		"type":          "string",
	 * 		"required":      false,
	 * 		"default":       ""
	 * }
	 * @apiParameter {
	 * 		"name":          "limit",
	 * 		"description":   "Number of result to return.",
	 * 		"type":          "integer",
	 * 		"required":      false,
	 * 		"default":       10
	 * }
	 * @apiParameter {
	 * 		"name":          "start",
	 * 		"description":   "Number of where to start returning results.",
	 * 		"type":          "integer",
	 * 		"required":      false,
	 * 		"default":       0
	 * }
	 * @apiParameter {
	 * 		"name":          "terms",
	 * 		"description":   "Terms to search for.",
	 * 		"type":          "string",
	 * 		"required":      true,
	 * 		"default":       "*:*"
	 * }
	 * @apiParameter {
	 * 		"name":          "sortBy",
	 * 		"description":   "Field to sort results by.",
	 * 		"type":          "string",
	 * 		"required":      false,
	 *      "default":       "",
	 * }
	 * @apiParameter {
	 * 		"name":          "sortDir",
	 * 		"description":   "Direction to sort results by.",
	 * 		"type":          "string",
	 * 		"required":      false,
	 * 		"default":       "",
	 * }
	 * @apiParameter {
	 * 		"name":          "filters",
	 * 		"description":   "Filters to apply to results.",
	 * 		"type":          "array",
	 * 		"required":      false,
	 * 		"default":       "[]",
	 * }
	 * @return  void
	 */
	public function listTask()
	{
		$config = Component::params('com_search');
		$query = new Query($config);

		$terms   = Request::getVar('terms', '*:*');
		$limit   = Request::getInt('limit', 10);
		$start   = Request::getInt('start', 0);
		$sortBy  = Request::getVar('sortBy', '');
		$sortDir = Request::getVar('sortDir', '');
		$type    = Request::getVar('type', '');
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

		$highlightOptions = array(
			'format' =>'<span class="highlight">\1</span>',
			'html'   => false,
			'regex'  => "|%s|iu"
		);

		foreach ($results as &$result)
		{
			$snippet = '';
			foreach ($result as $field => &$r)
			{
				if (is_string($r))
				{
					$r = strip_tags($r);
				}

				if ($field != 'url')
				{
					$r = String::highlight($r, $terms, $highlightOptions);
				}

				if ($field == 'description' || $field == 'fulltext' || $field == 'abstract')
				{
					if (isset($result['description']) && $result['description'] != $result['fulltext'])
					{
						$snippet .= $r;
					}
				}
			}

			$snippet = str_replace("\n", "", $snippet);
			$snippet = str_replace("\r", "", $snippet);
			$snippet  = String::excerpt($snippet, $terms, $radius = 200, $ellipsis = '…');

			$result['snippet'] = $snippet;
		}

		$response = new stdClass;
		$response->results = $results;
		$response->total = $numFound;
		$response->showing = count($results);
		$response->success = true;

		$this->send($response);
	}

	/**
	 * Display a list of suggestions for a term
	 *
	 * @apiMethod GET
	 * @apiUri    /search/suggest
	 * @apiParameter {
	 * 		"name":          "terms",
	 * 		"description":   "Terms to get suggestions for.",
	 * 		"type":          "string",
	 * 		"required":      true,
	 * 		"default":       ""
	 * }
	 * @return  void
	 */
	public function suggestTask()
	{
		$terms = Request::getVar('terms', '');
		$suggest = array();

		if ($terms != '')
		{
			$config = Component::params('com_search');
			$query = new \Hubzero\Search\Query($config);
			$suggest = $query->getSuggestions($terms);
		}

		$response = new stdClass;
		$response->results = $suggest;
		$response->success = true;

		$this->send($response);
	}

	/**
	 * Display a list of hub types for a term
	 *
	 * @apiMethod GET
	 * @apiUri    /search/getHubTypes
	 * 		"name":          "type",
	 * 		"description":   "Content type (groups, members, etc.)",
	 * 		"type":          "string",
	 * 		"required":      false,
	 * 		"default":       ""
	 * }
	 * @apiParameter {
	 * 		"name":          "terms",
	 * 		"description":   "Terms to search for.",
	 * 		"type":          "string",
	 * 		"required":      true,
	 * 		"default":       "*:*"
	 * }
	 * @return  void
	 */
	public function getHubTypesTask()
	{
		$config = Component::params('com_search');
		$query = new Query($config);

		$terms = Request::getVar('terms','*:*');
		$type  = Request::getVar('type', '');
		$limit = 0;
		$start = 0;

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


			$name = ucfirst(Inflector::pluralize($name));
			array_push($facets, array('type'=> $type, 'name' => $name,'count' => $count));
		}

		$response = new stdClass;
		$response->results = json_encode($facets);
		$response->total = $total;
		$response->success = true;

		$this->send($response);
	}
}
