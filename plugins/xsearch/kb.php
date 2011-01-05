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
JPlugin::loadLanguage( 'plg_xsearch_kb' );

//-----------

class plgXSearchKb extends JPlugin
{
	public function plgXSearchKb(&$subject, $config)
	{
		parent::__construct($subject, $config);

		// load plugin parameters
		$this->_plugin = JPluginHelper::getPlugin( 'xsearch', 'kb' );
		$this->_params = new JParameter( $this->_plugin->params );
	}
	
	//-----------
	
	public function &onXSearchAreas() 
	{
		$areas = array(
			'kb' => JText::_('PLG_XSEARCH_KNOWLEDGEBASE')
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
	
	//-----------
	
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
	
	//-----------
	
	/*public function after()
	{
		// ...
	}*/
}
