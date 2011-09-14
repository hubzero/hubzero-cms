<?php
/**
 * HUBzero CMS
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
 *
 * @package   hubzero-cms
 * @author    Shawn Rice <zooley@purdue.edu>
 * @copyright Copyright 2005-2011 Purdue University. All rights reserved.
 * @license   http://www.gnu.org/licenses/lgpl-3.0.html LGPLv3
 */

// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die( 'Restricted access' );

jimport( 'joomla.plugin.plugin' );
JPlugin::loadLanguage( 'plg_xsearch_groups' );

/**
 * Short description for 'plgXSearchGroups'
 * 
 * Long description (if any) ...
 */
class plgXSearchGroups extends JPlugin
{

	/**
	 * Short description for 'plgXSearchGroups'
	 * 
	 * Long description (if any) ...
	 * 
	 * @param      unknown &$subject Parameter description (if any) ...
	 * @param      unknown $config Parameter description (if any) ...
	 * @return     void
	 */
	public function plgXSearchGroups(&$subject, $config)
	{
		parent::__construct($subject, $config);

		// load plugin parameters
		$this->_plugin = JPluginHelper::getPlugin( 'xsearch', 'groups' );
		$this->_params = new JParameter( $this->_plugin->params );
	}

	/**
	 * Short description for 'onXSearchAreas'
	 * 
	 * Long description (if any) ...
	 * 
	 * @return     array Return description (if any) ...
	 */
	public function &onXSearchAreas()
	{
		$areas = array(
			'groups' => JText::_('PLG_XSEARCH_GROUPS')
		);
		return $areas;
	}

	/**
	 * Short description for 'onXSearch'
	 * 
	 * Long description (if any) ...
	 * 
	 * @param      object $searchquery Parameter description (if any) ...
	 * @param      mixed $limit Parameter description (if any) ...
	 * @param      integer $limitstart Parameter description (if any) ...
	 * @param      unknown $areas Parameter description (if any) ...
	 * @return     mixed Return description (if any) ...
	 */
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
		ximport('Hubzero_Document');
		Hubzero_Document::addComponentStylesheet('com_groups');
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
