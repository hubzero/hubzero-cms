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

class WhatsnewController extends Hubzero_Controller
{
	public function execute()
	{
		$this->_task = JRequest::getVar( 'task', '' );

		switch ($this->_task)
		{
			case 'browse':   $this->browse(); break;
			case 'feed.rss': $this->feed();   break;
			case 'feed':     $this->feed();   break;

			default: $this->browse(); break;
		}
	}

	public function browse()
	{
		// Determine if user has admin privledges
		$authorized = $this->_authorize();

		// Incoming
		$period = JRequest::getVar( 'period', 'month' );

		// Paging variables
		$start = JRequest::getInt( 'limitstart', 0 );
		$limit = JRequest::getInt( 'limit', 25 );

		// Get some needed CSS and JS
		$this->_getStyles();

		// Get categories
		$areas = $this->_getAreas();

		// Was there a category passed in the querystring?
		$area = trim(JRequest::getWord('category', ''));

		// Check the search string for a category prefix
		if ($period != NULL) {
			$searchstring = strtolower($period);
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
			$period = trim( $searchstring );
		}

		// Get the active category
		if ($area) {
			$activeareas = array($area);
		} else {
			$limit = 5;
			$activeareas = $areas;
		}

		// Load plugins
		JPluginHelper::importPlugin( 'whatsnew' );
		$dispatcher =& JDispatcher::getInstance();

		// Process the keyword for exact phrase matches, etc.
		$p = new WhatsnewPeriod( $period );
		$p->process();

		// Get the search result totals
		$totals = $dispatcher->trigger( 'onWhatsnew', array(
				$p,
				0,
				0,
				$activeareas)
			);

		$limit = ($limit == 0) ? 'all' : $limit;

		// Get the search results
		$results = $dispatcher->trigger( 'onWhatsnew', array(
				$p,
				$limit,
				$start,
				$activeareas)
			);

		// Get the total results found (sum of all categories)
		$i = 0;
		$total = 0;
		$cats = array();
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
			$active = '';
		}

		// Set the page title
		$document =& JFactory::getDocument();
		$document->setTitle( JText::_(strtoupper($this->_option)).': '.$this->_jtext($period) );

		// Set the pathway
		$app =& JFactory::getApplication();
		$pathway =& $app->getPathway();
		if (count($pathway->getPathWay()) <= 0) {
			$pathway->addItem(JText::_(strtoupper($this->_option)),'index.php?option='.$this->_option);
		}
		$pathway->addItem($this->_jtext($period),'index.php?option='.$this->_option.'&period='.$period);

		// Build some options for the time period <select>
		$periodlist = array();
		$periodlist[] = JHTMLSelect::option('week',JText::_('COM_WHATSNEW_OPT_WEEK'));
		$periodlist[] = JHTMLSelect::option('month',JText::_('COM_WHATSNEW_OPT_MONTH'));
		$periodlist[] = JHTMLSelect::option('quarter',JText::_('COM_WHATSNEW_OPT_QUARTER'));
		$periodlist[] = JHTMLSelect::option('year',JText::_('COM_WHATSNEW_OPT_YEAR'));

		$thisyear = strftime("%Y",time());
		for ($y = $thisyear; $y >= 2002; $y--)
		{
			if (time() >= strtotime('10/1/'.$y)) {
				$periodlist[] = JHTMLSelect::option($y, JText::_('COM_WHATSNEW_OPT_FISCAL_YEAR').' '.$y);
			}
		}
		for ($y = $thisyear; $y >= 2002; $y--)
		{
			if (time() >= strtotime('01/01/'.$y)) {
				$periodlist[] = JHTMLSelect::option('c_'.$y, JText::_('COM_WHATSNEW_OPT_CALENDAR_YEAR').' '.$y);
			}
		}

		$view = new JView( array('name'=>'results') );
		$view->title = JText::_(strtoupper($this->_option)).': '.$this->_jtext($period);
		$view->option = $this->_option;
		$view->period = $period;
		$view->periodlist = $periodlist;
		$view->totals = $totals;
		$view->total = $total;
		$view->results = $results;
		$view->cats = $cats;
		$view->active = $active;
		$view->start = $start;
		$view->limit = $limit;
		if ($this->getError()) {
			$view->setError( $this->getError() );
		}
		$view->display();
	}

	protected function feed()
	{
		include_once( JPATH_ROOT.DS.'libraries'.DS.'joomla'.DS.'document'.DS.'feed'.DS.'feed.php');

		$mainframe =& $this->mainframe;

		// Set the mime encoding for the document
		$jdoc =& JFactory::getDocument();
		$jdoc->setMimeEncoding('application/rss+xml');

		// Start a new feed object
		$doc = new JDocumentFeed;
		$params =& $mainframe->getParams();
		$doc->link = JRoute::_('index.php?option='.$this->_option);

		// Incoming
		$period = JRequest::getVar( 'period', 'month' );

		// Paging variables
		$start = JRequest::getInt( 'limitstart', 0 );
		$limit = JRequest::getInt( 'limit', 25 );

		// Get categories
		$areas = $this->_getAreas();

		// Was there a category passed in the querystring?
		$area = trim(JRequest::getWord('category', ''));

		// Check the search string for a category prefix
		if ($period != NULL) {
			$searchstring = strtolower($period);
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
			$period = trim( $searchstring );
		}

		// Get the active category
		if ($area) {
			$activeareas = array($area);
		} else {
			$limit = 5;
			$activeareas = $areas;
		}

		// Load plugins
		JPluginHelper::importPlugin( 'whatsnew' );
		$dispatcher =& JDispatcher::getInstance();

		// Process the keyword for exact phrase matches, etc.
		$p = new WhatsnewPeriod( $period );
		$p->process();

		// Fetch results
		$results = $dispatcher->trigger( 'onWhatsNew', array(
				$p,
				$limit,
				$start,
				$activeareas)
			);

		$jconfig =& JFactory::getConfig();

		// Run through the array of arrays returned from plugins and find the one that returned results
		$rows = array();
		if ($results) {
			foreach ($results as $result)
			{
				if (is_array($result) && !empty($result)) {
					$rows = $result;
					break;
				}
			}
		}

		// Build some basic RSS document information
		$doc->title = $jconfig->getValue('config.sitename').' - '.JText::_('COM_WHATSNEW_RSS_TITLE').': '.$period;
		$doc->title .= ($area) ? ': '.$area : '';
		$doc->description = JText::sprintf('COM_WHATSNEW_RSS_DESCRIPTION',$jconfig->getValue('config.sitename'));
		$doc->copyright = JText::sprintf('COM_WHATSNEW_RSS_COPYRIGHT', date("Y"), $jconfig->getValue('config.sitename'));
		$doc->category = JText::_('COM_WHATSNEW_RSS_CATEGORY');

		// Start outputing results if any found
		if (count($rows) > 0) {
			include_once( JPATH_ROOT.DS.'components'.DS.'com_resources'.DS.'helpers'.DS.'helper.php' );

			foreach ($rows as $row)
			{
				// Prepare the title
				$title = strip_tags($row->title);
				$title = html_entity_decode($title);

				// URL link to article
				if (strstr( $row->href, 'view' )) {
					// tests to see if itemid has already been included - this occurs for typed content items
					if (!strstr( $row->href, 'Itemid' )) {
						$temp = explode( 'id=', $row->href );
						$row->href = $row->href.'&Itemid='.$mainframe->getItemid($temp[1]);
					}
				}
				$link = JRoute::_($row->href);

				// Strip html from feed item description text
				$description = html_entity_decode(Hubzero_View_Helper_Html::purifyText(stripslashes($row->text)));
				$author = '';
				@$date = ( $row->publish_up ? date( 'r', strtotime($row->publish_up) ) : '' );

				if (isset($row->ranking) || isset($row->rating)) {
					$resourceEx = new ResourceExtended($row->id, $this->database);
					$resourceEx->getCitationsCount();
					$resourceEx->getLastCitationDate();
					$resourceEx->getContributors();

					$author = strip_tags($resourceEx->contributors);
				}

				// Load individual item creator class
				$item = new JFeedItem();
				$item->title       = $title;
				$item->link        = $link;
				$item->description = $description;
				$item->date        = $date;
				$item->category    = (isset($row->typetitle)) ? $row->typetitle : '';
				$item->author      = $author;

				// Loads item info into rss array
				$doc->addItem( $item );
			}
		}

		// Output the feed
		echo $doc->render();
	}

	//----------------------------------------------------------
	// Private functions
	//----------------------------------------------------------

	private function _jtext($period)
	{
		switch ($period)
		{
			case 'week':    return JText::_('COM_WHATSNEW_OPT_WEEK');    break;
			case 'month':   return JText::_('COM_WHATSNEW_OPT_MONTH');   break;
			case 'quarter': return JText::_('COM_WHATSNEW_OPT_QUARTER'); break;
			case 'year':    return JText::_('COM_WHATSNEW_OPT_YEAR');    break;
			default:
				$thisyear = strftime("%Y",time());
				for ($y = $thisyear; $y >= 2002; $y--)
				{
					if (time() >= strtotime('10/1/'.$y)) {
						if ($y == $period) {
							return JText::_('COM_WHATSNEW_OPT_FISCAL_YEAR').' '.$y;
						}
					}
				}
				for ($y = $thisyear; $y >= 2002; $y--)
				{
					if (time() >= strtotime('01/01/'.$y)) {
						if ('c_'.$y == $period) {
							return JText::_('COM_WHATSNEW_OPT_CALENDAR_YEAR').' '.$y;
						}
					}
				}
			break;
		}
	}

	private function _getAreas()
	{
		// Do we already have an array of areas?
		if (!isset($this->searchareas) || empty($this->searchareas)) {
			// No - so we'll need to get it

			$areas = array();

			// Load the XSearch plugins
			JPluginHelper::importPlugin( 'whatsnew' );
			$dispatcher =& JDispatcher::getInstance();

			// Trigger the functions that return the areas we'll be searching
			$searchareas = $dispatcher->trigger( 'onWhatsNewAreas' );

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

