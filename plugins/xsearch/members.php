<?php
/**
 * @package     hubzero-cms
 * @author      Shawn Rice <zooley@purdue.edu>
 * @copyright   Copyright 2005-2011 Purdue University. All rights reserved.
 * @license     http://www.gnu.org/licenses/lgpl-3.0.html LGPLv3
 *
 * Copyright 2005-2011 Purdue University. All rights reserved.
 * All rights reserved.
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

//-----------

jimport( 'joomla.plugin.plugin' );
JPlugin::loadLanguage( 'plg_xsearch_members' );

//-----------

class plgXSearchMembers extends JPlugin
{
	public function plgXSearchMembers(&$subject, $config)
	{
		parent::__construct($subject, $config);

		// load plugin parameters
		$this->_plugin = JPluginHelper::getPlugin( 'xsearch', 'members' );
		$this->_params = new JParameter( $this->_plugin->params );
	}
	
	//-----------
	
	public function &onXSearchAreas()
	{
		$areas = array(
			'members' => JText::_('PLG_XSEARCH_MEMBERS')
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

		$juser =& JFactory::getUser();

		// Build the query
		$c_count = "SELECT COUNT(*) ";
		$b = '';
		foreach ($words as $word) 
		{
			if (trim($word) != '') {
				$word = addslashes($word);
				$b .= "CASE WHEN LOWER(m.givenName) LIKE '%$word%' THEN 5 ELSE 0 END + ";
				$b .= "CASE WHEN LOWER(m.surname) LIKE '%$word%' THEN 5 ELSE 0 END + ";
				$b .= "CASE WHEN LOWER(m.name) LIKE '%".addslashes(implode(' ',$searchquery->searchTokens))."%' THEN 20 ELSE 0 END + ";
				//if (!$juser->get('guest')) {
					$b .= "CASE WHEN LOWER(b.bio) LIKE '%$word%' THEN 5 ELSE 0 END + ";
				//}
			}
		}
		$b = substr($b, 0, -3);
		$c_fields = "SELECT m.uidNumber AS id, CONCAT(m.givenName,' ', m.middleName,' ', m.surname) AS title, m.username AS alias, b.bio AS itext,  NULL AS ftext, m.public AS state, NULL AS created, m.modifiedDate AS modified, NULL AS publish_up, m.params AS params,
					CONCAT( 'index.php?option=com_members&id=', m.uidNumber ) as href, 'members' AS section, m.organization AS area, m.picture AS category, NULL AS rating, NULL AS times_rated, NULL AS ranking, NULL AS access, ($b) AS relevance ";
		$c_from = "FROM #__xprofiles AS m LEFT JOIN #__xprofiles_bio AS b ON m.uidNumber=b.uidNumber 
				WHERE m.public=1 AND (";
		foreach ($words as $word) 
		{
			if (trim($word) != '') {
				$word = addslashes($word);
				if ($juser->get('guest')) {
					$c_from .= "(LOWER(m.givenName) LIKE '%$word%') OR (LOWER(m.surname) LIKE '%$word%') OR (LOWER(m.name) LIKE '%$word%') OR (LOWER(b.bio) LIKE '%$word%' AND m.params LIKE '%access_bio=0%') OR ";
				} else {
					$c_from .= "(LOWER(m.givenName) LIKE '%$word%') OR (LOWER(m.surname) LIKE '%$word%') OR (LOWER(m.name) LIKE '%$word%') OR (LOWER(b.bio) LIKE '%$word%') OR ";
				}
			}
		}
		$c_from = substr($c_from, 0, -4);
		$c_from .= ")";
		/*if ($juser->get('guest')) {
			$c_from .= " AND m.params LIKE '%access_bio=0%'";
		} else {
			$c_from .= " AND (m.params LIKE '%access_bio=0%' OR m.params LIKE '%access_bio=1%')";
		}*/
		$c_order = " ORDER BY relevance DESC";
		$c_limit = ($limit != 'all') ? " LIMIT $limitstart,$limit" : "";

		if (!$limit) {
			// Get a count
			$database->setQuery( $c_count.$c_from );
			return $database->loadResult();
		} else {
			if (count($areas) > 1) {
				ximport('Hubzero_Document');
				Hubzero_Document::addComponentStylesheet('com_members');

				return $c_fields.$c_from;
			}
			
			// Get results
			$database->setQuery( $c_fields.$c_from.$c_order.$c_limit );
			$rows = $database->loadObjectList();

			foreach ($rows as $key => $row) 
			{
				$rows[$key]->href = JRoute::_('index.php?option=com_members&id='.$row->id);
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
		Hubzero_Document::addComponentStylesheet('com_members');
	}

	//-----------

	/*public function before()
	{
		// ...
	}*/

	//-----------

	public function out( $row, $keyword )
	{
		$config =& JComponentHelper::getParams( 'com_members' );
		
		if ($row->category) {
			$thumb  = $config->get('webpath');
			if (substr($thumb, 0, 1) != DS) {
				$thumb = DS.$thumb;
			}
			if (substr($thumb, -1, 1) == DS) {
				$thumb = substr($thumb, 0, (strlen($thumb) - 1));
			}
			if ($row->id < 0) {
				$id = abs($row->id);
				$thumb .= DS.'n'.plgXSearchMembers::niceidformat($id).DS.$row->category;
			} else {
				$thumb .= DS.plgXSearchMembers::niceidformat($row->id).DS.$row->category;
			}
			
			$thumb = plgXSearchMembers::thumbit($thumb);
		} else {
			$thumb = '';
		}
		
		$dfthumb = $config->get('defaultpic');
		if (substr($dfthumb, 0, 1) != DS) {
			$dfthumb = DS.$dfthumb;
		}
		$dfthumb = plgXSearchMembers::thumbit($dfthumb);
		
		if (strstr( $row->href, 'index.php' )) {
			$row->href = JRoute::_($row->href);
		}
		$juri =& JURI::getInstance();
		if (substr($row->href,0,1) == '/') {
			$row->href = substr($row->href,1,strlen($row->href));
		}
		
		$juser =& JFactory::getUser();
		$params = new JParameter( $row->params );
		
		$html  = "\t".'<li class="member">'."\n";
		if (is_file(JPATH_ROOT.$thumb)) {
			$p = $thumb;
		} else if (is_file(JPATH_ROOT.$dfthumb)) {
			$p = $dfthumb;
		}
		if ($p) {
			$html .= "\t\t".'<p class="photo"><img width="50" height="50" src="'.$p.'" alt="" /></p>'."\n";
		}
		$html .= "\t\t".'<p class="title"><a href="'.$row->href.'">'.stripslashes($row->title).'</a></p>'."\n";
		if ($params->get('access_bio') == 0 || ($params->get('access_bio') == 1 && !$juser->get('guest'))) {
			if ($row->itext) {
				$html .= "\t\t".'<p>&#133; '.stripslashes($row->itext).' &#133;</p>'."\n";
			}
		}
		$html .= "\t\t".'<p class="href">'.$juri->base().$row->href.'</p>'."\n";
		$html .= "\t".'</li>'."\n";
		return $html;
	}
	
	//-----------
	
	public function thumbit($thumb) 
	{
		$image = explode('.',$thumb);
		$n = count($image);
		$image[$n-2] .= '_thumb';
		$end = array_pop($image);
		$image[] = $end;
		$thumb = implode('.',$image);
		
		return $thumb;
	}
	
	//-----------

	public function niceidformat($someid) 
	{
		while (strlen($someid) < 5) 
		{
			$someid = 0 . "$someid";
		}
		return $someid;
	}

	//-----------

	/*public function after()
	{
		// ...
	}*/
}
