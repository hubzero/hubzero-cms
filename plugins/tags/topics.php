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
JPlugin::loadLanguage( 'plg_tags_topics' );

//-----------

class plgTagsTopics extends JPlugin
{
	private $_total = null;
	
	//-----------
	
	function plgTagsTopics(&$subject, $config)
	{
		parent::__construct($subject, $config);

		// load plugin parameters
		$this->_plugin = JPluginHelper::getPlugin( 'tags', 'topics' );
		$this->_params = new JParameter( $this->_plugin->params );
	}

	//-----------

	function onTagAreas()
	{
		$areas = array(
			'topics' => JText::_('Topics')
		);
		return $areas;
	}
	
	//-----------

	function onTagView( $tags, $limit=0, $limitstart=0, $sort='', $areas=null )
	{
		$database =& JFactory::getDBO();

		// Check if our area is in the array of areas we want to return results for
		if (is_array( $areas ) && $limit) {
			if (!array_intersect( $areas, $this->onTagAreas() ) && !array_intersect( $areas, array_keys( $this->onTagAreas() ) )) {
				return array();
			}
		}

		// Do we have a member ID?
		if (empty($tags)) {
			return array();
		}

		$ids = array();
		foreach ($tags as $tag) 
		{
			$ids[] = $tag->id;
		}

		ximport('wiki.page');
		
		// Instantiate some needed objects
		$wp = new WikiPage( $database );
		
		// Build query
		$filters = array();
		$filters['tags'] = $ids;
		$filters['sortby'] = ($sort) ? $sort : 'id';
		$filters['authorized'] = $this->_authorize();

		// Execute the query
		if (!$limit) {
			$filters['select'] = 'count';
			
			$database->setQuery( $wp->buildPluginQuery( $filters ) );
			$this->_total = $database->loadResult();
			return $this->_total;
		} else {
			if ($this->_total != null) {
				if ($this->_total == 0) {
					return array();
				}
			}
			
			$filters['select'] = 'records';
			$filters['limit'] = $limit;
			$filters['limitstart'] = $limitstart;
			
			$database->setQuery( $wp->buildPluginQuery( $filters ) );
			$rows = $database->loadObjectList();

			// Did we get any results?
			if ($rows) {
				// Loop through the results and set each item's HREF
				foreach ($rows as $key => $row) 
				{
					if ($row->area != '' && $row->category != '') {
						$rows[$key]->href = JRoute::_('index.php?option=com_groups&scope='.$row->category.'&pagename='.$row->alias);
					} else {
						$rows[$key]->href = JRoute::_('index.php?option=com_topics&scope='.$row->category.'&pagename='.$row->alias);
					}
					$rows[$key]->text = $rows[$key]->itext;
				}
			}

			// Return the results
			return $rows;
		}
	}
	
	//-----------

	function _authorize() 
	{
		// Check if they are logged in
		$juser =& JFactory::getUser();
		if ($juser->get('guest')) {
			return false;
		}
		
		// Check if they're a site admin (from LDAP)
		$xuser =& XFactory::getUser();
		if (is_object($xuser)) {
			$app =& JFactory::getApplication();
			if (in_array(strtolower($app->getCfg('sitename')), $xuser->get('admin'))) {
				return 'admin';
			}
		}
		
		return true;
	}
}