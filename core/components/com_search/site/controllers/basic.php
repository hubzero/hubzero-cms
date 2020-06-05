<?php
/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
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
