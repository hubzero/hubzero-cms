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
JPlugin::loadLanguage( 'plg_xsearch_content' );

//-----------

class plgXSearchContent extends JPlugin
{
	public function plgXSearchContent(&$subject, $config)
	{
		parent::__construct($subject, $config);

		// load plugin parameters
		$this->_plugin = JPluginHelper::getPlugin( 'xsearch', 'content' );
		$this->_params = new JParameter( $this->_plugin->params );
	}
	
	//-----------
	
	public function &onXSearchAreas()
	{
		$areas = array(
			'content' => JTEXT::_('PLG_XSEARCH_ARTICLES')
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
		$c_count = " SELECT count(DISTINCT c.id)";
		$c_fields = " SELECT c.id, c.title, c.alias, c.introtext AS itext, c.fulltext AS ftext, c.state, c.created, c.modified, c.publish_up, c.attribs AS params,
					CONCAT( 'index.php?option=com_content&task=view&id=', c.id ) AS href, 
					'content' AS section, s.alias AS area, cc.alias AS category, NULL AS rating, NULL AS times_rated, NULL AS ranking, c.access, ";
		$c_from = " FROM #__content AS c 
					LEFT JOIN #__sections AS s ON s.id = c.sectionid 
					LEFT JOIN #__categories AS cc ON cc.id = c.catid";

		$phrases = $searchquery->searchPhrases;
		if (!empty($phrases)) {
			$exactphrase = addslashes('"'.$phrases[0].'"');

			$c_rel = " ("
					. "  MATCH(c.introtext,c.fulltext) AGAINST ('$exactphrase' IN BOOLEAN MODE) +"
					. "  IF(MATCH (c.metakey) AGAINST ('$exactphrase' IN BOOLEAN MODE), 5, 0) +"
					. "  MATCH(c.title) AGAINST ('$exactphrase' IN BOOLEAN MODE)"
					. " ) AS relevance";

			$c_where = " c.state=1 AND ((MATCH(c.title) AGAINST ('$exactphrase' IN BOOLEAN MODE) > 0) OR"
					 . " (MATCH(c.introtext,c.fulltext) AGAINST ('$exactphrase' IN BOOLEAN MODE) > 0) )";
		} else {
			$text = implode(' ',$searchquery->searchWords);
			$text = addslashes($text);

			$c_rel = " ("
					. "  MATCH(c.introtext,c.fulltext) AGAINST ('$text') +"
					//. "  IF(MATCH (c.metakey) AGAINST ('$text'), 5, 0) +"
					. "  MATCH(c.title) AGAINST ('$text')"
					. " ) AS relevance";

			$c_where = " c.state=1 AND ((MATCH(c.title) AGAINST ('$text') > 0) OR"
					 . " (MATCH(c.introtext,c.fulltext) AGAINST ('$text') > 0) )";
		}

		$order_by  = " ORDER BY relevance DESC, title";
		$order_by .= ($limit != 'all') ? " LIMIT $limitstart,$limit" : "";

		if ($limit) {
			if (count($areas) > 1) {
				return $c_fields.$c_rel.$c_from ." WHERE ". $c_where;
			}
			
			// Get results
			$database->setQuery( $c_fields.$c_rel.$c_from ." WHERE ". $c_where . $order_by );
			$rows = $database->loadObjectList();
			
			if ($rows) {
				foreach ($rows as $key => $row) 
				{
					$path = '';
					if ($row->area) {
						$path .= DS.$row->area;
					}
					if ($row->category && $row->category != $row->area) {
						$path .= DS.$row->category;
					}
					if ($row->alias) {
						$path .= DS.$row->alias;
					}
					if (!$path) {
						//$path = JRoute::_($row->href);
						$path = '/content/article/'.$row->id;
					}
					$rows[$key]->href = $path;
				}
			}
			
			return $rows;
		} else {
			// Get a count
			$database->setQuery( $c_count.$c_from ." WHERE ". $c_where );
			return $database->loadResult();
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
			$path = '';
			if ($row->area) {
				$path .= DS.$row->area;
			}
			if ($row->category && $row->category != $row->area) {
				$path .= DS.$row->category;
			}
			if ($row->alias) {
				$path .= DS.$row->alias;
			}
			if (!$path) {
				//$path = JRoute::_($row->href);
				$path = '/content/article/'.$row->id;
			}
			$row->href = $path;
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