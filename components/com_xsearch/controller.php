<?php
/**
 * @package     hubzero-cms
 * @author      Shawn Rice <zooley@purdue.edu>
 * @copyright   Copyright 2005-2011 Purdue University. All rights reserved.
 * @license     http://www.gnu.org/licenses/lgpl-3.0.html LGPLv3
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
 */

// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die( 'Restricted access' );

ximport('Hubzero_Controller');

class XSearchController extends Hubzero_Controller
{
	public function execute()
	{
		$this->_stemming = 1;

		// Set the page title
		$document =& JFactory::getDocument();
		$document->setTitle( JText::_('COM_XSEARCH_TITLE') );

		// Set the pathway
		$app =& JFactory::getApplication();
		$pathway =& $app->getPathway();
		if (count($pathway->getPathWay()) <= 0) {
			$pathway->addItem(JText::_('COM_XSEARCH_TITLE'),'index.php?option=com_search');
		}

		// Add some needed CSS and JS to the template
		ximport('Hubzero_Document');
		Hubzero_Document::addComponentStylesheet($this->_option);

		// Push some JS to the tmeplate
		$document->addScript('components'.DS.$this->_option.DS.$this->_name.'.js');

		// Get the search string
		$keyword = urldecode(JRequest::getString('searchword'));
		$keyword = trim($keyword);

		// Do we have a search string?
		if (!$keyword) {
			$view = new JView( array('name'=>'nokeyword') );
			$view->title = JText::_('COM_XSEARCH_TITLE');
			$view->option = 'com_search';
			if ($this->getError()) {
				$view->setError( $this->getError() );
			}
			$view->display();
			return;
		}

		// Get configuration
		$config = JFactory::getConfig();

		// Get the pagination request variables
		$limit = JRequest::getInt('limit', $config->getValue('config.list_limit'));
		$limitstart = JRequest::getInt('limitstart', 0);

		// Get categories
		$areas = $this->_getAreas();

		// Was there a category passed in the querystring?
		$area = trim(JRequest::getWord('category', ''));

		// Check the search string for a category prefix
		if ($keyword != NULL) {
			$searchstring = strtolower($keyword);
			foreach ($areas as $c=>$t)
			{
				$regexp = "/" . $c . ":/";
		    	if (strpos($searchstring, $c . ":") !== false) {
					// We found an active category
					// NOTE: this will override any category sent in the querystring
					$area = $c;
					// Strip it off the search string
    		    	$searchstring = preg_replace($regexp, "", $searchstring);
					break;
				}
				// Does the category contain sub-categories?
				if (is_array($t) && !empty($t)) {
					// It does - loop through them and perform the same check
					foreach ($t as $sc=>$st)
					{
						$regexp = "/" . $sc . ":/";
				    	if (strpos($searchstring, $sc . ":") !== false) {
							// We found an active category
							// NOTE: this will override any category sent in the querystring
							$area = $sc;
							// Strip it off the search string
		    		    	$searchstring = preg_replace($regexp, "", $searchstring);
							break;
						}
					}
				}
			}
			$keyword = trim( $searchstring );
		}

		// Get the active category
		if ($area) {
			$activeareas = array($area);
		} else {
			//$limit = 5;
			$activeareas = $areas;
		}

		// Get XSearch plugins
		JPluginHelper::importPlugin( 'xsearch' );
		$dispatcher =& JDispatcher::getInstance();

		// Process the keyword for exact phrase matches, etc.
		$searchquery = new XSearchPhrase( $keyword, $this->_stemming );
		$searchquery->process();

		// Get the search result totals
		$totals = $dispatcher->trigger( 'onXSearch', array(
				$searchquery,
				0,
				0,
				$activeareas)
			);

		$limit = ($limit == 0) ? 'all' : $limit;

		// Get the search results
		if (count($activeareas) > 1) {
			$sqls = $dispatcher->trigger( 'onXSearch', array(
					$searchquery,
					'all',
					$limitstart,
					$activeareas)
				);
			if ($sqls) {
				$s = array();
				foreach ($sqls as $sql)
				{
					if (trim($sql) != '') {
						$s[] = $sql;
					}
				}
				$query  = "(";
				$query .= implode(") UNION (", $s);
				$query .= ") ORDER BY relevance DESC";
				$query .= ($limit != 'all' && $limit > 0) ? " LIMIT $limitstart, $limit" : "";
			}
			$this->database->setQuery( $query );
			$results = array($this->database->loadObjectList());
		} else {
			$results = $dispatcher->trigger( 'onXSearch', array(
					$searchquery,
					$limit,
					$limitstart,
					$activeareas)
				);
		}

		// Highlight the search word in the text
		$results = $this->_highlight( $searchquery, $results );

		// Get the total results found (sum of all categories)
		$i = 0;
		$total = 0;

		foreach ($areas as $c=>$t)
		{
			$cats[$i]['category'] = $c;

			// Do sub-categories exist?
			if (is_array($t) && !empty($t)) {
				// They do - do some processing
				$cats[$i]['title'] = ucfirst($c);
				$cats[$i]['total'] = 0;
				$cats[$i]['_sub'] = array();
				$z = 0;
				// Loop through each sub-category
				foreach ($t as $s=>$st)
				{
					// Ensure a matching array of totals exist
					if (is_array($totals[$i]) && !empty($totals[$i]) && isset($totals[$i][$z])) {
						// Add to the parent category's total
						$cats[$i]['total'] = $cats[$i]['total'] + $totals[$i][$z];
						// Get some info for each sub-category
						$cats[$i]['_sub'][$z]['category'] = $s;
						$cats[$i]['_sub'][$z]['title'] = $st;
						$cats[$i]['_sub'][$z]['total'] = $totals[$i][$z];
					}
					$z++;
				}
			} else {
				// No sub-categories - this should be easy
				$cats[$i]['title'] = $t;
				$cats[$i]['total'] = (!is_array($totals[$i])) ? $totals[$i] : 0;
			}

			// Add to the overall total
			$total = $total + intval($cats[$i]['total']);
			$i++;
		}

		// Do we have an active area?
		if (count($activeareas) == 1) {
			$active = $activeareas[0];
		} else {
			$active = 'all';
		}

		// Output HTML
		$view = new JView( array('name'=>'results') );
		$view->title = JText::_('COM_XSEARCH_TITLE');
		$view->option = 'com_search';
		$view->keyword = $keyword;
		$view->totals = $totals;
		$view->total = $total;
		$view->results = $results;
		$view->cats = $cats;
		$view->active = $active;
		$view->start = $limitstart;
		$view->limit = $limit;
		if ($this->getError()) {
			$view->setError( $this->getError() );
		}
		$view->display();
	}

	private function _highlight( $searchquery, $results )
	{
		// Get all the search words and phrases to highlight
		$toks = $searchquery->searchTokens;
		$words = array();
		if (count($toks) > 0) {
			foreach ($toks as $w)
			{
				if (strlen($w) > 2) {
					$words[] = $w;
				}
			}
		}
		$toks = $words;

		$resultback = 60;
		$resultlen  = 200;

		// Loop through all results
		for ($i = 0, $n = count($results); $i < $n; $i++)
		{
			for ($k=0; $k < count($results[$i]); $k++)
			{
				$row =& $results[$i][$k];

				// Clean the text up a bit first
				$row->itext = Hubzero_View_Helper_Html::purifyText( $row->itext );
				$lowerrow = strtolower( $row->itext );

				// Find first occurrence of a search word
				foreach ($toks as $tok)
				{
					$pos = strpos( $lowerrow, $tok );
					if ($pos !== false) break;
				}

				if ($pos > $resultback) {
					$row->itext = substr( $row->itext, ($pos - $resultback), $resultlen );
				} else {
					$row->itext = substr( $row->itext, 0, $resultlen );
				}

				// Highlight each word/phrase found
				foreach ($toks as $tok)
				{
					if (($tok == 'class') || ($tok == 'span') || ($tok == 'highlight')) {
						continue;
					}
					$row->itext = eregi_replace( $tok, "<span class=\"highlight\">\\0</span>", $row->itext);
					$row->title = eregi_replace( $tok, "<span class=\"highlight\">\\0</span>", $row->title);
				}

				$row->itext = trim($row->itext);
			}
		}

		return $results;
	}

	private function _getAreas()
	{
		// Do we already have an array of areas?
		if (!isset($this->searchareas) || empty($this->searchareas)) {
			// No - so we'll need to get it

			$areas = array();

			// Load the XSearch plugins
			JPluginHelper::importPlugin( 'xsearch' );
			$dispatcher =& JDispatcher::getInstance();

			// Trigger the functions that return the areas we'll be searching
			$searchareas = $dispatcher->trigger( 'onXSearchAreas' );

			// Build an array of the areas
			foreach ($searchareas as $area)
			{
				$areas = array_merge( $areas, $area );
			}

			// Save the array for use elsewhere
			$this->searchareas = $areas;
		}

		// Return the array
		return $this->searchareas;
	}
}

