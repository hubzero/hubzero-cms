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
JPlugin::loadLanguage( 'plg_whatsnew_topics' );

//-----------

class plgWhatsnewTopics extends JPlugin
{
	public function plgWhatsnewTopics(&$subject, $config)
	{
		parent::__construct($subject, $config);

		// load plugin parameters
		$this->_plugin = JPluginHelper::getPlugin( 'whatsnew', 'topics' );
		$this->_params = new JParameter( $this->_plugin->params );
	}
	
	//-----------
	
	public function onWhatsnewAreas() 
	{
		$areas = array(
			'topics' => JText::_('PLG_WHATSNEW_TOPICS')
		);
		return $areas;
	}

	//-----------

	public function onWhatsnew( $period, $limit=0, $limitstart=0, $areas=null, $tagids=array() )
	{
		if (is_array( $areas ) && $limit) {
			if (!array_intersect( $areas, $this->onWhatsnewAreas() ) && !array_intersect( $areas, array_keys( $this->onWhatsnewAreas() ) )) {
				return array();
			}
		}
		
		// Do we have a time period?
		if (!is_object($period)) {
			return array();
		}

		$database =& JFactory::getDBO();

		ximport('wiki.page');
		
		// Instantiate some needed objects
		$wp = new WikiPage( $database );
		
		// Build query
		$filters = array();
		$filters['startdate'] = $period->cStartDate;
		$filters['enddate'] = $period->cEndDate;
		$filters['sortby'] = 'date';
		$filters['authorized'] = $this->_authorize();
		if (count($tagids) > 0) {
			$filters['tags'] = $tagids;
		}

		if (!$limit) {
			// Get a count
			$filters['select'] = 'count';
			
			$database->setQuery( $wp->buildPluginQuery( $filters ) );
			return $database->loadResult();
		} else {
			// Get results
			$filters['select'] = 'records';
			$filters['limit'] = $limit;
			$filters['limitstart'] = $limitstart;
			
			$database->setQuery( $wp->buildPluginQuery( $filters ) );
			$rows = $database->loadObjectList();

			if ($rows) {
				foreach ($rows as $key => $row) 
				{
					if ($row->area != '' && $row->category != '') {
						$rows[$key]->href = JRoute::_('index.php?option=com_groups&scope='.$row->category.'&pagename='.$row->alias);
					} else {
						$rows[$key]->href = JRoute::_('index.php?option=com_topics&scope='.$row->category.'&pagename='.$row->alias);
					}
					$rows[$key]->text = $rows[$key]->itext;
					if ($row->title == '') {
						$rows[$key]->title = $rows[$key]->alias;
					}
				}
			}

			return $rows;
		}
	}
	
	//-----------

	private function _authorize() 
	{
		// Check if they are logged in
		$juser =& JFactory::getUser();
		if ($juser->get('guest')) {
			return false;
		}
		
		return true;
	}
	
	//----------------------------------------------------------
	// Optional custom functions
	// uncomment to use
	//----------------------------------------------------------

	/*public function documents() 
	{
		// ...
	}

	//-----------

	public function before()
	{
		// ...
	}

	//-----------

	public function out()
	{
		// ...
	}

	//-----------

	public function after()
	{
		// ...
	}*/
}
