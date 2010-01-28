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
JPlugin::loadLanguage( 'plg_xsearch_groups' );

//-----------

class plgXSearchGroups extends JPlugin
{
	public function plgXSearchGroups(&$subject, $config)
	{
		parent::__construct($subject, $config);

		// load plugin parameters
		$this->_plugin = JPluginHelper::getPlugin( 'xsearch', 'groups' );
		$this->_params = new JParameter( $this->_plugin->params );
	}
	
	//-----------
	
	public function &onXSearchAreas()
	{
		$areas = array(
			'groups' => JText::_('PLG_XSEARCH_GROUPS')
		);
		return $areas;
	}
	
	//-----------

	public function onXSearch( $searchquery, $limit=0, $limitstart=0, $areas=null )
	{
		if (is_array( $areas ) && $limit) {
			if (!array_intersect( $areas, $this->onXSearchAreas() ) && !array_intersect( $areas, array_keys( $this->onXSearchAreas() ) )) {
				return array();
			}
		}

		// Do we have a search term?
		$t = $searchquery->searchTokens;
		if (empty($t)) {
			return array();
		}
		
		$database =& JFactory::getDBO();

		// An array for all the words and phrases
		$words = $searchquery->searchTokens;

		// Build the query
		$c_count = "SELECT COUNT(*) ";
		$b = '';
		foreach ($words as $word) 
		{
			if (trim($word) != '') {
				$word = addslashes($word);
				$b .= "CASE WHEN LOWER(g.description) LIKE '%$word%' THEN 4 ELSE 0 END + ";
				$b .= "CASE WHEN LOWER(g.cn) LIKE '%$word%' THEN 4 ELSE 0 END + ";
				$b .= "CASE WHEN LOWER(g.description) LIKE '%".addslashes(implode(' ',$searchquery->searchTokens))."%' THEN 8 ELSE 0 END + ";
				$b .= "CASE WHEN LOWER(g.public_desc) LIKE '%$word%' THEN 4 ELSE 0 END + ";
			}
		}
		$b = substr($b, 0, -3);
		$c_fields = "SELECT g.gidNumber AS id, g.description AS title, g.cn AS alias, g.public_desc AS itext, NULL AS ftext, g.published AS state, NULL AS created, NULL AS modified, NULL AS publish_up, NULL AS params, 
		 			CONCAT( 'index.php?option=com_groups&gid=', g.cn ) as href, 'groups' AS section, NULL AS area, NULL AS category, NULL AS rating, NULL AS times_rated, NULL AS ranking, g.access, ($b) AS relevance ";
		$c_from = "FROM #__xgroups AS g 
				WHERE g.type=1 AND g.privacy<=1 AND (";
		foreach ($words as $word) 
		{
			if (trim($word) != '') {
				$word = addslashes($word);
				$c_from .= "(LOWER(g.description) LIKE '%$word%') OR (LOWER(g.cn) LIKE '%$word%') OR (LOWER(g.public_desc) LIKE '%$word%') OR ";
			}
		}
		$c_from = substr($c_from, 0, -4);
		$c_from .= ")";
		$c_order = " ORDER BY relevance DESC";
		$c_limit = ($limit != 'all') ? " LIMIT $limitstart,$limit" : "";

		if (!$limit) {
			// Get a count
			$database->setQuery( $c_count.$c_from );
			return $database->loadResult();
		} else {
			if (count($areas) > 1) {
				return $c_fields.$c_from;
			}
			
			// Get results
			$database->setQuery( $c_fields.$c_from.$c_order.$c_limit );
			$rows = $database->loadObjectList();

			foreach ($rows as $key => $row) 
			{
				$rows[$key]->href = JRoute::_('index.php?option=com_groups&gid='.$row->alias);
			}

			return $rows;
		}
	}

	//----------------------------------------------------------
	// Optional custom functions
	// uncomment to use
	//----------------------------------------------------------

	/*public function documents() 
	{
		ximport('xdocument');
		XDocument::addComponentStylesheet('com_groups');
	}

	//-----------

	public function before()
	{
		// ...
	}

	//-----------

	public function out( $row, $keyword )
	{
		// ...
	}

	//-----------

	public function after()
	{
		// ...
	}*/
}