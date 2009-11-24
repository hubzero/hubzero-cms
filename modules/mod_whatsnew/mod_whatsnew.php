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

//-------------------------------------------------------------

include_once( JPATH_ROOT.DS.'components'.DS.'com_whatsnew'.DS.'whatsnew.period.php' );

if (!class_exists('modWhatsNew')) {
	class modWhatsNew
	{
		private $attributes = array();

		//-----------

		public function __construct( $params ) 
		{
			$this->params = $params;
		}

		//-----------

		public function __set($property, $value)
		{
			$this->attributes[$property] = $value;
		}

		//-----------

		public function __get($property)
		{
			if (isset($this->attributes[$property])) {
				return $this->attributes[$property];
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

		public function formatTags($tags=array(), $num=3, $max=25)
		{
			$out = '';

			if (count($tags) > 0) {
				$out .= '<span class="taggi">'."\n";
				$counter = 0;

				for ($i=0; $i< count($tags); $i++) 
				{
					$counter = $counter + strlen(stripslashes($tags[$i]['raw_tag']));	
					if ($counter > $max) {
						$num = $num - 1;
					}
					if ($i < $num) {
						// display tag
						$out .= "\t".'<a href="'.JRoute::_('index.php?option=com_tags'.a.'tag='.$tags[$i]['tag']).'">'.stripslashes($tags[$i]['raw_tag']).'</a> '."\n";
					}
				}
				if ($i > $num) {
					$out .= ' (&#8230;)';
				}
				$out .= '</span>'."\n";
			}

			return $out;
		}

		//-----------

		public function display()
		{
			$module    =& $this->module;

			// Get some initial parameters
			$params =& $this->params;
			$count    = $params->get( 'count', 5 );
			$this->feed     = $params->get( 'feed' );
			$this->moduleid = $params->get( 'moduleid' );
			$this->period   = $params->get( 'period', 'resources:month' );
			$this->tagged   = $params->get( 'tagged', 0 );

			$database =& JFactory::getDBO();

			// Build the feed link if necessary
			if ($this->feed) {
				$feedlink = JRoute::_('index.php?option=com_whatsnew&amp;task=feed.rss&amp;period='.$period);
				if (substr($feedlink,0,5) == 'https') {
					$feedlink = ltrim($feedlink, 'https');
					$feedlink = 'http'.$feedlink;
				}
				if (substr($feedlink,0,1) == '/') {
					$xhub =& XFactory::getHub();
					$feedlink = $xhub->getCfg('hubLongURL').$feedlink;
				}
				$this->feedlink = $feedlink;
			}
			
			// Get categories
			$areas = $this->_getAreas();

			$area = '';

			// Check the search string for a category prefix
			if ($this->period != NULL) {
				$searchstring = strtolower($this->period);
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
				$this->period = trim( $searchstring );
			}
			$this->area = $area;

			// Get the active category
			$activeareas = array();
			if ($area) {
				$activeareas[] = $area;
			}

			// Load plugins
			JPluginHelper::importPlugin( 'whatsnew' );
			$dispatcher =& JDispatcher::getInstance();

			// Process the keyword for exact time period
			$p = new WhatsnewPeriod( $this->period );
			$p->process();

			// Get the search results
			$results = $dispatcher->trigger( 'onWhatsnew', array(
					$p, 
					$count, 
					0,
					$activeareas,
					array())
				);

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
			
			$this->rows = $rows;
			$this->rows2 = null;
			
			if ($this->tagged) {
				$juser =& JFactory::getUser();

				include_once( JPATH_ROOT.DS.'components'.DS.'com_members'.DS.'members.tags.php' );
				$mt = new MembersTags( $database );
				$tags = $mt->get_tags_on_object($juser->get('id'), 0, 0, NULL, 0, 0);
				
				$this->tags = $tags;
				
				if (count($tags) > 0) {
					$tagids = array();
					foreach ($tags as $tag) 
					{
						$tagids[] = $tag['tag_id'];
					}

					// Get the search results
					$results2 = $dispatcher->trigger( 'onWhatsnew', array(
							$p, 
							$count, 
							0,
							$activeareas,
							$tagids)
						);

					$rows2 = array();

					if ($results2) {
						foreach ($results2 as $result2) 
						{
							if (is_array($result2) && !empty($result2)) {
								$rows2 = $result2;
								break;
							}
						}
					}
					
					$this->rows2 = $rows2;
				}
			}

			// Push the module CSS to the template
			ximport('xdocument');
			XDocument::addModuleStyleSheet('mod_whatsnew');	
		}
	}
}

//-------------------------------------------------------------

$modwhatsnew = new modWhatsNew( $params );
$modwhatsnew->module = $module;
$modwhatsnew->display();

require( JModuleHelper::getLayoutPath('mod_whatsnew') );
?>