<?php
/**
 * @package		HUBzero CMS
 * @author		Shawn Rice <zooley@purdue.edu>
 * @copyright	Copyright 2005-2009 by Purdue Research Foundation, West Lafayette, IN 47906
 * @license		http://www.gnu.org/licenses/gpl-2.0.html GPLv2
 *
 * Copyright 2005-2009 by Purdue Research Foundation, West Lafayette, IN 47906.
 * All rights reserved.
 *
 * This program is free software; you can redistribute it and/or
 * modify it under the terms of the GNU General Public License,
 * version 2 as published by the Free Software Foundation.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program; if not, write to the Free Software
 * Foundation, Inc., 51 Franklin Street, Fifth Floor, Boston, MA  02110-1301, USA.
 */

// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die( 'Restricted access' );

class WhatsnewController extends JObject
{	
	private $_name  = NULL;
	private $_data  = array();
	private $_task  = NULL;

	//-----------
	
	public function __construct( $config=array() )
	{
		$this->_redirect = NULL;
		$this->_message = NULL;
		$this->_messageType = 'message';
		
		// Set the controller name
		if (empty( $this->_name )) {
			if (isset($config['name'])) {
				$this->_name = $config['name'];
			} else {
				$r = null;
				if (!preg_match('/(.*)Controller/i', get_class($this), $r)) {
					echo "Controller::__construct() : Can't get or parse class name.";
				}
				$this->_name = strtolower( $r[1] );
			}
		}
		
		// Set the component name
		$this->_option = 'com_'.$this->_name;
	}

	//-----------

	public function __set($property, $value)
	{
		$this->_data[$property] = $value;
	}
	
	//-----------
	
	public function __get($property)
	{
		if (isset($this->_data[$property])) {
			return $this->_data[$property];
		}
	}
	
	//-----------
	
	private function _getTask()
	{
		$task = JRequest::getVar( 'task', '' );
		$this->_task = $task;
		return $task;
	}
	
	//----------------------------------------------------------
	// Views
	//----------------------------------------------------------
	
	public function execute()
	{
		switch ($this->_getTask()) 
		{
			case 'browse':   $this->browse(); break;
			case 'feed.rss': $this->feed();   break;
			case 'feed':     $this->feed();   break;
			
			default: $this->browse(); break;
		}
	}
	
	//-----------
	
	public function browse()
	{
		$database =& JFactory::getDBO();

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
		$document->setTitle( JText::_(strtoupper($this->_name)).': '.WhatsnewHtml::jtext($period) );
		
		$app =& JFactory::getApplication();
		$pathway =& $app->getPathway();
		if (count($pathway->getPathWay()) <= 0) {
			$pathway->addItem(JText::_(strtoupper($this->_name)),'index.php?option='.$this->_option);
		}
		$pathway->addItem(WhatsnewHtml::jtext($period),'index.php?option='.$this->_option.a.'period='.$period);
		
		// Output HTML
		echo WhatsnewHtml::display( $period, $totals, $results, $cats, $active, $this->_option, $start, $limit, $total );
	}
	
	//-----------
	
	protected function feed() 
	{
		include_once( JPATH_ROOT.DS.'libraries'.DS.'joomla'.DS.'document'.DS.'feed'.DS.'feed.php');
		
		$mainframe =& $this->mainframe;
		$database =& JFactory::getDBO();
		
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
		$doc->title = $jconfig->getValue('config.sitename').' - '.JText::_('WHATSNEW_RSS_TITLE').': '.$period;
		$doc->title .= ($area) ? ': '.$area : '';
		$doc->description = JText::sprintf('WHATSNEW_RSS_DESCRIPTION',$jconfig->getValue('config.sitename'));
		$doc->copyright = JText::sprintf('WHATSNEW_RSS_COPYRIGHT', date("Y"), $jconfig->getValue('config.sitename'));
		$doc->category = JText::_('WHATSNEW_RSS_CATEGORY');

		// Start outputing results if any found
		if (count($rows) > 0) {
			include_once( JPATH_ROOT.DS.'components'.DS.'com_resources'.DS.'resources.extended.php' );
			
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
						$row->href = $row->href.a.'Itemid='.$mainframe->getItemid($temp[1]);
					}
				}
				$link = JRoute::_($row->href);

				// Strip html from feed item description text
				$description = html_entity_decode(WhatsnewHtml::cleanText(stripslashes($row->text)));
				$author = '';
				@$date = ( $row->publis_up ? date( 'r', strtotime($row->publish_up) ) : '' );

				if (isset($row->ranking) || isset($row->rating)) {
					$resourceEx = new ResourceExtended($row->id, $database);
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

	//-----------

	public function redirect()
	{
		if ($this->_redirect != NULL) {
			$app =& JFactory::getApplication();
			$app->redirect( $this->_redirect, $this->_message, $this->_messageType );
		}
	}
	
	//----------------------------------------------------------
	// Private functions
	//----------------------------------------------------------
	
	private function _getStyles($option='') 
	{
		ximport('xdocument');
		if ($option) {
			XDocument::addComponentStylesheet('com_'.$option);
		} else {
			XDocument::addComponentStylesheet($this->_option);
		}
	}

	//-----------
	
	private function _getScripts($option='',$name='')
	{
		$document =& JFactory::getDocument();
		if ($option) {
			$name = ($name) ? $name : $option;
			if (is_file(JPATH_ROOT.DS.'components'.DS.'com_'.$option.DS.$name.'.js')) {
				$document->addScript('components'.DS.'com_'.$option.DS.$name.'.js');
			}
		} else {
			if (is_file(JPATH_ROOT.DS.'components'.DS.$this->_option.DS.$this->_name.'.js')) {
				$document->addScript('components'.DS.$this->_option.DS.$this->_name.'.js');
			}
		}
	}
	
	//-----------
	
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
	
	//-----------

	private function _authorize() 
	{
		// Check if they are logged in
		$juser =& JFactory::getUser();
		if ($juser->get('guest')) {
			return false;
		}
	
		// Check if they're a site admin (from Joomla)
		if ($juser->authorize($this->_option, 'manage')) {
			return true;
		}

		return false;
	}
}
?>