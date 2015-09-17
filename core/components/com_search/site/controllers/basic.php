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
 * @author    Steve Snyder <snyder13@purdue.edu>
 * @copyright Copyright 2005-2015 HUBzero Foundation, LLC.
 * @license   http://opensource.org/licenses/MIT MIT
 */

namespace Components\Search\Site\Controllers;

use Hubzero\Component\SiteController;
use Components\Search\Models\Basic\Result\Set;
use Components\Search\Models\Basic\Terms;
use Document;
use Pathway;
use Request;
use Plugin;
use Config;
use Lang;

require_once dirname(dirname(__DIR__)) . DS . 'helpers' . DS . 'basic.php';

foreach (array('request', 'result', 'terms', 'authorization', 'documentmetadata') as $mdl)
{
	require_once dirname(dirname(__DIR__)) . DS . 'models' . DS . 'basic' . DS . $mdl . '.php';
}
foreach (array('assoc', 'assoclist', 'assocscalar', 'blank', 'set', 'sql') as $mdl)
{
	require_once dirname(dirname(__DIR__)) . DS . 'models' . DS . 'basic' . DS . 'result' . DS . $mdl . '.php';
}

/**
 * Search controller class
 */
class Basic extends SiteController
{
	/**
	 * Display search form and results (if any)
	 *
	 * @return  void
	 */
	public function displayTask()
	{
		Plugin::import('search');

		// Set breadcrumbs
		Pathway::append(
			Lang::txt('COM_SEARCH'),
			'index.php?option=com_search'
		);

		$terms = new Terms(Request::getString('terms'));

		// Set the document title
		Document::setTitle($terms->is_set() ? Lang::txt('COM_SEARCH_RESULTS_FOR', $this->view->escape($terms->get_raw())) : 'Search');

		// Get search results
		$results = new Set($terms);
		$results->set_limit(Request::getState('global.list.limit', 'limit', Config::get('list_limit'), 'int'));
		$results->set_offset(Request::getInt('limitstart', 0));
		$results->collect(Request::getBool('force-generic'));

		$this->view->url_terms = urlencode($terms->get_raw_without_section());
		@list($plugin, $section) = $terms->get_section();

		if ($plugin)
		{
			foreach ($results->get_result_counts() as $cat => $def)
			{
				if ($plugin == $cat)
				{
					$total = $def['count'];
					break;
				}
			}
		}
		else
		{
			$total = $results->get_total_count();
		}

		$this->view->terms   = $terms;
		$this->view->results = $results;
		$this->view->total   = $total;
		$this->view->plugin  = $plugin;
		$this->view->section = $section;

		$this->view->display();
	}
}

