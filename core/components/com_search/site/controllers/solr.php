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
use Components\Search\Helpers\SolrEngine as SearchEngine;
use Components\Search\Models\Hubtype;

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
	public function displayTask($response = NULL)
	{
		if (isset($response))
		{
			$this->view->query = $response->search;
			$this->view->queryString = $response->queryString;
			$this->view->results = $response->results;
		}
		else
		{
			$this->view->queryString = '';
			$this->view->results = '';
		}

		$this->view->types = Hubtype::all()->order('type', 'DESC')->rows()->toObject();
		$this->view->setLayout('display');
		$this->view->display();
	}

	public function searchTask()
	{
		$searchRequest = Request::getVar('search', array());
		$query = $searchRequest['query'];
		$HubtypeFilter = $searchRequest['type'];

		$hubSearch = new SearchEngine();

		if ($HubtypeFilter != '')
		{
			//$search = $hubSearch->createFilterQuery('hubtype')->setQuery('hubtype:'.$HubtypeFilter)->search($query)->limit(100);
			$search = $hubSearch->search($query)->limit(100);
		}
		else
		{
			$search = $hubSearch->search($query)->limit(100);
		}

		$result = $search->getResult();

		$response = new stdClass;
		$response->results = $result;
		$response->search = $search;
		$response->queryString = $query;

		return $this->displayTask($response);
	}
}

