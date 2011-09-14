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
JPlugin::loadLanguage( 'plg_xsearch_blogs' );

/**
 * Short description for 'plgXSearchBlogs'
 * 
 * Long description (if any) ...
 */
class plgXSearchBlogs extends JPlugin
{

	/**
	 * Short description for 'plgXSearchBlogs'
	 * 
	 * Long description (if any) ...
	 * 
	 * @param      unknown &$subject Parameter description (if any) ...
	 * @param      unknown $config Parameter description (if any) ...
	 * @return     void
	 */
	public function plgXSearchBlogs(&$subject, $config)
	{
		parent::__construct($subject, $config);

		// load plugin parameters
		$this->_plugin = JPluginHelper::getPlugin( 'xsearch', 'blogs' );
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
			'blogs' => JText::_('PLG_XSEARCH_BLOGS')
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
				//ximport('Hubzero_Document');
				//Hubzero_Document::addComponentStylesheet('com_events');

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
		ximport('Hubzero_Document');
		Hubzero_Document::addComponentStylesheet('com_blog');
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

	/*public function after()
	{
		// ...
	}*/
}

