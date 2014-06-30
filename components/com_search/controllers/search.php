<?php
/**
 * HUBzero CMS
 *
 * Copyright 2005-2011 Purdue University. All rights reserved.
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
 * @copyright Copyright 2005-2011 Purdue University. All rights reserved.
 * @license   http://www.gnu.org/licenses/lgpl-3.0.html LGPLv3
 */

// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die( 'Restricted access' );

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

foreach (array('plugin', 'request', 'result_set', 'result_types', 'terms', 'authorization') as $mdl)
{
	require_once __DIR__ . '/../models/' . $mdl . '.php';
}

/**
 * Search controller class
 */
class SearchControllerSearch extends \Hubzero\Component\SiteController
{
	/**
	 * Display search form and results (if any)
	 *
	 * @return     void
	 */
	public function displayTask($cachable = false, $urlparams = false)
	{
		JPluginHelper::importPlugin('search');

		$app = JFactory::getApplication();

		// Set breadcrumbs
		$pathway = $app->getPathway();
		$pathway->addItem(
			'Search',
			'index.php?option=com_search'
		);

		$terms = new SearchModelTerms(JRequest::getString('terms'));

		// Set the document title
		JFactory::getDocument()->setTitle($terms->is_set() ? JText::sprintf('COM_SEARCH_RESULTS_FOR', $this->view->escape($terms->get_raw())) : 'Search');

		// Get search results
		$results = new SearchModelResultSet($terms);
		$results->set_limit($app->getUserStateFromRequest('global.list.limit', 'limit', $app->getCfg('list_limit'), 'int'));
		$results->set_offset(JRequest::getInt('limitstart', 0));
		$results->collect(JRequest::getBool('force-generic'));

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

		$this->view->pagination = new JPagination(
			$total,
			$results->get_offset(),
			$results->get_limit()
		);

		$this->view->app     = $app;
		$this->view->terms   = $terms;
		$this->view->results = $results;
		$this->view->plugin  = $plugin;
		$this->view->section = $section;

		$this->view->display();
	}
}

