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
JPlugin::loadLanguage( 'plg_xsearch_kb' );

/**
 * Short description for 'plgXSearchKb'
 * 
 * Long description (if any) ...
 */
class plgXSearchKb extends JPlugin
{

	/**
	 * Short description for 'plgXSearchKb'
	 * 
	 * Long description (if any) ...
	 * 
	 * @param      unknown &$subject Parameter description (if any) ...
	 * @param      unknown $config Parameter description (if any) ...
	 * @return     void
	 */
	public function plgXSearchKb(&$subject, $config)
	{
		parent::__construct($subject, $config);

		// load plugin parameters
		$this->_plugin = JPluginHelper::getPlugin( 'xsearch', 'kb' );
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
			'kb' => JText::_('PLG_XSEARCH_KNOWLEDGEBASE')
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
		$f_count = "SELECT COUNT(*)";
		$f_fields = "SELECT f.id, f.title, f.alias, f.fulltext AS itext, f.fulltext AS ftext, f.state, f.created, f.modified, f.created AS publish_up, NULL AS params,
					CONCAT( 'index.php?option=com_kb&alias=', f.alias ) AS href, 'kb' AS section, cc.alias AS area, c.alias AS category, NULL AS rating, NULL AS times_rated, NULL AS ranking, f.access,";

		$f_from = " FROM #__faq AS f
		 			LEFT JOIN #__faq_categories AS c ON c.id = f.section 
					LEFT JOIN #__faq_categories AS cc ON cc.id = f.category";

		$phrases = $searchquery->searchPhrases;
		if (!empty($phrases)) {
			$exactphrase = addslashes('"'.$phrases[0].'"');

			$f_rel = " ("
					. "  MATCH(f.fulltext) AGAINST ('$exactphrase' IN BOOLEAN MODE) +"
					. "  MATCH(f.title) AGAINST ('$exactphrase' IN BOOLEAN MODE)"
					. " ) AS relevance";

			$f_where = " f.state=1 AND ((MATCH(f.title) AGAINST ('$exactphrase' IN BOOLEAN MODE) > 0) OR"
					 . " (MATCH(f.fulltext) AGAINST ('$exactphrase' IN BOOLEAN MODE) > 0) )";
		} else {
			$text = implode(' ',$searchquery->searchWords);
			$text = addslashes($text);

			$f_rel = " ("
					. "  MATCH(f.fulltext) AGAINST ('$text') +"
					. "  MATCH(f.title) AGAINST ('$text')"
					. " ) AS relevance";

			$f_where = " f.state=1 AND ((MATCH(f.title) AGAINST ('$text') > 0) OR"
					 . " (MATCH(f.fulltext) AGAINST ('$text') > 0) )";
		}

		$order_by  = " ORDER BY relevance DESC, title";
		$order_by .= ($limit != 'all') ? " LIMIT $limitstart,$limit" : "";

		if (!$limit) {
			// Get a count
			$database->setQuery( $f_count.$f_from ." WHERE ". $f_where );
			return $database->loadResult();
		} else {
			if (count($areas) > 1) {
				return $f_fields.$f_rel.$f_from ." WHERE ". $f_where;
			}

			// Get results
			$database->setQuery( $f_fields.$f_rel.$f_from ." WHERE ". $f_where . $order_by );
			$rows = $database->loadObjectList();

			if ($rows) {
				foreach ($rows as $key => $row)
				{
					$rows[$key]->href = JRoute::_('index.php?option=com_kb&section='.$row->area.'&category='.$row->category.'&alias='.$row->alias);
				}
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
		if (strstr( $row->href, 'index.php' )) {
			$row->href = JRoute::_('index.php?option=com_kb&section='.$row->area.'&category='.$row->category.'&alias='.$row->alias);
		}
		$juri =& JURI::getInstance();
		if (substr($row->href,0,1) == '/') {
			$row->href = substr($row->href,1,strlen($row->href));
		}

		// Start building the HTML
		$html  = "\t".'<li>'."\n";
		$html .= "\t\t".'<p class="title"><a href="'.$row->href.'">'.stripslashes($row->title).'</a></p>'."\n";
		if ($row->itext) {
			$html .= "\t\t".'<p>&#133; '.stripslashes($row->itext).' &#133;</p>'."\n";
		} else if ($row->ftext) {
			$html .= "\t\t".'<p>&#133; '.stripslashes($row->ftext).' &#133;</p>'."\n";
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

