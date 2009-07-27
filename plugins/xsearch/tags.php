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
JPlugin::loadLanguage( 'plg_xsearch_tags' );

//-----------

class plgXSearchTags extends JPlugin
{
	function plgXSearchTags(&$subject, $config)
	{
		parent::__construct($subject, $config);

		// load plugin parameters
		$this->_plugin = JPluginHelper::getPlugin( 'xsearch', 'tags' );
		$this->_params = new JParameter( $this->_plugin->params );
	}
	
	//-----------
	
	function &onXSearchAreas() 
	{
		$areas = array(
			'tags' => JText::_('TAGS')
		);
		return $areas;
	}

	//-----------

	function onXSearch( $searchquery, $limit=0, $limitstart=0, $areas=null )
	{
		$database =& JFactory::getDBO();
		include_once(JPATH_ROOT.DS.'components'.DS.'com_tags'.DS.'tags.class.php');

		if (is_array( $areas ) && $limit) {
			if (!array_intersect( $areas, $this->onXSearchAreas() ) && !array_intersect( $areas, array_keys( $this->onXSearchAreas() ) )) {
				return array();
			}
		}

		$t = $searchquery->searchTokens;
		if (empty($t)) {
			return array();
		}

		$tagging = new Tags();

		$text = '';
		if (!empty($searchquery->searchPhrases)) {
			$text = implode('" "',$searchquery->searchPhrases);
			$text = '"'.$text.'"';
		}
		$w = implode(' ',$searchquery->searchWords);
		$text .= ($w) ? ' '.$w : '';
		$text = addslashes($text);

		$rawtags = $searchquery->searchTokens;

		// Build query
		if (!$limit) {
			$query = "SELECT COUNT(*) ";
		} else {
			$query = "SELECT t.id, t.raw_tag AS title, t.tag AS alias, t.description AS itext, NULL AS ftext, NULL AS state, NULL AS created, NULL AS modified, NULL AS publish_up, NULL AS params,
					CONCAT( 'index.php?option=com_tags&tag=', t.tag ) as href, 'tags' AS section, NULL AS area, NULL AS category, NULL AS rating, NULL AS times_rated, NULL AS ranking, NULL AS access, (MATCH(t.description) AGAINST ('$text' IN BOOLEAN MODE)) AS relevance ";
		}
		$query .= "FROM #__tags AS t WHERE ";
		foreach ($rawtags as $rawtag)
		{
			$normtag = $tagging->normalize_tag($rawtag);
			$query .= "((LOWER(raw_tag) LIKE '%". addslashes($rawtag) ."%' OR tag LIKE '%". $normtag ."%' OR alias LIKE '%". $normtag ."%') AND t.admin=0) OR ";
		}
		$query .= " (MATCH(t.description) AGAINST ('$text' IN BOOLEAN MODE) > 0)";

		if (!$limit) {
			// Execute query
			$database->setQuery( $query );
			return $database->loadResult();
		} else {
			if (count($areas) > 1) {
				return $query;
			}
			
			$query .= " ORDER BY relevance";
			$query .= ($limit != 'all') ? " LIMIT $limitstart,$limit" : "";

			// Execute query
			$database->setQuery( $query );
			$rows = $database->loadObjectList();

			// Go through results and set the HREF
			if ($rows) {
				foreach ($rows as $key => $row) 
				{
					$rows[$key]->href = JRoute::_('index.php?option=com_tags&tag='.$row->alias);
				}
			} else {
				$rows = array();
			}

			return $rows;
		}
	}
	
	//----------------------------------------------------------
	// Optional custom functions
	// uncomment to use
	//----------------------------------------------------------

	/*function documents() 
	{
		// ...
	}

	//-----------

	function before()
	{
		// ...
	}

	//-----------

	function out()
	{
		// ...
	}

	//-----------

	function after()
	{
		// ...
	}*/
}
