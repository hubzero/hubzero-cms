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

//-----------

jimport( 'joomla.plugin.plugin' );
JPlugin::loadLanguage( 'plg_whatsnew_events' );

//-----------

class plgWhatsnewEvents extends JPlugin
{
	function plgWhatsnewEvents(&$subject, $config)
	{
		parent::__construct($subject, $config);

		// load plugin parameters
		$this->_plugin = JPluginHelper::getPlugin( 'whatsnew', 'events' );
		$this->_params = new JParameter( $this->_plugin->params );
	}
	
	//-----------

	function &onWhatsnewAreas() 
	{
		$areas = array(
			'events' => JText::_('EVENTS')
		);
		return $areas;
	}

	//-----------

	function onWhatsnew( $period, $limit=0, $limitstart=0, $areas=null, $tagids=array() )
	{
		$database =& JFactory::getDBO();

		if (is_array( $areas ) && $limit) {
			if (!array_intersect( $areas, $this->onWhatsnewAreas() ) && !array_intersect( $areas, array_keys( $this->onWhatsnewAreas() ) )) {
				return array();
			}
		}

		// Do we have a search term?
		if (!is_object($period)) {
			return array();
		}

		// Build the query
		$e_count = "SELECT count(DISTINCT e.id)";
		/*$e_fields = "SELECT "
				. " e.id,"
				. " e.title, "
				. " e.content AS text,"
				. " CONCAT( 'index.php?option=com_events&task=details&id=', e.id ) AS href,"
				. " e.publish_up AS publish_up,"
				. " 'events' AS section, NULL AS subsection";*/
		$e_fields = "SELECT e.id, e.title, NULL AS alias, e.content AS itext, NULL AS ftext, e.state, e.created, e.modified, e.publish_up, NULL AS params, 
					CONCAT( 'index.php?option=com_events&task=details&id=', e.id ) AS href, 'events' AS section, NULL AS area, NULL AS category, NULL AS rating, NULL AS times_rated, NULL AS ranking, e.access ";
		$e_from = " FROM #__events AS e";

		$e_where = "e.created > '$period->cStartDate' AND e.created < '$period->cEndDate'";

		$order_by  = " ORDER BY publish_up DESC, title";
		$order_by .= ($limit != 'all') ? " LIMIT $limitstart,$limit" : "";


		if (!$limit) {
			// Get a count
			$database->setQuery( $e_count.$e_from ." WHERE ". $e_where );
			return $database->loadResult();
		} else {
			// Get results
			$query = $e_fields.$e_from ." WHERE ". $e_where . $order_by;
			$database->setQuery( $query );
			$rows = $database->loadObjectList();

			if ($rows) {
				foreach ($rows as $key => $row) 
				{
					$rows[$key]->href = JRoute::_($row->href);
				}
			}

			return $rows;
		}
	}
	
	//----------------------------------------------------------
	// Optional custom functions
	// uncomment to use
	//----------------------------------------------------------

	function documents() 
	{
		ximport('xdocument');
		XDocument::addComponentStylesheet('com_events');
	}
	
	//-----------
	
	/*function before()
	{
		// ...
	}*/
	
	//-----------
	
	function out( $row, $period )
	{
		$month = JHTML::_('date', $row->publish_up, '%b');
		$day = JHTML::_('date', $row->publish_up, '%d');
		$year = JHTML::_('date', $row->publish_up, '%Y');
		
		if (strstr( $row->href, 'index.php' )) {
			$row->href = JRoute::_($row->href);
		}
		$juri =& JURI::getInstance();
		if (substr($row->href,0,1) == '/') {
			$row->href = substr($row->href,1,strlen($row->href));
		}
		
		// Start building the HTML
		$html  = t.'<li class="event">'.n;
		$html .= t.t.'<p class="event-date"><span class="month">'.$month.'</span> <span class="day">'.$day.'</span> <span class="year">'.$year.'</span></p>'.n;
		$html .= t.t.'<p class="title"><a href="'.$row->href.'">'.stripslashes($row->title).'</a></p>'.n;
		if ($row->itext) {
			$row->itext = str_replace('[[BR]]', '', $row->itext);
			$html .= t.t.'<p>&#133; '.WhatsnewHtml::cleanText(stripslashes($row->itext),200).'</p>'.n;
		}
		$html .= t.t.'<p class="href">'.$juri->base().$row->href.'</p>'.n;
		$html .= t.'</li>'.n;
		
		// Return output
		return $html;
	}
	
	//-----------
	
	/*function after()
	{
		// ...
	}*/
}
