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
JPlugin::loadLanguage( 'plg_xsearch_blogs' );

//-----------

class plgXSearchBlogs extends JPlugin
{
	public function plgXSearchBlogs(&$subject, $config)
	{
		parent::__construct($subject, $config);

		// load plugin parameters
		$this->_plugin = JPluginHelper::getPlugin( 'xsearch', 'blogs' );
		$this->_params = new JParameter( $this->_plugin->params );
	}
	
	//-----------

	public function &onXSearchAreas() 
	{
		$areas = array(
			'blogs' => JText::_('PLG_XSEARCH_BLOGS')
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

		$now = date( 'Y-m-d H:i:s', time() + 0 * 60 * 60 );

		// Build the query
		$e_count = "SELECT count(DISTINCT e.id)";
		$e_fields = "SELECT e.id, e.title, e.alias, e.content AS itext, NULL AS ftext, e.state, e.created_by AS `created`, NULL AS modified, e.publish_up, NULL AS `params`,
					NULL AS href, 'blogs' AS section, e.scope AS area, NULL AS category, NULL AS rating, NULL AS times_rated, NULL AS ranking, u.name AS access, ";
		$e_from = " FROM #__blog_entries AS e, #__users AS u";

		$e_where = " e.created_by=u.id";
		$juser =& JFactory::getUser();
		if ($juser->get('guest')) {
			$e_where .= " AND e.state=1";
		} else {
			$e_where .= " AND e.state>0";
		}
		$e_where .= " AND (e.publish_up = '0000-00-00 00:00:00' OR e.publish_up <= '".$now."') ";
		$e_where .= " AND (e.publish_down = '0000-00-00 00:00:00' OR e.publish_down >= '".$now."') ";

		$phrases = $searchquery->searchPhrases;
		if (!empty($phrases)) {
			$exactphrase = addslashes('"'.$phrases[0].'"');

			$e_rel = " ("
					. "  MATCH(e.content) AGAINST ('$exactphrase' IN BOOLEAN MODE) +"
					. "  MATCH(e.title) AGAINST ('$exactphrase' IN BOOLEAN MODE)"
					. " ) AS relevance";

			$e_where .= " AND ((MATCH(e.title) AGAINST ('$exactphrase' IN BOOLEAN MODE) > 0) OR"
					 . " (MATCH(e.content) AGAINST ('$exactphrase' IN BOOLEAN MODE) > 0) )";
		} else {
			$text = implode(' ',$searchquery->searchWords);
			$text = addslashes($text);

			$e_rel = " ("
					. "  MATCH(e.content) AGAINST ('$text') +"
					. "  MATCH(e.title) AGAINST ('$text')"
					. " ) AS relevance";

			$e_where .= " AND ((MATCH(e.title) AGAINST ('$text') > 0) OR"
					 . " (MATCH(e.content) AGAINST ('$text' IN BOOLEAN MODE) > 0) )";
		}

		$order_by  = " ORDER BY relevance DESC, title";
		$order_by .= ($limit != 'all') ? " LIMIT $limitstart,$limit" : "";


		if (!$limit) {
			// Get a count
			$database->setQuery( $e_count.$e_from ." WHERE ". $e_where );
			return $database->loadResult();
		} else {
			if (count($areas) > 1) {
				//ximport('xdocument');
				//XDocument::addComponentStylesheet('com_events');
				
				return $e_fields.$e_rel.$e_from ." WHERE ". $e_where;
			}
			
			// Get results
			$query = $e_fields.$e_rel.$e_from ." WHERE ". $e_where. " GROUP BY id". $order_by;
			$database->setQuery( $query );
			$rows = $database->loadObjectList();

			if ($rows) {
				foreach ($rows as $key => $row) 
				{
					switch ($row->area) 
					{
						case 'site':
							$rows[$key]->href = JRoute::_('index.php?option=com_blog&task='.JHTML::_('date',$row->publish_up, '%Y', 0).'/'.JHTML::_('date',$row->publish_up, '%m', 0).'/'.$row->alias);
						break;
						case 'member':
							$rows[$key]->href = JRoute::_('index.php?option=com_members&id='.$row->created.'&active=blog&task='.JHTML::_('date',$row->publish_up, '%Y', 0).'/'.JHTML::_('date',$row->publish_up, '%m', 0).'/'.$row->alias);
						break;
						case 'group':
						break;
					}
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

	/*public function documents() 
	{
		ximport('xdocument');
		XDocument::addComponentStylesheet('com_blog');
	}
	
	//-----------
	
	public function before()
	{
		// ...
	}*/
	
	//-----------
	
	public function out( $row, $keyword )
	{
		//$words = explode(' ', $keyword);
		if (!$row->href) {
			switch ($row->area) 
			{
				case 'site':
					$row->href = JRoute::_('index.php?option=com_blog&task='.JHTML::_('date',$row->publish_up, '%Y', 0).'/'.JHTML::_('date',$row->publish_up, '%m', 0).'/'.$row->alias);
				break;
				case 'member':
					$row->href = JRoute::_('index.php?option=com_members&id='.$row->created.'&active=blog&task='.JHTML::_('date',$row->publish_up, '%Y', 0).'/'.JHTML::_('date',$row->publish_up, '%m', 0).'/'.$row->alias);
				break;
				case 'group':
				break;
			}
		}
		
		if (strstr( $row->href, 'index.php' )) {
			$row->href = JRoute::_($row->href);
		}
		$juri =& JURI::getInstance();
		if (substr($row->href,0,1) == '/') {
			$row->href = substr($row->href,1,strlen($row->href));
		}
		
		// Start building the HTML
		$html  = "\t".'<li class="blog-entry">'."\n";
		$html .= "\t\t".'<p class="title"><a href="'.$row->href.'">'.stripslashes($row->title).'</a></p>'."\n";
		$html .= "\t\t".'<p class="details">'.JHTML::_('date', $row->publish_up, '%d %b %Y').' <span>|</span> '.JText::sprintf('PLG_XSEARCH_BLOGS_POSTED_BY','<cite><a href="'.JRoute::_('index.php?option=com_members&id='.$row->created).'">'.stripslashes($row->access).'</a></cite>').'</p>'."\n";
		if ($row->itext) {
			$html .= "\t\t".'<p>&#133; '.stripslashes($row->itext).' &#133;</p>'."\n";
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
