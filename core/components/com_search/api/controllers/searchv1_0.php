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
use Exception;
use stdClass;
use Request;
use Route;
use Lang;

use Hubzero\Search\Query;


/**
 * API controller class for blog entries
 */
class Searchv1_0 extends ApiController
{
	public function listTask()
	{

		$config = Component::params('com_search');
		$query = new \Hubzero\Search\Query($config);

		$terms = Request::getVar('terms', '');
		$limit = Request::getInt('limit', 10);
		$start = Request::getInt('start', 0);
		$sortBy = Request::getVar('sortBy', '');
		$sortDir = Request::getVar('sortDir', '');

		$filters = Request::getVar('filters', array());

		// Apply the limiting
		$query = $query->query($terms)->limit($limit)->start($start);

		// Apply the sorting
		if ($sortBy != '' && $sortDir != '')
		{
			$query = $query->sortBy($sortBy, $sortDir);
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

		$response = new stdClass;
		$response->results = $results;
		$response->total = $numFound;
		$response->success = true;

		$this->send($response);
	}

	public function suggestTask()
	{
		$terms = Request::getVar('terms', '');
		$suggest = array();

		if ($terms != '')
		{
			$config = Component::params('com_search');
			$query = new \Hubzero\Search\Query($config);
			$suggest = $query->getSuggestions($terms);
			ddie($suggest);
		}

		$response = new stdClass;
		$response->results = $suggest;
		$response->success = true;
		$this->send($response);
	}
}
