<?php
/**
 * HUBzero CMS
 *
 * Copyright 2005-2015 Purdue University. All rights reserved.
 *
 * This file is part of: The HUBzero(R) Platform for Scientific Collaboration
 *
 * The HUBzero(R) Platform for Scientific Collaboration (HUBzero) is free
 * software: you can redistribute it and/or modify it under the terms of
 * the GNU Lesser General Public License as published by the Free Software
 * Foundation, either version 3 of the License, or (at your option) any
 * later version.
 *
 * HUBzero is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU Lesser General Public License for more details.
 *
 * You should have received a copy of the GNU Lesser General Public License
 * along with this program.  If not, see <http://www.gnu.org/licenses/>.
 *
 * HUBzero is a registered trademark of Purdue University.
 *
 * @package   hubzero-cms
 * @author    Steve Snyder <snyder13@purdue.edu>
 * @copyright Copyright 2005-2015 Purdue University. All rights reserved.
 * @license   http://www.gnu.org/licenses/lgpl-3.0.html LGPLv3
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

if (!function_exists('stem'))
{
	/**
	 * Stem a string
	 *
	 * @param  string $str
	 * @return string
	 */
	function stem($str)
	{
		return $str;
	}
}

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

		$this->view->app     = \JFactory::getApplication();
		$this->view->terms   = $terms;
		$this->view->results = $results;
		$this->view->total   = $total;
		$this->view->plugin  = $plugin;
		$this->view->section = $section;

		$this->view->display();
	}
}

