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
JPlugin::loadLanguage( 'plg_xsearch_topics' );

/**
 * Short description for 'plgXSearchTopics'
 * 
 * Long description (if any) ...
 */
class plgXSearchTopics extends JPlugin
{

	/**
	 * Short description for 'plgXSearchTopics'
	 * 
	 * Long description (if any) ...
	 * 
	 * @param      unknown &$subject Parameter description (if any) ...
	 * @param      unknown $config Parameter description (if any) ...
	 * @return     void
	 */
	public function plgXSearchTopics(&$subject, $config)
	{
		parent::__construct($subject, $config);

		// load plugin parameters
		$this->_plugin = JPluginHelper::getPlugin( 'xsearch', 'topics' );
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
			'topics' => JText::_('PLG_XSEARCH_TOPICS')
		);
		return $areas;
	}

	/**
	 * Short description for 'onXSearch'
	 * 
	 * Long description (if any) ...
	 * 
	 * @param      object $searchquery Parameter description (if any) ...
	 * @param      integer $limit Parameter description (if any) ...
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

		include_once(JPATH_ROOT.DS.'components'.DS.'com_wiki'.DS.'tables'.DS.'page.php');

		// Instantiate some needed objects
		$wp = new WikiPage( $database );

		// Build query
		$filters = array();
		$filters['search'] = $searchquery;
		$filters['authorized'] = $this->_authorize();

		if (!$limit) {
			// Get a count
			$filters['select'] = 'count';

			$database->setQuery( $wp->buildPluginQuery( $filters ) );
			return $database->loadResult();
		} else {
			// Get results
			$filters['select'] = 'records';
			$filters['limit'] = $limit;
			$filters['limitstart'] = $limitstart;
			$filters['sortby'] = 'relevance';

			$query = $wp->buildPluginQuery( $filters );
			if (count($areas) > 1) {
				return $query;
			}

			$database->setQuery( $query );
			$rows = $database->loadObjectList();

			if ($rows) {
				foreach ($rows as $key => $row)
				{
					if ($row->group != '' && $row->scope != '') {
						$rows[$key]->href = JRoute::_('index.php?option=com_groups&scope='.$row->category.'&pagename='.$row->alias);
					} else {
						$rows[$key]->href = JRoute::_('index.php?option=com_topics&scope='.$row->category.'&pagename='.$row->alias);
					}
				}
			}

			return $rows;
		}
	}

	/**
	 * Short description for '_authorize'
	 * 
	 * Long description (if any) ...
	 * 
	 * @return     boolean Return description (if any) ...
	 */
	private function _authorize()
	{
		// Check if they are logged in
		$juser =& JFactory::getUser();
		if ($juser->get('guest')) {
			return false;
		}

		return true;
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
			if ($row->area != '' && $row->category != '') {
				$row->href = JRoute::_('index.php?option=com_groups&scope='.$row->category.'&pagename='.$row->alias);
			} else {
				$row->href = JRoute::_('index.php?option=com_topics&scope='.$row->category.'&pagename='.$row->alias);
			}
		}
		$juri =& JURI::getInstance();
		if (substr($row->href,0,1) == '/') {
			$row->href = substr($row->href,1,strlen($row->href));
		}

		$html  = "\t".'<li class="topic">'."\n";
		$html .= "\t\t".'<p class="title"><a href="'.$row->href.'">'.stripslashes($row->title).'</a></p>'."\n";
		$html .= "\t\t".'<p class="details">';
		if ($row->area != '' && $row->category != '') {
			$html .= JText::_('PLG_XSEARCH_GROUP_WIKI').': '.$row->area;
		} else {
			$html .= JText::_(strtoupper($row->section));
		}
		$html .= '</p>'."\n";
		if ($row->itext) {
			$html .= "\t\t".'<p>&#133; '.stripslashes($row->itext).' &#133;</p>'."\n";
		}
		$html .= "\t\t".'<p class="href">'.$juri->base().$row->href.'</p>'."\n";
		$html .= "\t".'</li>'."\n";
		return $html;
	}

	/*public function after()
	{
		// ...
	}*/
}

