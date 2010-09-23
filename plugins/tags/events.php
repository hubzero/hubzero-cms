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
JPlugin::loadLanguage( 'plg_tags_events' );

//-----------

class plgTagsEvents extends JPlugin
{
	private $_total = null;
	
	//-----------
	
	public function plgTagsEvents(&$subject, $config)
	{
		parent::__construct($subject, $config);

		// load plugin parameters
		$this->_plugin = JPluginHelper::getPlugin( 'tags', 'events' );
		$this->_params = new JParameter( $this->_plugin->params );
	}

	//-----------

	public function onTagAreas() 
	{
		$areas = array(
			'events' => JText::_('PLG_TAGS_EVENTS')
		);
		return $areas;
	}

	//-----------

	public function onTagView( $tags, $limit=0, $limitstart=0, $sort='', $areas=null )
	{
		if (is_array( $areas ) && $limit) {
			if (!array_intersect( $areas, $this->onTagAreas() ) && !array_intersect( $areas, array_keys( $this->onTagAreas() ) )) {
				return array();
			}
		}

		// Do we have a member ID?
		if (empty($tags)) {
			return array();
		}
		
		$database =& JFactory::getDBO();

		$ids = array();
		foreach ($tags as $tag) 
		{
			$ids[] = $tag->id;
		}
		$ids = implode(',',$ids);
		
		$now = date( 'Y-m-d H:i:s', time() + 0 * 60 * 60 );
		
		// Build the query
		/*$e_count = "SELECT e.id, COUNT(DISTINCT t.tag) AS uniques";
		$e_fields = "SELECT e.id, e.title, e.content AS text, COUNT(DISTINCT t.tag) AS uniques, CONCAT( 'index.php?option=com_events&task=details&id=', e.id ) AS href, e.publish_up, 'events' AS section";
		$e_from  = " FROM #__events AS e, #__tags_object AS tg";
		$e_from .= " INNER JOIN #__tags AS t ON (tg.tagid = t.id)";
		$e_where = " WHERE e.state=1 AND tg.objectid=e.id AND tg.tbl='events' AND t.id=tg.tagid AND t.id IN ($ids)";*/
		$e_count = "SELECT COUNT(f.id) FROM (SELECT e.id, COUNT(DISTINCT t.tagid) AS uniques";
		$e_fields = "SELECT e.id, e.title, e.content AS text, COUNT(DISTINCT t.tagid) AS uniques, CONCAT( 'index.php?option=com_events&task=details&id=', e.id ) AS href, e.publish_up, 'events' AS section";
		$e_from  = " FROM #__events AS e, #__tags_object AS t";
		$e_where = " WHERE e.state=1 AND t.objectid=e.id AND t.tbl='events' AND t.tagid IN ($ids)";
		
		//$e_where .= " AND (e.publish_up = '0000-00-00 00:00:00' OR e.publish_up <= '".$now."') ";
		//$e_where .= " AND (e.publish_down = '0000-00-00 00:00:00' OR e.publish_down >= '".$now."') ";
		$e_where .= " GROUP BY e.id HAVING uniques=".count($tags);
		$order_by  = " ORDER BY ";
		switch ($sort) 
		{
			case 'title': $order_by .= 'title ASC, publish_up';  break;
			case 'id':    $order_by .= "id DESC";                break;
			case 'date':  
			default:      $order_by .= 'publish_up DESC, title'; break;
		}
		$order_by .= ($limit != 'all') ? " LIMIT $limitstart,$limit" : "";


		if (!$limit) {
			// Get a count
			$database->setQuery( $e_count . $e_from . $e_where .") AS f" );
			$this->_total = $database->loadResult();
			return $this->_total;
		} else {
			if ($this->_total != null) {
				if ($this->_total == 0) {
					return array();
				}
			}
			
			// Get results
			$database->setQuery( $e_fields . $e_from . $e_where. $order_by );
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

	public function documents() 
	{
		ximport('Hubzero_Document');
		Hubzero_Document::addComponentStylesheet('com_events');
	}
	
	//-----------
	
	/*public function before()
	{
		// ...
	}*/
	
	//-----------
	
	public function out( $row )
	{
		$month = JHTML::_('date', $row->publish_up, '%b');
		$day = JHTML::_('date', $row->publish_up, '%d');
		$year = JHTML::_('date', $row->publish_up, '%Y');
		
		// Start building the HTML
		$html  = "\t".'<li class="event">'."\n";
		$html .= "\t\t".'<p class="event-date"><span class="month">'.$month.'</span> <span class="day">'.$day.'</span> <span class="year">'.$year.'</span></p>'."\n";
		$html .= "\t\t".'<p class="title"><a href="'.$row->href.'">'.stripslashes($row->title).'</a></p>'."\n";
		if ($row->text) {
			$row->text = str_replace('[[BR]]', '', $row->text);
			$html .= "\t\t".Hubzero_View_Helper_Html::shortenText(Hubzero_View_Helper_Html::purifyText(stripslashes($row->text)), 200)."\n";
		}
		$html .= "\t".'</li>'."\n";
		
		// Return output
		return $html;
	}
	
	//-----------
	
	/*public function after()
	{
		// ...
	}*/
}
