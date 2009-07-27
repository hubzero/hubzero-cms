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

		private function getAreas()
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

		public function display()
		{
			$mainframe =& $this->mainframe;
			$module    =& $this->module;

			// Get some initial parameters
			$params =& $this->params;
			$count    = $params->get( 'count', 5 );
			$feed     = $params->get( 'feed' );
			$moduleid = $params->get( 'moduleid' );
			$period   = $params->get( 'period', 'resources:month' );
			$tagged   = $params->get( 'tagged', 0 );

			$database =& JFactory::getDBO();

			// Build the feed link
			$feedlink = JRoute::_('index.php?option=com_whatsnew&amp;task=feed.rss&amp;period='.$period);
			if (substr($feedlink,0,5) == 'https') {
				$feedlink = ltrim($feedlink, 'https');
				$feedlink = 'http'.$feedlink;
			}
			if (substr($feedlink,0,1) == '/') {
				$xhub =& XFactory::getHub();
				$feedlink = $xhub->getCfg('hubLongURL').$feedlink;
			}

			// Get categories
			$areas = $this->getAreas();

			$area = '';

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
			$activeareas = array();
			if ($area) {
				$activeareas[] = $area;
			}

			// Load plugins
			JPluginHelper::importPlugin( 'whatsnew' );
			$dispatcher =& JDispatcher::getInstance();

			// Process the keyword for exact time period
			$p = new WhatsnewPeriod( $period );
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

			// Push the module CSS to the template
			ximport('xdocument');
			XDocument::addModuleStyleSheet('mod_whatsnew');

			// Output HTML
			$html  = '<div';
			$html .= ($moduleid) ? ' id="'.$moduleid.'"' : '';
			$html .= '>'."\n";

			if ($feed) {
				$html .= "\t".'<h3>' . $module->title;
				$html .= ' <a class="newsfeed" href="'.$feedlink.'" title="'.JText::_('SUBSCRIBE').'">'.JText::_('NEWS_FEED').'</a>';
				$html .= '</h3>'."\n";
			}

			if (!$tagged) {
				if (count($rows) > 0) {
					$count = 0;

					/*if ($tagged) {
						$html .= "\t".'<h4>'.JText::_('ALL').' '.ucfirst($area).'</h4>'."\n";
					}*/
					$html .= "\t".'<ul>'."\n";
					foreach ($rows as $row)
					{
						if (empty($row)) {
							continue;
						}
						$html .= "\t".' <li class="new">';
						$html .= '<a href="'. JRoute::_($row->href) .'">'.stripslashes($row->title).'</a><br />';
						$html .= '<span>'.JText::_('in').' ';
						$html .= ($row->section) ? JText::_($row->area) : JText::_(strtoupper($row->section));
						if ($row->publish_up) {
							$html .= ', '.JHTML::_('date', $row->publish_up, ' %b %d, %Y');
						}
						$html .= '</span></li>'."\n";

						$count++;
						if ($count >= 6) {
							break;
						}
					}
					$html .= "\t".'</ul>'."\n";
				} else {
					$html .= "\t".'<p>'.JText::_('NO_RESULTS').'</p>'."\n";
				}
			} else {
				$juser =& JFactory::getUser();

				include_once( JPATH_ROOT.DS.'components'.DS.'com_members'.DS.'members.tags.php' );
				$mt = new MembersTags( $database );
				$tags = $mt->get_tags_on_object($juser->get('id'), 0, 0, NULL, 0, 0);

				//$html .= "\t".'<h4>'.JText::_('IN_MY_INTERESTS').'</h4>'."\n";
				$html .= "\t".'<p class="category-header-details">'."\n";
				if (count($tags) > 0) {
					$html .= "\t\t".'<span class="configure">[<a href="'.JRoute::_('index.php?option=com_members'.a.'task=edit'.a.'id='.$juser->get('id')).'">'.JText::_('EDIT').'</a>]</span>'."\n";
				} else {
					$html .= "\t\t".'<span class="configure">[<a href="'.JRoute::_('index.php?option=com_members'.a.'task=edit'.a.'id='.$juser->get('id')).'">'.JText::_('ADD_INTERESTS').'</a>]</span>'."\n";
				}
				$html .= "\t\t".'<span class="q">'.JText::_('MY_INTERESTS').': '.$this->formatTags($tags).'</span>'."\n";
				$html .= "\t".'</p>'.n;

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

					if (count($rows2) > 0) {
						$count = 0;

						$html .= "\t".'<ul class="expandedlist">'."\n";
						foreach ($rows2 as $row2)
						{
							if (empty($row2)) {
								continue;
							}
							$html .= "\t".' <li class="new">';
							$html .= '<a href="'. JRoute::_($row2->href) .'">'.stripslashes($row2->title).'</a><br />';
							$html .= '<span>'.JText::_('in').' ';
							$html .= ($row2->section) ? JText::_($row2->area) : JText::_(strtoupper($row2->section));
							if ($row2->publish_up) {
								$html .= ', '.JHTML::_('date', $row2->publish_up, ' %b %d, %Y');
							}
							$html .= '</span></li>'."\n";

							$count++;
							if ($count >= 6) {
								break;
							}
						}
						$html .= "\t".'</ul>'."\n";
				    	//$html .= "\t".'<p class="more"><a href="'.JRoute::_('index.php?option=com_whatsnew').'">'.JText::_('VIEW_MORE').'</a></p>'."\n";
					} else {
						$html .= "\t".'<p>'.JText::_('NO_RESULTS').'</p>'."\n";
					}
				} else {
					$html .= "\t".'<p>'.JText::_('NO_RESULTS').'</p>'."\n";
				}
			}
			$html .= "\t".'<p class="more"><a href="'.JRoute::_('index.php?option=com_whatsnew&period='.$area.':'.$period).'">'.JText::_('VIEW_MORE').'</a></p>'."\n";
			$html .= '</div>'."\n";

			echo $html;
		}

		//-----------

		private function formatTags($tags=array(), $num=3, $max=25)
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
	}
}

//-------------------------------------------------------------

$modwhatsnew = new modWhatsNew();
$modwhatsnew->params = $params;
$modwhatsnew->mainframe = $mainframe;
$modwhatsnew->module = $module;

require( JModuleHelper::getLayoutPath('mod_whatsnew') );
?>
