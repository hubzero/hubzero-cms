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
JPlugin::loadLanguage( 'plg_xsearch_events' );

/**
 * Short description for 'plgXSearchEvents'
 * 
 * Long description (if any) ...
 */
class plgXSearchEvents extends JPlugin
{

	/**
	 * Short description for 'plgXSearchEvents'
	 * 
	 * Long description (if any) ...
	 * 
	 * @param      unknown &$subject Parameter description (if any) ...
	 * @param      unknown $config Parameter description (if any) ...
	 * @return     void
	 */
	public function plgXSearchEvents(&$subject, $config)
	{
		parent::__construct($subject, $config);

		// load plugin parameters
		$this->_plugin = JPluginHelper::getPlugin( 'xsearch', 'events' );
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
			'events' => JText::_('PLG_XSEARCH_EVENTS')
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

		// Build the query
		$e_count = "SELECT count(DISTINCT e.id)";
		$e_fields = "SELECT e.id, e.title, NULL AS alias, e.content AS itext, NULL AS ftext, e.state, e.created, e.modified, e.publish_up, NULL AS params, 
					CONCAT( 'index.php?option=com_events&task=details&id=', e.id ) AS href, 'events' AS section, NULL AS area, NULL AS category, NULL AS rating, NULL AS times_rated, NULL AS ranking, e.access, ";
		$e_from = " FROM #__events AS e";

		$phrases = $searchquery->searchPhrases;
		if (!empty($phrases)) {
			$exactphrase = addslashes('"'.$phrases[0].'"');

			$e_rel = " ("
					. "  MATCH(e.content) AGAINST ('$exactphrase' IN BOOLEAN MODE) +"
					. "  MATCH(e.title) AGAINST ('$exactphrase' IN BOOLEAN MODE)"
					. " ) AS relevance";

			$e_where = " e.state=1 AND ((MATCH(e.title) AGAINST ('$exactphrase' IN BOOLEAN MODE) > 0) OR"
					 . " (MATCH(e.content) AGAINST ('$exactphrase' IN BOOLEAN MODE) > 0) )";
		} else {
			$text = implode(' ',$searchquery->searchWords);
			$text = addslashes($text);

			$e_rel = " ("
					. "  MATCH(e.content) AGAINST ('$text') +"
					. "  MATCH(e.title) AGAINST ('$text')"
					. " ) AS relevance";

			$e_where = " e.state=1 AND ((MATCH(e.title) AGAINST ('$text') > 0) OR"
					 . " (MATCH(e.content) AGAINST ('$text') > 0) )";
		}

		$order_by  = " ORDER BY relevance DESC, title";
		$order_by .= ($limit != 'all') ? " LIMIT $limitstart,$limit" : "";

		if (!$limit) {
			// Get a count
			$database->setQuery( $e_count.$e_from ." WHERE ". $e_where );
			return $database->loadResult();
		} else {
			if (count($areas) > 1) {
				ximport('Hubzero_Document');
				Hubzero_Document::addComponentStylesheet('com_events');

				return $e_fields.$e_rel.$e_from ." WHERE ". $e_where;
			}

			// Get results
			$query = $e_fields.$e_rel.$e_from ." WHERE ". $e_where. " GROUP BY id". $order_by;
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


	/**
	 * Short description for 'documents'
	 * 
	 * Long description (if any) ...
	 * 
	 * @return     void
	 */
	public function documents()
	{
		ximport('Hubzero_Document');
		Hubzero_Document::addComponentStylesheet('com_events');
	}

	/*public function before()
	{
		// ...
	}*/

	/**
	 * Short description for 'out'
	 * 
	 * Long description (if any) ...
	 * 
	 * @param      mixed $row Parameter description (if any) ...
	 * @param      unknown $keyword Parameter description (if any) ...
	 * @return     string Return description (if any) ...
	 */
	public function out( $row, $keyword )
	{
		$month = JHTML::_('date', $row->publish_up, '%b');
		$day = JHTML::_('date', $row->publish_up, '%d');
		$year = JHTML::_('date', $row->publish_up, '%Y');

		$words = explode(' ', $keyword);

		if (strstr( $row->href, 'index.php' )) {
			$row->href = JRoute::_($row->href);
		}
		$juri =& JURI::getInstance();
		if (substr($row->href,0,1) == '/') {
			$row->href = substr($row->href,1,strlen($row->href));
		}

		// Start building the HTML
		$html  = "\t".'<li class="event">'."\n";
		$html .= "\t\t".'<p class="event-date"><span class="month">'.$month.'</span> <span class="day">'.$day.'</span> <span class="year">'.$year.'</span></p>'."\n";
		$html .= "\t\t".'<p class="title"><a href="'.$row->href.'">'.stripslashes($row->title).'</a></p>'."\n";
		if ($row->itext) {
			$row->itext = str_replace('[[BR]]', '', $row->itext);
			$html .= "\t\t".'<p>&#133; '.stripslashes($row->itext).' &#133;</p>'."\n";
		}
		$html .= "\t\t".'<p class="href">'.$juri->base().$row->href.'</p>'."\n";
		$html .= "\t".'</li>'."\n";

		// Return output
		return $html;
	}

	/*public function after()
	{
		// ...
	}*/
}

