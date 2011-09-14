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

jimport( 'joomla.plugin.plugin' );
JPlugin::loadLanguage( 'plg_xsearch_tags' );

class plgXSearchTags extends JPlugin
{
	public function plgXSearchTags(&$subject, $config)
	{
		parent::__construct($subject, $config);

		// load plugin parameters
		$this->_plugin = JPluginHelper::getPlugin( 'xsearch', 'tags' );
		$this->_params = new JParameter( $this->_plugin->params );
	}

	public function &onXSearchAreas()
	{
		$areas = array(
			'tags' => JText::_('PLG_XSEARCH_TAGS')
		);
		return $areas;
	}

	public function onXSearch( $searchquery, $limit=0, $limitstart=0, $areas=null )
	{
		if (is_array( $areas ) && $limit) {
			if (!array_intersect( $areas, $this->onXSearchAreas() ) && !array_intersect( $areas, array_keys( $this->onXSearchAreas() ) )) {
				return array();
			}
		}

		$t = $searchquery->searchTokens;
		if (empty($t)) {
			return array();
		}

		$database =& JFactory::getDBO();
		include_once(JPATH_ROOT.DS.'components'.DS.'com_tags'.DS.'helpers'.DS.'handler.php');

		$tagging = new TagsHandler($database);

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

